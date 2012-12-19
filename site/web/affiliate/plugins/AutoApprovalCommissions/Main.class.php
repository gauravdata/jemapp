<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package PostAffiliatePro
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
class AutoApprovalCommissions_Main extends Gpf_Plugins_Handler {

	const AUTO_APPROVAL_COMMISSIONS_DAYS = 'AutoApprovalCommissionsDays';
	const AUTO_APPROVAL_COMMISSIONS_NOTE = 'AutoApprovalCommissionsNote';

	public static function getHandlerInstance() {
		return new AutoApprovalCommissions_Main();
	}

	public function initFields(Pap_Merchants_Campaign_CommissionTypeEditAdditionalForm $additionalDetails) {
		$additionalDetails->addTextBoxWithDefault($this->_('Auto approve commissions after x days'),
		self::AUTO_APPROVAL_COMMISSIONS_DAYS,
            "0", $this->_("disabled"),
		$this->_('You can specify the number of days after which the commission will be auto approved. If you decline the commission before the auto approval date, it will stay declined.'),
		true);
		$additionalDetails->addTextBox($this->_('Auto approval note'),
		self::AUTO_APPROVAL_COMMISSIONS_NOTE,
		$this->_('This note will be added to every auto approved commission.'));
	}

	public function save(Gpf_Rpc_Form $form) {
		$commAutoApproveDays = $form->getFieldValue(self::AUTO_APPROVAL_COMMISSIONS_DAYS);
		$commAutoApproveNote = $form->getFieldValue(self::AUTO_APPROVAL_COMMISSIONS_NOTE);

		if (is_numeric($commAutoApproveDays) != "integer") {
			$form->setErrorMessage($this->_('Wrong format used for days interval.'));
			return;
		}

		$commTypeAttr = Pap_Db_Table_CommissionTypeAttributes::getInstance();
		$commTypeAttr->setCommissionTypeAttributeValue($form->getFieldValue('Id'), self::AUTO_APPROVAL_COMMISSIONS_DAYS,
		$commAutoApproveDays);
		$commTypeAttr->setCommissionTypeAttributeValue($form->getFieldValue('Id'), self::AUTO_APPROVAL_COMMISSIONS_NOTE,
		$commAutoApproveNote);
	}

	public function load(Gpf_Rpc_Form $form) {
		$commTypeAttr = Pap_Db_Table_CommissionTypeAttributes::getInstance();

		try {
			$commAutoApproveDays = $commTypeAttr->getCommissionTypeAttribute($form->getFieldValue('Id'),
			self::AUTO_APPROVAL_COMMISSIONS_DAYS)->getValue();
		} catch (Gpf_DbEngine_NoRowException $e) {
			$commAutoApproveDays = '0';
		}

		try {
			$commAutoApproveNote = $commTypeAttr->getCommissionTypeAttribute($form->getFieldValue('Id'),
			self::AUTO_APPROVAL_COMMISSIONS_NOTE)->getValue();
		} catch (Gpf_DbEngine_NoRowException $e) {
			$commAutoApproveNote = '';
		}

		$form->setField(self::AUTO_APPROVAL_COMMISSIONS_DAYS, $commAutoApproveDays);
		$form->setField(self::AUTO_APPROVAL_COMMISSIONS_NOTE, $commAutoApproveNote);
	}
}
?>
