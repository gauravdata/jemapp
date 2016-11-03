<?php

class MT_Email_Block_Adminhtml_System_Config_Form_Button_Delete
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mt/email/system/config/form/button/button.phtml');
    }

    public function getNote()
    {
        return Mage::helper('adminhtml')->__('This action will delete all transactional emails which was created by using MTEditor');
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    public function getAjaxCheckUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/mtemail/deleteAjax/', array(
            'website' => Mage::app()->getRequest()->getParam('website'),
            'store' => Mage::app()->getRequest()->getParam('store')
        ));
    }

    public function getButtonId()
    {
        return 'delete';
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'        => 'mtemail_install',
                'label'     => $this->helper('mtemail')->__('Delete templates'),
                'onclick'   => 'javascript:'.$this->getButtonId().'MtEmailAction(); return false;'
            ));

        return $button->toHtml();
    }
}