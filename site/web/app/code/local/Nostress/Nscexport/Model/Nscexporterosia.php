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
 * Nscexport erosia model 
 *
 * @category   Nostress
 * @package    Nostress_Nscexporterosia
 */
define("IS", "|"); //item separator

class Nostress_Nscexport_Model_Nscexporterosia extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model

    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexporterosia');
    }

	public function generateXml($nscexportId)
	{
	  	$encoding = "UTF-8";//encoding of xml file	
	  	$xmlHead = array('artikel_nr','artikel_name','artikel_kurztext','artikel_kategorie','artikel_url', 'artikel_img_url', 'artikel_preis','artikel_waehrung','artikel_ab18', 'artikel_marke', 'artikel_redpreis','artikel_versand','artikel_farbe'); 
		$xmlHead = implode(IS, $xmlHead)."\r\n";
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
 		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductCurrentId(),IS); // product id       
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductName(),IS);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductDescription(true),IS); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductFullCategoryPath('/'),IS);        //category        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductUrl(),IS);        //product url  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductImageUrl(),IS);        //image url
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductOriginalPriceInclTax(),IS);        //original price    
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductCurrency(),IS);        //currency
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,'Nein',IS);        //ab 18    
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductManufacturer(),IS);  //manufacturer  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductPriceInclTax(),IS);        //final price  
        $shipping = 0; //shipping_cost
        if($this->model->productHasData('shipping_cost'))
        	$shipping = $this->model->getProductOptionalAtribute('shipping_cost');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$shipping,IS);
 		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductOptionalAtribute('color'),IS);  //color
        
        $result .="\n";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	  
}
?>