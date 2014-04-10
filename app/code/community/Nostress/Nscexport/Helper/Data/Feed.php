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
* @copyright Copyright (c) 2012 NoStress Commerce (http://www.nostresscommerce.cz)
*
*/

/**
* Helper.
*
* @category Nostress
* @package Nostress_Nscexport
*
*/

class Nostress_Nscexport_Helper_Data_Feed extends Nostress_Nscexport_Helper_Data
{
	const INTERNATIONAL_CODE = 'INTERNATIONAL';
	const INTERNATIONAL_LABEL = 'International';
	const LABEL = 'label';
	const VALUE = 'value';
	const RED = 'red';
	const FRONTEND_INPUT = 'frontend_input';
	const KEYWORD_TAXONOMY = "Taxonomy";

	public function getAdapter($feedCode,$storeId) {
		$feed = Mage::getModel('nscexport/feed')->getFeedByCode($feedCode);
		if (!isset($feed) || !$feed->hasFileType())
			return null;

		$fileType = $feed->getFileType();
		$adapter = Mage::getModel('nscexport/engine_abstract_'.$fileType)->InitData($feed->getData(),$storeId);
		return $adapter;
	}

	public function getFeedOptions($enabled = null, $addFileType = null, $isMultiselect = null) {
		return Mage::getSingleton('nscexport/feed')->toOptionArray($enabled, $addFileType, $isMultiselect);
	}

	public function someFeedsDisabled()
	{
		$feedOptions =  $this->getOptions("link",false,false);
		if(count($feedOptions) > 0)
			return true;
		else
			return false;
	}
	public function getOptions($type,$gridOptions = false,$enabled = null,$addCounty = false)
	{
		$dataArray = array();
		$feedLink = "";
		$feeds = Mage::getModel('nscexport/feed')->getCollection();

		if(isset($enabled))
			$feeds->addFieldToFilter('enabled', $enabled);
		$feeds->load();

		foreach ($feeds as $feed)
		{
			$data = $feed->getData($type);
			if(!in_array($data, $dataArray))
			{
				$dataArray[$data] = array("label" => $data, "value" => $data);
				if($addCounty)
					$dataArray[$data]["country"] = $feed->getCountry();
			}
		}

		if($gridOptions)
			$dataArray = $this-> optionsArrayToGrid($dataArray);
		ksort($dataArray);
		return $dataArray;
	}

	public function getTypeOptions($feed = null) {
		$feedLinks = null;
		$typeArray = array();
		$helperArray = array();
		$feedLink = "";
		$feedLinks = Mage::getModel('nscexport/feed')->getCollection()
			->addFieldToFilter('code', $feed)
			->addFieldToFilter('enabled', true)
			->load();
		foreach ($feedLinks as $feedLink) {
			$feedLink = $feedLink->getLink();
		}
		$types = Mage::getModel('nscexport/feed')->getCollection()
			->addFieldToFilter('link', $feedLink)
			->addFieldToFilter('enabled', true)
			->load();

		foreach ($types as $type) {
			if (!in_array($type->type, $helperArray)) {
				$typeArray[] = array("label" => $type->type, "value" => $type->code);
				$helperArray[] = $type->type;
			}
		}

		return $typeArray;
	}

	public function getFileOptions($type =  null, $feed = null) {
		$fileArray = array();
		$helperArray = array();
		$feedLink = "";
		$files = Mage::getModel('nscexport/feed')->getCollection()
			->addFieldToFilter('enabled', true);
		if (isset($type)) {
			$feedTypes = Mage::getModel('nscexport/feed')->getCollection()
				->addFieldToFilter('code', $type)
				->addFieldToFilter('enabled', true)
				->load();
			foreach ($feedTypes as $feedType) {
				$feedType = $feedType->getType();
			}
			$files->addFieldToFilter('type', $feedType);
		}
		if (isset($feed)) {
			$feedLinks = Mage::getModel('nscexport/feed')->getCollection()
				->addFieldToFilter('code', $feed)
				->addFieldToFilter('enabled', true)
				->load();
			foreach ($feedLinks as $feedLink) {
				$feedLink = $feedLink->getLink();
			}
			$files->addFieldToFilter('link', $feedLink);
		}
		//Mage::log("Feed: ".$feed);
		$files->load();

		foreach ($files as $file) {
			if (!in_array($file->file_type, $helperArray)) {
				$fileArray[] = array("label" => $file->file_type, "value" => $file->code);
				$helperArray[] = $file->file_type;
			}
		}
		return $fileArray;
	}

	public function getAttributeCodes($storeId = 0, $validOnly = false,$frontendInput = "",$visibleAttributesOnly = false)
	{
	    $attributes = $this->getAttributeOptions($storeId, $validOnly,$frontendInput, $visibleAttributesOnly);
	    $codes = array();
	    foreach ($attributes as $attribute)
	        $codes[] = $attribute[self::VALUE];
	    return $codes;
	}

	public function getAttributeOptionsAll($storeId = 0,$feedCode = null)
	{
		return $this->getAttributeOptions($storeId,false,"",false,$feedCode);
	}
	
	public function getAttributeOptions($storeId = 0, $validOnly = false,$frontendInput = "",$visibleAttributesOnly = false,$feedCode = null)
	{
		$attributes = $this->_getAttributeOptions($storeId,$frontendInput,$visibleAttributesOnly);
		if(isset($feedCode))
			$attributes = $this->preprocessAttributeLabels($attributes,$feedCode);
		if( $validOnly) {
		    foreach( $attributes as $key => $data) {
		        if( (isset( $data['red']) && $data['red'] == 1) || (isset( $data['disabled']) && $data['disabled'] == 1)) {
		            unset( $attributes[$key]);
		        }
		    }
		}

		array_unshift($attributes,array(self::LABEL => $this->__("Please select"),self::VALUE=>""));
		return $attributes;
	}
	
	protected function preprocessAttributeLabels($attributes,$feedCode)
	{
		//Rename taxonomy fileds
		$feed = Mage::getModel('nscexport/feed')->getFeedByCode($feedCode);
		$taxonomyCode = $feed->getTaxonomyCode();
		if(empty($taxonomyCode))
			return $attributes;
		$taxonomy =  Mage::getModel('nscexport/taxonomy_setup')->getTaxonomyByCode($taxonomyCode);
		$taxonomyName = $taxonomy->getName();
		
		$labels = array();
		foreach( $attributes as $key => $data) 
		{
			if(!empty($data[self::LABEL]))
			{
				$label = $data[self::LABEL];
				if(strpos($label, self::KEYWORD_TAXONOMY) !== false)
				{
					$label = str_replace(self::KEYWORD_TAXONOMY, $taxonomyName." ".$this->__('Category'), $label);
					$attributes[$key][self::LABEL] = $label;
				}	
				$labels[] = ucfirst($label);
			}
		}
		
		//Sort the array
		array_multisort($labels,$attributes);
		
		return $attributes;
	}

    protected function _getAttributeOptions($storeId = 0,$frontendInput = "",$visibleAttributesOnly = false)
	{
		$attributes = $this->getPrefatchedVisibleProductAttributes($storeId,true);

		if(!$visibleAttributesOnly)
		{
			$loaderAttribs = Mage::helper('nscexport/data_loader')->getLoaderAttributes();			
			foreach($loaderAttribs as $key => $data)
			{
				if(array_key_exists($key, $attributes))
				{
					$attributes[$key] = array_merge($attributes[$key],$data);
				}
				else
					$attributes[$key] = $data;
			}
			ksort($attributes);
		}

		$labels = array();
		foreach ($attributes as $key => $attribute)
		{
			if(!empty($frontendInput))
			{
				if(empty($attribute[self::FRONTEND_INPUT]) || $attribute[self::FRONTEND_INPUT] != $frontendInput)
				{
					$unsetAttribute = false;
					if($frontendInput == "price")
					{
						if(strpos($attribute[self::VALUE],$frontendInput) === false)
							$unsetAttribute = true;
					}
					else
						$unsetAttribute = true;
					if($unsetAttribute)
					{
						unset($attributes[$key]);
						continue;
					}
				}
			}	
			$labels[] = ucfirst($attribute[self::LABEL]);
		}
		//Sort the array
		array_multisort($labels,$attributes);
		
		return $attributes;
	}

	public function getPrefatchedVisibleProductAttributes($storeId,$asOptionArray = false)
	{
		$collection = $this->getVisibleProductAttributes();
        if(!$asOptionArray)
            return $collection;

        $flatColumns = Mage::helper('nscexport/data_loader')->getProductFlatColumns($storeId);

        $attributes = array();
	    foreach ($collection as $item)
		{
			$attribute = array();
			$code = $item->getAttributeCode();
			$frontendInput = $item->getFrontendInput();
			$attribute[self::VALUE] = $code;
			$attribute[self::LABEL] = $this->getAttributeLabel($item,$storeId);
			$attribute[self::FRONTEND_INPUT] = $frontendInput;

			if(!in_array($code,$flatColumns))
				$attribute[self::RED] = 1;
			$attributes[$attribute[self::VALUE]] = $attribute;
		}
		return $attributes;
	}

	public function getPostProcessFunctionOptions($type = self::XML)
	{
		$functions = Mage::getModel('nscexport/data_transformation_xml')->getPostProcessFunctions();
		ksort($functions);

		if($type != self::XML)
			unset($functions[self::CDATA]);

		$result = array();
		foreach ($functions as $code => $label)
		{
			$function = array();
			$function[self::LABEL] = $label;
			$function[self::VALUE] = $code;
			$result[] = $function;
		}
		return $result;
	}

	public function getCountryOptions($isMultiselect = false) {
		$options = Mage::getSingleton('adminhtml/system_config_source_country')->toOptionArray($isMultiselect);

		$tmp = array_shift($options);
		array_unshift($options,$this->getOption(self::INTERNATIONAL_LABEL,self::INTERNATIONAL_CODE));

		if (!$isMultiselect)
			array_unshift($options,$tmp);

		return $options;
	}

	protected function getOption($label, $value = null) {
		if (!isset($value))
			$value = $label;
		return array(
			self::VALUE => $value,
			self::LABEL => $label
		);
	}

	public function optionsArrayToGrid($array) {
		$result = array();
		foreach ($array as $item) {
			$result[$item[self::VALUE]] = $item[self::LABEL];
		}
		return $result;
	}

	public function getAttributesSetup($layout, $asArray = true) {
		$pattern = $this->getDocPattern();
		$numberOfMatches = preg_match($pattern, $layout, $setup);
		if (!$numberOfMatches) {
			return false;
		}
		else
			$setup = $setup[0];
		if (!$asArray)
			return $setup;

		//transform to array
		$xml = $this->stringToXml($setup);
		$setup = $this->XMLnodeToArray($xml);

		return $setup;
	}

	public function getTrnasformationXslt($layout) {
		$pattern = $this->getDocPattern();
		$xlst = preg_replace($pattern, "", $layout);
		return $xlst;
	}

	protected function getDocPattern() {
		return "#\<".self::NOSTRESSDOC_TAG."\>(.+?)\<\/".self::NOSTRESSDOC_TAG."\>#s";
	}
}