<?php


class WSC_MageJam_Model_Payment_Api extends Mage_Checkout_Model_Cart_Payment_Api
{
    /**
     * Used for retrieving payment method configuration
     *
     * @param int $quoteId
     * @param null $store
     * @return array
     */
    public function getPaymentConfigList($quoteId = null, $store = null)
    {
        if (is_null($quoteId)) {
            return $this->_getPaymentConfigListWithoutQuote();
        }
        $quote = $this->_getQuote($quoteId, $store);
        $methodsResult = array();

        foreach ($this->getMethods($quote) as $method) {
            $methodsResult[] = $this->_preparePaymentConfigData($method);
        }

        return $methodsResult;
    }

    /**
     * Simplified version of getPaymentMethodsConfig
     *
     * @return array
     */
    protected function _getPaymentConfigListWithoutQuote() {
        $store = Mage::app()->getStore();
        $methodsResult = array();

        /* @var $config Mage_Payment_Model_Config */
        $config = Mage::getSingleton('payment/config');
        $methods = $config->getActiveMethods($store);

        foreach ($methods as $method) {
            $methodsResult[] = $this->_preparePaymentConfigData($method);
        }

        return $methodsResult;
    }

    /**
     * Prepares Payment Config Data for returning
     *
     * @param Mage_Payment_Model_Method_Abstract $method
     * @return mixed
     */
    protected function _preparePaymentConfigData(Mage_Payment_Model_Method_Abstract $method) {
        $code = $method->getCode();
        $result = Mage::getStoreConfig('payment/'. $code);
        $result['code'] = $code;
        $result['cc_types'] = $this->_getPaymentMethodAvailableCcTypes($method);
        if($result['group'] == 'paypal') {
            $result['paypal_config'] = Mage::getStoreConfig('paypal');
        }
        return $result;
    }

    /**
     * Retrieves available payment methods
     *
     * @param $quote
     * @return array
     */
    public function getMethods($quote)
    {
        $methods = array();
        $store = $quote->getStoreId();
        foreach (Mage::helper('payment')->getStoreMethods($store, $quote) as $method) {
            $_canUse = $this->_canUseMethod($method, $quote);
            $_isApplicable = $method->isApplicableToQuote($quote, Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL);

            if ($_canUse && $_isApplicable) {
                $methods[] = $method;
            }
        }
        return $methods;
    }

    /**
     * Check payment method model
     *
     * @param Mage_Payment_Model_Method_Abstract|null
     * @param $quote
     * @return bool
     */
    protected function _canUseMethod($method, $quote)
    {
        return $method && $method->canUseCheckout()
        && $method->isApplicableToQuote($quote, Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
            | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
            | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
        );
    }
	
	/**
     * @param  $quoteId
     * @param  $store
     * @return array
     */
    public function getPaymentMethodList($quoteId, $store=null)
    {
        $quote = $this->_getQuote($quoteId, $store);
        $store = $quote->getStoreId();

        $total = $quote->getBaseSubtotal();

        $methodsResult = array();
        $methods = Mage::helper('payment')->getStoreMethods($store, $quote);
        foreach ($methods as $key=>$method) {
            /** @var $method Mage_Payment_Model_Method_Abstract */
            if ($this->_canUsePaymentMethod($method, $quote)
                    && ($total != 0
                        || $method->getCode() == 'free'
                        || ($quote->hasRecurringItems() && $method->canManageRecurringProfiles()))) {
                $methodsResult[] =
                        array(
                            "code" => $method->getCode(),
                            "title" => $method->getTitle(),
                            "cc_types" => $this->_getPaymentMethodAvailableCcTypes($method)
                        );
            }
        }

        return $methodsResult;
    }
}