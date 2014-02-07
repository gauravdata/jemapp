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

class Nostress_Nscexport_Model_Nscexportcleafs extends Mage_Core_Model_Abstract
{
	private $model;  //Common export model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportcleafs');
    }
	  
	public function generateXml($nscexportId)
	{ 
	  	$encoding = "UTF-8";//encoding of xml file	
		$mainTagName = 'productFeed';
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
        $result ='<product id="'.$this->model->getProductCurrentId().'">';        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('name',true,$this->model->getProductName());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('price',false,$this->model->getProductPriceInclTax()); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('description',true,$this->model->getProductLongDescription(true));
       	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('productURL',true,$this->model->getProductUrl());  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('imageURL',true,$this->model->getProductImageUrl());
        
        $result .= "<additional>";
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('category',true,$this->model->getProductFullCategoryPath(';'),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('brand',true,$this->model->getProductManufacturer());
        if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('shippingCost',true,$this->model->getProductOptionalAtribute('shipping_cost'));
        else 
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('shippingCost',true,0);
        
        if($this->model->productHasData('delivery_time'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('deliveryTime',true,$this->model->getProductOptionalAtribute('delivery_time'));
        }
        else
        {          
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('deliveryTime',true,'2 werkdagen');
        }
        	
        $result .= "</additional>";        
        $result .="</product>";                          
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	
    	  
}
?>