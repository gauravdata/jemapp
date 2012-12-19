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
 * @package    Nostress_Nscexportbolha
 */

class Nostress_Nscexport_Model_Nscexportbolha extends Mage_Core_Model_Abstract
{		
	private $model;  //Common nscexport model
	private $productPlace;  //product nscexport sequence number
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('nscexport/nscexportbolha');
    }
  
	public function generateXml($nscexportId)
	{
	  	$encoding = "UTF-8";//encoding of xml file	
		$mainTagName = 'trgovina';
		$merchantName = Mage::helper('nscexport')-> getMerchantName('bolha'); 		
		
		$xmlHead = '<?xml version="1.0" encoding="'.$encoding.'"?><'.$mainTagName.' ids="'.$merchantName.'">';
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
		$result ='<izdelek>';   	 
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('izdelekID',false,$this->model->getProductCurrentId());
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('izdelekIme',true,$this->model->getProductName(),true);    
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('url',true,$this->model->getProductUrl(),true); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttributeAdvanced('opis',true,$this->model->getProductLongDescription(),true);  
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('slika',true,$this->model->getProductImageUrl());
		$result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('cena',false,$this->model->getProductPriceInclTax()); 
        $result .= Nostress_Nscexport_Helper_Data::formatProductAttribute('kategorijaID',true,$this->model->getProductCategoryId()); 
        $result .="</izdelek>";
        
        return Nostress_Nscexport_Helper_Data::changeEncoding($this->model->getNscexportEncoding(),$result);
	}
}
?>