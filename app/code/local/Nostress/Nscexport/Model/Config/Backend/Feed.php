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
    public function getValue()
    {
    	if(($value = parent::getValue()) == 'all')
    	{
    		$engineCollection = Nostress_Nscexport_Helper_Data::getEngineCollection();
    	    	
    		$engineOptionArray = array();
    		foreach($engineCollection as $code => $engine)
				{
				if($engine['enabled'] == 1)
    				$engineOptionArray[] =  $code; 
			}
			return $engineOptionArray;
    	}
		else
			return $value;
    }
    
    protected function _beforeSave()
    {
        $value = $this->getValue();
        $engineCollection = Nostress_Nscexport_Helper_Data::getEngineCollection();
        $valueSize = count($value);
        $colSize = count($engineCollection);
    	if($valueSize == $colSize)
    	{
    		$this->setValue(array('all'));
    	}
		return $this;
    }
}
?>