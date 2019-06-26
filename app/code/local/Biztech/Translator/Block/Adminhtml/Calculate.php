<?php

class Biztech_Translator_Block_Adminhtml_Calculate extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->_getAddRowButtonHtml($this->__('Calculate Characters'));
    }

    protected function _getAddRowButtonHtml($title)
    {

        $buttonBlock = $this->getElement()->getForm()->getParent()->getLayout()->createBlock('adminhtml/widget_button');

        $params = array(
            'website' => $buttonBlock->getRequest()->getParam('website')
        );

        $url = Mage::helper('adminhtml')->getUrl("adminhtml/translator/calculate", $params);


        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setLabel($this->__($title))
            ->setOnClick("calcchar('" . $url . "')")
            ->toHtml();
    }
}