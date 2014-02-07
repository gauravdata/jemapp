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
 * Nscexport ciao moddel 
 *
 * @category   Nostress
 * @package    Nostress_NscexportComparegroup 
 */

class Nostress_Nscexport_Model_Nscexportcomparegroupeu extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportcomparegroupeu');
    }	  

	public function generateXml($nscexportId)
	{
		$encoding = "ISO-8859-1";//encoding of xml file	
		$mainTagName = 'Products';
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
    	$result ="<Product>"; 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Category',true,$this->model->getProductParentCategoryName());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('SubCategory',true,$this->model->getProductCategoryName());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Brand',true,$this->model->getProductManufacturer());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ProductName',true,$this->model->getProductName());
    	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Deeplink',true,$this->model->getProductUrl());
    	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Price',false,$this->model->getProductPriceInclTax());
    	
    	$deliveryTime = '';
    	if($this->model->productHasData('delivery_time'))
        {
        	$deliveryTime = $this->model->getProductOptionalAtribute('delivery_time');       //stock_detail
        }
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('DeliveryPeriod',true,$deliveryTime);        //availability
        $shipping = 0;
        if($this->model->productHasData('shipping_cost'))
        	$shipping = $this->model->getProductOptionalAtribute('shipping_cost');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('DeliveryCosts',true,$shipping);
    	
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('OfferId',true,$this->model->getProductCurrentId());
    	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ProductVendorPartNr',true,$this->model->getProductSku()); 
    	if($this->model->productHasData('ean'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ProductEAN',true,$this->model->getProductTextAtribute('ean'));
        } 
    	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ProductDescription',true,$this->model->getProductLongDescription()); 
    	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('DeeplinkPicture',true,$this->model->getProductImageUrl());
    	$availability = '0';
        if($this->model->getProductIsInStock())
        	$availability = '1';  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('StockStatus',true,$availability);  
    	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ProductsInStock',true,$this->model->getProductQuantity());     
        if($this->model->productHasData('promotiontext'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('PromotionText',true,$this->model->getProductTextAtribute('promotiontext'));
        }
        $result .="</Product>";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }  
}
?>