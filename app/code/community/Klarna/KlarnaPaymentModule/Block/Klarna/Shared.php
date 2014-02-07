<?php
/**
 * File used to create the shared block for the Klarna solutions
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
 * Class used to create a shared block
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Block_Klarna_Shared
    extends Mage_Payment_Block_Form
{

    // @codingStandardsIgnoreStart method name required by Magento
    /**
     * Render template html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::helper('klarnaPaymentModule')->isOneStepCheckout()) {
            $this->setTemplate('klarna/form-onestepcheckout.phtml');
            $method = $this->getMethod();
            $baptizer = Mage::helper('klarnaPaymentModule/baptizer');
            $oscHelper = Mage::helper("klarnaPaymentModule/checkout_onestep")
                ->init(
                    $method,
                    $baptizer->init(
                        Mage::helper('klarnaPaymentModule/api')->getPaymentCode(
                            $method->getCode()
                        )
                    )
                );
            $variables = $oscHelper->assembleOscTemplate();

            foreach ($variables as $key => $value) {
                $this->assign($key, $value);
            }
        } else {
            $this->setTemplate('klarna/form.phtml');
        }
        return parent::_toHtml();
    }
    // @codingStandardsIgnoreEnd

    /**
     * Set the agb_link configuration key in KiTT
     *
     * @param string $country Lower case iso-alpha 2
     *
     * @return void
     */
    private function _setAGBLink($country)
    {
        $country = strtolower($country);
        KiTT::configure(
            array(
                'agb_link' => (string)Mage::getStoreConfig(
                    "klarna/{$country}/agblink", Mage::app()->getStore()->getId()
                )
            )
        );
    }

    /**
     * Get the checkout HTML
     *
     * @return string
     */
    public function getCheckoutHTML()
    {
        $paymentMethod = $this->getMethod();
        $quote = $paymentMethod->getInfoInstance()->getQuote();
        $shippingAddress = $quote->getShippingAddress();
        $location = Mage::helper("klarnaPaymentModule")
            ->location($shippingAddress);
        if ($location === null) {
            return;
        }

        $api = Mage::helper('klarnaPaymentModule/api');
        $api->loadConfig();

        $country = strtolower($location);
        $logic = KiTT::countryLogic(KiTT::locale($country));
        if ($logic->needAGB()) {
            $this->_setAGBLink($country);
        }

        $locale = Mage::helper('klarnaPaymentModule/lang')
            ->createLocale($location);

        $checkout = Mage::helper('klarnaPaymentModule/checkout')->init(
            $locale,
            $quote->getGrandTotal()
        );

        $code = $api->getPaymentCode($paymentMethod->getCode());
        $option = $checkout->getController()->getOption($code);
        $option->setBaptizer(
            Mage::helper("klarnaPaymentModule/baptizer")->init($code)
        );

        $session = Mage::getSingleton('checkout/session')->init('klarna');
        $error = $session->getData("{$code}_error");
        $session->unsetData("{$code}_error");

        $option->setAddress(
            Mage::helper('klarnaPaymentModule/address')
                ->toKlarna($shippingAddress)
        );

        if (is_array($error)) {
            $inputValues = $checkout->parseErrorData($error['data']);
            if (strlen($error['message']) > 0) {
                $option->setError($error['message']);
            }
            if (array_key_exists("paymentPlan", $inputValues)) {
                $option->selectPClass($inputValues["paymentPlan"]);
            }
            $option->prefill($inputValues);
        } else {
            if ($quote->getCustomerDob() !== null) {
                $option->setBirthDay(
                    date("Y-m-d", strtotime($quote->getCustomerDob()))
                );
            }
        }
        $data = Mage::helper('klarnaPaymentModule');

        $fee = $data->getInvoiceFeeDisplayValue(
            $paymentMethod->getAddressInvoiceFee($shippingAddress)
        );

        $fee = ($fee > 0) ? round($fee, 2) : 0;
        $option->setPaymentFee($fee);

        return $option->show();
    }
}
