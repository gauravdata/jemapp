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
 * Nscexport leguide model 
 *
 * @category   Nostress
 * @package    Nostress_NscexportLovecnacene
 */

class Nostress_Nscexport_Model_Nscexportlovecnacene extends Mage_Core_Model_Abstract
{		
	private $model;  //Common nscexport model
	private $productPlace;  //product nscexport sequence number
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportlovecnacene');
    }
  
	public function generateXml($nscexportId)
	{
	  	$encoding = "windows-1250";//encoding of xml file	
		$mainTagName = 'lovecnacene';
		$merchantName = Mage::helper('nscexport')-> getMerchantName('lovecnacene'); 		
		
		$xmlHead = '<?xml version="1.0" encoding="'.$encoding.'"?><'.$mainTagName.' trgovec="'.$merchantName.'">';
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
		$result ='<product>';   	 
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('pid',false,$this->model->getProductCurrentId());
	    $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('manufacturer',true,$this->model->getProductManufacturer()); 
	    if($this->model->productHasData('mpn'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('mpn',true,$this->model->getProductTextAtribute('mpn'));
        }
	    if($this->model->productHasData('ean'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('upc-ean',true,$this->model->getProductTextAtribute('ean'));
        }	    
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('product-name',true,$this->model->getProductName());       
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('product-category',true,$this->model->getProductFullCategoryPath(' > '));
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('merchant-product-link',true,$this->model->getProductUrl());   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('product-image',true,$this->model->getProductImageUrl());                        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('product-detail',true,$this->model->getProductLongDescription());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('merchant-price',false,$this->model->getProductPriceInclTax()); 
	    if($this->model->getProductIsInStock())
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('merchant-stock',false,'Da'); 
        }
        else
        {
            $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('merchant-stock',false,'Ne');
        }
        if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('merchant-shipping',true,$this->model->getProductOptionalAtribute('shipping_cost'));
 
        $result .="</product>";
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
	}
}
?>