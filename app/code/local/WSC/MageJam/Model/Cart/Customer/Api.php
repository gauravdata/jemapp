<?php

class WSC_MageJam_Model_Cart_Customer_Api extends Mage_Checkout_Model_Api_Resource_Customer
{
    public function __construct()
    {
        $this->_storeIdSessionField = "cart_store_id";

        $this->_attributesMap['quote'] = array('quote_id' => 'entity_id');
        $this->_attributesMap['quote_customer'] = array('customer_id' => 'entity_id');
        $this->_attributesMap['quote_address'] = array('address_id' => 'entity_id');
    }
        /**
         * @param  int $quoteId
         * @param  array of array|object $customerAddressData
         * @param  int|string $store
         * @return int
         */
    public function setAddresses($quoteId, $customerAddressData, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        $customerAddressData = $this->_prepareCustomerAddressData($customerAddressData);
        if (is_null($customerAddressData)) {
            $this->_fault('customer_address_data_empty');
        }

        foreach ($customerAddressData as $addressItem) {
//            switch($addressItem['mode']) {
//            case self::ADDRESS_BILLING:
            /** @var $address Mage_Sales_Model_Quote_Address */
            $address = Mage::getModel("sales/quote_address");
//                break;
//            case self::ADDRESS_SHIPPING:
//                /** @var $address Mage_Sales_Model_Quote_Address */
//                $address = Mage::getModel("sales/quote_address");
//                break;
//            }
            $addressMode = $addressItem['mode'];
            unset($addressItem['mode']);

            if (!empty($addressItem['entity_id'])) {
                $customerAddress = $this->_getCustomerAddress($addressItem['entity_id']);
                if ($customerAddress->getCustomerId() != $quote->getCustomerId()) {
                    $this->_fault('address_not_belong_customer');
                }
                $address->importCustomerAddress($customerAddress);

            } else {
                $address->setData($addressItem);
            }

            $address->implodeStreetAddress();

            if (($validateRes = $address->validate())!==true) {
                $this->_fault('customer_address_invalid', implode(PHP_EOL, $validateRes));
            }

            switch($addressMode) {
                case self::ADDRESS_BILLING:
                    if($quote->getCustomer() && $quote->getCustomer()->getEmail()) {
                        $address->setEmail($quote->getCustomer()->getEmail());
                    }
                    if (!$quote->isVirtual()) {
                        $usingCase = isset($addressItem['use_for_shipping']) ? (int)$addressItem['use_for_shipping'] : 0;
                        switch($usingCase) {
                            case 0:
                                $shippingAddress = $quote->getShippingAddress();
                                $shippingAddress->setSameAsBilling(0);
                                break;
                            case 1:

                                $billingAddress = clone $address;
                                $billingAddress->unsAddressId()->unsAddressType();

                                $shippingAddress = $quote->getShippingAddress();
                                $shippingMethod = $shippingAddress->getShippingMethod();
                                $shippingAddress->addData($billingAddress->getData())
                                    ->setSameAsBilling(1)
                                    ->setShippingMethod($shippingMethod)
                                    ->setCollectShippingRates(true);
                                break;
                        }
                    }
                    $quote->setBillingAddress($address);
                    break;

                case self::ADDRESS_SHIPPING:
                    $address->setCollectShippingRates(true)
                        ->setSameAsBilling(0);
                    $quote->setShippingAddress($address);
                    break;
            }

        }
        try {
            if(!$quote->getCustomer() || !$quote->getCustomer()->getEmail())  {
                $quote->setCustomerEmail($address->getEmail());
            }
            $quote
                ->collectTotals()
                ->save();
           
        } catch (Exception $e) {
            $this->_fault('address_is_not_set', $e->getMessage());
        }

        return true;
    }
        /**
         * Prepare customer entered data for implementing
         *
         * @param  array $data
         * @return array
         */
    protected function _prepareCustomerAddressData($data)
    {
        if (!is_array($data) || !is_array($data[0])) {
            return null;
        }

        $dataAddresses = array();
        foreach($data as $addressItem) {
            foreach ($this->_attributesMap['quote_address'] as $attributeAlias=>$attributeCode) {
                if(isset($addressItem[$attributeAlias]))
                {
                    $addressItem[$attributeCode] = $addressItem[$attributeAlias];
                    unset($addressItem[$attributeAlias]);
                }
            }
            $dataAddresses[] = $addressItem;
        }
        return $dataAddresses;
    }

}