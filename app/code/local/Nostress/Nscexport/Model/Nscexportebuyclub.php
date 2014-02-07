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
 * @package    Nostress_Nscexportebuyclub
 */

class Nostress_Nscexport_Model_Nscexportebuyclub extends Mage_Core_Model_Abstract
{		
	private $model;  //Common nscexport model
	private $productPlace;  //product nscexport sequence number
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportebuyclub');
    }
  
	public function generateXml($nscexportId)
	{
	  	$encoding = "ISO-8859-1";//encoding of xml file	
		$mainTagName = 'catalogue';
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
     
		$result ='<product>';  	 
		
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('categorie',true,$this->model->getProductFullCategoryPath(' / '),true);	
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('identifiant_unique',true,$this->model->getProductCurrentId(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('titre',true,$this->model->getProductName(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('prix',false,$this->model->getProductPriceInclTax());
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('URL_produit',true,$this->model->getProductUrl(),TRUE);
      	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('URL_image',true,$this->model->getProductImageUrl(),true);      	
      	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('occasion',false,$this->model->getProductIsNew() ? 0 : 1);      	
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('description',true,$this->model->getProductDescription(),true);
		$currency = $this->model->getProductCurrency ();
		
		//reference_modele
		
		if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('livraison',true,$this->model->getProductOptionalAtribute('shipping_cost'));
        else 
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('livraison',true,0);  

		
        	
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
	    $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('marque',true,$this->model->getProductManufacturer(),true);
    	
        if($this->model->productHasData('ean'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('ean',true,$this->model->getProductTextAtribute('ean'));
        }
		
	    if($this->model->productHasData('warranty'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('garantie',true,$this->model->getProductOptionalAtribute('warranty'));
        }
        else
        {          
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('garantie',true,2);   //in years
        }
        		
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute ( 'prix_barre', false, $this->model->getProductOriginalPriceInclTax () );
        
        $result .="</product>";
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
	}
}
?>