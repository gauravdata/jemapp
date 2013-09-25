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
class Pap_Db_VisitorAffiliate extends Gpf_DbEngine_Row {

    const TYPE_ACTUAL = 'A';

    function __construct() {
        parent::__construct();
    }
    
    protected function init() {
        $this->setTable(Pap_Db_Table_VisitorAffiliates::getInstance());
        parent::init();
    }

    /**
     * @return Pap_Tracking_Common_VisitorAffiliateCollection
     */
    public function loadCollectionFromRecordset(Gpf_Data_RecordSet $rowsRecordSet) {
        return $this->fillCollectionFromRecordset(new Pap_Tracking_Common_VisitorAffiliateCollection(), $rowsRecordSet);
    }
    
    public function setId($value) {
        return $this->set(Pap_Db_Table_VisitorAffiliates::ID, $value);
    }

    public function getId() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::ID);
    }

    public function getVisitorId() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::VISITORID);
    }

    public function setVisitorId($value) {
        $this->set(Pap_Db_Table_VisitorAffiliates::VISITORID, $value);
    }

    public function getUserId() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::USERID);
    }

    public function setUserId($value) {
        $this->set(Pap_Db_Table_VisitorAffiliates::USERID, $value);
    }

    public function getBannerId() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::BANNERID);
    }

    public function setBannerId($value) {
        $this->set(Pap_Db_Table_VisitorAffiliates::BANNERID, $value);
    }

    public function getCampaignId() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::CAMPAIGNID);
    }

    public function setCampaignId($value) {
        $this->set(Pap_Db_Table_VisitorAffiliates::CAMPAIGNID, $value);
    }

    public function getDateVisit() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::DATEVISIT);
    }

    public function setDateVisit($value) {
        $this->set(Pap_Db_Table_VisitorAffiliates::DATEVISIT, $value);
    }

    public function getData1() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::DATA1);
    }

    public function setData1($value) {
        $this->set(Pap_Db_Table_VisitorAffiliates::DATA1, $value);
    }

    public function getData2() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::DATA2);
    }

    public function setData2($value) {
        $this->set(Pap_Db_Table_VisitorAffiliates::DATA2, $value);
    }

    public function getIp() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::IP);
    }

    public function setIp($value) {
        $this->set(Pap_Db_Table_VisitorAffiliates::IP, $value);
    }

    public function setReferrerUrl($value) {
        $this->set(Pap_Db_Table_VisitorAffiliates::REFERRERURL, $value);
    }

    public function getReferrerUrl() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::REFERRERURL);
    }

    public function setType($value) {
        $this->set(Pap_Db_Table_VisitorAffiliates::TYPE, $value);
    }

    public function getType() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::TYPE);
    }

    public function getChannelId() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::CHANNELID);
    }

    public function setChannelId($value) {
        $this->set(Pap_Db_Table_VisitorAffiliates::CHANNELID, $value);
    }

    public function setActual($actual = true) {
        if ($this->isActual() == $actual) {
            return;
        }
        $this->setType($actual ? self::TYPE_ACTUAL : '');
    }

    public function isActual() {
        return $this->getType() == self::TYPE_ACTUAL;
    }
    
    public function isValid() {
        return $this->getValidTo() >= Gpf_Common_DateUtils::now();
    }

    public function getValidTo() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::VALIDTO);
    }

    public function setValidTo($value) {
        $this->set(Pap_Db_Table_VisitorAffiliates::VALIDTO, $value);
    }
    
    public function setAccountId($accountId) {
    	$this->set(Pap_Db_Table_VisitorAffiliates::ACCOUNTID, $accountId);
    }
    
    public function getAccountId() {
        return $this->get(Pap_Db_Table_VisitorAffiliates::ACCOUNTID);
    }
    
    public function toString() {
        return 'visitorId: '.$this->getVisitorId().", ".
               'userid: '.$this->getUserId().", ".
               'bannerid: '.$this->getBannerId().", ".
               'validto: '.$this->getValidTo().
               ($this->isActual() ? ' ACTUAL' : '');
               
    }
}

?>
