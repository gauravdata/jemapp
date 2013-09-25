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
class SolidTrustPay_Tracker extends Pap_Tracking_CallbackTracker {

    const TRANSACTION_COMPLETE = 'COMPLETE';
    
    /**
     * @return SolidTrustPay_Tracker
     */
    public function getInstance() {
        $tracker = new SolidTrustPay_Tracker();
        $tracker->setTrackerName("SolidTrustPay");
        return $tracker;
    }

    public function checkStatus() {
        if ($this->getPaymentStatus() != self::TRANSACTION_COMPLETE) {
            $this->debug('Transaction failed');
            return false;
        }
        if ($this->computeHash() != $this->getRequestObject()->getPostParam('hash')) {
            $this->debug('Hash do not match! Transaction was probably altered, or you entered wrong secondary password.');
            return false;
        }
        return true;
    }
    
    protected function getRequestObject() {
        return Pap_Contexts_Action::getContextInstance()->getRequestObject();
    }
    
    protected function computeHash() {
        return MD5($this->getTransactionID() . ":" . MD5(MD5(Gpf_Settings::get(SolidTrustPay_Config::SECONDARY_PASSWORD).'s+E_a*')) . ":" . $this->getTotalCost() .
            ":" . $this->getRequestObject()->getPostParam('merchantAccount') . ":" . $this->getData2('payerAccount'));
    }

    public function readRequestVariables() {
        $request = $this->getRequestObject();
        $cookieValue = stripslashes($request->getPostParam('user'.Gpf_Settings::get(SolidTrustPay_Config::CUSTOM_ITEM_NUMBER)));
        try {
            $customSeparator = Gpf_Settings::get(SolidTrustPay_Config::CUSTOM_SEPARATOR);
            if ($customSeparator != '') {
                $explodedCookieValue = explode($customSeparator, $cookieValue, 2);
                if (count($explodedCookieValue) == 2) {
                    $cookieValue = $explodedCookieValue[1];
                }
            }
        } catch (Gpf_Exception $e) {
        }
        $this->setCookie($cookieValue);
        
        $this->setTotalCost($request->getPostParam('amount'));
        $this->setTransactionID($request->getPostParam('tr_id'));
        $this->setProductID($request->getPostParam('item_id'));
        $this->setPaymentStatus($request->getPostParam('status'));
        $this->setCurrency($request->getPostParam('currency'));
        $this->setData1($request->getPostParam('memo'));
        $this->setData2($request->getPostParam('payerAccount'));
    }

    public function isRecurring() {
        return false;
    }

    public function getOrderID() {
            return $this->getTransactionID();
    }
}
?>
