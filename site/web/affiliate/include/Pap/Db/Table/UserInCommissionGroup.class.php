<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: UserInCommissionGroup.class.php 27606 2010-03-23 08:22:38Z mkendera $
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
class Pap_Db_Table_UserInCommissionGroup extends Gpf_DbEngine_Table {
    const ID = 'usercommgroupid';
    const USER_ID = 'userid';
    const COMMISSION_GROUP_ID = 'commissiongroupid';
    const STATUS = 'rstatus';
    const NOTE = 'note';
    const DATE_ADDED = 'dateadded';
    
    private static $instance;

    /**
     *
     * @return Pap_Db_Table_UserInCommissionGroup
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('pap_userincommissiongroup');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::USER_ID, 'char', 8);
        $this->createColumn(self::COMMISSION_GROUP_ID, 'char', 8);
        $this->createColumn(self::STATUS, 'char', 1);
        $this->createColumn(self::NOTE, 'char', 100);
        $this->createColumn(self::DATE_ADDED, 'datetime', 0);
    }
    
    protected function initConstraints() {
    	$this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::USER_ID, self::COMMISSION_GROUP_ID)));
    }
    
    public static function removeUserFromCampaignGroups($userId, $campaignId) {
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
         
        $delete->delete->add('ucg');
         
        $delete->from->add(self::getName(), 'ucg');
        $delete->from->addInnerJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
           'ucg.'.self::COMMISSION_GROUP_ID.'=cg.'.Pap_Db_Table_CommissionGroups::ID);

        $delete->where->add('ucg.'.self::USER_ID, '=', $userId);
        $delete->where->add('cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, '=', $campaignId);
        $delete->execute();
    }
    
    /**
     *
     * @return Pap_Db_UserInCommissionGroup
     */
    public function getUserCommissionGroup($userId, $campaignId) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->addAll(Pap_Db_Table_UserInCommissionGroup::getInstance(), 'ucg');

        $selectBuilder->from->add(Pap_Db_Table_UserInCommissionGroup::getName(), 'ucg');
        $selectBuilder->from->addInnerJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
        'ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID .'='. 'cg.'.Pap_Db_Table_CommissionGroups::ID);
        
        $selectBuilder->where->add('ucg.'.Pap_Db_Table_UserInCommissionGroup::USER_ID, '=', $userId);
        $selectBuilder->where->add('cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, '=', $campaignId);
                
        $userInGroup = new Pap_Db_UserInCommissionGroup();
        $userInGroup->setStatus(Pap_Features_PerformanceRewards_Condition::STATUS_APPROVED);
        try {
            $record = $selectBuilder->getOneRow();
            $userInGroup->fillFromRecord($record);
            $userInGroup->setPersistent(true);
        } catch (Gpf_DbEngine_NoRowException $e) {
            $userInGroup->setUserId($userId);
            $userInGroup->setCommissionGroupId(Pap_Db_Table_Campaigns::getInstance()->getDefaultCommissionGroup($campaignId)->getId());
        }
        return $userInGroup;
    }

    public function getUsersInCommissionGroupCount($campaignId) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('COUNT(*)', 'count');
        $selectBuilder->from->add(Pap_Db_Table_UserInCommissionGroup::getName(), 'ucg');
        $selectBuilder->from->addInnerJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
             'cg.'.Pap_Db_Table_CommissionGroups::ID.'=ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID);
        $selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'pu',
             'pu.'.Pap_Db_Table_Users::ID.'=ucg.'.Pap_Db_Table_UserInCommissionGroup::USER_ID);
        $selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'u',
             'u.'.Gpf_Db_Table_Users::ID.'=pu.'.Pap_Db_Table_Users::ACCOUNTUSERID);
        $selectBuilder->where->add('cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, '=', $campaignId);
        $selectBuilder->where->add('pu.' . Pap_Db_Table_Users::TYPE, '=', Pap_Application::ROLETYPE_AFFILIATE);
        $selectBuilder->where->add('u.'.Gpf_Db_Table_Users::STATUS, '=', Gpf_Db_User::APPROVED);
        $selectBuilder->where->add('pu.'.Pap_Db_Table_Users::DELETED, '=', Gpf::NO);

        try {
            $commissionGroupId = $selectBuilder->getOneRow();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return null;
        }
        return $selectBuilder->getOneRow()->get('count');
    }
    
    public static function getStatus($campaignId, $userId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Pap_Db_Table_UserInCommissionGroup::STATUS);
        $select->from->add(Pap_Db_Table_CommissionGroups::getName(), 'cg');
        $select->from->addInnerJoin(Pap_Db_Table_UserInCommissionGroup::getName(), 'ucg',
            'cg.'.Pap_Db_Table_CommissionGroups::ID.'=ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID);
        $select->where->add('cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, '=', $campaignId);
        $select->where->add('ucg.'.Pap_Db_Table_UserInCommissionGroup::USER_ID, '=', $userId);
        $row = $select->getOneRow();
        
        return $row->get(Pap_Db_Table_UserInCommissionGroup::STATUS);
    }
}
?>
