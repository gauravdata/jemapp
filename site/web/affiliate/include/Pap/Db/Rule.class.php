<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Db_Rule extends Gpf_DbEngine_Row {

	function init() {
		$this->setTable(Pap_Db_Table_Rules::getInstance());
		parent::init();
	}

	public function getId() {
		return $this->get(Pap_Db_Table_Rules::ID);
	}

	public function setId($ruleId) {
		$this->set(Pap_Db_Table_Rules::ID, $ruleId);
	}

	public function getCampaignId() {
		return $this->get(Pap_Db_Table_Rules::CAMPAIGN_ID);
	}
	
	public function setCampaignId($campaingId) {
	    $this->set(Pap_Db_Table_Rules::CAMPAIGN_ID, $campaingId);
	}

	public function getWhat() {
		return $this->get(Pap_Db_Table_Rules::WHAT);
	}
	
    public function setWhat($what) {
        $this->set(Pap_Db_Table_Rules::WHAT, $what);
    }

    public function getStatus() {
        return $this->get(Pap_Db_Table_Rules::STATUS);
    }
    
    public function setStatus($status) {
        $this->set(Pap_Db_Table_Rules::STATUS, $status);
    }
    
	public function getDate() {
		return $this->get(Pap_Db_Table_Rules::DATE);
	}
	
    public function setDate($date) {
        $this->set(Pap_Db_Table_Rules::DATE, $date);
    }

	public function getSince() {
		return $this->get(Pap_Db_Table_Rules::SINCE);
	}
	
    public function setSince($since) {
        $this->set(Pap_Db_Table_Rules::SINCE, $since);
    }

	public function getEquation() {
		return $this->get(Pap_Db_Table_Rules::EQUATION);
	}
	
    public function setEquation($equation) {
        $this->set(Pap_Db_Table_Rules::EQUATION, $equation);
    }

	public function getEquationValue1() {
		return $this->get(Pap_Db_Table_Rules::EQUATION_VALUE_1);
	}
	
    public function setEquationValue1($eqVal1) {
        $this->set(Pap_Db_Table_Rules::EQUATION_VALUE_1, $eqVal1);
    }

	public function getEquationValue2() {
		return $this->get(Pap_Db_Table_Rules::EQUATION_VALUE_2);
	}
	
    public function setEquationValue2($eqVal2) {
        $this->set(Pap_Db_Table_Rules::EQUATION_VALUE_2, $eqVal2);
    }

	public function getAction() {
		return $this->get(Pap_Db_Table_Rules::ACTION);
	}
	
    public function setAction($action) {
        return $this->set(Pap_Db_Table_Rules::ACTION, $action);
    }

    public function getCommissionGroupId() {
        return $this->get(Pap_Db_Table_Rules::COMMISSION_GROUP_ID);
    }
	
    public function setCommissionGroupId($groupId) {
        $this->set(Pap_Db_Table_Rules::COMMISSION_GROUP_ID, $groupId);
    }
    
	public function getBonusType() {
		return $this->get(Pap_Db_Table_Rules::BONUS_TYPE);
	}

	public function getBonusValue() {
		return $this->get(Pap_Db_Table_Rules::BONUS_VALUE);
	}
}

?>
