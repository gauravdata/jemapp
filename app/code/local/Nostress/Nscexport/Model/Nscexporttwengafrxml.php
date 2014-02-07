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
 * @package    Nostress_NscexportTwengafr
 */

class Nostress_Nscexport_Model_Nscexporttwengafrxml extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexporttwengafrxml');
    }

	public function generateXml($nscexportId)
	{
	  	$encoding = "UTF-8";//encoding of xml file	
		$mainTagName = 'catalog';
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
        $result ="<product>"; 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('product_url',true,$this->model->getProductUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('designation',true,$this->model->getProductDescription(true));  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('price',true,$this->model->getProductPriceInclTax());  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('category',true,$this->model->getProductFullCategoryPath('>'));  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('image_url',true,$this->model->getProductImageUrl());  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('description',true,$this->model->getProductLongDescription(true));  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('manufacturer_id',true,$this->model->getProductSku());  
        $shipping = '0';
        if($this->model->productHasData('shipping_cost'))
        	$shipping = $this->model->getProductOptionalAtribute('shipping_cost');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('shipping_cost',true,$shipping);       //shipping_cost
        $disponible = 'N';
        if($this->model->getProductIsInStock())
        	$disponible = 'Y';  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('in_stock',true,$disponible);       //in_stock
        
        if($this->model->productHasData('delivery_time'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('stock_detail',true,$this->model->getProductOptionalAtribute('delivery_time'));       //stock_detail
        }
        else
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('stock_detail',true,'');       //stock_detail
        }
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('upc_ean',false,$this->model->getProductTextAtribute('ean'));//ean  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ISBN',false,$this->model->getProductTextAtribute('isbn'));     
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('brand',true,$this->model->getProductManufacturer());  //manufacturer        
        $result .="</product>";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	  
}
?>