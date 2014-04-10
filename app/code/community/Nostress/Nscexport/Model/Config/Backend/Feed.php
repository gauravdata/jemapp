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
* Nscexports model
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Model_Config_Backend_Feed extends Mage_Core_Model_Config_Data
{
	const LABEL = 'label';
	const VALUE = 'value';
	
    public function getValue()
    {
    	$feedOptions =  Mage::helper('nscexport/data_feed')->getOptions("link",false,true); 
   		$codes = array();
    	foreach($feedOptions as $feed)
		{
			$codes[] = $feed[self::VALUE];
		}
		return $codes;
    }
    
    protected function _beforeSave()
    {
    	$feedsStatusChanged = Mage::getModel('nscexport/feed')->updateFeedsEnabled(array_values(parent::getValue()));
    	$this->setValue(null);
    	    	
    	foreach ($feedsStatusChanged as $item)
    	{
    		$taxonomyCode = $item->getTaxonomyCode();
    		if(isset($taxonomyCode))
    		{	//reload taxonomy
    			Mage::getModel('nscexport/entity_attribute_taxonomy')->prepareAttributes();
        		$message = Mage::getModel('nscexport/taxonomy')->reloadTaxonomy();
        		Mage::getSingleton('core/session')->addSuccess(Mage::helper('nscexport')->__("Taxonomy successfully reloaded."));        		
        		break;
    		}
    	}    	
    	
		return $this;
    }
}
?>