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
 * Nscexport nextag.com model 
 *
 * @category   Nostress
 * @package    Nostress_Nscexport 
 */

class Nostress_Nscexport_Model_Nscexportnextag extends Mage_Core_Model_Abstract
{
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportnextag');
    }
	  
	public function generateXml($nscexportId)
	{ 
	  	$encoding = "UTF-8";//encoding of xml file	
		$mainTagName = 'DataFeeds';
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
        $result ="<item_data>";        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_unique_id',true,$this->model->getProductCurrentId());
        if($this->model->productHasData('mpn'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_mpn',true,$this->model->getProductTextAtribute('mpn'));
        }
	    if($this->model->productHasData('ean'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_ean',true,$this->model->getProductTextAtribute('ean'));
        }	
        if($this->model->productHasData('upc'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_upc',true,$this->model->getProductTextAtribute('upc'));
        }	
        if($this->model->productHasData('isbn'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_isbn',true,$this->model->getProductTextAtribute('isbn'));
        }	
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_manufacturer',true,$this->model->getProductManufacturer());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_name',true,$this->model->getProductName());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_category',true,$this->model->getProductCategoryName());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_short_desc',true,$this->model->getProductDescription());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_page_url',true,$this->model->getProductUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_image_url',true,$this->model->getProductImageUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_price',false,$this->model->getProductPriceInclTax()); 
        if($this->model->getProductIsInStock())
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_inventory',true,'In Stock');
        else
            $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item_inventory',true,"Out of Stock");
      
        $result .="</item_data>";                          
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	
    	  
}
?>