<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @author Juraj Simon
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
class UsernameConstraint_Config extends Gpf_Plugins_Config {

	const CUSTOM_USERNAME_FORMAT = 'UsernameConstraintFormat';
	const CUSTOM_ERROR_MESSAGE = 'UsernameConstraintErrorMessage';

	protected function initFields() {
		$this->addTextBox($this->_("Username format"), self::CUSTOM_USERNAME_FORMAT,$this->_('It is possible to insert any valid regular expression'));
		$this->addTextBox($this->_("Custom error message"), self::CUSTOM_ERROR_MESSAGE,$this->_('Message which will be show to affiliate if he insert whong expression as username'));
	}

	/**
	 * @anonym
	 * @service custom_refid_settings write
	 * @param Gpf_Rpc_Params $params
	 * @return Gpf_Rpc_Form
	 */
	public function save(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);
		$form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::CUSTOM_USERNAME_FORMAT, $this->_('Refid format'));
		$form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::CUSTOM_ERROR_MESSAGE, $this->_('Custom error message'));
		if ($form->validate()) {
			Gpf_Settings::set(self::CUSTOM_USERNAME_FORMAT, $form->getFieldValue(self::CUSTOM_USERNAME_FORMAT));
			Gpf_Settings::set(self::CUSTOM_ERROR_MESSAGE, $form->getFieldValue(self::CUSTOM_ERROR_MESSAGE));
			$form->setInfoMessage($this->_('Username constraint settings saved'));
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
		$form->addField(self::CUSTOM_USERNAME_FORMAT, Gpf_Settings::get(self::CUSTOM_USERNAME_FORMAT));
		$form->addField(self::CUSTOM_ERROR_MESSAGE, Gpf_Settings::get(self::CUSTOM_ERROR_MESSAGE));
		return $form;
	}
}

?>
