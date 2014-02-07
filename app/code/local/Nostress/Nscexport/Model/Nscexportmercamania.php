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
 * Nscexport ciao moddel 
 *
 * @category   Nostress
 * @package    Nostress_Nscexport
 */

class Nostress_Nscexport_Model_Nscexportmercamania extends Mage_Core_Model_Abstract
{	
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportmercamania');
    }	  

	public function generateXml($nscexportId)
	{
		$encoding = "UTF-8";//encoding of xml file	
		$mainTagName = 'Offers';
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
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('categoria',true,$this->model->getProductFullCategoryPath('>')); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('referencia_interna',true,$this->model->getProductCurrentId()); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('nombre',true,$this->model->getProductName());
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('precio',false,$this->model->getProductPriceInclTax());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('url',true,$this->model->getProductUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('url_imagen',true,$this->model->getProductImageUrl());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('descripcion',true,$this->model->getProductDescription());
        
        
		// Cannot Guess shipping cost, simply ommit
		if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('gastos_de_envio',true,$this->model->getProductOptionalAtribute('shipping_cost'));
        else 
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('gastos_de_envio',true,0);  
      
        $availability = '60';
    	if($this->model->getProductIsInStock())
        {
        	$availability = 0;        	 
        }
        else if($this->model->productHasData('availability'))
        {
        	$availability = $this->model->getProductOptionalAtribute('availability');        	
        }
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('disponibilidad',true,$availability.' dias');
             
    	if($this->model->productHasData('warranty'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('garantia',true,$this->model->getProductOptionalAtribute('warranty'));
        }
        else
        {          
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('garantia',true,1);   //in years
        }
        
    	if($this->model->productHasData('delivery_time'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('plazo_de_envio',true,$this->model->getProductOptionalAtribute('delivery_time'));
        }
        else if($this->model->productHasData('delivery_date'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('plazo_de_envio',true,$this->model->getProductOptionalAtribute('delivery_date'));
        }	    
        else
        {          
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('plazo_de_envio',true,'24-48 horas');
        }
        
    	if($this->model->productHasData('ean'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ean',true,$this->model->getProductTextAtribute('ean'));
        }
        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('marca',true,$this->model->getProductManufacturer());
        //referencia_modelo
                
    	// Output depends on if 'productHasSpecialPrice'
		if ($this->model->productHasSpecialPrice ()) 
		{	
			// POk, so product has special price, we will outpout an extra field for the the old (original) price :
			// (product_price) where (product_price) is ORIGINAL product price (the 'price' attribute)
			// For this, a methd 'getProductOriginalPrice' has been added in app/code/local/Nostress/Export/Model/Export.php to get product->getPrice()

			$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute ( 'precio_tachado', false, $this->model->getProductOriginalPriceInclTax () );
			// Output 1 (1 stands for 'there is a discount on this product')
			$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute ( 'tipo_promocion', false, 1 );
		} 
		else 
		{ 	
			// Otherwise, product didnt have has special price, output is simply :
			// Output 0 (0 stands for 'nothing special')

			$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute ( 'tipo_promocion', false, 0 );
		}	      
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('divisa',true,$this->model->getProductCurrency());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ocasion',true,$this->model->getProductIsNew() ? 1 : 0);
                     
        $result .="</product>";

        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
    }  
}
?>