<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
class Ejunkie_Config extends Gpf_Plugins_Config {
    const CUSTOM_SEPARATOR = 'EjunkieCustomSeparator';
    const DISCOUNT_TAX = 'EjunkieDiscountTax';
    const DISCOUNT_FEE = 'EjunkieDiscountFee';
    const DISCOUNT_SHIPPING = 'EjunkieDiscountShipping';
    const DISCOUNT_HANDLING = 'EjunkieDiscountHandling';
    const USE_RECURRING_COMMISSION_SETTINGS = 'EjunkieUseRecurringCommissionSettings';
    const PROCESS_WHOLE_CART_AS_ONE_TRANSACTION = 'EjunkieProcessWholeCartAsOneTransaction';

    protected function initFields() {
        $this->addTextBox($this->_("Custom value separator"), self::CUSTOM_SEPARATOR, $this->_("Custom value separator should be set only when custom value is used by other script. See Ejunkie (IPN and custom used by other script) integration method."));
        $this->addCheckBox($this->_("Discount tax"), self::DISCOUNT_TAX, $this->_('Discounts tax from total cost value.'));
        $this->addCheckBox($this->_("Discount fee"), self::DISCOUNT_FEE, $this->_('Discounts fee from total cost value.'));
        $this->addCheckBox($this->_("Discount shipping"), self::DISCOUNT_SHIPPING, $this->_('Discounts shippings from total cost value.'));
        $this->addCheckBox($this->_("Discount handling"), self::DISCOUNT_HANDLING, $this->_('Discounts handling from total cost value.'));
        $this->addCheckBox($this->_("Use recurring commission settings"), self::USE_RECURRING_COMMISSION_SETTINGS, $this->_('Use settings defined in recurring commission settings in campaigns, instead of default Ejunkie plugin matching by transaction order id.'));
        $this->addCheckBox($this->_("Process whole cart as one transaction"), self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, $this->_('Process all items in cart as one big transaction.'));
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
        Gpf_Settings::set(self::DISCOUNT_TAX, $form->getFieldValue(self::DISCOUNT_TAX));
        Gpf_Settings::set(self::DISCOUNT_FEE, $form->getFieldValue(self::DISCOUNT_FEE));
        Gpf_Settings::set(self::DISCOUNT_SHIPPING, $form->getFieldValue(self::DISCOUNT_SHIPPING));
        Gpf_Settings::set(self::DISCOUNT_HANDLING, $form->getFieldValue(self::DISCOUNT_HANDLING));
        Gpf_Settings::set(self::USE_RECURRING_COMMISSION_SETTINGS, $form->getFieldValue(self::USE_RECURRING_COMMISSION_SETTINGS));
        Gpf_Settings::set(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, $form->getFieldValue(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION));
        $form->setInfoMessage($this->_('Ejunkie settings saved'));
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
        $form->addField(self::DISCOUNT_TAX, Gpf_Settings::get(self::DISCOUNT_TAX));
        $form->addField(self::DISCOUNT_FEE, Gpf_Settings::get(self::DISCOUNT_FEE));
        $form->addField(self::DISCOUNT_SHIPPING, Gpf_Settings::get(self::DISCOUNT_SHIPPING));
        $form->addField(self::DISCOUNT_HANDLING, Gpf_Settings::get(self::DISCOUNT_HANDLING));
        $form->addField(self::USE_RECURRING_COMMISSION_SETTINGS, Gpf_Settings::get(self::USE_RECURRING_COMMISSION_SETTINGS));
        $form->addField(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, Gpf_Settings::get(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION));
        return $form;
    }
}

?>
