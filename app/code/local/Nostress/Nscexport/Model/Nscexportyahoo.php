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
 * Nscexport twenga model 
 *
 * @category   Nostress
 * @package    Nostress_NscexportTwenga 
 */
define("IS", "\t"); //item separator

class Nostress_Nscexport_Model_Nscexportyahoo extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model

    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportyahoo');
    }

	public function generateXml($nscexportId)
	{
	  	$encoding = "UTF-8";//encoding of xml file	
		$xmlHead = "code".IS."name".IS."description".IS."price".IS.
				"product-url".IS."merchant-site-category".IS."medium".IS."image-url".IS.
				"upc".IS."isbn".IS."brand".IS."manufacturer".IS."manufacturer-part-no".IS.
				"model-no".IS."ean".IS."classification".IS."condition".IS."gender".IS."age-group".IS.
				"age-range".IS."size".IS."nrf-size".IS."color".IS."nrf-color".IS.
				"sale-price".IS."msrp".IS."in-stock".IS."availability".IS."promo-text".IS.
				"shipping-price".IS."shipping-weight".IS."shipping- surcharge".IS."shipping-class\n";
		$xmlTail = '';
		
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
        $result =""; 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductSku(),IS);        //sku   		
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductName(),IS);        //name   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductDescription(true),IS);        //description   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductOriginalPriceInclTax(),IS);        //orig price   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductUrl(),IS);        //product url   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,$this->model->getProductFullCategoryPath(' > '),IS);        //category   
        
        $medium = '';
        if($this->model->productHasData('medium'))
        	$medium = $this->model->getProductOptionalAtribute('medium');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$medium,IS);   //medium *********************************************
        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductImageUrl(),IS);        //image url   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductTextAtribute('upc'),IS);        //upc ********************************************   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductTextAtribute('isbn'),IS);        //isbn ********************************************
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductManufacturer(),IS);        //manufacturer   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductManufacturer(),IS);        //brand   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,$this->model->getProductCurrentId(),IS);        //manufacturer- part-no   
        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,'',IS);        //model no   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductTextAtribute('ean'),IS);        //ean   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,'new',IS);        //classification   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'new',IS);        //condition   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductTextAtribute('gender'),IS);        //gender  
        
        $age_group = '';
        if($this->model->productHasData('age_group'))
        	$age_group = $this->model->getProductOptionalAtribute('age_group');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$age_group,IS);        //age-group   
        $age_range = '';
        if($this->model->productHasData('age_range'))
        	$age_range = $this->model->getProductOptionalAtribute('age_range');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$age_range,IS);        //age-range 
        $size = '';
        if($this->model->productHasData('size'))
        	$size = $this->model->getProductOptionalAtribute('size'); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$size,IS);        //size  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductTextAtribute('nrf_size'),IS);        //nrf-size  
        
        $color = '';
        if($this->model->productHasData('color'))
        	$color = $this->model->getProductOptionalAtribute('color');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$color,IS);        //color   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductTextAtribute('nrf_color'),IS);        //nrf-color   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductPriceInclTax(),IS);        //sale price   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'',IS);        //msrp
        $disponible = 'N';
        if($this->model->getProductIsInStock())
        	$disponible = 'Y';  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$disponible,IS);       //in_stock
          
        $deliveryTime = '';
    	if($this->model->productHasData('delivery_time'))
        {
        	$deliveryTime .= $this->model->getProductOptionalAtribute('delivery_time');       //stock_detail
        }
        else if($this->model->productHasData('delivery_date'))
        {
        	$deliveryTime .= $this->model->getProductOptionalAtribute('delivery_date');       //stock_detail
        }
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$deliveryTime,IS);        //availability
        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'',IS);        //promo-text
        $shipping = 0;
        if($this->model->productHasData('shipping_cost'))
        	$shipping = $this->model->getProductOptionalAtribute('shipping_cost');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$shipping,IS);        //shipping_cost   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductWeight(),IS);        //weight   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'',IS);        //shipping- surcharge  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductTextAtribute('shipping_class'),IS);        //shipping-class      
        $result .="\n";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	  
}
?>