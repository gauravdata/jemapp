<?php

class WSC_MageJam_Block_System_Config_Form_Version extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Used for showing magejam version in System -> Config -> Magejam
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        /* @var $helper WSC_MageJam_Helper_Data */
        $helper = Mage::helper('magejam');
        return $helper->getMagejamVersion();
    }
}