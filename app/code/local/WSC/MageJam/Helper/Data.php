<?php

class WSC_MageJam_Helper_Data extends Mage_Payment_Helper_Data
{
	const CHECK_USE_FOR_COUNTRY       = 1;
    const CHECK_USE_FOR_CURRENCY      = 2;
    const CHECK_USE_CHECKOUT          = 4;
    const CHECK_USE_FOR_MULTISHIPPING = 8;
    const CHECK_USE_INTERNAL          = 16;
    const CHECK_ORDER_TOTAL_MIN_MAX   = 32;
    const CHECK_RECURRING_PROFILES    = 64;
    const CHECK_ZERO_TOTAL            = 128;
    
    /**
     * Retrieve requested payment method
     *
     * @param $code
     * @return mixed|null
     */
    public function getMethod($code)
    {
        if($this->isMagentoVersion18Or19()) {
        	return $this->_getMethodOnVersion18Or19($code);    
        }else{
        	return $this->_getMethodOnVersionLower($code);	
        } 
    }
    
	/**
     * Retrieve requested payment method
     *
     * @param $code
     * @return mixed|null
     */
    protected function _getMethodOnVersion18Or19($code)
    {
        $quote = $this->getQuote();
        $store = $quote ? $quote->getStoreId() : null;

        foreach (Mage::helper('payment')->getStoreMethods($store, $quote) as $method) {
            if($method->getCode() != $code) {
                continue;
            }
            $canUse = $this->_canUseMethod($method);
            $isApplicable = $method->isApplicableToQuote($quote, self::CHECK_ZERO_TOTAL);
            if ($canUse && $isApplicable) {
                return $method;
            }
        }
        return null;
    }
    
	/**
     * Retrieve requested payment method
     *
     * @param $code
     * @return mixed|null
     */
    protected function _getMethodOnVersionLower($code)
    {
        $quote = $this->getQuote();
        $store = $quote ? $quote->getStoreId() : null;

        $total = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
        foreach (Mage::helper('payment')->getStoreMethods($store, $quote) as $method) {
            if($method->getCode() != $code) {
                continue;
            }
            $canUse = $this->_canUseMethod($method);
            $isApplicable = $total != 0
                        || $method->getCode() == 'free'
                        || ($quote->hasRecurringItems() && $method->canManageRecurringProfiles());
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

	protected function canUseMethod1($method)
    {
        return $method->isApplicableToQuote($this->getQuote(), self::CHECK_USE_FOR_COUNTRY
            | self::CHECK_USE_FOR_CURRENCY
            | self::CHECK_ORDER_TOTAL_MIN_MAX
        );
    }
    
    /**
     * Check payment method model
     *
     * @param Mage_Payment_Model_Method_Abstract $method
     * @return bool
     */
    public function _canUseMethod($method)
    {
        if($this->isMagentoVersion18Or19()) {
        	return $method && $method->canUseCheckout() && $this->canUseMethod1($method);    
        }else{
        	if (!$method || !$method->canUseCheckout()) {
	            return false;
	        }
	        return $this->_canUseMethod1OnVersionLower($method);	
        } 
    }
    
    protected function _canUseMethod1OnVersionLower($method)
    {
        if (!$method->canUseForCountry($this->getQuote()->getBillingAddress()->getCountry())) {
            return false;
        }

        if (!$method->canUseForCurrency($this->getQuote()->getStore()->getBaseCurrencyCode())) {
            return false;
        }

        /**
         * Checking for min/max order total for assigned payment method
         */
        $total = $this->getQuote()->getBaseGrandTotal();
        $minTotal = $method->getConfigData('min_order_total');
        $maxTotal = $method->getConfigData('max_order_total');

        if((!empty($minTotal) && ($total < $minTotal)) || (!empty($maxTotal) && ($total > $maxTotal))) {
            return false;
        }
        return true;
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
    
    public function isMagentoVersion18Or19()
    {
    	$version = Mage::getVersion();
	    return (0 === strpos($version, '1.8') || 0 === strpos($version, '1.9'));
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * Remove invalid xml in product title, description & short description
     */
    public function stripInvalidXmlInProduct(Mage_Catalog_Model_Product $product) {

        $title = $this->stripInvalidXml($product->getName());
        $description = $this->stripInvalidXml($product->getDescription());
        $short_description = $this->stripInvalidXml($product->getShortDescription());

        $product->setName($title);
        $product->setDescription($description);
        $product->setShortDescription($short_description);
    }

    /**
     * Removes invalid XML
     *
     * @access public
     * @param string $value
     * @return string
     */
    public function stripInvalidXml($value)
    {

        $ret = "";

        if (empty($value))
        {
            return $ret;
        }

        $length = strlen($value);
        for ($i=0; $i < $length; $i++)
        {
            $current = ord($value{$i});
            if (($current == 0x9) ||
                ($current == 0xA) ||
                ($current == 0xD) ||
                (($current >= 0x20) && ($current <= 0xD7FF)) ||
                (($current >= 0xE000) && ($current <= 0xFFFD)) ||
                (($current >= 0x10000) && ($current <= 0x10FFFF)))
            {
                $ret .= chr($current);
            }
            else
            {
                $ret .= " ";
            }
        }

        return $ret;
    }
}