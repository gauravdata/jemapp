<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Campaign.class.php 33739 2011-07-12 06:58:23Z iivanco $
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
class Pap_Db_Campaign extends Gpf_DbEngine_Row {

    /* Campaign types */
    const CAMPAIGN_TYPE_PUBLIC = 'P';
    const CAMPAIGN_TYPE_PUBLIC_MANUAL = 'M';
    const CAMPAIGN_TYPE_ON_INVITATION = 'I';

    /* Campaign statuses */
    const CAMPAIGN_STATUS_ACTIVE = 'A';
    const CAMPAIGN_STATUS_STOPPED_INVISIBLE = 'S';
    const CAMPAIGN_STATUS_STOPPED = 'W';
    const CAMPAIGN_STATUS_DELETED = 'D';
    const CAMPAIGN_STATUS_ACTIVE_DATERANGE = 'T';
    const CAMPAIGN_STATUS_ACTIVE_RESULTS = 'L';

    /* Campaign types */
    const USER_IN_CAMPAIGN_STATUS_APPROVED = 'A';
    const USER_IN_CAMPAIGN_STATUS_PENDING = 'P';
    const USER_IN_CAMPAIGN_STATUS_DECLINED = 'D';

    function init() {
        $this->setTable(Pap_Db_Table_Campaigns::getInstance());
        parent::init();
    }

    /**
     * @return int cookie lifetime in seconds
     */
    public function getCookieLifetime() {
        return Pap_Tracking_Cookie::computeLifeTimeDaysToSeconds($this->get(Pap_Db_Table_Campaigns::COOKIELIFETIME));
    }

    /**
     * @return boolean if cookie should be overwritten
     */
    public function getOverwriteCookie() {
        return $this->get(Pap_Db_Table_Campaigns::OVERWRITECOOKIE);
    }
    
    public function resetOverwriteCookieToDefault() {
        $this->set(Pap_Db_Table_Campaigns::OVERWRITECOOKIE, 'D');
    }

    public function getId() {
        return $this->get(Pap_Db_Table_Campaigns::ID);
    }

    public function setId($value) {
        return $this->set(Pap_Db_Table_Campaigns::ID, $value);
    }

    public function getName() {
        return $this->get(Pap_Db_Table_Campaigns::NAME);
    }

    public function setName($value) {
        return $this->set(Pap_Db_Table_Campaigns::NAME, $value);
    }
    
    public function setDateInserted($value) {
        $this->set(Pap_Db_Table_Campaigns::DATEINSERTED, $value);
    }
    
    public function setType($value) {
        return $this->set(Pap_Db_Table_Campaigns::TYPE, $value);
    }
    
    public function setStatus($value) {
        return $this->set(Pap_Db_Table_Campaigns::STATUS, $value);
    }

    public function getAccountId() {
        return $this->get(Pap_Db_Table_Campaigns::ACCOUNTID);
    }
    
    public function setAccountId($value) {
        $this->set(Pap_Db_Table_Campaigns::ACCOUNTID, $value);
    }

    public function getLongDescription() {
        return $this->get(Pap_Db_Table_Campaigns::LONG_DESCRIPTION);
    }
    
    /**
     * returns campaign type
     *
     */
    public function getCampaignType() {
        return $this->get(Pap_Db_Table_Campaigns::TYPE);
    }

    public function setCampaignType($value) {
        return $this->set(Pap_Db_Table_Campaigns::TYPE, $value);
    }

    /**
     * returns campaign status
     *
     */
    public function getCampaignStatus() {
        return $this->get(Pap_Db_Table_Campaigns::STATUS);
    }

    public function setCampaignStatus($value) {
        return $this->set(Pap_Db_Table_Campaigns::STATUS, $value);
    }

    //    public function getCookieLifetime() {
    //        return $this->get(Pap_Db_Table_Campaigns::COOKIELIFETIME);
    //    }

    public function setCookieLifetime($value) {
        return $this->set(Pap_Db_Table_Campaigns::COOKIELIFETIME, $value);
    }


    public function getDescription() {
        return $this->get(Pap_Db_Table_Campaigns::DESCRIPTION);
    }
    /**
     * checks if user is in campaign, if yes, it will return valid commissionGroupID,
     * otherwise it returns false
     *
     * @param string $userId
     * @return string or false
     */
    public function checkUserIsInCampaign($userId) {
        $result = new Gpf_Data_RecordSet();
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('u.usercommgroupid', 'usercommgroupid');
        $selectBuilder->select->add('u.commissiongroupid', 'commissiongroupid');
        $selectBuilder->select->add('u.rstatus', 'rstatus');
        $selectBuilder->from->add('qu_pap_userincommissiongroup', 'u');
        $selectBuilder->from->addInnerJoin('qu_pap_commissiongroups', 'g',
            'u.commissiongroupid=g.commissiongroupid');

        $selectBuilder->where->add('g.campaignid', '=', $this->getId());
        $selectBuilder->where->add('u.userid', '=', $userId);
        $selectBuilder->limit->set(0, 1);

        $result->load($selectBuilder);

        if($result->getSize() == 0) {
            return false;
        }

        foreach($result as $record) {
            if($this->isUserCommissionGroupStatusAllowed($record->get('rstatus'))) {
                return $record->get('commissiongroupid');
            }
            break;
        }

        return false;
    }

    private function isUserCommissionGroupStatusAllowed($status) {
        if($status != Pap_Features_PerformanceRewards_Condition::STATUS_DECLINED &&
        $status != Pap_Features_PerformanceRewards_Condition::STATUS_PENDING) {
            return true;
        }
        return false;
    }

    public function delete() {
        if (Gpf_Application::isDemo() && Gpf_Application::isDemoEntryId($this->getId())) {
            throw new Gpf_Exception("Demo campaign can not be deleted");
        }
        return parent::delete();
    }

    public function insert($createDefaultCommissionGroup = true) {
        parent::insert();
        if ($createDefaultCommissionGroup) {
            $this->createDefaultCommissionGroup();
        }
    }

    private function createDefaultCommissionGroup() {
        $commissionGroup = new Pap_Db_CommissionGroup();
        $commissionGroup->setCampaignId($this->getId());
        $commissionGroup->setDefault(GPF::YES);
        $commissionGroup->setName('Default commission group');
        $commissionGroup->insert();
    }
    
    public function getIsDefault() {
        if ($this->get(Pap_Db_Table_Campaigns::IS_DEFAULT) == Gpf::YES) {
            return true;
        }
        return false;
    }
    
    public function setIsDefault($value = true) {
        if ($value) {
            $this->set(Pap_Db_Table_Campaigns::IS_DEFAULT, Gpf::YES);
            return;
        }
        $this->set(Pap_Db_Table_Campaigns::IS_DEFAULT, Gpf::NO);
    }
}

?>
