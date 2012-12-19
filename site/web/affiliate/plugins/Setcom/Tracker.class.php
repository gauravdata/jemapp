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
class Setcom_Tracker extends Pap_Tracking_CallbackTracker {
    
    const SETCOM_URL = 'https://www.setcom.com/secure/components/synchro.cfc?wsdl';
    const TRANSACTION_COMPLETE = 'Complete';

    private $checksum;
    private $parity;
    
    private function getChecksum() {
        return $this->checksum;
    }
    
    private function getParity() {
        return $this->parity;
    }
    
    private function setChecksum($value) {
        $this->checksum = $value;
    }
    
    private function setParity($value) {
        $this->parity = $value;
    }
    
    /**
     * @return Setcom_Tracker
     */
    public function getInstance() {
        $tracker = new Setcom_Tracker();
        $tracker->setTrackerName("Setcom");
        return $tracker;
    }

    public function checkStatus() {
        $this->debug('checking state');
        if (($this->getTransactionID()=='')||($this->getChecksum()=='')||($this->getParity()=='')) {
            return false;
        }
        return $this->verifyTransaction();
    }
    
    private function decodeXML($string) {
        $output = str_replace(array("<wddxPacket version='1.0'><header/><data><string>","</string></data></wddxPacket>"),array("",""), $string);
        return htmlspecialchars_decode($output);
    }
    
    protected function sendBackVerification() {
        $this->debug('sending back verification');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::SETCOM_URL);
        curl_setopt($ch, CURLOPT_POSTFIELDS    ,'Method=order_synchro'.
                                                '&tnxid='.$this->getTransactionID().
                                                '&checksum='.$this->getChecksum(). 
                                                '&parity='.$this->getParity(). 
                                                '&Identifier='.Gpf_Settings::get(Setcom_Config::MERCHANT_IDENTIFIER).
                                                '&Usrname='.Gpf_Settings::get(Setcom_Config::MERCHANT_USERNAME).
                                                '&Pwd='.Gpf_Settings::get(Setcom_Config::MERCHANT_PASSWORD));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        $xml_Data = curl_exec($ch);
        $xml_Data = stripslashes($xml_Data);
        $xml_Data = $this->decodeXML($xml_Data);
        return $xml_Data;
    }

    protected function verifyTransaction() {
        
        $xml_Data = $this->sendBackVerification();
        
        try {
            $xml = @(new SimpleXMLElement($xml_Data));
        } catch (Exception $e) {
            $this->debug('Wrong XML format as response from server.');
            return false;
        }
        

        if ($xml->outcome->status != self::TRANSACTION_COMPLETE) {
            $this->debug('Transaction is not complete.');
            return false;
        }
        $seller = $this->getXmlElementByName('seller',$xml);
        $buyer = $this->getXmlElementByName('buyer',$xml);
        $financial = $this->getXmlElementByName('financial',$xml);
        
        $this->setEmail($buyer->username);
        $this->setTotalCost((float)$financial->amount_total/100);
        
        
        $cookieValue = $seller->reference;
        $this->debug('totCost:'.$this->getTotalCost().', cookie:'.$cookieValue);
        try {
            $customSeparator = Gpf_Settings::get(Setcom_Config::CUSTOM_SEPARATOR);
            if ($customSeparator != '') {
                $explodedCookieValue = explode($customSeparator, $cookieValue, 2);
                if (count($explodedCookieValue) == 2) {
                    $cookieValue = $explodedCookieValue[1];
                }
            }
        } catch (Gpf_Exception $e) {
        }
        
        $this->setCookie($cookieValue);
        
        return true;
    }
    
    protected function getXmlElementByName($name, $elements) {
        foreach ($elements as $element) {
            if ($element->getName() == $name) {
                return $element;
            }
        }
        return false;
    }

    protected function getRequestObject() {
        return Pap_Contexts_Action::getContextInstance()->getRequestObject();
    }

    public function readRequestVariables() {
        $request = $this->getRequestObject();
        
        $this->setTransactionID($request->getPostParam('tnxid'));
        $this->setChecksum($request->getPostParam('checksum'));
        $this->setParity($request->getPostParam('parity'));
    }

    public function isRecurring() {
        return false;
    }

    public function getOrderID() {
        return $this->getTransactionID();
    }
}
?>
