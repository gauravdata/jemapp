<?php
/**
 * Event observer
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
 * Class to observe and handle Magento events
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Model_Observer extends Mage_Core_Model_Abstract
{

    /**
     * Collects invoice fee from qoute/addresses to quote
     *
     * @param Varien_Event_Observer $observer Observer instance
     *
     * @return void
     */
    public function salesQuoteCollectTotalsAfter(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $quote->setInvoiceFee(0);
        $quote->setBaseInvoiceFee(0);
        $quote->setInvoiceFeeExcludedVat(0);
        $quote->setBaseInvoiceFeeExcludedVat(0);
        $quote->setInvoiceTaxAmount(0);
        $quote->setBaseInvoiceTaxAmount(0);
        $quote->setInvoiceFeeRate(0);

        foreach ($quote->getAllAddresses() as $address) {
            $quote->setInvoiceFee(
                (float) $quote->getInvoiceFee() + $address->getInvoiceFee()
            );
            $quote->setBaseInvoiceFee(
                (float) $quote->getBaseInvoiceFee() + $address->getBaseInvoiceFee()
            );

            $quoteFeeExclVat = $quote->getInvoiceFeeExcludedVat();
            $addressFeeExclCat = $address->getInvoiceFeeExcludedVat();
            $quote->setInvoiceFeeExcludedVat(
                (float) $quoteFeeExclVat + $addressFeeExclCat
            );

            $quoteBaseFeeExclVat = $quote->getBaseInvoiceFeeExcludedVat();
            $baseFeeExclVat = $address->getBaseInvoiceFeeExcludedVat();
            $quote->setBaseInvoiceFeeExcludedVat(
                (float) $quoteBaseFeeExclVat + $baseFeeExclVat
            );

            $quoteFeeTaxAmount = $quote->getInvoiceTaxAmount();
            $addressFeeTaxAmount = $address->getInvoiceTaxAmount();
            $quote->setInvoiceTaxAmount(
                (float) $quoteFeeTaxAmount + $addressFeeTaxAmount
            );

            $quoteTaxAmountBase = $quote->getBaseInvoiceTaxAmount();
            $baseTaxAmount = $address->getBaseInvoiceTaxAmount();
            $quote->setBaseInvoiceTaxAmount(
                (float) $quoteTaxAmountBase + $baseTaxAmount
            );
            $quote->setInvoiceFeeRate($address->getInvoiceFeeRate());
        }
    }

    /**
     * Adds invoice fee to a completed order
     *
     * @param Varien_Event_Observer $observer Magento observer object
     *
     * @return void
     */
    public function salesOrderPaymentPlaceEnd(Varien_Event_Observer $observer)
    {
        $payment = $observer->getPayment();
        if ($payment->getMethodInstance()->getCode() != 'klarna_invoice') {
            return;
        }

        $info = $payment->getMethodInstance()->getInfoInstance();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (! $quote->getId()) {
            $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        }

        //Set the invoice fee included tax value
        $info->setAdditionalInformation('invoice_fee', $quote->getInvoiceFee());
        $info->setAdditionalInformation(
            'base_invoice_fee', $quote->getBaseInvoiceFee()
        );
        $info->setAdditionalInformation(
            'invoice_fee_exluding_vat', $quote->getInvoiceFeeExcludedVat()
        );
        $info->setAdditionalInformation(
            'base_invoice_fee_exluding_vat', $quote->getBaseInvoiceFeeExcludedVat()
        );
        //Set the invoice fee tax amount
        $info->setAdditionalInformation(
            'invoice_tax_amount', $quote->getInvoiceTaxAmount()
        );
        $info->setAdditionalInformation(
            'base_invoice_tax_amount', $quote->getBaseInvoiceTaxAmount()
        );
        //Set the invoice fee rate used
        $info->setAdditionalInformation(
            'invoice_fee_rate', $quote->getInvoiceFeeRate()
        );
        $info->save();
    }

    /**
     * Handle the invoice capture for Klarna orders
     *
     * @param object $observer Magento observer object
     *
     * @return Klarna_KlarnaPaymentModule_Model_Observer
     */
    public function salesOrderInvoicePay($observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        $payment = $order->getPayment();

        $method = $payment->getMethod();
        $supportedMethods = array(
            'kreditor_invoice',
            'klarna_invoice',
            'kreditor_partpayment',
            'klarna_partpayment',
            'klarna_specpayment'
        );

        if (!in_array($method, $supportedMethods)) {
            return $this;
        }

        $info = $payment->getAdditionalInformation();
        $gateway = Mage::helper("klarnaPaymentModule/gateway_advanced");

        // The array that will hold the items that we are going to use
        $items = array();

        // Get the item ids that we are going to use in our call to Klarna
        $itemkeys = array();
        if (isset($_REQUEST['invoice']['items'])) {
            $itemkeys = array_keys($_REQUEST['invoice']['items']);
        }

        // Loop through the item collection and check if its the object that we
        // should send to Klarna
        foreach ($invoice->getAllItems() as $item) {
            if (in_array($item->getOrderItemId(), $itemkeys)) {
                $items[] = $item;
            }
        }

        Mage::helper('klarnaPaymentModule/api')->loadConfig($order->getStoreId());
        $country = $order->getShippingAddress()->getCountry();
        $klarna = null;
        try {
            $klarna = KiTT::api(KiTT::locale($country));
            $gateway->init($klarna, $order, $info);
            $result = $gateway->activate($items);
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }

        $locale = Mage::helper('klarnaPaymentModule/lang')
            ->createOrderLocale($order);
        $translator = KiTT::translator($locale);
        $message = $translator->translate("INVOICE_CREATED_SUCCESSFULLY");
        $message = str_replace("xx", $result["klarna_invoice_number"], $message);
        Mage::getSingleton('adminhtml/session')->addSuccess($message);
        $order->addStatusHistoryComment($message);

        $invoices = $payment->setAdditionalInformation("klarna_invoice_collection");
        if (!is_array($invoices)) {
            $invoices = array();
        }
        $invoices[] = $result["klarna_invoice_link"];

        $payment->setAdditionalInformation("klarna_invoice_collection", $invoices);

        Mage::dispatchEvent(
            'klarna_post_activation',
            array(
                'klarna' => $klarna,
                'id' => $result["klarna_invoice_number"],
                'locale' => $locale
            )
        );


        return $this;
    }

    /**
     * Method to handle the download and showing of the pclasses,
     * aswell as the checking for updates.
     *
     * @param object $observer magento observer object
     *
     * @return void
     */
    public function adminSystemConfigChangedSectionKlarna($observer)
    {
        $helper = Mage::helper("klarnaPaymentModule/pclass");
        $pclassaction = Mage::app()->getRequest()->getParam(
            'klarna_pclasses_buttons'
        );
        $api = Mage::helper("klarnaPaymentModule/api");
        switch ($pclassaction) {
        case "update":
            $helper->updatePclasses(Mage::app()->getStores(), $api);
            //Fall-through to view after updating.
        case "view":
            $helper->displayPClasses($api);
            break;
        }

        $updateaction = Mage::app()->getRequest()->getParam('klarna_updates');

        if ($updateaction == 'check_update') {
            $helper = Mage::helper('klarnaPaymentModule/api');
            $notice = $helper->checkForUpdates();
            if ($notice) {
                Mage::getSingleton('core/session')->addNotice($notice);
            }
        }
    }

    /**
     * Method used to check for updates when logging in to the backend
     *
     * @return void
     */
    public function adminSessionUserLoginSuccess()
    {
        $storeId = Mage::app()->getStore()->getId();
        if (Mage::getStoreConfig('klarna/general/check_on_login', $storeId)) {
            $helper = Mage::helper('klarnaPaymentModule/api');
            $notice = $helper->checkForUpdates();
            if ($notice) {
                Mage::getSingleton('core/session')->addNotice($notice);
            }
        }
    }

    /**
     * Method for updating the order status after completing a purchase
     *
     * @param object $observer Magento observer object
     *
     * @return Klarna_KlarnaPaymentModule_Model_Observer
     */
    public function salesOrderPlaceAfter($observer)
    {
        $order = $observer->getOrder();
        if (!isset($order)) {
            return $this;
        }
        $payment = $order->getPayment();

        if (!isset($payment)) {
            return $this;
        }

        $api = Mage::helper("klarnaPaymentModule/api");

        try {
            $api->getPaymentCode($payment->getMethod());
        } catch (Exception $e) {
            return $this;
        }

        $api->loadConfig();

        // Update the order status with the status recieved from Klarna
        $status = $payment->getAdditionalInformation('klarna_status');
        if (!isset($status)) {
            return $this;
        }

        $label = Mage::helper("klarnaPaymentModule")->getStatusLabel($status);
        $order->addStatusToHistory($label)->save();
        return $this;
    }

    /**
     * Method for updating the order status when viewing the order in the backend
     *
     * Only check for a status update if the order is viewed in the backend
     * This is done so that the checkOrder status isn't run multiple times
     * directly after the purchase has been made.
     *
     * @param object $observer Magento observer object
     *
     * @return Klarna_KlarnaPaymentModule_Model_Observer
     */
    public function salesOrderLoadAfter($observer)
    {
        if (!Mage::getSingleton('admin/session')->isLoggedIn()) {
            return $this;
        }

        $order = $observer->getOrder();
        $payment = $order->getPayment();

        $oldStatus = $order->getStatus();
        if ($oldStatus !== "klarna_pending") {
            return $this;
        }

        Mage::helper('klarnaPaymentModule/api')->loadConfig($order->getStoreId());

        $country = $order->getShippingAddress()->getCountry();
        $tID = $payment->getAdditionalInformation('klarna_transaction_id');

        $status = "";
        try {
            $klarna = KiTT::api(KiTT::locale($country));
            $result = (int)$klarna->checkOrderStatus($tID);
            $status = Mage::helper('klarnaPaymentModule')->getStatusLabel($result);
        } catch (KlarnaException $e) {
            $message = KiTT_String::decode($e->getMessage());
            Mage::getSingleton('core/session')->addError($message);
            return $this;
        }

        if ($status !== $oldStatus) {
            $order->addStatusToHistory($status)->save();
        }

        return $this;
    }

    /**
     * Method used for canceling a Klarna invoice when a Magento order is canceled
     *
     * @param object $observer Magento observer object
     *
     * @return Klarna_KlarnaPaymentModule_Model_Observer
     */
    public function salesOrderPaymentCancel($observer)
    {
        $payment = $observer->getEvent()->getPayment();
        $method = $payment->getMethod();
        if (($method !== 'klarna_invoice')
            && ($method !== 'klarna_partpayment')
            && ($method !== 'klarna_specpayment')
        ) {
            return $this;
        }

        $info = $payment->getAdditionalInformation();
        $gateway = Mage::helper("klarnaPaymentModule/gateway_advanced");

        $order = $payment->getOrder();
        Mage::helper('klarnaPaymentModule/api')->loadConfig($order->getStoreId());
        $country = strtolower($order->getShippingAddress()->getCountry());

        try {
            $gateway->init(KiTT::api(KiTT::locale($country)), $order, $info);
            $result = $gateway->cancel();
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }

        $invoiceID = $result["klarna_invoice_number"];
        $message = "Klarna invno: {$invoiceID} has been canceled";
        Mage::getSingleton('adminhtml/session')->addSuccess($message);
        $order->addStatusHistoryComment($message);

        return $this;
    }

    /**
     * Method used for sending a customer noticifcation by email on post Klarna
     * invoice activation
     *
     * @param object $observer Magento observer object
     *
     * @return void
     */
    public function klarnaPostActivation($observer)
    {
        $storeId = Mage::app()->getStore()->getId();
        if (!Mage::getStoreConfig('klarna/advanced/send_by_email', $storeId)) {
            return;
        }
        try {
            $klarna = $observer->getKlarna();
            $invoiceID = $observer->getId();
            $locale = $observer->getLocale();
            $translator = KiTT::translator($locale);
            $message = $translator->translate("email_sent_for");
            $klarna->emailInvoice($invoiceID);
            $message .= " {$invoiceID}";
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        } catch (Exception $e) {
            Mage::throwException("Error : {$e->getCode()}# {$e->getMessage()}");
        }
    }

}
