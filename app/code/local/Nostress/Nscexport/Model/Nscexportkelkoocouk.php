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

class Nostress_Nscexport_Model_Nscexportkelkoocouk extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model

    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportkelkoocouk');
    }

	public function generateXml($nscexportId)
	{
	  	$encoding = "ISO-8859-1";//encoding of xml file  	
		$xmlHead = "Category".IS."Type".IS."FieldC".IS."FieldD".IS.
				"FieldE".IS."FieldF".IS."FieldG".IS."FieldH".IS.
				"FieldI".IS."FieldJ".IS."FieldK".IS."SKU".IS."Description".IS.
				"Promotion".IS."Image".IS."LinkToProduct".IS."Price".IS."DeliveryCost".IS."DeliveryTime".IS.
				"Availability".IS."Warranty".IS."Condition".IS."OfferType".IS."Bid\n";
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
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,$this->model->getProductParentCategoryName(),IS);        //category  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,$this->model->getProductCategoryName(),IS);        //type   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductManufacturer(),IS);        //manufacturer -- fielad C
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductName(),IS);        //name -- fielad D

        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'',IS);        //fielad E
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'',IS);        //fielad F
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'',IS);        //fielad G
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'',IS);        //fielad H
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'',IS);        //fielad I
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'',IS);        //fielad J
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductTextAtribute('ean'),IS);        //ean -- fielad K      
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductSku(),IS);        //sku   		  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductDescription(true),IS);        //description   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'',IS);   //promotion text
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductImageUrl(),IS);        //image url 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductUrl(),IS);        //product url        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductPriceInclTax(),IS);        //price   
        
        $shipping = 0;
        if($this->model->productHasData('shipping_cost'))
        	$shipping = $this->model->getProductOptionalAtribute('shipping_cost');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$shipping,IS);        //shipping_cost  
        
        $deliveryTime = '';
    	if($this->model->productHasData('delivery_time'))
        {
        	$deliveryTime = $this->model->getProductOptionalAtribute('delivery_time').' working days';       //stock_detail
        }
        else if($this->model->productHasData('delivery_date'))
        {
        	$deliveryTime = $this->model->getProductOptionalAtribute('delivery_date').' working days';       //stock_detail
        }
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$deliveryTime,IS);        //availability
        
        $disponible = 'Out of Stock';
        if($this->model->getProductIsInStock())
        	$disponible = 'In Stock';  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$disponible,IS);       //in_stock
        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductOptionalAtribute('warranty'),IS);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,'new',IS);        //condition   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,$this->model->getProductType(),IS);        //condition   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductTextAtribute('bid'),IS);        //bid  
        
        $result .="\n";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	  
}
?>