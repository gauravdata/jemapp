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
class SolidTrustPay_Config extends Gpf_Plugins_Config {
    const CUSTOM_SEPARATOR = 'SolidTrustPayCustomSeparator';
    const CUSTOM_ITEM_NUMBER = 'SolidTrustPayCustomItemNumber';
    const SECONDARY_PASSWORD = 'SolidTrustPaySecondaryPassword';
    
    protected function initFields() {
        $this->addTextBox($this->_("Custom value separator"), self::CUSTOM_SEPARATOR, $this->_("Custom value separator should be set only when custom value is used by other script. See SolidTrustPay (IPN and custom used by other script) integration method."));
        $values = array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10');
        $this->addListBox($this->_("Custom item number"), self::CUSTOM_ITEM_NUMBER, $values, $this->_("Custom item number which will be carrying cookie value."));
        $this->addPasswordTextBox("Secondary password", self::SECONDARY_PASSWORD, $this->_('Secondary password from yours SolidTrustPay merchant account'));
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
        Gpf_Settings::set(self::CUSTOM_ITEM_NUMBER, $form->getFieldValue(self::CUSTOM_ITEM_NUMBER));
        Gpf_Settings::set(self::SECONDARY_PASSWORD, $form->getFieldValue(self::SECONDARY_PASSWORD));
        $form->setInfoMessage($this->_('SolidTrustPay settings saved'));
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
        $form->addField(self::CUSTOM_ITEM_NUMBER, Gpf_Settings::get(self::CUSTOM_ITEM_NUMBER));
        $form->addField(self::SECONDARY_PASSWORD, Gpf_Settings::get(self::SECONDARY_PASSWORD));
        return $form;
    }
}

?>
