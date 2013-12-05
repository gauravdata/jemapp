<?php
/**
 * File used to handle Magento and Klarna addresses
 *
 * PHP Version 5.2
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */

/**
 * Klarna_KlarnaPaymentModule_Helper_Address
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Helper_Address extends Mage_Core_Helper_Abstract
{

    /**
     * Update a Magento address with post values and save it
     *
     * @param object $address The Magento address
     * @param array  $post    Array holding the post values
     *
     * @return void
     */
    public function updateAddress($address, $post)
    {
        if (array_key_exists("street", $post)) {
            $street = $post["street"];
            if (array_key_exists("house_number", $post)) {
                $street .=  " " . $post["house_number"];
            }
            if (array_key_exists("house_extension", $post)) {
                $street .= " " . $post["house_extension"];
            }
            $address->setStreet(KiTT_String::decode(trim($street)));
        }

        if (array_key_exists("first_name", $post)) {
            $address->setFirstname(KiTT_String::decode($post["first_name"]));
        }

        if (array_key_exists("last_name", $post)) {
            $address->setLastname(KiTT_String::decode($post["last_name"]));
        }

        if (array_key_exists("zipcode", $post)) {
            $address->setPostcode(KiTT_String::decode($post["zipcode"]));
        }

        if (array_key_exists("city", $post)) {
            $address->setCity(KiTT_String::decode($post["city"]));
        }

        if (array_key_exists("phone_number", $post)) {
            $address->setTelephone(KiTT_String::decode($post["phone_number"]));
        }

        $address->setCompany($this->_getcompanyName($address, $post));

        $address->save();
    }

    /**
     * Get company name if possible.
     *
     * @param object $address The Magento address
     * @param array  $post    Array holding the post values
     *
     * @return string Company name or empty string.
     */
    private function _getcompanyName($address, $post)
    {
        $logic = KiTT::countryLogic(KiTT::locale($address->getCountryId()));

        if ($logic->isCompanyAllowed() === false) {
            return '';
        }

        if (array_key_exists('invoice_type', $post)
            && $post['invoice_type'] !== 'company'
        ) {
            return '';
        }

        // If there is a company name in the POST, update it on the address.
        if (array_key_exists('company_name', $post)) {
            return KiTT_String::decode($post['company_name']);
        }

        // Otherwise keep what is on the address.
        return $address->getCompany();
    }

    /**
     * Update a Magento address with a KlarnaAddr and save it
     *
     * @param object     $magento The Magento address to update
     * @param KlarnaAddr $klarna  The KlarnaAddr to use
     *
     * @return void
     */
    public function updateWithKlarnaAddr($magento, $klarna)
    {
        $street = $klarna->getStreet();
        if (strlen($klarna->getHouseNumber()) > 0) {
            $street .= " " . $klarna->getHouseNumber();
        }
        if (strlen($klarna->getHouseExt()) > 0) {
            $street .= " " . $klarna->getHouseExt();
        }


        $logic = KiTT::countryLogic(KiTT::locale($klarna->getCountry()));
        // If it's a company purchase set company name.
        $company = $klarna->getCompanyName();
        if (strlen($company) > 0 && $logic->isCompanyAllowed()) {
            $magento->setCompany(KiTT_String::decode($company));
        } else {
            $magento->setFirstname(KiTT_String::decode($klarna->getFirstName()))
                ->setLastname(KiTT_String::decode($klarna->getLastName()))
                ->setCompany('');
        }

        $magento->setPostcode(KiTT_String::decode($klarna->getZipCode()))
            ->setStreet(KiTT_String::decode(trim($street)))
            ->setCity(KiTT_String::decode($klarna->getCity()))
            ->save();
    }

    /**
     * Update a Magento address with another Magento address and save it.
     *
     * @param object $primary   The address to update
     * @param object $secondary The address to mirror
     *
     * @return void
     */
    public function mirrorAddress($primary, $secondary)
    {
        $primary->setFirstname($secondary->getFirstname())
            ->setLastname($secondary->getLastname())
            ->setPostcode($secondary->getPostcode())
            ->setStreet($secondary->getStreet())
            ->setCity($secondary->getCity())
            ->setTelephone($secondary->getTelephone())
            ->setCountry($secondary->getCountry())
            ->setCompany($secondary->getCompany())
            ->save();
    }

    /**
     * Create a KlarnaAddr from a Magento address
     *
     * @param object $address The Magento address to convert
     *
     * @return KlarnaAddr
     */
    public function toKlarna($address)
    {
        $streetFields = $address->getStreet();
        $street = $streetFields[0];
        if (Mage::helper('klarnaPaymentModule')->mergeStreetFields()
            && count($streetFields) > 1
        ) {
            $street .= " " . $streetFields[1];
        }

        $logic = KiTT::countryLogic(KiTT::locale($address->getCountry()));
        $split = KiTT_Addresses::splitStreet(trim($street), $logic->getSplit());

        $houseNo = "";
        if (array_key_exists("house_number", $split)) {
            $houseNo = $split["house_number"];
        }

        $houseExt = "";
        if (array_key_exists("house_extension", $split)) {
            $houseExt = $split["house_extension"];
        }

        $klarnaAddr = new KlarnaAddr(
            "",
            "",
            $address->getTelephone(),
            KiTT_String::encode($address->getFirstname()),
            KiTT_String::encode($address->getLastname()),
            "",
            KiTT_String::encode(trim($split["street"])),
            KiTT_String::encode($address->getPostcode()),
            KiTT_String::encode($address->getCity()),
            $address->getCountry(),
            KiTT_String::encode(trim($houseNo)),
            KiTT_String::encode(trim($houseExt))
        );

        $logic = KiTT::countryLogic(KiTT::locale($address->getCountry()));
        $company = $address->getCompany();
        if (strlen($company) > 0 && $logic->isCompanyAllowed()) {
            $klarnaAddr->setCompanyName(KiTT_String::encode($company));
            $klarnaAddr->isCompany = true;
        } else {
            $klarnaAddr->setCompanyName('');
            $klarnaAddr->isCompany = false;
        }

        return $klarnaAddr;
    }

}
