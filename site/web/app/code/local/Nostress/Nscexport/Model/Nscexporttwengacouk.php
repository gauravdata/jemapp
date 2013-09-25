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
 * @package    Nostress_NscexportTwengacouk 
 */

class Nostress_Nscexport_Model_Nscexporttwengacouk extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexporttwengacouk');
    }

	public function generateXml($nscexportId)
	{
	  	$encoding = "UTF-8";//encoding of xml file	
		$xmlHead = "brand|designation|price|product_url|description|image_url|category|merchant_id|upc_ean|in_stock|stock_detail|shipping_cost\n";
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
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductManufacturer());        //manufacturer        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductName());   //name
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductPriceInclTax());     //price
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductUrl());    //product url           
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductDescription(true));      //description 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductImageUrl());    //product image    
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductFullCategoryPath(' / '));      //category    
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,$this->model->getProductCurrentId());     //ProductCode
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,$this->model->getProductTextAtribute('ean'));//ean              
        $disponible = 'N';
        if($this->model->getProductIsInStock())
        	$disponible = 'Y';  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$disponible);       //in_stock
        
        if($this->model->productHasData('delivery_time'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductOptionalAtribute('delivery_time'));       //stock_detail
        }
        else if($this->model->productHasData('delivery_date'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductOptionalAtribute('delivery_date'));       //stock_detail
        }
        else
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'');       //stock_detail
        }
        
        $shipping = '0';
        if($this->model->productHasData('shipping_cost'))
        	$shipping = $this->model->getProductOptionalAtribute('shipping_cost');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$shipping);       //shipping_cost
                
        $result .="\n";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	  
}
?>