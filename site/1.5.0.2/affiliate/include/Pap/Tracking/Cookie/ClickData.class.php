<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Sale.class.php 20226 2008-08-27 09:18:01Z mfric $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Tracking_Cookie_ClickData extends Pap_Tracking_Cookie_Base {
    
    /**
     * property names are so short because they are directly encoded to JSON
     */
    public $cl = null;
    public $ba = null;
    public $pb = null;
    public $ts = null;
    public $rf = null;
    public $ip = null;
    public $d1 = null;
    public $d2 = null;
    public $ch = null;
    
    public function __construct() {
    }
    
    public function setClick(Pap_Db_RawClick $click) {
        $this->cl = $click->getId();
        $this->ba = $click->getBannerId();
        $this->pb = $click->getParentBannerId();
        $this->ts = $click->getDateTimestamp();
        $this->rf = substr(Pap_Tracking_Request::encodeRefererUrl($click->getRefererUrl()), 0, 80);
        $this->ip = $click->getIp();
        $this->d1 = $click->getData1();
        $this->d2 = $click->getData2();
        $this->ch = $click->getChannel();
    }
    
    /**
     * @return Pap_Db_RawClick
     * @throws Gpf_Exception
     */
    public function getClick() {
        $click = new Pap_Db_RawClick();
        $click->setId($this->getClickId());
        $click->load();
        return $click;
    }
        
    public function getClickId() {
        return $this->cl;
    }
    
    public function getParentBannerId() {
        return $this->pb;
    }
    
    public function getBannerId() {
        return $this->ba;
    }
    
    public function getTimestamp() {
        return $this->ts;
    }
    
    public function getReferrerUrl() {
        return Pap_Tracking_Request::decodeRefererUrl($this->rf);
    }
    
    public function getIp() {
        return $this->ip;
    }
    
    public function getData1() {
        return $this->d1;
    }
    
    public function getData2() {
        return $this->d2;
    }
    
    public function getChannelId() {
        return $this->ch;
    }
    
    public function equals(Pap_Tracking_Cookie_ClickData $clickData) { 
        return (
        $this->getBannerId() == $clickData->getBannerId() &&
        $this->getChannelId() == $clickData->getChannelId() &&
        $this->getTimestamp() == $clickData->getTimestamp());
    }
}
?>
