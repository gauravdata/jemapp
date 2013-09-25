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

class Nostress_Nscexport_Model_Nscexportcherchons extends Mage_Core_Model_Abstract
{		
	private $model;  //Common nscexport model
	private $productPlace;  //product nscexport sequence number
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportcherchons');
    }
  
	public function generateXml($nscexportId)
	{
		global $productPlace;  //product nscexport sequence number		
	  		  	
		$productPlace = 0;  //product nscexport sequence number
	  	$encoding = "UTF-8";//encoding of xml file	
		$mainTagName = 'produits';
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
	public function addProductAttributes() {

		$result ='<produit>';   
	 	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('titre',true,$this->model->getProductName(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('description',true,$this->model->getProductDescription(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('prix',false,$this->model->getProductPriceInclTax());
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute ( 'prixBarre', false, $this->model->getProductOriginalPriceInclTax () );
		
		$ekoTax = 0;
		if($this->model->productHasData('eko_tax'))
        {
        	$ekoTax = $this->model->getProductOptionalAtribute('eco_tax');        	
        }
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ecoTaxe',true,$ekoTax);
        
        $shippingCost = 0;
        if($this->model->productHasData('shipping_cost'))
        	$shippingCost = $this->model->getProductOptionalAtribute('shipping_cost');        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('fraisLivraison',true,$this->model->formatPrice($shippingCost));
         
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('url',true,$this->model->getProductUrl(),true);
      	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('image',true,$this->model->getProductImageUrl(),true);
         
      	$stock = 'En Stock';
		if($this->model->getProductIsInStock())
        {
        	$stock = 'Rupture de stock'; 
        }
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('stock',true,$stock,true);    	        			
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('categorie',true,$this->model->getProductFullCategoryPath(' / '),true);
		
		if($this->model->productHasData('size'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('taille',true,$this->model->getProductTextAtribute('size'),true);
        }
		if($this->model->productHasData('color'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('couleur',true,$this->model->getProductOptionalAtribute('color'),true);
        }
		if($this->model->productHasData('material'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('matiere',true,$this->model->getProductTextAtribute('material'),true);
        }
		if($this->model->productHasData('delivery_time'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('delaisLivraison',true,$this->model->getProductOptionalAtribute('delivery_time'),true);
        }
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('poids',true,$this->model->getProductWeight(),true);
		if($this->model->productHasData('warranty'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('garantie',true,$this->model->getProductOptionalAtribute('warranty'),true);
        }
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('marque',true,$this->model->getProductManufacturer(),true);
		        
        $result .="</produit>";
        
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