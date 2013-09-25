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
* @category Nostress 
* @package Nostress_Nscexport
* 
*/

class Nostress_Nscexport_Block_Adminhtml_Widget_Grid_Column_Renderer_Starting extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function _getValue(Varien_Object $row)
    {
		$starting = Mage::helper('nscexport')->getStartingIn($row);

		if($starting->getPrefix() == '-') {
			$style = 'color:red; font-weight:bold;';
		}
		else {
			$style = '';
		}

		$return = '<span style="'.$style.'">'.$starting->getPrefix().' '.$starting->getHours().'h '.$starting->getMinutes().'m '.$starting->getSeconds().'s </span>';

		return $return;
    }
}