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
 * @package PostAffiliatePro
 */

class Pap_Features_ReCaptcha_Main extends Gpf_Plugins_Handler {
    /**
     * @return Pap_Features_ReCaptcha_Main
     */
    public static function getHandlerInstance() {
        return new Pap_Features_ReCaptcha_Main();
    }

    public function loadSettings(Gpf_Rpc_Form $form) {
        $form->setField(Pap_Settings::RECAPTCHA_PRIVATE_KEY, Gpf_Settings::get(Pap_Settings::RECAPTCHA_PRIVATE_KEY));
        $form->setField(Pap_Settings::RECAPTCHA_PUBLIC_KEY, Gpf_Settings::get(Pap_Settings::RECAPTCHA_PUBLIC_KEY));
        $form->setField(Pap_Settings::RECAPTCHA_ENABLED, Gpf_Settings::get(Pap_Settings::RECAPTCHA_ENABLED));
        $form->setField(Pap_Settings::RECAPTCHA_THEME, Gpf_Settings::get(Pap_Settings::RECAPTCHA_THEME));
        return $form;
    }

    public function saveSettings(Gpf_Rpc_Form $form) {
        Gpf_Settings::set(Pap_Settings::RECAPTCHA_PUBLIC_KEY, $form->getFieldValue(Pap_Settings::RECAPTCHA_PUBLIC_KEY));
        Gpf_Settings::set(Pap_Settings::RECAPTCHA_PRIVATE_KEY, $form->getFieldValue(Pap_Settings::RECAPTCHA_PRIVATE_KEY));
        Gpf_Settings::set(Pap_Settings::RECAPTCHA_ENABLED, $form->getFieldValue(Pap_Settings::RECAPTCHA_ENABLED));
        Gpf_Settings::set(Pap_Settings::RECAPTCHA_THEME, $form->getFieldValue(Pap_Settings::RECAPTCHA_THEME));
    }

    public function validateCaptcha(Pap_Contexts_Signup $context) {
        $params = $context->get('params');
        if(Gpf_Settings::get(Pap_Settings::RECAPTCHA_ENABLED) != Gpf::YES || $params->get('isFromApi') == Gpf::YES) {
            return;
        }
        require_once('../include/Pap/Features/ReCaptcha/recaptchalib.php');
        $form = new Gpf_Rpc_Form($params);
        $resp = recaptcha_check_answer (Gpf_Settings::get(Pap_Settings::RECAPTCHA_PRIVATE_KEY),
        $_SERVER["REMOTE_ADDR"],
        $form->getFieldValue("recaptcha_challenge_field"),
        $form->getFieldValue("recaptcha_response_field"));
        if (!$resp->is_valid) {
            throw new Gpf_Exception($this->_("The reCAPTCHA wasn't entered correctly"));
        }
    }
    
    public function initJsResource(Gpf_Contexts_Module $module) {
        $module->addJsResource('http://api.recaptcha.net/js/recaptcha_ajax.js');
    }
    
    public function addApplicationSettings(Pap_ApplicationSettings $appSettings) {
        $appSettings->addValue(Pap_Settings::RECAPTCHA_ENABLED, Gpf_Settings::get(Pap_Settings::RECAPTCHA_ENABLED));
        $appSettings->addValue(Pap_Settings::RECAPTCHA_PUBLIC_KEY, Gpf_Settings::get(Pap_Settings::RECAPTCHA_PUBLIC_KEY));
        $appSettings->addValue(Pap_Settings::RECAPTCHA_THEME, Gpf_Settings::get(Pap_Settings::RECAPTCHA_THEME));
    }
}
?>
