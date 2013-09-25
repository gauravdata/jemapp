<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class CustomRefid_Config extends Gpf_Plugins_Config {

	const CUSTOM_REFID_FORMAT = 'CustomRefidFormat';

	protected function initFields() {
		$this->addTextBox($this->_("Refid format"), self::CUSTOM_REFID_FORMAT,
		$this->_('It is possible to use following values in ID: {9} - will be replaced by any character in range [0-9], {z} - will be replaced by any character in range [a-z], {Z} - will be replaced by any character in range [A-Z], {X} - will be replaced by any character in range [0-9a-zA-Z], all other characters will be copied to refid as you will specify in format. Example of good format is e.g. {ZZZ}-{XXXXX}-{999}'));
	}

	/**
	 * @anonym
	 * @service custom_refid_settings write
	 * @param Gpf_Rpc_Params $params
	 * @return Gpf_Rpc_Form
	 */
	public function save(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);
		$form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::CUSTOM_REFID_FORMAT, $this->_('Refid format'));
		$form->addValidator(new Gpf_Rpc_Form_Validator_RegExpValidator('/(\{[zZ9X]+\})/', $this->_('Enter valid %s')), self::CUSTOM_REFID_FORMAT);
		if ($form->validate()) {
			Gpf_Settings::set(self::CUSTOM_REFID_FORMAT, $form->getFieldValue(self::CUSTOM_REFID_FORMAT));
			$form->setInfoMessage($this->_('Refid format saved'));
			return $form;
		}		
		return $form;		
	}

	/**
	 * @anonym
	 * @service custom_refid_settings read
	 * @param Gpf_Rpc_Params $params
	 * @return Gpf_Rpc_Form
	 */
	public function load(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);
		$form->addField(self::CUSTOM_REFID_FORMAT, Gpf_Settings::get(self::CUSTOM_REFID_FORMAT));
		return $form;
	}
}

?>
