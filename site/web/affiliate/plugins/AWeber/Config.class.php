<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic  (created by Rick Braddy / WinningWare.com for PostAffiliatePro)
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
class AWeber_Config extends Gpf_Plugins_Config {    
    
    const AUTORESPONDER_ADDRESS = 'aweber_autoresponder_address';
    
    protected function initFields() {
        $this->addTextBox($this->_('Autoresponder'), self::AUTORESPONDER_ADDRESS, $this->_('AWeber autoresponder\'s subscription address; mylist@aweber.com'));      		    }
    
    /**
     * @anonym
     * @service
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addValidator(new Gpf_Rpc_Form_Validator_EmailValidator(), self::AUTORESPONDER_ADDRESS, $this->_('Autoresponder'));
        if (!$form->validate()) {
            return $form;
        }
        Gpf_Settings::set(self::AUTORESPONDER_ADDRESS, $form->getFieldValue(self::AUTORESPONDER_ADDRESS));
        $form->setInfoMessage($this->_('AWeber saved'));
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
        $form->addField(self::AUTORESPONDER_ADDRESS, Gpf_Settings::get(self::AUTORESPONDER_ADDRESS));
        return $form;
    }
}

?>
