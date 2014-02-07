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
 * @package    Nostress_Nscexportacheterfacile
 */

class Nostress_Nscexport_Model_Nscexportacheterfacile extends Mage_Core_Model_Abstract
{		
	private $model;  //Common nscexport model
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportacheterfacile');
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
     
		$result ='<produit>';  	 
		
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('idproduit',true,$this->model->getProductCurrentId(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('reference',true,$this->model->getProductSku(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('ean',true,$this->model->getProductTextAtribute('ean'),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('nomproduit',true,$this->model->getProductName(),true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('categorie',true,$this->model->getProductParentCategoryName(),true);	
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('souscategorie',true,$this->model->getProductCategoryName(), true);
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('prix',false,$this->model->getProductPriceInclTax(),true);					
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('disponibilite',false,$this->model->getProductIsInStock() ? 'En Stock' : 'Rupture de stock',true);     
	
		if($this->model->productHasData('delivery_time'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('delaiexpedition',true,$this->model->getProductOptionalAtribute('delivery_time'),true);
        }
        else if($this->model->productHasData('delivery_date'))
        {
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('delaiexpedition',true,$this->model->getProductOptionalAtribute('delivery_date'),true);
        }	    
        else
        {          
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('delaiexpedition',true,'48 h',true);
        }
		
        if($this->model->productHasData('shipping_cost'))
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('fraisdeport',true,$this->model->getProductOptionalAtribute('shipping_cost'),true);
        else 
        	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('fraisdeport',true,0,true);  
        
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('urlficheproduit',true,$this->model->getProductUrl(),TRUE);
      	$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('urlimage',true,$this->model->getProductImageUrl(),true);      
	    $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('marque',true,$this->model->getProductManufacturer(),true);        
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('description',true,$this->model->getProductLongDescription(),true);
                
        $result .="</produit>";
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
	}
}
?>