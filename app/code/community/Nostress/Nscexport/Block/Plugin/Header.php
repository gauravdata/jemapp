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
class Nostress_Nscexport_Block_Plugin_Header extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	public function render(Varien_Data_Form_Element_Abstract $element)
    {    
    	$label = $this->__("Update Plugin List");
    	$items = Mage::getModel('nscexport/plugin')->getCollection()->load()->getItems();
    	if(empty($items))
    		$label = $this->__("Get Plugin List");
    	$link = "adminhtml/nscexport_action/reloadpluginlist";
    	$buttonHtml = $this->getButtonHtml($label,$link);
    	
    	$html = "<span style='color:black;font-weight:bold;font-size:16px'>{$this->__("Plugin list")} - ";
    	$html .= "<a target='_blank' href='{$this->helper('nscexport')->getHelpUrl(Nostress_Nscexport_Helper_Version::PLUGIN_LIST_LINK)}'>{$this->__("Order in Koongo Store")}</a></span>";
    	$html .= "
		<table cellspacing=\"15\">
			<tr>
				<th>{$buttonHtml}</th>
			</tr>
			<tr>
				<th>{$this->__('Plugin Name')}</th>
				<th>{$this->__('Description')}</th>
				<th>{$this->__('Installed Version')}</th>
				<th>{$this->__('Latest Version')}</th>
				<th>{$this->__('Compatible with Connector version')}</th>							
				<th>{$this->__('Status')}</th>				    		
			</tr>";
    	return $html;    	    	    	
    }
    
    public function getButtonHtml($label, $link)
    {
    	$buttonBlock = $this->getLayout()->createBlock('adminhtml/widget_button')
    	->setData(array(
    			'label'     => $label,
    			'type'    => 'button',
    			'onclick'   => "setLocation('".$this->getUrl($link)."');",
    			'class'   => 'reload'
    	));
    	return $buttonBlock->toHtml();
    }
}