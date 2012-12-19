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
 * Nscexport beslist model 
 *
 * @category   Nostress
 * @package    Nostress_Nscexport 
 */

class Nostress_Nscexport_Model_Nscexportsuperdeal extends Mage_Core_Model_Abstract
{
	private $model;  //Common export model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportsuperdeal');
    }
	  
	public function generateXml($nscexportId)
	{ 
	  	$encoding = "utf-8";//encoding of xml file	
		$mainTagName = 'shop';
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
        $result ="<item>"; 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('internal_id',true,$this->model->getProductCurrentId());       
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('name',true,$this->model->getProductName(),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('description',true,$this->model->getProductDescription(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('manufacturer',true,$this->model->getProductManufacturer());
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('price_eur',false,$this->model->getProductPriceInclTax());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('product_url',true,$this->model->getProductUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('product_img',true,$this->model->getProductImageUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ean',true,$this->model->getProductTextAtribute('ean'));
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('part_no',true,$this->model->getProductSku());
    	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('category',true,$this->model->getProductFullCategoryPath('|'));
        if($this->model->getProductIsInStock())
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('available',true,1); 
        }
        else if($this->model->productHasData('availability'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('available',true,$this->model->getProductOptionalAtribute('availability'));
        }
        else
        {
            $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('available',false,0);
        }
        if ($this->model->productHasSpecialPrice ()) 
		{
			$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('discount',false,1); 
		}
		else 
		{
			$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('discount',false,0);
		}
		      	        
        $result .="</item>";                          
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	
    	  
}
?>