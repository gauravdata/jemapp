<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rob
 * Date: 15-11-13
 * Time: 12:01
 * To change this template use File | Settings | File Templates.
 */

class Twm_TestEvents_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function onSalesOrderSaveAfter(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $order = $event->getOrder();

        $incrementId = $order->getIncrementId();

        $status = $order->getStatus();
        $state = $order->getState();

        Mage::log("Order #{$incrementId} saved (state: {$state})", Zend_Log::DEBUG, 'debug.log');

        if ($state == Mage_Sales_Model_Order::STATE_PROCESSING)
        {
            Mage::log("Order #{$incrementId} saved (status: {$status})", Zend_Log::DEBUG, 'debug.log');
            if ($order->hasInvoices()) {
                $invoicesPaid = true;
                foreach ($order->getInvoiceCollection() as $invoice) {
                    if ($invoice->getState() !== Mage_Sales_Model_Order_Invoice::STATE_PAID)
                    {
                        $invoiceId = $invoice->getId();
                        Mage::log("Invoice #{$invoiceId} not paid", Zend_Log::DEBUG, 'debug.log');
                        $invoicesPaid = false;
                    }
                }
                if ($invoicesPaid) {
                    Mage::log("Order #{$incrementId} saved and invoices paid (status: {$status})", Zend_Log::DEBUG, 'debug.log');
                }
            }

        }
    }
}