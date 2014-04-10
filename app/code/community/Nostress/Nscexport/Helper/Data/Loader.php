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

class Nostress_Nscexport_Helper_Data_Loader extends Nostress_Nscexport_Helper_Data
{
    const GROUP_ROW_SEPARATOR = ";;";
    const GROUP_ROW_ITEM_SEPARATOR = "||";
    const GROUP_CONCAT = "GROUP_CONCAT";
    const GROUP_SEPARATOR = "SEPARATOR";
    const GROUPED_COLUMN_ALIAS = "concat_colum";
    
    const COLUMN_CATEGORIES_DATA = "ccd";
	
    const CONDITION_EXPORT_OUT_OF_STOCK = 'export_out_of_stock';
    const CONDITION_PARENTS_CHILDS = 'parents_childs';
    const CONDITION_TYPES = 'types';
    const DISABLED = "disabled";
    
    public function getCommonParameters()
    {
    	$params = array();
    	
    	$params[self::PARAM_REVIEW_URL] = $this->getGeneralConfig(self::PARAM_REVIEW_URL);
    	$params[self::PARAM_IMAGE_FOLDER] = $this->getGeneralConfig(self::PARAM_IMAGE_FOLDER);
    	$params[self::PARAM_ALLOW_INACTIVE_CATEGORIES_EXPORT] = $this->getGeneralConfig(self::PARAM_ALLOW_INACTIVE_CATEGORIES_EXPORT);
    	$params[self::PARAM_ALLOW_CHILD_PRODUCTS_EXPORT] = $this->getGeneralConfig(self::PARAM_ALLOW_CHILD_PRODUCTS_EXPORT);
    	
    	return $params;
    }
    
    public function groupConcatColumns($columns)
    {
        $res = self::GROUP_CONCAT . "(";
        $columnValues = array_values($columns);
        
        $columnString = "";
        $separator = $this->getGroupRowItemSeparator();
        foreach ($columnValues as $value) 
        {
            if(empty($columnString))
                $columnString = $value;
            else
        	    $columnString .= ",'{$separator}',".$value;
        }
        $res .= $columnString." ".self::GROUP_SEPARATOR." '{$this->getGroupRowSeparator()}'";
        
        $res .= ") as ".self::GROUPED_COLUMN_ALIAS;
        return $res;
    }
    
    protected function getGroupRowSeparator()
    {
        return self::GROUP_ROW_SEPARATOR;
    }
    
    protected function getGroupRowItemSeparator()
    {
        return self::GROUP_ROW_ITEM_SEPARATOR;
    }
    
	public function getPriceColumnFormat($columnName, $taxRateColumnName,$currencyRate = null, $originalPriceIncludeTax=false,$calcPriceIncludeTax = true, $round=true,$weeeColumnTaxable,$weeeColumnNonTaxable)
	{
		$resSql = $columnName;
		
		if(isset($currencyRate) && is_numeric($currencyRate))
		{
			$resSql .= "*".$currencyRate;
		}
		
		if(!empty($weeeColumnTaxable))
			$resSql = "(({$resSql})+{$weeeColumnTaxable})";
		
		if(!$originalPriceIncludeTax && $calcPriceIncludeTax)
		{
			$resSql .= "*(1+ IFNULL(".$taxRateColumnName.",0))";		
		}
		else if($originalPriceIncludeTax && !$calcPriceIncludeTax)
		{
			$resSql .= "*(1/(1+ IFNULL(".$taxRateColumnName.",0)))";
		}	

		if(!empty($weeeColumnNonTaxable))
			$resSql = "(({$resSql})+{$weeeColumnNonTaxable})";
			
	    if ($round) {
	    	$resSql = $this->getRoundSql($resSql);            
	    }
	   
	    return $resSql;
	}
	
	public function getRoundSql($column,$decimalPlaces = 2)
	{
		return "ROUND(".$column.",{$decimalPlaces})";     
	}
	
	public function getStoreCurrencyRate($store)
	{
		$from = $store->getBaseCurrencyCode();
		$to = $store->getCurrentCurrencyCode();

		if($from == $to)
			return null;
		else
			return $this->getCurrencyRate($from,$to);
	}
	
	protected function getCurrencyRate($from,$to)
	{
		return Mage::getModel('directory/currency')->load($from)->getRate($to);
	}
	
	public function checkFlatCatalogs()
	{
	 	$allStoreIds = array_keys(Mage::app()->getStores());
	    foreach($allStoreIds as $id)
	    {
		    $this->checkFlatCatalog($id);
	    }
	}
	
	public function checkFlatCatalog($storeId)
	{
		$this->getProductFlatColumns($storeId);
		$this->getCategoryFlatColumns($storeId);
	}
	
	public  function getProductFlatColumns($storeId)
	{
		try
		{
	    	$productFlatResource = Mage::getResourceModel('catalog/product_flat')->setStoreId($storeId);
	    	return $productFlatResource->getAllTableColumns();
		}
		catch (Exception $e)
		{
			Mage::throwException("11");			
			return array();
		} 
	}
	
	public  function getCategoryFlatColumns($storeId)
	{
	    try
	    {
			$flatResource = Mage::getResourceModel('catalog/category_flat')->setStoreId($storeId);	    
	    	$describe =  Mage::getSingleton('core/resource')->getConnection('core_write')->describeTable($flatResource->getMainTable());
        	return array_keys($describe);
        }
        catch (Exception $e)
        {
        	Mage::throwException("11");        	
        	return array();
        }
	}
	
	public function getLoaderAttributes()
	{
		$resource = Mage::getResourceModel('nscexport/data_loader_product');
		
		$columns = $resource->getAllColumns();
		$staticColumns = $resource->getStaticColumns();
		$staticColumns = array_combine($staticColumns, $staticColumns);
		$columns = array_merge($columns,$staticColumns);
		$multiColumns = $resource->getMultiColumns();
		
		ksort($columns);
		$attributes = array();
		foreach ($columns as $alias => $column) 
		{
			$attribute = array();
			$attribute[self::VALUE] = $alias;
			$attribute[self::LABEL] = $this->codeToLabel($alias);
			if(in_array($alias,$multiColumns))
				$attribute[self::DISABLED] = "1";
			$attributes[$attribute[self::VALUE]] = $attribute;
		}
		return $attributes;
	}
	
	public function getDefaultTaxCountry($store)
	{
		return $store->getConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_COUNTRY);
	}
	
	public function getDefaultTaxRegion($store)
	{
		return $store->getConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_REGION);
	}
	
	public function reloadCache($storeIds,$websiteIds)
	{
		foreach ($storeIds as $storeId)
		{
			Mage::getModel('nscexport/cache_categorypath')->reload($storeId);
			Mage::getModel('nscexport/cache_superattributes')->reload($storeId);
			Mage::getModel('nscexport/cache_mediagallery')->reload($storeId);
			Mage::getModel('nscexport/cache_tax')->reload($storeId);
		}
		
		foreach ($websiteIds as $websiteId) {
			Mage::getModel('nscexport/cache_weee')->reload($websiteId);
		}
	}
}