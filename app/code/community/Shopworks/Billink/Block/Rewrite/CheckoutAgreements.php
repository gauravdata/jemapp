<?php

/**
 * Class Shopworks_Billink_Block_Rewrite_MageCheckoutAgreements
 *
 * Overwrite the agreements block so we can hide/show the Billink agreement.
 */
class Shopworks_Billink_Block_Rewrite_CheckoutAgreements extends Mage_Checkout_Block_Agreements
{
    /**
     * @var Shopworks_Billink_Helper_Billink
     */
    protected $_helper;
    /**
     * @var Shopworks_Billink_Helper_BillinkAgreement
     */
    protected $_agreementHelper;


    /**
     * Magento constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_helper = Mage::helper('billink/Billink');
        $this->_agreementHelper = Mage::helper('billink/BillinkAgreement');
    }

    /**
     * @throws Exception
     * @return array
     */
    public function getAgreements()
    {
        $billinkAgreementsId =  $this->_agreementHelper->getBillinkTermsId();

        //Get the original agreements
        $agreements = parent::getAgreements();

        //If the billink payment service is used, make sure the conditions are visible
        if($this->isBillinkTermsUsed())
        {
            if(!$this->_isBillinkTermInAgreements($agreements, $billinkAgreementsId))
            {
                throw new Exception('The Billink terms and conditions are not enabled');
            }
        }
        //If the billink payment service is not used, make sure the Billink terms are hidden.
        else if($this->_agreementHelper->hasAgreement())
        {
            $agreements = $this->_removeBillinkAgreement($agreements, $billinkAgreementsId);
        }

        return $agreements;
    }

    /**
     * @return bool
     */
    protected function isBillinkTermsUsed()
    {
        return $this->_helper->isReadyToUse() && $this->_agreementHelper->hasAgreement() && $this->_helper->isBillinkUsedForCheckout();
    }

    /**
     * @param array $agreements
     * @param string $billinkAgreementsId
     * @return bool
     */
    private function _isBillinkTermInAgreements($agreements, $billinkAgreementsId)
    {
        foreach($agreements as $agreement)
        {
            /** @var Mage_Checkout_Model_Agreement $agreement */
            if($agreement->getId() == $billinkAgreementsId)
            {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $agreements
     * @param string $billinkAgreementsId
     * @return array
     */
    private function _removeBillinkAgreement($agreements, $billinkAgreementsId)
    {
        $filteredAgreements = array();
        foreach($agreements as $agreement)
        {
            /** @var Mage_Checkout_Model_Agreement $agreement */
            if($agreement->getId() != $billinkAgreementsId)
            {
                $filteredAgreements[] = $agreement;
            }
        }

        return $filteredAgreements;
    }
}
