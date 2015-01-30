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
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@nostresscommerce.cz so we can send you a copy immediately.
 * 
 * @copyright  Copyright (c) 2010 NoStress Commerce (http://www.nostresscommerce.cz)
 *
 */
/**
 * 
 * @category   Nostress
 * @package    Nostress_Nscexport
 * @author     NoStress Commerce Team <info@nostresscommerce.cz>
 */
class Nostress_Nscexport_Block_Version_Server extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	$serverId= Mage::helper(str_replace("nostress_","",strtolower($this->getModuleName())).'/version')->getServerId();
        $element->setValue($serverId);
        $element->setBold(true);
        return "<span style='color:blue;font-weight:bold;font-size:16px'>{$serverId}</span>";        
    }   

     /**
     * Enter description here...
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
    	$label = $element->getLabel();
    	$label = "<span style='font-weight:bold;font-size:16px'>{$label}</span>";
    	$element->setLabel($label);

    	return parent::render($element);
    }
}