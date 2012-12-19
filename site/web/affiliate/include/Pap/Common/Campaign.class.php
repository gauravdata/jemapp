<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Campaign.class.php 30197 2010-11-30 11:29:37Z mjancovic $
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
class Pap_Common_Campaign extends Pap_Db_Campaign  {
    const CAMPAIGN_COMMISSION_STATUS_NOTDEFINED = Gpf::NO;
    const CAMPAIGN_COMMISSION_STATUS_DEFINED = Gpf::YES;

    function __construct() {
        parent::__construct();
    }

    /**
     * returns commission group for user.
     * If it doesn't exists, it will create default commission group and assign user to it.
     *
     * @param string $userId
     * @return string or false
     */
    public function getCommissionGroupForUser($userId) {
        $commGroupId = $this->checkUserIsInCampaign($userId);

        if($commGroupId != false) {
            return $commGroupId;
        }

        if($this->getCampaignType() == Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC) {

            $defaultCommGroupId = $this->getDefaultCommissionGroup();

            return $defaultCommGroupId;
        }
        Gpf_Log::info($this->_('No commissiongroup recognized - this is just hint: This campaign has type: %s. If, problem occured during commissiongrup recognition, you shoud check if this type is correct.', $this->getCampaignType()));

        return false;
    }

    /**
     * returns ID of default commission group for this campaign
     *
     * @return string
     */
    public function getDefaultCommissionGroup() {
        return Pap_Db_Table_Campaigns::getInstance()->getDefaultCommissionGroup($this->getId())->getId();
    }

    private function getCommissionTypeSelect($commissionType, $code = '',$countryCode = '') {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Pap_Db_Table_CommissionTypes::getInstance());
        $select->from->add(Pap_Db_Table_CommissionTypes::getName());
        $select->where->add(Pap_Db_Table_CommissionTypes::CAMPAIGNID, '=', $this->getId());
        $select->where->add(Pap_Db_Table_CommissionTypes::TYPE, '=', $commissionType);
        $select->where->add(Pap_Db_Table_CommissionTypes::STATUS, '=', Pap_Db_CommissionType::STATUS_ENABLED);
        if ($code != null && $code != '') {
            $select->where->add(Pap_Db_Table_CommissionTypes::CODE, '=', $code);
        }
        if (!strlen($countryCode)) {
            $compoundCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
            $compoundCondition->add(Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID, '=', null, 'OR');
            $compoundCondition->add(Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID, '=', '', 'OR');
            $select->where->addCondition($compoundCondition);
        } else {
            $select->where->add(Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID, '!=', null);
            $select->where->add(Pap_Db_Table_CommissionTypes::COUNTRYCODES, 'like', '%' . $countryCode . '%');
        }
        return $select;
    }

    /**
     * checks if commission type exists in this campaign
     *
     * @param string $commissionType
     * @return Pap_Db_CommissionType
     */
    public function getCommissionTypeObject($commissionType, $code = '',$countryCode = '') {
        $baseTypeSelect = $this->getCommissionTypeSelect($commissionType, $code, '');
        $commType = new Pap_Db_CommissionType();

        try {
            $baseTypesCollection = $commType->loadCollectionFromRecordset($baseTypeSelect->getAllRows());
        } catch (Gpf_DbEngine_NoRowException $e) {
            throw new Pap_Tracking_Exception("Commission type not found in campaign: " . $e->getMessage());
        }
        if ($baseTypesCollection->getSize()==0) {
            throw new Pap_Tracking_Exception("Commission type not found in campaign");
        }

        $countrySpecificTypeSelect = $this->getCommissionTypeSelect($commissionType, $code, $countryCode);
        try {
            $countryTypesCollection = $commType->loadCollectionFromRecordset($countrySpecificTypeSelect->getAllRows());
        } catch (Gpf_DbEngine_NoRowException $e) {
            return $baseTypesCollection->get(0);
        }
        if ($countryTypesCollection->getSize()==0) {
            return $baseTypesCollection->get(0);
        }
        return $countryTypesCollection->get(0);
    }

    /**
     * returns commission object
     *
     * @param int $tier
     * @param string $commissionGroupId
     * @param string $commissionTypeId
     * @return Pap_Db_Commission
     */
    public function getCommission($tier, $commissionGroupId, $commissionTypeId) {
        $commission = new Pap_Db_Commission();
        $commission->setGroupId($commissionGroupId);
        $commission->setTypeId($commissionTypeId);
        $commission->setTier($tier);

        try {
            $commission->loadFromData();
        } catch (Gpf_DbEngine_NoRowException $e) {
            throw new Gpf_Exception("Cannot load commission for tier=".$tier.", comgroupid=".$commissionGroupId.", commtypeid=".$commissionTypeId);
        }

        return $commission;
    }

    /**
     * returns recordset with commission objects
     *
     * @param string $commissionGroupId
     * @param string $commissionTypeId
     * @return Gpf_DbEngine_Row_Collection <Pap_Db_Commission>
     */
    public function getCommissionsCollection($commissionGroupId, $commissionTypeId) {
        $commission = new Pap_Db_Commission();
        $commission->setGroupId($commissionGroupId);
        $commission->setTypeId($commissionTypeId);

        try {
            return $commission->loadCollection();
        } catch (Gpf_DbEngine_NoRowException $e) {
            throw new Gpf_Exception("Cannot load commission settings for comgroupid=".$commissionGroupId.", commtypeid=".$commissionTypeId);
        }
    }

    public function getDefaultFixedCost($type, $actionCode) {
        $cmpType = $this->getCommissionTypeObject($type, $actionCode);
        return array('fixedcosttype' => $cmpType->getFixedcostType(), 'fixedcostvalue' => $cmpType->getFixedcostValue());
    }

    /**
     * function checks if for this campaigns some commission types are set
     * and if they have some commissions defined.
     * If not, it returns N, if yes it returns Y.
     *
     */
    public function getCommissionStatus() {
        $cTable = Pap_Db_Table_Commissions::getInstance();

        $commissionsExist = $cTable->checkCommissionsExistInCampaign($this->getId());
        if($commissionsExist) {
            return self::CAMPAIGN_COMMISSION_STATUS_DEFINED;
        }

        return self::CAMPAIGN_COMMISSION_STATUS_NOTDEFINED;
    }

    /**
     * @param String $campaignId
     * @return NULL|Pap_Common_Campaign
     */
    public static function getCampaignById($campaignId) {
        if($campaignId == '') {
            return null;
        }

        $campaign = new Pap_Common_Campaign();
        $campaign->setPrimaryKeyValue($campaignId);
        try {
            $campaign->load();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return null;
        }
        return $campaign;
    }

    public function insertCommissionType($type) {
        $commissionType = new Pap_Db_CommissionType();
        $commissionType->setCampaignId($this->getId());
        $commissionType->setType($type);
        $commissionType->setStatus(Pap_Db_CommissionType::STATUS_ENABLED);
        $commissionType->setApproval(Pap_Db_CommissionType::APPROVAL_AUTOMATIC);
        $commissionType->setRecurrencePresetId(Pap_Db_CommissionType::RECURRENCE_NONE);
        $commissionType->setZeroOrdersCommission(Gpf::NO);
        $commissionType->setSaveZeroCommission(Gpf::NO);
        $commissionType->insert();

        return $commissionType->getId();
    }
}
?>
