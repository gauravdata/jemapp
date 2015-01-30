<?php
/**
 * Magento Module developed by NoStress Commerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@nostresscommerce.cz so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2009 NoStress Commerce (http://www.nostresscommerce.cz)
 *
 */

/**
 * Model for Export
 *
 * @category Nostress
 * @package Nostress_Nscexport
 *
 */

class Nostress_Nscexport_Model_Profile extends Nostress_Nscexport_Model_Abstract
{
	const START_TIME = 'start_time';
	const FILTER_FROM = 'from';
	const FILTER_TO = 'to';
	const FILTER_GTEQ = 'gteq'; //greaterOrEqualValue
	const FILTER_LTEQ = 'lteq';  //lessOrEqualValue

	const CONDITIONS = 'conditions';
	const VISIBILITY = 'visibility';
	const VISIBILITY_PARENT = 'visibility_parent';
	
	const CODE_NOT_ENABLED = 101;
	const CODE_ERROR = 301;

	const DEF_GROUP_BY_CATEGORY = "0";
	const DEF_RELOAD_CACHE = 1;

	const START_TIME_HOUR_INDEX = 0;
	const START_TIME_MINUTE_INDEX = 1;
	const START_TIME_SECOND_INDEX = 2;
	const DEFAULT_START_TIME = "00";

	const COL_FILENAME = "filename";
	const COL_CONFIG = "config";
	const COL_STATUS = "status";

	private $product; //current processed product
	private $store; //current chosen store
	private $category; //category for which i product exported
	private $_taxHelper; //tax helper
	private $encoding; //chosen encoding
	private $editedCategoryIds; //edited ids of categories
	private $configManageStock = ''; //edited ids of categories

	private $_attributeSetMap;
	private $_attributeSet = null;
	private $_decimalDelimiter = ".";
	private $_feedObject;
    private $_reloadCache;
    private $_profileDirectAttributes = array("id","store_id","name","frequency","enabled","filename","feed");

	public function _construct()
	{
		parent::_construct ();
		$this->_init ( 'nscexport/profile' );
		$this->_taxHelper = new Mage_Tax_Helper_Data ( );
		$delimiter = $this->getConfig(Nostress_Nscexport_Helper_Data::PARAM_DELIMITER);
		if(isset($delimiter))
			$this->setDecimalDelimiter($delimiter);
	}

	public function setStatus($status)
	{
	    $id = $this->getId();
		if(!$id)
			return;
		$this->log("Export profile {$id} {$status}");
		$this->setData(self::COL_STATUS,$status);
		$this->setUpdateTime($this->helper()->getDateTime(null,true));
        $this->save();
	}

	public function getAllProfiles()
	{
		$collection = $this->getCollection();
		$collection->load();
		return $collection->getItems();
	}

    public function getCollectionByStoreId($storeId)
	{
		$collection = $this->getCollection()->addFieldToFilter('store_id',$storeId);
		$collection->load();
		return $collection;
	}

	public function processData($data, $id) {

		$config = array();

		//general configuration part
		$general = $this->getConfigField(self::GENERAL,$data);
		foreach ($this->_profileDirectAttributes as $attribute) {
			$this->setData($attribute,$this->getArrayField($attribute,$general,""));
			unset($general[$attribute]);
		}
		$this->addFilenameSuffix();
		$config[self::GENERAL] = $general;
		$days = $this->getConfigField(self::CRON_DAYS, $general);
		$times = $this->getConfigField(self::CRON_TIMES, $general);
		unset($config[self::GENERAL][self::CRON_DAYS]);
		unset($config[self::GENERAL][self::CRON_TIMES]);

		//feed configuration part
		$feed = $this->getConfigField(self::FEED, $data);

		$preparedAttributes = array();
		$counter = 0;
		$allAttributesInfo = $this->helper()->getVisibleProductAttributes(true);
		$attributes = $this->getAttributeFromArray($feed,array());

		foreach ($attributes as $key => $attribute) {

			$index = $counter;
			if ($attribute[self::CODE] == self::CUSTOM_ATTRIBUTE) {
				if (isset($attribute[self::DELETE]) && $attribute[self::DELETE] == 1) {
					continue;
				}
				$index = $counter+Nostress_Nscexport_Helper_Data::CUSTOM_ATTRIBUTE_ROW_INDEX_OFFSET;
			}

			if(isset($attribute[self::POST_PROCESS]))
			{
				$postprocFunctions = $attribute[self::POST_PROCESS];
				if(is_array($postprocFunctions))
					$attribute[self::POST_PROCESS] = implode(self::POSTPROC_DELIMITER, $postprocFunctions);
			}

			//add attribute type
			if(isset($attribute[self::MAGENTO_ATTRIBUTE]))
			{
				$magentoAttributeCode = $attribute[self::MAGENTO_ATTRIBUTE];
				if(isset($allAttributesInfo[$magentoAttributeCode]))
				{
					$attributeInfo = $allAttributesInfo[$magentoAttributeCode];
					$attribute[self::MAGENTO_ATTRIBUTE_TYPE] = $attributeInfo->getFrontendInput();
				}

				if($magentoAttributeCode == self::SHIPPING_COST)
					$attribute[self::MAGENTO_ATTRIBUTE_TYPE] = "price";
			}

			$preparedAttributes[$index] = $attribute;
			$counter++;
		}

		$feed[self::ATTRIBUTES][self::ATTRIBUTE] = $preparedAttributes;
		$config[self::FEED] = $feed;

	    //product configuration part
	    $product = $this->getConfigField(self::PRODUCT,$data);//self::PRODUCT,$data);
	    $types = $this->getArrayField(self::TYPES,$product,array());
	    $types = implode(",",$types);
	    $product[self::TYPES] = $types;
	    $config[self::PRODUCT] = $product;
	    $config[self::UPLOAD] = $this->getConfigField(self::UPLOAD,$data);

	    $attributeFilter = $this->getConfigField(self::ATTRIBUTE_FILTER,$data);
	    foreach( array( self::VISIBILITY, self::VISIBILITY_PARENT) as $vis) {
	        if( !isset( $attributeFilter[ $vis])) {
	            $attributeFilter[$vis] = array();
	        }
	    }
	    $config[self::ATTRIBUTE_FILTER] = $attributeFilter;
	    $config[self::ATTRIBUTE_FILTER][self::CONDITIONS] = Mage::getModel( 'nscexport/rule')->parseConditionsPost( $this->getConfigField( 'rule',$data));

	    $this->setConfig($config);
		$categoryProductIds = $data['category_product_ids'];
		$oldCategoryProductIds = "";
		$oldDays = array();
		$oldTimes = array();
		$profileIsNew = false;

		if($this->getId() != '')
		{
			// Get old export values
			$originalProfile = Mage::getModel('nscexport/profile')->load($this->getId());
			$deleteFeedFiles = false;

			$cronModel = Mage::getModel("nscexport/cron");
			$oldDays = $cronModel->getDaysPerProfile($this->getId());
			$oldTimes = $cronModel->getTimesPerProfile($this->getId());

			$oldCategoryProductIds = Mage::getModel('nscexport/categoryproducts')->getExportCategoryProducts($this->getId());

			//If search engine was changed => start times have to be recounted or categories to export were changed
			if($originalProfile->getFeed() != $this->getFeed() || $oldCategoryProductIds != $categoryProductIds)
			{
				$deleteFeedFiles = true;
			}
			else
			{
				//rename xml files
				if($this->getFilename() != $originalProfile->getFilename())
				{
				    //rename file nad temp file
				    $this->helper()->renameFile($originalProfile->getCurrentFilename(true),$this->getCurrentFilename(true));
				    $this->resetUrl();
				}
			}
			if($deleteFeedFiles)
			{
			    $originalProfile->deleteFiles();
			    $this->setUrl($this->helper()->__("Feed File doesn't exist."));
			}

    	}
    	else
    	{
    		$profileIsNew = true;
    		$this->setUrl($this->helper()->__("Feed File doesn't exist."));
    		$this->setCreatedTime($this->helper()->getDateTime());
    	}
    	$this->save();
    	if($oldCategoryProductIds != $categoryProductIds)
    		$this->helper()->updateCategoryProducts($this->getId(),$categoryProductIds,$this->getStoreId());
    	$this->updateDayTimes($oldDays,$oldTimes,$days,$times);

    	if($profileIsNew)
    		$this->helper()->logNewProfileEvent($this->getFeed(),$this->getFileUrl());
	}

	public function getFilename($full = false,$fileSuffix = null)
	{
	    $filename = $this->getData(self::COL_FILENAME);
	    if(isset($fileSuffix))
	        $filename = $this->helper()->changeFileSuffix($filename,$fileSuffix);
	    if($full)
	    {
	        $feedDir = $this->helper()->getFeedDirectoryName($this->getFeed());

	        $dirPath = $this->helper()->getFullFilePath("",$feedDir);

	        $this->helper()->createDirectory($dirPath);

	        $filename = $this->helper()->getFullFilePath($filename,$feedDir);
	    }
	    return $filename;
	}

	protected function updateDayTimes($originalDays,$originalTimes, $days,$times)
	{
		$cronModel = Mage::getModel("nscexport/cron");

		$daysToDelete = array_diff($originalDays,$days);
		$timesToDelete = array_diff($originalTimes,$times);
		$cronModel->deleteRecords($this->getId(),$daysToDelete,$originalTimes);
		$cronModel->deleteRecords($this->getId(),$originalDays,$timesToDelete);

		$daysToAdd = array_diff($days,$originalDays);
		$timesToAdd = array_diff($times,$originalTimes);
		$cronModel->addRecords($this->getId(),$daysToAdd,$times);
		$cronModel->addRecords($this->getId(),array_diff($days,$daysToAdd),$timesToAdd);
	}

	protected function getCurrentFilename($full=false)
	{
	    $suffix = $this->getFeedObject()->getFileType();
	    $generalConfig = $this->getCustomConfig(self::GENERAL,false);
	    $compressFile = $this->getArrayField("compress_file",$generalConfig,"0");
	    if($compressFile)
	        $suffix = Nostress_Nscexport_Helper_Data::FILE_TYPE_ZIP;

	    return $this->getFilename($full,$suffix);
	}

	public function delete()
	{
	    $this->deleteFiles();
	    parent::delete();
	}

	public function setMessageStatusError($message,$status,$errorLink = "")
	{
    	$this->setMessage($message);
    	$this->setStatus($status);
    	$this->save();
	}

	//delete feed and temp feed files
	protected function deleteFiles()
	{
		$this->helper()->deleteFile($this->getCurrentFilename());
	    $this->setUrl($this->helper()->__("Feed File doesn't exist."));
	}

	public function resetUrl()
	{
	    $this->setUrl($this->getFileUrl());
	}

    protected function getFileUrl()
	{
	    $filename = $this->getCurrentFilename();
	    $feedDir = $this->helper()->getFeedDirectoryName($this->getFeed());
	    $filename = $this->helper()->getFileUrl($filename,$feedDir);
	    return $filename;
	}

	protected function addFilenameSuffix()
	{
	    $filename = $this->helper()->addFileSuffix($this->getFilename(),$this->getFeed());
	    $this->setFilename($filename);
	}

    protected function helper()
    {
    	return Mage::helper('nscexport/data');
    }

    public function exportCategoryTree()
    {
    	$feedConfig = $this->getMergedProfileFeedConfig();
    	return $this->getArrayField("category_tree",$feedConfig[self::COMMON],false);
    }

    public function exportProducts()
    {
    	$attributes = $this->getMagentoAttributes();
    	if(isset($attributes) && !empty($attributes))
    		return true;
    	else
    		return false;
    }

    public function getLoaderParams()
    {
        $productConfig = $this->getCustomConfig(self::PRODUCT);
        $attributeFilterConfig = $this->getCustomConfig(self::ATTRIBUTE_FILTER,false);
        if(isset($attributeFilterConfig))
        	$productConfig = array_merge($productConfig,$attributeFilterConfig);
        $feedObject = $this->getFeedObject();
        $attributes = $this->getMagentoAttributes();

        $params = array();
        $params["export_id"] = $this->getExportId();
        $params["store_id"] = $this->getStoreId();
        $params["use_product_filter"] = $this->getArrayField("use_product_filter",$productConfig,"0");
        $params["group_by_category"] = self::DEF_GROUP_BY_CATEGORY;
        $params["reload_cache"] = $this->getReloadCache();
        $params["taxonomy_code"] = $feedObject->getTaxonomyCode();
        $params["batch_size"] = $this->helper()->getGeneralConfig(Nostress_Nscexport_Helper_Data::PARAM_BATCH_SIZE);
        $params["conditions"] = $productConfig;
        $params["attributes"] = $attributes;

        $feedConfig = $this->getMergedProfileFeedConfig();

        $stockDependence = $this->getArrayField("stock_status_dependence",$productConfig,"");
        if(empty($stockDependence))
        	$stockDependence = $this->getArrayField("stock_status_dependence",$feedConfig[self::COMMON][self::STOCK],"");
        $params["stock_status_dependence"] = $stockDependence;

        $commonConfigAttributes = array("category_lowest_level","datetime_format","category_path_delimiter","sort_attribute","sort_order");
        foreach ($commonConfigAttributes as $attributeCode)
        {
        	$params[$attributeCode] = $this->getArrayField($attributeCode,$feedConfig[self::COMMON],null);
        }

        $loadAllProductCategories = "0";
        if(in_array("categories",$attributes))
            $loadAllProductCategories = "1";
        $params['load_all_product_categories'] = $loadAllProductCategories;

        return $params;
    }

    protected function getTransformParams()
    {
        $productConfig = $this->getCustomConfig(self::PRODUCT);

        $params = $this->getMergedProfileFeedConfig();
		if(isset($params['common']))
		{
        	foreach($params['common'] as $key => $param)
        	{
        		$params[$key] = $param;
        	}
        	unset($params['common']);
        }
        $params["file_type"] = $this->getFeedObject()->getFileType();
        $params[self::FILE_URL] = $this->getFileUrl();
        $params["store_id"] = $this->getStoreId();
        $params["parents_childs"] = $this->getArrayField("parents_childs",$productConfig,"0");
        $params["xslt"] = $this->getFeedObject()->getTrnasformationXslt();
        return $params;
    }

    public function getXmlTransformParams()
    {
    	return $this->getTransformParams();
    }

    public function getXsltTransformParams()
    {
    	$params = $this->getTransformParams();
    	$attributes = $this->getAttributeFromArray($params);
    	//add params
    	if(isset($attributes) && is_array($attributes))
    	{
    		$cdataSectionElements = array();
    		$customColumnsHeader = array();
    		$columnsHeader = array();
    		foreach ($params[self::ATTRIBUTES][self::ATTRIBUTE] as $key => $attribute)
    		{
    			if ($attribute[self::CODE] == self::CUSTOM_ATTRIBUTE)
    			{
    				$customColumnsHeader[] = $attribute[self::LABEL];
    			}
    			else if($attribute[self::TYPE] != self::DISABLED && $attribute[self::TYPE] != self::CSV_DISABLED)
    			{
    				$columnsHeader[] = $attribute[self::LABEL];
    			}

    			if(array_key_exists(self::POST_PROCESS,$attribute) && strpos($attribute[self::POST_PROCESS], self::CDATA) !== false)
    			{
    				$cdataSectionElements[] =  $attribute[self::LABEL];
    			}
    		}
    		$params[self::CUSTOM_COLUMNS_HEADER] = $customColumnsHeader;
    		$params[self::CDATA_SECTION_ELEMENTS] = $cdataSectionElements;
    		$params[self::BASIC_ATTRIBUTES_COLUMNS_HEADER] = $columnsHeader;
    	}
    	return $params;
    }

    public function getWriterParams()
    {
        $params = $this->getCustomConfig(self::GENERAL);
        $suffix = $this->getFeedObject()->getFileType();
        $params["full_filename"] = $this->getFilename(true,$suffix);
        $params["filename"] = $this->getFilename(false,$suffix);
        $params["zip_filename"] = $this->getFilename(true,Nostress_Nscexport_Helper_Data::FILE_TYPE_ZIP);
        return $params;
    }

    protected function getMergedProfileFeedConfig($feedCode = null)
    {
        $feedConfig = $this->getFeedObject($feedCode)->getAttributesSetup();

        $profileFeedConfig = $this->getCustomConfig(self::FEED,false);
        if(!empty($profileFeedConfig))
        {
            $profileFeedConfig = $this->helper()->updateArray($profileFeedConfig,$feedConfig);
        }
        else
        {
            $profileFeedConfig = $feedConfig;
        }

        $attributes = $this->removeEmptyAttributes($profileFeedConfig);
        if(!empty($attributes))
        	$profileFeedConfig[self::ATTRIBUTES][self::ATTRIBUTE] = $attributes;

        return $profileFeedConfig;
    }

    protected function removeEmptyAttributes($feedConfig)
    {
        $attributeInfoArray = $this->getAttributeFromArray($feedConfig,array());

        $attributes = array();
        foreach ($attributeInfoArray as $attribute)
        {
         	$code = $this->getArrayField(self::CODE,$attribute);
         	$label = $this->getArrayField(self::LABEL,$attribute,"");
         	if(empty($label) && $code != self::CUSTOM_ATTRIBUTE)
         		continue;
         	$attributes[] = $attribute;
        }
        return $attributes;
    }

    public function getBackendConfig($feedCode = null)
    {
        $profileFeedConfig = $this->getMergedProfileFeedConfig($feedCode);

        $config = $this->getConfig();
        if(empty($config))
        {
            $config = array();
        }

        $config[self::FEED] = $profileFeedConfig;
        return $config;
    }

    protected function getCustomConfig($index,$exception = true)
    {
        return $this->getConfigField($index,$this->getConfig(),$exception);
    }

    protected function getConfigField($index,$config,$exception = true)
    {
        $field = $this->getArrayField($index,$config);
        if(!isset($field) && $exception)
        {
            $this->logAndException("Can't load %s configuration.",$index);

        }
        return $field;
    }

    public function getReloadCache()
    {
        if(!isset($this->_reloadCache))
            $this->_reloadCache = self::DEF_RELOAD_CACHE;
        return $this->_reloadCache;
    }

    public function setReloadCache($reloadCache)
    {
        $this->_reloadCache = $reloadCache;
    }

    public function getFeedObject($feedCode = null)
    {
        if(!isset($feedCode))
            $feedCode = $this->getFeed();
        if(!isset($this->_feedObject))
            $this->_feedObject = Mage::getModel('nscexport/feed')->getFeedByCode($feedCode);
        return $this->_feedObject;
    }

    protected function getMagentoAttributes()
    {
         //$feedConfig = $this->getCustomConfig(self::FEED);
         $feedConfig = $this->getMergedProfileFeedConfig();
         $attributeInfoArray = $this->getAttributeFromArray($feedConfig);

         $attributes = array();
          if(empty($attributeInfoArray))
         {
         	if($this->exportCategoryTree())
         		 return $attributes;

         	$this->logAndException("Missing feed attributes configuration.");
         }

         $attributes = array();
         foreach ($attributeInfoArray as $attribute)
         {
             $magentoAttribute = $this->getArrayField(self::MAGENTO_ATTRIBUTE,$attribute);
             if(isset($magentoAttribute) && !empty($magentoAttribute) && !in_array($attribute,$attributes))
                 $attributes[] = $magentoAttribute;

             $prefix = $this->getArrayField(self::PREFIX,$attribute,"");
             $suffix = $this->getArrayField(self::SUFFIX,$attribute,"");
             $prefixSuffixAttributes = $this->helper()->grebVariables($prefix.$suffix);
             if(!empty($prefixSuffixAttributes))
             	$attributes = array_merge($attributes,$prefixSuffixAttributes);
         }

         //attribute from stock setup
         $common = $this->getArrayField("common",$feedConfig,array());
         $stock = $this->getArrayField("stock",$common);
         if(!isset($stock))
             $this->logAndException("Missing feed stock configuration.");
         $availabilityAttribute = $this->getArrayField("availability",$stock);
         if(!empty($availabilityAttribute))
             $attributes[] = $availabilityAttribute;

         //attribute from shipping setup
         $shipping = $this->getArrayField(self::SHIPPING,$common);
         if(isset($shipping))
         {
         	$dependentAttribute = $this->getArrayField(self::DEPENDENT_ATTRIBUTE,$shipping);
         	if(!empty($dependentAttribute))
            	 $attributes[] = $dependentAttribute;
         }

         //remove unexisting attributes
        $allAttributes = Mage::helper('nscexport/data_feed')->getAttributeCodes($this->getStoreId());
        $attributes = array_intersect($attributes, $allAttributes);
         
         return $attributes;
    }

    public function setConfig($config)
    {
        $this->setData(self::COL_CONFIG,json_encode($config));
    }

    public function getConfig()
    {
        $id = $this->getId();
         if(!isset($id))
             return null;
        $config = json_decode($this->getData(self::COL_CONFIG),true);
		return $config;
    }

    protected function getAttributeFromArray($input,$default = null)
    {
    	$attributes = $this->getArrayField(self::ATTRIBUTES,$input);
        $attributeInfoArray = $this->getArrayField(self::ATTRIBUTE,$attributes,$default);
		return $attributeInfoArray;
    }

    public function getConditions() {

        if (!$this->hasData(self::CONDITIONS)) {
            $config = $this->getConfig();
            if( $config !== null && isset( $config[self::ATTRIBUTE_FILTER][self::CONDITIONS])) {
                $this->setData(self::CONDITIONS, $config[self::ATTRIBUTE_FILTER][self::CONDITIONS]);
            } else {
                $this->setData(self::CONDITIONS, array());
            }
        }
        return $this->getData(self::CONDITIONS);
    }

    public function getStoreIdsByProfileIds($profileIds)
    {
    	$collection = $this->getCollection();
    	$select = $collection->getSelect();
    	$select->columns('store_id');
    	$select->group('store_id');
    	$select->where('export_id IN (?)',$profileIds);
    	$collection->load();
    	$storeIds = array();
    	foreach ($collection as $item)
    		$storeIds[] = $item->getStoreId();
    	return $storeIds;
    }

    public function getProfilesByIds($profileIds)
    {
    	$collection = $this->getCollection();
    	$select = $collection->getSelect();
    	$select->where('export_id IN (?)',$profileIds);
    	return $collection->load();
    }

    public function getProfilesByNames($names)
    {
    	$collection = $this->getCollection();
    	$select = $collection->getSelect();
    	$select->where('name IN (?)',$names);
    	return $collection->load();
    }

    public function updateProfileFeedConfig()
    {
        $feedObject = $this->getFeedObject();
        if(empty($feedObject))
            return;
        $feedConfig = $this->getFeedObject()->getAttributesSetup();
        $profileFeedConfig = $this->getCustomConfig(self::FEED,false);
        if(empty($feedConfig[self::ATTRIBUTES][self::ATTRIBUTE]) || empty($profileFeedConfig[self::ATTRIBUTES][self::ATTRIBUTE]))
            return;

        $attributesProfile = $profileFeedConfig[self::ATTRIBUTES][self::ATTRIBUTE];
        $attributesFeed = $feedConfig[self::ATTRIBUTES][self::ATTRIBUTE];

        //Fast check
        if($this->attributesCompare($attributesProfile,$attributesFeed))
            return;

        $attributesResult = array();
        $attributesProfileMap = array();
        $attributesCustom = array();

        //separate custom attributes
        foreach ($attributesProfile as $key => $data)
        {
            if(empty($data[self::CODE]))
                return;
            if($data[self::CODE] == self::CUSTOM_ATTRIBUTE)
            {
                $attributesCustom[] = $data;
                unset($attributesProfile[$key]);
            }
            else
            {
                $code = $data[self::CODE];

                if(array_key_exists($code,$attributesProfileMap))
                    array_push($attributesProfileMap[$code], $data);
                else
                    $attributesProfileMap[$code] = array($data);
            }
        }

        $attributeCodes = Mage::helper('nscexport/data_feed')->getAttributeCodes($this->getStoreId());
        //recreate profile attributes
        foreach ($attributesFeed as $data)
        {
            $index = 0;
            if(empty($data[self::CODE]))
                return;
            else
                $index = $data[self::CODE];

            if(isset($attributesProfileMap[$index][0]))
            {
                $attributesResult[] = $attributesProfileMap[$index][0];
                array_shift($attributesProfileMap[$index]);
            }
            else
            {
                $magentoAttribute = "";
                if(array_key_exists(self::MAGENTO_ATTRIBUTE, $data) && in_array($data[self::MAGENTO_ATTRIBUTE], $attributeCodes))
                    $magentoAttribute = $data[self::MAGENTO_ATTRIBUTE];

                $attributesResult[] = array(self::CODE => $index, self::MAGENTO_ATTRIBUTE => $magentoAttribute);
            }
        }
        $attributesResult = array_merge($attributesResult,$attributesCustom);

        //save to config
        $config = $this->getConfig();
        $config[self::FEED][self::ATTRIBUTES][self::ATTRIBUTE] = $attributesResult;
        $this->setConfig($config);
        $this->save();
    }

    public function isUploadable() {
        
        $config = $this->getUploadParams();
        $suffix = $this->getFeedObject()->getFileType();
        $fullFilename = $this->getFilename(true, $suffix);
        $enabled =  Mage::helper('nscexport/version')->isLicenseValid();
        
        return ( $enabled && isset( $config['enabled']) && $config['enabled'] == 1 &&
            is_file( $fullFilename) &&
            !empty( $config['hostname']) && !empty( $config['username']) && !empty( $config['password'])
        );
    }
    
    public function getUploadParams() {
        return $this->getCustomConfig( self::UPLOAD, false);
    }
           
    public function checkFtpConnection( $config) {
        
        $ftp = new Varien_Io_Ftp();
        try {
            // test pripojeni
            $ftpConfig = array(
                'host'      => $config['hostname'],
                'port' => $config['port'],
                'user'  => $config['username'],
                'password'  => $config['password'],
                'path' => $config['path'],
                'passive' => (bool) $config['passive_mode']
            );
            $ftp->open( $ftpConfig);
            
            // test prava zapisu
            $filename = "ftp_test.xml";
            $fullfilename = Mage::getBaseDir('var').'/'.$filename;
            file_put_contents( $fullfilename, "FTP TEST"); // vytvori novy soubor
            //nahraje na FTP
            if( !$ftp->write( $filename, $fullfilename)) {
                throw new Zend_Exception( 'Check write permissions!', self::CODE_ERROR);
            }
            // smaze z FTP
            if( !$ftp->rm( $filename)) {
                throw new Zend_Exception( 'Check delete permissions!', self::CODE_ERROR);
            }
            unlink( $fullfilename); // smaze z disku
            $ftp->close();
            return $this->helper()->__('Connection Successful!');
        } catch (Exception $e) {
            $ftp->close();
            return $this->helper()->__('Error: '.$e->getMessage()."!");
        }
    }
    
    public function uploadFeed() {
        
        $config = $this->getUploadParams();
        if( !$config['enabled'] || empty( $config['hostname']) || empty( $config['username']) || empty( $config['password'])) {
            throw new Zend_Exception( 'Upload via FTP is not enabled!', self::CODE_NOT_ENABLED);
        }
        
        $suffix = $this->getFeedObject()->getFileType();
        $fullFilename = $this->getFilename(true, $suffix);
        $filename = $this->getFilename(false, $suffix);
        
        if( !is_file($fullFilename)) {
            throw new Zend_Exception( 'Feed file does not exists!', self::CODE_ERROR);
        }
        
        $ftp = new Varien_Io_Ftp();
        $ftpConfig = array(
            'host'      => $config['hostname'],
            'port' => $config['port'],
            'user'  => $config['username'],
            'password'  => $config['password'],
            'path' => $config['path'],
            'passive' => (bool) $config['passive_mode']
        );
        $ftp->open( $ftpConfig);
        
        $result = $ftp->write($filename, $fullFilename);
        
        if( !$result) {
            $ftp->close();
            throw new Zend_Exception( 'Feed file can not be uploaded via FTP! Check permissions!', self::CODE_ERROR);
        }
        
        $ftp->close();
        
        return $result;
    }
    
    /**
     * Fast compare of both attribute arrays
     * @param unknown $attributesProfile
     * @param unknown $attributesFeed
     * @return boolean
     */
    protected function attributesCompare($attributesProfile,$attributesFeed)
    {
        $lastIndex = 0;
        foreach ($attributesFeed as $index => $data)
        {
            $lastIndex = $index;
            $code = '';
            if(isset($data[self::CODE]))
                $code = $data[self::CODE];
            else
                return false;

            if(isset($attributesProfile[$index][self::CODE]) && $attributesProfile[$index][self::CODE] == $code)
                continue;
            return false;
        }

        $lastIndex++;
        if(isset($attributesProfile[$lastIndex][self::CODE]) &&  $attributesProfile[$lastIndex][self::CODE] != self::CUSTOM_ATTRIBUTE)
            return false;
        return true;
    }
}