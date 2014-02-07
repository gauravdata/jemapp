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
 * Nscexport bestprice model 
 *
 * @category   Nostress
 * @package    Nostress_Nscexport 
 */

class Nostress_Nscexport_Model_Nscexportbestprice extends Mage_Core_Model_Abstract
{
	private $model;  //Common nscexport model
	const DATE_FORMAT = "%Y/%m/%d %H:%M";
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportbestprice');
    }
	  
	public function generateXml($nscexportId)
	{ 
	  	$encoding = "ISO-8859-7";//encoding of xml file	
		$mainTagName = 'products';
		$xmlHead = '<?xml version="1.0" encoding="'.$encoding.'"?><'.$mainTagName.'><date>'.StrFTime(self::DATE_FORMAT, Time()).'</date>';
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
        $result ="<product>";    

		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('productId',false,$this->model->getProductCurrentId(),false);        
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('title',true,$this->model->getProductName(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('description',true,$this->model->getProductDescription(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('price',false,$this->model->getProductPriceInclTax());
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('original_price',false,$this->model->getProductOriginalPriceInclTax());
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('image',true,$this->model->getProductImageUrl());
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('url',true,$this->model->getProductUrl());  
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('category_id',true,$this->model->getProductCategoryId(),true);     
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('category_name',true,$this->model->getProductFullCategoryPath('->'),true);
              
        $result .="</product>";                          
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	
    	  
}
?>