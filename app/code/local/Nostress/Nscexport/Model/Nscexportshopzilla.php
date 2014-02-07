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
 * @package    Nostress_Nscexportshopzilla 
 */

class Nostress_Nscexport_Model_Nscexportshopzilla extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportshopzilla');
    }

	public function generateXml($nscexportId)
	{
	  	$encoding = "UTF-8";//encoding of xml file	
	  	
		$xmlHead = "Category|Manufacturer|Title|Product Description|Link|Image|SKU|Stock|Condition|Shipping Weight|Shipping Cost|Bid|Promotional Description|EAN / UPC|Price\n";
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
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductCategoryName());      //category
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductManufacturer());        //manufacturer        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductName());   //name
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductDescription(true));      //description 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductUrl());    //product url 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductImageUrl());    //product image    
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductSku());     //sku
        $disponible = 'Out of Stock';
        if($this->model->getProductIsInStock())
        	$disponible = 'In Stock';  
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$disponible);       //in_stock
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,'');     //condition
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,$this->model->getProductWeight());     //Weight 
        $shipping = 0;
        if($this->model->productHasData('shipping_cost'))
        	$shipping = $this->model->getProductOptionalAtribute('shipping_cost');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$shipping);       //shipping_cost
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,'');  //bid
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,'');  //Promotional Description
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(false,$this->model->getProductTextAtribute('ean'));//ean        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributePipe(true,$this->model->getProductPriceInclTax());     //price 
        $result .="\n";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }	  
}
?>