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
 * Nscexport kelkoo model 
 *
 * @category   Nostress
 * @package    Nostress_NscexportKelkoo 
 */

class Nostress_Nscexport_Model_Nscexportkelkoo extends Mage_Core_Model_Abstract
{		
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportkelkoo');
    }
  	  
	public function generateXml($nscexportId)
	{
	  	$encoding = "utf-8";//encoding of xml file	
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
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Merk',true,$this->model->getProductManufacturer());                
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Model',true,$this->model->getProductName());  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Omschrijving',true,$this->model->getProductDescription());        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Prijs',false,$this->model->getProductPriceExclTax());  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Deeplink',true,$this->model->getProductUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Image',true,$this->model->getProductImageUrl());       
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Categorie',true,$this->model->getProductCategoryName()); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Sku',false,$this->model->getProductSku());     
        $result .="</Product>\n";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	  
}
?>