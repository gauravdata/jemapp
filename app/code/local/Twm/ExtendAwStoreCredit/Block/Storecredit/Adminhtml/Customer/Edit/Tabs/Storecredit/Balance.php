<?php
/**
 * Created by PhpStorm.
 * User: mathijs
 * Date: 11-12-17
 * Time: 15:58
 */ 
class Twm_ExtendAwStoreCredit_Block_Storecredit_Adminhtml_Customer_Edit_Tabs_Storecredit_Balance extends AW_Storecredit_Block_Adminhtml_Customer_Edit_Tabs_Storecredit_Balance
{
    public function isCustomerSubscribed()
    {
        $customerId = $this->getCustomer()->getId();
        $customerStorecredit = Mage::getModel('aw_storecredit/storecredit')->loadByCustomerId($customerId);
        if (!$customerStorecredit->getId()) {
            return Mage::helper('aw_storecredit/config')->isAutoSubscribedCustomers();
        }

        $result = false;
        $subscribeState = $customerStorecredit->getSubscribeState();
        if ($subscribeState == AW_Storecredit_Model_Source_Storecredit_Subscribe_State::SUBSCRIBED_VALUE) {
            $result = true;
        }
        return $result;
    }
}