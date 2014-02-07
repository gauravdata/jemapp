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
 * Nscexport monitorcien model 
 *
 * @category   Nostress
 * @package    Nostress_Nscexport 
 */

class Nostress_Nscexport_Model_Nscexportmonitorcien extends Mage_Core_Model_Abstract
{
	private $model;  //Common export model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportmonitorcien');
    }
	  
	public function generateXml($nscexportId)
	{ 
	  	$encoding = "ISO-8859-2";//encoding of xml file	
		$mainTagName = 'offers';
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
        $result ="<offer>";        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('name',true,$this->model->getProductName());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('category',true,$this->model->getProductFullCategoryPath('/'),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('description',true,$this->model->getProductDescription());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('price_vat',false,$this->model->getProductPriceInclTax());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('image',true,$this->model->getProductImageUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('url',true,$this->model->getProductUrl());                 
        $result .="</offer>";                          
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	
    	  
}
?>