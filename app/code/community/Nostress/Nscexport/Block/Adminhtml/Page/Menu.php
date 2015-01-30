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
 * Adminhtml menu bloc
 *  
 * @category Nostress 
 * @package Nostress_Nscexport
 * 
 */

class Nostress_Nscexport_Block_Adminhtml_Page_Menu extends Mage_Adminhtml_Block_Page_Menu
{
    /**
     * Get menu level HTML code
     *
     * @param array $menu
     * @param int $level
     * @return string
     */
    public function getMenuLevel($menu, $level = 0)
    {
    	$html = parent::getMenuLevel($menu, $level);
    	if($level == 0)
    	{	
    		$skinUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."/adminhtml/default/default/";
    		$replace = "<div style='float:left'><img style='vertical-align:middle' id='koongo_icon' src='' alt='' border='0' /></div>&nbsp;<script>$('koongo_icon').src = '".$skinUrl."' + 'images/nscexport/koongo.ico';</script>Koongo";
    		$html = str_replace("Koongo", $replace, $html);
    	}
    	return $html;
    }
}
