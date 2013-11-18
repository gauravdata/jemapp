<?php

class Twm_ForceHousenumber_Block_Checkout extends Idev_OneStepCheckout_Block_Checkout
{

    const REGEX_MATCH_HOUSENUMBER = '\s(\d+\s*\-*\w*)$';

    public function customerHasAddresses()
    {
        foreach ($this->getCustomer()->getAddresses() as $address) {
             if ($address->getStreet(2)) return true;
        }
        return false;
    }

    public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                if (!$address->getStreet(2)) continue;
                $options[] = array(
                    'value'=>$address->getId(),
                    'label'=>$address->format('oneline')
                );
            }

            $addressId = '';
            if (empty($addressId)) {
                if ($type=='billing') {
                    $address = $this->getCustomer()->getDefaultBillingAddress();
                } else {
                    $address = $this->getCustomer()->getDefaultShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            if ($type=='billing') {
                $address = $this->getQuote()->getBillingAddress();
            } else {
                $address = $this->getQuote()->getShippingAddress();
            }
            if ($address) {
                $addressIde = $address->getCustomerAddressId();
                if($addressIde){
                    $addressId = $addressIde;
                }
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select')
                ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
                ->setValue($addressId)
                ->setOptions($options);

            $select->addOption('', Mage::helper('checkout')->__('New Address'));

            $isPost = $this->getRequest()->getPost();
            $isPost = (!empty($isPost));
            $selectedValue = $this->getRequest()->getPost('billing_address_id', false);


            if($this->getNewAddressSelectValueOnError($type)){
                $select->setValue('');
            }

            return $select->getHtml();
        }
        return '';
    }

}