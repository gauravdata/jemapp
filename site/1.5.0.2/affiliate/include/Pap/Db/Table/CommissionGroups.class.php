<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: CommissionGroups.class.php 29493 2010-10-07 12:16:48Z iivanco $
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
class Pap_Db_Table_CommissionGroups extends Gpf_DbEngine_Table {
    const ID = 'commissiongroupid';
    const IS_DEFAULT = 'isdefault';
    const NAME = 'name';
    const CAMPAIGN_ID = 'campaignid';
    const COOKIE_LIFE_TIME = 'cookielifetime';
    const PRIORITY = 'priority';

    private static $instance;

    /**
     * @return Pap_Db_Table_CommissionGroups
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('pap_commissiongroups');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::IS_DEFAULT, 'char', 1);
        $this->createColumn(self::NAME, 'char', 60);
        $this->createColumn(self::CAMPAIGN_ID, 'char', 8);
        $this->createColumn(self::COOKIE_LIFE_TIME, 'int', 0);
        $this->createColumn(self::PRIORITY, 'int');
    }

    protected function initConstraints() {
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Commissions::GROUP_ID, new Pap_Db_Commission());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID, new Pap_Db_UserInCommissionGroup());
    }

    /**
     * @param String $campaignId
     *
     * @return Gpf_Data_RecordSet
     */
    public function getAllCommissionGroups($campaignId) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(self::ID, 'commissiongroupid');
        $selectBuilder->select->add(self::NAME, 'name');
        $selectBuilder->select->add(self::IS_DEFAULT, 'isdefault');
        $selectBuilder->from->add(self::getName());
        $selectBuilder->where->add(self::CAMPAIGN_ID, '=', $campaignId);

        return $selectBuilder->getAllRows();
    }

    public function getUserCommissionGroup($campaignId, $userId) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('cg.'.Pap_Db_Table_CommissionGroups::ID, 'commissiongroupid');
        $selectBuilder->from->add(Pap_Db_Table_Campaigns::getName(), 'ca');
        $selectBuilder->from->addInnerJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
            'ca.'.Pap_Db_Table_Campaigns::ID.'=cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID);
        $selectBuilder->from->addInnerJoin(Pap_Db_Table_UserInCommissionGroup::getName(), 'ucg',
            'cg.'.Pap_Db_Table_CommissionGroups::ID.'=ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID);
        $selectBuilder->where->add('ca.'.Pap_Db_Table_Campaigns::ID, '=', $campaignId);
        $selectBuilder->where->add('ucg.'.Pap_Db_Table_UserInCommissionGroup::USER_ID, '=', $userId);

        try {
            $commissionGroupId = $selectBuilder->getOneRow();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return null;
        }

        return $commissionGroupId->get('commissiongroupid');
    }

    public function getUserInCommissionGroup($campaignId, $userId) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('ucg.'.Pap_Db_Table_UserInCommissionGroup::ID, 'usercommgroupid');
        
        $selectBuilder->from->add(Pap_Db_Table_UserInCommissionGroup::getName(), 'ucg');
        $selectBuilder->from->addInnerJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
            'ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID.'=cg.'.Pap_Db_Table_CommissionGroups::ID);
        
        $selectBuilder->where->add('cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, '=', $campaignId);
        $selectBuilder->where->add('ucg.'.Pap_Db_Table_UserInCommissionGroup::USER_ID, '=', $userId);

        try {
            $userInCommisionGroupId = $selectBuilder->getOneRow();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return null;
        }

        return $userInCommisionGroupId->get('usercommgroupid');
    }

    /**
     * @service commission_group read
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function getAllCommissionGroupsForAllCampaigns(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $select = $this->getCommissionGroupsSelect();
        try {
            $commissionGroupsData = $select->getAllRows();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return $form;
        }

        $commissionGroupsData->addColumn('commissiongroupvalue', '');
        $cTable = Pap_Db_Table_Commissions::getInstance();
        $rsCommissions = $cTable->getAllCommissionsInCampaign();

        foreach ($commissionGroupsData as $commissionGroupData) {
            $commissionGroupData->set('commissiongroupvalue', $cTable->getCommissionsDescription($commissionGroupData->get(Pap_Db_Table_Campaigns::ID),
            $rsCommissions, $commissionGroupData->get(Pap_Db_Table_CommissionGroups::ID)));
        }

        $form->setField('commissionGroups', '', $commissionGroupsData->toObject());

        return $form;
    }

    /**
     * @service commission_group read
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function getAllCommissionGroupsForCampaign(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $select = $this->getCommissionGroupsSelect($params->get('campaignid'));
        try {
            $commissionGroupsData = $select->getAllRows();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return $form;
        }

        $cTable = Pap_Db_Table_Commissions::getInstance();
        $rsCommissions = $cTable->getAllCommissionsInCampaign();

        $commissionGroups = new Gpf_Data_RecordSet();
        $commissionGroups->setHeader(array('id', 'name', 'commissiongroupvalue'));

        foreach ($commissionGroupsData as $commissionGroupData) {
            $commissionGroups->add(array($commissionGroupData->get(Pap_Db_Table_CommissionGroups::ID),
            Pap_Db_Table_CommissionGroups::NAME,
            $commissionGroupData->set('commissiongroupvalue', $cTable->getCommissionsDescription($commissionGroupData->get(Pap_Db_Table_Campaigns::ID),
            $rsCommissions, $commissionGroupData->get(Pap_Db_Table_CommissionGroups::ID)))));
        }

        $form->setField('commissionGroups', '', $commissionGroupsData->toObject());

        return $form;
    }

    private function getCommissionGroupsSelect($campaignId = null) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('ca.'.Pap_Db_Table_Campaigns::ID);
        $select->select->add('cg.'.Pap_Db_Table_CommissionGroups::ID);
        $select->select->add('cg.'.Pap_Db_Table_CommissionGroups::NAME);
        $select->from->add(Pap_Db_Table_Campaigns::getName(), 'ca');
        $select->from->addInnerJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
           'ca.'.Pap_Db_Table_Campaigns::ID.'=cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID);
        if ($campaignId != null) {
            $select->where->add('cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, '=', $campaignId);
        }

        return $select;
    }
}
?>
