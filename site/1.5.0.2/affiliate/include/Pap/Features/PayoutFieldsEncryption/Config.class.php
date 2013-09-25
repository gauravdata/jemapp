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
class Pap_Features_PayoutFieldsEncryption_Config extends Gpf_Plugins_Config {
    const ENCRYPT_KEY = 'EncryptFieldsKey';
    const ENCRYPT_IV = 'EncryptFieldsIv';
    
    protected function initFields() {
        $this->addTextBox($this->_("Key"), self::ENCRYPT_KEY, $this->_('Key must be 16, 24 or 32 characters long'));
        $this->addTextBox($this->_("Initialization vector"), self::ENCRYPT_IV, $this->_('Initialization vector must be 16 characters long'));        
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        $key = trim($form->getFieldValue(self::ENCRYPT_KEY));
        $iv = trim($form->getFieldValue(self::ENCRYPT_IV));
        
        if (!in_array(strlen($key), array(16, 24, 32))) {
            $form->setFieldError(self::ENCRYPT_KEY, $this->_('Key must be 16, 24 or 32 characters long'));
        }
        if (strlen($iv) != 16) {
            $form->setFieldError(self::ENCRYPT_IV, $this->_('Initialization vector must be 16 characters long'));
        }
        
        if (!$form->isError()) {
            if ($key != Gpf_Settings::get(self::ENCRYPT_KEY) || $iv != Gpf_Settings::get(self::ENCRYPT_IV)) {
                Pap_Features_PayoutFieldsEncryption_Main::getHandlerInstance()->recodeAllValues($key, $iv);
            }
            Gpf_Settings::set(self::ENCRYPT_KEY, $key);
            Gpf_Settings::set(self::ENCRYPT_IV, $iv);
            $form->setInfoMessage($this->_('Encrypt codes saved'));
        }
        
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
        $form->addField(self::ENCRYPT_KEY, Gpf_Settings::get(self::ENCRYPT_KEY));
        $form->addField(self::ENCRYPT_IV, Gpf_Settings::get(self::ENCRYPT_IV));
        return $form;
    }
}

?>
