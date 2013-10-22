<?php
/**
 * Block used for extracting plugin version on config page
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Block_Config_Version extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		//return version of plugin
		return Mage::getConfig()->getModuleConfig("Comaxx_Docdata")->version;
	}
}
