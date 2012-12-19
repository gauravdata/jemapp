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
class Eway_Tracker extends Pap_Tracking_CallbackTracker {

    /**
     * @return Eway_Tracker
     */
    public function getInstance() {
        $tracker = new Eway_Tracker();
        $tracker->setTrackerName("Eway");
        return $tracker;
    }

    public function checkStatus() {
        if ($this->getPaymentStatus() == 'True') {
            return true;
        }
        return false;
    }
    
    protected function getRequestObject() {
        return Pap_Contexts_Action::getContextInstance()->getRequestObject();
    }
    
    protected function readXmlData() {
        $post_data = file_get_contents('php://input');
        if (get_magic_quotes_gpc()) {
            $post_data = stripslashes($post_data);
        }   
        return $post_data; 
    }
    
    protected function getXmlElementByName($name, $elements) {
        foreach ($elements as $element) {
            if ($element->getName() == $name) {
                return $element;
            }
        }
        return false;
    }
    
    private function parseAmount($amount) {
        return trim(str_replace(',','',$amount));
    }
    
    private function inputVariables($customValue, $amount, $trnId, $trnStatus){
        $cookieValue = stripslashes($customValue);
        try {
            $customSeparator = Gpf_Settings::get(Eway_Config::CUSTOM_SEPARATOR);
            if ($customSeparator != '') {
                $explodedCookieValue = explode($customSeparator, $cookieValue, 2);
                if (count($explodedCookieValue) == 2) {
                    $cookieValue = $explodedCookieValue[1];
                }
            }
        } catch (Gpf_Exception $e) {
        }
        $this->setCookie(trim($cookieValue));
        $this->setTotalCost($this->parseAmount($amount) / 100);
        $this->setTransactionID(trim($trnId));
        $this->setPaymentStatus(trim($trnStatus));
    }

    public function readRequestVariables() {
        if (Gpf_Settings::get(Eway_Config::RESPONSE_TYPE)=='xml') {
            $input = $this->readXmlData();
        
            $this->debug("Input get: " . $input);
            try {
                $xml = new SimpleXMLElement($input);
            } catch (Exception $e) {
                $this->setPaymentStatus("False");
                $this->debug('Wrong XML format.');
                return;
            }
            $this->inputVariables($this->getXmlElementByName('ewayTrxnOption'.Gpf_Settings::get(Eway_Config::CUSTOM_FIELD_NUMBER),$xml), $this->getXmlElementByName('ewayReturnAmount', $xml), $this->getXmlElementByName('ewayTrxnReference', $xml), $this->getXmlElementByName('ewayTrxnStatus', $xml));
            return;
        } else {
            $request = $this->getRequestObject();
            $this->inputVariables($request->getPostParam('eWAYoption'.Gpf_Settings::get(Eway_Config::CUSTOM_FIELD_NUMBER)), $request->getPostParam('eWAYReturnAmount'), $request->getPostParam('ewayTrxnReference'), $request->getPostParam('ewayTrxnStatus'));
        }
    }

    public function isRecurring() {
        return false;
    }

    public function getOrderID() {
        return $this->getTransactionID();
    }
}
?>
