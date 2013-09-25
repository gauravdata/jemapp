<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Signup.class.php 25452 2009-09-23 09:17:15Z mbebjak $
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

abstract class Pap_SignupBase extends Gpf_ModuleBase {

	/**
	 * @var Gpf_Rpc_Form
	 */
	protected $signupResponse;

	/**
	 * @return String
	 */
	protected abstract function getFormId();

	/**
	 * @return String
	 */
	protected abstract function getSignupTemplateName();

	/**
	 * @return String
	 */
	protected abstract function getSignupSettingsClassName();

	/**
	 * @return String
	 */
	protected abstract function getSignupFormService();

	protected function onStart() {
		parent::onStart();
		$this->checkSignupFields();
		if ($this->isPostRequest()) {
			$this->signupResponse = $this->processPostRequest();
		}
	}

	protected function checkSignupFields() {
		$dynamicFormPanel = $this->createDynamicFormPanel();
		$dynamicFormPanel->checkTemplate();
	}

	/**
	 * @return Gpf_Ui_DynamicFormPanel
	 */
	protected function createDynamicFormPanel() {
		return new Gpf_Ui_DynamicFormPanel($this->getSignupTemplateName(). '.tpl', $this->getFormId(), "signup");
	}

	protected function setSessionInfo(Gpf_Rpc_Data $sessionInfo) {
		Pap_Module::setSessionInfo($sessionInfo);
	}

	protected function getTitle() {
		return $this->_localize(Gpf_Settings::get(Pap_Settings::PROGRAM_NAME));
	}

	protected function initCachedData() {
		parent::initCachedData();
		$this->renderSignupFieldsRequest();
		$this->renderSignupRequest();
		$this->renderSignupSettingsRequest();
	}

	protected function initStyleSheets() {
		parent::initStyleSheets();
		$this->addStyleSheets(Pap_Module::getStyleSheets());
	}

	protected function getCachedTemplateNames() {
		return array('notification_window', 'context_menu', 'icon_button',
                     'window', 'window_left', 'window_header', 'window_header_refresh',
                     'window_bottom_left', 'window_empty_content','task','page_header','button',
                     'loading_screen', 'window_move_panel', 'single_content_panel',
                     'item', 'signup_form', 'icon_button', 'tooltip_popup', 'link_button', 'form_field_checkbox',
                     'form_field', 'button', 'listbox', 'listbox_popup', 'grid_pager', $this->getSignupTemplateName());
	}

	protected function renderSignupRequest() {
		if ($this->signupResponse !== null) {
			$this->renderSignupFormSaveRequest();
		}
	}

	protected function renderSignupFormSaveRequest() {
		Gpf_Rpc_CachedResponse::add($this->signupResponse, $this->getSignupFormService(), "add", "signupFormSaveRequest");
	}

	protected function renderSignupSettingsRequest() {
		$signupSettingsClassName = $this->getSignupSettingsClassName();
		$settings = new $signupSettingsClassName;
		Gpf_Rpc_CachedResponse::add($settings->getSettingsNoRpc(), $signupSettingsClassName, 'getSettings');
	}


	protected function renderSignupFieldsRequest() {
		$formFields = Gpf_Db_Table_FormFields::getInstance()->getFieldsNoRpc($this->getFormId(), array(Gpf_Db_FormField::STATUS_MANDATORY, Gpf_Db_FormField::STATUS_OPTIONAL));
		Gpf_Rpc_CachedResponse::add($formFields, 'Gpf_Db_Table_FormFields', 'getFields');
		foreach ($formFields as $field) {
			if ($field->get('type') == 'C') {
				$this->addCountriesRequest();
			}
		}
	}

	private function addCountriesRequest() {
		$countryData = new Gpf_Country_CountryData();
		Gpf_Rpc_CachedResponse::add($countryData->getRows(new Gpf_Rpc_Params()), 'Gpf_Country_CountryData', 'getRows');
	}

	public function assignModuleAttributes(Gpf_Templates_Template $template) {
		parent::assignModuleAttributes($template);
		$template->assign(Pap_Settings::PROGRAM_NAME, Gpf_Settings::get(Pap_Settings::PROGRAM_NAME));
		$template->assign(Pap_Settings::PROGRAM_LOGO, Gpf_Settings::get(Pap_Settings::PROGRAM_LOGO));
	}

	protected function assignTemplateVariables($template) {
		parent::assignTemplateVariables($template);
		Pap_Module::assignTemplateVariables($template);
	}
}
?>
