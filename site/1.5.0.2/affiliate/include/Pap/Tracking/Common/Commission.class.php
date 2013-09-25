<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Commission.class.php 21322 2008-09-29 14:35:10Z mbebjak $
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
class Pap_Tracking_Common_Commission extends Gpf_Object {

    private $tier;
    private $type;
    private $value;
    private $subtype;
    private $status;
    private $groupid;
    private $typeid;

    public function __construct($tier = 1, $type = Pap_Db_CommissionType::COMMISSION_FIXED,
    $value = 0, $subtype = Pap_Db_Table_Commissions::SUBTYPE_NORMAL){
        $this->tier = $tier;
        $this->type = $type;
        $this->value = $value;
        $this->subtype = $subtype;
    }

    public function loadFrom(Pap_Db_Commission $commission){
        $this->tier = $commission->getTier();
        $this->type = $commission->getCommissionType();
        $this->value = $commission->getCommissionValue();
        $this->subtype = $commission->getSubtype();
        $this->groupid = $commission->getGroupId();
        $this->typeid = $commission->getCommissionTypeId();
    }

    public function getGroupId() {
        return $this->groupid;
    }

    public function getCommissionTypeId() {
        return $this->typeid;
    }

    public function getTier() {
        return $this->tier;
    }

    public function getSubType() {
        return $this->subtype;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getValue() {
        return $this->value;
    }

    public function getType() {
        return $this->type;
    }

    public function getCommission($totalCost) {
        if($this->type == Pap_Db_CommissionType::COMMISSION_PERCENTAGE) {
            if(!is_numeric($totalCost)) {
                throw new Gpf_Exception("    STOPPING, For percentage campaign there has to be TotalCost parameter");
            }
            $returnValue = ($this->value / 100) * $totalCost;
        } else {
            $returnValue = $this->value;
        }
        if (Gpf_Settings::get(Pap_Settings::ALLOW_COMPUTE_NEGATIVE_COMMISSION) == Gpf::NO) {
            $returnValue = ($returnValue < 0 ? 0 : $returnValue);
        }
        return $returnValue;
    }

    public function setStatusFromType(Pap_Db_CommissionType $commissionType) {
        if($commissionType->getApproval() == Pap_Db_CommissionType::APPROVAL_AUTOMATIC) {
            $this->setStatus(Pap_Common_Constants::STATUS_APPROVED);
            return;
        }
        $this->setStatus(Pap_Common_Constants::STATUS_PENDING);
    }
}
?>
