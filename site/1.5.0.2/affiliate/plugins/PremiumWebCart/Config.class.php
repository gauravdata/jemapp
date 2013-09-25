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
class PremiumWebCart_Config extends Gpf_Plugins_Config {
    
    const PROCESS_WHOLE_CART_AS_ONE_TRANSACTION = 'PremiumWebCartProcessWholeCartAsOneTransaction';
    const MERCHANT_ID = 'PremiumWebCartMerchantId';
    const API_SIGNATURE = 'PremiumWebCartApiSignature';
    
    protected function initFields() {
        $this->addCheckBox($this->_("Process whole cart as one transaction"), self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, $this->_('Process all items in cart as one big transaction.'));
        $this->addTextBox($this->_("Merchant ID"), self::MERCHANT_ID, $this->_("Premium Web Cart Merchant ID must be set."));
        $this->addTextBox($this->_("API Signature"), self::API_SIGNATURE, $this->_("Premium Web Cart API signature must be set."));
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, $form->getFieldValue(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION));
        Gpf_Settings::set(self::MERCHANT_ID, $form->getFieldValue(self::MERCHANT_ID));
        Gpf_Settings::set(self::API_SIGNATURE, $form->getFieldValue(self::API_SIGNATURE));
        $form->setInfoMessage($this->_('Configuration saved'));
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
        $form->addField(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, Gpf_Settings::get(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION));
        $form->addField(self::MERCHANT_ID, Gpf_Settings::get(self::MERCHANT_ID));
        $form->addField(self::API_SIGNATURE, Gpf_Settings::get(self::API_SIGNATURE));
        return $form;
    }
}

?>
