<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric, Maros Galik
 *   @since Version 1.0.0
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
class Pap_Tracking_ActionTracker extends Pap_Tracking_ClickTracker {

    /**
     * @var array<Pap_Tracking_ActionRequestObject>
     */
    protected $actionInstances = array();
     
    /**
     * @var Pap_Tracking_ActionTracker
     */
    private static $instance = null;

    protected $saleCookie;

    protected $visitorId = null;
    protected $accountId = null;
    
    protected $trackMethod = null;

    /**
     * @return Pap_Tracking_ActionTracker
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new Pap_Tracking_ActionTracker();
        }
        return self::$instance;
    }
    
    /**
     * creates new sale object
     *
     * @return Pap_Tracking_ActionObject
     */
    public function createSale() {
        $obj = new Pap_Tracking_ActionObject();
        $this->actionInstances[] = $obj;
        return $obj;
    }

    /**
     * creates new sale object
     *
     * @return Pap_Tracking_ActionObject
     */
    public function createAction($code) {
        $obj = new Pap_Tracking_ActionObject($code);
        $this->actionInstances[] = $obj;
        return $obj;
    }

    /**
     * sets value of the cookie to be used
     *
     * @param string $value
     */
    public function setCookieValue($value) {
        $this->saleCookie = $value;
    }

    /**
     * registers all created sales
     */
    public function register() {
        if(!is_array($this->actionInstances)) {
            return;
        }

        $context = Pap_Contexts_Action::getContextInstance();

        if(count($this->actionInstances) <= 0) {
            $context->debug('No sales to register, create sale using createSale() function first!');
            return;
        }

        $this->track();
    }
    
    protected function isCronRunning() {
        $taskRunner = new Gpf_Tasks_Runner();
        return $taskRunner->isRunningOK();
    }
    
    /**
     * 
     * @return boolean
     */
    protected function isOfflineVisitProcessingEnabled() {
        return (Gpf_Settings::get(Pap_Settings::VISIT_OFFLINE_PROCESSING_DISABLE)=='Y')?false:true;        
    }
    
    /**
     * 
     * @return boolean
     */
    protected function isOfflineVisitProcessingSet() {
        return (Gpf_Settings::get(Pap_Settings::VISIT_OFFLINE_PROCESSING_DISABLE)=='')?false:true;        
    }
    
    
    /**
     * 
     * @return Pap_Db_Visit
     */
    protected function createVisit() {
        $visit = new Pap_Db_Visit(Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT));
        $visit->setSaleParams($this->getJSONsale());
        $visit->setCookies($this->getCookieParams());
        $visit->setVisitorId($this->getVisitorId());
        $visit->setAccountId($this->getAccountId());
        $visit->setTrackMethod($this->getTrackMethod());
        $visit->setIp(@$_SERVER['REMOTE_ADDR']);
        $visit->setUserAgent(@$_SERVER['HTTP_USER_AGENT']);
        $visit->setDateVisit(Gpf_Common_DateUtils::now());
        $visit->setReferrerUrl($this->referrer);
        $visit->setVisitorIdHash(sprintf('%u', crc32($this->getVisitorId())) % 255);
        return $visit;
    }
    
    protected function getVisitProcessor() {
        return new Pap_Tracking_Visit_Processor();
    } 

    public function track() {
        $visit = $this->createVisit();        
        if (($this->isOfflineVisitProcessingSet() && !$this->isOfflineVisitProcessingEnabled()) 
            || (!$this->isOfflineVisitProcessingSet() && !$this->isCronRunning())) {
            $processor = $this->getVisitProcessor();
            $processor->runOnline($visit);
        } else {
            $visit->save();
        }
    }

    protected function getJSONsale() {
        $json = new Gpf_Rpc_JSON();
        return $json->encode($this->actionInstances);
    }
    
    /**
     * @var array<Pap_Tracking_ActionRequestObject>
     */
    public function getActionInstances() {
        return $this->actionInstances;
    }
    
    public function setRefererUrl($value) {
        $this->referrer = $value;
    }
    
    public function setVisitorId($value) {
    	$this->visitorId = $value;
    }
    
    public function getVisitorId() {
        if ($this->visitorId != null) {
            return $this->visitorId;
        }
        if (@$_COOKIE[Pap_Tracking_Cookie::VISITOR_ID] != null &&
            @$_COOKIE[Pap_Tracking_Cookie::VISITOR_ID] != '') {
            return @$_COOKIE[Pap_Tracking_Cookie::VISITOR_ID];
        }
        return md5(uniqid(mt_rand(), true));
    }
    
    public function setAccountId($accountId) {
        $this->accountId = $accountId;
    }
    
    public function getAccountId() {
        return $this->accountId;
    }

    public function setTrackMethod($value) {
        $this->trackMethod = $value;
    }

    public function getTrackMethod() {
        return $this->trackMethod;
    }

    protected function getCookieParams() {
        $cookieParams = '';

        if ($this->saleCookie != null) {
            $cookieParams = '||'.Pap_Tracking_Cookie::SALE_COOKIE_NAME.'=' . $this->saleCookie;
        } else if (isset($_COOKIE[Pap_Tracking_Cookie::SALE_COOKIE_NAME])) {
            $cookieParams = '||'.Pap_Tracking_Cookie::SALE_COOKIE_NAME.'=' . urlencode(@$_COOKIE[Pap_Tracking_Cookie::SALE_COOKIE_NAME]);
        }

        if (isset($_COOKIE[Pap_Tracking_Cookie::FIRST_CLICK_COOKIE_NAME])) {
            $cookieParams .= '||'.Pap_Tracking_Cookie::FIRST_CLICK_COOKIE_NAME.'=' . urlencode(@$_COOKIE[Pap_Tracking_Cookie::FIRST_CLICK_COOKIE_NAME]);
        }

        if (isset($_COOKIE[Pap_Tracking_Cookie::LAST_CLICK_COOKIE_NAME])) {
            $cookieParams .= '||'.Pap_Tracking_Cookie::LAST_CLICK_COOKIE_NAME.'=' . urlencode(@$_COOKIE[Pap_Tracking_Cookie::LAST_CLICK_COOKIE_NAME]);
        }

        return stripslashes($cookieParams);
    }

    public function callNotifySale() {
        echo "PostAffTracker.notifySale();\n";
    }
}
?>
