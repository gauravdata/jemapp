<?php

class WSC_MageJam_Helper_Data extends Mage_Payment_Helper_Data
{
    /**
     * Retrieve requested payment method
     *
     * @param $code
     * @return mixed|null
     */
    public function getMethod($code)
    {
        $quote = $this->getQuote();
        $store = $quote ? $quote->getStoreId() : null;

        foreach (Mage::helper('payment')->getStoreMethods($store, $quote) as $method) {
            if($method->getCode() != $code) {
                continue;
            }
            $canUse = $this->_canUseMethod($method);
            $isApplicable = $method->isApplicableToQuote($quote, Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL);
            if ($canUse && $isApplicable) {
                return $method;
            }
        }
        return null;
    }


    /**
     * Returns Quote instance from session
     *
     * @return mixed
     */
    public function getQuote()
    {
        /* @var $quote Mage_Sales_Model_Quote */
        $quote =  Mage::getSingleton('checkout/session')->getQuote();
        if(!$quote->getIsActive()) {
            $quote->setIsActive(true)->save();
        }
        return $quote;
    }

    /**
     * Check payment method model
     *
     * @param Mage_Payment_Model_Method_Abstract $method
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        return $method && $method->canUseCheckout()
        && $method->isApplicableToQuote(
            $this->getQuote(), Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
            | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
            | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
        );
    }

    /**
     * Retrieves store id from store code, if no store id specified,
     * it use seted session or admin store
     *
     * @param null $store
     * @return int
     * @throws Mage_Api_Exception
     */
    public function getStoreId($store = null)
    {
        if (is_null($store)) {
            $store = Mage::app()->getDefaultStoreView()->getId();
        }

        try {
            $storeId = Mage::app()->getStore($store)->getId();
        } catch (Mage_Core_Model_Store_Exception $e) {
            throw new Mage_Api_Exception('store_not_exists');
        }

        return $storeId;
    }

    public function getMagejamVersion()
    {
        return (string) Mage::getConfig()->getNode('modules')->WSC_MageJam->version;
    }
}