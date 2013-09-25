<?php
/**
 * Copyright (C) 2007 Google Inc.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
 
class GoogleCheckout_GoogleLog {
    
    const L_OFF = 0;
    const L_ERR = 1; 
    const L_RQST = 2;
    const L_RESP = 4;
    const L_ERR_RQST = 3; //self::self::L_ERR + self::self::L_RQST
    const L_ALL = 7; //self::self::L_ERR + self::self::L_RQST + self::self::L_RESP;
    
    var $errorLogFile;
    var $messageLogFile;
 // L_ALL (err+requests+responses), self::L_ERR, self::L_RQST, self::L_RESP, self::L_OFF    
    var $logLevel = self::L_ERR_RQST;
    
  /**
   * SetLogFiles
   */
  function GoogleLog($errorLogFile, $messageLogFile, $logLevel=self::L_ERR_RQST, $die=true){
    $this->logLevel = $logLevel;
    if($logLevel == self::L_OFF) {
      $this->logLevel = self::L_OFF;
    }
    $this->logLevel = $logLevel;;
  }
  
  function LogError($log){
    if($this->logLevel & self::L_ERR){
        Gpf_Log::error(sprintf("\n%s:- %s\n",date("D M j G:i:s T Y"),$log));
      return true;
    }
    return false;
  }
  
  function LogRequest($log){
    if($this->logLevel & self::L_RQST){
      Gpf_Log::error(sprintf("\n%s:- %s\n",date("D M j G:i:s T Y"),$log)); 
       return true;
    }
    return false;
  }
  
  function LogResponse($log) {
    if($this->logLevel & self::L_RESP){
      $this->LogRequest($log);
      return true;
    }
    return false;
  }
}
?>
