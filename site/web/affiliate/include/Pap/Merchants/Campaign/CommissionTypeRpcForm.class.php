<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
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
class Pap_Merchants_Campaign_CommissionTypeRpcForm extends Gpf_Rpc_Form {
    private $commissionTypeId;
    protected $commissionGroupId;

    public function __construct(Gpf_Rpc_Params $params = null, $edit=true) {
		parent::__construct($params);
        $this->initCommissionGroupId($this->getFieldValue("CommissionGroupId"));
		if ($edit) {
            $this->commissionTypeId = $this->getFieldValue("Id");
            if($this->commissionTypeId == "") {
                throw new Exception($this->_("CommissionTypeId (Id) cannot be empty!"));
            }
		}
    }

    protected function initCommissionGroupId($commissionGroupId) {
    	$this->commissionGroupId = $commissionGroupId;
    	if($this->commissionGroupId == "") {
            throw new Exception($this->_("CommissionGroupId cannot be empty!"));
        }
    }

    public function loadForm() {
        $commissionType = new Pap_Db_CommissionType();
        $commissionType->setId($this->commissionTypeId);

        try {
            $commissionType->load();
        } catch (Gpf_DbEngine_NoRow $e) {
            throw new Exception($this->_("Commission type does not exist"));
        }

        $this->loadCommissionType($commissionType);
        $this->loadAllCommissions();

    }

    public function loadCommissionType(Pap_Db_CommissionType $commissionType) {
    	$this->setField("Id", $commissionType->getId());
       	$this->setField(Pap_Db_Table_CommissionTypes::TYPE, $commissionType->getType());
       	$this->setField(Pap_Db_Table_CommissionTypes::NAME, $commissionType->getName());
       	$this->setField(Pap_Db_Table_CommissionTypes::CODE, $commissionType->getCode());
    	$this->setField(Pap_Db_Table_CommissionTypes::STATUS, $commissionType->getStatus());
    	$this->setField(Pap_Db_Table_CommissionTypes::APPROVAL, $commissionType->getApproval());
    	$this->setField(Pap_Db_Table_CommissionTypes::RECURRENCEPRESETID, $commissionType->getRecurrencePresetId());
    	$this->setField(Pap_Db_Table_CommissionTypes::ZEROORDERSCOMMISSION, $commissionType->getZeroOrdersCommissions());
        $this->setField(Pap_Db_Table_CommissionTypes::SAVEZEROCOMMISSION, $commissionType->getSaveZeroCommissions());
    	$this->setField(Pap_Db_Table_CommissionTypes::FIXEDCOSTTYPE, $commissionType->getFixedcostType());
    	$this->setField(Pap_Db_Table_CommissionTypes::FIXEDCOSTVALUE, $commissionType->getFixedcostValue());
    }

    public function loadAllCommissions() {
        $this->loadSubTypeCommissions(Pap_Db_Table_Commissions::SUBTYPE_NORMAL);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CommissionTypeForm.loadAllCommissions', $this);
    }

    public function loadSubTypeCommissions($subType) {
        $commission = new Pap_Db_Commission();
        $commission->setGroupId($this->commissionGroupId);
        $commission->setTypeId($this->commissionTypeId);
        $commission->setSubtype($subType);
        foreach ($commission->loadCollection() as $commission) {
            $this->setField($this->preffixCommissionFormName($subType, $commission->getTier(), "commission"),
                            $commission->getCommissionValue());
            $this->setField($this->preffixCommissionFormName($subType, $commission->getTier(), "commissionType"),
                            $commission->getCommissionType());
        }
    }

    private function preffixCommissionFormName($subType, $tier, $name) {
        return $subType.'_'.$tier.'_'.$name;
    }

    public function saveForm() {
        $this->checkSaveInput();
        $commissionType = new Pap_Db_CommissionType();
        $commissionType->setId($this->commissionTypeId);

        try {
            $commissionType->load();
            $this->fill($commissionType);
        } catch (Gpf_DbEngine_NoRow $e) {
            throw new Exception($this->_("Commission type does not exist"));
        }

        $commissionType->save();

        $this->saveCommissions();
    }

    private function checkSaveInput() {
        if (!$this->existsField(Pap_Db_Table_CommissionTypes::TYPE)) {
            throw new Gpf_Exception($this->_("Field 'type' does not exist"));
        }

        $commType = $this->getFieldValue(Pap_Db_Table_CommissionTypes::TYPE);
        if($commType == Pap_Common_Constants::TYPE_CLICK
          || $commType == Pap_Common_Constants::TYPE_CPM) {
            $this->setField(Pap_Db_Table_CommissionTypes::ZEROORDERSCOMMISSION, '');
            $this->setField(Pap_Db_Table_CommissionTypes::SAVEZEROCOMMISSION, '');
            $this->setField("commissionType", Pap_Db_CommissionType::COMMISSION_FIXED);
        }
    }

     private function saveCommissions() {
        $this->saveSubtypeCommissions(Pap_Db_Table_Commissions::SUBTYPE_NORMAL);
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CommissionTypeForm.saveAllCommissions', $this);
    }



    public function saveSubtypeCommissions($subType) {
        $maximumTier = 0;
        for ($i = 1; $i < 100; $i++) {
            if(!$this->existsField($this->preffixCommissionFormName($subType, $i, "commission"))) {
                break;
            }
            $maximumTier = $i;

            $this->saveCommissionRecord($i, $subType,
                $this->getFieldValue($this->preffixCommissionFormName($subType, $i, "commissionType")),
                $this->getFieldValue($this->preffixCommissionFormName($subType, $i, "commission")));
        }

        $this->deleteUnusedCommissions($maximumTier, $subType);
    }

    private function saveCommissionRecord($tier, $subtype, $commissionType, $commissionValue) {
        $commission = new Pap_Db_Commission();
        $commission->setTier($tier);
        $commission->setSubtype($subtype);
        $commission->setGroupId($this->commissionGroupId);
        $commission->setTypeId($this->commissionTypeId);
        if ($commissionValue == null) {
            $commissionValue = 0;
        }

        try {
            $commission->loadFromData();
            // loaded, change commission value and save
            $commission->setCommType($commissionType);
            $commission->setCommission($commissionValue);
            $commission->save();
        } catch(Gpf_DbEngine_NoRowException $e) {
            // doesn't exist, insert new record
            $commission->setCommType($commissionType);
            $commission->setCommission($commissionValue);
            $commission->insert();
        } catch(Gpf_DbEngine_TooManyRowsException $e) {
            // there are multiple rows, it is a mistake
            $commission->deleteUnusedCommissions($tier, $subtype, $this->commissionGroupId, $this->commissionTypeId, 'exact');
            $commission->setCommType($commissionType);
            $commission->setCommission($commissionValue);
            $commission->insert();
        }
    }

    public function deleteUnusedCommissions($fromTier, $subtype) {
        $commission = new Pap_Db_Commission();
        $commission->deleteUnusedCommissions($fromTier, $subtype, $this->commissionGroupId, $this->commissionTypeId, 'above');
    }


    /**
     *
     * @return Pap_Db_CommissionType
     */
    private function getNewComissionType($campaignId = null) {
        $commissionType = new Pap_Db_CommissionType();
        $commissionType->setRecurrencePresetId(Pap_Db_CommissionType::RECURRENCE_NONE);
        $this->fill($commissionType);
        if ($campaignId != null) {
            $commissionType->setCampaignId($campaignId);
        }
        $commissionType->setStatus(Pap_Db_CommissionType::STATUS_DISABLED);
        $commissionType->insert();
        return $commissionType;
    }

    public function addSignupForm() {
        $this->checkSaveInput();
        $commissionType = $this->getNewComissionType();
        $this->commissionTypeId = $commissionType->getId();
        $this->saveCommissions();
    }

    public function addForm() {
        $this->checkSaveInput();

        $commissionGroup = new Pap_Db_CommissionGroup();
        $commissionGroup->setId($this->commissionGroupId);
        $commissionGroup->load();

        $commissionType = $this->getNewComissionType($commissionGroup->getCampaignId());

        $this->commissionTypeId = $commissionType->getId();

        $this->saveCommissions();
    }
}
?>
