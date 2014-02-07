<?php
/**
 * File used to create the shared info block for the Klarna solutions
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
 * Class used to create a shared info block
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Block_Klarna_SharedInfo
    extends Mage_Payment_Block_Info
{

    /**
     * Render template html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $payment = $this->getMethod();
        $info = $this->getInfo();

        $country = $this->_getCountryFromInfo($info);

        $helper = Mage::helper("klarnaPaymentModule");
        $this->assign(
            "logo", $helper->getLogo($payment->getCode(), $country)
        );

        Mage::helper('klarnaPaymentModule/api')->loadConfig();
        $locale = Mage::helper("klarnaPaymentModule/lang")
            ->createLocale($country);
        $translator = KiTT::translator($locale);

        $transactionId = $info->getAdditionalInformation("klarna_transaction_id");
        if (strlen($transactionId) > 0) {
            $this->assign("showTransactionNumber", true);
            $this->assign(
                "label", $translator->translate("reservation_number_text")
            );
            $this->assign("transactionNumber", $transactionId);
        }

        $reference = $info->getAdditionalInformation('reference');
        if (strlen($reference) > 0) {
            $this->assign(
                'reference_label', $translator->translate('reference')
            );
            $this->assign('reference', $reference);
        }

        $invoices = $info->getAdditionalInformation("klarna_invoice_collection");

        if ((Mage::getSingleton('admin/session')->isLoggedIn())
            && (is_array($invoices))
            && (count($invoices) > 0)
        ) {
            $this->assign("showInvoiceNumbers", true);
            $this->assign(
                "invoiceLabel", $translator->translate("click_invoice_to_print")
            );
            $this->assign("invoiceNumbers", $invoices);
        }

        $kittURL = Klarna_KlarnaPaymentModule_Helper_Checkout::STATIC_KITT;
        $this->assign("checkoutCSS", "{$kittURL}res/v1.1/checkout.css");

        $this->setTemplate('klarna/info.phtml');
        return parent::_toHtml();
    }

    /**
     * Get shipping country from the info instance
     *
     * @param object $info Magento info instance
     *
     * @return mixed
     */
    private function _getCountryFromInfo($info)
    {
        $order = $info->getOrder();
        if (isset($order)) {
            return $order->getShippingAddress()->getCountry();
        }

        $quote = $info->getQuote();
        if (isset($quote)) {
            return $quote->getShippingAddress()->getCountry();
        }

        Mage::throwException("Unable to find a country");
    }

    /**
     * Get the info message to display on PDFs
     *
     * @return string
     */
    public function toPdf()
    {
        Mage::helper('klarnaPaymentModule/api')->loadConfig();
        $order = $this->getInfo()->getOrder();
        $locale = Mage::helper('klarnaPaymentModule/lang')
            ->createOrderLocale($order);
        $translator = KiTT::translator($locale);
        $code = $this->getMethod()->getCode();
        $label = Mage::helper("klarnaPaymentModule")->getTitleLabel($code);
        return $translator->translate($label);
    }

}
