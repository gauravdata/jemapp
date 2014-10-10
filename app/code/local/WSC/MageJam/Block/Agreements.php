<?php

class WSC_MageJam_Block_Agreements extends Mage_Checkout_Block_Agreements
{
    protected $_postedAgreements = null;

    public function isChecked($id)
    {
        if(is_null($this->_postedAgreements)) {
            /* @var $session Mage_Checkout_Model_Session */
            $session = Mage::getSingleton('checkout/session');
            $postedAgreements = $session->getPostedAgreements(array());
            if(is_null($postedAgreements)) {
                $this->_postedAgreements = array();
            } else {
                $this->_postedAgreements = $postedAgreements;
            }
        }
        return isset($this->_postedAgreements[$id]);
    }
}