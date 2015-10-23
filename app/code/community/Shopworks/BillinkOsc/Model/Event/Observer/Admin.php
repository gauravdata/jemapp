<?php

class Shopworks_BillinkOsc_Model_Event_Observer_Admin extends Shopworks_Billink_Model_Event_Observer_Admin
{
    private $_errorMessageOscTermsDisabled = 'U moet de algemene voorwaarde aanzetten voor de One Step Checkout module. Dit kan in System -> configuration -> Terms and conditions -> Show Magento Checkout Terms and Conditions';

    /**
     * @var Shopworks_BillinkOsc_Helper_Data
     */
    private $_helper;

    /**
     * @var Shopworks_Billink_Helper_BillinkAgreement
     */
    private $_agreementHelper;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('billink_osc');
        $this->_agreementHelper = Mage::helper('billink/BillinkAgreement');
    }

    /**
     * Show a notification if the Billink module is not authenticated
     */
    public function showBillinkPluginNotifications()
    {
        if($this->_helper->isOscEnabled())
        {
            if($this->isBillinkEnabled() && $this->isUserLoggedIn() && $this->_agreementHelper->hasAgreement())
            {
                //Check if the terms settings for onestepcheckout is enabled
                if(!Mage::getStoreConfig('onestepcheckout/terms/enable_default_terms'))
                {
                    $this->_addMessage($this->_errorMessageOscTermsDisabled);
                }
            }
        }
    }
}