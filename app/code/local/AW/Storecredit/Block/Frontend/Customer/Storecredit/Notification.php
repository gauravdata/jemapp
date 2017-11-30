<?php

class AW_Storecredit_Block_Frontend_Customer_Storecredit_Notification extends Mage_Core_Block_Template
{
    public function getIsSubscribed()
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $storeCredit = Mage::getModel('aw_storecredit/storecredit')->loadByCustomerId($customerId);
        if ($storeCredit->getId()
            && $storeCredit->getSubscribeState() == AW_Storecredit_Model_Source_Storecredit_Subscribe_State::SUBSCRIBED_VALUE
        ) {
            return true;
        }
        return false;
    }

    public function getAction()
    {
        return Mage::getUrl('awstorecredit/storecredit/subscribe');
    }


}