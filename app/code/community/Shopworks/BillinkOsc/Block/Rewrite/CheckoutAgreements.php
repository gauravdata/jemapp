<?php

class Shopworks_BillinkOsc_Block_Rewrite_CheckoutAgreements extends Shopworks_Billink_Block_Rewrite_CheckoutAgreements
{
    /**
     * @var Shopworks_BillinkOsc_Helper_Data
     */
    private $_oscHelper;

    /**
     * Constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_oscHelper = Mage::helper('billink_osc');
    }

    /**
     * Overwrite method to add OSC check
     * @return bool
     */
    protected function isBillinkTermsUsed()
    {
        return $this->_helper->isReadyToUse() && $this->_agreementHelper->hasAgreement() && ($this->_helper->isBillinkUsedForCheckout() || $this->_oscHelper->isOscEnabled());
    }
}