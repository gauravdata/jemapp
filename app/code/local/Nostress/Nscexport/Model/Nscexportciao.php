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
 * @package    Nostress_NscexportCiao 
 */

class Nostress_Nscexport_Model_Nscexportciao extends Mage_Core_Model_Abstract
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
		$mainTagName = 'Offers';
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
    	$result ="<Offer>"; 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('MerchantCategory',true,$this->model->getProductCategoryName()); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('OfferID',true,$this->model->getProductCurrentId()); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Name',true,$this->model->getProductName());       
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Brand',true,$this->model->getProductManufacturer());                
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Description',true,$this->model->getProductDescription());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('DeepLink',true,$this->model->getProductUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ProductID',TRUE,$this->model->getProductSku());  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ImageUrl',true,$this->model->getProductImageUrl());        
        
        if($this->model->productHasData('delivery_time'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Delivery',true,$this->model->getProductOptionalAtribute('delivery_time'));
        else if($this->model->productHasData('delivery'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Delivery',true,$this->model->getProductOptionalAtribute('delivery'));
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Prices',false,$this->model->getProductPriceExclTax());
        if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ShippingCost',true,$this->model->getProductOptionalAtribute('shipping_cost'));	
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('Currency',true,$this->model->getProductCurrency());     
        $result .="</Offer>";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }  
}
?>