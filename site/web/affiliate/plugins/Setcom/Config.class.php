<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
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
class Setcom_Config extends Gpf_Plugins_Config {
    const CUSTOM_SEPARATOR = 'SetcomCustomSeparator';
    const MERCHANT_USERNAME = 'SetcomMerchantUsername';
    const MERCHANT_PASSWORD = 'SetcomMerchantPassword';
    const MERCHANT_IDENTIFIER = 'SetcomMerchantIdentifier';
    
    
    protected function initFields() {        
        $this->addTextBox($this->_("Merchant username"), self::MERCHANT_USERNAME, $this->_("Merchant username from your Setcom merchant account"));
        $this->addTextBox($this->_("Merchant password"), self::MERCHANT_PASSWORD, $this->_("Merchant password from your Setcom merchant account"));
        $this->addTextBox($this->_("Merchant identifier"), self::MERCHANT_IDENTIFIER, $this->_("Merchant identifier from your Setcom merchant account"));
        $this->addTextBox($this->_("Custom value separator"), self::CUSTOM_SEPARATOR, $this->_("Custom value separator should be set only when custom value is used by other script. See Setcom integration method."));
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::CUSTOM_SEPARATOR, $form->getFieldValue(self::CUSTOM_SEPARATOR));
        Gpf_Settings::set(self::MERCHANT_USERNAME, $form->getFieldValue(self::MERCHANT_USERNAME));
        Gpf_Settings::set(self::MERCHANT_PASSWORD, $form->getFieldValue(self::MERCHANT_PASSWORD));
        Gpf_Settings::set(self::MERCHANT_IDENTIFIER, $form->getFieldValue(self::MERCHANT_IDENTIFIER));
        $form->setInfoMessage($this->_('Setcom plugin settings saved'));
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
        $form->addField(self::CUSTOM_SEPARATOR, Gpf_Settings::get(self::CUSTOM_SEPARATOR));
        $form->addField(self::MERCHANT_USERNAME, Gpf_Settings::get(self::MERCHANT_USERNAME));
        $form->addField(self::MERCHANT_PASSWORD, Gpf_Settings::get(self::MERCHANT_PASSWORD));
        $form->addField(self::MERCHANT_IDENTIFIER, Gpf_Settings::get(self::MERCHANT_IDENTIFIER));
        return $form;
    }
}

?>
