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

class Nostress_Nscexport_Model_Config_Source_Feed
{    
    protected $_options;

    public function toOptionArray()
    {
    	$countryCollection = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
    	$engineCollection = Nostress_Nscexport_Helper_Data::getEngineCollection();
    	$countryCollection[] = array('value'=> 'OTHERS' ,'label' =>  Mage::helper('nscexport')->__("International"));
    	
    	$engineOptionArray = array();
    	foreach($engineCollection as $code => $engine)
		{
    		if(isset($engine['country']) && $engine['country'] != "")
    			$countryCode = $engine['country'];
    		else
    			$countryCode = Nostress_Nscexport_Helper_Data::mapWebSuffixToCountryCode($engine['suffix']);
    		$engineOptionArray[$countryCode][] =  array('value'=>$code, 'label'=> $engine['title']); 
		}
		
		$resultArray = array();
		foreach($countryCollection as $country)
		{
			$countryCode = $country['value'];
			if(isset($engineOptionArray[$countryCode]))
			{
				$country['value'] = $engineOptionArray[$countryCode];
				$resultArray[] = $country;
			}
		}
        return $resultArray;
    }
}
?>