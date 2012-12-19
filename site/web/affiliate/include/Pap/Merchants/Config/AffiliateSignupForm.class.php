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
class Pap_Merchants_Config_AffiliateSignupForm extends Gpf_Object {

	const POST_SIGNUP_TYPE_URL = "url";
	const POST_SIGNUP_TYPE_PAGE = "page";

	/**
	 * @service affiliate_signup_setting read
	 * @param $fields
	 */
	public function load(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);
		$form->setField(Pap_Settings::POST_SIGNUP_TYPE_SETTING_NAME, Gpf_Settings::get(Pap_Settings::POST_SIGNUP_TYPE_SETTING_NAME));

		$form->setField(Pap_Settings::POST_SIGNUP_URL_SETTING_NAME, Gpf_Settings::get(Pap_Settings::POST_SIGNUP_URL_SETTING_NAME));

		$form->setField(Pap_Settings::SIGNUP_TERMS_SETTING_NAME, Gpf_Settings::get(Pap_Settings::SIGNUP_TERMS_SETTING_NAME));

		$form->setField(Pap_Settings::FORCE_TERMS_ACCEPTANCE_SETTING_NAME, Gpf_Settings::get(Pap_Settings::FORCE_TERMS_ACCEPTANCE_SETTING_NAME));

		$form->setField(Pap_Settings::INCLUDE_PAYOUT_OPTIONS, Gpf_Settings::get(Pap_Settings::INCLUDE_PAYOUT_OPTIONS));

		$form->setField(Pap_Settings::PAYOUT_OPTIONS, Gpf_Settings::get(Pap_Settings::PAYOUT_OPTIONS));

		$form->setField(Pap_Settings::FORCE_PAYOUT_OPTION, Gpf_Settings::get(Pap_Settings::FORCE_PAYOUT_OPTION));

		$form->setField('assignAffiliateTo', Gpf_Settings::get(Pap_Settings::ASSIGN_NON_REFERRED_AFFILIATE_TO));

		//		$form->setField(self::OPTIONAL_PAYOUT_FIELDS,
		//		Gpf_Settings::get(self::OPTIONAL_PAYOUT_FIELDS));

		$form->setField(Pap_Settings::AFFILIATE_APPROVAL, Gpf_Settings::get(Pap_Settings::AFFILIATE_APPROVAL));
		$form->setField(Pap_Settings::AFFILIATE_LOGOUT_URL, Gpf_Settings::get(Pap_Settings::AFFILIATE_LOGOUT_URL));

		Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AffiliateSignupForm.load', $form);

		return $form;
	}

	/**
	 * @service affiliate_signup_setting write
	 * @param $fields
	 */
	public function save(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);

		$signupType = $form->getFieldValue("postSignupType");
		Gpf_Settings::set(Pap_Settings::POST_SIGNUP_TYPE_SETTING_NAME, $signupType);

		if ($signupType == self::POST_SIGNUP_TYPE_URL) {
			Gpf_Settings::set(Pap_Settings::POST_SIGNUP_URL_SETTING_NAME,
			                  $form->getFieldValue("postSignupUrl"));
		}

		Gpf_Settings::set(Pap_Settings::SIGNUP_TERMS_SETTING_NAME,
		                  $form->getFieldValue(Pap_Settings::SIGNUP_TERMS_SETTING_NAME));

		Gpf_Settings::set(Pap_Settings::FORCE_TERMS_ACCEPTANCE_SETTING_NAME,
		                  $form->getFieldValue(Pap_Settings::FORCE_TERMS_ACCEPTANCE_SETTING_NAME));

		Gpf_Settings::set(Pap_Settings::INCLUDE_PAYOUT_OPTIONS,
		                  $form->getFieldValue(Pap_Settings::INCLUDE_PAYOUT_OPTIONS));

		Gpf_Settings::set(Pap_Settings::PAYOUT_OPTIONS, $form->getFieldValue(Pap_Settings::PAYOUT_OPTIONS));

		Gpf_Settings::set(Pap_Settings::FORCE_PAYOUT_OPTION, $form->getFieldValue(Pap_Settings::FORCE_PAYOUT_OPTION));

		Gpf_Settings::set(Pap_Settings::ASSIGN_NON_REFERRED_AFFILIATE_TO, $form->getFieldValue('assignAffiliateTo'));

		//		Gpf_Settings::set(self::OPTIONAL_PAYOUT_FIELDS,
		//		$form->getFieldValue(self::OPTIONAL_PAYOUT_FIELDS));

		Gpf_Settings::set(Pap_Settings::AFFILIATE_APPROVAL, $form->getFieldValue(Pap_Settings::AFFILIATE_APPROVAL));

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.AffiliateSignupForm.save', $form);

		$form->setInfoMessage($this->_("Affiliate signup settings saved"));
		return $form;
	}
}

?>
