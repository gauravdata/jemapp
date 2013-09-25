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
class SafePay_Tracker extends Pap_Tracking_CallbackTracker {
    
    const CURL = 'curl';
    const SOCKET = 'socket';

    protected $passPhrase;
    protected $confirmationId;
    protected $quantity;
    protected $amount;
    
    
    
    private function setPassPhrase($passPhrase) {
        $this->passPhrase = $passPhrase;
    }
    
    private function getPassPhrase() {
        return $this->passPhrase;
    }
    
    private function setConfirmationId($confirmationId) {
        $this->confirmationId = $confirmationId;
    }
    
    private function getConfirmationId() {
        return $this->confirmationId;
    }
    
    private function setQuantity($quantity) {
        $this->quantity = $quantity;
    }
    
    private function getQuantity() {
        return $this->quantity;
    }
    
    private function setAmount($amount) {
        $this->amount = $amount;
    }
    
    private function getAmount() {
        return $this->amount;
    }
    
    private function getConfirmMethod() {
        return self::CURL;
    }
    
    private function getConfirmScriptData() {
        return array(
            "host" => "https://www.safepaysolutions.com",
            "path" => "index.php",
            "ssl" => 1 // 1 for https, 0 for http
        );
    }
    
    /**
     * @return SafePay_Tracker
     */
    public function getInstance() {
        $tracker = new SafePay_Tracker();
        $tracker->setTrackerName("SafePay");
        return $tracker;
    }
    

    /**
     * 
     * @return Pap_Tracking_Request
     */    
    protected function getRequestObject() {
        return Pap_Contexts_Action::getContextInstance()->getRequestObject();
    }
    
    
    public function checkStatus() {
        $this->debug("Checking status...");
        if($this->getPaymentStatus() == 1 && ($this->getType() == '_ipn_payment' || $this->getType() == '')){
            if($this->getPassPhrase() != '' && (strlen($this->getPassPhrase()) > 0) && (strtoupper(md5(Gpf_Settings::get(SafePay_Config::SECRET_PASSPHRASE))) != $this->getPassPhrase())){
              $this->debug("Secret passphrase error\n");
              return false;
            }
            if(!is_numeric($this->getAmount()) || $this->getAmount() < 0){
              $this->debug("Amount error (".$this->getAmount().")\n");
              return false;
            }
            if(!is_numeric($this->getQuantity()) || $this->getQuantity() < 0){
              $this->debug("Item quantity error (".$this->getQuantity().")\n");
              return false;
            }
            if ($this->getTransactionID()=='' || !preg_match("/^([0-9]){8}-([0-9]){1,}$/", $this->getTransactionID())){
              $this->debug("Transaction ID error (".$this->getTransactionID().")\n");
              return false;
            }
            $confirmed = $this->confirmTransaction($this->getConfirmMethod(), $this->getConfirmScriptData(), $this->getConfirmationId(), $this->getTransactionID(), $this->getTotalCost());
            if($confirmed == 1){
                return true;
            }
        } elseif ($this->getType() == '_ipn_subscription') {
            $this->debug("IPN subscription not supported");
            return false;
        }
        return false;
    }
    
    function confirmTransaction($confirmUsing, $confirmScript, $confirmationID, $transactionID, $totalAmount){
        $confirmScriptFull = $confirmScript["host"] . "/" . $confirmScript["path"];
    
        if(is_numeric($confirmationID) && $confirmationID > 0){
          $data = "confirmID=$confirmationID&trid=$transactionID&amount=$totalAmount";
          if($confirmUsing == self::CURL){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $confirmScriptFull);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $answer = curl_exec($ch);
            $curlErr = curl_error($ch);
            if(strlen($curlErr) > 0) $this->debug("CURL ERROR on <$confirmScriptFull>: " . $curlErr . "\n");
            curl_close($ch);
            $this->debug("Confirmation script answer: " . $answer . "\n");
            if(strlen($answer) > 0 && strpos($answer, "SUCCESS") !== false) $answer = 1;
            else $answer = 0;
          }
          elseif($confirmUsing == self::SOCKET){
            if($confirmScript["ssl"]){
              $port = "443";
              $ssl = "ssl://";
            }
            else $port = "80";
            $fp = @fsockopen($ssl . $confirmScript["host"], $port, $errnum, $errstr, 30); 
            if(!$fp){
              $this->debug("SOCKET ERROR! $errnum: $errstr\n");
              $answer = 0;
            } 
            else{ 
              fputs($fp, "POST {$confirmScript[path]} HTTP/1.1\r\n"); // PATH
              fputs($fp, "Host: {$confirmScript[host]}\r\n");         // HOST
              fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
              fputs($fp, "Content-length: ".strlen($data)."\r\n"); 
              fputs($fp, "Connection: close\r\n\r\n"); 
              fputs($fp, $data . "\r\n\r\n"); 
              while(!feof($fp)) $answer .= @fgets($fp, 1024);
              fclose($fp); 
              $this->debug("Confirmation script answer: " . $answer . "\n");
              if(strlen($answer) > 0 && strpos($answer, "SUCCESS") !== false) $answer = 1;
              else $answer = 0;
            }
          }
        }
        else $answer = 1;
        return $answer;
      }
      
    public function isRecurring() {
        return false;
    }
    
    public function getOrderID() {
        return $this->getTransactionID();
    }
    
    public function readRequestVariables() {
        $request = $this->getRequestObject();
        
        $this->setCookie(stripslashes($request->getPostParam('custom'.SafePay_Config::CUSTOM_FIELD_NUMBER)));
        $this->setAmount($request->getPostParam('iamount'));
        $this->setProductID($request->getPostParam('itemNum'));
        $this->setPaymentStatus($request->getPostParam('result'));
        $this->setPassPhrase($request->getPostParam('passPhrase'));
        $this->setData1($request->getPostParam('ipayer'));
        $this->setType($request->getPostParam('_ipn_act'));
        $this->setTransactionID($request->getPostParam('tid'));
        
        if (($this->getType() == '_ipn_payment') || ($this->getType() == '')) {
            $this->setConfirmationId($request->getPostParam('confirmID'));
            $this->setQuantity($request->getPostParam('iquantity'));
            $this->setTotalCost($this->getQuantity()*$this->getAmount());
        }
    }
    
}
?>
