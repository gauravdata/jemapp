<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: ClickImpression.class.php 31991 2011-04-07 12:20:05Z mkendera $
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
abstract class Pap_Db_ClickImpression extends Gpf_DbEngine_Row {
	
	const STATUS_RAW = 'R';
	const STATUS_UNIQUE = 'U';
	const STATUS_DECLINED = 'D';
	
    protected function init() {
        $this->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
        $this->setBannerId('');
        $this->setCampaignId('');
        $this->setUserId('');
        $this->setParentBannerId('');
        $this->setCountryCode('');
        $this->setChannel('');
        $this->setData1('');
        $this->setData2('');
        parent::init();
    }
    
    public function setRaw($value) {
        $this->set(Pap_Db_Table_ClicksImpressions::RAW, $value);
    }

    public function setUnique($value) {
        $this->set(Pap_Db_Table_ClicksImpressions::UNIQUE, $value);
    }
    
    public function getRaw() {
        return $this->get(Pap_Db_Table_ClicksImpressions::RAW);
    }

    public function getUnique() {
        return $this->get(Pap_Db_Table_ClicksImpressions::UNIQUE);
    }

    public function setTime($time) {
        $this->set(Pap_Stats_Table::DATEINSERTED, $time);
    }
        
    public function getTime() {
        return $this->get(Pap_Stats_Table::DATEINSERTED);
    }

    public function setAccountId($id) {
        $this->set(Pap_Stats_Table::ACCOUNTID, $id);
    }

    public function getAccountId() {
        return $this->get(Pap_Stats_Table::ACCOUNTID);
    }

    public function getUserId() {
        return $this->get(Pap_Stats_Table::USERID);
    }
    
    public function setUserId($id) {
        $this->set(Pap_Stats_Table::USERID, $id);
    }
    
    public function getCampaignId() {
        return $this->get(Pap_Stats_Table::CAMPAIGNID);
    }

    public function setCampaignId($id) {
        $this->set(Pap_Stats_Table::CAMPAIGNID, $id);
    }
    
    public function getBannerId() {
        return $this->get(Pap_Stats_Table::BANNERID);
    }

    public function setBannerId($id) {
        $this->set(Pap_Stats_Table::BANNERID, $id);
    }

    public function setParentBannerId($id) {
        $this->set(Pap_Stats_Table::PARENTBANNERID, $id);
    }

    public function setCountryCode($code) {
        $this->set(Pap_Stats_Table::COUNTRYCODE, $code);
    }
    
    public function getCountryCode() {
        return $this->get(Pap_Stats_Table::COUNTRYCODE);
    }

    public function setData1($value) {
        $this->set(Pap_Stats_Table::CDATA1, $value);
    }

    public function setData2($value) {
        $this->set(Pap_Stats_Table::CDATA2, $value);
    }
    
    public function setChannel($value) {
        $this->set(Pap_Stats_Table::CHANNEL, $value);
    }

    public function addRaw() {
        $this->addRawCount(1);
    }

    public function addRawCount($count) {
        $this->setRaw($this->getRaw()+$count);
    }
        
    public function addUnique() {
        $this->addUniqueCount(1);
    }
    
    public function addUniqueCount($count) {
        $this->setUnique($this->getUnique()+$count);
    }
    
    public function mergeWith(Pap_Db_ClickImpression $clickImpression) {
        $this->addRawCount($clickImpression->getRaw());
        $this->addUniqueCount($clickImpression->getUnique());
    }
}
?>
