<?php
/**
 * Klarna_KlarnaPaymentModule_Helper_Data
 *
 * PHP Version 5.3
 *
 * @category  Payment
 * @package   Klarna_Module_Magento
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */

/**
 * Data helper class
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Helper_Data
    extends Mage_Core_Helper_Abstract
{

    const NONKLARNA = -1;

    /**
     * Check if OneStepCheckout is activated or not
     *
     * @return bool
     */
    public function isOneStepCheckout()
    {
        return (bool) Mage::getStoreConfig(
            'onestepcheckout/general/rewrite_checkout_links'
        );
    }

    /**
     * Check if OneStepCheckout displays their prises with the tax included
     *
     * @return bool
     */
    public function isOneStepCheckoutTaxIncluded()
    {
        return (bool) Mage::getStoreConfig(
            'onestepcheckout/general/display_tax_included'
        );
    }

    /**
     * Get logo CSS class to use
     *
     * @param string $methodCode method code, Can be klarna_invoice,
     *                           klarna_partpayment or klarna_specpayment.
     * @param string $country    iso-alpha-2 code
     *
     * @return string Entire uri to the logo for the given methodCode
     */
    public function getLogo($methodCode, $country)
    {
        $type = $this->_getLogoType($methodCode);
        if (strlen($type) == 0) {
            //Return the CSS class for a default logo
            return "klarna_logo_invoice_default";
        }
        $country = strtolower($country);
        return "klarna_logo_{$type}_{$country}";
    }

    /**
     * Get the logo type to use for the css logo class
     *
     * @param string $methodCode The Magento method code to convert
     *
     * @return string
     */
    private function _getLogoType($methodCode)
    {
        switch($methodCode) {
        case 'klarna_invoice':
            return "invoice";
        case 'klarna_partpayment':
            return "account";
        case 'klarna_specpayment':
            return "special";
        default:
            return "";
        }
    }

    /**
     * Get the default label to translate for a specified payment method
     *
     * @param string $method Payment method
     *
     * @return string
     */
    public function getTitleLabel($method)
    {
        switch ($method) {
        case "klarna_partpayment":
            return "MODULE_PARTPAY_TEXT_TITLE";
        case "klarna_specpayment":
            return "MODULE_SPEC_TEXT_TITLE";
        case "klarna_invoice":
            return "MODULE_INVOICE_TEXT_TITLE";
        default:
            return "Klarna";
        }
    }

    /**
     * Get the status label for a KlarnaFlag
     *
     * @param KlarnaFlags $status a KlarnaFlags status code
     *
     * @return string
     */
    public function getStatusLabel($status)
    {
        switch($status){
        case KlarnaFlags::ACCEPTED:
            return Klarna_KlarnaPaymentModule_Model_Order_Status::ACCEPTED;
        case KlarnaFlags::DENIED:
            return Klarna_KlarnaPaymentModule_Model_Order_Status::DENIED;
        default:
            return Klarna_KlarnaPaymentModule_Model_Order_Status::PENDING;
        }
    }

    /**
     * Get an array of activated Klarna countries
     *
     * @param int $storeId The storeid to fetch countries for
     *
     * @return array
     */
    public function getActivatedCountries($storeId = null)
    {
        $activatedCountries = Mage::getStoreConfig(
            'klarna/general/activated_countries', $storeId
        );
        return explode(",", $activatedCountries);
    }

    /**
     * Use the KiTT locator to locate the customers origin
     *
     * This method requires helper/api->requireKiTT to have run first
     *
     * @param Mage_Customer_Model_Address_Abstract $address Customer address
     *
     * @return string
     */
    public function location($address = null)
    {
        $addr = null;
        if ($address instanceof Mage_Customer_Model_Address_Abstract) {
            $addr = Mage::helper("klarnaPaymentModule/address")->toKlarna($address);
        }

        $locator = KiTT::locator();

        $currency = Mage::app()->getStore()->getCurrentCurrencyCode();
        $language = Mage::helper('klarnaPaymentModule/lang')
            ->getLocaleCode(Mage::app()->getLocale()->getLocaleCode());

        return $locator->locate($currency, $language, $addr);
    }

    /**
     * Guess the Customer address
     *
     * @param object $quote Magento Quote Object
     *
     * @return object Magento Address Object
     */
    public function guessCustomerAddress($quote = null)
    {
        $address = null;
        if (isset($quote)) {
            $address = $quote->getShippingAddress();
        }

        if ($address !== null && $address->getCountryId() !== null) {
            return $address;
        }

        return Mage::getSingleton('customer/session')
            ->getCustomer()
            ->getPrimaryShippingAddress();
    }

    /**
     * Retrieve the merchant id for the given country.
     *
     * @param string|int $country iso-alpha-2 country code or KlarnaCountry
     *                            constant.
     *
     * @return int merchant ID for the specifieod country
     */
    public function getMerchantId($country)
    {
        if (is_int($country)) {
            $country = KlarnaCountry::getCode($country);
        }
        $country = strtolower($country);
        return $this->merchantid = (int) Mage::getStoreConfig(
            "klarna/{$country}/merchant_id",
            Mage::app()->getStore()->getId()
        );
    }

    /**
     * Get the invoice fee configuration value by country
     *
     * @param string $country The country iso to fetch for
     *
     * @return float
     */
    public function getInvoiceFeeByCountry($country)
    {
        $country = strtolower($country);
        return Mage::getStoreConfig("klarna/{$country}/invoice_fee");
    }

    /**
     * Will return the invoice fee formatted according to the tax settings
     * in magento
     *
     * @param float   $base       The initial invoice fee to use taken from the
     *                            settings
     * @param Address $address    A Magento address object
     * @param int     $taxClassId integer ID of the tax class used for the
     *                            invoice.
     *
     * @return array
     */
    public function getInvoiceFeeArray($base, $address, $taxClassId)
    {
        //Get the correct rate to use
        $store = Mage::app()->getStore();
        $calc = Mage::getSingleton('tax/calculation');
        $rateRequest = $calc->getRateRequest(
            $address, $address, $taxClassId, $store
        );
        $taxClass = (int) Mage::getStoreConfig('klarna/general/tax_class');;
        $rateRequest->setProductClassId($taxClass);
        $rate = $calc->getRate($rateRequest);

        //Get the vat display options for products from Magento tax settings
        $VatOptions = Mage::getStoreConfig(
            "tax/calculation/price_includes_tax", $store->getId()
        );

        if ($VatOptions == 1) {
            //Catalog prices are set to include taxes
            $value = $calc->calcTaxAmount($base, $rate, true, false);
            $excl = ($base - $value);
            return array(
                'excl' => $excl,
                'base_excl' => $this->calcBaseValue($excl),
                'incl' => $base,
                'base_incl' => $this->calcBaseValue($base),
                'taxamount' => $value,
                'base_taxamount' => $this->calcBaseValue($value),
                'rate' => $rate
            );
        }
        //Catalog prices are set to exclude taxes
        $value = $calc->calcTaxAmount($base, $rate, false, false);
        $incl = ($base + $value);

        return array(
            'excl' => $base,
            'base_excl' => $this->calcBaseValue($base),
            'incl' => $incl,
            'base_incl' => $this->calcBaseValue($incl),
            'taxamount' => $value,
            'base_taxamount' => $this->calcBaseValue($value),
            'rate' => $rate
        );
    }

    /**
     * Try to calculate the value of the invoice fee with the base currency
     * of the store if the purchase was done with a different currency.
     *
     * @param float $value value to calculate on
     *
     * @return float
     */
    protected function calcBaseValue($value)
    {
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();

        if ($currentCurrencyCode !== $baseCurrencyCode) {
            $currencyModel = Mage::getModel('directory/currency');
            $currencyRates = $currencyModel->getCurrencyRates(
                $baseCurrencyCode, array($currentCurrencyCode)
            );
            return ($value / $currencyRates[$currentCurrencyCode]);
        }
        return $value;
    }

    /**
     * Returns the value to be displayed in the frontend according to either
     * settings in OneStepCheckout or Magento
     *
     * @param array $feeArray formatted array recieved from getAddressInvoiceFee
     *
     * @return float
     */
    public function getInvoiceFeeDisplayValue($feeArray)
    {
        $storeId = Mage::app()->getStore()->getId();
        if ($this->isOneStepCheckout()) {
            if ($this->isOneStepCheckoutTaxIncluded()) {
                //OneStepCheckout displays their products including taxes
                return $feeArray['incl'];
            }
            //OneStepCheckout displays their products excluding taxes
            return $feeArray['excl'];
        }

        /**
         * 1: Display excluding VAT
         * 2: Display including VAT
         * 3: Display both
         */
        if (Mage::getStoreConfig("tax/sales_display/price", $storeId) == 1) {
            //Display options are set to display only excluding prices
            return $feeArray['excl'];
        }
        //Display settings are set to either show including or both
        //including and excluding.
        return $feeArray['incl'];
    }

    /**
     * Check if we should merge the fields Street Address 1 and 2
     *
     * @return bool
     */
    public function mergeStreetFields()
    {
        return (bool) Mage::getStoreConfig(
            "klarna/advanced/append_street_address_2"
        );
    }
}
