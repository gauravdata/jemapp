<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Nostress_Nscexport_Model_Entity_Attribute_Source_Taxonomy extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	const CATEGORY = 'category';	
	const SOURCE_MODEL = 'nscexport/taxonomy';
	
	const VALUE = 'value';
	const LABEL = 'label';
	const PATH = 'path';
	
	const SELECT_LABEL = "Please select value...";
	
	protected $_src;
	protected $_taxonomyCode;
	protected $_locale;
	
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) 
        {
        	$this->_options = $this->_getAllOptions();
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) 
        {              	 	
            $_options[$option[self::VALUE]] = $option[self::LABEL];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option[self::VALUE] == $value) {
                return $option[self::LABEL];
            }
        }
        return false;
    }
    
    protected function _getAllOptions()
    {
    	$code = $this->getTaxonomyCode();
    	$locale = $this->getLocale();
		$select = $this->getSelectFields();
		$options = array();
		if($select != false)
			$options = $this->getSource()->getTaxonomy($code,$locale,$select);	
				
		$labels = array();		
		foreach($options as $key => $option)
		{
			if(isset($option[self::VALUE]) && $option[self::VALUE] == Nostress_Nscexport_Model_Taxonomy_Preprocessor::UNSELECTABLE_CATEGORY_ID)
        		unset($options[$key]);
			else
				$labels[] = $option[self::PATH];
		} 	
		array_multisort($labels,SORT_STRING,$options);		
		$options = array_merge(array("" => $this->helper()->__(self::SELECT_LABEL)),$options);
		
		return $options;
    }
    
	protected function getSelectFields()
	{
		return $this->getSource()->getSelectFields($this->getTaxonomyCode());
	}
    
    protected function getTaxonomyCode()
    {
    	if(!isset($this->_taxonomyCode))
    	{
    		if(!isset($this->_attribute))
    			return "";
    		$attributeCode = $this->_attribute->getAttributeCode();
        	$this->_taxonomyCode = $this->helper()->createTaxonomyCodeFromAttributeCode($attributeCode);
    	}
    	return $this->_taxonomyCode;
    }
    
    protected function getLocale()
    {
    	if(!isset($this->_locale))
    	{
    		$category = Mage::registry(self::CATEGORY);
    		$storeId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    		if(isset($category) && is_object($category))
        		$storeId = $category->getStoreId();
    		
    		$store = Mage::app()->getStore($storeId);
        	$this->_locale = $this->helper()->getStoreLocale($store);
    	}
    	return $this->_locale;
    }
    
    protected function getSource()
    {
    	if(!isset($this->_src))
    		$this->_src = Mage::getModel(self::SOURCE_MODEL);
    	return $this->_src;
    }
    
    protected function helper()
    {
    	return Mage::helper('nscexport');
    }

}
