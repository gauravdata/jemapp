<?php
class AW_Storecredit_Block_Adminhtml_Sales_Order_Creditmemo_Refund extends Mage_Core_Block_Template
{

    public function getRefundValue()
    {
        return round($this->getCreditmemo()->getBaseStoreCreditRefundValue(), 2);
    }

    public function isAutoRefund()
    {
        return Mage::helper('aw_storecredit/config')->isAutomaticallyStoreCreditRefund();
    }

    public function isCanShow()
    {
        return Mage::helper('aw_storecredit/config')->isModuleEnabled()
        && Mage::helper('aw_storecredit')->isModuleOutputEnabled()
        && $this->isCustomerExist()
        && !$this->isRefundOnline();
    }

    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }

    public function isCustomerExist()
    {
        $creditmemo = $this->getCreditmemo();
        if (!$creditmemo && !$creditmemo->getCustomerId()) {
            return false;
        }
        $customer = Mage::getModel('customer/customer')->load($creditmemo->getCustomerId());
        if (!$customer || !$customer->getId()) {
            return false;
        }
        return true;
    }

    public function isRefundOnline()
    {
        $params = Mage::app()->getRequest()->getParams();
        if (array_key_exists('invoice_id', $params) && $params['invoice_id']) {
            return true;
        }
        return false;
    }

}