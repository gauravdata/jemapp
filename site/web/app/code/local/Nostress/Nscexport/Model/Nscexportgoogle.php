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
 * @package    Nostress_Nscexportgoogle 
 */

class Nostress_Nscexport_Model_Nscexportgoogle extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportgoogle');
    }	 
    
	public function generateXml($nscexportId)
	{	
	  	$encoding = "utf-8";//encoding of xml file	
		$mainTagName = 'channel';
		$xmlHead = '<?xml version="1.0" encoding="'.$encoding.'"?><rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"><'.$mainTagName.'>';
		$xmlTail = '</'.$mainTagName.'></rss>';
		
		//Get nscexport values 
		$this->model = Mage::getModel('nscexport/nscexport')->load($nscexportId,'export_id');
		$store = Mage::getModel('core/store')->load($this->model->getStoreId()); //chosen store
		$this->model->setStore($store);
		$this->model->setNscexportEncoding($encoding);
		$this->model->setUpdateTime(now());
        $this->model->save();

        $xmlHead.= '<title>'.$this->model->getName().'</title>';
		$xmlHead.= '<link>'.Nostress_Nscexport_Helper_Data::getXmlUrl($this->model->getFilename(),strtolower($this->model->getSearchengine())).'</link>';
		$xmlHead.= '<description></description>';

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
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('g:id',true,$this->model->getProductCurrentId(),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('title',true,$this->model->getProductName(),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('link',true,$this->model->getProductUrl(),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('g:price',false,$this->model->getProductPriceInclTax(true),true);  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('description',true,$this->model->getProductDescription(),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('g:condition',true,'new',true); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('g:product_type',true,$this->model->getProductFullCategoryPath('/'),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('g:image_link',true,$this->model->getProductImageUrl(),true);
                $stock = 'in stock';
        if(!$this->model->getProductIsInStock())
        	$stock = 'out of stock';
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('g:availability',false,$stock,true);  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('g:quantity',true,$this->model->getProductQuantity(),true); 
        $result .="</item>";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }
}
?>