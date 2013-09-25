<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Fric
*   @since Version 1.0.0
*   $Id: CommissionType.class.php 30645 2011-01-04 08:16:27Z mkendera $
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
class Pap_Db_CommissionType extends Gpf_DbEngine_Row {
    const STATUS_ENABLED = 'E';
    const STATUS_DISABLED = 'D';

    const APPROVAL_AUTOMATIC = 'A';
    const APPROVAL_MANUAL = 'M';

    const COMMISSION_PERCENTAGE = '%';
    const COMMISSION_FIXED = '$';

    /* Recurrence types */
    const RECURRENCE_NONE = '';
    const RECURRENCE_DAILY = 'A';
    const RECURRENCE_WEEKLY = 'B';
    const RECURRENCE_MONTHLY = 'C';
    const RECURRENCE_QUARTERLY = 'Q'; // new constant!!!
    const RECURRENCE_SEMIANNUALLY = 'D';
    const RECURRENCE_YEARLY = 'E';

    public function __construct(){
        parent::__construct();
    }

    public function init() {
        $this->setTable(Pap_Db_Table_CommissionTypes::getInstance());
        parent::init();
    }

    public function getId() {
    	return $this->get(Pap_Db_Table_CommissionTypes::ID);
    }

    public function setId($commissionTypeId) {
    	$this->set(Pap_Db_Table_CommissionTypes::ID, $commissionTypeId);
    }

    public function getApproval() {
    	return $this->get(Pap_Db_Table_CommissionTypes::APPROVAL);
    }

    public function getZeroOrdersCommissions() {
    	return $this->get(Pap_Db_Table_CommissionTypes::ZEROORDERSCOMMISSION);
    }

    public function getSaveZeroCommissions() {
        return $this->get(Pap_Db_Table_CommissionTypes::SAVEZEROCOMMISSION);
    }

    public function getRecurrencePresetId() {
    	return $this->get(Pap_Db_Table_CommissionTypes::RECURRENCEPRESETID);
    }

    public function getStatus() {
        return $this->get(Pap_Db_Table_CommissionTypes::STATUS);
    }

    public function getType() {
        return $this->get(Pap_Db_Table_CommissionTypes::TYPE);
    }

    public function getFixedcostType() {
        return $this->get(Pap_Db_Table_CommissionTypes::FIXEDCOSTTYPE);
    }

    public function getFixedcostValue() {
        return $this->get(Pap_Db_Table_CommissionTypes::FIXEDCOSTVALUE);
    }

    public function setFixedcostType($value) {
        $this->set(Pap_Db_Table_CommissionTypes::FIXEDCOSTTYPE, $value);
    }

    public function setFixedcostValue($value) {
        $this->set(Pap_Db_Table_CommissionTypes::FIXEDCOSTVALUE, $value);
    }

    public function setName($value) {
        $this->set(Pap_Db_Table_CommissionTypes::NAME, $value);
    }

    public function getName() {
        return $this->get(Pap_Db_Table_CommissionTypes::NAME);
    }

    public function getCode() {
        return $this->get(Pap_Db_Table_CommissionTypes::CODE);
    }

    public function setCode($code) {
        return $this->set(Pap_Db_Table_CommissionTypes::CODE, $code);
    }

    public function setStatus($status) {
        $this->set(Pap_Db_Table_CommissionTypes::STATUS, $status);
    }

    public function setCampaignId($campaignId) {
        $this->set(Pap_Db_Table_CommissionTypes::CAMPAIGNID, $campaignId);
    }

    public function getCampaignId() {
        return $this->get(Pap_Db_Table_CommissionTypes::CAMPAIGNID);
    }

    public function setType($type) {
        $this->set(Pap_Db_Table_CommissionTypes::TYPE, $type);
    }

    public function setApproval($approval) {
        $this->set(Pap_Db_Table_CommissionTypes::APPROVAL, $approval);
    }

    public function setRecurrencePresetId($recurrenceType) {
        $this->set(Pap_Db_Table_CommissionTypes::RECURRENCEPRESETID, $recurrenceType);
    }

    public function getParentCommissionTypeId() {
        return $this->get(Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID);
    }

    public function setParentCommissionTypeId($parentId) {
        $this->set(Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID, $parentId);
    }

    public function setCountryCodes($countryCodes) {
        $this->set(Pap_Db_Table_CommissionTypes::COUNTRYCODES, $countryCodes);
    }

    public function getCountryCodes() {
        return $this->get(Pap_Db_Table_CommissionTypes::COUNTRYCODES);
    }

    /**
     * Sets whether transaction should be saved even if the total cost value is set to zero
     *
     * @param String $zeroOrdersCommission "Y" or "N"
     */
    public function setZeroOrdersCommission($zeroOrdersCommission) {
        $this->set(Pap_Db_Table_CommissionTypes::ZEROORDERSCOMMISSION, $zeroOrdersCommission);
    }

    /**
     * Sets whether transaction should be saved even if the commision value is set to zero
     *
     * @param String $saveZeroCommission "Y" or "N"
     */
    public function setSaveZeroCommission($saveZeroCommission) {
        $this->set(Pap_Db_Table_CommissionTypes::SAVEZEROCOMMISSION, $saveZeroCommission);
    }
    
    protected function beforeSaveCheck() {
        parent::beforeSaveCheck();
        
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CommissionType.beforeSaveCheck', $this);
    }
}

?>
