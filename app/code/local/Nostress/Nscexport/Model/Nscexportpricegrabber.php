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
 * @package    Nostress_NscexportTwenga 
 */
define("IS", "\t"); //item separator

class Nostress_Nscexport_Model_Nscexportpricegrabber extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model

    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportpricegrabber');
    }

	public function generateXml($nscexportId)
	{
	  	$encoding = "UTF-8";//encoding of xml file	
		$xmlHead = "Unique Retailer SKU (RETSKU)".IS."Manufacturer Name".IS."Manufacturer Part Number (MPN)".IS."Product Title".IS.
				"Categorization".IS."Product URL".IS."Image URL".IS."Detailed Description".IS.
				"Selling Price".IS."Product Condition".IS."Availability".IS."EAN".IS."Shipping Costs".IS.
				"Weight\n";
		$xmlTail = '';
		
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
        $result =""; 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductSku(),IS);        //sku   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductManufacturer(),IS);        //manufacturer  		
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,$this->model->getProductCurrentId(),IS);        //manufacturer- part-no         
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductName(),IS);        //name   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,$this->model->getProductFullCategoryPath(' > '),IS);        //category
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductUrl(),IS);        //product url  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductImageUrl(),IS);        //image url           
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductLongDescription(true),IS);        //description           
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductPriceInclTax(),IS);        //final price   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'new',IS);        //condition  
        $disponible = 'No';
        if($this->model->getProductIsInStock())
        	$disponible = 'Yes';  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$disponible,IS);       //in_stock
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductTextAtribute('ean'),IS);        //ean  
        $shipping = 0;
        if($this->model->productHasData('shipping_cost'))
        	$shipping = $this->model->getProductOptionalAtribute('shipping_cost');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$shipping,IS);        //shipping_cost
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductWeight(),IS);        //weight 
		$result .="\n";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	  
}
?>