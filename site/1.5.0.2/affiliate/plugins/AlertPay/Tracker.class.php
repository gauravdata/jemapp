<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
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
class AlertPay_Tracker extends Pap_Tracking_CallbackTracker {

    private $testMode;
    private $securityCode;
    private $purchaseType;

    /**
     * @return AlertPay_Tracker
     */
    public function getInstance() {
        $tracker = new AlertPay_Tracker();
        $tracker->setTrackerName("AlertPay");
        return $tracker;
    }

    public function checkStatus() {
        if ($this->securityCode != Gpf_Settings::get(AlertPay_Config::SECURITY_CODE)) {
            $this->debug('Invalid security code. Received security code: '.$this->securityCode. ' Transaction: '.$this->getTransactionID().', payer email: '.$this->getEmail());
            return false;
        }

        if ($this->testMode == '1' && Gpf_Settings::get(AlertPay_Config::ALLOW_TEST_SALES) != Gpf::YES) {
            $this->debug('Test sales are not registered. If you want to register test sales, turn it on in plugin configuration. Transaction: '.$this->getTransactionID().', payer email: '.$this->getEmail());
            return false;
        }

        if ($this->getPaymentStatus() != "Success") {
            $this->debug('Payment status is not Success. Transaction: '.$this->getTransactionID().', status: '.$this->getPaymentStatus().', payer email: '.$this->getEmail());
            return false;
        }

        return true;
    }

    public function readRequestVariables() {
        $request = new Pap_Tracking_Request();
        	
        $this->testMode = $request->getPostParam('ap_test');
        $this->securityCode = $request->getPostParam('ap_securitycode');
        $this->purchaseType = $request->getPostParam('ap_purchasetype');
        	
        // assign posted variables to local variables
        $cookieValue = stripslashes($request->getPostParam('apc_'.Gpf_Settings::get(AlertPay_Config::CUSTOM_FIELD_NUMBER)));
        $this->setCookie($cookieValue);
        $this->setTotalCost($request->getPostParam('ap_totalamount'));
        $this->setTransactionID($request->getPostParam('ap_referencenumber'));
        $this->setEmail($request->getPostParam('ap_custemailaddress'));
        $this->setProductID($request->getPostParam('ap_itemname'));
        $this->setPaymentStatus($request->getPostParam('ap_status'));

        $this->debug('Request variables: '.
        'testMode:'.$this->testMode.', '.
        'securityCode:'.$this->securityCode.', '.
        'purchaseType:'.$this->purchaseType.', '.
        'cookieValue:'.$this->getCookie().', '.
        'totalCost:'.$this->getTotalCost().', '.
        'transactionId:'.$this->getTransactionID().', '.
        'email:'.$this->getEmail().', '.
        'productID:'.$this->getProductID().', '.
        'paymentStatus:'.$this->getPaymentStatus());
    }

    public function isRecurring() {
        return $this->purchaseType == "Subscription" &&
        (Gpf_Settings::get(AlertPay_Config::DIFF_RECURRING_COMMISSIONS) == Gpf::YES);
    }

    public function getOrderID() {
        return $this->getTransactionID();
    }

    protected function processSubscriptionPayment() {
        $this->debug("Start registering recurring payment / subscription");

        $recurringComm = new Pap_Features_RecurringCommissions_RecurringCommissionsForm();
        $recurringComm->createCommissionsNoRpc($this->getOrderID());

        $this->debug("End registering recurring payment / subscription");
    }
}
?>
