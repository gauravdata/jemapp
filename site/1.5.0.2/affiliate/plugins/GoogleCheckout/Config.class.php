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
class GoogleCheckout_Config extends Gpf_Plugins_Config {
    const CUSTOM_SEPARATOR = 'GoogleCheckoutCustomSeparator';
    const MERCHANT_ID = 'GoogleCheckoutMerchantId';
    const MERCHANT_KEY = 'GoogleCheckoutMerchantKey';
    const PROCESS_WHOLE_CART_AS_ONE_TRANSACTION = 'GoogleCheckoutProcessWholeCartAsOneTransaction';
    
    protected function initFields() {
        $this->addTextBox($this->_("Custom value separator"), self::CUSTOM_SEPARATOR, $this->_("Custom value separator should be set only when custom value is used by other script. See GoogleCheckout (custom value used by other script) integration method."));
        $this->addTextBox($this->_("Your merchant ID"), self::MERCHANT_ID, $this->_("Your Google Checkout merchant ID"));
        $this->addTextBox($this->_("Your merchant Key"), self::MERCHANT_KEY, $this->_("Your Google Checkout merchant key"));
        $this->addCheckBox($this->_("Process whole cart as one transaction"), self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, $this->_('Process all items in cart as one big transaction. Note: you must use merchant-private-data item to transmit custom data to pap, instead of merchant-private-item-data when usign this option.'));
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
        Gpf_Settings::set(self::MERCHANT_ID, $form->getFieldValue(self::MERCHANT_ID));
        Gpf_Settings::set(self::MERCHANT_KEY, $form->getFieldValue(self::MERCHANT_KEY));
        Gpf_Settings::set(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, $form->getFieldValue(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION));
        $form->setInfoMessage($this->_('Google Checkout Settings saved'));
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
        $form->addField(self::MERCHANT_ID, Gpf_Settings::get(self::MERCHANT_ID));
        $form->addField(self::MERCHANT_KEY, Gpf_Settings::get(self::MERCHANT_KEY));
        $form->addField(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, Gpf_Settings::get(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION));
        return $form;
    }
}

?>
