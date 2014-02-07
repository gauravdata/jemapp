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
 * Nscexport beslist model 
 *
 * @category   Nostress
 * @package    Nostress_Nscexport 
 */

class Nostress_Nscexport_Model_Nscexportshopping extends Mage_Core_Model_Abstract
{
	private $model;  //Common export model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportshopping');
    }
	  
	public function generateXml($nscexportId)
	{ 
	  	$encoding = "UTF-8";//encoding of xml file	
		$mainTagName = 'Products';
		$xmlHead = '<?xml version="1.0" encoding="'.$encoding.'"?><'.$mainTagName.'>';
		$xmlTail = '</'.$mainTagName.'>';
		
		//Get export values 
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
    	//$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('aaa',true,$this->model->());
    	
        $result ="<Product>";        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Merchant_SKU',true,$this->model->getProductSku());
    	if($this->model->productHasData('mpn'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('MPN',true,$this->model->getProductTextAtribute('mpn'));
        }
    	if($this->model->productHasData('upc'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('UPC',true,$this->model->getProductTextAtribute('upc'));
        }
        if($this->model->productHasData('ean'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('EAN',true,$this->model->getProductTextAtribute('ean'));
        }
   		if($this->model->productHasData('isbn'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ISBN',true,$this->model->getProductTextAtribute('isbn'));
        }
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Manufacturer',true,$this->model->getProductManufacturer());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Product_Name',true,$this->model->getProductName());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Product_URL',true,$this->model->getProductUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Current_Price',false,$this->model->getProductPriceInclTax(true)); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Original_Price',false,$this->model->getProductOriginalPriceInclTax(true));
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Category_Name',true,$this->model->getProductParentCategoryName(),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Sub-category_Name',true,$this->model->getProductCategoryName(),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Product_Description',true,$this->model->getProductLongDescription(true));
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Image_URL',true,$this->model->getProductImageUrl());
        
        if($this->model->productHasData('color'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Colour',true,$this->model->getProductOptionalAtribute('color'));
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Product_Weight',false,$this->model->getProductWeight());
        
     	if($this->model->getProductIsInStock())
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Stock_Availability',false,"Y"); 
        }
        else if($this->model->productHasData('availability'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Stock_Availability',true,$this->model->getProductOptionalAtribute('availability'));
        }
        else
        {
            $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Stock_Availability',false,"N");
        }
        
        if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Shipping_Rate',true,$this->model->formatPrice($this->model->getProductOptionalAtribute('shipping_cost')));
        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Bundle',false,$this->model->isProductType('bundle')?"Y":"N");   			        
        $result .="</Product>";                          
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	
    	  
}
?>