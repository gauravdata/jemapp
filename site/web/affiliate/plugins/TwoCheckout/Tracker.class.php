<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro plugins
 */
class TwoCheckout_Tracker extends Pap_Tracking_CallbackTracker {

    /**
     *
     * @var Pap_Tracking_Request
     */
    private $request;
    /**
     * @return TwoCheckout_Tracker
     */

    private $notificationType;
    public function getInstance() {
        $tracker = new TwoCheckout_Tracker();
        $tracker->setTrackerName("TwoCheckout");
        return $tracker;
    }

    protected function registerCommission() {
        $this->debug("Start registering sales TwoCheckout");
        if($this->isRefund()) {
            $this->makeRefund();
            return;
        }
        if (Gpf_Settings::get(TwoCheckout_Config::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION) == Gpf::YES) {
            $this->processWholeCartAsOneTransaction();
        } else {
            $this->processEachItemInCartSeparatly();
        }
    }

    protected function processSubscriptionPayment() {
        $this->debug('2checkout INS plugin: processSubscriptionPayment: '.$this->notificationType);
        if($this->notificationType == 'RECURRING_STOPPED' || $this->notificationType == 'RECURRING_COMPLETE') {
            $this->removeRecurringCommission();
            return;
        }
        parent::processSubscriptionPayment();
    }

    private function removeRecurringCommission() {
        $this->debug('2checkout INS plugin: Removing recurring commisions with orderId: ' . $this->getSubscriptionID());
        $commissions = Pap_Features_RecurringCommissions_Main::getRecurringSelect($this->getSubscriptionID())->getAllRows();
        $recurringCommissions = new Pap_Features_RecurringCommissions_RecurringCommission();

        foreach ($recurringCommissions->loadCollectionFromRecordset($commissions) as $recurringCommission) {
            $recurringCommission->delete();
        }
    }

    private function makeRefund() {
        $this->debug('2checkout refund started');
        $transaction = new Pap_Db_Transaction();
        $transaction->setOrderId($this->getSubscriptionID());
        try{
            $collection = $transaction->loadCollection(array(Pap_Db_Table_Transactions::ORDER_ID));
        } catch (Gpf_Exception $e) {
            $this->debug('2checkout refund failed - Error in loading transactions: '.$e->getMessage());
            return;
        }

        if($collection->getSize() == 0) {
            $this->debug('2checkout refund failed: No transactions with order id: '.$this->getSubscriptionID());
            return;
        }

        foreach($collection as $transactionDb) {
            $transaction = new Pap_Common_Transaction();
            $transaction->processRefundChargeback($transactionDb->getId(), $transactionDb->getType());
            $this->debug('2checkout refunded transaction with id '.$transactionDb->getId());
        }
    }

    private function processEachItemInCartSeparatly() {
        for($i = 1; $i <= $this->request->getPostParam('item_count'); $i++) {
            $this->setTotalCost($this->request->getPostParam('item_usd_amount_' . $i));
            $this->setProductID($this->request->getPostParam('item_id_' . $i));
            parent::registerCommission();
        }
    }

    private function processWholeCartAsOneTransaction() {
        $this->debug('TwoCheckout - Process whole cart as one transaction');

        $productId = '';
        $totalUsd = 0;
        for($i = 1; $i <= $this->request->getPostParam('item_count'); $i++) {
            $productId .= (string)$this->request->getPostParam('item_id_'.$i) . ', ';
            $totalUsd += (float)$this->request->getPostParam('item_usd_amount_'.$i);
        }

        $this->setTotalCost($totalUsd);
        $this->setProductID($productId);
        parent::registerCommission();
    }

    public function getOrderID() {
        return $this->request->getPostParam('sale_id');
    }


    public function checkStatus() {
        return $this->getPaymentStatus();
    }

    private function isRefund() {
        return $this->request->getPostParam('message_type') == 'REFUND_ISSUED';
    }

    public function isRecurring() {
        if(
        $this->notificationType == 'RECURRING_INSTALLMENT_SUCCESS'
        || $this->notificationType == 'RECURRING_STOPPED'
        || $this->notificationType == 'RECURRING_COMPLETE') {
            return true;
        }
        return false;
    }

    /**
     *  @return Pap_Tracking_Request
     */
    protected function getRequestObject() {
        return Pap_Contexts_Action::getContextInstance()->getRequestObject();
    }

    public function readRequestVariables() {
        $this->debug('2checkout INS plugin: $_POST array:' . print_r($_POST, true));
        $this->request = $this->getRequestObject();
        $parts = explode(Gpf_Settings::get(TwoCheckout_Config::CUSTOM_SEPARATOR), $this->request->getPostParam('vendor_order_id'));
        $PAPvisitorId = array_pop($parts);

        $this->debug('2checkout INS plugin: PapVisitorId: ' . $PAPvisitorId);
        $this->setCookie($PAPvisitorId);
        $this->resolveStatus();
        $this->setSubscriptionID($this->request->getPostParam('sale_id'));
    }

    private function resolveStatus() {
        $messageType = trim($this->request->getPostParam('message_type'));
        $this->debug('2checkout INS plugin: resolveStatus: '.$messageType);
        $this->notificationType = $messageType;
        switch ($messageType) {
            case 'ORDER_CREATED':
            case 'REFUND_ISSUED':
            case 'RECURRING_INSTALLMENT_SUCCESS':
            case 'RECURRING_STOPPED':
            case 'RECURRING_COMPLETE':
                $this->setPaymentStatus(true);
                return;
        }
        $this->setPaymentStatus(false);
        $this->debug('2checkout INS plugin: Message type not supported: ' . $messageType);
    }
}
?>
