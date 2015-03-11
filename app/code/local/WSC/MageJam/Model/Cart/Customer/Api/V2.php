<?php

class WSC_MageJam_Model_Cart_Customer_Api_V2 extends WSC_MageJam_Model_Cart_Customer_Api {
    /**
     * Prepare customer entered data for implementing
     *
     * @param  object $data
     * @return array
     */
    protected function _prepareCustomerAddressData($data)
    {
        if (is_array($data)) {
            $dataAddresses = array();
            foreach($data as $addressItem) {
                if (null !== ($_addressItem = get_object_vars($addressItem))) {
                    $dataAddresses[] = $_addressItem;
                }
            }
            return parent::_prepareCustomerAddressData($dataAddresses);
        }

        return null;
    }
}