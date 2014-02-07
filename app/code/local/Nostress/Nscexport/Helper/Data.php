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
* Helper.
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Helper_Data extends Mage_Core_Helper_Abstract
{
	const STATUS_ACTIVE = 1;	//active products
	const NO_VISIBLE = 1;   //Visible NOWHERE
	
	const XML_PATH_NSC_GOOGLEANALYTICS_SETTINGS = 'nscexport/ga/';
	const XML_PATH_NSC_GENERAL_SETTINGS = 'nscexport/general/';
	const XML_PATH_NSC_GENERAL_ADD_PRODUCTS = 'add_products';
	const XML_PATH_NSC_GENERAL_URL_STORE = 'url_store';
	const XML_PATH_NSC_GENERAL_URL_CATEGORY = 'url_category';
	const XML_PATH_NSC_GENERAL_FILE_SUFFIX_SHOW = 'suffix_show';
	
	/**
	 * Puts information about products into xml File
	 *
	 * @param Nostress_Nscexport_Model_Nscexport $model
	 * @param String $xmlHead
	 * @param String $xmlTail
	 * @param NscexportXXXXXX model  $engineModul
	 * @return unknown
	 */
	public static function exportProducts($nscexportModel,$xmlHead,$xmlTail,$searchengineModel)
	{
		try
        {       	
        	Mage::helper('nscexport/version')->validateLicenceBackend();
        	$memoryLimit = (int)ini_get('memory_limit')*800000;
        	//Mage::log('$memoryLimit='.$memoryLimit);
        	$nscexportModel->setMessage('');    	
        	$nscexportModel->save();
			$profileId = $nscexportModel->getId();
        	
        	$breakGenerationProcess = false;  //stops generation of xml file
        	$timeStart = time();  //time of start of script    	
			
        	//open outputfile
        	$fileExists = false;
        	$io = Nostress_Nscexport_Helper_Data::getTempFile($nscexportModel,$fileExists,'a+');        	
        	if(!$fileExists)
        		$io->streamWrite($xmlHead);        		
        	
        	//prepare batch manager
        	$batchManager = Nostress_Nscexport_Helper_Data::prepareBatchManager($profileId);        	
        	$startMemory = memory_get_usage();
        	
        	while(self::exportProductBatch($batchManager,$nscexportModel,$searchengineModel,$io))
        	{
        		//$m = memory_get_usage() -  $startMemory;
        		//Mage::log('$memory_get_usage() -  $startMemory='.$m);
        		if((memory_get_usage() -  $startMemory > $memoryLimit) || Nostress_Nscexport_Helper_Data::breakGenerationProcess($timeStart))
				{        		 	
					$breakGenerationProcess = true;
					break;
				}
        	}
        	
        	if($breakGenerationProcess && $batchManager->profileHasRecordsToExport($profileId))
        	{	
        		$io->streamClose();  
        		//pocet zaznamu v tabulce category product
        		$allRecords = Mage::getModel('nscexport/categoryproducts')->getCategoryProductsCount($profileId);   
        		$expRecords = $batchManager->getRecordsCount($profileId);    
        		$nscexportModel->setMessageAndStatus(Mage::helper('nscexport')->__('Process exported %d/%d products. ',$allRecords-$expRecords,$allRecords).Mage::helper('nscexport')->__('Please rerun generation process!'),'INTERRUPTED');
        		return false;
        	}
        	else
        	{
        		$io->streamWrite($xmlTail);
    			$io->streamClose();
    			Nostress_Nscexport_Helper_Data::createOriginalFile($nscexportModel);
    			//Xml file path creation
        		$xmlUrl = Nostress_Nscexport_Helper_Data::getXmlUrl($nscexportModel->getFilename(),strtolower($nscexportModel->getSearchengine()));
				$nscexportModel->setUrl($xmlUrl);				
				$nscexportModel->setMessageAndStatus(Mage::helper('nscexport')->__('Export has been successfully generated'),'FINISHED');
				return true;
        	}        		
        }
    	catch (Exception $e) 
    	{
    		if($io != null)
    			$io->streamClose();
    		$nscexportModel->setMessageAndStatus(Mage::helper('nscexport')->__('Unable to generate an export').". ".$e->getMessage(),'ERROR');  
    		throw $e;			                                               
    	} 
        	
	}
	
	public static function prepareBatchManager($profileId)
	{
		$manager = Mage::getModel('nscexport/records');
		$manager->setBatchSize((int)Mage::getConfig()->getNode('default/nscexport/nscexport/batch_size'));
		if(!$manager->profileHasRecordsToExport($profileId))
		{
			//add records
			$relationIds = Mage::getModel('nscexport/categoryproducts')->getExportRelationIdsFiltred($profileId);
			$manager->saveRelations($profileId,$relationIds);
		}
		return $manager;
	}
	
	public static function exportProductBatch($batchManager,$nscexportModel,$searchengineModel,$io)
	{
		//load export batch
		$productBatch = $batchManager->getExportBatch($nscexportModel->getId());
		
		if(count($productBatch) == 0)
			return false;
		
		$storeId = (int)$nscexportModel->getStoreId();
		//load products
		$products = Nostress_Nscexport_Helper_Data::getProductsToExport($storeId,array_keys($productBatch));
		
		$tempResult = '';
		$category = null;
		
		$batchRecordIds = array();
		$loops = 0;
		do
		{
			$loops++;
			foreach($products as $product) 
			{		
				$productId = $product->getId();
				$batchRecordIds[] = $productBatch[$productId]['recordId'];	 
				
				//only active and visible products 
				if(($product->getVisibility() == self::NO_VISIBLE) || ($product->getStatus() != self::STATUS_ACTIVE))
				{
					unset($productBatch[$productId]);
					continue;
				}			
	
				$curProductCategoryId = $productBatch[$productId]['categoryId'];	
				$product->setStoreId($storeId);						
				if($category == null || $category->getId() != $curProductCategoryId)
					$category = Mage::getModel('catalog/category')->setStoreId($storeId)->load($curProductCategoryId);
				
				$nscexportModel->setCategory($category);
				$nscexportModel->setProduct($product);	
				
				$tempResult .= $searchengineModel->addProductAttributes();		
				unset($productBatch[$productId]);					
			}
			if(count($productBatch) > 0)
			{
				$products = Nostress_Nscexport_Helper_Data::getProductsToExport($storeId,array_keys($productBatch),false);
				if(count($products) == 0)
				{
					throw new Exception(Mage::helper('nscexport')->__("Products with ID's %s can't be loaded!",implode(",",array_keys($productBatch))));
					return false;
				}
			}
			else
				break;
		}
		while($loops <= 1);

		//save to file
		$io->streamWrite($tempResult);
		//delete records from database
		$batchManager->deleteRecords($batchRecordIds);
		return true;
	}
	
    /**
	 * Puts information about products into xml File
	 *
	 * @param Nostress_Nscexport_Model_Nscexport $model
	 * @param String $xmlHead
	 * @param String $xmlTail
	 * @param NscexportXXXXXX model  $engineModul
	 * @return unknown
	 */
	public static function exportProductsInCategoryTree($model,$xmlHead,$xmlTail,$engineModul)
	{
		try
        {   
        	$oldProductId = $model->getProductId();
        	$model->setMessage('');    	
        	$model->save();       	
        	$breakGenerationProcess = false;  //stops generation of xml file
        	$_statusActive = 1;	//export only active products
	  		$no_visible = 1;   //Visible NOWHERE
        	$timeStart = time();  //time of start of script    	
			$fileExists = false;
			try
        	{
				$io = Nostress_Nscexport_Helper_Data::getTempFile($model,$fileExists,'r+');
        	}
        	catch (Exception $e) 
    		{
    			$io = null;
    		}
        	$result = '';
        	
        	if(!$fileExists)
        	{
        		$result .= $xmlHead.$engineModul->addCategoryTree();
        	}
        	else 
        	{
        		while(($tmp = $io->streamRead()) == true)
        			$result .= $tmp;
        	}        		
        		
        	if($io != null)
        		$io->streamClose();
        	       		
        		
        	$maxProductId = Nostress_Nscexport_Helper_Data::getMaxProductId($model);        	
        	$categories = explode(',',$model->getCategoryIds());
        	if($model->getCategoryIds() != '') 
        	{
				while(sizeof($products = Nostress_Nscexport_Helper_Data::getProductsToNscexport($model)) !== 0 ) 
				{
					foreach($products as $_prod) 
					{
						if($_prod->getVisibility() == $no_visible)
							continue;
						if($_prod->getStatus() != $_statusActive) //only active products
							continue;
						$_prod->setStoreId($model->getStoreId());
						$prodCatIds = $_prod->getCategoryIds();
						foreach($prodCatIds as $catId) 
						{
							if(in_array($catId,$categories)) 
							{
								Mage::dispatchEvent('nscexport_product_get_final_price', array('product'=>$_prod , 'model'=>$model));
								$category = Mage::getModel('catalog/category')->load($catId);
								$model->setCategory($category);
								$model->setProduct($_prod);
								$categoryTag = '<category name="'.Nostress_Nscexport_Helper_Data::formatContent($category->getName()).'" id="'.$category->getId().'" >';
								
								$productXml = $engineModul->addProductAttributes();
								
								$result = str_replace($categoryTag,$categoryTag.$productXml,$result);								
								break;
							}
						}
					}
					$model->setNewProductId();
					if($breakGenerationProcess = Nostress_Nscexport_Helper_Data::breakGenerationProcess($timeStart))
						break;
				}
			}

			$io = Nostress_Nscexport_Helper_Data::getTempFile($model,$fileExists,'w');
			
        	if($breakGenerationProcess && $model->getProductId() <= $maxProductId)
        	{        		
        		$io->streamWrite($result);
        		$io->streamClose();
        		$result = (100* $model->getProductId())/$maxProductId;   
        		$model->setMessageAndStatus(Mage::helper('nscexport')->__('Export has been generated on ').(int)$result.Mage::helper('nscexport')->__('%. Please rerun generation process!'),'INTERRUPTED');
        		return $result;
        	}
        	else
        	{        		
        		$io->streamWrite($result.$xmlTail);
    			$io->streamClose();   				
    			
    			Nostress_Nscexport_Helper_Data::createOriginalFile($model);
    			//Xml file path creation
        		$xmlUrl = Nostress_Nscexport_Helper_Data::getXmlUrl($model->getFilename(),strtolower($model->getSearchengine()));
				$model->setUrl($xmlUrl);
				$model->setProductId(0);
				$model->setMessageAndStatus(Mage::helper('nscexport')->__('Export has been successfully generated'),'FINISHED');
        		return;
        	}        		
        }
    	catch (Exception $e) 
    	{
	   		if($io != null)
    			$io->streamClose(); 
    		$model->setProductId($oldProductId);   		
    		$model->setMessageAndStatus(Mage::helper('nscexport')->__('Unable to generate an export').". ".$e->getMessage(),'ERROR');    		
    		throw $e;			                                               
    	} 
    	return;
	}	
	
	
	/**
	 * Generates minimal price of bundle items,counts price from prices of bundle items
	 *
	 * @param unknown_type $product
	 * @param unknown_type $store
	 * @param unknown_type $includeTax
	 * @return unknown
	 */
	/*public static function generateBundleProducrMinimalPrice($product,$store,$includeTax,$original)
	{
		$optionCollection = self::getOptionCollection($product,$store);
		$price = 0;
		foreach($optionCollection as $option)
		{			
			$price += self::getOptionsMinimalPrice($product,$option,$store,$includeTax,$original);
		}		
		return $price;
	}
	
	public static function getOptionsMinimalPrice($product,$option,$store,$includeTax,$original)
	{
		$selectionCollection = Mage::getModel('bundle/selection')->getCollection()->setOptionIdsFilter(array($option->getId()));
		
		$minimalPrice = -1;
		
		$string = count($selectionCollection);
		foreach ($selectionCollection as $selection)
		{
			$product = Mage::getModel('catalog/product')->setStoreId($store->getId())->load($selection->getProductId());
			$price = self::convertProductPrice($store,$product,$includeTax,$original);			
			if($minimalPrice == -1 || $price >= 0 && $price < $minimalPrice)
			{
				$minimalPrice = $price;
				
				$prodstring = $product->getId();//smazat
			}
		}
		
		file_put_contents("test.txt",get_class($selectionCollection).$string."product:".$prodstring." minimal Price:".$minimalPrice."\n",'a+');
		
		return $minimalPrice;
	}
	
	protected static function convertProductPrice($store,$product,$includeTax,$original)
	{
		if($original)
			$price = $product->getPrice ();
		else 
			$price = $product->getFinalPrice ();
		$price = $store->convertPrice($price);
		$taxHelper = new Mage_Tax_Helper_Data ( );
		return $taxHelper->getPrice ( $product, $price, $includeTax);
	}*/
	
	/**
	 * Returns collection of products options
	 *
	 * @param unknown_type $product
	 * @param unknown_type $store
	 * @return unknown
	 */
	/*public static function getOptionCollection($product,$store)
    {       
    	$product->getTypeInstance(true)->setStoreFilter($store->getId(), $product);
        return $product->getTypeInstance(true)->getOptionsCollection($product);
        //$selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
        //    $product->getTypeInstance(true)->getOptionsIds($product),$product);
        //file_put_contents('test1.txt',count($selectionCollection).$product->getName().$product->getTypeInstance(true)->getOptionsIds($product));
        //$optionCollection->appendSelections($selectionCollection);
		//return $optionCollection;
    }*/
    

	
	/**
	 * Decides if process should be stopped or not.
	 *
	 * @param Time of script start $timeStart
	 * @return True if skript should be stopped.
	 */
	public static function breakGenerationProcess($timeStart)
	{
		$timeDifference = time()-$timeStart;
		
		//Mage::log('max_execution_time='.ini_get('max_execution_time'));
		//Mage::log('$timeDifference='.$timeDifference);
		if(ini_get('max_execution_time') == 0)
			return false;
		if($timeDifference > ini_get('max_execution_time') - Mage::getConfig()->getNode('default/nscexport/nscexport/time_rest'))
			return true;
		else
			return false;
	}
	
	/**
	 * Deletes old original file and renames temporary file to original file.
	 *
	 * @param Nscexport Profile $model
	 */
	public static function createOriginalFile($model)
	{
		//Mage::log('createOriginalFile() '.$model->getSearchengine().' STARTED');
		$fileName = $model->getFilename();
		$engineName = strtolower($model->getSearchengine());		
		
		$tempPrefix = (string)Mage::getConfig()->getNode('default/nscexport/temp_file_prefix');
		$oldFile = Nostress_Nscexport_Helper_Data::getFullFilePath($tempPrefix.$fileName,$engineName);
		$newFile = Nostress_Nscexport_Helper_Data::getFullFilePath($fileName,$engineName);
		
		//Mage::log('$oldFile = '.$oldFile.'|| $newFile = '.$newFile);
		if(is_file($oldFile))
		{
			//Mage::log('Delete old file '.$fileName.' and create new file '.$newFile);
			Nostress_Nscexport_Helper_Data::deleteXmlFile($fileName,$engineName);
			rename($oldFile,$newFile);
		}
		
		//Mage::log('createOriginalFile() '.$model->getSearchengine().' FINISHED');
	}
	
	 /**
     * Return real file path
     *
     * @return string
     */
    public static function getDirectoryPath($engine)
    {    	    	
        $path = (string)Mage::getConfig()->getNode('default/nscexport/engine/'.strtolower($engine).'/filepath');	
        $path = str_replace('//', '/', Mage::getBaseDir() . $path);
        return $path;
    }
	
	/**
	 * Returns opened file for writeing.
	 *
	 * @param Nscexport profile $model
	 * @param Boolean $fileExists
	 * @return Opened file for writeing.
	 */
	public static function getTempFile($model,&$fileExists,$mode)
	{
		$fileName = $model->getFilename();
		$io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $engineName = $model->getSearchengine();        
        $io->open(array('path' => Nostress_Nscexport_Helper_Data::getDirectoryPath($engineName).strtolower($engineName)));
        $tempPrefix = (string)Mage::getConfig()->getNode('default/nscexport/temp_file_prefix');
        $fileExists = $io->fileExists($tempPrefix.$fileName);    
        try 
        {
        	$io->streamOpen($tempPrefix.$fileName,$mode);
        }
        catch (Exception $e) 
        {
        	throw $e;        	
        }
        return $io;
	}
	
	/**
	 * Rerurns max product id.
	 *
	 * @param  Nscexport profile $model
	 * @return product id
	 */
	public static function getMaxProductId($model)
	{
		$products = Mage::getModel('catalog/product')->setStoreId((int)$model->getStoreId())->getCollection();    				
		$products->getSelect()->order(array('entity_id DESC'))->limit(1,1);
		return $products->getFirstItem()->getId();		
	}
	
	public static function getParentCategory($categoryId)
	{
		$category = Mage::getModel('catalog/category')->load($categoryId);
		return $category->getParentCategory();
	}
	
	public static function getFullCategoryPath($category,$store)
	{
		$rootCatId = $store->getRootCategoryId();
		$delimiter = '/*/';
		$result = $category->getName();		
		$category = $category->getParentCategory();
		while($category->getId() != $rootCatId)
		{			
			$result = $delimiter.$result;			
			$result = $category->getName().$result;
			$category = $category->getParentCategory();			
		}
		return $result;
	}
	
	/**
	 * Return number of product related to configuration of module Nscexport
	 *
	 * @param Nscexport $model
	 * @return collection of products
	 */
	public static function getProductsToExport($storeId,$productIds,$withDetails = true)
	{	
        $products = Mage::getModel('catalog/product')->getCollection();
        $products->setStoreId($storeId);
        $whereCondition = 'e.entity_id in ('.implode(",",$productIds).')';		
        $products->getSelect()->order('entity_id', 'desc')->where($whereCondition);
       
        $products->addAttributeToSelect('*');
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        $products->addAttributeToSelect($attributes);
        $products->addTaxPercents();
        if($withDetails)
            $products->addMinimalPrice()
            			->addFinalPrice();
            

        //$sqlString = $products->getSelect()->assemble();

        $products->load();
        return $products;    
	}
	
	/*
	 * Returns number of contributions for current product and store view.
	 */
	public static function getNumberOfContributions($storeId,$productId)
	{
		$reviews = Nostress_Nscexport_Helper_Data::getReviewsCollection($storeId,$productId);
		return $reviews->getSize();
	}
	
	/**
	 * Creates product url addres.
	 *
	 * @param unknown_type $product
	 * @param unknown_type $category
	 * @return string with url path
	 */
	public static function getProductUrl($product,$store,$category = null)
	{
		if(!$store->getConfig('catalog/seo/product_use_categories'))
			$category = null;
		
		$result = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK,false).$product->getUrlPath($category);	
		//$result = $store->getUrl().$product->getUrlPath($category);
		//if(strpos($result,$product->getUrlKey().'.html') === false)
		//	$result .= $product->getUrlKey().'.html';
		return $result;	
	}
	
	public static function getNumberOfReviews($product,$storeId)
	{
		if (!$product->getRatingSummary()) 
		{
	        Nostress_Nscexport_Helper_Data::setRatingSummary($product,$storeId);
	    }	
	   	return $product->getRatingSummary()->getReviewsCount();
	}
	
	private static function setRatingSummary($product,$storeId)
	{
		Mage::getModel('review/review')->getEntitySummary($product,$storeId);	    
	}
	
	/*
	 * Returns reviews url
	 */ 
    public static function getReviewsUrl($productId,$store)
	{
		return $store->getBaseUrl('link',false).'review/product/list/id/'.$productId;
	}
	
	/*
	 * Returns collection of reviews for current product and store
	 */
	public static function getReviewsCollection($storeId,$productId)
    {
        return Mage::getModel('review/review')->getCollection()
                ->addStoreFilter($storeId)
                ->addStatusFilter('approved')
                ->addEntityFilter('product', $productId)
                ->setDateOrder();
    }

	/*
	 * Returns url path to products image.
	 */
	public static function generateImageUrl($product,$store)
	{
		return $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA,false).'catalog/product'.$product->getImage();
	}

	/*
	 * Returns encoded string.
	 */
	public static function changeEncoding($newEncoding,$stringForEncoding)
	{
		if($stringForEncoding == "")
			return "";
		
		$extension = "mbstring";
		if(!extension_loaded($extension)) 
		{
			throw new Exception(Mage::helper('nscexport')->__('PHP Extension "%s" must be loaded', $extension).'.');
		}
		else 
			$oldEncoding = mb_detect_encoding($stringForEncoding);
		try
		{		
			$stringForEncoding = iconv($oldEncoding, $newEncoding.'//TRANSLIT', $stringForEncoding);
		}
		catch(Exception $e)
		{
			
			try
			{
				$stringForEncoding = iconv($oldEncoding, $newEncoding.'//IGNORE', $stringForEncoding);
			}
			catch(Exception $e)
			{				
				//echo $stringForEncoding;
				throw $e;
			}			
		}
		if($stringForEncoding == false)
			throw new Exception('Conversion from encoding '.$oldEncoding.' to '.$newEncoding.' failure. Following string can not be converted:<BR>'.$stringForEncoding);

		return $stringForEncoding;
	}

	/*
	 * Returns formated string form xml.
	 */
	public static function formatContent($content)
	{
		return htmlspecialchars(strip_tags(str_replace(">","> ",$content)));
	}
	
	/*
	 * Returns product atribute string for xml export.
	 */
	public static function formatProductAttribute($tagName,$formatContent,$content)//$changeEncoding,$newEncoding = null)
	{
		$result = "<".$tagName.">";
		if($formatContent)
			$content = htmlspecialchars(strip_tags(str_replace(">","> ",$content)));
		//if($changeEncoding)
		//	$content = self::changeEncoding($newEncoding,$content);
		$result .=$content;
		$result .= "</".$tagName.">";
		return $result;
	}

	/*
	 * Returns product atribute string for xml export.
	 * Optional boolean parameter $addCDATA - if true then content is envrloped by <![CDATA['content']]>
	 */
	public static function formatProductAttributeAdvanced($tagName,$formatContent,$content,$addCDATA,$tagNameAddition = "")//$changeEncoding,$newEncoding = null)
	{
		$result = "<".$tagName.$tagNameAddition.">";
		if($formatContent)
			$content = htmlspecialchars(strip_tags($content));
		if($addCDATA)
			$content = '<![CDATA['.$content.']]>';
			
		$result .= $content;
		$result .= "</".$tagName.">";
		return $result;
	}

	/*
	 * Returns product atribute string for xml export.
	 */
	public static function formatProductAttributePipe($formatContent,$content,$delimiter = "|")//$changeEncoding,$newEncoding = null)
	{
		if($formatContent)
			$content = htmlspecialchars(strip_tags($content));
		//if($changeEncoding)
		//	$content = self::changeEncoding($newEncoding,$content);
		$result =$content;
		$result .= $delimiter;
		return $result;
	}

	/*
	* Returns product atribute string for xml export.
	*/
	public static function formatProductAttributeTab($formatContent,$content,$delimiter = "\t")//$changeEncoding,$newEncoding = null)
	{
		if($formatContent)
			$content = htmlspecialchars(strip_tags($content));

		$result =$content;
		$result .= $delimiter;
		return $result;
	}
	/*
	 * Returns magento time shift.
	 */
	public static function getTimeShift()
	{
		return 2;
	}

	/**
	 * Deletes records(from table cron_schedule) which are asociated to searcheengine given by parameter
	 * @param $searchengine
	 */
	public static function deleteCronSchedule($searchengine)
	{
		$collection = Mage::getModel('cron/schedule')->getCollection();
		$searchengine = 'nscexport_nscexport'.$searchengine;
		foreach($collection as $item)
		{
			if(strtolower($item->getJobCode()) == $searchengine)
				$item->delete();
		}
		
	}
	/*
	 * Insert schduled task to table cron_schedule.
	 */
	public static function setCronSchedule($nscexportId,$jobCode,$status)
	{
		$nscexport = Mage::getModel('nscexport/nscexport')->load($nscexportId,'export_id');
		$timeShift = self::getTimeShift();

		$model = Mage::getModel('cron/schedule');
		$model->setJobCode($jobCode);
		$model->setStatus($status);

		$nscexportHour = intval($nscexport->getStartTimeHour());
		$nscexportMinute = intval($nscexport->getStartTimeMinute());
		$nscexportSecond = intval($nscexport->getStartTimeSecond());

		$now = getdate(time());

		if(intval($now['hours']+$timeShift) > $nscexportHour || ( $now['hours'] == $nscexportHour && ($now['minutes'] > $nscexportMinute || ($now['minutes'] == $nscexportMinute && $now['seconds'] >= $nscexportSecond))))
		{
			$now = getdate(strtotime("+1 day"));
			$scheduleTime = $now['year'].'-'.$now['mon'].'-'.$now['mday'].' '.($nscexportHour-$timeShift).':'.$nscexportMinute.':'.$nscexportSecond;
		}
		else
		{
			$scheduleTime = $now['year'].'-'.$now['mon'].'-'.$now['mday'].' '.($nscexportHour-$timeShift).':'.$nscexportMinute.':'.$nscexportSecond;
		}
		$model->setScheduledAt($scheduleTime);
		$model->setCreatedAt(now());
		$model->save();
	}

	/*
	 * Returns Ids of export profiles, which must be processed.
	 */
	public static function findNscexports($now,$nscexportId)
	{
		$model = Mage::getModel('nscexport/nscexport')->load($nscexportId,'export_id');
		$curEngineModelName = self::getEngineModelOriginalName($model->getSearchengine());

		$secondsPerHour = 3600;
		$secondsPerMinute = 60;
		$secondsPerDay = 86400;
		$timeShift = self::getTimeShift();;

		//echo "Cas Od ".$model->getData('start_time_hour')."H".$model->getData('start_time_minute')."M".$model->getData('start_time_second')."S <BR>";
		//echo "CAS do ".($now['hours']+$timeShift)."H".$now['minutes']."M".$now['seconds']."S <BR>";
		$timeFrom = ($model->getData('start_time_hour')*$secondsPerHour) + ($model->getData('start_time_minute') * $secondsPerMinute) + $model->getData('start_time_second');
		$timeTo = (($now['hours']+$timeShift)*$secondsPerHour) + ($now['minutes']*$secondsPerMinute) + $now['seconds'];

		$modelCollection = Mage::getResourceModel('nscexport/nscexport_collection');
		$modelCollection->load()->getItems();

		$idsToNscexport = Array();
		foreach($modelCollection as $mod)
		{
			if(self::getEngineModelOriginalName($mod->getSearchengine()) != $curEngineModelName)
				continue;
				
			$time = ($mod->getData('start_time_hour')*$secondsPerHour) + ($mod->getData('start_time_minute') * $secondsPerMinute) + $mod->getData('start_time_second');
				
			//echo "CasOd: ".$timeFrom." CasAkce: ".$time." CasDo: ".$timeTo."<BR>";
			//echo "CasAKCE ".$mod->getData('start_time_hour')."H".$mod->getData('start_time_minute')."M".$mod->getData('start_time_second')."S <BR>";
			if($time >= $timeFrom && $time <= $timeTo)
			{
				//echo "Prvni pripad <BR>";
				array_push($idsToNscexport, $mod->getId());
			}
			else if($timeTo < $timeFrom && ($time >= $timeFrom || $time <= $timeTo)) //else if(($time >= $timeFrom && $timeTo < $timeFrom) || ($time <= $timeTo && $timeTo < $timeFrom))
			{
				//echo "Druhy pripad <BR>";
				array_push($idsToNscexport, $mod->getId());
			}
		}
		return $idsToNscexport;
	}

	/*
	 * Returns true if it is possible to generate XML.
	 */
	public static function allowGenerateXml($nscexportId)
	{
		$model = Mage::getModel('nscexport/nscexport')->load($nscexportId,'export_id');
		if(!$model->getEnabled()) //disabled to generate XML
		return false;

		$frequencyDaily = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
		$frequencyWeekly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
		$frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

		$freq = $model->getFrequency();
		if($freq == $frequencyDaily)
		return true;
		else
		{	//frequency is weekly or monthly
			if($model->getUpdateTime() == null)
			return true;
			$lastGenerationTime = strtotime($model->getUpdateTime());
			if($freq == $frequencyWeekly)
			$testTime = strtotime("-1 week");	//time before week
			else if($freq == $frequencyMonthly)
			$testTime = strtotime("-1 month");	//time before month
				
			if($lastGenerationTime <= $testTime)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	/*
	 * Sets or deletes config data.
	 */
	public static function setOrDeleteConfigData($nscexportId,$searchEngine)
	{
		if($nscexportId == 0)
			self::deleteConfig($searchEngine);
		else
			self::setConfigData($nscexportId);
	}

	/*
	 * Deletes config for searchengine given by parameter $searchEngine;
	 */
	public static function deleteConfig($searchEngine)
	{
		//Clean up config cache.
		Mage::app()->cleanCache('config');
		$engineModelName = self::getEngineModelOriginalName($searchEngine);
		
		self::deleteCronSchedule($engineModelName);
		
		$cronStringPath = 'crontab/jobs/nscexport_nscexport'.$engineModelName.'/schedule/cron_expr';
		$cronModelPath =  'crontab/jobs/nscexport_nscexport'.$engineModelName.'/run/model';
		$path = 'nscexport/nscexport'.$engineModelName.'/';
			
		$enabledPath = $path.'enabled';
		Mage::getModel('core/config_data')->load($enabledPath, 'path')
		->delete();
		 
		$timePath = $path.'time';
		Mage::getModel('core/config_data')->load($timePath, 'path')
		->delete();

		$frequencyPath = $path.'frequency';
		Mage::getModel('core/config_data')->load($frequencyPath, 'path')
		->delete();

		$idPath = $path.'id';
		Mage::getModel('core/config_data')->load($idPath, 'path')
		->delete();

		Mage::getModel('core/config_data')->load($cronStringPath, 'path')
		->delete();

		Mage::getModel('core/config_data')->load($cronModelPath, 'path')
		->delete();
	}

	/*
	 * Sets config data for nscexport profile to table core_config_data.
	 */
	public static function setConfigData($nscexportId)
	{
		//Clean up config cache.
		Mage::app()->cleanCache('config');
		 
		$model = Mage::getModel('nscexport/nscexport')->load($nscexportId,'export_id');
		$engineModelName = self::getEngineModelOriginalName($model->getSearchengine());
		 
		$cronStringPath = 'crontab/jobs/nscexport_nscexport'.$engineModelName.'/schedule/cron_expr';
		$cronModelPath =  'crontab/jobs/nscexport_nscexport'.$engineModelName.'/run/model';
		 
		$path = 'nscexport/nscexport'.$engineModelName.'/';
		$enabled = $model->getEnabled(); // $this->getData('groups/nscexportseznam/enabled/value');
		$time = $model->getData('start_time_hour').','.$model->getData('start_time_minute').','.$model->getData('start_time_second');//  $this->getData('groups/nscexportseznam/fields/time/value');
		//$frequency = $model->getFrequency(); //$this->getData('groups/nscexportseznam/frequency/value');
		$frequency = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY; //set all cron frequency to daily
		 
		$enabledPath = $path.'enabled';
		Mage::getModel('core/config_data')->load($enabledPath, 'path')
		->setValue($enabled)
		->setPath($enabledPath)
		->save();


		$timePath = $path.'time';
		Mage::getModel('core/config_data')->load($timePath, 'path')
		->setValue($time)
		->setPath($timePath)
		->save();
		 

		$frequencyPath = $path.'frequency';
		Mage::getModel('core/config_data')->load($frequencyPath, 'path')
		->setValue($frequency)
		->setPath($frequencyPath)
		->save();

		$idPath = $path.'id';
		Mage::getModel('core/config_data')->load($idPath, 'path')
		->setValue($nscexportId)
		->setPath($idPath)
		->save();

		//$frequencyDaily = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
		//$frequencyWeekly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
		//$frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

		$cronDayOfWeek = date('N');

		/*$cronExprArray = array(
		 intval($model->getData('start_time_minute')),                                   # Minute
		 intval($model->getData('start_time_hour')),                                   # Hour
		 ( $frequency == $frequencyMonthly ) ? '1' : '*',     # Day of the Month
		 '*',                                                # Month of the Year
		 ( $frequency == $frequencyWeekly ) ? '1' : '*',       # Day of the Week
		 );*/

		$cronExprArray = array(
		intval($model->getData('start_time_minute')),                                   # Minute
		intval($model->getData('start_time_hour')),                                   # Hour
            '*',     # Day of the Month
            '*',                                                # Month of the Year
            '*',       # Day of the Week
		);

		$cronExprString = join(' ', $cronExprArray);

		try {
			Mage::getModel('core/config_data')
			->load($cronStringPath, 'path')
			->setValue($cronExprString)
			->setPath($cronStringPath)
			->save();
			Mage::getModel('core/config_data')
			->load($cronModelPath, 'path')
			->setValue((string) Mage::getConfig()->getNode($cronModelPath))
			->setPath($cronModelPath)
			->save();
		} catch (Exception $e) {
			throw new Exception(Mage::helper('cron')->__('Unable to save Cron expression'));
		}

	}

	/*
	 * Returns id of nscexport profile, whose  execution is nearest. Smallest time difference between
	 * execution time and present.
	 */
	public static function getNearestTime($searchEngine,$now)
	{
		$modelCollection = Mage::getResourceModel('nscexport/nscexport_collection');
		$modelCollection->load();

		$nearestNscexportId = 0;
		$hoursPerDay = 24;
		$secondsPerHour = 3600;
		$secondsPerMinute = 60;
		$firstTime = true;
		$currentDif = -1;
		$timeShift = self::getTimeShift();; //time shift od internal clock
		$nowSeconds = (($now['hours']+$timeShift)*$secondsPerHour) + ($now['minutes']*$secondsPerMinute) + $now['seconds'];

		//echo 'Actual time ='. ($now['hours']+$timeShift).'H '.$now['minutes'].'M '.$now['seconds'].'S';
		//echo 'NOW: '.$nowSeconds.'seconds'."\t";
		$curEngineModelName = self::getEngineModelOriginalName($searchEngine);
		
		foreach($modelCollection as $model)
		{
			//Search only through export profiles for curren searchengine
			if(self::getEngineModelOriginalName($model->getSearchengine()) != $curEngineModelName)
			 	continue;
			//count time different
			$modelTime = ($model->getData('start_time_hour')*$secondsPerHour) + ($model->getData('start_time_minute') * $secondsPerMinute) + $model->getData('start_time_second');
				
			$different = $modelTime - $nowSeconds;
				
			if($different < 0)
			$different += 	($hoursPerDay * $secondsPerHour);
				
			//echo 'ID:'.$model->getId().'modelTime: '.$modelTime.' dif: '.$different."\n";

			if($firstTime)
			{
				$currentDif = $different;
				$nearestNscexportId = $model->getId();
				$firstTime = false;
				continue;
			}
				
				
			if($different < $currentDif)
			{  //new export found
				$currentDif = $different;
				$nearestNscexportId = $model->getId();
			}
		}
		return $nearestNscexportId;
	}


	/*
	 * Returns xml files url path.
	 */
	public static function getXmlUrl($fileName,$engine)
	{
		$path = Nostress_Nscexport_Helper_Data::getPath($engine);
		$path = str_replace('/media/','',$path);
		$url = Mage::getBaseUrl('media').$path.$engine.'/'.$fileName;
		return $url;
	}

	/*
	 * Returns directory path to xmlfile
	 */
	public static function getPath($engine)
	{
		return (string)Mage::getConfig()->getNode('default/nscexport/engine/'.strtolower($engine).'/filepath');
	}

	/*
	 * Deletes specified xml file.
	 */
	public static function deleteXmlFile($fileName,$engine)
	{
		if($fileName == null || $fileName === '')
		return;
		$file = Nostress_Nscexport_Helper_Data::getFullFilePath($fileName,$engine);
		if (file_exists($file))
		{
			unlink($file);
		}
	}

	/*
	 * Return full file path.
	 */
	public static function getFullFilePath($fileName,$engine)
	{
		$file = str_replace('//', '/', Mage::getBaseDir() . Nostress_Nscexport_Helper_Data::getPath($engine).'/'.$engine.'/'.$fileName);
		return $file;
	}

	/**
	 * Renamse feed files.
	 * @param $searchengine
	 * @param $oldFileName
	 * @param $newFileName
	 * @param $tempPrefix
	 */
	public static function renameSearchengineProfileFiles($searchengine,$oldFileName,$newFileName,$tempPrefix = '')
	{
		//renam original file
		$oldFile = self::getFullFilePath($oldFileName,$searchengine);
		if(is_file($oldFile))
		{								
			$newFile = self::getFullFilePath($newFileName,$searchengine);
			rename($oldFile,$newFile);
		}
							
		//rename temp file
		$oldFile = self::getFullFilePath($tempPrefix.$oldFileName,$searchengine);
		if(is_file($oldFile))
		{								
			$newFile = self::getFullFilePath($tempPrefix.$newFileName,$searchengine);
			rename($oldFile,$newFile);	
		}
	}
	
	public static function deleteSearchengineProfileFiles($searchengine,$fileName,$tempPrefix = '')
	{
		//delete feed and temp feed
		self::deleteXmlFile($fileName,strtolower($searchengine));
		self::deleteXmlFile($tempPrefix.$fileName,strtolower($searchengine));
	}
	
	public static function updateConfigFields($searchengine)
	{
		$exportId = self::getNearestTime($searchengine,getdate(time())); 
		self::setOrDeleteConfigData($exportId,$searchengine);
	}
	
	public static function getFileSuffixByCode($searchengineCode)
	{
    	$suffix = "xml";    	
    	$engines = Mage::getConfig()->getNode('default/nscexport/engine')->asArray();					
		foreach($engines as $code => $engine)
		{
			if($code == $searchengineCode)
			{
				$suffix = $engine['filesuffix'];							 	
				break;
			}    	
		}
    	return $suffix;
	}
	
	public static function removeFileSuffix($filename)
	{
		$filename = str_ireplace(".xml","",$filename);
    	$filename = str_ireplace(".csv","",$filename);
    	return $filename;
	}
	
	public static function addFileSuffix($filename,$searchengineCode)
	{
		$filename .= ".".self::getFileSuffixByCode($searchengineCode);
    	return $filename;
	}
	
	public static function mapWebSuffixToCountryCode($webSuffix)
	{
		$result = "";
		switch($webSuffix)
		{
			case ".co.uk":
				$result = "GB";
				break;
			case ".eu":
			case ".com":
			case "":
				$result = "OTHERS";
				break;
			default:
				$result = str_replace(".","",strtoupper($webSuffix));
				break;
		}
		return $result;
	}
	
	public static function getEngineCollection()
	{
		$engCollection = Mage::getConfig()->getNode('default/nscexport/engine')->asArray();
		foreach($engCollection as $key => $engine)
		{
			if(!isset($engine['title']))
			{	
				$engine['title'] = Mage::helper('nscexport')->getEngineTitle($engine);
				$engCollection[$key] = $engine;
			}
		}
		return $engCollection;
	}
	
	public static function getEngineInfo($engineCode)
	{
		return Mage::getConfig()->getNode('default/nscexport/engine/'.$engineCode)->asArray();
	}
	
	public static function getAllowedEngines()
	{
		$engines = Mage::getConfig()->getNode('default/nscexport/engines/allow');
		return explode(",",$engines);
	}
	
    public static function getAllowedEnginesCollection($selectedEngine)
    {
    	$engineCollection = self::getEngineCollection();
    	$allowedEngines = self::getAllowedEngines();
    	if(isset($allowedEngines[0]) && $allowedEngines[0] == 'all')
    		return $engineCollection;
    	
    	$resultCollection = array();
    	foreach($engineCollection as $code => $engine)
    	{
    		if(in_array($code, $allowedEngines) || $code == $selectedEngine)
    			$resultCollection[$code] = $engine;
    	}
    	return $resultCollection;
    }
	
	public static function deleteProfile($profileId)
	{
		$model = Mage::getModel('nscexport/nscexport');

		//Get old export values and delete xmlfile 
		$curModel = Mage::getModel('nscexport/nscexport')->load($profileId,'export_id');
		Nostress_Nscexport_Helper_Data::deleteXmlFile($curModel->getFilename(),strtolower($curModel->getSearchengine()));
		$tempPrefix = (string)Mage::getConfig()->getNode('default/nscexport/temp_file_prefix');
		Nostress_Nscexport_Helper_Data::deleteXmlFile($tempPrefix.$curModel->getFilename(),strtolower($curModel->getSearchengine()));
				
		//get searchengine of deleted profile
		$searchEngine = $curModel->getSearchengine();				
									
		$model->setId($profileId)->delete();		
									 						
		$nscexportId = Nostress_Nscexport_Helper_Data::getNearestTime($searchEngine,getdate(time()));				
		Nostress_Nscexport_Helper_Data::setOrDeleteConfigData($nscexportId,$searchEngine);
	}
	
	public static function generateProfile($profileId)
	{
		$model = Mage::getModel('nscexport/nscexport')->load($profileId,'export_id');    
	    $nscexport = Mage::getModel(self::getEngineModelName($model->getSearchengine()));  	    	
		$nscexport->generateXml($profileId);
	}
	
	public static function getEngineModelName($engineName)
	{
		$result  = 'nscexport/nscexport';
		$result .= 	self::getEngineModelOriginalName($engineName);
		return $result;
	}
	
	public static function getEngineModelOriginalName($engineName)
	{
		$engineName = strtolower($engineName);
		$info = self::getEngineInfo($engineName);
		$result = $engineName;
		if(isset($info['model']) && $info['model'] != "")
			$result = 	$info['model'];
		return $result;
	}
	
	/**
	 *
	 * @param Mage_Cron_Model_Schedule $schedule
	 * @return Varien_Object
	 */
	public function getRuntime(Mage_Cron_Model_Schedule $schedule)
	{
		$execTime = $schedule->getExecutedAt();
		$stopTime = $schedule->getFinishedAt();
		if($execTime == '0000-00-00 00:00:00') {
			$runtime = new Varien_Object();
			$runtime->setIsPending(1);
			$runtime->setHours(0);
			$runtime->setMinutes(0);
			$runtime->setSeconds(0);
			$runtime->setToString('0h 0m 0s');
			return $runtime;
		}

		if($stopTime == '0000-00-00 00:00:00') {
			$stopTime = now();
		}

		$runtime = strtotime($stopTime) - strtotime($execTime);
		$runtimeSec = $runtime % 60;
		$runtimeMin = (int) ($runtime / 60) % 60;
		$runtimeHour = (int) ($runtime / 3600);

		$runtime = new Varien_Object();
		$runtime->setIsPending(0);
		$runtime->setHours($runtimeHour);
		$runtime->setMinutes($runtimeMin);
		$runtime->setSeconds($runtimeSec);
		$runtime->setToString($runtimeHour . 'h ' . $runtimeMin . 'm ' . $runtimeSec . 's');
		return $runtime;
	}

	/**
	 * @todo render as Column in Grid
	 * @todo unterscheiden zwischen überfällig Rot normal schwarz
	 *
	 * @param Mage_Cron_Model_Schedule $schedule
	 * @return Varien_Object
	 */
	public function getStartingIn(Mage_Cron_Model_Schedule $schedule)
	{
		$schedTime = $schedule->getScheduledAt();

		if($schedTime == '0000-00-00 00:00:00' or $schedTime == '')
        {
			$runtime = new Varien_Object();
			$runtime->setHours(0);
			$runtime->setMinutes(0);
			$runtime->setSeconds(0);
			$runtime->setToString('0h 0m 0s');
			return $runtime;
		}

		// Calc Time interval till Exec
		$starttime = strtotime($schedTime) - strtotime(now());
		$prefix = '+';
		if($starttime < 0) {
			$prefix = '-';
			$starttime *= - 1;
		}
		$runtimeSec = $starttime % 60;
		$runtimeMin = (int) ($starttime / 60) % 60;
		$runtimeHour = (int) ($starttime / 3600);

		$runtime = new Varien_Object();
		$runtime->setHours($runtimeHour);
		$runtime->setMinutes($runtimeMin);
		$runtime->setSeconds($runtimeSec);
		$runtime->setPrefix($prefix);
		$runtime->setToString($runtimeHour . 'h ' . $runtimeMin . 'm ' . $runtimeSec . 's');

		return $runtime;
	}

	/**
	 *
	 * @return 
	 */
	public function getAvailableJobCodes()
	{
		return Mage::getConfig()->getNode('crontab/jobs');
	}

    /**
     * Transforms a datetime string into a DateTime object in the UTC (GMT) timezone
     * (Assumes that $datetime_string is currently in the timezone set in the Magento config)
     * @param  $datetime_string
     * @return DateTime
     */
    public function dateCorrectTimeZoneForDb($datetime_string)
    {
        $timezone_mage = new DateTimeZone(Mage::getStoreConfig('general/locale/timezone'));

        //$timezone_php = date_default_timezone_get();
        $datetime_mage = new DateTime($datetime_string, $timezone_mage);
        $datetime_offset = $datetime_mage->getOffset(); // offset in seconds, including daylight savings time
        $datetime_mage->modify('-'.$datetime_offset.' seconds');

        return $datetime_mage;
    }
	
    public static function getExportProcessesCodes()
    {
    	$jobCodes = Mage::getSingleton('nscexport/schedule_code')->get();		
		foreach($jobCodes as $key => $code)
		{
			
			if(strpos($code,'nscexport_') === FALSE)
				unset($jobCodes[$key]);
		}
		
		$result = array();
		foreach($jobCodes as $key => $code)
		{
			//$result[str_replace("nscexport_nscexport","",$key)] = str_replace("nscexport_nscexport","",$code);
			$engine = self::getEngineInfo(str_replace("nscexport_nscexport","",$code));
			$result[$key] = $engine['name'].$engine['suffix'];
		}
		return $result;
    }
    
    public static function updateCategoryProducts($profileId,$categoryproducts,$storeId)
    {
		$catProdModel = Mage::getModel('nscexport/categoryproducts');
		$catProdModel->updateCategoryProducts($profileId,$categoryproducts,$storeId);	
		Mage::getSingleton('adminhtml/session')->addSuccess(
              	Mage::helper('nscexport')->__('The export profile-product relations has been updated.')
        );
    }
    
    public static function prepareCategoryProductCollection($store,$category)
    {
    	$collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('category_id')
            ->addStoreFilter($store)
            ->joinField('position',            	
                'catalog/category_product',
                'position',
            	'product_id=entity_id',
                'category_id='.$category->getId(),
                'left')
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
    	$collection->addCategoryFilter($category,true);
        $collection->setStoreId($store->getId());
        return $collection; 
    }
    
    
    /**
     * Return store config value for key
     *
     * @param   string $key
     * @return  string
     */
    public function getNscGoogleanalyticsStoreConfig ($key, $flag=false)
    {
        $path = self::XML_PATH_NSC_GOOGLEANALYTICS_SETTINGS . $key;
        if ($flag) {
            return Mage::getStoreConfigFlag($path);
        } else {
            return Mage::getStoreConfig($path);
        }
    }
    
    /**
     * Return store config value for key
     *
     * @param   string $key
     * @return  string
     */
    public function getNscGeneralStoreConfig ($key, $flag=false)
    {
        $path = self::XML_PATH_NSC_GENERAL_SETTINGS.$key;
        if ($flag) {
            return Mage::getStoreConfigFlag($path);
        } else {
            return Mage::getStoreConfig($path);
        }
    }
    
    
    protected function getConfig($configPath) {
		return Mage::getConfig ()->getNode ($configPath);
	}	
    
	public function getMerchantName($engineName) 
	{
		return ( string ) $this->getConfig ('default/nscexport/merchant/'.$engineName.'_username' );
	}
    
	public function addProductToExportProfiles($product)
	{
		$relationsUpdated = false;
		foreach($product->getStoreIds() as $productStoreViewId)
		{
			$profileCollection =  Mage::getModel('nscexport/nscexport')->getCollectionByStoreId($productStoreViewId);
			$categoryIds = $product->getCategoryIds();
			
			$catProdModel = Mage::getModel('nscexport/categoryproducts');
			
			
			foreach($profileCollection as $profile)
			{
				foreach($categoryIds as $categoryId)
				{
					if($catProdModel->isCategoryInProfile($categoryId,$profile->getId()))
					{
						$catProdModel->addProductToProfile($product->getId(),$categoryId,$profile->getId());
						$relationsUpdated = true;
					}
				}
			}			
		}	
		if($relationsUpdated)
			Mage::getSingleton('adminhtml/session')->addSuccess(
                	Mage::helper('nscexport')->__('The export profile-product relations has been updated.')
            	);	
	}
	
	public function getEngineTitle($engine)
	{
		if(!isset($engine) || !isset($engine['name']) || !isset($engine['suffix']))
			return "";
		$title = $engine['name'].$engine['suffix'];
		if(isset($engine['filesuffix']) && $this->getNscGeneralStoreConfig(Nostress_Nscexport_Helper_Data::XML_PATH_NSC_GENERAL_FILE_SUFFIX_SHOW) == 1)
		{
			$title .= " - ".$engine['filesuffix'];
		}
		return $title;
	}
	/*
	 * Rerurns array of centrum category records
	 */
	public static function getCentrumCategory()
	{
		return array(
		        '1' => 'Auto-moto, lodě' ,
            '101' => '&nbsp;&nbsp;Autodíly' ,
            '100' => '&nbsp;&nbsp;Autodoplňky' ,
            '107' => '&nbsp;&nbsp;&nbsp;&nbsp;Antiradary' ,
            '259' => '&nbsp;&nbsp;&nbsp;&nbsp;Auto audio &amp video@' ,
            '108' => '&nbsp;&nbsp;&nbsp;&nbsp;Doplňky pro nákladní vozidla' ,
            '112' => '&nbsp;&nbsp;&nbsp;&nbsp;Externí doplňky' ,
            '111' => '&nbsp;&nbsp;&nbsp;&nbsp;Interní doplňky' ,
            '110' => '&nbsp;&nbsp;&nbsp;&nbsp;Péče o auto' ,
            '109' => '&nbsp;&nbsp;&nbsp;&nbsp;Pneumatiky' ,
            '113' => '&nbsp;&nbsp;&nbsp;&nbsp;Zabezpečení' ,
            '114' => '&nbsp;&nbsp;&nbsp;&nbsp;Zahrádky, nosiče' ,
            '102' => '&nbsp;&nbsp;Lodě' ,
            '103' => '&nbsp;&nbsp;Motocykly' ,
            '104' => '&nbsp;&nbsp;Nářadí' ,
            '105' => '&nbsp;&nbsp;Sportovní vozidla' ,
            '2' => 'Dětské zboží' ,
            '191' => '&nbsp;&nbsp;Dětské ložní prádlo@' ,
            '203' => '&nbsp;&nbsp;Dětský nábytek@' ,
            '341' => '&nbsp;&nbsp;Hračky@' ,
            '121' => '&nbsp;&nbsp;Kočárky' ,
            '754' => '&nbsp;&nbsp;Oděvy@' ,
            '124' => '&nbsp;&nbsp;Péče o dítě' ,
            '125' => '&nbsp;&nbsp;Plenky' ,
            '3' => 'Dům, byt a zahrada' ,
            '126' => '&nbsp;&nbsp;Bazény' ,
            '132' => '&nbsp;&nbsp;Domácí spotřebiče' ,
            '232' => '&nbsp;&nbsp;&nbsp;&nbsp;Díly a doplňky' ,
            '147' => '&nbsp;&nbsp;&nbsp;&nbsp;Malé kuchyňské spotřebiče@' ,
            '229' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní spotřebiče' ,
            '765' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Luxy' ,
            '228' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Větráky' ,
            '764' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Žehličky a šicí stroje' ,
            '231' => '&nbsp;&nbsp;&nbsp;&nbsp;Pračky a sušičky' ,
            '233' => '&nbsp;&nbsp;&nbsp;&nbsp;Velké kuchyňské spotřebiče' ,
            '240' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chladničky a mrazničky' ,
            '241' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Myčky' ,
            '242' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sporáky a trouby' ,
            '129' => '&nbsp;&nbsp;Kuchyně' ,
            '143' => '&nbsp;&nbsp;&nbsp;&nbsp;Grily a Barbecue' ,
            '146' => '&nbsp;&nbsp;&nbsp;&nbsp;Kuchyňské potřeby' ,
            '144' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Formy na pečení' ,
            '145' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hrnce a pánve' ,
            '756' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kořenky' ,
            '148' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nože' ,
            '147' => '&nbsp;&nbsp;&nbsp;&nbsp;Kuchyňské spotřebiče' ,
            '151' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kávovary' ,
            '150' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kuchyňské roboty' ,
            '153' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mixéry' ,
            '154' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ruční mixéry' ,
            '152' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mikrovlnné trouby' ,
            '155' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Toustovače' ,
            '149' => '&nbsp;&nbsp;&nbsp;&nbsp;Nádobí (stolní' ,
            '156' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Barové nádobí' ,
            '757' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Čajový servis' ,
            '157' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jídelní servis' ,
            '159' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Příbory' ,
            '160' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Servírovací příbory' ,
            '201' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Prostírání@' ,
            '161' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Servírovací talíře a tácy' ,
            '162' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sklenice a hrnky' ,
            '133' => '&nbsp;&nbsp;Potřeby pro chovatele' ,
            '243' => '&nbsp;&nbsp;&nbsp;&nbsp;Kočky' ,
            '244' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní' ,
            '245' => '&nbsp;&nbsp;&nbsp;&nbsp;Psi' ,
            '246' => '&nbsp;&nbsp;&nbsp;&nbsp;Ptáci' ,
            '247' => '&nbsp;&nbsp;&nbsp;&nbsp;Rybičky' ,
            '127' => '&nbsp;&nbsp;Potřeby pro kutily' ,
            '134' => '&nbsp;&nbsp;&nbsp;&nbsp;Barvy a laky' ,
            '136' => '&nbsp;&nbsp;&nbsp;&nbsp;Elektro' ,
            '826' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chladící zařízení a vzduchotechnika' ,
            '838' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Měřící technika' ,
            '138' => '&nbsp;&nbsp;&nbsp;&nbsp;Nářadí' ,
            '139' => '&nbsp;&nbsp;&nbsp;&nbsp;Ochranné pomůcky' ,
            '141' => '&nbsp;&nbsp;&nbsp;&nbsp;Tapety' ,
            '137' => '&nbsp;&nbsp;&nbsp;&nbsp;Výrobky pro kuchyň a koupelnu' ,
            '837' => '&nbsp;&nbsp;Stavební materiál' ,
            '140' => '&nbsp;&nbsp;&nbsp;&nbsp;Dlažby a obklady' ,
            '825' => '&nbsp;&nbsp;&nbsp;&nbsp;Dveře a okna' ,
            '135' => '&nbsp;&nbsp;&nbsp;&nbsp;Zabezpečovací technika' ,
            '142' => '&nbsp;&nbsp;&nbsp;&nbsp;Železářské zboží' ,
            '128' => '&nbsp;&nbsp;Úklid a čištění' ,
            '131' => '&nbsp;&nbsp;Vybavení domácnosti' ,
            '166' => '&nbsp;&nbsp;&nbsp;&nbsp;Bytový textil' ,
            '191' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dětské ložní prádlo' ,
            '201' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kuchyňský textil' ,
            '193' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lůžkoviny' ,
            '212' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Matrace@' ,
            '196' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Osušky' ,
            '181' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Povlečení' ,
            '198' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Přehozy' ,
            '192' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Přikrývky a deky' ,
            '171' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Záclony, závěsy a rolety' ,
            '163' => '&nbsp;&nbsp;&nbsp;&nbsp;Dekorace' ,
            '175' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Domácí vůně' ,
            '176' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Drobnůstky' ,
            '177' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hodiny' ,
            '185' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kuřácké potřeby' ,
            '178' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ostatní bytové dekorace' ,
            '181' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Povlečení@' ,
            '183' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rámy na obrazy' ,
            '173' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rohože a předložky' ,
            '182' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sváteční dekorace' ,
            '760' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vánoční' ,
            '761' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Velikonoční' ,
            '184' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vázy' ,
            '678' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Výrobky z papíru a párty potřeby@' ,
            '186' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Zrcadla' ,
            '164' => '&nbsp;&nbsp;&nbsp;&nbsp;Koupelny' ,
            '165' => '&nbsp;&nbsp;&nbsp;&nbsp;Krby' ,
            '167' => '&nbsp;&nbsp;&nbsp;&nbsp;Nábytek' ,
            '202' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dekorativní a konferenční stolky' ,
            '203' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dětský nábytek' ,
            '204' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jídelní nábytek' ,
            '377' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kancelářský nábytek@' ,
            '206' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Knihovny' ,
            '208' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Křesla' ,
            '207' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kuchyňské a jídelní stoly' ,
            '209' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Média stojany' ,
            '771' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PC stolky@' ,
            '211' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Police a regály' ,
            '212' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Postele a matrace' ,
            '763' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Matrace' ,
            '762' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Postele' ,
            '824' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Proutěný nábytek' ,
            '213' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sedací soupravy' ,
            '214' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Skříně' ,
            '216' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Skříňky a komody' ,
            '844' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Školní nábytek' ,
            '210' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TV a video stolky' ,
            '215' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Zahradní nábytek' ,
            '217' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Židle' ,
            '168' => '&nbsp;&nbsp;&nbsp;&nbsp;Osvětlení' ,
            '797' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Koupelnová svítidla' ,
            '222' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nástěnné lampy' ,
            '225' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Speciální osvětlení' ,
            '224' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Stojací lampy' ,
            '221' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Stolní lampy' ,
            '223' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Stropní lampy' ,
            '798' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Světelné zdroje' ,
            '226' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Svíčky a svícny' ,
            '227' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venkovní osvětlení' ,
            '169' => '&nbsp;&nbsp;&nbsp;&nbsp;Předložky a koberce' ,
            '130' => '&nbsp;&nbsp;Zahrada' ,
            '9' => '&nbsp;&nbsp;&nbsp;&nbsp;Květiny@' ,
            '759' => '&nbsp;&nbsp;&nbsp;&nbsp;Semena a sazeničky' ,
            '215' => '&nbsp;&nbsp;&nbsp;&nbsp;Zahradní nábytek@' ,
            '758' => '&nbsp;&nbsp;&nbsp;&nbsp;Zahradní nářadí' ,
            '4' => 'Elektronika' ,
            '248' => '&nbsp;&nbsp;Audio' ,
            '259' => '&nbsp;&nbsp;&nbsp;&nbsp;Auto audio &amp video' ,
            '275' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Přehrávače' ,
            '274' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Příslušenství' ,
            '276' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Reproduktory' ,
            '277' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Zesilovače' ,
            '260' => '&nbsp;&nbsp;&nbsp;&nbsp;Budíky a radiobudíky' ,
            '261' => '&nbsp;&nbsp;&nbsp;&nbsp;CD přehrávače' ,
            '262' => '&nbsp;&nbsp;&nbsp;&nbsp;Gramofony' ,
            '263' => '&nbsp;&nbsp;&nbsp;&nbsp;Kazetové přehrávače' ,
            '264' => '&nbsp;&nbsp;&nbsp;&nbsp;Mikrofony' ,
            '265' => '&nbsp;&nbsp;&nbsp;&nbsp;MP3 přehrávače' ,
            '266' => '&nbsp;&nbsp;&nbsp;&nbsp;Přenosné přístroje' ,
            '279' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Diktafony' ,
            '278' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Discmany' ,
            '280' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Malé kazetové přehrávače' ,
            '281' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MP3 přehrávače' ,
            '282' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rádia' ,
            '283' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Walkmany' ,
            '269' => '&nbsp;&nbsp;&nbsp;&nbsp;Rádia' ,
            '268' => '&nbsp;&nbsp;&nbsp;&nbsp;Reprobedny' ,
            '270' => '&nbsp;&nbsp;&nbsp;&nbsp;Sluchátka' ,
            '271' => '&nbsp;&nbsp;&nbsp;&nbsp;Speciální audiopřístroje' ,
            '272' => '&nbsp;&nbsp;&nbsp;&nbsp;Stereosystémy' ,
            '273' => '&nbsp;&nbsp;&nbsp;&nbsp;Zesilovače' ,
            '250' => '&nbsp;&nbsp;Foto a video' ,
            '299' => '&nbsp;&nbsp;&nbsp;&nbsp;Digitální fotoaparáty' ,
            '300' => '&nbsp;&nbsp;&nbsp;&nbsp;Klasické fotoaparáty' ,
            '301' => '&nbsp;&nbsp;&nbsp;&nbsp;Příslušenství' ,
            '311' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Batohy a brašny' ,
            '304' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Blesky' ,
            '305' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Filmy' ,
            '306' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Objektivy' ,
            '307' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ostatní příslušenství' ,
            '308' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Příslušenství pro digitální fotoaparáty' ,
            '309' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Příslušenství videokamer' ,
            '310' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Stativy' ,
            '302' => '&nbsp;&nbsp;&nbsp;&nbsp;Videokamery' ,
            '303' => '&nbsp;&nbsp;&nbsp;&nbsp;Vybavení fotolaboratoří' ,
            '251' => '&nbsp;&nbsp;GPS a navigace' ,
            '252' => '&nbsp;&nbsp;Komunikace' ,
            '312' => '&nbsp;&nbsp;&nbsp;&nbsp;Bezdrátové telefony' ,
            '313' => '&nbsp;&nbsp;&nbsp;&nbsp;Doplňky pro mobilní telefony' ,
            '326' => '&nbsp;&nbsp;&nbsp;&nbsp;Faxy@' ,
            '315' => '&nbsp;&nbsp;&nbsp;&nbsp;Mobilní telefony' ,
            '316' => '&nbsp;&nbsp;&nbsp;&nbsp;Příslušenství' ,
            '317' => '&nbsp;&nbsp;&nbsp;&nbsp;Telefony' ,
            '318' => '&nbsp;&nbsp;&nbsp;&nbsp;Vysílačky' ,
            '253' => '&nbsp;&nbsp;Optika' ,
            '319' => '&nbsp;&nbsp;&nbsp;&nbsp;Dalekohledy' ,
            '320' => '&nbsp;&nbsp;&nbsp;&nbsp;Doplňky' ,
            '321' => '&nbsp;&nbsp;&nbsp;&nbsp;Noční vidění' ,
            '322' => '&nbsp;&nbsp;&nbsp;&nbsp;Teleskopy' ,
            '323' => '&nbsp;&nbsp;&nbsp;&nbsp;Zaměřovače' ,
            '254' => '&nbsp;&nbsp;Ostatní elektronika' ,
            '249' => '&nbsp;&nbsp;Příslušenství' ,
            '284' => '&nbsp;&nbsp;&nbsp;&nbsp;Baterie' ,
            '291' => '&nbsp;&nbsp;&nbsp;&nbsp;Čištění a opravy' ,
            '557' => '&nbsp;&nbsp;&nbsp;&nbsp;Datové nosiče@' ,
            '286' => '&nbsp;&nbsp;&nbsp;&nbsp;Dálkové ovladače' ,
            '287' => '&nbsp;&nbsp;&nbsp;&nbsp;Kabely a konektory' ,
            '288' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní doplňky' ,
            '289' => '&nbsp;&nbsp;&nbsp;&nbsp;Záruky' ,
            '290' => '&nbsp;&nbsp;&nbsp;&nbsp;Zásuvky a prodlužovačky' ,
            '255' => '&nbsp;&nbsp;Projektory' ,
            '324' => '&nbsp;&nbsp;&nbsp;&nbsp;Meotary' ,
            '325' => '&nbsp;&nbsp;&nbsp;&nbsp;Multimediální projektory' ,
            '257' => '&nbsp;&nbsp;Tiskárny, kopírky a faxy' ,
            '326' => '&nbsp;&nbsp;&nbsp;&nbsp;Faxové přístroje' ,
            '767' => '&nbsp;&nbsp;&nbsp;&nbsp;Kopírky' ,
            '327' => '&nbsp;&nbsp;&nbsp;&nbsp;Tiskárny' ,
            '329' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Inkoustové' ,
            '330' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jehličkové' ,
            '331' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laserové' ,
            '328' => '&nbsp;&nbsp;&nbsp;&nbsp;Tonery a jiné příslušenství' ,
            '258' => '&nbsp;&nbsp;TV a video' ,
            '332' => '&nbsp;&nbsp;&nbsp;&nbsp;Digitální video přehrávače' ,
            '333' => '&nbsp;&nbsp;&nbsp;&nbsp;Domácí kinosystémy' ,
            '334' => '&nbsp;&nbsp;&nbsp;&nbsp;DVD přehrávače' ,
            '335' => '&nbsp;&nbsp;&nbsp;&nbsp;Příslušenství' ,
            '336' => '&nbsp;&nbsp;&nbsp;&nbsp;Televizory' ,
            '337' => '&nbsp;&nbsp;&nbsp;&nbsp;Videopřehrávače' ,
            '13' => 'Hobby a umění' ,
            '677' => '&nbsp;&nbsp;Hudební nástroje' ,
            '839' => '&nbsp;&nbsp;&nbsp;&nbsp;Příslušenství' ,
            '678' => '&nbsp;&nbsp;Papírenské výrobky' ,
            '682' => '&nbsp;&nbsp;&nbsp;&nbsp;Papírenské potřeby' ,
            '683' => '&nbsp;&nbsp;&nbsp;&nbsp;Party potřeby' ,
            '684' => '&nbsp;&nbsp;&nbsp;&nbsp;Přáníčka' ,
            '685' => '&nbsp;&nbsp;&nbsp;&nbsp;Svatební dekorace' ,
            '679' => '&nbsp;&nbsp;Sbírky a starožitnosti' ,
            '686' => '&nbsp;&nbsp;&nbsp;&nbsp;Autogramy a rukopisy' ,
            '688' => '&nbsp;&nbsp;&nbsp;&nbsp;Film a hudba' ,
            '689' => '&nbsp;&nbsp;&nbsp;&nbsp;Historie' ,
            '690' => '&nbsp;&nbsp;&nbsp;&nbsp;Kartičky' ,
            '691' => '&nbsp;&nbsp;&nbsp;&nbsp;Kulturní a náboženské předměty' ,
            '692' => '&nbsp;&nbsp;&nbsp;&nbsp;Mince a odznaky' ,
            '693' => '&nbsp;&nbsp;&nbsp;&nbsp;Miniatury a figuríny' ,
            '702' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní' ,
            '694' => '&nbsp;&nbsp;&nbsp;&nbsp;Panenky' ,
            '696' => '&nbsp;&nbsp;&nbsp;&nbsp;Přepravní prostředky' ,
            '697' => '&nbsp;&nbsp;&nbsp;&nbsp;Reklamní předměty' ,
            '698' => '&nbsp;&nbsp;&nbsp;&nbsp;Sportovní předměty' ,
            '699' => '&nbsp;&nbsp;&nbsp;&nbsp;Umění a fotografie' ,
            '687' => '&nbsp;&nbsp;&nbsp;&nbsp;Upomínkové předměty' ,
            '700' => '&nbsp;&nbsp;&nbsp;&nbsp;Vojenství' ,
            '821' => '&nbsp;&nbsp;&nbsp;&nbsp;Zbraně' ,
            '701' => '&nbsp;&nbsp;&nbsp;&nbsp;Známky' ,
            '680' => '&nbsp;&nbsp;Umění' ,
            '703' => '&nbsp;&nbsp;&nbsp;&nbsp;Fotografie' ,
            '704' => '&nbsp;&nbsp;&nbsp;&nbsp;Kresba, malba' ,
            '705' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní umělecké předměty' ,
            '707' => '&nbsp;&nbsp;&nbsp;&nbsp;Otisky a grafika' ,
            '706' => '&nbsp;&nbsp;&nbsp;&nbsp;Plákáty a reprodukce' ,
            '708' => '&nbsp;&nbsp;&nbsp;&nbsp;Sošky' ,
            '716' => '&nbsp;&nbsp;&nbsp;&nbsp;Umělecké potřeby@' ,
            '681' => '&nbsp;&nbsp;Záliby a řemesla' ,
            '710' => '&nbsp;&nbsp;&nbsp;&nbsp;Fotografování' ,
            '711' => '&nbsp;&nbsp;&nbsp;&nbsp;Korálky' ,
            '712' => '&nbsp;&nbsp;&nbsp;&nbsp;Modely' ,
            '713' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní řemeslné potřeby' ,
            '714' => '&nbsp;&nbsp;&nbsp;&nbsp;Papírnické potřeby' ,
            '715' => '&nbsp;&nbsp;&nbsp;&nbsp;Práce se dřevem' ,
            '847' => '&nbsp;&nbsp;&nbsp;&nbsp;Pyrotechnika' ,
            '719' => '&nbsp;&nbsp;&nbsp;&nbsp;Śití a vyšívání' ,
            '716' => '&nbsp;&nbsp;&nbsp;&nbsp;Umělecké potřeby' ,
            '717' => '&nbsp;&nbsp;&nbsp;&nbsp;Výroba šperků' ,
            '718' => '&nbsp;&nbsp;&nbsp;&nbsp;Známky a nálepky' ,
            '720' => '&nbsp;&nbsp;&nbsp;&nbsp;Železniční modely' ,
            '5' => 'Hry a hračky' ,
            '338' => '&nbsp;&nbsp;Akční figurky' ,
            '339' => '&nbsp;&nbsp;Autíčka' ,
            '827' => '&nbsp;&nbsp;Dřevěné hračky' ,
            '340' => '&nbsp;&nbsp;Elektronické hračky' ,
            '341' => '&nbsp;&nbsp;Hračky pro malé děti' ,
            '342' => '&nbsp;&nbsp;Hry' ,
            '351' => '&nbsp;&nbsp;&nbsp;&nbsp;Deskové hry' ,
            '352' => '&nbsp;&nbsp;&nbsp;&nbsp;Karetní hry' ,
            '353' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní' ,
            '768' => '&nbsp;&nbsp;&nbsp;&nbsp;Soubory her' ,
            '354' => '&nbsp;&nbsp;&nbsp;&nbsp;Stolní hry' ,
            '355' => '&nbsp;&nbsp;&nbsp;&nbsp;Šipky' ,
            '343' => '&nbsp;&nbsp;Ostatní hračky' ,
            '344' => '&nbsp;&nbsp;Panenky' ,
            '345' => '&nbsp;&nbsp;Plyšáci' ,
            '346' => '&nbsp;&nbsp;Puzzle' ,
            '347' => '&nbsp;&nbsp;Stavebnice' ,
            '348' => '&nbsp;&nbsp;Venkovní hračky' ,
            '349' => '&nbsp;&nbsp;Videohry' ,
            '356' => '&nbsp;&nbsp;&nbsp;&nbsp;Doplňky' ,
            '357' => '&nbsp;&nbsp;&nbsp;&nbsp;Hry na konzole' ,
            '358' => '&nbsp;&nbsp;&nbsp;&nbsp;Konzole' ,
            '593' => '&nbsp;&nbsp;&nbsp;&nbsp;PC hry@' ,
            '350' => '&nbsp;&nbsp;Výukové hračky' ,
            '7' => 'Kancelář' ,
            '375' => '&nbsp;&nbsp;Kancelářské vybavení' ,
            '378' => '&nbsp;&nbsp;&nbsp;&nbsp;Etiketovací systémy' ,
            '379' => '&nbsp;&nbsp;&nbsp;&nbsp;Kalkulačky' ,
            '380' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní kancelářské vybavení' ,
            '255' => '&nbsp;&nbsp;&nbsp;&nbsp;Projektory@' ,
            '317' => '&nbsp;&nbsp;&nbsp;&nbsp;Telefony@' ,
            '257' => '&nbsp;&nbsp;&nbsp;&nbsp;Tiskárny, kopírky a faxy@' ,
            '376' => '&nbsp;&nbsp;Kancelářské zboží' ,
            '397' => '&nbsp;&nbsp;&nbsp;&nbsp;Balicí potřeby' ,
            '392' => '&nbsp;&nbsp;&nbsp;&nbsp;Kalendáře a plánovače' ,
            '393' => '&nbsp;&nbsp;&nbsp;&nbsp;Papírenské produkty' ,
            '405' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kancelářský papír' ,
            '401' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nálepky' ,
            '400' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Notesy a zápisníky' ,
            '402' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ostatní papírenské zboží' ,
            '403' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Papír pro fototisk' ,
            '394' => '&nbsp;&nbsp;&nbsp;&nbsp;Pořadače' ,
            '395' => '&nbsp;&nbsp;&nbsp;&nbsp;Prezentační tabule' ,
            '328' => '&nbsp;&nbsp;&nbsp;&nbsp;Příslušenství k tiskárnám@' ,
            '396' => '&nbsp;&nbsp;&nbsp;&nbsp;Psací potřeby' ,
            '398' => '&nbsp;&nbsp;&nbsp;&nbsp;Základní zboží' ,
            '377' => '&nbsp;&nbsp;Kancelářský nábytek' ,
            '406' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní kancelářský nábytek' ,
            '771' => '&nbsp;&nbsp;&nbsp;&nbsp;PC stoly' ,
            '407' => '&nbsp;&nbsp;&nbsp;&nbsp;Pracovní plochy' ,
            '408' => '&nbsp;&nbsp;&nbsp;&nbsp;Skříně' ,
            '8' => 'Knihy, hudba, video' ,
            '412' => '&nbsp;&nbsp;Časopisy' ,
            '774' => '&nbsp;&nbsp;&nbsp;&nbsp;Pro dámy' ,
            '773' => '&nbsp;&nbsp;&nbsp;&nbsp;Pro pány' ,
            '772' => '&nbsp;&nbsp;&nbsp;&nbsp;Zájmové' ,
            '409' => '&nbsp;&nbsp;Hudba' ,
            '413' => '&nbsp;&nbsp;&nbsp;&nbsp;Blues' ,
            '414' => '&nbsp;&nbsp;&nbsp;&nbsp;Country' ,
            '415' => '&nbsp;&nbsp;&nbsp;&nbsp;Dětská' ,
            '812' => '&nbsp;&nbsp;&nbsp;&nbsp;Domácí' ,
            '809' => '&nbsp;&nbsp;&nbsp;&nbsp;Etnická' ,
            '416' => '&nbsp;&nbsp;&nbsp;&nbsp;Folk' ,
            '417' => '&nbsp;&nbsp;&nbsp;&nbsp;Hard rock a metal' ,
            '418' => '&nbsp;&nbsp;&nbsp;&nbsp;Jazz' ,
            '419' => '&nbsp;&nbsp;&nbsp;&nbsp;Klasická' ,
            '808' => '&nbsp;&nbsp;&nbsp;&nbsp;Mluvené slovo' ,
            '422' => '&nbsp;&nbsp;&nbsp;&nbsp;Muzikály' ,
            '424' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní' ,
            '807' => '&nbsp;&nbsp;&nbsp;&nbsp;Pop' ,
            '426' => '&nbsp;&nbsp;&nbsp;&nbsp;Rap, Hip hop a Jungle' ,
            '806' => '&nbsp;&nbsp;&nbsp;&nbsp;Relaxační' ,
            '427' => '&nbsp;&nbsp;&nbsp;&nbsp;Rock' ,
            '428' => '&nbsp;&nbsp;&nbsp;&nbsp;Soul, R&ampB' ,
            '429' => '&nbsp;&nbsp;&nbsp;&nbsp;Soundracky' ,
            '425' => '&nbsp;&nbsp;&nbsp;&nbsp;Sváteční' ,
            '430' => '&nbsp;&nbsp;&nbsp;&nbsp;Taneční a elektronická' ,
            '805' => '&nbsp;&nbsp;&nbsp;&nbsp;Techno' ,
            '410' => '&nbsp;&nbsp;Knihy' ,
            '431' => '&nbsp;&nbsp;&nbsp;&nbsp;Audioknihy' ,
            '775' => '&nbsp;&nbsp;&nbsp;&nbsp;Auto-moto' ,
            '776' => '&nbsp;&nbsp;&nbsp;&nbsp;Beletrie' ,
            '432' => '&nbsp;&nbsp;&nbsp;&nbsp;Biografie' ,
            '433' => '&nbsp;&nbsp;&nbsp;&nbsp;Cestopisy' ,
            '435' => '&nbsp;&nbsp;&nbsp;&nbsp;Dům, byt a zahrada' ,
            '436' => '&nbsp;&nbsp;&nbsp;&nbsp;E-knihy' ,
            '780' => '&nbsp;&nbsp;&nbsp;&nbsp;Finance a účetnictví' ,
            '437' => '&nbsp;&nbsp;&nbsp;&nbsp;Historie' ,
            '438' => '&nbsp;&nbsp;&nbsp;&nbsp;Jazykové a literární vědy' ,
            '449' => '&nbsp;&nbsp;&nbsp;&nbsp;Kuchařky' ,
            '779' => '&nbsp;&nbsp;&nbsp;&nbsp;Mapy, atlasy a průvodce' ,
            '439' => '&nbsp;&nbsp;&nbsp;&nbsp;Náboženství a spiritualita' ,
            '853' => '&nbsp;&nbsp;&nbsp;&nbsp;Odborná literatura' ,
            '441' => '&nbsp;&nbsp;&nbsp;&nbsp;Počítače a internet' ,
            '440' => '&nbsp;&nbsp;&nbsp;&nbsp;Podnikání' ,
            '777' => '&nbsp;&nbsp;&nbsp;&nbsp;Poezie' ,
            '434' => '&nbsp;&nbsp;&nbsp;&nbsp;Pro děti' ,
            '778' => '&nbsp;&nbsp;&nbsp;&nbsp;Próza' ,
            '443' => '&nbsp;&nbsp;&nbsp;&nbsp;Rodina a vztahy' ,
            '444' => '&nbsp;&nbsp;&nbsp;&nbsp;Sci-fi a fantasy' ,
            '445' => '&nbsp;&nbsp;&nbsp;&nbsp;Sociální vědy' ,
            '446' => '&nbsp;&nbsp;&nbsp;&nbsp;Sport' ,
            '854' => '&nbsp;&nbsp;&nbsp;&nbsp;Technická literatura' ,
            '448' => '&nbsp;&nbsp;&nbsp;&nbsp;Učebnice' ,
            '447' => '&nbsp;&nbsp;&nbsp;&nbsp;Umění' ,
            '452' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Architektura a design' ,
            '454' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fotografie' ,
            '455' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hudba' ,
            '450' => '&nbsp;&nbsp;&nbsp;&nbsp;Věda' ,
            '828' => '&nbsp;&nbsp;&nbsp;&nbsp;Zábavná literatura' ,
            '451' => '&nbsp;&nbsp;&nbsp;&nbsp;Zdraví a fitness' ,
            '411' => '&nbsp;&nbsp;Video a DVD' ,
            '457' => '&nbsp;&nbsp;&nbsp;&nbsp;Akční a dobrodružné' ,
            '458' => '&nbsp;&nbsp;&nbsp;&nbsp;Animované' ,
            '811' => '&nbsp;&nbsp;&nbsp;&nbsp;České' ,
            '461' => '&nbsp;&nbsp;&nbsp;&nbsp;Dětské filmy a pohádky' ,
            '466' => '&nbsp;&nbsp;&nbsp;&nbsp;Dokumentární a naučné' ,
            '460' => '&nbsp;&nbsp;&nbsp;&nbsp;Drama' ,
            '795' => '&nbsp;&nbsp;&nbsp;&nbsp;Erotické' ,
            '804' => '&nbsp;&nbsp;&nbsp;&nbsp;Historické' ,
            '462' => '&nbsp;&nbsp;&nbsp;&nbsp;Horory' ,
            '463' => '&nbsp;&nbsp;&nbsp;&nbsp;Hudební' ,
            '464' => '&nbsp;&nbsp;&nbsp;&nbsp;Komedie' ,
            '465' => '&nbsp;&nbsp;&nbsp;&nbsp;Mezinárodní' ,
            '802' => '&nbsp;&nbsp;&nbsp;&nbsp;Rodinné' ,
            '799' => '&nbsp;&nbsp;&nbsp;&nbsp;Romantické' ,
            '800' => '&nbsp;&nbsp;&nbsp;&nbsp;Sci-fi a Fantasy' ,
            '801' => '&nbsp;&nbsp;&nbsp;&nbsp;Thriller a Krimi' ,
            '803' => '&nbsp;&nbsp;&nbsp;&nbsp;Válečné' ,
            '810' => '&nbsp;&nbsp;&nbsp;&nbsp;Western' ,
            '468' => '&nbsp;&nbsp;&nbsp;&nbsp;Zdraví a sport' ,
            '9' => 'Květiny a dárky' ,
            '469' => '&nbsp;&nbsp;Aranžování' ,
            '819' => '&nbsp;&nbsp;Dárky a dárkové předměty' ,
            '470' => '&nbsp;&nbsp;Růže' ,
            '759' => '&nbsp;&nbsp;Sazeničky a semena@' ,
            '10' => 'Oděvy a obuv' ,
            '471' => '&nbsp;&nbsp;Cestovní brašny a batohy' ,
            '473' => '&nbsp;&nbsp;Dámské' ,
            '481' => '&nbsp;&nbsp;&nbsp;&nbsp;Bundy a kabáty' ,
            '483' => '&nbsp;&nbsp;&nbsp;&nbsp;Kalhoty' ,
            '484' => '&nbsp;&nbsp;&nbsp;&nbsp;Kostýmky' ,
            '486' => '&nbsp;&nbsp;&nbsp;&nbsp;Spodní prádlo' ,
            '636' => '&nbsp;&nbsp;&nbsp;&nbsp;Sportovní oděvy@' ,
            '488' => '&nbsp;&nbsp;&nbsp;&nbsp;Sukně' ,
            '489' => '&nbsp;&nbsp;&nbsp;&nbsp;Svršky' ,
            '496' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Blůzy a košile' ,
            '497' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ramínkové' ,
            '498' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sportovní' ,
            '499' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Svetry' ,
            '500' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Trička' ,
            '490' => '&nbsp;&nbsp;&nbsp;&nbsp;Šaty' ,
            '754' => '&nbsp;&nbsp;Dětské' ,
            '475' => '&nbsp;&nbsp;Hodinky' ,
            '501' => '&nbsp;&nbsp;&nbsp;&nbsp;Dámské' ,
            '502' => '&nbsp;&nbsp;&nbsp;&nbsp;Pánské' ,
            '503' => '&nbsp;&nbsp;&nbsp;&nbsp;Sportovní' ,
            '834' => '&nbsp;&nbsp;Kožené oděvy' ,
            '820' => '&nbsp;&nbsp;Látky' ,
            '476' => '&nbsp;&nbsp;Novorozenecké' ,
            '829' => '&nbsp;&nbsp;Obuv' ,
            '485' => '&nbsp;&nbsp;&nbsp;&nbsp;Dámská obuv' ,
            '491' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kozačky' ,
            '492' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sandály' ,
            '493' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sportovní obuv' ,
            '494' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Večerní obuv, lodičky' ,
            '495' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vycházková obuv' ,
            '507' => '&nbsp;&nbsp;&nbsp;&nbsp;Pánská obuv' ,
            '518' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kotníčková obuv' ,
            '520' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sandály' ,
            '519' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Společenská obuv' ,
            '521' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sportovní obuv' ,
            '522' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Turistická obuv' ,
            '523' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vycházková obuv' ,
            '833' => '&nbsp;&nbsp;&nbsp;&nbsp;Zdravotní obuv' ,
            '835' => '&nbsp;&nbsp;Oděvní doplňky' ,
            '482' => '&nbsp;&nbsp;&nbsp;&nbsp;Dámské doplňky' ,
            '505' => '&nbsp;&nbsp;&nbsp;&nbsp;Pánské doplňky' ,
            '512' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Klobouky' ,
            '513' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kravaty' ,
            '514' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Manžetové knoflíčky' ,
            '515' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ostatní doplňky' ,
            '516' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pásky' ,
            '517' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rukavice' ,
            '836' => '&nbsp;&nbsp;&nbsp;&nbsp;Rukavice' ,
            '477' => '&nbsp;&nbsp;Pánské' ,
            '504' => '&nbsp;&nbsp;&nbsp;&nbsp;Bundy a kabáty' ,
            '506' => '&nbsp;&nbsp;&nbsp;&nbsp;Kalhoty' ,
            '508' => '&nbsp;&nbsp;&nbsp;&nbsp;Ponožky' ,
            '509' => '&nbsp;&nbsp;&nbsp;&nbsp;Spodní prádlo' ,
            '637' => '&nbsp;&nbsp;&nbsp;&nbsp;Sportovní oděvy@' ,
            '511' => '&nbsp;&nbsp;&nbsp;&nbsp;Svršky' ,
            '524' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Košile' ,
            '843' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Oblekové košile' ,
            '525' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Polokošile' ,
            '529' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vycházkové košile' ,
            '526' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sportovní' ,
            '527' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Svetry' ,
            '528' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Trička' ,
            '478' => '&nbsp;&nbsp;Sluneční brýle' ,
            '479' => '&nbsp;&nbsp;Speciální oděvy' ,
            '530' => '&nbsp;&nbsp;&nbsp;&nbsp;Kostýmy' ,
            '531' => '&nbsp;&nbsp;&nbsp;&nbsp;Profesní stejnokroje' ,
            '532' => '&nbsp;&nbsp;&nbsp;&nbsp;Sportovní oděvy' ,
            '533' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cyklooděvy' ,
            '534' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dresy' ,
            '535' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Golfové oděvy' ,
            '536' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lovecké oděvy' ,
            '480' => '&nbsp;&nbsp;Šperky' ,
            '537' => '&nbsp;&nbsp;&nbsp;&nbsp;Brože a spony' ,
            '538' => '&nbsp;&nbsp;&nbsp;&nbsp;Drahé kameny' ,
            '539' => '&nbsp;&nbsp;&nbsp;&nbsp;Náboženské šperky' ,
            '540' => '&nbsp;&nbsp;&nbsp;&nbsp;Náhrdelníky' ,
            '541' => '&nbsp;&nbsp;&nbsp;&nbsp;Náramky' ,
            '542' => '&nbsp;&nbsp;&nbsp;&nbsp;Náušnice' ,
            '543' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní šperky' ,
            '545' => '&nbsp;&nbsp;&nbsp;&nbsp;Přívesky' ,
            '544' => '&nbsp;&nbsp;&nbsp;&nbsp;Prsteny' ,
            '546' => '&nbsp;&nbsp;&nbsp;&nbsp;Šperkovnice' ,
            '11' => 'Počítače' ,
            '547' => '&nbsp;&nbsp;Doplňky a periférie' ,
            '557' => '&nbsp;&nbsp;&nbsp;&nbsp;Datové nosiče' ,
            '563' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Audiokazety' ,
            '564' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CD' ,
            '565' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Diskety' ,
            '566' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dvd' ,
            '568' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paměťové karty' ,
            '567' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pouzdra a obaly' ,
            '569' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Videokazety' ,
            '287' => '&nbsp;&nbsp;&nbsp;&nbsp;Kabely a konektory@' ,
            '559' => '&nbsp;&nbsp;&nbsp;&nbsp;Modemy' ,
            '781' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Externí' ,
            '782' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Interní' ,
            '560' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní doplňky' ,
            '561' => '&nbsp;&nbsp;&nbsp;&nbsp;Reprobedny' ,
            '562' => '&nbsp;&nbsp;&nbsp;&nbsp;Vstupní zařízení' ,
            '570' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Klávesnice' ,
            '571' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Myši a trackbally' ,
            '548' => '&nbsp;&nbsp;Komponenty' ,
            '572' => '&nbsp;&nbsp;&nbsp;&nbsp;CD, DVD jednotky' ,
            '573' => '&nbsp;&nbsp;&nbsp;&nbsp;Disketové jednotky' ,
            '574' => '&nbsp;&nbsp;&nbsp;&nbsp;Hard disky' ,
            '575' => '&nbsp;&nbsp;&nbsp;&nbsp;I/O karty a adaptéry' ,
            '576' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní komponenty' ,
            '577' => '&nbsp;&nbsp;&nbsp;&nbsp;Paměti' ,
            '578' => '&nbsp;&nbsp;&nbsp;&nbsp;Procesory' ,
            '580' => '&nbsp;&nbsp;&nbsp;&nbsp;Síťové karty' ,
            '581' => '&nbsp;&nbsp;&nbsp;&nbsp;Video karty' ,
            '583' => '&nbsp;&nbsp;&nbsp;&nbsp;Základní desky' ,
            '584' => '&nbsp;&nbsp;&nbsp;&nbsp;Zálohovací páskové jednotky' ,
            '582' => '&nbsp;&nbsp;&nbsp;&nbsp;Zvukové karty' ,
            '549' => '&nbsp;&nbsp;Monitory' ,
            '550' => '&nbsp;&nbsp;Notebooky' ,
            '585' => '&nbsp;&nbsp;&nbsp;&nbsp;Doplňky' ,
            '586' => '&nbsp;&nbsp;&nbsp;&nbsp;Počítače' ,
            '587' => '&nbsp;&nbsp;&nbsp;&nbsp;Tašky a kufříky' ,
            '551' => '&nbsp;&nbsp;PDA' ,
            '588' => '&nbsp;&nbsp;&nbsp;&nbsp;Doplňky' ,
            '589' => '&nbsp;&nbsp;&nbsp;&nbsp;PDA přístroje' ,
            '596' => '&nbsp;&nbsp;&nbsp;&nbsp;Software@' ,
            '555' => '&nbsp;&nbsp;Síťová zařízení' ,
            '600' => '&nbsp;&nbsp;&nbsp;&nbsp;Huby a switche' ,
            '287' => '&nbsp;&nbsp;&nbsp;&nbsp;Kabely a konektory@' ,
            '602' => '&nbsp;&nbsp;&nbsp;&nbsp;Routery a brány' ,
            '580' => '&nbsp;&nbsp;&nbsp;&nbsp;Síťové karty@' ,
            '552' => '&nbsp;&nbsp;Skenery' ,
            '553' => '&nbsp;&nbsp;Software' ,
            '591' => '&nbsp;&nbsp;&nbsp;&nbsp;Dětský' ,
            '592' => '&nbsp;&nbsp;&nbsp;&nbsp;Grafický' ,
            '593' => '&nbsp;&nbsp;&nbsp;&nbsp;Hry' ,
            '594' => '&nbsp;&nbsp;&nbsp;&nbsp;Obchodní a kancelářský' ,
            '595' => '&nbsp;&nbsp;&nbsp;&nbsp;Operační systémy' ,
            '596' => '&nbsp;&nbsp;&nbsp;&nbsp;Pro PDA' ,
            '597' => '&nbsp;&nbsp;&nbsp;&nbsp;Síťový' ,
            '598' => '&nbsp;&nbsp;&nbsp;&nbsp;Užitný' ,
            '599' => '&nbsp;&nbsp;&nbsp;&nbsp;Výukový' ,
            '794' => '&nbsp;&nbsp;&nbsp;&nbsp;Vývojářský' ,
            '554' => '&nbsp;&nbsp;Stolní počítače' ,
            '327' => '&nbsp;&nbsp;Tiskárny@' ,
            '6' => 'Potraviny a nápoje' ,
            '360' => '&nbsp;&nbsp;Dárkové balíčky' ,
            '361' => '&nbsp;&nbsp;Nápoje' ,
            '769' => '&nbsp;&nbsp;&nbsp;&nbsp;Alkohol' ,
            '365' => '&nbsp;&nbsp;&nbsp;&nbsp;Čaje' ,
            '755' => '&nbsp;&nbsp;&nbsp;&nbsp;Džusy' ,
            '363' => '&nbsp;&nbsp;&nbsp;&nbsp;Káva' ,
            '749' => '&nbsp;&nbsp;&nbsp;&nbsp;Limonády' ,
            '770' => '&nbsp;&nbsp;&nbsp;&nbsp;Pivo' ,
            '364' => '&nbsp;&nbsp;&nbsp;&nbsp;Víno' ,
            '362' => '&nbsp;&nbsp;Potraviny' ,
            '840' => '&nbsp;&nbsp;&nbsp;&nbsp;Dětská výživa' ,
            '366' => '&nbsp;&nbsp;&nbsp;&nbsp;Dezerty' ,
            '367' => '&nbsp;&nbsp;&nbsp;&nbsp;Instantní potraviny' ,
            '368' => '&nbsp;&nbsp;&nbsp;&nbsp;Koření' ,
            '851' => '&nbsp;&nbsp;&nbsp;&nbsp;Lahůdky' ,
            '370' => '&nbsp;&nbsp;&nbsp;&nbsp;Maso a uzeniny' ,
            '841' => '&nbsp;&nbsp;&nbsp;&nbsp;Mléčné výrobky' ,
            '371' => '&nbsp;&nbsp;&nbsp;&nbsp;Mořské výrobky' ,
            '852' => '&nbsp;&nbsp;&nbsp;&nbsp;Mražené výrobky' ,
            '369' => '&nbsp;&nbsp;&nbsp;&nbsp;Omáčky' ,
            '842' => '&nbsp;&nbsp;&nbsp;&nbsp;Ovoce a zelenina' ,
            '850' => '&nbsp;&nbsp;&nbsp;&nbsp;Pečivo' ,
            '372' => '&nbsp;&nbsp;&nbsp;&nbsp;Sladkosti a čokoláda' ,
            '373' => '&nbsp;&nbsp;&nbsp;&nbsp;Svačinky' ,
            '848' => '&nbsp;&nbsp;&nbsp;&nbsp;Tabák' ,
            '849' => '&nbsp;&nbsp;&nbsp;&nbsp;Vejce' ,
            '374' => '&nbsp;&nbsp;&nbsp;&nbsp;Vitamíny a doplňky' ,
            '12' => 'Sport a turistika' ,
            '645' => '&nbsp;&nbsp;Americký fotbal@' ,
            '646' => '&nbsp;&nbsp;Baseball a Softball@' ,
            '647' => '&nbsp;&nbsp;Basketbal@' ,
            '610' => '&nbsp;&nbsp;Bowling' ,
            '815' => '&nbsp;&nbsp;Cestování' ,
            '816' => '&nbsp;&nbsp;&nbsp;&nbsp;Letenky' ,
            '818' => '&nbsp;&nbsp;&nbsp;&nbsp;Pobyty a zájezdy' ,
            '817' => '&nbsp;&nbsp;&nbsp;&nbsp;Ubytování' ,
            '611' => '&nbsp;&nbsp;Cyklistika' ,
            '628' => '&nbsp;&nbsp;&nbsp;&nbsp;Cyklodoplňky' ,
            '533' => '&nbsp;&nbsp;&nbsp;&nbsp;Cyklooděvy@' ,
            '630' => '&nbsp;&nbsp;&nbsp;&nbsp;Helmy' ,
            '631' => '&nbsp;&nbsp;&nbsp;&nbsp;Kola' ,
            '632' => '&nbsp;&nbsp;&nbsp;&nbsp;Součástky a opravy' ,
            '633' => '&nbsp;&nbsp;&nbsp;&nbsp;Stojany na kola' ,
            '613' => '&nbsp;&nbsp;Fitness' ,
            '468' => '&nbsp;&nbsp;&nbsp;&nbsp;Fitness video@' ,
            '635' => '&nbsp;&nbsp;&nbsp;&nbsp;Fitness vybavení' ,
            '636' => '&nbsp;&nbsp;&nbsp;&nbsp;Oděvy - dámské' ,
            '637' => '&nbsp;&nbsp;&nbsp;&nbsp;Oděvy - pánské' ,
            '638' => '&nbsp;&nbsp;&nbsp;&nbsp;Posilovací stroje' ,
            '639' => '&nbsp;&nbsp;&nbsp;&nbsp;Silové tréninky' ,
            '648' => '&nbsp;&nbsp;Fotbal@' ,
            '615' => '&nbsp;&nbsp;Golf' ,
            '640' => '&nbsp;&nbsp;&nbsp;&nbsp;Batohy' ,
            '641' => '&nbsp;&nbsp;&nbsp;&nbsp;Kluby' ,
            '642' => '&nbsp;&nbsp;&nbsp;&nbsp;Míčky' ,
            '535' => '&nbsp;&nbsp;&nbsp;&nbsp;Oděvy@' ,
            '644' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní vybavení' ,
            '650' => '&nbsp;&nbsp;Hokej@' ,
            '617' => '&nbsp;&nbsp;Kolektivní sporty' ,
            '645' => '&nbsp;&nbsp;&nbsp;&nbsp;Americký fotbal' ,
            '646' => '&nbsp;&nbsp;&nbsp;&nbsp;Baseball a softball' ,
            '647' => '&nbsp;&nbsp;&nbsp;&nbsp;Basketbal' ,
            '813' => '&nbsp;&nbsp;&nbsp;&nbsp;Florbal' ,
            '648' => '&nbsp;&nbsp;&nbsp;&nbsp;Fotbal' ,
            '649' => '&nbsp;&nbsp;&nbsp;&nbsp;Lakros' ,
            '650' => '&nbsp;&nbsp;&nbsp;&nbsp;Lední a in-line hokej' ,
            '651' => '&nbsp;&nbsp;&nbsp;&nbsp;Volejbal' ,
            '618' => '&nbsp;&nbsp;Lezení' ,
            '845' => '&nbsp;&nbsp;Nářadí do tělocvičny' ,
            '619' => '&nbsp;&nbsp;Ostatní sporty' ,
            '814' => '&nbsp;&nbsp;&nbsp;&nbsp;Bojové sporty' ,
            '652' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Box' ,
            '653' => '&nbsp;&nbsp;&nbsp;&nbsp;Jezdectví' ,
            '655' => '&nbsp;&nbsp;&nbsp;&nbsp;Šerm' ,
            '654' => '&nbsp;&nbsp;&nbsp;&nbsp;Vzduchoplavectví' ,
            '620' => '&nbsp;&nbsp;Outdoor a kempování' ,
            '621' => '&nbsp;&nbsp;Paintbal' ,
            '624' => '&nbsp;&nbsp;Raketové sporty, tenis' ,
            '786' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní' ,
            '785' => '&nbsp;&nbsp;&nbsp;&nbsp;Squash a ricochet' ,
            '784' => '&nbsp;&nbsp;&nbsp;&nbsp;Tenis' ,
            '622' => '&nbsp;&nbsp;Rybářství a lovectví' ,
            '536' => '&nbsp;&nbsp;&nbsp;&nbsp;Lovecké oděvy@' ,
            '657' => '&nbsp;&nbsp;&nbsp;&nbsp;Lovecké potřeby' ,
            '658' => '&nbsp;&nbsp;&nbsp;&nbsp;Lukostřelba' ,
            '660' => '&nbsp;&nbsp;&nbsp;&nbsp;Návnady' ,
            '659' => '&nbsp;&nbsp;&nbsp;&nbsp;Nože' ,
            '662' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní rybářské potřeby' ,
            '661' => '&nbsp;&nbsp;&nbsp;&nbsp;Pruty a navijáky' ,
            '664' => '&nbsp;&nbsp;&nbsp;&nbsp;Vábničky' ,
            '663' => '&nbsp;&nbsp;&nbsp;&nbsp;Vlasce' ,
            '623' => '&nbsp;&nbsp;Skating a Skateboarding' ,
            '665' => '&nbsp;&nbsp;&nbsp;&nbsp;Helmy a chrániče' ,
            '666' => '&nbsp;&nbsp;&nbsp;&nbsp;In-line brusle' ,
            '667' => '&nbsp;&nbsp;&nbsp;&nbsp;Skateboardy' ,
            '374' => '&nbsp;&nbsp;Vitamíny a doplňky@' ,
            '625' => '&nbsp;&nbsp;Vodní sporty' ,
            '668' => '&nbsp;&nbsp;&nbsp;&nbsp;Hloubkové potápění' ,
            '102' => '&nbsp;&nbsp;&nbsp;&nbsp;Lodě@' ,
            '670' => '&nbsp;&nbsp;&nbsp;&nbsp;Neopreny' ,
            '671' => '&nbsp;&nbsp;&nbsp;&nbsp;Plavání a potápění' ,
            '672' => '&nbsp;&nbsp;&nbsp;&nbsp;Vodní lyžování' ,
            '673' => '&nbsp;&nbsp;&nbsp;&nbsp;Vybavení na pláž' ,
            '612' => '&nbsp;&nbsp;Zboží pro fanoušky' ,
            '627' => '&nbsp;&nbsp;Zimní sporty' ,
            '674' => '&nbsp;&nbsp;&nbsp;&nbsp;Lyžování' ,
            '675' => '&nbsp;&nbsp;&nbsp;&nbsp;Snowboarding' ,
            '676' => '&nbsp;&nbsp;&nbsp;&nbsp;Snowshoeing' ,
            '14' => 'Zdraví a osobní péče' ,
            '796' => '&nbsp;&nbsp;Erotické pomůcky' ,
            '730' => '&nbsp;&nbsp;Kadeřnické potřeby' ,
            '723' => '&nbsp;&nbsp;Koupelové produkty' ,
            '846' => '&nbsp;&nbsp;Lékařské a laboratorní zařízení' ,
            '725' => '&nbsp;&nbsp;Líčení' ,
            '733' => '&nbsp;&nbsp;&nbsp;&nbsp;Aplikátory a doplňky' ,
            '734' => '&nbsp;&nbsp;&nbsp;&nbsp;Laky na nehty' ,
            '735' => '&nbsp;&nbsp;&nbsp;&nbsp;Obličejová líčidla' ,
            '736' => '&nbsp;&nbsp;&nbsp;&nbsp;Oční stíny' ,
            '787' => '&nbsp;&nbsp;&nbsp;&nbsp;Olejíčky na nehty a jiné' ,
            '737' => '&nbsp;&nbsp;&nbsp;&nbsp;Rtěnky' ,
            '724' => '&nbsp;&nbsp;Masážní pomůcky' ,
            '726' => '&nbsp;&nbsp;Osobní hygiena' ,
            '727' => '&nbsp;&nbsp;Péče o chrup' ,
            '790' => '&nbsp;&nbsp;&nbsp;&nbsp;Kartáčky na zuby' ,
            '789' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní' ,
            '788' => '&nbsp;&nbsp;&nbsp;&nbsp;Zubní pasty' ,
            '728' => '&nbsp;&nbsp;Péče o pokožku' ,
            '738' => '&nbsp;&nbsp;&nbsp;&nbsp;Opalovací přípravky' ,
            '739' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní péče o pokožku' ,
            '740' => '&nbsp;&nbsp;&nbsp;&nbsp;Pleťové vody a krémy' ,
            '729' => '&nbsp;&nbsp;Péče o vlasy' ,
            '792' => '&nbsp;&nbsp;&nbsp;&nbsp;Kondicionéry' ,
            '793' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní' ,
            '791' => '&nbsp;&nbsp;&nbsp;&nbsp;Šampony' ,
            '722' => '&nbsp;&nbsp;Potřeby na holení' ,
            '721' => '&nbsp;&nbsp;Volně prodejné léky' ,
            '731' => '&nbsp;&nbsp;Vůně' ,
            '741' => '&nbsp;&nbsp;&nbsp;&nbsp;Dámské' ,
            '742' => '&nbsp;&nbsp;&nbsp;&nbsp;Pánské' ,
            '732' => '&nbsp;&nbsp;Zdravotnické přípravky' ,
            '744' => '&nbsp;&nbsp;&nbsp;&nbsp;Ortézy a stahovadla' ,
            '745' => '&nbsp;&nbsp;&nbsp;&nbsp;Ostatní medicínské potřeby' ,
            '747' => '&nbsp;&nbsp;&nbsp;&nbsp;Péče o nohy' ,
            '746' => '&nbsp;&nbsp;&nbsp;&nbsp;První pomoc' ,
            '748' => '&nbsp;&nbsp;&nbsp;&nbsp;Speciální potřeby' ,
            '743' => '&nbsp;&nbsp;&nbsp;&nbsp;Tlakoměry a jiné' ,
		);
	}
}
