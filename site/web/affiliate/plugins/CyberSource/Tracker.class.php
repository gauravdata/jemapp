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
class CyberSource_Tracker extends Pap_Tracking_CallbackTracker {
    
    private $reasonCode;
    
    protected function getReasonCode() {
        return $this->reasonCode;
    }
    
    protected function setReasonCode($value) {
        $this->reasonCode = $value;
    }
    
    /**
     * @return Netbilling_Tracker
     */
    public function getInstance() {
        $tracker = new CyberSource_Tracker();
        $tracker->setTrackerName("CyberSource");
        return $tracker;
    }
    
    protected function setPendingTransaction() {
        $this->setStatus(Pap_Common_Constants::STATUS_PENDING);
    }
    
    public function checkStatus() {
        $code = $this->getPaymentStatus();
        if ($code == "REVIEW") {
            $this->setPendingTransaction();
            $this->debug('Transaction pending, reason: ' . $this->getReasonCode());
            return true;
        }
        if (($code == "REJECT") || ($code == "ERROR")) {
            $this->debug('Transaction rejected or other error occured, reason code:' . $this->getReasonCode());
            return false;
        }
        
        return true;
    }
    
    protected function getRequestObject() {
        return Pap_Contexts_Action::getContextInstance()->getRequestObject();
    }

    public function readRequestVariables() {
        $request = $this->getRequestObject();
        
        $customUserFieldNumber = Gpf_Settings::get(CyberSource_Config::CUSTOM_FIELD_NUMBER);
        
        $this->setCookie($request->getPostParam('merchantDefinedData' . $customUserFieldNumber));
        $this->setTotalCost($request->getPostParam('orderAmount'));
        $this->setEmail($request->getPostParam('billTo_email'));
        $this->setTransactionID($request->getPostParam('orderNumber'));
        $this->setPaymentStatus($request->getPostParam('decision'));
        $this->setReasonCode($request->getPostParam('reasonCode'));
    }

    public function getOrderID() {
        return $this->getTransactionID();
    }
}
?>
