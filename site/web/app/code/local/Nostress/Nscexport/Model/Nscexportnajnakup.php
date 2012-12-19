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
 * Nscexport najnakup model 
 *
 * @category   Nostress
 * @package    Nostress_Nscexport 
 */

class Nostress_Nscexport_Model_Nscexportnajnakup extends Mage_Core_Model_Abstract
{
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportnajnakup');
    }
	  
	public function generateXml($nscexportId)
	{ 
	  	$encoding = "utf-8";//encoding of xml file	
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
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('CODE',true,$this->model->getProductCurrentId());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('NAME',true,$this->model->getProductName());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('DESCRIPTION',true,$this->model->getProductDescription());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('IMAGE_URL',true,$this->model->getProductImageUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('PRODUCT_URL',true,$this->model->getProductUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute ('PRICE', false, $this->model->getProductOriginalPriceInclTax () );
        if ($this->model->productHasSpecialPrice ()) 
		{
			$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('SPECIAL_PRICE',false,$this->model->getProductPriceInclTax());
		}
		if($this->model->productHasData('shipping_cost'))
			$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('SHIPPING',true,$this->model->getProductOptionalAtribute('shipping_cost'));
    	if($this->model->productHasData('ean'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('EAN',true,$this->model->getProductTextAtribute('ean'));
        }
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('MANUFACTURER',true,$this->model->getProductManufacturer());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('CATEGORY',true,$this->model->getProductFullCategoryPath(' > '));
    	if($this->model->getProductIsInStock())
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('AVAILABILITY',false,"na skalde"); 
        }
        else if($this->model->productHasData('availability'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('AVAILABILITY',true,$this->model->getProductOptionalAtribute('availability'));
        }
        else
        {
            $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('AVAILABILITY',true,"na otázku");
        }               
        $result .="</SHOPITEM>";                          
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	
    	  
}
?>