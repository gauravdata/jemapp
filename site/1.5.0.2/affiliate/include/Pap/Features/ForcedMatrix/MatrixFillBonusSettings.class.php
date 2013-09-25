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
class Pap_Features_ForcedMatrix_MatrixFillBonusSettings {
	
	public static function getFillBonus($fillCount) {
		$fillBonus = explode(';', Gpf_Settings::get(Pap_Settings::MATRIX_FILL_BONUS));
		$fillCountIndex = $fillCount - 1;
		if (array_key_exists($fillCountIndex, $fillBonus)) {
			return $fillBonus[$fillCountIndex];
		}
		if (Gpf_Settings::get(Pap_Settings::MATRIX_OTHER_FILL_BONUS) > 0) {
			return Gpf_Settings::get(Pap_Settings::MATRIX_OTHER_FILL_BONUS);
		}
		return 0;
	}
	
	public static function setFillBonuses(array $matrixFillBonus) {
		Gpf_Settings::set(Pap_Settings::MATRIX_FILL_BONUS, implode(';', $matrixFillBonus));
	}
	
	/**
	 * @return array
	 */
	public static function getFillBonuses() {
		return explode(';', Gpf_Settings::get(Pap_Settings::MATRIX_FILL_BONUS));
	}
}
?>
