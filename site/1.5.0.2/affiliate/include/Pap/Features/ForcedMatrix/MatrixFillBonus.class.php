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
class Pap_Features_ForcedMatrix_MatrixFillBonus extends Gpf_Object {

	public function process(Pap_Common_User $originalParent) {
		$userFillSettings = new Pap_Features_ForcedMatrix_MatrixFillUserSettings($originalParent);
		$fillCount = $userFillSettings->getFillCount();
		$userFillSettings->setFillCount(++$fillCount);

		if ($this->existFillBonus($fillCount) && !$userFillSettings->hasUserFillBonus($fillCount)) {
			if (Gpf_Settings::get(Pap_Settings::MATRIX_SPILLOVER) != Pap_Features_ForcedMatrix_Matrix::SPILLOVER_EXPAND_MATRIX) {
				if ($fillCount == 1) {
					$this->addFillBonus($originalParent, $fillCount, Pap_Features_ForcedMatrix_MatrixFillBonusSettings::getFillBonus($fillCount));
					$userFillSettings->setLastFillCountWithBonus($fillCount);
				}
				return;
			}
			$this->addFillBonus($originalParent, $fillCount, Pap_Features_ForcedMatrix_MatrixFillBonusSettings::getFillBonus($fillCount));
			$userFillSettings->setLastFillCountWithBonus($fillCount);
		}
	}

	protected function addFillBonus(Pap_Common_User $user, $fillCount, $commission) {
		$transaction = new Pap_Common_Transaction();
		$transaction->setCommission($commission);
		$transaction->setType(Pap_Db_Transaction::TYPE_EXTRA_BONUS);
		$transaction->setDateInserted(Gpf_Common_DateUtils::now());
		$transaction->setStatus(Pap_Common_Constants::STATUS_APPROVED);
		$transaction->setPayoutStatus(Pap_Common_Constants::PSTATUS_UNPAID);
		$transaction->setUserId($user->getId());
		$transaction->setSystemNote($this->_('Matrix %sx fill bonus', $fillCount));
		$transaction->insert();
	}

	private function existFillBonus($fillCount) {
		return Pap_Features_ForcedMatrix_MatrixFillBonusSettings::getFillBonus($fillCount) > 0 || Gpf_Settings::get(Pap_Settings::MATRIX_OTHER_FILL_BONUS) > 0;
	}
}
?>
