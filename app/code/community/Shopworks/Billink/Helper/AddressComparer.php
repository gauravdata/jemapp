<?php

/**
 * Class Shopworks_Billink_Helper_AddressComparer
 */
class Shopworks_Billink_Helper_AddressComparer
{
    /**
     * Compare 2 addresses
     *
     * @param Mage_Sales_Model_Quote_Address $address1
     * @param Mage_Sales_Model_Quote_Address $address2
     * @return bool
     */
    public function areEqual($address1, $address2)
    {
        return $this->_serializeAddress($address1) ==  $this->_serializeAddress($address2);
    }

    /**
     * Create a string from an address for easy comparison
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return string
     */
    private function _serializeAddress(Mage_Sales_Model_Quote_Address $address)  {
        return serialize(
            array(
                'firstname'     => $address->getFirstname(),
                'lastname'      => $address->getLastname(),
                'street'        => $address->getStreet(),
                'company'       => $address->getCompany(),
                'city'          => $address->getCity(),
                'postcode'      => $address->getPostcode(),
            )
        );
    }
}