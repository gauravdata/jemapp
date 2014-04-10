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
* @copyright Copyright (c) 2012 NoStress Commerce (http://www.nostresscommerce.cz) 
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
	const LABEL = 'label';
	const VALUE = 'value';
	const COUNTRY = 'country';
	
	protected $_options;
	
	public function toOptionArray() 
	{		
		$feedOptions =  Mage::helper('nscexport/data_feed')->getOptions("link",false,null,true);
		return $feedOptions;
	}
}
?>