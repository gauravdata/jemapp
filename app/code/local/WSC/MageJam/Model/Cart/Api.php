<?php

class WSC_MageJam_Model_Cart_Api extends Mage_Checkout_Model_Cart_Api
{
    /**
     * Used for api method magejam_cart.create (soap v1) or cartCreate() soap v2
     *
     * @param null $store
     * @param null $customerId
     * @return int
     */
    public function create($store = null, $customerId = null)
    {
        $storeId = Mage::helper('magejam')->getStoreId($store);
        if (!$customerId) {
            return parent::create($storeId);
        }
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel('sales/quote');
        $quote->setStoreId($storeId);
        $quote->loadByCustomer($customerId);
        if ($quote->getId()) {
            return (int) $quote->getId();
        }

        /* @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }
        $quote->setCustomer($customer)
               ->setIsActive(true)
               ->setIsMultiShipping(false)
               ->save();
        return (int) $quote->getId();
    }

    /**
     * Used for api method magejam_cart.merge() soap v1 OR cartMerge() soap v2
     *
     * @param $guestQuoteId
     * @param $customerQuoteId
     * @param null $store
     * @return array
     */
    public function merge($guestQuoteId, $customerQuoteId, $store = null)
    {
        $guestQuote = $this->_getQuote($guestQuoteId, $store);
        $customerQuote = parent::_getQuote($customerQuoteId, $guestQuote->getStoreId());
        $customerQuote->merge($guestQuote);
        $customerQuote->getBillingAddress();
        $customerQuote->getShippingAddress()->setCollectShippingRates(true);
        $customerQuote->collectTotals()->save();

        $guestQuote->setIsActive(0)
            ->save();

        return $this->_quoteInfo($customerQuote);
    }

    /**
     * @param $quoteId
     * @param null $store
     * @return bool
     */
    public function collectTotals($quoteId, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);
        $quote->getBillingAddress();
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals()->save();
        return $this->_quoteInfo($quote);
    }

    /**
     * Retrieves quote by quote identifier and store code or by quote identifier
     *
     * @param int $quoteId
     * @param string|int $store
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote($quoteId, $store = null)
    {
        $storeId = Mage::helper('magejam')->getStoreId($store);
        return parent::_getQuote($quoteId, $storeId);
    }



    /**
     * Retrieve full information about quote
     *
     * @param  Mage_Sales_Model_Quote $quote
     * @return array
     */
    protected function _quoteInfo(Mage_Sales_Model_Quote $quote)
    {
        if ($quote->getGiftMessageId() > 0) {
            $quote->setGiftMessage(
                Mage::getSingleton('giftmessage/message')->load($quote->getGiftMessageId())->getMessage()
            );
        }

        $result = $this->_getAttributes($quote, 'quote');
        $result['shipping_address'] = $this->_getAttributes($quote->getShippingAddress(), 'quote_address');
        $result['billing_address'] = $this->_getAttributes($quote->getBillingAddress(), 'quote_address');
        $result['items'] = array();

        foreach ($quote->getAllItems() as $item) {
            if ($item->getGiftMessageId() > 0) {
                $item->setGiftMessage(
                    Mage::getSingleton('giftmessage/message')->load($item->getGiftMessageId())->getMessage()
                );
            }

            $result['items'][] = $this->_getAttributes($item, 'quote_item');
        }

        $result['payment'] = $this->_getAttributes($quote->getPayment(), 'quote_payment');

        return $result;
    }
}