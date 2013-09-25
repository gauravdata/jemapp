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
class AutoResponsePlus_Config extends Gpf_Plugins_Config {    
    
    const REMOTE_CONTROL_EMAIL = 'auto_response_plus_remote_control_email';
    const AUTORESPONDER_ADDRESS = 'auto_response_plus_autoresponder_address';
    const NAME = 'auto_response_plus_name';
    const PASSWORD = 'auto_response_plus_password';
    const HTML = 'auto_response_plus_html';
    const TRACKING_TAB = 'auto_response_plus_tracking_tab';
    const DROP_RULES = 'auto_response_plus_drop_rules';
    
    protected function initFields() {
        $this->addTextBox($this->_('Remote control email'), self::REMOTE_CONTROL_EMAIL);
        $this->addTextBox($this->_('Owner name'), self::NAME);
        $this->addPasswordTextBox($this->_('Password'), self::PASSWORD);
        $this->addTextBox($this->_('Autoresponder\'s address'), self::AUTORESPONDER_ADDRESS, $this->_('Autoresponder\'s subscription address (without the domain)'));        
        $this->addCheckBox($this->_('Html'), self::HTML);
        $this->addTextBox($this->_('Tracking tag'), self::TRACKING_TAB, $this->_('The tracking tag to be associated with the subscription'));
        $this->addCheckBox($this->_('Drop rules'), self::DROP_RULES);        
    }
    
    /**
     * @anonym
     * @service
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::REMOTE_CONTROL_EMAIL, $this->_('Remote control email'));
        $form->addValidator(new Gpf_Rpc_Form_Validator_EmailValidator(), self::REMOTE_CONTROL_EMAIL);
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::NAME, $this->_('Owner name'));
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), self::PASSWORD, $this->_('Password'));                        
        if (!$form->validate()) {
            return $form;
        }
        Gpf_Settings::set(self::REMOTE_CONTROL_EMAIL, $form->getFieldValue(self::REMOTE_CONTROL_EMAIL));
        Gpf_Settings::set(self::NAME, $form->getFieldValue(self::NAME));
        Gpf_Settings::set(self::PASSWORD, $form->getFieldValue(self::PASSWORD));
        Gpf_Settings::set(self::AUTORESPONDER_ADDRESS, $form->getFieldValue(self::AUTORESPONDER_ADDRESS));
        Gpf_Settings::set(self::HTML, $form->getFieldValue(self::HTML));
        Gpf_Settings::set(self::TRACKING_TAB, $form->getFieldValue(self::TRACKING_TAB));
        Gpf_Settings::set(self::DROP_RULES, $form->getFieldValue(self::DROP_RULES));
        $form->setInfoMessage($this->_('Auto Response Plus saved'));
        return $form;
    }

    /**
     * @anonym
     * @service
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addField(self::REMOTE_CONTROL_EMAIL, Gpf_Settings::get(self::REMOTE_CONTROL_EMAIL));
        $form->addField(self::NAME, Gpf_Settings::get(self::NAME));
        $form->addField(self::PASSWORD, Gpf_Settings::get(self::PASSWORD));
        $form->addField(self::AUTORESPONDER_ADDRESS, Gpf_Settings::get(self::AUTORESPONDER_ADDRESS));
        $form->addField(self::HTML, Gpf_Settings::get(self::HTML));
        $form->addField(self::TRACKING_TAB, Gpf_Settings::get(self::TRACKING_TAB));
        $form->addField(self::DROP_RULES, Gpf_Settings::get(self::DROP_RULES));
        return $form;
    }
}

?>
