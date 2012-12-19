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
class Netbilling_Tracker extends Pap_Tracking_CallbackTracker {

    /**
     * @return Netbilling_Tracker
     */
    public function getInstance() {
        $tracker = new Netbilling_Tracker();
        $tracker->setTrackerName("Netbilling");
        return $tracker;
    }

    protected function refundChargeback() {
        $transaction = new Pap_Common_Transaction(); 
        $transaction->processRefundChargeback($this->getTransactionID(), Pap_Db_Transaction::TYPE_REFUND, '',
            '', 0, true);
    }
    
    protected function setPendingTransaction() {
        $this->setStatus(Pap_Common_Constants::STATUS_PENDING);
    }
    
    public function checkStatus() {
        $code = $this->getPaymentStatus();
        
        if ($code == "I") {
            $this->setPendingTransaction();
            $this->debug('Transaction pending');
            return true;
        }
        if (($code == "F") || ($code == "0")) {
            $this->debug('Transaction failed');
            return false;
        }
        if ($code == "R") {
            $this->refundChargeback();
            return false;
        }
        
        return true;
    }

    public function readRequestVariables() {
        $this->debug('netbill: '.$_REQUEST['Ecom_UserData_Pap'].'______'.$_REQUEST['Ecom_Ezic_TransactionId'].'__;;;;__'.$_REQUEST['Ecom_Receipt_Description']);
        $this->setCookie($_REQUEST['Ecom_UserData_Pap']);
        $this->setTotalCost($_REQUEST['Ecom_Cost_Total']);

        $this->setEmail($_REQUEST['Ecom_BillTo_Online_Email']);
        $this->setTransactionID($_REQUEST['Ecom_Ezic_TransactionId']);
        $this->setProductID($_REQUEST['Ecom_Receipt_Description']);

        $this->setPaymentStatus($_REQUEST['Ecom_Ezic_TransactionStatus']);
    }

    public function getOrderID() {
        return $this->getTransactionID();
    }
}
?>
