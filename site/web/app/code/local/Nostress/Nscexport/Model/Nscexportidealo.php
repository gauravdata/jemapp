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
 * @package    Nostress_Nscexportidealo 
 */
define("IS", "|"); //item separator

class Nostress_Nscexport_Model_Nscexportidealo extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model

    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportidealo');
    }

	public function generateXml($nscexportId)
	{
	  	$encoding = "UTF-8";//encoding of xml file	
	  	
	  	$mainTagName = 'LISTE';
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
    	$result ="<ARTIKEL>";
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ARTIKELNUMMER',true,$this->model->getProductCurrentId()); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('EAN',true,$this->model->getProductTextAtribute('ean'));
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('HERSTELLERARTIKELNUMMER',true,$this->model->getProductSku());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('HERSTELLERNAME',true,$this->model->getProductManufacturer());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('PRODUKTNAME',true,$this->model->getProductName());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('PRODUKTGRUPPE',true,$this->model->getProductFullCategoryPath('>'));
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('PREIS',true,$this->model->getProductPriceInclTax());
        
        $avail = 'unbekannt';
    	if($this->model->getProductIsInStock())
        {
        	$avail= 'sofort lieferbar';
        	
        }
        else if($this->model->productHasData('availability'))
        {
        	$result .= $this->model->getProductOptionalAtribute('availability');
        }
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('LIEFERZEIT',true,$avail); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('PRODUKTURL',true,$this->model->getProductUrl());
        
        $result .="<BILDLINKS>";
        $imgs = $this->model->getProductImageUrlArray();
        $i = 1;
        foreach($imgs as $img)
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('BILDURL'.$i,true,$img);
        	$i++;
        }
        $result .="</BILDLINKS>";
        
        $result .="<VERSANDKOSTEN>";
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('VORKASSE',true,0); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('NACHNAHME',true,0);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('KREDITKARTE',true,0);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('PAYPAL',true,0);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('POSTENTGELT_NACHNAHME',true,0);
        $result .="</VERSANDKOSTEN>";
        
        
  
        $result .="</ARTIKEL>";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	  
}
?>