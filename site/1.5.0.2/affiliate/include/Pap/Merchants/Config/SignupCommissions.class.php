<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
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
class Pap_Merchants_Config_ReferralCommissionTypeRpcForm extends Pap_Merchants_Campaign_CommissionTypeRpcForm {

	protected function initCommissionGroupId($commissionGroupId) {
		$this->commissionGroupId = $commissionGroupId;
	}

	/**
	 * @service commission read
	 * @param $fields
	 */
	public function load(Gpf_Rpc_Params $params) {
		$form = new Pap_Merchants_Config_ReferralCommissionTypeRpcForm($params);
		$form->loadForm();
		return $form;
	}
}

class Pap_Merchants_Config_SignupCommissions extends Pap_Merchants_Campaign_CommissionTypeForm {

	const REFERRAL_COMMISSION = 'referralCommission';

	/**
	 * @service commission read
	 * @param $fields
	 */
	public function load(Gpf_Rpc_Params $params) {
		return parent::load($params);
	}

	/**
	 * @service commission write
	 * @param $fields
	 */
	public function save(Gpf_Rpc_Params $params) {
		return parent::save($params);
	}

	/**
	 * @service commission write
	 * @param $fields
	 */
	public function add(Gpf_Rpc_Params $params) {
		return parent::addSignup($params);
	}

	/**
	 * @service commission read
	 * @param $fields
	 */
	public function loadSignupBonus(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);

		$form->setField(Pap_Settings::SIGNUP_BONUS,
		Gpf_Settings::get(Pap_Settings::SIGNUP_BONUS));
		return $form;
	}

	/**
	 * @service commission write
	 * @param $fields
	 */
	public function saveSignupBonus(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);

		Gpf_Settings::set(Pap_Settings::SIGNUP_BONUS,
		$form->getFieldValue(Pap_Settings::SIGNUP_BONUS));
		$form->setInfoMessage($this->_("Signup bonus saved"));

		return $form;
	}

	/**
	 * @service commission read
	 * @param Gpf_Rpc_Params $params
	 */
	public function loadReferralCommissions(Gpf_Rpc_Params $params) {
		return Pap_Db_Table_CommissionTypes::getInstance()->getAllCommissionTypes(null, Pap_Common_Constants::TYPE_REFERRAL);		
	}

	protected function getFormObject(Gpf_Rpc_Params $params, $edit = true) {
		return new Pap_Merchants_Config_ReferralCommissionTypeRpcForm($params, $edit);
	}
}
?>
