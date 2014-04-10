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
* Exports model - source for dropdown menu "Product group size"
* 
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Model_Config_Source_Dayofweek
{        
	const MONDAY = 1;
	const TUESDAY = 2;
	const WEDNESDAY = 3;
	const THURSDAY = 4;
	const FRIDAY = 5;
	const SATURDAY = 6;
	const SUNDAY = 7;
	
    public function toOptionArray()
    {
        return array(
            array('value' => self::MONDAY, 'label' => Mage::helper('nscexport')->__('Monday')),
            array('value' => self::TUESDAY, 'label' => Mage::helper('nscexport')->__('Tuesday')),
            array('value' => self::WEDNESDAY, 'label' => Mage::helper('nscexport')->__('Wednesday')),
            array('value' => self::THURSDAY, 'label' => Mage::helper('nscexport')->__('Thursday')),
            array('value' => self::FRIDAY, 'label' => Mage::helper('nscexport')->__('Friday')),
            array('value' => self::SATURDAY, 'label' => Mage::helper('nscexport')->__('Saturday')),
            array('value' => self::SUNDAY, 'label' => Mage::helper('nscexport')->__('Sunday'))                             
        );
    }
    
    public function getAllValues()
    {
    	$options = $this->toOptionArray();
    	$result = array();
    	foreach ($options as $option)
    	{
    		$result[] = $option["value"];
    	}
    	return $result;
    }
}
?>