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
 * Nscexport greene model 
 *
 * @category   Nostress
 * @package    Nostress_Nscexport 
 */

class Nostress_Nscexport_Model_Nscexportgreene extends Mage_Core_Model_Abstract
{
	private $model;  //Common nscexport model
	const DATE_FORMAT = "%Y/%m/%d %H:%M";
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportgreene');
    }
	  
	public function generateXml($nscexportId)
	{ 
	  	$encoding = "ISO-8859-7";//encoding of xml file	
		$mainTagName = 'products';
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
        $result ='<product id="'.$this->model->getProductCurrentId().'">';    
     
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('name',true,$this->model->getProductName(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('link',true,$this->model->getProductUrl());
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('price_with_vat',false,$this->model->getProductPriceInclTax());
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('category',true,$this->model->getProductCategoryName(),true,' id="'.$this->model->getProductCategoryId().'"');
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('image',true,$this->model->getProductImageUrl());
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('thumbnail',true,$this->model->getProductImageUrl());
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('manufacturer',true,$this->model->getProductManufacturer());	
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('description',false,$this->model->getProductDescription(),true);              
        $result .="</product>";                          
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	
    	  
}
?>