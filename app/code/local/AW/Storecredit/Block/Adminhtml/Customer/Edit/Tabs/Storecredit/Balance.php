<?php

class AW_Storecredit_Block_Adminhtml_Customer_Edit_Tabs_Storecredit_Balance extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setData('customer', Mage::registry('current_customer'));
        $this->setData('website', $this->getCustomer()->getStore()->getWebsite());
    }

    public function getCurrentBalance()
    {
        $balance = 0;

        $customerId = $this->getCustomer()->getId();
        $customerStorecredit = Mage::getModel('aw_storecredit/storecredit')->loadByCustomerId($customerId);
        if ($customerStorecredit) {
            $balance = Mage::helper('core')->currency($customerStorecredit->getBalance(), true, false);
        }

        return $balance;
    }

    public function isCustomerSubscribed()
    {
        $customerId = $this->getCustomer()->getId();
        $customerStorecredit = Mage::getModel('aw_storecredit/storecredit')->loadByCustomerId($customerId);
        if (!$customerStorecredit->getId()) {
            return false;
        }

        $result = false;
        $subscribeState = $customerStorecredit->getSubscribeState();
        if ($subscribeState == AW_Storecredit_Model_Source_Storecredit_Subscribe_State::SUBSCRIBED_VALUE) {
            $result = true;
        }
        return $result;
    }

    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('aw_storecredit_');

        $fieldset = $form->addFieldset(
            'notification_fieldset', array('legend' => Mage::helper('aw_storecredit')->__('Store Credit Balance'))
        );

        $fieldset->addField(
            'current_balance',
            'label',
            array(
                'label'   => Mage::helper('aw_storecredit')->__('Current Balance: '),
                'bold'    => 'true',
                'value'   => $this->getCurrentBalance(),
            )
        );

        $fieldset->addField(
            'balance_update_notification',
            'checkbox',
            array(
                 'label'   => Mage::helper('aw_storecredit')->__('Subscribed to Balance Update?'),
                 'name'    => 'balance_update_notification',
                 'id'      => 'balance_update_notification',
                 'value'   => 1,
                 'checked' => $this->isCustomerSubscribed(),
            )
        );

        $this->setForm($form);
        return $this;
    }
}