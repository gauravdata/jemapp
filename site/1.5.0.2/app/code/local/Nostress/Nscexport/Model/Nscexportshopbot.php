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
 * Nscexport heureka model 
 *
 * @category   Nostress
 * @package    Nostress_NscexportHeureka 
 */

class Nostress_Nscexport_Model_Nscexportshopbot extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportshopbot');
    }	 
    
	public function generateXml($nscexportId)
	{	
	  	$encoding = "utf-8";//encoding of xml file	
		$mainTagName = 'items';
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
        $result ="<item>";        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('id',true,$this->model->getProductCurrentId());
        if($this->model->productHasData('mpn'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('mpn',true,$this->model->getProductTextAtribute('mpn'));
        }        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('productname',true,$this->model->getProductName(),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('category',true,$this->model->getProductFullCategoryPath('::'),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('url',true,$this->model->getProductUrl(),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('photoUrl',true,$this->model->getProductImageUrl(),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('brand',true,$this->model->getProductManufacturer(),true); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('price',false,$this->model->getProductPriceInclTax());        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('description',true,$this->model->getProductDescription(),true);        
        $stock = 'YES';
        if(!$this->model->getProductIsInStock())
        	$stock = 'NO';
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('stock',false,$stock);                       
        $result .="</item>";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }
}
?>