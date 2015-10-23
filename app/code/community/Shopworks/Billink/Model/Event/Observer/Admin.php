<?php

/**
 * Class Shopworks_Billink_Model_Event_Observer_Admin
 */
class Shopworks_Billink_Model_Event_Observer_Admin
{
    private $_errorMessageAuth = 'De authenticatie gegevens voor de Billink module zijn nog niet ingevuld. De module is nog niet actief';
    private $_errorMessageAgreements = 'Er is een probleem bij het instellen van de Billink <strong>Algemene voorwaarden</strong> controleer de configuratie voor storeview(s) %s a.u.b.';
    private $_errorMessageStoreAgreementDisabled = 'Schakel <strong>algemene voorwaarden</strong> in (zie: System -> Configuration -> Checkout -> Enable Terms and Conditions) voor storeview(s) %s a.u.b. U kunt geen Billink betalingen accepteren.';
    private $_errorMessageTaxConfigDiscount = 'Voor een goede werking van de Billink module moeten kortingen worden toegepast voordat de BTW berekend wordt. Dit kan aangepast worden in: Sytem -> configuration -> Tax -> Apply Customer Tax';
    private $_errorMessageTaxConfigAlgorithm = 'Om afrondingsverschillen in de Billink module te voorkomen moet BTW worden berekend over rijen. Dit kan aangepast worden in: System -> configuration -> Tax -> Tax Calculation Method Based On.';

    /**
     * @var Shopworks_Billink_Helper_Billink
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
        $this->_helper = Mage::helper('billink/Billink');
        $this->_agreementHelper = Mage::helper('billink/BillinkAgreement');
    }

    /**
     * Show a notification if the Billink module is not authenticated
     */
    public function showBillinkPluginNotifications()
    {
        if($this->isBillinkEnabled() && $this->isUserLoggedIn())
        {
            // Authentication check
            $this->_checkForAuthentication();
            // Terms and Agreements check
            $this->_checkTerms();
            // Check tax and discount settings
            $this->_checkTaxDiscountSettings();
        }
    }

    /**
     * @return bool
     */
    protected function isBillinkEnabled()
    {
        return (boolean)Mage::getStoreConfig('payment/billink/active');
    }

    /**
     * @return mixed
     */
    protected function isUserLoggedIn()
    {
        return Mage::getSingleton('admin/session')->isLoggedIn();
    }

    /**
     * Authentication check
     */
    private function _checkForAuthentication()
    {
        if(!$this->_helper->isBillinkAuthenticated())
        {
            $this->_addMessage($this->_errorMessageAuth);
        }
    }

    /**
     * Terms and Agreements check
     */
    private function _checkTerms()
    {
        //Get store id's
        $storeIds = $this->_agreementHelper->getIncorrectBillinkAgreemenstsConfigStoreIds();

        if(count($storeIds) > 0)
        {
            //Convert store ids to comma seperated names
            $storeNames = array();
            foreach ($storeIds as $storeId)
            {
                $storeNames[] = Mage::getModel('core/store')->load($storeId)->getData('name');
            }
            
            // Terms and Agreements check
            // Get the store ids  
            $storeIds = $this->_agreementHelper->getStoreIdsDisabledAgreements();
           
            if(count($storeIds) > 0)
            {
                $storeNames = $this->_convertStoreIdsToNames($storeIds);
                $errorMessage = sprintf($this->_errorMessageStoreAgreementDisabled, implode(', ', $storeNames));
                $this->_addMessage($errorMessage);
            }
            
            $errorMessage = sprintf($this->_errorMessageAgreements, implode(', ', $storeNames));
            $this->_addMessage($errorMessage);
        }
    }

    /**
     * @param string $message
     */
    protected function _addMessage($message)
    {
        /** @var Mage_Core_Model_Session $session */
        $session = Mage::getSingleton('core/session');
        $session->addError($message);
    }

    /**
     * Check tax and discount settings
     */
    private function _checkTaxDiscountSettings()
    {
        //Can be changed in:
        //Sytem -> configuration -> Tax -> Apply Customer Tax (set to: 'after discount')
        if (!Mage::getStoreConfig("tax/calculation/apply_after_discount"))
        {
            $this->_addMessage($this->_errorMessageTaxConfigDiscount);
        }

        //Can be changed in:
        //System -> configuration -> Tax -> Tax Calculation Method Based On (set to: 'row total')
        if(Mage::getStoreConfig("tax/calculation/algorithm") != Mage_Tax_Model_Calculation::CALC_ROW_BASE)
        {
            $this->_addMessage($this->_errorMessageTaxConfigAlgorithm);
        }
    }

    /**
     * Convert store ids to comma seperated names
     *
     * @param array $storeIds
     * @return array
     */
    private function _convertStoreIdsToNames(array $storeIds)
    {
        $storeNames = array();
        foreach ($storeIds as $storeId)
        {
            $storeNames[] = Mage::getModel('core/store')->load($storeId)->getName();
        }
        return $storeNames;
    }
}