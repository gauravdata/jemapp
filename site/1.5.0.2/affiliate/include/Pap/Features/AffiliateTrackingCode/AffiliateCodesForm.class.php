<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
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
class Pap_Features_AffiliateTrackingCode_AffiliateCodesForm extends Pap_Features_AffiliateTrackingCode_CodesForm {
	/**
	 * @service affiliate_tracking_code write_own
	 * @param $ids
	 * @return Gpf_Rpc_Action
	 */
	public function saveFields(Gpf_Rpc_Params $params) {
		$action = new Gpf_Rpc_Action($params);
		$action->setErrorMessage($this->_('Failed to save %s field(s) in %s(s)', '%s', $this->getDbRowObjectName()));
		$action->setInfoMessage($this->_('%s field(s) in %s(s) successfully saved', '%s', $this->getDbRowObjectName()));

		$fields = new Gpf_Data_RecordSet();
		$fields->loadFromArray($action->getParam("fields"));

		foreach ($fields as $field) {
			$dbRow = $this->loadAffTrackingCode($field);
			$dbRow->setStatus(Pap_Common_Constants::STATUS_PENDING);
			$dbRow->set($field->get("name"), $field->get("value"));
			$dbRow->save();
			$action->addOk();
		}

		return $action;
	}

	/**
	 * @param String $field
	 * @return Pap_Db_AffiliateTrackingCode
	 */
	protected function loadAffTrackingCode(Gpf_Data_Record $field) {
		$codeId = $field->get('id');
		if (substr($codeId, 0, 4) == 'NEW_') {
			return $this->loadAffTrackingCodeFrom(substr($codeId, 4), $this->getAffiliateId());
		}
		$dbRow = $this->createDbRowObject();
		$dbRow->setPrimaryKeyValue($codeId);
		$dbRow->load();
		return $dbRow;
	}

	/**
	 * @param String $commissionTypeId
	 * @param String $affiliateId
	 * @return Pap_Db_AffiliateTrackingCode
	 */
	protected function loadAffTrackingCodeFrom($commissionTypeId, $affiliateId) {
		$dbRow = $this->createDbRowObject();
		$dbRow->setCommissionTypeId($commissionTypeId);
		$dbRow->setAffiliateId($affiliateId);
		try {
			$dbRow->loadFromData(array(Pap_Db_Table_AffiliateTrackingCodes::COMMTYPEID, Pap_Db_Table_AffiliateTrackingCodes::AFFILIATEID));
		} catch (Gpf_Exception $e) {
		}
		return $dbRow;
	}
	
	/**
	 * @return String
	 */
	protected function getAffiliateId() {
		return Gpf_Session::getAuthUser()->getPapUserId();
	}
}
