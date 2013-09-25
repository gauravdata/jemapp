<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
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
class Pap_Db_Visit extends Gpf_DbEngine_Row {

    const UNPROCESSED = 'U';
    const PROCESSED = 'P';
    const INPROCESSING = 'I';
    const INCRONPROCESSING = 'C';
    
    private $index;

    private $newVisitor = true;
    
    private $countryCode = "";

    function __construct($index = 0){
        $this->index = $index;
        parent::__construct();
    }
    
    public function setCountryCode($countryCode) {
    	$this->countryCode = $countryCode;
    }
    
    public function getCountryCode() {
    	return $this->countryCode;
    }
    
    public function setVisitorIdHash($hash) {
        $this->set(Pap_Db_Table_Visits::VISITORID_HASH, $hash);
    }
    
    public function getVisitorIdHash() {
        return $this->get(Pap_Db_Table_Visits::VISITORID_HASH);
    }

    public function setNewVisitor($value) {
        $this->newVisitor = $value;
    }

    public function isNewVisitor() {
        return $this->newVisitor;
    }

    function init() {
        $this->setTable(Pap_Db_Table_Visits::getInstance($this->index));
        parent::init();
    }

    public function setAccountId($value) {
        $this->set(Pap_Db_Table_Visits::ACCOUNTID, $value);
    }
    
    public function getVisitorId() {
        return $this->get(Pap_Db_Table_Visits::VISITORID);
    }

    public function setVisitorId($value) {
        $this->set(Pap_Db_Table_Visits::VISITORID, $value);
    }

    public function getSaleParams() {
    	return $this->get(Pap_Db_Table_Visits::SALE_PARAMS);
    }

    public function getGetParams() {
        return $this->get(Pap_Db_Table_Visits::GET_PARAMS);
    }

    public function getUrl() {
        return $this->get(Pap_Db_Table_Visits::URL);
    }

    public function getReferrerUrl() {
        return Pap_Tracking_Request::decodeRefererUrl($this->get(Pap_Db_Table_Visits::REFERRERURL));
    }

    public function getDateVisit() {
    	return $this->get(Pap_Db_Table_Visits::DATEVISIT);
    }

    public function getAnchor() {
        return $this->get(Pap_Db_Table_Visits::ANCHOR);
    }

    public function getIp() {
        return $this->get(Pap_Db_Table_Visits::IP);
    }

    public function getCookies() {
        return $this->get(Pap_Db_Table_Visits::COOKIES);
    }

    public function setCookies($value) {
        $this->set(Pap_Db_Table_Visits::COOKIES, $value);
    }

    public function setDateVisit($value) {
        $this->set(Pap_Db_Table_Visits::DATEVISIT, $value);
    }

    public function setIp($value) {
        $this->set(Pap_Db_Table_Visits::IP, $value);
    }

    public function setSaleParams($value) {
        $this->set(Pap_Db_Table_Visits::SALE_PARAMS, $value);
    }

    public function setUserAgent($value) {
        $this->set(Pap_Db_Table_Visits::USER_AGENT, $value);
    }

    public function setGetParams($value) {
    	$this->set(Pap_Db_Table_Visits::GET_PARAMS, $value);
    }
    
    public function setReferrerUrl($value) {
    	$this->set(Pap_Db_Table_Visits::REFERRERURL, $value);
    }
    
    public function setUrl($value) {
        $this->set(Pap_Db_Table_Visits::URL, $value);
    }
    
    public function setAnchor($value) {
        $this->set(Pap_Db_Table_Visits::ANCHOR, $value);
    }
    
    public function getUserAgent() {
    	return $this->get(Pap_Db_Table_Visits::USER_AGENT);
    }
    
    public function getTrackMethod() {
    	return $this->get(Pap_Db_Table_Visits::TRACKMETHOD);
    }
    
    public function setTrackMethod($value) {
        $this->set(Pap_Db_Table_Visits::TRACKMETHOD, $value);
    }
    
    public function getAccountId() {
    	return $this->get(Pap_Db_Table_Visits::ACCOUNTID);
    }
    
    public function setProcessed() {
        $this->set(Pap_Db_Table_Visits::RSTATUS, self::PROCESSED);
    }

    public function setInCronProcessing() {
        $this->set(Pap_Db_Table_Visits::RSTATUS, self::INCRONPROCESSING);
    }

    public function getId() {
        return $this->get(Pap_Db_Table_Visits::ID);
    }
}


?>
