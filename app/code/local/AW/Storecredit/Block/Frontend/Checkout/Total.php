<?php
class AW_Storecredit_Block_Frontend_Checkout_Total extends Mage_Checkout_Block_Total_Default
{
    protected $_template = 'aw_storecredit/checkout/total.phtml';
    protected $_storecredit = null;

    public function getAwStorecredit()
    {
        if (null === $this->_storecredit) {
            $this->_storecredit = $this->getTotal()->getAwStorecredit();
            if (null === $this->_storecredit) {
                $this->_storecredit = Mage::helper('aw_storecredit/totals')->getQuoteStoreCredit();
            }
        }
        return $this->_storecredit;
    }
}