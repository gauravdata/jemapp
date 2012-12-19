<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
class MoneyBookers_Tracker extends Pap_Tracking_CallbackTracker {

    private $md5sig;

    /**
     * @return MoneyBookers_Tracker
     */
    public function getInstance() {
        $tracker = new MoneyBookers_Tracker();
        $tracker->setTrackerName("MoneyBookers");
        return $tracker;
    }
    
    public function checkNotification() {
        $this->debug('ID: '.$this->getTransactionID());
        // build own md5sig: merchant_id . transaction_id . upper_case(secret word) . mb_amount . mb_currency . status
        try {
        $my_md5sig = md5($this->getData4().$this->getTransactionID().
                      strtoupper(md5(Gpf_Settings::get(MoneyBookers_Config::SECRET_WORD))).
                      $this->getData2().$this->getData3().$this->getData1());
        } catch (Gpf_Exception $e) {
            $this->debug('md5 not created');
            $this->debug($e->getMessage());
        }
                      
        if (strtoupper($my_md5sig) != strtoupper($this->md5sig)) {
            $this->debug('  bad signature - notification failed: '.strtoupper($my_md5sig) .'-'. strtoupper($this->md5sig));
            return false;
        }
        
        return true;
    }
    
    protected function refundChargeback($transactionId, $type, $note) {
        $transaction = new Pap_Common_Transaction(); 
        $transaction->processRefundChargeback($transactionId, $type, $note, '', 0, true);
    }
    
    public function checkRefundChargeback($transactionId, $type, $note) {
        if ($this->getData1() == '-3') {
            $this->refundChargeback($this->getTransactionID(), $this->getType(), '');
            return true;
        }
        return false;
    }
    
    private function getPaymentStatusFromMoneyBookersFormat() {
    	if ($this->getData1() == '2') {
    		return Pap_Common_Transaction::PAYOUT_PAID;
    	}
    	return Pap_Common_Transaction::PAYOUT_UNPAID;
    }
    
    private function processPayoutStatus() {
        $transaction = new Pap_Common_Transaction();
        $transaction->setId($this->getTransactionID());
        try {
            $transaction->load();
        } catch (Gpf_DbEngine_NoRowException $e) {
        	$this->setPaymentStatus($this->getPaymentStatusFromMoneyBookersFormat());
        	return;
        }
        
        if ($this->checkRefundChargeback()) {
        	return;
        }
        
        $transaction->setPayoutStatus($this->getPaymentStatusFromMoneyBookersFormat());
        $transaction->save();
    }
    
    public function checkCookie() {
        if (!$this->checkNotification()) {
            return false;
        }
        
        if ($this->getCookie() == '') {
            $this->debug('ended - cookie information not found');
            return false;
        }
        
        if (!$this->processPayoutStatus()) {
        	return false;
        }
        
        return true;
    }
    
    protected function outputSuccess() {
        echo '200';
    }
    
    public function readRequestVariables() {
        $this->debug('REQUEST: '.print_r($_REQUEST, true));
       
        $this->md5sig = $_POST['md5sig'];
    	
        $this->setCookie($_POST['field'.Gpf_Settings::get(MoneyBookers_Config::FIELD_NUMBER)]);

        $this->setTotalCost($_POST['amount']);
        if (isset($_POST['transaction_id'])) {
        	$this->setTransactionID($_POST['transaction_id']);
        } else {
            $this->setTransactionID($_POST['mb_transaction_id']);
        }
        $this->setEmail($_POST['pay_from_email']);
                
        $this->setData1($_POST['status']);
        $this->setData2($_POST['mb_amount']);
        $this->setData3($_POST['mb_currency']);
        $this->setData4($_POST['merchant_id']);
        
        $this->outputSuccess();
    }

    public function getOrderID() {
        return $this->getTransactionID();
    }
    
    public function checkStatus() {
    	return true;
    }
}
?>
