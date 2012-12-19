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
class UltraCart_Tracker extends Pap_Tracking_CallbackTracker {

    /**
     * @return UltraCart_Tracker
     */
    public function getInstance() {
        $tracker = new UltraCart_Tracker();
        $tracker->setTrackerName("UltraCart");
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

    protected function outputError() {
        echo '99';
    }
    
    protected function outputSuccess() {
        echo '200';
    }
    
    private function computeTotalCost(SimpleXMLElement $xml) {        
        if (Gpf_Settings::get(UltraCart_Config::SHIPPING_HANDLING_SUBSTRACT) == Gpf::YES) {
            return (string)$xml->order->subtotal;
        }
        return (string)$xml->order->total;
    }
    
    public function readRequestVariables() {
        $input = $this->readXmlData();
        $this->debug("Input get: ".$input);
        try {
            $xml = new SimpleXMLElement($input);
        } catch (Exception $e) {
            $this->setPaymentStatus("Failed");
            $this->debug('Wrong XML format.');
            $this->outputError();
            return;
        }
              
        // assign posted variables to local variables
        $customField = 'custom_field_'.Gpf_Settings::get(UltraCart_Config::CUSTOM_FIELD_NUMBER);
        $this->debug("Custom field number: ".Gpf_Settings::get(UltraCart_Config::CUSTOM_FIELD_NUMBER));
        $cookieValue = (string)$xml->order->$customField;
        
        $this->setCookie($cookieValue);
        
        $this->setTotalCost($this->computeTotalCost($xml));
                              
        $this->setEmail((string)$xml->order->email);
        $this->setTransactionID((string)$xml->order->order_id);
        $this->setProductID((string)$xml->order->item->item_id);
        
        $this->setPaymentStatus((string)$xml->order->payment_status);
        $this->outputSuccess();
    }

    
    
    public function getOrderID() {
        return $this->getTransactionID();
    }
}
?>
