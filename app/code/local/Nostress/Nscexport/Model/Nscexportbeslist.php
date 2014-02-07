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

class Nostress_Nscexport_Model_Nscexportbeslist extends Mage_Core_Model_Abstract
{
	private $model;  //Common export model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportbeslist');
    }
	  
	public function generateXml($nscexportId)
	{ 
	  	$encoding = "UTF-8";//encoding of xml file	
		$mainTagName = 'producten';
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
        $result ="<product>";        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Titel',true,$this->model->getProductName());
    	if($this->model->productHasData('ean'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('EAN',true,$this->model->getProductTextAtribute('ean'));
        }
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Merk',true,$this->model->getProductManufacturer());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Artikelcodefabrikant',true,$this->model->getProductSku());         
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Omschrijving',true,$this->model->getProductDescription());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Prijs',false,$this->model->getProductPriceInclTax()); 
        if($this->model->productHasData('delivery_time'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Levertijd',true,$this->model->getProductOptionalAtribute('delivery_time'));
        }
        else if($this->model->productHasData('delivery_date'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Levertijd',true,$this->model->getProductOptionalAtribute('delivery_date'));
        }
        else
        {          
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Levertijd',true,'48 uur');
        }
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Deeplink',true,$this->model->getProductUrl());  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Image-locatie',true,$this->model->getProductImageUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Categorie',true,$this->model->getProductFullCategoryPath('/'),true);
		if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Portokosten',true,$this->model->getProductOptionalAtribute('shipping_cost'));
        else 
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Portokosten',true,0);
      	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Winkelproductcode',true,$this->model->getProductCurrentId(),true);        
        $result .="</product>";                          
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	
    	  
}
?>