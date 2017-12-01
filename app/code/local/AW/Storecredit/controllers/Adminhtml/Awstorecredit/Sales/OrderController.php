<?php
class AW_Storecredit_Adminhtml_Awstorecredit_Sales_OrderController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/aw_storecredit');
    }

    public function saveStoreCreditAction()
    {
        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        $useStoreCredit = $this->getRequest()->getParam('use_storecredit');
        if ( ! Mage::helper('aw_storecredit')->isModuleOutputEnabled()
            || ! Mage::helper('aw_storecredit/config')->isModuleEnabled()
            || ! $quote
            || ! $quote->getCustomerId()
            || is_null($useStoreCredit)
        ) {
            return;
        }
        if ($useStoreCredit) {
            $storeCredit = Mage::getModel('aw_storecredit/storecredit')->loadByCustomerId($quote->getCustomerId());
            if ($storeCredit) {
                $quote->setStorecreditInstance($storeCredit);
                if (count(Mage::helper('aw_storecredit/totals')->getQuoteStoreCredit($quote->getId())) == 0) {
                    Mage::helper('aw_storecredit/totals')->addStoreCreditToQuote($storeCredit, $quote);
                }
            }
        }
        if ( ! $useStoreCredit
            && count(Mage::helper('aw_storecredit/totals')->getQuoteStoreCredit($quote->getId())) >= 1
        ) {
            $storeCredit = Mage::getModel('aw_storecredit/storecredit')->loadByCustomerId($quote->getCustomerId());
            if ($storeCredit) {
                $quote->setStorecreditInstance(null);
                Mage::helper('aw_storecredit/totals')->removeStoreCreditFromQuote($storeCredit->getEntityId(), $quote);
            }
        }
        return;
    }
}