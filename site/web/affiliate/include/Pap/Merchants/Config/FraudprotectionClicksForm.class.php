<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: FraudprotectionForm.class.php 16669 2008-03-25 16:13:22Z mjancovic $
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
class Pap_Merchants_Config_FraudprotectionClicksForm extends Gpf_Object {


    /**
     * @service fraud_protection read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $form->setField(Pap_Settings::BANNEDIPS_CLICKS_FROM_IFRAME,
        Gpf_Settings::get(Pap_Settings::BANNEDIPS_CLICKS_FROM_IFRAME));

        
        $form->setField(Pap_Settings::BANNEDIPS_CLICKS,
        Gpf_Settings::get(Pap_Settings::BANNEDIPS_CLICKS));

        $form->setField(Pap_Settings::BANNEDIPS_CLICKS_ACTION,
        Gpf_Settings::get(Pap_Settings::BANNEDIPS_CLICKS_ACTION));

        $form->setField(Pap_Settings::BANNEDIPS_LIST_CLICKS,
        Gpf_Settings::get(Pap_Settings::BANNEDIPS_LIST_CLICKS));

        $form->setField(Pap_Settings::REPEATING_BANNER_CLICKS,
        Gpf_Settings::get(Pap_Settings::REPEATING_BANNER_CLICKS));

        $form->setField(Pap_Settings::REPEATING_CLICKS_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::REPEATING_CLICKS_SETTING_NAME));

        $form->setField(Pap_Settings::REPEATING_CLICKS_SECONDS_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::REPEATING_CLICKS_SECONDS_SETTING_NAME));

        $form->setField(Pap_Settings::REPEATING_CLICKS_ACTION_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::REPEATING_CLICKS_ACTION_SETTING_NAME));

        Gpf_Plugins_Engine::extensionPoint('FraudProtectionClicksForm.load', $form);

        return $form;
    }

    /**
     * @service fraud_protection write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $this->initValidators($form);
        if (!$form->validate()) {
            return $form;
        }
        Gpf_Settings::set(Pap_Settings::BANNEDIPS_CLICKS_FROM_IFRAME,
        $form->getFieldValue(Pap_Settings::BANNEDIPS_CLICKS_FROM_IFRAME));
        
        Gpf_Settings::set(Pap_Settings::BANNEDIPS_CLICKS,
        $form->getFieldValue(Pap_Settings::BANNEDIPS_CLICKS));

        Gpf_Settings::set(Pap_Settings::BANNEDIPS_CLICKS_ACTION,
        $form->getFieldValue(Pap_Settings::BANNEDIPS_CLICKS_ACTION));

        Gpf_Settings::set(Pap_Settings::BANNEDIPS_LIST_CLICKS,
        $form->getFieldValue(Pap_Settings::BANNEDIPS_LIST_CLICKS));


        Gpf_Settings::set(Pap_Settings::REPEATING_BANNER_CLICKS,
        $form->getFieldValue(Pap_Settings::REPEATING_BANNER_CLICKS));

        Gpf_Settings::set(Pap_Settings::REPEATING_CLICKS_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::REPEATING_CLICKS_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::REPEATING_CLICKS_SECONDS_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::REPEATING_CLICKS_SECONDS_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::REPEATING_CLICKS_ACTION_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::REPEATING_CLICKS_ACTION_SETTING_NAME));

        Gpf_Plugins_Engine::extensionPoint('FraudProtectionClicksForm.save', $form);

        $form->setInfoMessage($this->_("Fraud protections saved"));
        return $form;
    }

    private function initValidators(Gpf_Rpc_Form $form) {
        $form->addValidator(new Pap_Merchants_Config_AutoDeleteWithRepeatingClicksValidator($form), Pap_Settings::REPEATING_CLICKS_SECONDS_SETTING_NAME, '');
    }
}

class Pap_Merchants_Config_AutoDeleteWithRepeatingClicksValidator extends Pap_Merchants_Config_AutoDeleteRawClicksValidator {

    private $value;

    public function validate($value) {
        $this->value = $value;
        return parent::validate(Gpf_Settings::get(Pap_Settings::AUTO_DELETE_RAWCLICKS));
    }

    protected function getAutoDeleteRawClicks() {
        return Gpf_Settings::get(Pap_Settings::AUTO_DELETE_RAWCLICKS);
    }

    protected function getCompareValue() {
        return $this->value;
    }

    protected function computeCompareValue($compareValue) {
        return $compareValue / (60 * 60 * 24);
    }

    public function getText() {
        return $this->_('Must be smaller then "Delete raw click records older than" defined in Tracking settings');
    }
}

?>
