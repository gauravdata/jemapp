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
class AuthorizeNet_Config extends Gpf_Plugins_Config {
    const PARAM_NAME = 'AuthorizeNetParamName';
    const DISCOUNT_TAX = 'AuthorizeNetDiscountTax';
    const DUTY_TAX = 'AuthorizeNetDutyTax';
    const FREIGHT_TAX = 'AuthorizeNetFreightTax';
    
    protected function initFields() {
        $this->addTextBox($this->_('Parameter name'), self::PARAM_NAME, $this->_("Parameter with this name will be used for sending required info to PAP"));
        $this->addCheckBox($this->_("Discount tax"), self::DISCOUNT_TAX, $this->_('Discounts tax from total cost value.'));
        $this->addCheckBox($this->_("Discount duty"), self::DUTY_TAX, $this->_('Discounts duty tax from total cost value.'));
        $this->addCheckBox($this->_("Discount freight"), self::FREIGHT_TAX, $this->_('Discounts freight tax from total cost value.'));
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::PARAM_NAME, $form->getFieldValue(self::PARAM_NAME));
        Gpf_Settings::set(self::DISCOUNT_TAX, $form->getFieldValue(self::DISCOUNT_TAX));
        Gpf_Settings::set(self::DUTY_TAX, $form->getFieldValue(self::DUTY_TAX));
        Gpf_Settings::set(self::FREIGHT_TAX, $form->getFieldValue(self::FREIGHT_TAX));
        $form->setInfoMessage($this->_('Authorize.net plugin configuration saved'));
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
        $form->addField(self::PARAM_NAME, Gpf_Settings::get(self::PARAM_NAME));
        $form->addField(self::DISCOUNT_TAX, Gpf_Settings::get(self::DISCOUNT_TAX));
        $form->addField(self::DUTY_TAX, Gpf_Settings::get(self::DUTY_TAX));
        $form->addField(self::FREIGHT_TAX, Gpf_Settings::get(self::FREIGHT_TAX));
        return $form;
    }
}

?>
