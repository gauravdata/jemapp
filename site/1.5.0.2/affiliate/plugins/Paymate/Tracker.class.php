<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
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
class Paymate_Tracker extends Pap_Tracking_CallbackTracker {
      
    /**
     * @return Paymate_Tracker
     */
    public function getInstance() {
        $tracker = new Paymate_Tracker();
        $tracker->setTrackerName("Paymate");
        return $tracker;
    }
    
    protected function setPendingTransaction() {
        $this->setStatus(Pap_Common_Constants::STATUS_PENDING);
    }

    public function checkStatus() {
        
        if ($this->getPaymentStatus() == "PP") {
            $this->setPendingTransaction();
            return true;
        }
        if ($this->getPaymentStatus() == "PA") {
            return true;
        }
        if (($this->getPaymentStatus() == "PD") || ($this->getPaymentStatus() == "")) {
            return false;
        }
        
        return false;
    }

    protected function getRequestObject() {
        return Pap_Contexts_Action::getContextInstance()->getRequestObject();
    }

    public function readRequestVariables() {
        $request = $this->getRequestObject();
        $cookieValue = stripslashes($request->getPostParam('ref'));
        $refNumber = '';
         
        try {
            $customSeparator = Gpf_Settings::get(Paymate_Config::CUSTOM_SEPARATOR);
            if ($customSeparator != '') {
                $explodedCookieValue = explode($customSeparator, $cookieValue, 2);
                if (count($explodedCookieValue) == 2) {
                    $cookieValue = $explodedCookieValue[1];
                    $refNumber = $explodedCookieValue[0];
                }
            }
        } catch (Gpf_Exception $e) {
        }
        $this->setCookie($cookieValue);
        $this->setProductID($refNumber);
        $this->setTotalCost($request->getPostParam('paymentAmount'));
        $this->setTransactionID($request->getPostParam('transactionID'));
        $this->setPaymentStatus($request->getPostParam('responseCode'));
        $this->setEmail($request->getPostParam('buyerEmail'));
    }

    public function isRecurring() {
        return false;
    }

    public function getOrderID() {
    	return $this->getTransactionID();
    }
}
?>
