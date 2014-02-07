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
 * Nscexport Koopjespakker model 
 *
 * @category   Nostress
 * @package    Nostress_NscexportKoopjespakker 
 */

class Nostress_Nscexport_Model_Nscexportkoopjespakker extends Mage_Core_Model_Abstract
{		
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportkoopjespakker');
    }
  	  
	public function generateXml($nscexportId)
	{
	  	$encoding = "UTF-8";//encoding of xml file	
		$xmlTail = '';
		
		//Get nscexport values 
		$this->model = Mage::getModel('nscexport/nscexport')->load($nscexportId,'export_id');
		$store = Mage::getModel('core/store')->load($this->model->getStoreId()); //chosen store
		$this->model->setStore($store);
		$this->model->setNscexportEncoding($encoding);
		$this->model->setUpdateTime(now());
        $this->model->save();

        $engine = strtolower($this->model->getSearchengine());
        $xmlHead = "#country=".$this->model->getStoreCountry()."\n#type=extended\n#currency=".$this->model->getProductCurrency()."\n#version=5.0\n#HTTP_SERVER_VARS_PHP_SELF=".Nostress_Nscexport_Helper_Data::getPath($engine).$engine.'/'.$this->model->getFilename()."\ntitle\turl\tdescription\tprice\tofferid\timage\tmanufacturer\tcategory\tsku\tean\tshippingcost\n";
		
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
        $result ="";    
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeTab(true,$this->model->getProductName());   //name
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeTab(true,$this->model->getProductUrl());    //product url
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeTab(true,$this->model->getProductDescription(true));      //description 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeTab(true,$this->model->getProductPriceInclTax());     //price
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeTab(false,$this->model->getProductCurrentId());     //ProductCode
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeTab(true,$this->model->getProductImageUrl());    //product image 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeTab(true,$this->model->getProductManufacturer());        //manufacturer   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeTab(true,$this->model->getProductFullCategoryPath(' / '));      //category  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeTab(true,$this->model->getProductSku()); //sku  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeTab(false,$this->model->getProductTextAtribute('ean'));//ean              
        if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeTab(false,$this->model->getProductOptionalAtribute('shipping_cost'));
        else 
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeTab(false,0);
      	/*
        if($this->model->getProductPriceInclTax()>50)
        	$result .=Nostress_Nscexport_Helper_Data::formatProductAttributeTab(false,"0");// Shippingcost
        else
        	$result .=Nostress_Nscexport_Helper_Data::formatProductAttributeTab(false,"7.50");// Shippingcost   
        */
        $result .="\n";


        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	  
}
?>
