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
 * Nscexport leguide model 
 *
 * @category   Nostress
 * @package    Nostress_NscexportLeguide 
 */

class Nostress_Nscexport_Model_Nscexportleguide extends Mage_Core_Model_Abstract
{		
	private $model;  //Common nscexport model
	private $productPlace;  //product nscexport sequence number
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportleguide');
    }
  
	public function generateXml($nscexportId)
	{
		global $productPlace;  //product nscexport sequence number		
	  		  	
		$productPlace = 0;  //product nscexport sequence number
	  	$encoding = "ISO-8859-1";//encoding of xml file	
		$mainTagName = 'catalogue';
		$xmlHead = '<?xml version="1.0" encoding="'.$encoding.'"?><'.$mainTagName.' lang="FR" date="'.now().'" GMT= "+1">';
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
	public function addProductAttributes() {
		global $productPlace;
		$productPlace ++;
		$result = '';
		//$result ='<product place="'.$productPlace.'">';       
		$result ='<product>';   
	 
		
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('categorie',true,$this->model->getProductFullCategoryPath(' / '),true);	
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('identifiant_unique',true,$this->model->getProductSku(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('titre',true,$this->model->getProductName(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('description',true,$this->model->getProductDescription(),true);
		$currency = $this->model->getProductCurrency ();
		
		// Output price :
		// (actual_price) where (actual_price) is CURRENT product price (its final price...)
		// Warning, 'getProductPriceInclTax' takes the final price (maybe discounted)
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('prix',false,$this->model->getProductPriceInclTax());

		// Output depends on if 'productHasSpecialPrice'
		// method has been re-writed in app/code/local/Nostress/Export/Model/Export.php to fit my needs
		if ($this->model->productHasSpecialPrice ()) 
		{	
			// POk, so product has special price, we will outpout an extra field for the the old (original) price :
			// (product_price) where (product_price) is ORIGINAL product price (the 'price' attribute)
			// For this, a methd 'getProductOriginalPrice' has been added in app/code/local/Nostress/Export/Model/Export.php to get product->getPrice()

			$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute ( 'prix_barre', false, $this->model->getProductOriginalPriceInclTax () );
			// Output 1 (1 stands for 'there is a discount on this product')
			$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute ( 'type_promotion', false, 1 );
		} 
		else 
		{ 	
			// Otherwise, product didnt have has special price, output is simply :
			// Output 0 (0 stands for 'nothing special')

			$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute ( 'type_promotion', false, 0 );
		}

      	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('URL_produit',true,$this->model->getProductUrl(),TRUE);
      	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('URL_image',true,$this->model->getProductImageUrl(),true);

		// Cannot Guess shipping cost, simply ommit
		if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('frais_de_port',true,$this->model->getProductOptionalAtribute('shipping_cost'));
        else 
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('frais_de_port',true,0);  

        if($this->model->getProductIsInStock())
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('disponibilite',false,0); 
        }
        else if($this->model->productHasData('availability'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('disponibilite',true,$this->model->getProductOptionalAtribute('availability'));
        }
        else
        {
            $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('disponibilite',false,60);
        }

	    if($this->model->productHasData('delivery_time'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('delai_de_livraison',true,$this->model->getProductOptionalAtribute('delivery_time'));
        }
        else if($this->model->productHasData('delivery_date'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('delai_de_livraison',true,$this->model->getProductOptionalAtribute('delivery_date'));
        }	    
        else
        {          
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('delai_de_livraison',true,'48 heures');
        }
        
    	if($this->model->productHasData('warranty'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('garantie',true,$this->model->getProductOptionalAtribute('warranty'));
        }
        else
        {          
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('garantie',true,1);   //in years
        }
        
    	if($this->model->productHasData('deee'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('D3E',true,$this->model->getProductTextAtribute('deee'));
        }        
        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('marque',true,$this->model->getProductManufacturer(),true);
    	
        if($this->model->productHasData('ean'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ean',true,$this->model->getProductTextAtribute('ean'));
        }
        
        $result .="</product>";
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
	}

	
    /**
     * Return xml string with product attributes
     *
     * @param Mage_Catalog_Model_Product $product     
     * @return string
     */
	/*
    public function addProductAttributes()
    {           
    	global $productPlace;    	
    	$productPlace++;    	     
        $result ='<product place="'.$productPlace.'">';       
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('categorie',true,$this->model->getProductCategoryName(),true); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('identifiant_unique',true,$this->model->getProductSku(),true); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('titre',true,$this->model->getProductName(),true);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('description',true,$this->model->getProductDescription(),true); 
        $currency = $this->model->getProductCurrency();
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('prix',false,$this->model->getProductPriceInclTax(),true,' currency="'.$currency .'"');
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('URL_produit',true,$this->model->getProductUrl(),TRUE);
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('URL_image',true,$this->model->getProductImageUrl(),true);
        
        if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('frais_de_port',true,$this->model->getProductOptionalAtribute('shipping_cost'));
        else 
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('frais_de_port',true,0);  
        
        if($this->model->getProductIsInStock())
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('disponibilite',false,0); 
        }
        else if($this->model->productHasData('availability'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('disponibilite',true,$this->model->getProductOptionalAtribute('availability'));
        }
        else
        {
            $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('disponibilite',false,60);
        }
        	
        if($this->model->productHasData('delivery_date'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('delai_de_livraison',true,$this->model->getProductOptionalAtribute('delivery_date')+' heures');
        }
        else
        {          
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('delai_de_livraison',true,'48 heures');
        }
        
    	if($this->model->productHasData('warranty'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('garantie',true,$this->model->getProductOptionalAtribute('warranty'));
        }
        else
        {          
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('garantie',true,1);   //in years
        }
        
    	if($this->model->productHasData('deee'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('D3E',true,$this->model->getProductTextAtribute('deee'));
        }        
        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('marque',true,$this->model->getProductManufacturer(),true);
    	
        if($this->model->productHasData('ean'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ean',true,$this->model->getProductTextAtribute('ean'));
        }
        
        if($this->model->productHasSpecialPrice())
			$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('prix_barre',false,$this->model->getProductSpecialPriceInclTax());
		
    	if($this->model->productHasData('type_promotion'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('type_promotion',true,$this->model->getProductOptionalAtribute('type_promotion'));
        }
        else
        {          
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('type_promotion',false,1);   
        }     
        $result .="</product>\n";
        
        try
        {
        	$result = Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);	
        }
    	catch (Exception $e) 
    	{
    		$result .= '<ERROR>'.$e->getMessage().'</ERROR>';   			                                
    	}
        return $result;
    }	  */
}
?>