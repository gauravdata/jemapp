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
class RoboKassa_Tracker extends Pap_Tracking_CallbackTracker {

    private $crc;

    private $shpItem;
    
    /**
     * @return RoboKassa_Tracker
     */
    public function getInstance() {
        $tracker = new RoboKassa_Tracker();
        $tracker->setTrackerName("RoboKassa");
        return $tracker;
    }
    
    public function checkNotification() {
        // merchant pass2
        $mrh_pass2 = Gpf_Settings::get(RoboKassa_Config::SECURE_PASS2);
        
        // build own CRC 
        $my_crc = md5($this->getTotalCost().':'.$this->getTransactionID().':'.$mrh_pass2.':Shp_item='.$this->shpItem.':shp_papCookie='.$this->getCookie());        
        $this->debug('ID: '.$this->getTransactionID());
        if (strtoupper($my_crc) != strtoupper($this->crc)) {
            $this->debug('  bad signature - notification failed'); 
            return false; 
        }   
        return true;  
    }

    public function checkCookie() {
        if (!$this->checkNotification()) {
            return false;
        }
        
        if ($this->getCookie() == '') {
            return false;
        } 
        return true;
    }
   
    protected function outputSuccess() {
        echo "OK".$this->getTransactionID(); 
    }
    
    public function readRequestVariables() {        
        $this->crc = $_REQUEST['SignatureValue'];
        $this->shpItem = $_REQUEST['Shp_item'];
        $this->setCookie($_REQUEST['shp_papCookie']);
        $this->setTotalCost($_REQUEST['OutSum']);                              
        $this->setTransactionID($_REQUEST['InvId']);
        $this->outputSuccess();
    }

    public function getOrderID() {
        return $this->getTransactionID();
    }
    
    public function checkStatus() {
        return true;
    }
}
?>
