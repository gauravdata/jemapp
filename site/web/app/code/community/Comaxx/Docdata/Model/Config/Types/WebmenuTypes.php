<?php
/**
 * Source model for the web menu types
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Model_Config_Types_WebmenuTypes
{
	public function toOptionArray()
	{
		return array(
			// 1 to enable the menu (this value is used for the active state)
			array('value' => '1', 'label' => Mage::helper('docdata')->__('Show only the Docdata Webmenu option')),
			// 0 to disable the webmenu / attempt to go directly to the payment method
			array('value' => '0', 'label' => Mage::helper('docdata')->__('Go directly to a selected payment method in the Docdata Webmenu')),
		);
	}
}