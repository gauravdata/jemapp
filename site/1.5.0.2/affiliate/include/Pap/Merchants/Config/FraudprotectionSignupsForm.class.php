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
class Pap_Merchants_Config_FraudprotectionSignupsForm extends Gpf_Object {


    /**
     * @service fraud_protection read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $form->setField(Pap_Settings::BANNEDIPS_LIST_SIGNUPS,
        Gpf_Settings::get(Pap_Settings::BANNEDIPS_LIST_SIGNUPS));

        $form->setField(Pap_Settings::BANNEDIPS_SIGNUPS,
        Gpf_Settings::get(Pap_Settings::BANNEDIPS_SIGNUPS));

        $form->setField(Pap_Settings::BANNEDIPS_SIGNUPS_ACTION,
        Gpf_Settings::get(Pap_Settings::BANNEDIPS_SIGNUPS_ACTION));

        $form->setField(Pap_Settings::REPEATING_SIGNUPS_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::REPEATING_SIGNUPS_SETTING_NAME));

        $form->setField(Pap_Settings::REPEATING_SIGNUPS_SECONDS_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::REPEATING_SIGNUPS_SECONDS_SETTING_NAME));

        $form->setField(Pap_Settings::REPEATING_SIGNUPS_ACTION_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::REPEATING_SIGNUPS_ACTION_SETTING_NAME));

        Gpf_Plugins_Engine::extensionPoint('FraudProtectionSignupsForm.load', $form);

        return $form;
    }

    /**
     * @service fraud_protection write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        Gpf_Settings::set(Pap_Settings::BANNEDIPS_LIST_SIGNUPS,
        $form->getFieldValue(Pap_Settings::BANNEDIPS_LIST_SIGNUPS));

        Gpf_Settings::set(Pap_Settings::BANNEDIPS_SIGNUPS,
        $form->getFieldValue(Pap_Settings::BANNEDIPS_SIGNUPS));

        Gpf_Settings::set(Pap_Settings::BANNEDIPS_SIGNUPS_ACTION,
        $form->getFieldValue(Pap_Settings::BANNEDIPS_SIGNUPS_ACTION));

        Gpf_Settings::set(Pap_Settings::REPEATING_SIGNUPS_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::REPEATING_SIGNUPS_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::REPEATING_SIGNUPS_SECONDS_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::REPEATING_SIGNUPS_SECONDS_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::REPEATING_SIGNUPS_ACTION_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::REPEATING_SIGNUPS_ACTION_SETTING_NAME));

        Gpf_Plugins_Engine::extensionPoint('FraudProtectionSignupsForm.save', $form);

        $form->setInfoMessage($this->_("Fraud protections saved"));
        return $form;
    }

}
?>
