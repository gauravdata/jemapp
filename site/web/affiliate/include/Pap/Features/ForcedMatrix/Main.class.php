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
class Pap_Features_ForcedMatrix_Main extends Gpf_Plugins_Handler {

	public static function getHandlerInstance() {
		return new Pap_Features_ForcedMatrix_Main();
	}

	public function save(Gpf_Rpc_Form $form) {
		Gpf_Settings::set(Pap_Settings::MATRIX_WIDTH, $form->getFieldValue('matrixWidth'));
		Gpf_Settings::set(Pap_Settings::MATRIX_HEIGHT, $form->getFieldValue('matrixHeight'));
		Gpf_Settings::set(Pap_Settings::FULL_FORCED_MATRIX, $form->getFieldValue('fullForcedMatrix'));
		Gpf_Settings::set(Pap_Settings::MATRIX_SPILLOVER, $form->getFieldValue('spillover'));
		if ($form->existsField('spilloverAffiliate')) {
			Gpf_Settings::set(Pap_Settings::MATRIX_AFFILIATE, $form->getFieldValue('spilloverAffiliate'));
		}
		$this->saveExpandSettings($form);
		if ($form->existsField(Pap_Settings::MATRIX_OTHER_FILL_BONUS)) {
			Gpf_Settings::set(Pap_Settings::MATRIX_OTHER_FILL_BONUS, $form->getFieldValue(Pap_Settings::MATRIX_OTHER_FILL_BONUS));
		}
		$this->saveMatrixFillBonus($form);
	}

	public function load(Gpf_Rpc_Form $form) {
		$form->setField('matrixWidth', Gpf_Settings::get(Pap_Settings::MATRIX_WIDTH));
		$form->setField('matrixHeight', Gpf_Settings::get(Pap_Settings::MATRIX_HEIGHT));
		$form->setField('fullForcedMatrix', Gpf_Settings::get(Pap_Settings::FULL_FORCED_MATRIX));
		$form->setField('spillover', Gpf_Settings::get(Pap_Settings::MATRIX_SPILLOVER));
		$form->setField('spilloverAffiliate', Gpf_Settings::get(Pap_Settings::MATRIX_AFFILIATE));
		$form->setField(Pap_Settings::MATRIX_EXPAND_WIDTH, Gpf_Settings::get(Pap_Settings::MATRIX_EXPAND_WIDTH));
		$form->setField(Pap_Settings::MATRIX_EXPAND_HEIGHT, Gpf_Settings::get(Pap_Settings::MATRIX_EXPAND_HEIGHT));
		$form->setField(Pap_Settings::MATRIX_OTHER_FILL_BONUS, Gpf_Settings::get(Pap_Settings::MATRIX_OTHER_FILL_BONUS));
		$this->loadMatrixFillBonus($form);
	}

	public function computeParentFor(Pap_Common_User $child) {
		$matrix = $this->createMatrix();
		$matrix->computeParentFor($child);
	}

	public function computeParentForUnrefferedAffiliate(Pap_Common_User $child) {
		if (Gpf_Settings::get(Pap_Settings::FULL_FORCED_MATRIX) == Gpf::YES) {
			$this->computeParentFor($child);
		}
	}

	public function addAction(Pap_Features_PerformanceRewards_ActionList $list) {
		$list->addAction('a_fm_1', 'Pap_Features_ForcedMatrix_AddBonusCommToOriginalRefererAction', 'BCO');
	}

	public function processFillBonus(Pap_Common_User $child) {
		try {
			$originalParent = Pap_Affiliates_User::loadFromId($child->getOriginalParentUserId());
		} catch (Gpf_Exception $e) {
		    Gpf_Log::debug('Forced Matrix: Cannot load parent: ' . $e->getMessage());
			return;
		}
		$matrix = $this->createMatrix();
		if ($matrix->isFilled($originalParent)) {
			$matrixFillBonus = new Pap_Features_ForcedMatrix_MatrixFillBonus();
			$matrixFillBonus->process($originalParent);
		}
	}

	private function saveMatrixFillBonus(Gpf_Rpc_Form $form) {
		$matrixFillBonuses = array();
		$i = 1;
		while ($form->existsField('matrix_'.$i.'_FillBonus')) {
			$matrixFillBonuses[] = $form->getFieldValue('matrix_'.$i.'_FillBonus');
			$i++;
		}
		Pap_Features_ForcedMatrix_MatrixFillBonusSettings::setFillBonuses($matrixFillBonuses);
	}

	private function loadMatrixFillBonus(Gpf_Rpc_Form $form) {
		$matrixFillBonuses = Pap_Features_ForcedMatrix_MatrixFillBonusSettings::getFillBonuses();
		for ($i = 0; $i < count($matrixFillBonuses); $i++) {
			$index = $i + 1;
			$form->setField('matrix_'.$index.'_FillBonus', $matrixFillBonuses[$i]);
		}
	}

	private function saveExpandSettings(Gpf_Rpc_Form $form) {
		if ($form->existsField(Pap_Settings::MATRIX_EXPAND_WIDTH) && $form->existsField(Pap_Settings::MATRIX_EXPAND_HEIGHT)) {
			if ($form->getFieldValue(Pap_Settings::MATRIX_EXPAND_WIDTH) > 0 || $form->getFieldValue(Pap_Settings::MATRIX_EXPAND_HEIGHT) > 0) {
				Gpf_Settings::set(Pap_Settings::MATRIX_EXPAND_WIDTH, $form->getFieldValue(Pap_Settings::MATRIX_EXPAND_WIDTH));
				Gpf_Settings::set(Pap_Settings::MATRIX_EXPAND_HEIGHT, $form->getFieldValue(Pap_Settings::MATRIX_EXPAND_HEIGHT));
				return;
			}
			$form->setFieldError(Pap_Settings::MATRIX_EXPAND_WIDTH, $this->_('expand width by adding or expand height by adding must be defined'));
			$form->setFieldError(Pap_Settings::MATRIX_EXPAND_HEIGHT, $this->_('expand height by adding or expand width by adding must be defined'));
		}
	}

	/**
	 * @return Pap_Features_ForcedMatrix_Matrix
	 */
	private function createMatrix() {
		return new Pap_Features_ForcedMatrix_Matrix(new Pap_Common_UserTree());
	}
}
?>
