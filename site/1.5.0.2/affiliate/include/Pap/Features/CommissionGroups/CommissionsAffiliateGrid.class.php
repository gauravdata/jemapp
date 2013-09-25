<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: ExistingExportsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
class Pap_Features_CommissionGroups_CommissionsAffiliateGrid extends Gpf_View_MemoryGridService {
	
	/**
	 * @var Pap_Db_Table_CommissionGroups
	 */
	private $commissionsGroupTable;
	/**
	 * @var Pap_Db_Table_Commissions
	 */
	private $commissionsTable;

	/**
	 * @service commission_group read
	 *
	 * @return Gpf_Rpc_Serializable
	 */
	public function getRows(Gpf_Rpc_Params $params) {
		return parent::getRows($params);
	}

	public function filterRow(Gpf_Data_Row $row) {
		$this->setCommissionGroupId($row);

		if ($row->get('commissiongroupid') !== '') {
			return parent::filterRow($row);
		}
		return null;
	}

	protected function loadResultData() {
		$this->commissionsGroupTable = Pap_Db_Table_CommissionGroups::getInstance();
		$this->commissionsTable = Pap_Db_Table_Commissions::getInstance();
		return parent::loadResultData();
	}

	protected function initViewColumns() {
		$this->addViewColumn('campaignName', $this->_('Campaign name'), true);
		$this->addViewColumn('commissiongroupid', $this->_('Commission group'), true);
	}

	protected function initDataColumns() {
		$this->setKeyDataColumn('ca.'.Pap_Db_Table_Campaigns::ID);
		$this->addDataColumn(Pap_Db_Table_Campaigns::ID, 'ca.'.Pap_Db_Table_Campaigns::ID);
		$this->addDataColumn('campaignName', 'ca.'.Pap_Db_Table_Campaigns::NAME);
		$this->addDataColumn('commissiongroupid', 'SPACE(0)');
	}

	protected function initDefaultView() {
		$this->addDefaultViewColumn('campaignName', '', 'A');
		$this->addDefaultViewColumn('commissiongroupid', '', 'N');
	}

	protected function buildFrom(){
		$this->_selectBuilder->from->add(Pap_Db_Table_Campaigns::getName(), 'ca');
		$this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg', 'cg.' . Pap_Db_Table_CommissionGroups::CAMPAIGN_ID . '=ca.' . Pap_Db_Table_Campaigns::ID);
		$this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_UserInCommissionGroup::getName(), 'uic', 'uic.' . Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID . '=cg.' . Pap_Db_Table_CommissionGroups::ID);
	}
	
	protected function buildWhere() {
	    $this->_selectBuilder->where->add('uic.' . Pap_Db_Table_UserInCommissionGroup::USER_ID, '=', $this->getUserID());
	}

	private function getUserID() {
		if (!$this->_params->exists('userid')) {
			throw new Gpf_Exception('User ID is missing');
		}
		return $this->_params->get('userid');
	}

	private function setCommissionGroupId(Gpf_Data_Row $row) {
		if (!is_null(($commissionGroupId = $this->commissionsGroupTable->getUserCommissionGroup($row->get(Pap_Db_Table_Campaigns::ID), $this->getUserID())))) {
			$row->set('commissiongroupid', $commissionGroupId);
			return;
		}
		try {
			$row->set('commissiongroupid', $this->commissionsTable->getDefaultCommissionGroup($row->get(Pap_Db_Table_Campaigns::ID)));
		} catch (Gpf_DbEngine_NoRowException $e) {
		}
	}
}
?>
