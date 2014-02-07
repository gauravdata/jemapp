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
 * Nscexport ciao moddel 
 *
 * @category   Nostress
 * @package    Nostress_NscexportCiaoes 
 */

class Nostress_Nscexport_Model_Nscexportciaoes extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportciao');
    }	  

	public function generateXml($nscexportId)
	{
		$encoding = "UTF-8";//encoding of xml file	
		$mainTagName = 'offers';
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
    	$result ="<offer>"; 
    	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('offer-id',true,$this->model->getProductCurrentId()); 
    	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('name',true,$this->model->getProductName());  
    	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('brand',true,$this->model->getProductManufacturer());      	
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('category',true,$this->model->getProductFullCategoryPath(' > ')); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('description',true,$this->model->getProductDescription());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('price',false,$this->model->getProductPriceInclTax());    
        if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('delivery-charge',true,$this->model->getProductOptionalAtribute('shipping_cost'));	               
             	
        if($this->model->getProductIsInStock())
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('availability',false,"en stock"); 
        }
        else if($this->model->productHasData('availability'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('availability',true,$this->model->getProductOptionalAtribute('availability'));
        }
        else
        {
            $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('availability',false,"No disponible");
        }
               	
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('deeplink',true,$this->model->getProductUrl());        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('imageurl',true,$this->model->getProductImageUrl());        
     	if($this->model->productHasData('ean'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ean',true,$this->model->getProductTextAtribute('ean'));
        }
    	if($this->model->productHasData('isbn'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('isbn',true,$this->model->getProductTextAtribute('isbn'));
        }

        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('mpn',true,$this->model->getProductTextAtribute('mpn'));          
        $result .="</offer>";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }  
}
?>