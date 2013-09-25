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
 * Nscexport keldelice model
 *
 * @category   Nostress
 * @package    Nostress_NscexportKeldelice
 */

class Nostress_Nscexport_Model_Nscexportkeldelice extends Mage_Core_Model_Abstract
{
	private $model;  //Common nscexport model
	
	public function _construct()
	{
		parent::_construct();
		$this->_init('nscexport/nscexportkeldelice');
	}

	public function generateXml($nscexportId)
	{	  	
	  	$encoding = "iso-8859-1";//encoding of xml file	
		$mainTagName = 'catalog';
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
		$result ="<product>";		
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('title',true,$this->model->getProductName(),true); 
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('description',true,$this->model->getProductDescription(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('category',true,$this->model->getProductCategoryName());		
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('price',false,$this->model->getProductPriceExclTax()); 
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('product_url',true,$this->model->getProductUrl(),true); 		
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('image_url_1',true,$this->model->getProductImageUrl(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('image_url_2',true,"",false);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('image_url_3',true,"",false);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('image_url_4',true,"",false);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('image_url_5',true,"",false);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('sku',false,$this->model->getProductSku());
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('manufacturer',true,$this->model->getProductManufacturer(),true);
		$result .= "<ean13/>";
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('weight',false,$this->model->getProductWeight());
		
		if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('shipping_cost',true,$this->model->getProductOptionalAtribute('shipping_cost'));
        else 
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('shipping_cost',true,0);  
			
		$result .="</product>\n";

		return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
	}
}
?>