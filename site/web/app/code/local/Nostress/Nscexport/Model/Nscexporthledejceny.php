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
 * Nscexport hledejceny model 
 *
 * @category   Nostress
 * @package    Nostress_NscexportHledejceny 
 */

class Nostress_Nscexport_Model_Nscexporthledejceny extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexporthledejceny');
    }	 
    
	public function generateXml($nscexportId)
	{	
	  $encoding = "windows-1250";//encoding of xml file	
		$mainTagName = 'SHOP';
		$xmlHead = '<?xml version="1.0" encoding="'.$encoding.'"?><'.$mainTagName.'>';
		$xmlTail = '</'.$mainTagName.'>';
		
		//Get nscexport values 
		$this->model = Mage::getModel('nscexport/nscexport')->load($nscexportId,'export_id');
		$store = Mage::getModel('core/store')->load($this->model->getStoreId()); //chosen store
		$this->model->setStore($store);
		$this->model->setNscexportEncoding($encoding);
		$this->model->setUpdateTime(now());
    $this->model->save();
    return Nostress_Nscexport_Helper_Data::exportProducts($this->model,$xmlHead,$xmlTail,$this);
	}
    
    /**
     * Return xml string with product attributes
     *
     * @param Mage_Catalog_Model_Product $product     
     * @return string
     */
    public function addProductAttributes()
    {    
        $result ="<SHOPITEM>";
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ID',true,$this->model->getProductCurrentId());       
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('PRODUCT',true,$this->model->getProductName());       
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('DESCRIPTION',true,$this->model->getProductDescription());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('URL',true,$this->model->getProductUrl());   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('IMGURL',true,$this->model->getProductImageUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('PRICE',false,$this->model->getProductPriceExclTax());               
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('PRICE_VAT',false,$this->model->getProductPriceInclTax());         		
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('CATEGORYTEXT',true,$this->model->getProductFullCategoryPath(" - "));               
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('MANUFACTURER',true,$this->model->getProductManufacturer());                
        $result .="</SHOPITEM>\n";
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }
}
?>