<?php
class AW_Storecredit_Block_Frontend_Header_Link extends Mage_Core_Block_Template
{
    public function addStorecreditLink()
    {
        if (!Mage::helper('aw_storecredit/config')->isModuleEnabled()) {
            return $this;
        }

        if (!Mage::helper('aw_storecredit/config')->isDisplayInToplinks()) {
            return $this;
        }

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer || !$customer->getId()) {
            return $this;
        }

        $parentBlock = $this->getParentBlock();
        if (!$parentBlock) {
            return $this;
        }
        if (Mage::helper('aw_storecredit/config')->isDisplayBalanceInToplinks()) {
            $storeCreditModel = Mage::getModel('aw_storecredit/storecredit')->loadByCustomerId($customer->getId());
            $currentBalance = Mage::helper('core')->currency($storeCreditModel->getBalance(), true, false);
            $label = $this->__('Store Credit (%s)', $currentBalance);
        } else {
            $label = $this->__('Store Credit');
        }
        $parentBlock->addLink($label, 'awstorecredit/storecredit/index/', $label, true, array('_secure' => Mage::app()->getStore(true)->isCurrentlySecure()), 25, null, 'class="top-link-aw-storecredit"');

        return $this;
    }
}