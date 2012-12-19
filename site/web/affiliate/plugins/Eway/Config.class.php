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
class Eway_Config extends Gpf_Plugins_Config {
    const CUSTOM_SEPARATOR = 'EwayCustomSeparator';
    const CUSTOM_FIELD_NUMBER = 'EwayCustomFieldNumber';
    const RESPONSE_TYPE = 'EwayResponseType';
    
    protected function initFields() {
        $this->addTextBox($this->_("Custom value separator"), self::CUSTOM_SEPARATOR, $this->_("Custom value separator should be set only when custom value is used by other script. See Eway (IPN and custom used by other script) integration method."));
        $this->addListBox($this->_('Custom field number'), self::CUSTOM_FIELD_NUMBER, array('1'=>'1','2'=>'2','3'=>'3'), $this->_('Enter the number of custom field which will be used for transmitting affiliate cookie'));
        $this->addListBox($this->_('Response type'), self::RESPONSE_TYPE, array('xml'=>'XML','http'=>'HTTP'), $this->_('Select response type, for Merchant hosted select XML, for shared select HTTP'));        
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
        Gpf_Settings::set(self::CUSTOM_FIELD_NUMBER, $form->getFieldValue(self::CUSTOM_FIELD_NUMBER));
        Gpf_Settings::set(self::RESPONSE_TYPE, $form->getFieldValue(self::RESPONSE_TYPE));
        $form->setInfoMessage($this->_('Eway plugin settings saved'));
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
        $form->addField(self::CUSTOM_FIELD_NUMBER, Gpf_Settings::get(self::CUSTOM_FIELD_NUMBER));
        $form->addField(self::RESPONSE_TYPE, Gpf_Settings::get(self::RESPONSE_TYPE));
        return $form;
    }
}

?>
