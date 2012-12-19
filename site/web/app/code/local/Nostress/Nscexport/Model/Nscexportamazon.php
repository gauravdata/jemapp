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
 * Nscexport amazon model 
 *
 * @category   Nostress
 * @package    Nostress_Nscexport 
 */

class Nostress_Nscexport_Model_Nscexportamazon extends Mage_Core_Model_Abstract
{
	private $model;  //Common export model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportamazon');
    }
	  
	public function generateXml($nscexportId)
	{ 
	  	$encoding = "UTF-8";//encoding of xml file	
		$mainTagName = 'products';
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

        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('sku',true,$this->model->getProductSku()); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('item-price',false,$this->model->getProductOriginalPriceInclTax());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('currency',true,$this->model->getProductCurrency());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('quantity',true,$this->model->getProductQuantity());        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('sale-price',false,$this->model->getProductPriceInclTax());
        
        if($this->model->getProductIsInStock())
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('leadtime-to-ship',true,0); 
        }
        else if($this->model->productHasData('availability'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('leadtime-to-ship',true,$this->model->getProductOptionalAtribute('availability'));
        }        
                
        $result .="</product>";                          
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	
    	  
}
?>