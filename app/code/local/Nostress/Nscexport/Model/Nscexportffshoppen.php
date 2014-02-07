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

class Nostress_Nscexport_Model_Nscexportffshoppen extends Mage_Core_Model_Abstract
{
	private $model;  //Common export model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportffshoppen');
    }
	  
	public function generateXml($nscexportId)
	{ 
	  	$encoding = "UTF-8";//encoding of xml file	
		$mainTagName = 'Producten';
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
        $result ="<Product>";        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Title',true,$this->model->getProductName());
    	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Description',true,$this->model->getProductDescription());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Price',false,$this->model->getProductPriceInclTax()); 
        
        if($this->model->productHasData('delivery_time'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Deliverytime',true,$this->model->getProductOptionalAtribute('delivery_time'));
        }
        else if($this->model->productHasData('delivery_date'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Deliverytime',true,$this->model->getProductOptionalAtribute('delivery_date'));
        }
        else
        {    
        	$locRes =  "Out of stock";
        	if($this->model->getProductIsInStock() == 1)
        		$locRes = "In stock";   
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Deliverytime',true,$locRes);
        }
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Image-location',true,$this->model->getProductImageUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Category',true,$this->model->getProductFullCategoryPath('/'),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Shopproductcode',true,$this->model->getProductCurrentId(),true);                
        if($this->model->productHasData('ean'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Ean',true,$this->model->getProductTextAtribute('ean'));
        }
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Brand',true,$this->model->getProductManufacturer());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Fabricproductcode',true,$this->model->getProductSku());         
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Longdescription',true,$this->model->getProductLongDescription()); 	
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ProductURL',true,$this->model->getProductUrl());  
        if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Sendingcosts',true,$this->model->getProductOptionalAtribute('shipping_cost'));
        else 
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Sendingcosts',true,0);
      	      
        $result .="</Product>";                          
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	
    	  
}
?>