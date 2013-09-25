<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
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
class SafePay_Config extends Gpf_Plugins_Config {
    const CUSTOM_FIELD_NUMBER = 'SafePayCustomField';
    const SECRET_PASSPHRASE = 'SafePaySecretPassPhrase';
    
    protected function initFields() {
        $this->addListBox($this->_('Custom field number'), self::CUSTOM_FIELD_NUMBER, array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'), $this->_('Enter the number of custom field which will be used for transmitting affiliate cookie'));
        $this->addTextBox($this->_('Secret passphrase'), self::SECRET_PASSPHRASE, $this->_('Enter your secres passphrase if you set it up in your SafePay merchant account. Otherwise leave this field empty.'));        
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::CUSTOM_FIELD_NUMBER, $form->getFieldValue(self::CUSTOM_FIELD_NUMBER));
        Gpf_Settings::set(self::SECRET_PASSPHRASE, $form->getFieldValue(self::SECRET_PASSPHRASE));
        $form->setInfoMessage($this->_('Settings was saved'));
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
        $form->addField(self::CUSTOM_FIELD_NUMBER, Gpf_Settings::get(self::CUSTOM_FIELD_NUMBER));
        $form->addField(self::SECRET_PASSPHRASE, Gpf_Settings::get(self::SECRET_PASSPHRASE));
        return $form;
    }
}

?>
