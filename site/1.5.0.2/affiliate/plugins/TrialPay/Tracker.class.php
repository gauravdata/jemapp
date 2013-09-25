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
class TrialPay_Tracker extends Pap_Tracking_CallbackTracker {

    /**
     * @return TrialPay_Tracker
     */
    public function getInstance() {
        $tracker = new TrialPay_Tracker();
        $tracker->setTrackerName("TrialPay");
        return $tracker;
    }

    public function checkStatus() {
        if($this->getPaymentStatus() != "Processed") {
            $this->debug('Payment status is not Processed. Status: '.$this->getPaymentStatus().'. Transaction: '.$this->getTransactionID().', payer email: '.$this->getEmail());
            return false;
        }
        
        $this->debug("Payment successful.");
        return true;
    }

    protected function readXmlData() {
        $post_data = file_get_contents('php://input');   
        return $post_data; 
    }

    public function readRequestVariables() {
        $input = $this->readXmlData();
        $this->debug("Input get: ".$input);
        try {
            $xml = new SimpleXMLElement($input);
        } catch (Exception $e) {
            $this->setPaymentStatus("Failed");
            $this->debug('Wrong XML format.');
            return;
        }

        $cookieValue = (string)$xml->sub_id;
        try {
            $customSeparator = Gpf_Settings::get(TrialPay_Config::SEPARATOR);
            if ($customSeparator != '') {
                $explodedCookieValue = explode($customSeparator, $cookieValue, 2);
                if (count($explodedCookieValue) == 2) {
                    $cookieValue = $explodedCookieValue[1];   
                }
            }
        } catch (Gpf_Exception $e) {
            $this->debug("Bad Separated value");
        }
        
        // assign posted variables to local variables
        
        $this->setCookie($cookieValue);
        $this->setTotalCost((string)$xml->total);
                              
        $this->setEmail((string)$xml->email);
        $this->setTransactionID((string)$xml->order_id);
        $this->setProductID((string)$xml->item_id);
        
        $this->setPaymentStatus('Processed');
        
    }

    
    
    public function getOrderID() {
        return $this->getTransactionID();
    }
}   
?>
