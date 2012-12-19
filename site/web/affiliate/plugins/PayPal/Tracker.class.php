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
class PayPal_Tracker extends Pap_Tracking_CallbackTracker {

    const CALLBACK_STATUS_VERIFIED = 'VERIFIED';

    protected $reasonCode;
    protected $parentTransId;

    protected function getParentTransId() {
        return $this->parentTransId;
    }

    protected function setParentTransId($value) {
        $this->parentTransId = $value;
    }

    private function getTransactionIdFromOrderId($orderId){
        $transaction = new Pap_Common_Transaction();
        try {
            $output = $transaction->getFirstRecordWith(Pap_Db_Table_Transactions::ORDER_ID, $orderId, array(Pap_Common_Constants::STATUS_APPROVED, Pap_Common_Constants::STATUS_PENDING));
            $this->debug('Parent transaction for refund found by orderId.');
        } catch (Gpf_DbEngine_NoRowException $e) {
            $output = $transaction->getFirstRecordWith(Pap_Db_Table_Transactions::DATA1, $orderId, array(Pap_Common_Constants::STATUS_APPROVED, Pap_Common_Constants::STATUS_PENDING));
            $this->debug('Parent transaction for refund found by data1.');
        }
        return $output->getId();
    }

    /**
     * @return PayPal_Tracker
     */
    public function getInstance() {
        $tracker = new PayPal_Tracker();
        $tracker->setTrackerName("PayPal");
        return $tracker;
    }

    protected function refundChargeback() {
        $transaction = new Pap_Common_Transaction();
        $transaction->processRefundChargeback($this->getTransactionIdFromOrderId($this->getParentTransId()), Pap_Db_Transaction::TYPE_REFUND, '',
        $this->getOrderID(), 0, true);
    }

    public function checkStatus() {
        if ($this->getPaymentStatus() == "Refunded") {
            $this->debug('Transaction '.$this->getOrderID().' will be marked as refund of transaction '. $this->getParentTransId());
            if (strpos($this->sendBackVerification(), self::CALLBACK_STATUS_VERIFIED) !== false) {
                try {
                    $this->refundChargeback();
                    $this->debug('Refund complete, ending processing.');
                } catch (Gpf_Exception $e) {
                    $this->debug('Error ocured during transaction register:' . $e->getMessage());
                }
            } else {
                $this->debug("callback: returned INVALID. Transaction: ".$this->getTransactionID().", payer email: ".$this->getEmail());
            }
            return false;
        }

        // check payment status
        if($this->getPaymentStatus() != "Completed") {
            $this->debug('Payment status is NOT COMPLETED. Transaction: '.$this->getTransactionID().', payer email: '.$this->getEmail().', status: '.$this->getPaymentStatus());
            return false;
        }

        // check transaction type
        if((strpos($this->getType(), 'subscr') !== false) && ($this->getType() != 'subscr_payment')) {
            $this->debug("Ignoring this type: '".$this->getType()."'");
            return false;
        }

        // check callback validity
        $result = $this->sendBackVerification();
        $this->setStatus($result);

        if (strpos($result, self::CALLBACK_STATUS_VERIFIED) !== false) {
            $this->debug("callback: returned VERIFIED");
            return true;
        }
        else if (strpos($result, "INVALID") !== false) {
            // log for manual investigation
            $this->debug("callback: returned INVALID. Transaction: ".$this->getTransactionID().", payer email: ".$this->getEmail());
            return false;
        } else {
            // unknown response
            $this->debug("callback: unknown response: $result");
            return false;
        }
    }

    protected function sendBackVerification() {
        if (Gpf_Settings::get(PayPal_Config::TEST_MODE)==Gpf::YES) {
            $this->debug("  Test mode: skipping back verification");
            return self::CALLBACK_STATUS_VERIFIED;
        }
        $this->debug("  Sending back verification started");

        $postvars = '';
        $req = 'cmd=_notify-validate';

        foreach ($_POST as $key => $value) {
            $value = stripslashes(stripslashes($value));
            $postvars .= "$key=$value; ";
            $req .= "&$key=".urlencode($value);
        }

        $this->debug("  PayPal callback: POST variables: $postvars");

        // post back to PayPal system to validate
        $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
        $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

        if (!$fp) {
            $this->debug("  PayPal callback: HTTP error, cannot post back. Error number: $errno, Error msg: $errstr");
            return false;
        } else {
            fputs ($fp, $header . $req);

            $result = '';
            while (!feof($fp)) {
                $result .= fgets ($fp, 1024);
            }

            $this->debug("    PayPal callback was successful");
            fclose ($fp);
        }

        $this->debug("  Sending back verification ended");
        return $result;
    }

    protected function discountFromTotalcost ($totalcost, $value) {
        if (($value != '') && (is_numeric($value))) {
            return $totalcost - $value;
        }
        return $totalcost;
    }

    protected function computeTotalCost(Pap_Tracking_Request $request) {
        if ($request->getPostParam('mc_gross') != '') {
            return $this->adjustTotalCost($request->getPostParam('mc_gross'), $request);
        }
        $totalCost = 0;
        $counter = 1;
        $amount = $request->getPostParam('mc_gross_' . $counter);
        while ($amount != '') {
            $totalCost += $amount;
            $counter ++;
            $amount = $request->getPostParam('mc_gross_' . $counter);
        }
        return $this->adjustTotalCost($totalCost, $request);
    }

    /**
     *  @return Pap_Tracking_Request
     */
    protected function getRequestObject() {
        return Pap_Contexts_Action::getContextInstance()->getRequestObject();
    }

    public function readRequestVariables() {
        $request = $this->getRequestObject();
        $cookieValue = stripslashes($request->getPostParam('custom'));
        try {
            $customSeparator = Gpf_Settings::get(PayPal_Config::CUSTOM_SEPARATOR);
            if ($customSeparator != '') {
                $explodedCookieValue = explode($customSeparator, $cookieValue, 2);
                if (count($explodedCookieValue) == 2) {
                    $cookieValue = $explodedCookieValue[1];
                    if (Gpf_Settings::get(PayPal_Config::USE_COUPON)==Gpf::YES) {
                        $couponCode = $explodedCookieValue[0];
                    }
                }
            }
        } catch (Gpf_Exception $e) {
        }
        if (isset($couponCode)) {
            $this->setCoupon($couponCode);
        }
        $this->setCookie($cookieValue);
        $this->setTotalCost($this->computeTotalCost($request));
        $this->setTransactionID($request->getPostParam('txn_id'));
        $this->setSubscriptionID($request->getPostParam('subscr_id'));
        $this->setProductID($request->getPostParam('item_number'));
        $this->setType($request->getPostParam('txn_type'));
        $this->setPaymentStatus($request->getPostParam('payment_status'));
        $this->setEmail($request->getPostParam('payer_email'));
        $this->setParentTransId($request->getPostParam('parent_txn_id'));
        $this->setCurrency($request->getPostParam('mc_currency'));

        $this->readRequestAffiliateVariables($request);

        $this->readAdditionalVariables($request);

        if ($this->isRecurring()) {
            $this->setData1($this->getTransactionID());
        }
    }

    public function readRequestAffiliateVariables(Pap_Tracking_Request $request) {
        $this->setUserFirstName($request->getPostParam('first_name'));
        $this->setUserLastName($request->getPostParam('last_name'));
        $this->setUserEmail($request->getPostParam('payer_email'));
        $this->setUserCity($request->getPostParam('address_city'));
        $this->setUserAddress($request->getPostParam('address_street'));
    }
    
    public function readAdditionalVariables(Pap_Tracking_Request $request) {
        if ($this->getData1() == '') {
            $this->setData1($request->getRequestParameter('data1'));
        }
        if ($this->getData2() == '') {
            $this->setData2($request->getRequestParameter('data2'));
        }
        if ($this->getData3() == '') {
            $this->setData3($request->getRequestParameter('data3'));
        }
        if ($this->getData4() == '') {
            $this->setData4($request->getRequestParameter('data4'));
        }
        if ($this->getData5() == '') {
            $this->setData5($request->getRequestParameter('data5'));
        }
        if ($this->getCouponCode() == '') {
            $this->setCoupon($request->getRequestParameter('coupon_code'));
        }
        if ($this->getChannelId() == '') {
            $this->setChannelId($request->getRequestParameter('channelId'));
        }
    }

    public function isRecurring() {
        if($this->getType() == 'subscr_payment' ||
          $this->getType() == 'recurring_payment') {
            return true;
        }

        if (Gpf_Settings::get(PayPal_Config::NORMAL_COMMISSION_AS_RECURRING_COMMISSION) == Gpf::YES &&
        $this->existRecurringCommission($this->getTransactionID())) {
            return true;
        }
        return false;
    }

    protected function processAccountIdAndVisitorId(Pap_Tracking_ActionTracker $saleTracker, $cookie) {
        parent::processAccountIdAndVisitorId($saleTracker, $cookie);
        if (Gpf_Settings::get(PayPal_Config::APPROVE_AFFILIATE) == Gpf::YES) {
            $this->debug('Automatic approval of affiliates with sale is enabled');
            $userId = $this->computeAffiliateId($saleTracker->getVisitorId(), $saleTracker->getAccountId());
            try {
                $affiliate = new Pap_Common_User();
                $affiliate->setId($userId);
                $affiliate->load();
                if ($affiliate->getStatus() == Pap_Common_Constants::STATUS_PENDING) {
                    $affiliate->setStatus(Pap_Common_Constants::STATUS_APPROVED);
                    $affiliate->update();
                }
            } catch (Gpf_Exception $e) {
                $this->debug('Error occured during approving affiliate with id=' . $userId);
            }
        }
    }

    protected function existRecurringCommission($orderId) {
        try {
            $commissions = Pap_Features_RecurringCommissions_Main::getRecurringSelect($orderId)->getAllRows();
            if ($commissions->getSize() > 0) {
                return true;
            }
        } catch (Gpf_Exception $e) {
        }
        return false;
    }

    protected function allowUseRecurringCommissionSettings() {
        return (Gpf_Settings::get(PayPal_Config::USE_RECURRING_COMMISSION_SETTINGS) == Gpf::YES);
    }

    public function getOrderID() {
        if($this->isRecurring()) {
            return $this->getSubscriptionID();
        } else {
            return $this->getTransactionID();
        }
    }

    protected function isAffiliateRegisterAllowed() {
        return (Gpf_Settings::get(PayPal_Config::REGISTER_AFFILIATE) == Gpf::YES);
    }

    protected function prepareSales(Pap_Tracking_ActionTracker $saleTracker) {
        if ($this->getRequestObject()->getPostParam('num_cart_items') > 0
            && Gpf_Settings::get(PayPal_Config::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION) == GPF::NO) {
                $this->prepareSeparateCartItems($saleTracker);
        } else {
            parent::prepareSales($saleTracker);
        }
    }

    private function prepareSeparateCartItems(Pap_Tracking_ActionTracker $saleTracker) {
        $request = $this->getRequestObject();
        $numItems = $request->getPostParam('num_cart_items');

        for ($i=1; $i<=$numItems; $i++) {
            $sale = $saleTracker->createSale();
            $sale->setTotalCost($this->adjustTotalCost($request->getPostParam('mc_gross_'.$i), $request, $i));
            $sale->setOrderID($this->getOrderID());
            $sale->setProductID($request->getPostParam('item_number'.$i));
            $sale->setData1($this->getData1());
            $sale->setData2($this->getData2());
            $sale->setData3($this->getData3());
            $sale->setData4($this->getData4());
            $sale->setData5($this->getData5());
            $sale->setCoupon($this->getCouponCode());
            $sale->setCurrency($this->getCurrency());
            $sale->setChannelId($this->getChannelId());
            if ($this->getStatus()!='') {
                $sale->setStatus($this->getStatus());
            }
            if($this->getAffiliateID() != '' && $this->getCampaignID() != '') {
                $sale->setAffiliateID($this->getAffiliateID());
                $sale->setCampaignID($this->getCampaignID());
            }

            $this->setVisitorAndAccount($saleTracker, $this->getAffiliateID(), $this->getCampaignID(), $this->getCookie());
        }
    }
    
    private function adjustTotalCost($originalTotalCost, Pap_Tracking_Request $request, $index = '') {
        $totalCost = $originalTotalCost;
        $this->debug('Original totalcost: '.$totalCost);
        if (Gpf_Settings::get(PayPal_Config::DISCOUNT_FEE)==Gpf::YES) {
            $totalCost = $this->discountFromTotalcost($totalCost, $request->getPostParam('mc_fee'.$index));
            $this->debug('Discounting fee ('.$request->getPostParam('mc_fee'.$index).') from totalcost.');
        }
        if (Gpf_Settings::get(PayPal_Config::DISCOUNT_TAX)==Gpf::YES) {
            $totalCost = $this->discountFromTotalcost($totalCost, $request->getPostParam('tax'.$index));
            $this->debug('Discounting tax ('.$request->getPostParam('tax'.$index).') from totalcost.');
        }
        if (Gpf_Settings::get(PayPal_Config::DISCOUNT_HANDLING)==Gpf::YES) {
            $totalCost = $this->discountFromTotalcost($totalCost, $request->getPostParam('mc_handling'.$index));
            $this->debug('Discounting handling ('.$request->getPostParam('mc_handling'.$index).') from totalcost.');
        }
        if (Gpf_Settings::get(PayPal_Config::DISCOUNT_SHIPPING)==Gpf::YES) {
            if ($index == '' && $request->getPostParam('mc_shipping') == '') {
                $totalCost = $this->discountFromTotalcost($totalCost, $request->getPostParam('shipping'));
                $this->debug('Discounting shipping ('.$request->getPostParam('shipping').') from totalcost.');
            } else {
                $totalCost = $this->discountFromTotalcost($totalCost, $request->getPostParam('mc_shipping'.$index));
                $this->debug('Discounting shipping ('.$request->getPostParam('mc_shipping'.$index).') from totalcost.');
            }
        }
        $this->debug('Totalcost after discounts: '.$totalCost);
        return $totalCost;
    }
}
?>
