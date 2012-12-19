<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: CommissionTypes.class.php 29292 2010-09-13 08:40:56Z mbebjak $
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
class Pap_Db_Table_CommissionTypes extends Gpf_DbEngine_Table {
    const ID = 'commtypeid';
    const TYPE = 'rtype';
    const STATUS = 'rstatus';
    const NAME = 'name';
    const APPROVAL = 'approval';
    const CODE = 'code';
    const RECURRENCEPRESETID = 'recurrencepresetid';
    const ZEROORDERSCOMMISSION = 'zeroorderscommission';
    const SAVEZEROCOMMISSION = 'savezerocommission';
    const CAMPAIGNID = 'campaignid';
    const FIXEDCOSTVALUE = 'fixedcostvalue';
    const FIXEDCOSTTYPE = 'fixedcosttype';
    const COUNTRYCODES = 'countrycodes';
    const PARENT_COMMISSIONTYPE_ID = 'parentcommtypeid';

    private static $instance;

    /**
     * @return Pap_Db_Table_CommissionTypes
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('pap_commissiontypes');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::TYPE, self::CHAR, 1);
        $this->createColumn(self::STATUS, self::CHAR, 1);
        $this->createColumn(self::NAME, self::CHAR, 40);
        $this->createColumn(self::APPROVAL, self::CHAR, 1);
        $this->createColumn(self::CODE, self::CHAR, 20);
        $this->createColumn(self::RECURRENCEPRESETID, self::CHAR, 8);
        $this->createColumn(self::ZEROORDERSCOMMISSION, self::CHAR, 1);
        $this->createColumn(self::SAVEZEROCOMMISSION, self::CHAR, 1);
        $this->createColumn(self::CAMPAIGNID, self::CHAR, 8);
        $this->createColumn(self::FIXEDCOSTTYPE, self::CHAR, 1);
        $this->createColumn(self::FIXEDCOSTVALUE, self::FLOAT);
        $this->createColumn(self::COUNTRYCODES, 'text');
        $this->createColumn(self::PARENT_COMMISSIONTYPE_ID, self::CHAR, 8);
    }

    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(
                                    array(self::CODE, self::CAMPAIGNID, self::TYPE, self::COUNTRYCODES),
                                    $this->_("Action code must be unique in campaign")));
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(
                                    array(self::NAME, self::CAMPAIGNID, self::TYPE, self::COUNTRYCODES),
                                    $this->_("Action name must be unique in campaign")));
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Commissions::TYPE_ID, new Pap_Db_Commission());
    }

    /**
     *
     * @param $campaignId
     * @param $commissionType
     * @param $affiliateId
     * @return Gpf_Data_RecordSet
     */
    public function getAllUserCommissionTypes($campaignId = null, $commissionType = null, $affiliateId = null) {
        $selectBuilder = $this->getAllCommissionTypesSelect($campaignId, $commissionType);
        $selectBuilder->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'c', 'ct.'.self::CAMPAIGNID.'=c.'.Pap_Db_Table_Campaigns::ID);
        if (Gpf_Session::getAuthUser()->getAccountId() != Gpf_Db_Account::DEFAULT_ACCOUNT_ID) {
            $selectBuilder->where->add(Pap_Db_Table_Campaigns::ACCOUNTID, '=', Gpf_Session::getAuthUser()->getAccountId());
        }
        if ($affiliateId !== null && $affiliateId !== '') {
            $selectBuilder->from->addLeftJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
                'ct.'.self::CAMPAIGNID.'=cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID);
            $selectBuilder->from->addLeftJoin(Pap_Db_Table_UserInCommissionGroup::getName(), 'uicg',
                'cg.'.Pap_Db_Table_CommissionGroups::ID.'=uicg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID);
            $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
                $subCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
                $subCondition->add('uicg.'.Pap_Db_Table_UserInCommissionGroup::USER_ID, '=', $affiliateId);
                $subCondition->add('uicg.'.Pap_Db_Table_UserInCommissionGroup::STATUS, '=', 'A');
            $condition->addCondition($subCondition,  'OR');
            $condition->add('c.'.Pap_Db_Table_Campaigns::TYPE, '=', 'P', 'OR');
            $selectBuilder->where->addCondition($condition);
            $selectBuilder->groupBy->add('ct.'.self::ID);
        }
        return $selectBuilder->getAllRows();
    }

    /**
     *
     * @param $campaignId
     * @param $commissionType
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    private function getAllCommissionTypesSelect($campaignId = null, $commissionType = null) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('ct.'.self::ID, 'commtypeid');
        $selectBuilder->select->add('ct.'.self::TYPE, 'rtype');
        $selectBuilder->select->add('ct.'.self::STATUS, 'rstatus');
        $selectBuilder->select->add('ct.'.self::NAME, 'name');
        $selectBuilder->select->add('ct.'.self::APPROVAL, 'approval');
        $selectBuilder->select->add('ct.'.self::CODE, 'code');
        $selectBuilder->select->add('ct.'.self::RECURRENCEPRESETID, 'recurrencepresetid');
        $selectBuilder->select->add('ct.'.self::COUNTRYCODES, self::COUNTRYCODES);
        $selectBuilder->select->add('ct.'.self::CAMPAIGNID, self::CAMPAIGNID);
        $selectBuilder->select->add('ct.'.self::PARENT_COMMISSIONTYPE_ID, self::PARENT_COMMISSIONTYPE_ID);
        $selectBuilder->select->add('ct.'.self::ZEROORDERSCOMMISSION, 'zeroorderscommission');
        $selectBuilder->select->add('ct.'.self::SAVEZEROCOMMISSION, 'savezerocommission');
        $selectBuilder->select->add('ct.'.self::FIXEDCOSTTYPE, 'fixedcosttype');
        $selectBuilder->select->add('ct.'.self::FIXEDCOSTVALUE, 'fixedcostvalue');
        $selectBuilder->from->add(self::getName(), 'ct');
        
        if ($commissionType !== null && $commissionType !== '') {
            $selectBuilder->where->add('ct.'.self::TYPE, '=', $commissionType);
        }
        if ($campaignId !== null && $campaignId !== '') {
            $selectBuilder->where->add('ct.'.self::CAMPAIGNID, '=', $campaignId);
        }
        $selectBuilder->orderBy->add('ct.'.self::STATUS, false);
        $selectBuilder->orderBy->add('ct.'.self::TYPE);
        $selectBuilder->orderBy->add('ct.'.self::COUNTRYCODES);
        $selectBuilder->orderBy->add('ct.'.self::NAME);
        return $selectBuilder;
    }

    /**
     * @param String $campaignId
     * @param String $commissionType
     * @return Gpf_Data_RecordSet
     */
    public function getAllCommissionTypes($campaignId = null, $commissionType = null) {
        $result = new Gpf_Data_RecordSet('id');

        $selectBuilder = $this->getAllCommissionTypesSelect($campaignId, $commissionType);

        $result->load($selectBuilder);
        return $result;
    }

    /**
     * @return Pap_Db_CommissionType
     */
    public static function getReferralCommissionType() {
    	$commissionType = new Pap_Db_CommissionType();
    	$commissionType->setType(Pap_Db_Transaction::TYPE_REFERRAL);
    	$commissionType->loadFromData(array(self::TYPE));

    	return $commissionType;
    }

    /**
     * Load commissionType from campaignId and type
     *
     * @throws Gpf_DbEngine_NoRowException
     * @throws Gpf_DbEngine_TooManyRowsException
     *
     * @return Pap_Db_CommissionType
     */
    public function getCommissionType($campaignId, $type) {
        $commissionType = new Pap_Db_CommissionType();
        $commissionType->setCampaignId($campaignId);
        $commissionType->setType($type);
        $commissionType->loadFromData(array(Pap_Db_Table_CommissionTypes::CAMPAIGNID, Pap_Db_Table_CommissionTypes::TYPE));

        return $commissionType;
    }

	/**
	 * @param $commType
	 * @return boolean
	 */
	public static function isSpecialType($rtype) {
		return in_array($rtype, self::getSpecialTypesArray());
	}

	/**
	 * @return array
	 */
	public static function getSpecialTypesArray() {
		return array(Pap_Common_Constants::TYPE_EXTRABONUS,
		Pap_Common_Constants::TYPE_SIGNUP,
		Pap_Common_Constants::TYPE_REFUND,
		Pap_Common_Constants::TYPE_CHARGEBACK,
		Pap_Common_Constants::TYPE_REFERRAL);
	}
}
?>
