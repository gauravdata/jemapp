<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
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
 * @package PostAffiliatePro plugins
 */
class Pap_Features_ForcedMatrix_MatrixFillUserSettings {

	const MATRIX_LAST_FILL_COUNT_WITH_BONUS = 'matrixLastFillCountWithBonus';
	const MATRIX_FILL_COUNT = 'matrixFillCount';
	const MATRIX_WIDTH = 'matrixWidth';
	const MATRIX_HEIGHT = 'matrixHeight';

	/**
	 * @var Pap_Common_User
	 */
	private $user;

	public function __construct(Pap_Common_User $user) {
		$this->user = $user;
	}

	public function getFillCount() {
		return $this->getSettingWithDefaultValue(self::MATRIX_FILL_COUNT, 0);
	}

	public function setFillCount($fillCount) {
		Gpf_Db_Table_UserAttributes::setSetting(self::MATRIX_FILL_COUNT, $fillCount, $this->user->getAccountUserId());
	}

	public function hasUserFillBonus($fillCount) {
		try {
			$fillBonusCount = $this->getLastFillCountWithBonus();
			return $fillBonusCount >= $fillCount;
		} catch (Gpf_DbEngine_NoRowException $e) {
		}
		return false;
	}

	public function setLastFillCountWithBonus($fillCount) {
		Gpf_Db_Table_UserAttributes::setSetting(self::MATRIX_LAST_FILL_COUNT_WITH_BONUS, $fillCount, $this->user->getAccountUserId());
	}

	public function getLastFillCountWithBonus() {
		return $this->getSettingWithDefaultValue(self::MATRIX_LAST_FILL_COUNT_WITH_BONUS, 0);
	}

	public function getMatrixWidth() {
		return $this->getSettingWithDefaultValue(self::MATRIX_WIDTH, Gpf_Settings::get(Pap_Settings::MATRIX_WIDTH));
	}

	public function getMatrixHeight() {
		return $this->getSettingWithDefaultValue(self::MATRIX_HEIGHT, Gpf_Settings::get(Pap_Settings::MATRIX_HEIGHT));
	}
	
	public function setMatrixWidth($width) {
		Gpf_Db_Table_UserAttributes::setSetting(self::MATRIX_WIDTH, $width, $this->user->getAccountUserId());
	}
	
	public function setMatrixHeight($height) {
		Gpf_Db_Table_UserAttributes::setSetting(self::MATRIX_HEIGHT, $height, $this->user->getAccountUserId());
	}

	/**
	 * @param String_type $attributeName
	 * @param String $defaultValue
	 * @return String
	 */
	private function getSettingWithDefaultValue($attributeName, $defaultValue) {
		try {
			return Gpf_Db_Table_UserAttributes::getSetting($attributeName, $this->user->getAccountUserId());
		} catch (Gpf_DbEngine_NoRowException $e) {
		}
		return $defaultValue;
	}
}
?>
