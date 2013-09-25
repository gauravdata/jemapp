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
class WebMoney_Tracker extends Pap_Tracking_CallbackTracker {

    private $webMoneyHash;
    private $merchantHash;
    private $test;
    
    /**
     * @return WebMoney_Tracker
     */
    public function getInstance() {
        $tracker = new WebMoney_Tracker();
        $tracker->setTrackerName("WebMoney");
        return $tracker;
    }
    
    private function isSignatureValid() {
        if (strtoupper($this->webMoneyHash) != strtoupper($this->merchantHash)) {
            $this->debug('  bad signature - notification failed');
            $params = '';
            foreach ($_POST as $k => $v) {
            	$params .= ' '.$k.'=>'.$v;            	
            }
            $this->debug('  bad signature params: '.$params);
            return false;
        }
        $this->debug('  notification successful');
        return true;
    }

    public function checkCookie() {
        if ($this->isPrerequest() || !$this->isSignatureValid() ||
            $this->isTest() || $this->getCookie() == '') {
            return false;
        }
        
        return true;
    }
    
    private function isTest() {
        if (Gpf_Settings::get(WebMoney_Config::ALLOW_TEST_SALES) == Gpf::YES) {
            return false;
        }
        
        if ($this->test != '0') {
            $this->debug('  testing mode');
            return true;
        }
         
        return false;   
    }
    
    private function isPrerequest() {
        if (isset($_POST['LMI_PREREQUEST'])) {
            $this->debug('  prerequest post - ended');
            return true;
        }
        return false;
    }
    
    
    public function readRequestVariables() {        
        $this->setCookie($_POST['PAP_COOKIE']);
        $this->setTotalCost($_POST['LMI_PAYMENT_AMOUNT']);                              
        $this->setTransactionID($_POST['LMI_PAYMENT_NO']);
        
        $this->test = $_POST['LMI_MODE'];
        
        $this->webMoneyHash = $_POST['LMI_HASH'];
        $this->merchantHash = md5($_POST['LMI_PAYEE_PURSE'].$_POST['LMI_PAYMENT_AMOUNT'].$_POST['LMI_PAYMENT_NO'].$_POST['LMI_MODE'].
                $_POST['LMI_SYS_INVS_NO'].$_POST['LMI_SYS_TRANS_NO'].$_POST['LMI_SYS_TRANS_DATE'].
                Gpf_Settings::get(WebMoney_Config::SECRET_KEY).$_POST['LMI_PAYER_PURSE'].$_POST['LMI_PAYER_WM']);

        $this->setStatus(Pap_Common_Constants::STATUS_PENDING);
        
        $this->setData5($_POST['LMI_SYS_TRANS_NO']);
    }

    public function getOrderID() {
        return $this->getTransactionID();
    }
    
    public function finishTransaction($newStatus) {      
        $transaction = new Pap_Db_Transaction();
        $transaction->setOrderId($_POST['LMI_PAYMENT_NO']);
        $transaction->setData5($_POST['LMI_SYS_TRANS_NO']);
        try {
            $transaction->loadFromData(array(Pap_Db_Table_Transactions::ORDER_ID, Pap_Db_Table_Transactions::DATA5));
        } catch (Gpf_DbEngine_NoRowException $e) {
            $this->debug('No such transaction with order id: ' . $transaction->getOrderId() . ' and data5: ' . $transaction->getData5() . '. Changing status ended.');
            return;
        }
        $transaction->setStatus($newStatus);
        $transaction->update();
    }

    public function checkStatus() {
        return true;
    }
}
?>
