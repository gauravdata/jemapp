<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: CampaignForm.class.php 33889 2011-07-25 10:49:29Z iivanco $
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
class Pap_Merchants_Campaign_CampaignForm extends Gpf_View_FormService {
    /**
     * @var Pap_Common_Campaign
     */
    private $campaign;
    private $errorMsg = '';

    /**
     * @return Pap_Common_Campaign
     */
    protected function createDbRowObject() {
        $this->campaign = new Pap_Common_Campaign();
        return $this->campaign;
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return 'Campaign';
    }

    /**
     * @param Gpf_DbEngine_RowBase $dbRow
     */
    protected function setDefaultDbRowObjectValues(Gpf_DbEngine_RowBase $dbRow) {
        $dbRow->set('rtype', Pap_Common_Campaign::CAMPAIGN_TYPE_PUBLIC);
    }

    /**
     * @service campaign read
     *
     * @param $fields
     * @return Gpf_Rpc_Serializable
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = parent::load($params);
        $form->setField("commissionsexist", $this->campaign->getCommissionStatus());
        return $form;
    }

    /**
     * @service campaign write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        return parent::save($params);
    }

    protected function afterSave(Pap_Db_Campaign $dbCampaign, $saveType) {
        if ($saveType == self::EDIT) {
            $this->updateAccountOfBanners($dbCampaign);
        }
    }

    private function updateAccountOfBanners(Pap_Db_Campaign $dbCampaign) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Pap_Db_Table_Banners::getInstance());
        $select->from->add(Pap_Db_Table_Banners::getName());
        $select->where->add(Pap_Db_Table_Banners::CAMPAIGN_ID, '=', $dbCampaign->getId());

        $banner = new Pap_Db_Banner();
        $bannersCollection = $banner->loadCollectionFromRecordset($select->getAllRows());
        foreach ($bannersCollection as $banner) {
            $banner->update(array(Pap_Db_Table_Banners::ACCOUNT_ID));
        }
    }

    /**
     * @service campaign write
     *
     * @param $fields
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $fields = new Gpf_Data_RecordSet();
        $fields->loadFromArray($action->getParam("fields"));

        $action->setErrorMessage($this->_('Failed to save %s field(s) in %s(s)', '%s', $this->getDbRowObjectName()));
        $action->setInfoMessage($this->_('%s field(s) in %s(s) successfully saved', '%s', $this->getDbRowObjectName()));

        foreach ($fields as $field) {
            $dbRow = $this->createDbRowObject();
            $dbRow->setPrimaryKeyValue($field->get('id'));
            $dbRow->load();

            if ($dbRow->getIsDefault() && $field->get("name") == "rstatus" && $field->get("value") != Pap_Db_Campaign::CAMPAIGN_STATUS_ACTIVE) {
                $action->setErrorMessage($this->_('You cannot deactivate default campaign'));
                $action->addError();
                continue;
            }

            $dbRow->set($field->get("name"), $field->get("value"));
            $dbRow->save();
            $action->addOk();
        }

        return $action;
    }

    /**
     *
     * @service campaign delete
     * @param $ids
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('You cannot delete default campaign'));

        $selectedIds = array();
        foreach ($action->getIds() as $id) {
            $selectedIds[] = $id;
        }

        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->from->add(Pap_Db_Table_Campaigns::getName());
        $select->select->add(Pap_Db_Table_Campaigns::ID);
        $select->where->add(Pap_Db_Table_Campaigns::IS_DEFAULT,'=',Gpf::YES);
        $select->where->add(Pap_Db_Table_Campaigns::ID,'IN', $selectedIds);
        if ($select->getAllRows()->getSize() > 0) {
            $action->addError();
            return $action;
        }

        return parent::deleteRows($params);
    }

    /**
     * Saves capping settings for this campaign
     *
     * @service campaign write
     * @param $fields
     */
    public function saveCapping(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $campaign = new Pap_Common_Campaign();
        $campaign->set('campaignid', $form->getFieldValue("Id"));

        try {
            $campaign->load();
        } catch (Gpf_DbEngine_NoRow $e) {
            $form->setErrorMessage($this->_("Campaign does not exist"));
            return $form;
        }

        $form->fill($campaign);

        $this->checkBeforeSave($campaign, $form);

        try {
            $campaign->save();
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $form->load($campaign);
        $form->setInfoMessage($this->_("Campaign capping saved"));
        return $form;
    }

    /**
     * This function should be moved to campaign validity plugin
     */
    private function checkCampaignValidity() {
        if($row->get('rstatus') == Pap_Db_Campaign::CAMPAIGN_STATUS_ACTIVE_RESULTS) {
            if($row->get('validnumber') == '') {
                throw new Gpf_Exception($this->_("Number of transactions in 'Active until' campaign validity has to be set"));
            }
        } else {
            if($row->get('validnumber') == '') {
                $row->set('validnumber', 0);
            }
        }

        if($row->get('rstatus') == Pap_Db_Campaign::CAMPAIGN_STATUS_ACTIVE_DATERANGE) {
            if($row->get('validfrom') == '' || $row->get('validto') == '') {
                throw new Gpf_Exception($this->_("There are missing From and To dates for 'Active in date range' campaign validity"));
            }
        } else {
            if($row->get('validfrom') == '') {
                $row->setNull('validfrom');
            }
            if($row->get('validto') == '') {
                $row->setNull('validto');
            }
        }
    }

    protected function checkBeforeSave(Pap_Common_Campaign $row, Gpf_Rpc_Form $form, $operationType) {
        /**
         $this->checkCampaignValidity($row, $form, $operationType);
         */

        if($row->getIsDefault() && $row->get('rstatus') <> Pap_Db_Campaign::CAMPAIGN_STATUS_ACTIVE) {
            throw new Gpf_Exception($this->_("Error, you cannot deactivate Default campaign"));
        }

        if($row->get('rstatus') == "") {
            throw new Gpf_Exception($this->_("You have to specify status of campaign (rstatus field)"));
        }

        /*
         if ($row->getIsDefault() || $row->getCampaignType() == null) {
         if ($form->existsField('rtype') && $form->getFieldValue('rtype') != Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC) {
         throw new Gpf_Exception($this->_("Type of default campaign must be public"));
         }
         }
         */
        $row->set("dateinserted", Gpf_Common_DateUtils::now());
        if (!$form->existsField('accountid') || $form->getFieldValue('accountid') == '') {
            $row->set("accountid", Gpf_Session::getAuthUser()->getAccountId());
        }

        return true;
    }

    /**
     * @throws Gpf_Exception
     * @return int
     */
    public function computeAutomaticCommission($campaignId, $userId, $commissionTypeId, $totalcost = '', $fixedcost = '' , $tier = null){
        $commTypeValue = $this->getCommissionTypeAndValue($campaignId, $userId, $commissionTypeId, $tier);
        if($commTypeValue == false) {
            throw new Gpf_Exception($this->_('Invalid commission type').': '.$this->errorMsg);
        }
        if($fixedcost != '' && !is_numeric($fixedcost)) {
            throw new Gpf_Exception($this->_('Error, you entered invalid value for fixed cost!'));
        }

        if($fixedcost != '' && is_numeric($fixedcost))  {
            $commTypeValue['fixedcostType'] = Pap_Db_CommissionType::COMMISSION_FIXED;
            $commTypeValue['fixedcostValue'] = $fixedcost;
        }

        if($commTypeValue == false) {
            throw new Gpf_Exception($this->_('Invalid commission type'));
        }

        return $this->computeCommission($commTypeValue, $totalcost);
    }

    /**
     * @service campaign read
     *
     * @param $fields
     * @return Gpf_Rpc_Serializable
     */
    public function computeAutomaticCommissionRpc(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        try {
            $commission = $this->computeAutomaticCommission($params->get("campaignid"), $params->get("userid"), $params->get("type"),
            $params->get("totalcost"), $params->get("fixedcost"), $params->get("tier"));
        } catch (Gpf_Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $form->setField("commission", $commission['commission']);
        $form->setField("fixedcost", $commission['fixedcost']);
        $form->setInfoMessage($this->_("Commission successfuly computed"));

        return $form;
    }

    private function getCommissionTypeAndValue($campaignId, $userId, $commissionTypeId, $tier = null) {
        if (is_null($tier) || $tier == '') {
            $tier = 1;
        }

        // getting user
        if($userId == '') {
            $this->errorMsg = $this->_("User is not valid!");
            return false;
        }
        $user = new Pap_Common_User();
        $user->setPrimaryKeyValue($userId);
        try {
            $user->load();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $this->errorMsg = $this->_("User is not valid!");
            return false;
        }

        // getting campaign
        $campaign = new Pap_Common_Campaign();
        $campaign->setId($campaignId);
        try {
            $campaign->load();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $this->errorMsg = $this->_("Campaign is not valid!");
            return false;
        }
        // getting commission type
        try {
            $commissionType = new Pap_Db_CommissionType();
            $commissionType->setId($commissionTypeId);
            $commissionType->setStatus(Pap_Db_CommissionType::STATUS_ENABLED);
            $commissionType->loadFromData();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $this->errorMsg = $this->_("Transaction type is not valid or doesn't exist in this campaign!");
            return false;
        }
        $fixedcostType = $commissionType->getFixedcostType();
        $fixedcostValue = $commissionType->getFixedcostValue();
        // getting commission group
        $commGroupId = $campaign->getCommissionGroupForUser($userId);
        if($commGroupId == false) {
            $this->errorMsg = $this->_("Cannot recognize commission group for this user in campaign!");
            return false;
        }

        $rsCommissions = $campaign->getCommissionsCollection($commGroupId, $commissionType->getId());
        $commType = null;
        $commValue = null;
        foreach($rsCommissions as $record) {
            if($record->get('tier') == $tier && $record->get('subtype') == 'N') {
                $commType = $record->get('commissiontype');
                $commValue = $record->get('commissionvalue');
                break;
            }
        }

        if($commType == null) {
            $this->errorMsg = $this->_("Error getting commission settings!");
            return false;
        }

        return array('type' => $commType, 'value' => $commValue, 'fixedcostValue' => $fixedcostValue, 'fixedcostType' => $fixedcostType);
    }

    private function computeCommission($commTypeValue, $totalCost) {
        $commType = $commTypeValue['type'];
        $commValue = $commTypeValue['value'];
        $fixcostType = $commTypeValue['fixedcostType'];
        $fixcostValue = $commTypeValue['fixedcostValue'];
        $realFixedCostValue = 0;
        if ($fixcostType == Pap_Db_CommissionType::COMMISSION_PERCENTAGE) {
            $realFixedCostValue = round(($totalCost / 100) * $fixcostValue, 2);
        } else {
            $realFixedCostValue = $fixcostValue;
        }

        if($commType == Pap_Db_CommissionType::COMMISSION_PERCENTAGE) {
            if($totalCost == '' || !is_numeric($totalCost)) {
                $this->errorMsg = $this->_("Error, for percentage commissions you have to define Total cost!");
                return false;
            }

            $commissionValue = round(($commValue / 100) * ($totalCost - $realFixedCostValue), 2);
        } else {
            $commissionValue = $commValue - $realFixedCostValue;
        }
        if (Gpf_Settings::get(Pap_Settings::ALLOW_COMPUTE_NEGATIVE_COMMISSION) == Gpf::NO) {
            $commissionValue = ($commissionValue < 0 ? 0 : $commissionValue);
        }
        return array('commission' => $commissionValue, 'fixedcost' => $realFixedCostValue);
    }

    /**
     * @service campaign add
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        $form = parent::add($params);

        if ($form->isSuccessful()) {
            $this->insertDefaultCommissionTypes();
        }
        return $form;
    }

    protected function addRow(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $row) {
        $newCampaign = new Pap_Db_Campaign();
        $newCampaign->setAccountId($row->get(Pap_Db_Table_Campaigns::ACCOUNTID));
        $newCampaign->setIsDefault();
        try {
            $newCampaign->loadFromData(array(Pap_Db_Table_Campaigns::ACCOUNTID,Pap_Db_Table_Campaigns::IS_DEFAULT));
        } catch (Gpf_DbEngine_NoRowException $e){
            $row->set(Pap_Db_Table_Campaigns::IS_DEFAULT,Gpf::YES);
        }
        $row->insert();
    }

    private function insertDefaultCommissionTypes() {
        $this->insertDefaultCommissionType(Pap_Common_Constants::TYPE_CLICK);
        $this->insertDefaultCommissionType(Pap_Common_Constants::TYPE_SALE);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CampaignForm.insertDefaultCommissionTypes', $this);
    }

    public function insertDefaultCommissionType($type) {
        $this->campaign->insertCommissionType($type);
    }

    /**
     * @service campaign write
     * @return Gpf_Rpc_Action
     */
    public function setAsDefault(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to set default campaign'));
        $action->setInfoMessage($this->_('Default campaign changed'));

        foreach ($action->getIds() as $id) {
            try {
                $this->setCampaignDefault($id);
                $action->addOk();
                return $action;
            } catch (Exception $e) {
                $action->setErrorMessage($e->getMessage());
                $action->addError();
            }
        }
        return $action;
    }

    /**
     * @throws Gpf_Exception
     */
    public function setCampaignDefault($campaignId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Pap_Db_Table_Campaigns::ACCOUNTID);
        $select->from->add(Pap_Db_Table_Campaigns::getName());
        $select->where->add(Pap_Db_Table_Campaigns::ID, '=', $campaignId);
        $campaign = $select->getOneRow();

        $oldCampaignSelect = new Gpf_SqlBuilder_SelectBuilder();
        $oldCampaignSelect->select->add(Pap_Db_Table_Campaigns::ID);
        $oldCampaignSelect->from->add(Pap_Db_Table_Campaigns::getName());
        $oldCampaignSelect->where->add(Pap_Db_Table_Campaigns::ACCOUNTID, '=', $campaign->get(Pap_Db_Table_Campaigns::ACCOUNTID));
        $oldCampaignSelect->where->add(Pap_Db_Table_Campaigns::IS_DEFAULT, '=', Gpf::YES);
        $oldCampaignId = $oldCampaignSelect->getOneRow()->get(Pap_Db_Table_Campaigns::ID);

        if ($oldCampaignId == $campaignId) {
            return;
        }

        $oldCampaignUpdate = new Gpf_SqlBuilder_UpdateBuilder();
        $oldCampaignUpdate->from->add(Pap_Db_Table_Campaigns::getName());
        $oldCampaignUpdate->set->add(Pap_Db_Table_Campaigns::IS_DEFAULT, Gpf::NO);
        $oldCampaignUpdate->where->add(Pap_Db_Table_Campaigns::ID, '=', $oldCampaignId);
        $oldCampaignUpdate->executeOne();

        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Pap_Db_Table_Campaigns::getName());
        $update->set->add(Pap_Db_Table_Campaigns::IS_DEFAULT, Gpf::YES);
        $update->where->add(Pap_Db_Table_Campaigns::ID, '=', $campaignId);
        $update->executeOne();
    }
}


?>
