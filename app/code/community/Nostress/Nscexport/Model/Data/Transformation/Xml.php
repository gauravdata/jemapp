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
* Xml data transformation for export process
* @category Nostress
* @package Nostress_Nscexport
*
*/

class Nostress_Nscexport_Model_Data_Transformation_Xml extends Nostress_Nscexport_Model_Data_Transformation
{
    const GROUP_ID = 'group_id';
    const IS_CHILD = 'is_child';

    const MAIN_TAG = 'items';
    const ITEM_TAG = 'item';
    const BASIC_ATTRIBUTES = 'attributes';
    const MULTI_ATTRIBUTES = 'multi_attributes';
    const CUSTOM_ATTRIBUTES = 'custom_attributes';
    const TRANSLATIONS = 'translate';

    const EMPTY_VALUE = "";

    const DEF_TEXT_SEPARATOR = '"';
    const DEF_SETUP_SEPARATOR = ',';
    const DEF_DECIMAL_DELIMITER = '.';
    const DEF_PATH_IDS_DELIMITER = '/';

    const SUPER_ATTRIBUTES = 'super_attributes';
    const MEDIA_GALLERY = 'media_gallery';
    const CATEGORIES = 'categories';

    const PP_CDATA = 'cdata';
    const PP_ENCODE_SPECIAL = 'encode_special_chars';
    const PP_DECODE_SPECIAL = 'decode_special_chars';
    const PP_REMOVE_EOL = 'remove_eol';
    const PP_STRIP_TAGS = 'strip_tags';
    const PP_DELETE_SPACES = 'delete_spaces';

    const URL_SUFFIX = "#";

    protected $_store;
    protected $_mediaUrl;
	protected $_parent;
	protected $_groupId;
	protected $_row;
	protected $_multiAttributes = array(self::SUPER_ATTRIBUTES=>"attribute",self::MEDIA_GALLERY=>"image",self::CATEGORIES=>"category");
	protected $_itemData = "";
	protected $_categoryTree;
	protected $_multiAttributesMap;
	protected $_customAttributesMap;
	protected $_skippedProductsCounter;
	protected $changeDecimalDelimiter = false;
	protected $_simpleProductCounter = 0;
	protected $_shippingExportEnabled = false;
	protected $_postProcessFunctions = array(	self::PP_CDATA => "Character data",
												self::PP_ENCODE_SPECIAL=>"Encode special characters",
												self::PP_DECODE_SPECIAL=>"Decode special characters",
												self::PP_REMOVE_EOL=>"Remove end of lines",
												self::PP_STRIP_TAGS=>"Strip tags",
												self::PP_DELETE_SPACES=>"Delete spaces"
												);

	/**
	 * Main inicialization funciton
	 * @param array $params
	 */
	public function init($params)
    {
        parent::init($params);
        $this->resetVariables();
        $this->preprocessAttributes();
        $this->initAttributeMaps();
        $this->initDecimalDelimiter();
        $this->initShippingIntervals();
    }

    protected function resetVariables()
    {
    	$this->_itemData = "";
    	$this->_categoryTree = null;
    	$this->_multiAttributesMap = null;
    	$this->_customAttributesMap = null;
    	$this->_skippedProductsCounter = 0;
    	$this->changeDecimalDelimiter = false;
    	$this->resetSimpleProductsCounter();
    	$this->_shippingExportEnabled = false;
    	$this->_store = null;
    	$this->_mediaUrl = null;
    }

	/**
	 * Load attributes from configuration
	 */
	protected function preprocessAttributes()
	{
	    $attributes = $this->getAttributes();
	    $attributes = $this->getArrayField(self::ATTRIBUTE,$attributes,array());
	    $this->setAttributes($attributes);
	    if(empty($attributes) && $this->getCategoryTree() == 0)
	    {
	    	$this->logAndException("2");
	    }
	}

	/**
	 * Inits decimal delimiter value
	 */
    protected function initDecimalDelimiter()
    {
        $delimiter = $this->getDecimalDelimiter();
        if($delimiter != self::DEF_DECIMAL_DELIMITER)
            $this->changeDecimalDelimiter = true;
    }

    /**
     * Init attribute mapping setup
     */
    protected function initAttributeMaps()
    {
        $map = $this->getAttributes();
        //Format price
		$map = $this->preparePriceFields($map);

		$this->_multiAttributesMap = array();
		foreach ($map as $key => $attribute)
		{
			//add shipping cost into feed
			$this->initShippingExport($attribute);

		   	//add static attributes
		   	$attribute = $this->initStaticAttributes($attribute);

		   	//prepare postprocess functions
		   	$attribute = $this->initPostprocessActions($attribute);

		    //check limit
		    $attribute = $this->initLimit($attribute);

		    //prepare translations
		    $attribute = $this->initTranslation($attribute);

			//Multiselect attribute process
		    $attribute = $this->initMultiselectAttributes($attribute);

		    //Init prefix and suffix attributes
		    $attribute = $this->initPrefixAttributes($attribute);
		    $attribute = $this->initSuffixAttributes($attribute);

		    //Choose custom attributes
		    $attribute = $this->initCustomAttributes($attribute);

		    //Remove multi attributes from attribute map
		    if($attribute)
		    	$attribute = $this->initMultiAttributes($attribute);
		    if($attribute === false)
		    {
		    	unset($map[$key]);
		    	continue;
		    }
		    $map[$key] = $attribute;
		}
		$this->setAttributes($map);
    }

    public function insertCategories($categories)
    {
    	$this->_categoryTree = array();
    	foreach($categories as $category)
    		$this->insertCategoryInTree($category);

    	$categoriesXmlString = $this->treeToXml($this->_categoryTree,self::CATEGORIES);
    	$categoriesXmlString = $this->getElement(self::CATEGORIES,$categoriesXmlString);
	    $this->appendResult($categoriesXmlString);
    }

	/**
     * Main transformation function
     * Creates target XML file
     * @param $data
     */
	public function transform($data)
	{
		parent::transform($data);
		$saveItemData = false;

		foreach($data as $row)
		{
		    $this->setRow($row);
		    $isChild = $this->getValue(self::IS_CHILD);
			if(!$isChild || $this->getChildsOnly())
		    {
                $saveItemData = true;
		    }

		    if($this->getChildsOnly() && !$isChild && !$this->isSimpleProduct())
		    {
		    	$this->_skippedProductsCounter++;
		        continue;
		    }

		    if($saveItemData)
		    {
		        $saveItemData = false;
                $this->saveItemData();
		    }

		    $label = $this->getIsChildLabel($isChild);
            $this->addItemData($this->getTag($label));

		    $this->processBasicAttributes($isChild);
		    $this->processCustomAttributes($isChild);
		    $this->processMultiAttributes($isChild);
		    $this->addItemData($this->getTag($label,true));
		}
	}

	/**
	 * Set row with product data for transformation
	 * @param unknown_type $row
	 */
	protected function setRow($row)
	{
		$row = $this->preProcessRow($row);
	    if(array_key_exists(self::GROUP_ID,$row) && $this->setGroupId($row[self::GROUP_ID]))
	    {
	        $this->setParent($row);
	        $this->resetSimpleProductsCounter();
	    }
	    else
	    {
	    	$this->incrementSimpleProductsCounter();
	    }
	    $this->_row = $row;
	}

	/**
	 * Returns parent-child label
	 * @param unknown_type $isChild
	 */
	protected function getIsChildLabel($isChild)
   	{
   	    $label = self::PARENT;
   	    if($this->getChildsOnly())
   	    	return $label;
		if($isChild)
		    $label = self::CHILD;
        return $label;
   	}

	/**
	 * Process basic attributes
	 * @param bool $isChild Is product child.
	 */
	protected function processBasicAttributes($isChild)
	{
		$map = $this->getAttributes();
		$this->addItemData($this->getTag(self::BASIC_ATTRIBUTES));
	    foreach ($map as &$attributeInfo)
	    {
	    	$value = $this->getAttributeValue($attributeInfo,$isChild);
	    	if(!$this->isValueEmpty($value))
	    	    $this->addItemData($this->getElement($attributeInfo[self::CODE],$value));
	    }
	    $this->addItemData($this->getTag(self::BASIC_ATTRIBUTES,true));
	}

	/**
	 * Process custom attributes
	 * @param bool $isChild Is product child.
	 */
	protected function processCustomAttributes($isChild)
	{
	    if(!isset($this->_customAttributesMap) || empty($this->_customAttributesMap))
	        return;

	    $fileType = $this->getFileType();
		$this->addItemData($this->getTag(self::CUSTOM_ATTRIBUTES));
		foreach ($this->_customAttributesMap as &$attributeInfo)
	    {
	    	$value = $this->getAttributeValue($attributeInfo,$isChild);
	    	if($this->isValueEmpty($value) && $fileType == self::XML)
	    		continue;

	    	$this->addItemData($this->getTag(self::ATTRIBUTE));
	    	$this->addItemData($this->getElement(self::VALUE,$value));
	    	$this->addItemData($this->getElement(self::TAG,$attributeInfo[self::TAG]));
	    	$this->addItemData($this->getElement(self::LABEL,$attributeInfo[self::LABEL]));
	    	$this->addItemData($this->getTag(self::ATTRIBUTE,true));
	    }
	    $this->addItemData($this->getTag(self::CUSTOM_ATTRIBUTES,true));
	}

	/**
	 * Process super attributes
	 * @param bool $isChild Is product child.
	 */
	protected function processSuperAttributes($attributes)
	{
	    if(!$this->getValue(self::IS_CHILD))
	        return array();
	    foreach ($attributes as $key => $attribute)
	    {
	    	$code = $this->getArrayValue(self::CODE,$attribute);
	    	$value = $this->getValue($code);
	    	unset($attributes[$key][self::CODE]);
	    	$attributes[$key][self::VALUE] = $value;
	    }
	    return $attributes;
	}

	/**
	 * Process multi attributes
	 * @param bool $isChild Is product child.
	 */
	protected function processMultiAttributes($isChild)
	{
		if(!isset($this->_multiAttributes) || empty($this->_multiAttributes))
			return;
		$this->addItemData($this->getTag(self::MULTI_ATTRIBUTES));
	    foreach (array_keys($this->_multiAttributes) as $multiAttribute)
	    {
	    	$loadParentValue = false;
	    	if(isset($this->_multiAttributesMap[$multiAttribute][self::PARENT_ATTRIBUTE_VALUE]))
	        	$loadParentValue  = $this->evaluateParentAttributeCondition($isChild,$this->_multiAttributesMap[$multiAttribute][self::PARENT_ATTRIBUTE_VALUE]);
	        if($multiAttribute == self::SUPER_ATTRIBUTES)
	            $loadParentValue = '1';
	    	$multiAttribValue = $this->getValue($multiAttribute,$loadParentValue);
	    	if(!isset($multiAttribute))
	    	    continue;

	    	if(!is_array($multiAttribValue))
	    	{
	    	    $columns = $this->getMultiAttributeColumns($multiAttribute);
	    	    if(!isset($columns))
	    	        return;
	    	    $multiAttribValue = $this->parseAttribute($multiAttribValue,$columns);

	    	    if($multiAttribute == self::MEDIA_GALLERY)
	    	    	$multiAttribValue = $this->addMediaUrl($multiAttribValue);

                $this->setParentAttribute($multiAttribute,$multiAttribValue);
	    	}

	    	if($multiAttribute == self::SUPER_ATTRIBUTES)
	    	    $multiAttribValue = $this->processSuperAttributes($multiAttribValue);

	    	$string = $this->arrayToXml($multiAttribValue,$multiAttribute);
	    	$this->addItemData($string);
	    }
	    $this->addItemData($this->getTag(self::MULTI_ATTRIBUTES,true));
	}

	protected function addMediaUrl($mediaGallery)
	{
		$mediaUrl = $this->getMediaUrl();
		foreach ($mediaGallery as $key => $item)
		{
			$mediaGallery[$key][self::VALUE] = $mediaUrl.$item[self::VALUE];
		}
		return $mediaGallery;
	}

	/**
	 * Method loads and prepares attribute value.
	 * Attribute value is concatenated with prefix and suffix.
	 * @param array $setup Attribute setup.
	 * @param bool $isChild Is product child.
	 */
	protected function getAttributeValue(&$setup,$isChild)
	{
        $magentoAttribute = $setup[self::MAGENTO_ATTRIBUTE];

        $eppav = "0";
        if(isset($setup[self::PARENT_ATTRIBUTE_VALUE]))
            $eppav = $setup[self::PARENT_ATTRIBUTE_VALUE];
        $parentCondition = $this->evaluateParentAttributeCondition($isChild,$eppav);

        if($this->_shippingExportEnabled && $magentoAttribute == self::SHIPPING_COST)
        {
        	$value = $this->getShippingCostValue($parentCondition);
        }
        else
        	$value = $this->getValue($magentoAttribute,$parentCondition);

	    if(isset($setup[self::MULTISELECT_OPTIONS]))
        {
        	$optionText = $this->getOptionText($value,$setup[self::MULTISELECT_OPTIONS]);
        	$setup[self::MULTISELECT_OPTIONS][$value] = $optionText;
        	$value = $optionText;
        }

    	if($this->isValueEmpty($value) && isset($setup[self::CONSTANT]))
    	    $value = $setup[self::CONSTANT];

    	//prepare prefix and suffix
    	$prefix = "";
    	if(isset($setup[self::PREFIX]))
    	    $prefix = $setup[self::PREFIX];
    	if(isset($setup[self::PREFIX_VARS]))
    		$prefix = $this->replaceVarsWithValues($prefix,$setup[self::PREFIX_VARS],$parentCondition);

    	$suffix = "";
    	if(isset($setup[self::SUFFIX]))
    	    $suffix = $setup[self::SUFFIX];
    	if(isset($setup[self::SUFFIX_VARS]))
    		$suffix = $this->replaceVarsWithValues($suffix,$setup[self::SUFFIX_VARS],$parentCondition);

    	$value = $prefix.$value.$suffix;

    	if(!empty($setup[self::TRANSLATIONS]))
    	{
    		if(isset($setup[self::TRANSLATIONS][$value]))
    		{
    			$value = $setup[self::TRANSLATIONS][$value];
    		}
    		else // Regular expression usage by func0der <www.func0der.de>
    		{
    			foreach($setup[self::TRANSLATIONS] as $from => $to)
    			{
    				if(preg_match($from, $value) === 1)
    				{
    					$value = preg_replace($from, $to, $value);
    					break;
    				}
    			}
    		}
    	}

    	//prepocess value
    	$postProcessFunctions = isset($setup[self::POST_PROCESS])?$setup[self::POST_PROCESS]:array();
    	$limit = isset($setup[self::LIMIT])?$setup[self::LIMIT]:null;
    	$value = $this->postProcess($value,$postProcessFunctions,$limit);

    	return $value;
	}

	protected function replaceVarsWithValues($string,$vars,$parent)
	{
		foreach($vars as $key => &$data)
		{
			$data = $this->getValue($data,$parent);
		}
		return str_replace(array_keys($vars),array_values($vars),$string);
	}

	protected function getOptionText($valueIds,$options)
	{
		if(!isset($valueIds) || $valueIds === "")
			return "";

		$text = "";
		if(isset($options[$valueIds]))
			$text = $options[$valueIds];

		$valueIdsArray = explode(',', $valueIds);

		$values = array();
        foreach ($valueIdsArray as $id)
        {
          if (isset($options[$id]))
          	$values[] = $options[$id];
        }

        $values = implode(",",$values);
		return $values;
	}



	protected function getValue($index,$parent = '0',$prepareValue = true)
	{
	    switch($parent)
	    {
	        case '0':
	            $value = $this->getArrayValue($index,$this->_row);
	            break;
	        case '1':
	            $value = $this->getParentValue($index);
	            break;
	        default:
	            $value = $this->getArrayValue($index,$this->_row);
	            if(empty($value))
	                $value = $this->getParentValue($index);
	            break;
	    }
	    if($prepareValue)
	    	$value = $this->prepareValue($value,$index,$parent);
	    return $value;
	}

	/**
	 * Returns parent product value
	 * @param unknown_type $index
	 */
	protected function getParentValue($index)
	{
	    return $this->getArrayValue($index,$this->_parent);
	}

	/**
	 * Prepares attribute value
	 * @param string $value Attribute value.
	 * @param string $index Attribute index.
	 * @param bool $parent Is parent value.
	 */
	protected function prepareValue($value,$index,$parent)
	{
		if($this->changeDecimalDelimiter && is_numeric($value))
			$value = str_replace(self::DEF_DECIMAL_DELIMITER,$this->getDecimalDelimiter(),$value);

		if($parent == '1' && $index == self::URL)
		{
			$value .= self::URL_SUFFIX.$this->getSimpleProductsCounterValue();
		}
		return $value;
	}


   	/**
   	 * Post processing actions with attribute value
   	 */
   	protected function postProcess($value,$setup = null,$limit  = null)
   	{
   	    if($this->isValueEmpty($value))
   	        return $value;


   	    if(empty($setup) || !is_array($setup))
   	    {
   	        $setup = array();
   	    }


   	    foreach ($setup as $item)
   	    {
   	        switch($item)
   	        {
   	            case self::PP_ENCODE_SPECIAL:
   	                $value = $this->ppEncodeSpecial($value);
   	                break;
   	            case self::PP_DECODE_SPECIAL:
   	                $value = $this->ppDecodeSpecial($value);
   	                break;
   	            case self::PP_STRIP_TAGS:
   	                $value = $this->ppStripTags($value);
   	                break;
   	            case self::PP_DELETE_SPACES:
   	                $value = $this->ppDeleteSpaces($value);
   	                break;
   	            case self::PP_REMOVE_EOL:
   	                $value = $this->ppRemoveEol($value);
   	                break;
   	        }
   	    }
   	    $value = $this->ppFile($value);
   	    $value = $this->ppDefault($value,$limit);
   	    return $value;
   	}

   	/**
   	 * Returns multi attribute colum names
   	 * @param string $attributeName Multi attribute name.
   	 */
	protected function getMultiAttributeColumns($attributeName)
	{
	    $resourceModelName = "";
	    switch ($attributeName)
	    {
	        case self::SUPER_ATTRIBUTES:
	            $resourceModelName = 'nscexport/cache_superattributes';
	            break;
	        case self::CATEGORIES:
	            $resourceModelName = 'nscexport/cache_categories';
	            break;
	        case self::MEDIA_GALLERY:
	            $resourceModelName = 'nscexport/cache_mediagallery';
	            break;
	        default:
	            return null;
	    }

	    $resourceModel = Mage::getResourceModel($resourceModelName);
	    $columns = array_keys($resourceModel->getCacheColumns());
	    return $columns;
	}

	/**
	 * Parse multi attribute.
	 * @param string $attributeValue
	 * @param array $columns Column names as array.
	 * @return Separate attributes.
	 */
	protected function parseAttribute($attributeValue,$columns)
	{
	    $itemSeparator = Nostress_Nscexport_Helper_Data_Loader::GROUP_ROW_ITEM_SEPARATOR;
	    $rowSeparator = Nostress_Nscexport_Helper_Data_Loader::GROUP_ROW_SEPARATOR;

	    $rows = explode($rowSeparator,$attributeValue);
	    $result = array();
	    foreach ($rows as $key => $row)
	    {
	        $values = explode($itemSeparator,$row);
	        if(count($columns) == count($values))
	            $result[$key] = array_combine($columns,$values);
	    }
	    return $result;
	}

	protected function setParentAttribute($index,$value)
	{
	    if(!$this->getValue(self::IS_CHILD) && isset($this->_parent))
	        $this->_parent[$index] = $value;
	}

	protected function getShippingConfig($index,$default = "")
	{
		$shipping = $this->getShipping();
		if(!empty($shipping[$index]))
			return $shipping[$index];
		else
			return $default;
	}

	protected function getShippingCostValue($parentCondition)
	{
		$dependentAttribute = $this->getShippingConfig(self::DEPENDENT_ATTRIBUTE);
		$intervals = $this->getShippingConfig(self::COST_SETUP);
		$dependentAttributeValue = $this->getValue($dependentAttribute,$parentCondition,false);
		if(empty($dependentAttribute) || empty($intervals) || empty($dependentAttributeValue))
			return "";

		foreach($intervals as $interval)
		{
			if($dependentAttributeValue >= $interval[self::PRICE_FROM] && $dependentAttributeValue < $interval[self::PRICE_TO])
				return $interval[self::COST];
		}
		return "";
	}

	/*********************************************** INIT ATTRIBUTE MAPs -- FUNCTIONS -- START *********************************************/
    protected function initShippingExport($attribute)
    {
    	if(!empty($attribute[self::MAGENTO_ATTRIBUTE]) && $attribute[self::MAGENTO_ATTRIBUTE] == self::SHIPPING_COST)
    		$this->_shippingExportEnabled = true;
    }

    protected function initShippingIntervals()
    {
    	$intervals = $this->getShippingConfig(self::COST_SETUP);
    	if(empty($intervals))
    		return;
    	foreach ($intervals as $index => $interval)
    	{
    		if(empty($interval[self::PRICE_FROM]) && empty($interval[self::PRICE_TO]))
    		{
    			unset($intervals[$index]);
    			continue;
    		}
    		else if(empty($interval[self::PRICE_FROM]))
    			$interval[self::PRICE_FROM] = self::SHIPPING_INTERVAL_MIN;
    		else if(empty($interval[self::PRICE_TO]))
    			$interval[self::PRICE_TO] = self::SHIPPING_INTERVAL_MAX;

    		$intervals[$index][self::PRICE_TO] = str_replace(",",self::DEF_DECIMAL_DELIMITER,$interval[self::PRICE_TO]);
    		$intervals[$index][self::PRICE_FROM] = str_replace(",",self::DEF_DECIMAL_DELIMITER,$interval[self::PRICE_FROM]);
    		$intervals[$index][self::COST] = str_replace(self::DEF_DECIMAL_DELIMITER,$this->getDecimalDelimiter(),$interval[self::COST]);
    	}

    	$shipping = $this->getShipping();
    	$shipping[self::COST_SETUP] = $intervals;
    	$this->setShipping($shipping);
    }


	protected function initStaticAttributes($attribute)
    {
    	$resetMagentoAttribute = true;
    	switch($attribute[self::MAGENTO_ATTRIBUTE])
    	{
    		//Add currency into feed
    		case self::CURRENCY:
    			$attribute[self::CONSTANT] .= $this->helper()->getStoreCurrency($this->getStoreId());
    			break;
    		case self::COUNTRY_CODE:
    			$attribute[self::CONSTANT] .= $this->helper()->getStoreCountry($this->getStore());
    			break;
    		case self::LOCALE:
    			$attribute[self::CONSTANT] .= $this->helper()->getStoreLocale($this->getStore());
    			break;
    		case self::LANGUAGE:
    			$attribute[self::CONSTANT] .= $this->helper()->getStoreLanguage($this->getStore());
    			break;
    		case self::SHIPPING_METHOD_NAME:
    			$attribute[self::CONSTANT] .= $this->getShippingConfig(self::METHOD_NAME);
    			$resetMagentoAttribute = false;
    			break;
    		default:
    			$resetMagentoAttribute = false;
    			break;
    	}
    	if($resetMagentoAttribute)
    		$attribute[self::MAGENTO_ATTRIBUTE] = "";
    	return $attribute;
    }

	protected function initPostprocessActions($attribute)
    {
     	if(isset($attribute[self::POST_PROCESS]))
	    {
	    	$postprocessFuncitons = $attribute[self::POST_PROCESS];
	    	if(empty($postprocessFuncitons))
	    		$postprocessFuncitons = array();
	    	else
	    	{
	    		if(strpos($postprocessFuncitons,self::POSTPROC_DELIMITER) === false)
	    			$postprocessFuncitons = array($postprocessFuncitons);
	    		else
	    			$postprocessFuncitons = explode(self::POSTPROC_DELIMITER,$postprocessFuncitons);
	    	}
	    	$attribute[self::POST_PROCESS] = $postprocessFuncitons;
	    }
	    return $attribute;
    }

    protected function initLimit($attribute)
    {
    	if(isset($attribute[self::LIMIT]))
	    {
	    	$limit = $attribute[self::LIMIT];
	    	if(empty($limit) || !is_numeric($limit) || $limit < 0)
	    		unset($attribute[self::LIMIT]);
	    }
	    return $attribute;
    }

    protected function initTranslation($attribute)
    {
    	if(isset($attribute[self::TRANSLATIONS]))
	    {
	    	$attribute[self::TRANSLATIONS] = $this->helper()->optionsToSearchArray($attribute[self::TRANSLATIONS],"from","to");
	    }

	    return $attribute;
    }

    protected function initMultiselectAttributes($attribute)
    {
     	$productAttribute = $this->helper()->getProductAttribute($attribute[self::MAGENTO_ATTRIBUTE]);
	    if($this->helper()->attributeIsMultiselect($productAttribute))
	    {
	    	$options = $productAttribute->getFrontend()->getSelectOptions();
	    	$options = $this->helper()->optionsToSearchArray($options);
	    	$attribute[self::MULTISELECT_OPTIONS] = $options;
	    }
	    return $attribute;
    }

    protected function initPrefixAttributes($attribute)
    {
    	return $this->initContextAttributes($attribute,self::PREFIX,self::PREFIX_VARS);
    }

    protected function initSuffixAttributes($attribute)
    {
    	return $this->initContextAttributes($attribute,self::SUFFIX,self::SUFFIX_VARS);
    }

    protected function initContextAttributes($attribute,$type,$varType)
    {
    	if(!isset($attribute[$type]))
    		return $attribute;
    	$vars = $this->helper()->grebVariables($attribute[$type],true,true);
    	if(!empty($vars))
    		$attribute[$varType] = $vars;
    	return $attribute;
    }

    protected function initCustomAttributes($attribute)
    {
   		if($attribute[self::CODE]== self::CUSTOM_ATTRIBUTE)
	    {
	    	$productAttribute = $this->helper()->getProductAttribute($attribute[self::MAGENTO_ATTRIBUTE]);
	    	$label = $this->helper()->getAttributeLabel($productAttribute,$this->getStoreId());

	    	if(empty($attribute[self::LABEL]) && !empty($label))
	    	{
	    		$attribute[self::LABEL] = $label;
	    		$attribute[self::TAG] = $label;
	    	}
	    	else
	    	{
	    		$attribute[self::TAG] = $attribute[self::LABEL];
	    	}

	    	$attribute[self::TAG] = $this->helper()->createCode($attribute[self::TAG],"_",false,":");

	    	$this->_customAttributesMap[] = $attribute;
	    	return false;
	    }
	    return $attribute;
    }

    protected function initMultiAttributes($attribute)
    {
    	if(array_key_exists($attribute[self::MAGENTO_ATTRIBUTE],$this->_multiAttributes))
	    {
	    	$this->_multiAttributesMap[$attribute[self::MAGENTO_ATTRIBUTE]] = $attribute;
	    	return false;
	    }
	    return $attribute;
    }

	protected function preparePriceFields($map)
	{
	    $priceFormat = $this->getPriceFormat();

	    $currency = $this->helper()->getStoreCurrency($this->getStoreId());
	    $symbol = $this->helper()->getStoreCurrency($this->getStoreId(),true);

	    foreach ($map as $key => $attributesInfo)
	    {
	        if((isset($attributesInfo[self::MAGENTO_ATTRIBUTE_TYPE]) && $attributesInfo[self::MAGENTO_ATTRIBUTE_TYPE] == "price")
	        		|| strpos($attributesInfo[self::MAGENTO_ATTRIBUTE],"price") !== false)
	        {
	            switch($priceFormat)
	            {
	                case Nostress_Nscexport_Model_Config_Source_Priceformat::CURRENCY_SUFFIX:
	                    $attributesInfo[self::SUFFIX] = " ".$currency.$attributesInfo[self::SUFFIX];
	                    break;
	                case Nostress_Nscexport_Model_Config_Source_Priceformat::CURRENCY_PREFIX:
	                    $attributesInfo[self::PREFIX] .= $currency." ";
	                    break;
	                case Nostress_Nscexport_Model_Config_Source_Priceformat::SYMBOL_SUFFIX:
	                    $attributesInfo[self::SUFFIX] = " ".$symbol.$attributesInfo[self::SUFFIX];
	                    break;
	                case Nostress_Nscexport_Model_Config_Source_Priceformat::SYMBOL_PREFIX:
	                    $attributesInfo[self::PREFIX] .= $symbol." ";
	                    break;
	                default:
	                    break;
	            }
	            $map[$key] = $attributesInfo;
	        }
	    }
	    return $map;
	}

    /*********************************************** INIT ATTRIBUTE MAPS FUNCTIONS -- END *********************************************/

	//******************************** POST PROCESS ACTIONS - START******************************///
   	protected function ppEncodeSpecial($value)
   	{
   	    return htmlspecialchars($value);
   	}

   	protected function ppDecodeSpecial($value)
   	{
   	    return htmlspecialchars_decode($value);
   	}

   	protected function ppStripTags($value)
   	{
   	    return strip_tags($value);
   	}

   	protected  function ppDeleteSpaces($string)
   	{
   	    return preg_replace("/\s+/", '', $string);
   	}

   	protected function ppRemoveEol($string)
   	{
   	    return str_replace(array("\r\n", "\r", "\n"), ' ', $string);
   	}

   	protected function ppFile($value)
   	{
   		switch($this->getFileType())
   		{
   			case self::CSV:
   			case self::TXT:
   				$value = $this->ppCsv($value);
   				break;
   			//case self::XML:
   			default:
   				break;
   		}
   	    return $value;
   	}

   	protected function ppCsv($value)
   	{
   	    $value = str_replace(self::DEF_TEXT_SEPARATOR,"&quot;",$value);
   	    return $value;
   	}

   	protected function ppDefault($value,$limit)
   	{
   		//$value = utf8_encode($value);
   		$value = $this->helper()->removeIllegalChars($value);
   	    $value = $this->helper()->changeEncoding($this->getEncoding(),$value);
   	    $value = $this->ppLimit($value,$limit,$this->getEncoding());
   	    $value = $this->getCdataString($value);
   	    return $value;
   	}

   	protected function ppLimit($value,$limit,$encoding)
   	{
   	    if(isset($limit))
   	    {
   	        $value =  mb_substr($value,0,$limit,$encoding);
   	    }
   	    return $value;
   	}
   	//******************************** POST PROCESS ACTIONS - END******************************///

	//***************************** PARENT METHODS OVERWRITE  **************//
	public function getResult($allData = false)
	{
	    if($allData)
	        $this->saveItemData();
	    $result = parent::getResult();
	    if(!empty($result))
	        $result = $this->getHeader().$result.$this->getTail();
	    return $result;
	}

	protected function check($data)
	{
		if(!parent::checkSrc($data) || !is_array($data))
		{
			$message = $this->logAndException("3");
		}
		return true;
	}

   	protected function addItemData($string)
   	{
   	    $this->_itemData .= $string;
   	}

   	protected function saveItemData()
   	{
   	    if(empty($this->_itemData))
   	        return;
   	    $element = $this->getElement(self::ITEM_TAG,$this->_itemData);
   	    $this->appendResult($element);
   	    $this->_itemData = "";
   	}

	//***************************** PARENT METHODS OVERWRITE -- END **************//

	///////////////////////////////////COMMON FUNCTIONS/////////////////////////////////
	protected function getStore()
	{
		if(!isset($this->_store))
			$this->_store = Mage::app()->getStore($this->getStoreId);
		return $this->_store;
	}

	protected function getMediaUrl()
	{
		if(!isset($this->_mediaUrl))
		{
			$folder = $this->helper()->getGeneralConfig(Nostress_Nscexport_Helper_Data::PARAM_IMAGE_FOLDER);
			$this->_mediaUrl = $this->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$folder;
		}
		return $this->_mediaUrl;

	}

	public function getSkippedProductsCounter()
	{
		return $this->_skippedProductsCounter;
	}

	protected function resetSimpleProductsCounter()
	{
		$this->_simpleProductCounter = 0;
	}

	protected function incrementSimpleProductsCounter()
	{
		$this->_simpleProductCounter++;
	}

	protected function getSimpleProductsCounterValue()
	{
		return $this->_simpleProductCounter;
	}

	protected function getCdataString($input)
   	{
   		return $this->helper()->getCdataString($input);
   	}

	protected function setGroupId($groupId)
	{
	    if($groupId == $this->_groupId)
	        return false;
	    else
	    {
	        $this->_groupId = $groupId;
	        return true;
	    }
	}

	protected function setParent($row)
	{
	    $this->_parent = $row;
	}

	protected function getHeader()
	{
	    return "<?xml version=\"1.0\" encoding=\"{$this->getEncoding()}\"?><".self::MAIN_TAG.">";
	}

	protected function getTail()
	{
	    return "</".self::MAIN_TAG.">";
	}

	protected function getElement($name,$value)
	{
	    return "<{$name}>{$value}</{$name}>";
	}

    protected function getTag($name,$end = false)
   	{
   		if($end)
   			return "</{$name}>";
   		else
   		{
   			return "<{$name}>";
   		}
   	}

	protected function preProcessRow($row)
	{
	    if(array_key_exists("stock_status",$row))
	    {
	       $stockStatus = $row["stock_status"];
	       $attribute = '';
	       $stockStatus = $this->getStockStatusValue($stockStatus,$attribute);
	       if(!empty($attribute))
	           $stockStatus = $row[$attribute];
	       $row["stock_status"] = $stockStatus;

	    }
	    return $row;
	}

	protected function getStockStatusValue($status,&$attribute)
	{
	    $stock = $this->getStock();
	    if($status)
	        $status = $stock["yes"];
	    else
	    {
	        if(empty($stock["availability"]))
	             $status = $stock["no"];
	        else
	           $attribute =  $stock["availability"];
	    }
	    return $status;
	}

	protected function getArrayValue($index,$array)
	{
	    if(array_key_exists($index,$array))
	        return $array[$index];
	    else
	    {
	        //$this->helper()->log($this->helper()->__("Missing input data column %s",$index));
	        return self::EMPTY_VALUE;
	    }
	}

	protected function isValueEmpty($value)
	{
		if(empty($value) && $value != "0")
			return true;
		else
			return false;
	}

	protected function isSimpleProduct()
	{
	    return $this->getValue(self::TYPE) == "simple";
	}

	public function getChildsOnly()
	{
	    $parentChilds = $this->getParentsChilds();
	    if($parentChilds == Nostress_Nscexport_Model_Config_Source_Parentschilds::CHILDS_ONLY)
	        return true;
	    return false;
	}

	public function getPostProcessFunctions()
	{
		return $this->_postProcessFunctions;
	}

	protected function arrayToXml($input,$multiAttribute)
	{
	    $result = "";
	    foreach ($input as $row)
	    {
	        $rowText = "";
	        foreach($row as $index => $value)
	        {
	            if($this->isValueEmpty($value))
	                continue;
	            $value = $this->postProcess($value);
	            $rowText .= $this->getElement($index,$value);
	        }

	        if(!empty($rowText))
	            $result .= $this->getElement($this->_multiAttributes[$multiAttribute],$rowText);
/*	    	$xml = new SimpleXMLElement("<{$this->_multiAttributes[$multiAttribute]}/>");
            array_walk_recursive($row, array ($xml, 'addChild'));
            $result .= $xml->asXML();	*/
	    }
	    if(!empty($result))
	        $result = $this->getElement($multiAttribute,$result);
	    return $result;
	}

	protected function treeToXml($input,$multiAttribute)
	{
		$result = "";
		foreach ($input as $row)
		{
			$rowText = "";
			foreach($row as $index => $value)
			{
				if($this->isValueEmpty($value))
					continue;
				if($index == self::CHILDREN)
				{
					$value = $this->treeToXml($value,$multiAttribute);
				}
				else
					$value = $this->postProcess($value);

				$rowText .= $this->getElement($index,$value);
			}

			if(!empty($rowText))
				$result .= $this->getElement($this->_multiAttributes[$multiAttribute],$rowText);
		}
		return $result;
	}

	/////////////////////CATEGORY PROCESS FUNCTIONS//////////////////////////////////

	protected function insertCategoryInTree($category)
	{
		$tmpTree = &$this->_categoryTree;
		$level = $this->getArrayField(self::LEVEL, $category,'0');
		$categoryId = $this->getArrayField(self::ID, $category,'-1');
		if($level <= 0)
		{
			$tmpTree[$categoryId] = $category;
			return;
		}
		$pathIds = $this->getCategoryPathIds($category,$level);

		$canInsert = false;
		$categoryInserted = false;
		foreach($pathIds as $id)
		{
			if(isset($tmpTree[$id]))
			{
				if(!isset($tmpTree[$id][self::CHILDREN]))
					$tmpTree[$id][self::CHILDREN] = array();
				$tmpTree = &$tmpTree[$id][self::CHILDREN];
				$canInsert = true;
			}
			else if($canInsert)
			{
				$tmpTree[$categoryId] = $category;
				$categoryInserted = true;
			}
		}

		if(!$categoryInserted)
		{
			$tmpTree = &$this->_categoryTree;
			$tmpTree[$categoryId] = $category;
		}

		return;
	}

	protected function getCategoryPathIds($category,$level)
	{
		$idsPath = $this->getArrayField(self::PATH_IDS, $category,null);
		if(!isset($idsPath))
		{
			$tmpTree = $category;
		}
		if(isset($idsPath))
		{

			if(strpos($idsPath,self::DEF_PATH_IDS_DELIMITER) !== false)
			{
				$idsPath = explode(self::DEF_PATH_IDS_DELIMITER, $idsPath);
			}
			else
				$idsPath = array($idsPath);
		}
		return $idsPath;
	}

	protected function evaluateParentAttributeCondition($isChild,$parentAttributeCondition)
	{
	    $result =  $parentAttributeCondition;
	    if(!$isChild)
	      $result = '0';
	    return $result;
	}
}
?>