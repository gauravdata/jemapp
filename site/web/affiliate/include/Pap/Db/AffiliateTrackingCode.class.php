<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ActiveView.class.php 18000 2008-05-13 16:00:48Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Db_AffiliateTrackingCode extends Gpf_DbEngine_Row {
      
	const TYPE_SCRIPT = 'S';
	const TYPE_HTML = 'H';
	
    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Pap_Db_Table_AffiliateTrackingCodes::getInstance());
        parent::init();
    }
    
    public function setId($value) {
        $this->set(Pap_Db_Table_AffiliateTrackingCodes::ID, $value);
    }

    public function setAffiliateId($value) {
        $this->set(Pap_Db_Table_AffiliateTrackingCodes::AFFILIATEID, $value);
    }
    
	public function getAffiliateId() {
        return $this->get(Pap_Db_Table_AffiliateTrackingCodes::AFFILIATEID);
    }

	public function setCommissionTypeId($value) {
        $this->set(Pap_Db_Table_AffiliateTrackingCodes::COMMTYPEID, $value);
    }
    
    public function getCommissionTypeId() {
        return $this->get(Pap_Db_Table_AffiliateTrackingCodes::COMMTYPEID);
    }
    
    public function setStatus($value) {
        $this->set(Pap_Db_Table_AffiliateTrackingCodes::R_STATUS, $value);
    }

    public function getStatus() {
        return $this->get(Pap_Db_Table_AffiliateTrackingCodes::R_STATUS);
    }
    
    public function setCode($value) {
        $this->set(Pap_Db_Table_AffiliateTrackingCodes::CODE, $value);
    }

    public function getCode() {
        return $this->get(Pap_Db_Table_AffiliateTrackingCodes::CODE);
    }
    
    public function getType() {
        return $this->get(Pap_Db_Table_AffiliateTrackingCodes::TYPE);
    }
}

?>
