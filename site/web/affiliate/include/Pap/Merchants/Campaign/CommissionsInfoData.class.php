<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: InvoiceFormatForm.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
class Pap_Merchants_Campaign_CommissionsInfoData extends Pap_Common_Overview_InfoData {

	private function __construct() {
		$this->init();
	}

	/**
	 * @return Pap_Merchants_Campaign_CommissionsInfoData
	 */
	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Load commissions detail for campaign
	 *
	 * @service commission read
	 * @param campaignid
	 */
	public function commissionsDetail(Gpf_Rpc_Params $params) {
		return parent::getDetails($params);
	}

	protected function buildData(Gpf_Data_RecordSet $fields, Gpf_Rpc_Params $params) {
		$selectBuilder = Pap_Db_Table_Commissions::getInstance()->getAllCommissionsInCampaignSelectBuilder($params->get('campaignid'), '');
		$selectBuilder->orderBy->add('c.' . Pap_Db_Table_Commissions::ID);
		$selectBuilder->orderBy->add('tier');
		$selectBuilder->orderBy->add('ct.' . Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID);
		$commissionGroupID = $this->getCommissionGroupId($params->get('campaignid'));

		foreach ($selectBuilder->getAllRowsIterator() as $commission) {
			if ($commission->get('commissiongroupid') != $commissionGroupID) {
				continue;
			}
			$fields->add($this->createRecordArray($commission));
			$this->addCommission($commission);
		}
	}
	
	private function getFixedCost($commTypeId) {
		$commType = new Pap_Db_CommissionType();
		$commType->setId($commTypeId);
		$commType->load();
        if (($commType->getFixedcostValue()==0) || ($commType->getFixedcostValue()=='null')) {
        	return "";
        }
        return $commType->getFixedcostType().$commType->getFixedcostValue();
	}

	private function addCommission(Gpf_Data_Record $commission) {		
		$fixedcostValue = $this->getFixedCost($commission->get(Pap_Db_Table_Transactions::COMMISSIONTYPEID));						
		$this->fieldValues[$this->getFieldCode($commission)] = $commission->get(Pap_Db_Table_Commissions::VALUE) . $commission->get(Pap_Db_Table_Commissions::TYPE);		
		if (($commission->get('tier') == 1) && ($fixedcostValue != "")) {
			$this->fieldValues[$this->getFieldCode($commission) . "F"] = $fixedcostValue;
		}
	}
	
	private function getFieldCode(Gpf_Data_Record $commission) {
		if ($commission->get('rtype') == Pap_Common_Constants::TYPE_ACTION) {
			return $commission->get(Pap_Db_Table_Commissions::ID);
		}
		return $commission->get('rtype') . $commission->get('tier');
	}

	private function getCommissionGroupId($campaignID) {
		if (Gpf_Session::getAuthUser()->getRoleTypeId() == Pap_Application::ROLETYPE_AFFILIATE) {
            return $this->getAffiliateCommissionGroupId($campaignID);
		}
		return Pap_Db_Table_Commissions::getInstance()->getDefaultCommissionGroup($campaignID);
	}

	private function getAffiliateCommissionGroupId($campaignID) {
		$campaign = new Pap_Db_Campaign();
		$campaign->setId($campaignID);
		$commissionGroupId = $campaign->checkUserIsInCampaign(Gpf_Session::getAuthUser()->getPapUserId());

		if ($commissionGroupId != false) {
			return $commissionGroupId;
		}
		return Pap_Db_Table_Commissions::getInstance()->getDefaultCommissionGroup($campaignID);
	}

	/**
	 * Load list of commissions
	 *
	 * @service commission read
	 * @param campaignid
	 */
	public function getFields(Gpf_Rpc_Params $params) {
		return parent::getFields($params);
	}

	private function createRecordArray(Gpf_Data_Record $commission) {
		return array($commission->get(Pap_Db_Table_Commissions::ID), $commission->get('rtype') . $commission->get('tier'), $this->getCommissionText($commission), 'T',$commission->get('countrycodes'), 'M', null, '');
	}

	private function getCommissionText(Gpf_Data_Record $commission) {
		$tier = $commission->get('tier');
		if ($tier > 1) {
			return $this->getTier($tier);
		}
		switch ($commission->get('rtype')) {
			case Pap_Common_Constants::TYPE_CLICK:
				return $this->_('per click').' :';
			case Pap_Common_Constants::TYPE_SALE:
				return $this->_('per sale') . ' :';
			case Pap_Common_Constants::TYPE_CPM:
				return $this->_('per CPM') . ' :';
			case Pap_Common_Constants::TYPE_ACTION:
				return $this->_($commission->get('commissionTypeName')).":";
		}
		return '';
	}

	private function getTier($tier) {
		if ($tier == 2) {
			return $this->_('2nd tier') . ' :';
		}
		if ($tier == 3) {
			return $this->_('3rd tier') . ' :';
		}
		if ($tier > 3) {
			return $this->_('%sth tier', $tier) . ' :';
		}
		return '';
	}
}

?>
