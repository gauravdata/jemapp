<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Sale.class.php 26182 2009-11-19 14:03:54Z mbebjak $
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
class Pap_Tracking_Cookie_Sale extends Pap_Tracking_Cookie_Base {
    
    /**
     * property names are so short because they are directly encoded to JSON
     */
    
    public $c = null;
    public $a = null;
    public $ch = null;
    
    public function __construct() {
    }
    
    public function decode($string) {
        try {
            parent::decode($string);
        } catch (Pap_Tracking_Exception $e) {
            $this->decodeOldFormat($string);
        }
    }
    
    /**
     * @param string $cookieValue
     */
    private function decodeOldFormat($cookieValue) {
    	if($cookieValue == '') {
            throw new Pap_Tracking_Exception("Sale cookie does not exist");
    	}
    		
        $arr = explode('_', $cookieValue);
        if(!is_array($arr) || count($arr) < 2 || count($arr) > 3) {
            throw new Pap_Tracking_Exception("Sale cookie format is incorrect");
        }
        $this->a = $arr[0];
        $this->c = $arr[1];
        if(count($arr) > 2) {
            $this->ch = $arr[2];
        }
    }
    
    public function setCampaignId($campaignId) {
        $this->c = $campaignId;
    }
    
    public function setAffiliateId($affiliateId) {
        $this->a = $affiliateId;
    }

    public function setChannelId($channelId) {
        $this->ch = $channelId;
    }
    
    public function getCampaignId() {
        return $this->c;
    }
    
    public function getAffiliateId() {
        return $this->a;
    }

    public function getChannelId() {
        return $this->ch;
    }
}
?>
