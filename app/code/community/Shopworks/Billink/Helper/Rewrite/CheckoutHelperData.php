<?php

/**
 * Class Shopworks_Billink_Helper_Rewrite_CheckoutHelperData
 *
 * This class return all the required terms that need to be accepted by the customer before an order can be placed.
 * Because the Billink terms only apply when the billink module is active, we add some logic to the class to make this
 * possible
 */
class Shopworks_Billink_Helper_Rewrite_CheckoutHelperData extends Mage_Checkout_Helper_Data
{
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
     * @return array
     */
    public function getRequiredAgreementIds()
    {
        //All parent logic is saved
        $requiredAgreementIds = parent::getRequiredAgreementIds();

        //Remove the agreement if Billink uses agreements and the Billink agreement is not used for the checkout
        if($this->_agreementHelper->hasAgreement() && !$this->_helper->isBillinkUsedForCheckout())
        {
            $arrayIndex = array_search($this->_agreementHelper->getBillinkTermsId(), $requiredAgreementIds);
            unset($requiredAgreementIds[$arrayIndex]);

            //Update the internal value
            $this->_agreements = $requiredAgreementIds;
        }

        return $this->_agreements;
    }

}