<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
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
class SignupActionCommissions_Config extends Gpf_Plugins_Config {
    const AFTER_SIGNUP_ACTION_CODE = 'AfterSignupActionCode';
    const AFTER_SIGNUP_ACTION_CAMPAIGNID = 'AfterSignupActionCampaignId';
    const DEFAULT_ORDER_ID = 'SignupActionCommission';
    
    protected function initFields() {
        $this->addTextBox($this->_("After signup action code"), self::AFTER_SIGNUP_ACTION_CODE);
        $this->addTextBox($this->_("After signup action campaign ID"), self::AFTER_SIGNUP_ACTION_CAMPAIGNID);
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::AFTER_SIGNUP_ACTION_CODE, $form->getFieldValue(self::AFTER_SIGNUP_ACTION_CODE));
        Gpf_Settings::set(self::AFTER_SIGNUP_ACTION_CAMPAIGNID, $form->getFieldValue(self::AFTER_SIGNUP_ACTION_CAMPAIGNID));
        $form->setInfoMessage($this->_('After signup action saved'));
        return $form;
    }

    /**
     * @anonym
     * @service custom_separator read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addField(self::AFTER_SIGNUP_ACTION_CODE, Gpf_Settings::get(self::AFTER_SIGNUP_ACTION_CODE));
        $form->addField(self::AFTER_SIGNUP_ACTION_CAMPAIGNID, Gpf_Settings::get(self::AFTER_SIGNUP_ACTION_CAMPAIGNID));
        return $form;
    }
}

?>
