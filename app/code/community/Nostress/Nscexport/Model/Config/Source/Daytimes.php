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

class Nostress_Nscexport_Model_Config_Source_Daytimes
{
	const DEFAULT_TIME = "00:00";
	
    public function toOptionArray()
    {
        $times = array();
        for($i=0;$i<24;$i++) {
            $value = sprintf( "%02d:00", $i);
            $times[] = array( 'value'=>$value, 'label'=>$value);
            $value = sprintf( "%02d:30", $i);
            $times[] = array( 'value'=>$value, 'label'=>$value);
        }
        return $times;
    }
    
    public function getDefaultValue()
    {
    	return self::DEFAULT_TIME;
    }
}
?>