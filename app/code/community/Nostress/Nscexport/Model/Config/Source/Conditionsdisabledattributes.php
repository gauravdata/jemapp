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
* @copyright Copyright (c) 2013 NoStress Commerce (http://www.nostresscommerce.cz)
*
*/

/**
* Exports model - condtions disabled attributes
*
* @category Nostress
* @package Nostress_Nscexport
*
*/

class Nostress_Nscexport_Model_Config_Source_Conditionsdisabledattributes
{
	const LABEL = 'label';
	const VALUE = 'value';
	
	protected $_options;
	
	public function toOptionArray() {
	    
	    if( !$this->_options) {
    	    $defaultStoreId = current(array_keys( Mage::app()->getStores()));
    
    	    $attributes = array();
            $attributesValues = Mage::helper('nscexport/data_feed')->getAttributeOptions( $defaultStoreId);
            unset( $attributesValues[0]);
            $this->_options = $attributesValues;
	    }
	    
		return $this->_options;
	}
}
?>