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
class UltraCart_Config extends Gpf_Plugins_Config {
    const CUSTOM_FIELD_NUMBER = 'UltraCartCustomFieldNumber';
    const SHIPPING_HANDLING_SUBSTRACT = 'UltraCartShippingHandlingSubstract';
    
    protected function initFields() {
        $this->addTextBox($this->_("Custom field number (1-5)"), self::CUSTOM_FIELD_NUMBER, $this->_("Custom field number that can be used by %s", Gpf_Settings::get(Pap_Settings::BRANDING_QUALITYUNIT_PAP)) . ".");        
        $this->addCheckBox($this->_("Substract shipping and handling from total cost"), self::SHIPPING_HANDLING_SUBSTRACT, $this->_("Subtotal value will be used as total cost. Subtotal cost is total cost value without shipping and handling cost"));
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $customFieldNumber = (integer)$form->getFieldValue(self::CUSTOM_FIELD_NUMBER);
        if ($customFieldNumber < 1 || $customFieldNumber > 5) {
            $form->setFieldError(self::CUSTOM_FIELD_NUMBER, $this->_('Custom field number must be from range 1-5.'));
            return $form;
        }
        Gpf_Settings::set(self::CUSTOM_FIELD_NUMBER, $customFieldNumber);
        Gpf_Settings::set(self::SHIPPING_HANDLING_SUBSTRACT, $form->getFieldValue(self::SHIPPING_HANDLING_SUBSTRACT));
        $form->setInfoMessage($this->_('UltraCart plugin configuration saved'));
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
        $form->addField(self::SHIPPING_HANDLING_SUBSTRACT, Gpf_Settings::get(self::SHIPPING_HANDLING_SUBSTRACT));
        return $form;
    }
}

?>
