<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Maros Fric
*   @since Version 1.0.0
*   $Id: Commission.class.php 26320 2009-11-30 08:29:53Z mbebjak $
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
class Pap_Db_Commission extends Gpf_DbEngine_Row {
    
    const COMMISSION_TYPE_PERCENTAGE = '%';
    const COMMISSION_TYPE_FIXED = '$';

    public function __construct(){
        parent::__construct();
    }

    public function init() {
        $this->setTable(Pap_Db_Table_Commissions::getInstance());
        parent::init();
    }

    public function getCommissionType() {
        return $this->get(Pap_Db_Table_Commissions::TYPE);
    }

    public function getCommissionValue() {
        return $this->get(Pap_Db_Table_Commissions::VALUE);
    }
    
    public function getGroupId() {
        return $this->get(Pap_Db_Table_Commissions::GROUP_ID);
    }
    
    public function setGroupId($groupId) {
        $this->set(Pap_Db_Table_Commissions::GROUP_ID, $groupId);
    }
    
    public function setType($type) {
        $this->set(Pap_Db_Table_Commissions::TYPE, $type);
    }
    
    public function setTypeId($typeId) {
        $this->set(Pap_Db_Table_Commissions::TYPE_ID, $typeId);
    }
    
    public function setTier($tier) {
        $this->set(Pap_Db_Table_Commissions::TIER, $tier);
    }
    
    public function getTier() {
        return $this->get(Pap_Db_Table_Commissions::TIER);
    }

    public function setSubtype($subtype) {
        $this->set(Pap_Db_Table_Commissions::SUBTYPE, $subtype);
    }
    
    public function getSubtype() {
        return $this->get(Pap_Db_Table_Commissions::SUBTYPE);
    }
    
    public function setCommType($type) {
        $this->set(Pap_Db_Table_Commissions::TYPE, $type);
    }
    
    public function setCommissionTypeId($commissionTypeId) {
        $this->set(Pap_Db_Table_Commissions::TYPE_ID, $commissionTypeId);
    }
    
    public function getCommissionTypeId() {
        return $this->get(Pap_Db_Table_Commissions::TYPE_ID);
    }
    
    public function setCommission($value) {
        $this->set(Pap_Db_Table_Commissions::VALUE, $value);
    }
    
    /**
     * deletes tier commission
     * if deleteType == exact, it will delete only given tier
     * if deleteType == above, it will delete given tier and all above
     *
     * @param unknown_type $fromTier
     * @param unknown_type $subType
     * @param unknown_type $commGroupId
     * @param unknown_type $commTypeId
     * @param unknown_type $deleteType
     */
    public function deleteUnusedCommissions($fromTier, $subType, $commGroupId, $commTypeId, $deleteType = 'extact') {
    	$deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
    	$deleteBuilder->from->add(Pap_Db_Table_Commissions::getName());
    	$deleteBuilder->where->add('subtype', '=', $subType);
    	if($deleteType == 'above') {
    		$deleteBuilder->where->add('tier', '>', $fromTier);
    	} else {
    		$deleteBuilder->where->add('tier', '=', $fromTier);
    	}
	    $deleteBuilder->where->add('commtypeid', '=', $commTypeId);
    	$deleteBuilder->where->add('commissiongroupid', '=', $commGroupId);
    	
    	$deleteBuilder->delete();
	}
}
?>
