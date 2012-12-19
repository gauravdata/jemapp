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
class Bluepay_Tracker extends Pap_Tracking_CallbackTracker {
      
    /**
     * @return Paymate_Tracker
     */
    public function getInstance() {
        $tracker = new Bluepay_Tracker();
        $tracker->setTrackerName("Bluepay");
        return $tracker;
    }
    
    protected function setPendingTransaction() {
        $this->setStatus(Pap_Common_Constants::STATUS_PENDING);
    }

    public function checkStatus() {
        
        if ($this->getPaymentStatus() == "APPROVED") {
            return true;
        }
        
        return false;
    }

    protected function getRequestObject() {
        return Pap_Contexts_Action::getContextInstance()->getRequestObject();
    }

    public function readRequestVariables() {
        $request = $this->getRequestObject();
        $cookieValue = stripslashes($request->getPostParam(Bluepay_Config::HTML_COOKIE_VARIABLE));
        $refNumber = '';
         
        try {
            $customSeparator = Gpf_Settings::get(Bluepay_Config::CUSTOM_SEPARATOR);
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
        $this->setTotalCost($request->getPostParam('AMOUNT'));
        $this->setTransactionID($request->getPostParam('INVOICE_ID'));
        $this->setPaymentStatus($request->getPostParam('Result'));
    }

    public function isRecurring() {
        return false;
    }

    public function getOrderID() {
    	return $this->getTransactionID();
    }
}
?>
