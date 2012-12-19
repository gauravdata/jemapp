<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: ApplicationSettings.class.php 16785 2008-03-31 13:20:07Z vzeman $
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
class Pap_Common_SignupApplicationSettings extends Pap_ApplicationSettings {

    protected function loadSetting() {
        parent::loadSetting();
        $this->addSetting(Pap_Settings::POST_SIGNUP_TYPE_SETTING_NAME);
        $this->addSetting(Pap_Settings::POST_SIGNUP_URL_SETTING_NAME);
        $this->addValue(Pap_Settings::SIGNUP_TERMS_SETTING_NAME, Gpf_Lang::_localizeRuntime(Gpf_Settings::get(Pap_Settings::SIGNUP_TERMS_SETTING_NAME)));
        $this->addSetting(Pap_Settings::INCLUDE_PAYOUT_OPTIONS);
        $this->addSetting(Pap_Settings::PAYOUT_OPTIONS);
        $this->addSetting(Pap_Settings::FORCE_PAYOUT_OPTION);
        $this->addSetting(Pap_Settings::FORCE_TERMS_ACCEPTANCE_SETTING_NAME);
        $this->addSetting(Pap_Settings::DEFAULT_PAYOUT_METHOD);
        $this->loadParentFromRequest();
        $this->addDefaultCountry();
    }

    private function addDefaultCountry() {
        $form = new Gpf_Country_CountryForm();
        $result = $form->loadDefaultCountry(new Gpf_Rpc_Params());
        $this->addValue('defaultCountry', $result->getValue('default'));
    }

    private function loadParentFromRequest() {
        $affiliateParamName = Pap_Tracking_Request::getAffiliateClickParamName();
        if (array_key_exists($affiliateParamName, $_REQUEST)) {
            $this->addValue("parentAffiliateIdFromRequest", $_REQUEST[$affiliateParamName]);
        } else {
            $this->addValue("parentAffiliateIdFromRequest", "");
        }
    }
}
?>
