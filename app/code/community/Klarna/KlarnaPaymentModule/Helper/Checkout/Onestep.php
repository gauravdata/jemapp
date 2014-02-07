<?php
/**
 * File containing Klarna_KlarnaPaymentModule_Helper_Checkout_Onestep class
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
 * Helper for Onestep Checkout
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */

class Klarna_KlarnaPaymentModule_Helper_Checkout_Onestep
    extends Mage_Core_Helper_Abstract
{
    /**
     * @var Klarna_KlarnaPaymentModule_Model_Quote
     */
    protected $quote;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var KiTT_Baptizer
     */
    protected $baptizer;

    /**
     * @var KiTT_Locale
     */
    protected $locale;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var array
     */
    protected $vars;

    /**
     * Initializer.
     *
     * @param object        $paymentMethod Magento payment method object.
     * @param KiTT_Baptizer $baptizer      KiTT_Baptizer
     *
     * @return Klarna_KlarnaPaymentModule_Helper_Checkout_Onestep
     */
    public function init($paymentMethod, $baptizer)
    {
        $this->quote = $paymentMethod->getInfoInstance()->getQuote();
        $this->code = $paymentMethod->getCode();

        $this->method = Mage::helper("klarnaPaymentModule/api")
            ->getPaymentCode($this->code);

        $this->baptizer = $baptizer;

        return $this;
    }
     /**
     * assemble all needed variables for the OneStepCheckout Template.
     *
     * @return array An associative array containing the values needed.
     */
    public function assembleOscTemplate()
    {

        // --- Mage Functions
        $session = Mage::getSingleton("checkout/session")->init('klarna');
        $khelper = Mage::helper('klarnaPaymentModule');

        $img_path = Mage::getBaseUrl(
            Mage_Core_Model_Store::URL_TYPE_SKIN,
            Mage::app()->getRequest()->isSecure()
        );

        $country = strtolower(
            $khelper->guessCustomerAddress($this->quote)
                ->getCountry()
        );

        $this->locale = Mage::helper('klarnaPaymentModule/lang')
            ->createLocale($country);

        $this->fields = $this->getCountrySpecificFields();

        $merchantId = $khelper->getMerchantId($country);

        Mage::helper('klarnaPaymentModule/api')->loadConfig();

        $this->translator = KiTT::translator($this->locale);

        // --- Basic setup always used
        $this->vars = array(
            'country' => $country,
            'merchant_id' => $merchantId,
            'fee' => $this->_getInvoiceFee($khelper),
            'img_path' => $img_path,
            'code' => $this->code,
            'method' => $this->method,
            'logo' => $khelper->getLogo($this->code, $country)
        );

        $this->error = array();

        $sessionError = $session->getData("{$this->method}_error");
        if (is_array($sessionError)
            && array_key_exists("data", $sessionError)
        ) {
            $this->error = $sessionError["data"];
        }
        $session->unsetData("{$this->method}_error");

        // --- Personal number
        $this->_getPNOFields();

        // --- Gender variables
        $this->_getGenderFields();

        // --- Date of Birth variables
        $this->_getDateOfBirthFields($this->quote->getCustomerDob());

        // --- Labels
        $this->_labels();

        // --- PClasses
        $this->_pclassFields($this->quote->getGrandTotal());

        // --- Consent input
        $this->_consentFields();

        // --- Checkout Javascripts
        $this->vars['checkoutJs'] = $this->_getCheckoutJS(
            $country, $merchantId
        );

        return $this->vars;
    }

    /**
     * PNO fields.
     *
     * @return void No return value
     */
    private function _getPNOFields()
    {
        if (in_array('useGetAddresses', $this->fields)) {
            $this->vars['useGetAddresses'] = true;
        } else if (in_array('personalnumber', $this->fields)) {
            $this->vars['showPno'] = true;
        } else {
            return;
        }

        $pno = "";
        if (array_key_exists("pno", $this->error)) {
            $pno = $this->error["pno"];
        }

        $this->vars['pno'] = $pno;
        $this->vars['pno_name'] = $this->baptizer->nameField("pno");
    }

    /**
     * Gender fields.
     *
     * @return void No return value
     */
    private function _getGenderFields()
    {
        if (!in_array('gender', $this->fields)) {
            return;
        }

        $this->vars['showGender'] = true;
        $gender = null;
        if (array_key_exists("gender", $this->error)) {
            $gender = $this->error["gender"];
        }
        $this->vars['gender'] = $gender;
        $this->vars['gender_name'] = $this->baptizer->nameField("gender");
    }

    /**
     * Date of Birth fields.
     *
     * @param object $customerDob customers date of birth
     *
     * @return void No return value
     */
    private function _getDateOfBirthFields($customerDob)
    {
        if (!in_array('dob', $this->fields)) {
            return;
        }

        $dob_year = null;
        $dob_day = null;
        $dob_month = null;

        if (count($this->error) == 0) {
            if ($customerDob !== null) {
                $date = date("Y-m-d", strtotime($customerDob));
                list($dob_year, $dob_month, $dob_day) = explode('-', $date);
            }
        } else {
            if (array_key_exists("birth_year", $this->error)) {
                $dob_year = $this->error["birth_year"];
            }
            if (array_key_exists("birth_day", $this->error)) {
                $dob_day = $this->error["birth_day"];
            }
            if (array_key_exists("birth_month", $this->error)) {
                $dob_month = $this->error["birth_month"];
            }
        }

        $this->vars['dob'] = true;

        $this->vars['dob_year'] = $dob_year;
        $this->vars['dob_year_name'] = $this->baptizer->nameField("birth_year");
        $this->vars['year_disabled'] = (strlen($this->vars['dob_year']) == 0);

        $this->vars['dob_day'] = $dob_day;
        $this->vars['dob_day_name'] = $this->baptizer->nameField("birth_day");
        $this->vars['day_disabled'] = (strlen($this->vars['dob_year']) == 0);

        $this->vars['dob_month'] = $dob_month;
        $this->vars['dob_month_name'] = $this->baptizer->nameField("birth_month");
        $this->vars['month_disabled'] = (strlen($this->vars['dob_year']) == 0);
    }

    /**
     * Labels.
     *
     * @return void No return value
     */
    private function _labels()
    {
        $this->vars['dobLabel'] = $this->translator->translate('birthday');
        $this->vars['dayLabel'] = $this->translator->translate('date_day');
        $this->vars['monthLabel'] = $this->translator->translate('date_month');
        $this->vars['yearLabel'] = $this->translator->translate('date_year');
        $this->vars['genderLabel'] = $this->translator->translate('sex');
        $this->vars['maleLabel'] = $this->translator->translate('sex_male');
        $this->vars['femaleLabel'] = $this->translator->translate('sex_female');

        $pnoLabel = ($this->method === KiTT::INVOICE)
            ? 'klarna_personalOrOrganisatio_number'
            : 'person_number';
        $this->vars['pno_label'] = $this->translator->translate($pnoLabel);

        $this->vars['delivery_address'] = $this->translator->translate(
            'delivery_address'
        );

        $this->vars['goods_delivered_here'] = $this->translator->translate(
            'goods_delivered_here'
        );
    }

    /**
     * PClass fields.
     *
     * @param float $sum The value of the cart
     *
     * @return void No return value
     */
    private function _pclassFields($sum)
    {
        $pcollection = KiTT::pclassCollection(
            $this->method,
            $this->locale,
            $sum,
            KlarnaFlags::CHECKOUT_PAGE
        );

        if (count($pcollection->pclasses) > 0) {
            $defaultPClass = null;
            if ($this->method === KiTT::PART) {
                if (array_key_exists("paymentPlan", $this->error)) {
                    $defaultPClass = $this->error["paymentPlan"];
                }
                if (isset($defaultPClass)) {
                    $pcollection->setDefault($defaultPClass);
                }
            }
            $this->vars['pclasses'] = $pcollection->table();
            $this->vars['pclasses_name'] = $this->baptizer->nameField(
                "paymentPlan"
            );
        }
    }

    /**
     * Consent fields.
     *
     * @return void No return value
     */
    private function _consentFields()
    {
        if (!in_array('consent', $this->fields)) {
            return;
        }

        $country = strtolower($this->locale->getCountryCode());
        $this->_setAGBLink($country);

        $this->vars['consent'] = true;
        $this->vars['consent_name'] = $this->baptizer->nameField("consent");

        $template = KiTT::templateLoader($this->locale)
            ->load("consent.mustache");

        $this->vars['consent_div'] = $template->render(
            array(
                'config' => KiTT::configuration(),
                'locale' => $this->locale,
                'lang' => $this->translator,
                'type' => $this->method,
                'country' => $country
            )
        );
    }

    /**
     * Set the agb_link configuration key in KiTT
     *
     * @param string $country Lower case iso-alpha 2
     *
     * @return void No return value
     */
    private function _setAGBLink($country)
    {
        $country = strtolower($country);
        KiTT::configure(
            array(
                'agb_link' => (string)Mage::getStoreConfig(
                    "klarna/{$country}/agblink",
                    Mage::app()->getStore()->getId()
                )
            )
        );
    }

    /**
     * Get the Checkout javaScripts for OneStepCheckout
     *
     * @param string $country     iso-alpha-2 code
     * @param int    $merchantId store eid
     *
     * @return string Entire constructor for the Terms javascripts.
     */
    private function _getCheckoutJS($country, $merchantId)
    {
        $ajaxPath = Mage::getBaseUrl(
            Mage_Core_Model_Store::URL_TYPE_LINK,
            Mage::app()->getRequest()->isSecure()
        ) . "klarna/address/dispatch";

        $params = array(
            'box' => "payment_form_{$this->code}",
            'country' => strtolower($country),
            'language' => $this->locale->getLanguageCode(),
            'eid' => $merchantId,
            'ajax_path' => $ajaxPath,
            'params' => array(
                'shipmentAddressInput' => $this->baptizer->nameField('address_key')
            )
        );

        $termFunc = '';
        switch ($this->method) {
        case KiTT::INVOICE:
            $termFunc = 'Invoice';
            $params["charge"] = $this->vars['fee'];
            break;
        case KiTT::PART:
            $termFunc = 'Part';
            break;
        case KiTT::SPEC:
            $termFunc = 'Special';
            break;
        }
        $params = json_encode($params);

        return "new Klarna.Checkout.{$termFunc} ({$params});";
    }

    /**
     * Get an array of input fields required for the specific countries. Needed
     * for OneStepCheckout.
     *
     * @return array
     */
    public function getCountrySpecificFields()
    {
        $logic = KiTT::countryLogic($this->locale);
        $array = array();
        //If it's a country that requires a getAddresses call we do not display
        //any fields. The PNO field needed will be included with the
        //onestepcheckout template
        if ($logic->useGetAddresses()) {
            $array[] = 'useGetAddresses';
        }
        if ($logic->needConsent()) {
            $array[] = 'consent';
        }
        if ($logic->needGender()) {
            $array[] = 'gender';
        }
        if ($logic->needDateOfBirth()) {
            $array[] = 'dob';
        } else {
            $array[] = 'personalnumber';
        }
        return $array;
    }

    /**
     * Get the displayable invoice fee.
     *
     * @param Klarna_KlarnaPaymentModule_Helper_Data $helper Helper object
     *
     * @return float
     */
    private function _getInvoiceFee($helper)
    {
        $address = $helper->guessCustomerAddress($this->quote);
        $taxClassId = $this->quote->getCustomerTaxClassId();

        $feeArray = $helper->getInvoiceFeeArray(
            $helper->getInvoiceFeeByCountry(
                $this->locale->getCountryCode()
            ),
            $address,
            $taxClassId
        );

        return round($helper->getInvoiceFeeDisplayValue($feeArray), 2);
    }
}
