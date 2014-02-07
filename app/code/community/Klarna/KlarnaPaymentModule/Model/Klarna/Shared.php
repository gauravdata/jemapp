<?php
/**
 * Class with shared functions
 *
 * PHP Version 5.3
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */

/**
 * Shared Klarna Functions
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Klarna_KlarnaPaymentModule_Model_Klarna_Shared
    extends Mage_Payment_Model_Method_Abstract
{
    // @codingStandardsIgnoreStart variable name required by Magento
    protected $_formBlockType = 'klarnaPaymentModule/klarna_shared';
    protected $_infoBlockType = 'klarnaPaymentModule/klarna_sharedInfo';

    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc               = false;
    // @codingStandardsIgnoreEnd

    const SHIPPING = "shipping";
    const BILLING = "billing";

    protected $info;

    protected $data;

    protected $shippingaddress;

    protected $billingaddress;

    protected $method;

    protected $postValues;

    /**
     * Get title for a payment option.
     *
     * @return string Title of the payment option.
     */
    public function getTitle()
    {
        $info = $this->getInfoInstance();
        $quote = $info->getQuote();
        $address = $quote->getShippingAddress();
        $country = $address->getCountry();

        $grandTotal = $quote->getGrandTotal();

        $api = Mage::helper("klarnaPaymentModule/api");

        try {
            $api->loadConfig();
        } catch (Exception $e) {
            return $this->getConfigData('title');
        }

        if (strlen($country) == 0 && strlen($grandTotal) == 0) {
            $order = $info->getOrder();
            $address = $order->getShippingAddress();
            $country = $order->getShippingAddress()->getCountry();
            $grandTotal = $order->getGrandTotal();
        }

        $code = $api->getPaymentCode($this->getCode());
        $helper = Mage::helper("klarnaPaymentModule");
        $location = $helper->location($address);
        if ($location === null) {
            //If we somehow reached this far without a valid Klarna location
            //we should try to return a translated title.
            $locale = Mage::helper("klarnaPaymentModule/lang")
                ->createLocale($country);
            $translator = KiTT::translator($locale);
            $label = $helper->getTitleLabel($code);
            return $translator->translate($label);
        }

        $checkout = Mage::helper('klarnaPaymentModule/checkout')->init(
            Mage::helper('klarnaPaymentModule/lang')->createLocale($location),
            $grandTotal
        );

        $option = $checkout->getController()->getOption($code);
        $option->setBaptizer(
            Mage::helper("klarnaPaymentModule/baptizer")->init($code)
        );

        $fee = $helper->getInvoiceFeeDisplayValue(
            $this->getAddressInvoiceFee($address)
        );
        $fee = ($fee > 0) ? $fee : 0;
        $option->setPaymentFee($fee);

        return $option->getTitle();
    }

    /**
     * Assign data to info model instance
     *
     * @param mixed $data data to assign
     *
     * @return Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        $this->data = $data;
        if (!($this->data instanceof Varien_Object)) {
            $this->data = new Varien_Object($this->data);
        }

        $info = $this->getInfoInstance();
        $quote = $info->getQuote();
        $this->shippingaddress = $quote->getShippingAddress();
        $this->billingaddress = $quote->getBillingAddress();
        $this->method = $this->data->getMethod();

        $shippingCountry = strtoupper($this->shippingaddress->getCountry());

        $this->postValues = Mage::helper('klarnaPaymentModule/sanitize')->get(
            $data->getData(),
            $shippingCountry,
            Mage::helper('klarnaPaymentModule/api')->getPaymentCode($this->method)
        );

        $this->updateAddress($shippingCountry);

        // Don't send in reference for non-company purchase.
        $addressHelper = Mage::helper('klarnaPaymentModule/address');
        $klarnaAddr = $addressHelper->toKlarna($this->shippingaddress);

        if (!$klarnaAddr->isCompany) {
            if (array_key_exists('reference', $this->postValues)) {
                unset($this->postValues['reference']);
            }
        } else {
            // This insane ifcase is for OneStepCheckout.
            if (!array_key_exists('reference', $this->postValues)) {
                $reference = $klarnaAddr->getFirstName() . " " .
                    $klarnaAddr->getLastName();
                $this->postValues['reference'] = $reference;
            }
        }

        $infoHelper = Mage::helper('klarnaPaymentModule/additionalInfo');

        $infoHelper->set($info, $this->postValues);
        $info->setAdditionalInformation(
            "email",
            $infoHelper->getEmail(
                $this->shippingaddress,
                $this->billingaddress,
                Mage::getSingleton('customer/session')->getCustomer()
            )
        );

        //We cannot perform these actions with OneStepCheckout because they try
        //to save the payment method as soon as the customer views the checkout
        if (Mage::helper("klarnaPaymentModule")->isOneStepCheckout()) {
            return $this;
        }

        $locale = Kitt::locale($this->shippingaddress->getCountry());
        $logic = KiTT::countryLogic($locale);

        $this->_checkDateOfBirth($logic, $locale);
        $this->_checkConsent($logic, $locale);

        return $this;
    }

    /**
     * Check that Date of birth has been supplied if required.
     *
     * @param KiTT_CountryLogic $logic  KiTT_CountryLogic instance
     * @param KiTT_Locale       $locale KiTT_Locale instance
     *
     * @return void No return value.
     */
    private function _checkDateOfBirth(
        KiTT_CountryLogic $logic, KiTT_Locale $locale
    ) {
        if (!$logic->needDateOfBirth()) {
            return;
        }

        if ($this->postValues['birth_day'] === "00"
            || $this->postValues['birth_month'] === "00"
            || $this->postValues['birth_year'] === "00"
        ) {
            $this->_errorWithPrefix($locale, 'birthday');
        }
    }

    /**
     * Check that consent has been given if needed.
     *
     * @param KiTT_CountryLogic $logic  KiTT_CountryLogic instance
     * @param KiTT_Locale       $locale KiTT_Locale instance
     *
     * @return void No return value.
     */
    private function _checkConsent(
        KiTT_CountryLogic $logic, KiTT_Locale $locale
    ) {
        if (!$logic->needConsent()) {
            return;
        }
        if ((!array_key_exists("consent", $this->postValues))
            || ($this->postValues["consent"] !== "consent")
        ) {
            $this->error($locale, "no_consent");
        }
    }

    /**
     * Update addresses with data from our checkout box
     *
     * @param string $country iso-alpha-2 code
     *
     * @return void
     */
    protected function updateAddress($country)
    {
        $logic = KiTT::countryLogic(KiTT::locale($country));
        //Update with the getAddress call for Swedish customers
        $addressHelper = Mage::helper('klarnaPaymentModule/address');
        if ($logic->useGetAddresses()) {
            $klarnaAddr = $this->_getMatchingAddresses();
            if ($klarnaAddr !== null) {
                $addressHelper->updateWithKlarnaAddr(
                    $this->shippingaddress, $klarnaAddr
                );
            }
        }

        //Update the address with values from the checkout
        $addressHelper->updateAddress(
            $this->shippingaddress,
            $this->postValues
        );

        //Check to see if the addresses must be same. If so overwrite billing
        //address with the shipping address.
        if ($logic->shippingSameAsBilling()) {
            $addressHelper->mirrorAddress(
                $this->billingaddress, $this->shippingaddress
            );
        }
    }

    /**
     * Throws a translated exception to magentos error handler.
     *
     * @param KiTT_Locale $locale KiTT locale instance
     * @param string      $key    key to get from the language file
     *
     * @throws Mage_Payment_Model_Info_Exception
     *
     * @return void
     */
    protected function error($locale, $key = "error_title_2")
    {
        $message = $this->_getErrorMessage($locale, $key);
        throw new Mage_Payment_Model_Info_Exception($message);
    }

    /**
     * Retrieve the wanted error message translated for the given country.
     *
     * @param KiTT_Locale $locale KiTT locale instance
     * @param string      $key    key to get from the language file
     *
     * @return translated string matching the given key
     */
    private function _getErrorMessage($locale, $key)
    {
        $translator = KiTT::translator($locale);
        return $translator->translate($key);
    }

    /**
     * Throws an error message prefixed with error_title_1
     *
     * @param KiTT_Locale $locale KiTT locale instance
     * @param string      $key    key to get from the language file
     *
     * @throws Mage_Payment_Model_Info_Exception
     *
     * @return void
     */
    private function _errorWithPrefix($locale, $key)
    {
        $message = $this->_getErrorMessage($locale, 'error_title_1');
        $message .= " " . $this->_getErrorMessage($locale, $key);
        throw new Mage_Payment_Model_Info_Exception($message);
    }

    /**
     * Get a matching address from KiTT using getAddresses
     *
     * @return KlarnaAddr
     */
    private function _getMatchingAddresses()
    {
        $klarnaAddr = null;
        $locale = KiTT::locale(KlarnaCountry::SE);
        try {
            $kittAddresses = new KiTT_Addresses(KiTT::api($locale));
            $klarnaAddr = $kittAddresses->getMatchingAddress(
                $this->postValues["pno"],
                $this->postValues["address_key"]
            );
        } catch (KlarnaException $e) {
            $this->error($locale);
        }
        return $klarnaAddr;
    }

    /**
     * Get Invoice fee
     *
     * @param Mage_Customer_Model_Address_Abstract $address address object
     * @param int                                  $classId class id
     *
     * @return array
     */
    public function getAddressInvoiceFee(
        Mage_Customer_Model_Address_Abstract $address, $classId = null
    ) {
        if ($address->getCountry() === null) {
            $address = $this->getDefaultAddress();
        }

        $helper = Mage::helper('klarnaPaymentModule');
        $base = $helper->getInvoiceFeeByCountry($address->getCountry());
        if ($base > 0) {
            return $helper->getInvoiceFeeArray($base, $address, $classId);
        }
        return null;
    }

    /**
     * Loads the default address from customer information.
     *
     * This methods emulates the logic of compareDefaultsFromCart in
     * OneStepCheckout when selecting the default address.
     *
     * @return Mage_Customer_Address_Abstract
     */
    protected function getDefaultAddress()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $selectedAddress = $quote->getBillingAddress()->getCustomerAddressId();
            if ($selectedAddress) {
                return $quote->getCustomer()->getAddressById($selectedAddress);
            }
            return $quote->getCustomer()->getPrimaryBillingAddress();
        }

        return null;
    }

    /**
     * Shared checks if module is enabled
     *
     * @param object $quote optional quote object
     *
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        if (is_null($quote)) {
            return false;
        }

        $code = $this->getCode();
        $enabled = Mage::getStoreConfig(
            "klarna/payments/{$code}_enabled",
            Mage::app()->getStore()->getId()
        );

        if (!$enabled) {
            return false;
        }

        $api = Mage::helper('klarnaPaymentModule/api');
        $api->loadConfig();
        $address = $quote->getShippingAddress();

        // In OneStepCheckout the compareDefaultsFromCart observer is called
        // after isAvailable and it updates the pre-selected address. So the
        // default address is considered if the address on the quote lacks a
        // country
        if ($address->getCountry() == null) {
            $address = $this->getDefaultAddress();
            if ($address == null) {
                return false;
            }
        }

        $location = Mage::helper('klarnaPaymentModule')->location($address);

        // Check if it's a valid Klarna location
        if ($location === null) {
            return false;
        }

        $grandTotal = $quote->getGrandTotal();

        if (empty($grandTotal) || $grandTotal <= 0) {
            return false;
        }

        $locale = Mage::helper('klarnaPaymentModule/lang')
            ->createLocale($location);

        $checkout = Mage::helper('klarnaPaymentModule/checkout')
            ->init($locale, $grandTotal);

        $controller = $checkout->getController();
        $paymentCode = $api->getPaymentCode($code);
        $option = $controller->getOption($paymentCode);

        return $option->isAvailable();
    }

    /**
     * Authorize the purchase
     *
     * @param object $payment Magento payment model
     * @param double $amount  The amount to authorize with
     *
     * @return Klarna_KlarnaPaymentModule_Model_Klarna_Shared
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        $gateway = Mage::helper("klarnaPaymentModule/gateway_advanced");

        $order = $payment->getOrder();
        $shippingAddress = $order->getShippingAddress();
        $billingAddress = $order->getBillingAddress();

        $country = $shippingAddress->getCountry();
        $logic = KiTT::countryLogic(KiTT::locale($country));
        if (Mage::helper("klarnaPaymentModule")->isOneStepCheckout()
            && $logic->shippingSameAsBilling()
        ) {
            $addressHelper = Mage::helper("klarnaPaymentModule/address");
            $addressHelper->mirrorAddress($billingAddress, $shippingAddress);
        }

        $info = $this->getInfoInstance();
        $data = $info->getAdditionalInformation();

        Mage::helper("klarnaPaymentModule/api")->loadConfig();

        try {
            $gateway->init(KiTT::api(KiTT::locale($country)), $order, $data);
            $result = $gateway->transaction();
        } catch (KlarnaException $e) {
            // PNO validation from php-api
            if ($e->getCode() === 50005) {
                $locale = Mage::helper('klarnaPaymentModule/lang')
                    ->createOrderLocale($order);
                $translator = KiTT::translator($locale);
                $message = $translator->translate('error_title_2');
            } else {
                $message = KiTT_String::decode($e->getMessage());
            }
            $api = Mage::helper("klarnaPaymentModule/api");
            $method = $api->getPaymentCode($payment->getMethod());
            $session = Mage::getSingleton("checkout/session")->init('klarna');
            $errorData = array(
                'message' => $message,
                'data' => $data
            );

            $session->setData("{$method}_error", $errorData);
            $session->setGotoSection('payment');
            $session->setUpdateSection('payment-method');

            Mage::throwException(wordwrap($message));
        }

        $info->setAdditionalInformation(
            "klarna_transaction_id",
            $result["klarna_transaction_id"]
        );

        $info->setAdditionalInformation(
            "klarna_status",
            $result["klarna_status"]
        );

        return $this;
    }

}
