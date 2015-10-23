<?php

/**
 * Class Shopworks_Billink_Helper_BillinkAgreement
 */
class Shopworks_Billink_Helper_BillinkAgreement
{
    /**
     * @param null $store
     * @return bool
     */
    public function hasAgreement($store=null)
    {
        $isBillinkEnabled = Mage::getStoreConfig('payment/billink/active', $store);
        return $isBillinkEnabled && $this->isMagentoAgreementsEnabled($store) && $this->isBillinkAgreementsEnabled($store);
    }

    /**
     * @param null $store
     * @return bool
     */
    private function isBillinkAgreementsEnabled($store=null)
    {
        return $this->getBillinkTermsId($store) != 0;
    }

    /**
     * @param null $store
     * @return mixed
     */
    private function isMagentoAgreementsEnabled($store=null)
    {
         return Mage::getStoreConfig('checkout/options/enable_agreements', $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getBillinkTermsId($store=null)
    {
        $billinkAgreementsId =  Mage::getStoreConfig('payment/billink/billink_terms', $store);
        return $billinkAgreementsId;
    }

    /**
     * Returns store ids where the Billink module agreements are configured incorrect
     *
     * @return array storeIds
     */
    public function getIncorrectBillinkAgreemenstsConfigStoreIds()
    {
        $storeIdsWithInvalidConfig = array();

        /** @var Mage_Core_Model_Website $website */
        foreach (Mage::app()->getWebsites() as $website)
        {
            /** @var Mage_Core_Model_Store_Group $group */
            foreach ($website->getGroups() as $group)
            {
                /** @var Mage_Core_Model_Store $store */
                foreach ($group->getStores() as $store)
                {
                    if($this->hasAgreement($store))
                    {
                        $billinkAgreementsId =  $this->getBillinkTermsId($store);
                        /** @var Mage_Checkout_Model_Agreement $agreement */
                        $agreement = Mage::getModel('checkout/agreement')->load($billinkAgreementsId);
                        $agreementStoreIds = $agreement->getStoreId();

                        $isAgreementEnabled = $agreement->getIsActive();
                        $isAgreementGlobalAvailable = in_array('0', $agreementStoreIds);
                        $isAgreementAvailableForStore = in_array($store->getId(), $agreementStoreIds);

                        if(!$isAgreementEnabled)
                        {
                            $storeIdsWithInvalidConfig[] = $store->getId();
                        }
                        if(!$isAgreementGlobalAvailable && !$isAgreementAvailableForStore)
                        {
                            $storeIdsWithInvalidConfig[] = $store->getId();
                        }
                    }
                }
            }
        }

        return $storeIdsWithInvalidConfig;
    }

    /**
     * Returns store ids where the Billink module agreements disabled for a specific store
     *
     * @return array storeIds
     */
    public function getStoreIdsDisabledAgreements()
    {
        $storeIdsWithInvalidConfig = array();

        /** @var Mage_Core_Model_Website $website */
        foreach (Mage::app()->getWebsites() as $website)
        {
            /** @var Mage_Core_Model_Store_Group $group */
            foreach ($website->getGroups() as $group)
            {
                /** @var Mage_Core_Model_Store $store */
                foreach ($group->getStores() as $store)
                {
                    $isBillinkEnabled = Mage::getStoreConfig('payment/billink/active', $store);

                    if($isBillinkEnabled && $this->isBillinkAgreementsEnabled($store) && !$this->isMagentoAgreementsEnabled($store))
                    {
                        $storeIdsWithInvalidConfig[] = $store->getId();
                    }
                }
            }
        }

        return $storeIdsWithInvalidConfig;
    }
}