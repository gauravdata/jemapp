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
 * Nscexport Fruugo model 
 *
 * @category   Nostress
 * @package    Nostress_NscexportFruugo
 */

class Nostress_Nscexport_Model_Nscexportfruugo extends Mage_Core_Model_Abstract
{		
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportfruugo');
    }
  	  
	public function generateXml($nscexportId)
	{
	  	$encoding = "utf-8";//encoding of xml file	
		$mainTagName = 'Products';
		$xmlHead = '<?xml version="1.0" encoding="'.$encoding.'"?><merchantProductdata><merchantProducts>';
		$xmlTail = '</merchantProducts></merchantProductdata>';
		
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
 
    /** determine if product is simple or configurable 
     *  if configurable then it should give the same fields as when it is a simple product only the name and the price should be taken from the simple product the rest                 
     *  of the fields should be from the configurable product. 
     */
    
    /* if ($this->model->getProductType()=="configurable"){

        $_attributes = Mage::helper('core')->decorateArray($this->getAllowAttributes());
        $result ="<merchantProduct>"; 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('type',false,$this->model->getProductType());
        $result .= $_product;
        $result .="</merchantProduct>\n";
        }
*/

     // else {
        $result ="<merchantProduct>";
                
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('merchantProductId',false,$this->model->getProductSku());    
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('manufacturer',true,$this->model->getProductManufacturer());     
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('brand',true,$this->model->getProductManufacturer());     
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('blocked',false,0);

        $result .="<mediaGalleries>";
        $result .="<mediaGallery>";
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('language',false,$this->model->getStoreLanguage()); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('mediaType',false,'STANDARD_IMAGE');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('mediaName',true,$this->model->getProductName()); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('mediaLink',true,$this->model->getProductImageUrl());     
        $result .="</mediaGallery>";
        $result .="</mediaGalleries>";

        $result .="<descriptions>";
        $result .="<description>";
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('language',false,$this->model->getStoreLanguage());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('productTitle',true,$this->model->getProductName()); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('shortDescription',true,$this->model->getProductDescription($removeEndOfLine = true));   
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('longDescription',true,$this->model->getProductLongDescription($removeEndOfLine = true));   
        $result .="</description>";
        $result .="</descriptions>";

        $result .="<categories>";
        $result .="<category>";
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('categoryValue',true,$this->model->getProductCategoryName());
        //$result .="<categoryValue>39111500</categoryValue>";  
        $result .="</category>";
        $result .="</categories>";
        if($this->model->productHasData('ean') || $this->model->productHasData('isbn'))
        {
        	$result .="<productCodes>";
            $result .="<productCode>";
        	if($this->model->productHasData('ean'))
        	{
        		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('type',false,'EAN');
        		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('value',true,$this->model->getProductTextAtribute('ean'));
        	}
        	if($this->model->productHasData('isbn'))
        	{
        		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('type',false,'ISBN');
        		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('value',true,$this->model->getProductTextAtribute('isbn'));
        	}
        	$result .="</productCode>";
        	$result .="</productCodes>";
        }
        $result .="<skus>";
        $result .="<sku>";
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('merchantSkuId',false,$this->model->getProductSku());  

        $result .="<supply>";
        if ($this->model->getProductIsInStock())
                $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('stockStatus',false,'INSTOCK');
        else
                $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('stockStatus',false,'BACKORDERED');
        
        if($this->model->productHasData('availability'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('stockintoStockDays',true,$this->model->getProductOptionalAtribute('availability'));
        }
        else
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('stockintoStockDays',false,28);      
                
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('stockValue',true,$this->model->getProductQuantity());
        $result .="</supply>";

        $result .="<prices>";
        $result .="<price>";
        $priceType = 'NORMAL';
        if($this->model->productHasSpecialPrice())
        	$priceType = 'DISCOUNT';
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('priceType',false,$priceType);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('value',false,$this->model->getProductPriceExclTax()); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('currency',false,$this->model->getProductCurrency()); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('country',false,$this->model->getStoreCountry());  
        $result .="</price>";
        $result .="</prices>";

        $result .="</sku>";
        $result .="</skus>";

        $result .="<attributes>";
        
        if($this->model->productHasData('color'))
        {
        	$result .="<attribute>";
            $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('language',false,$this->model->getStoreLanguage());
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('attributeName',false,'colour');
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('attributeValue',true,$this->model->getProductOptionalAtribute('color')); 
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('attributeType',true,'COLOR'); 
        	$result .="</attribute>";
      	}
      	if($this->model->productHasData('weight'))
        {
        	$result .="<attribute>";
            $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('language',false,$this->model->getStoreLanguage());
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('attributeName',false,'weight');
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('attributeValue',true,$this->model->getProductWeight()); 
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('attributeType',false,'WEIGHT'); 
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('attributeUnit',false,'kg');
        	$result .="</attribute>";
      	}
        $result .="</attributes>";
        $result .="</merchantProduct>\n";
      // }

      return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	  
}
?>
