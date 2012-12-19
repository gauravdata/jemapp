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
class PayPal_Config extends Gpf_Plugins_Config {
    const CUSTOM_SEPARATOR = 'PaypalCustomSeparator';
    const DISCOUNT_TAX = 'PaypalDiscountTax';
    const DISCOUNT_FEE = 'PaypalDiscountFee';
    const DISCOUNT_SHIPPING = 'PaypalDiscountShipping';
    const DISCOUNT_HANDLING = 'PaypalDiscountHandling';
    const REGISTER_AFFILIATE = 'PaypalRegisterAffiliate';
    const USE_RECURRING_COMMISSION_SETTINGS = 'PaypalUseRecurringCommissionSettings';
    const NORMAL_COMMISSION_AS_RECURRING_COMMISSION = 'PaypalNormalCommissionAsRecurringCommission';
    const TEST_MODE = 'PayPalTestMode';
    const APPROVE_AFFILIATE = 'PayPalApproveAffiliate';
    const PROCESS_WHOLE_CART_AS_ONE_TRANSACTION = 'PayPalProcessWholeCartAsOneTransaction';
    const USE_COUPON = 'PayPalUseCoupon';
    
    protected function initFields() {
        $this->addTextBox($this->_("Custom value separator"), self::CUSTOM_SEPARATOR, $this->_("Custom value separator should be set only when custom value is used by other script. See Paypal (IPN and custom used by other script) integration method."));
        $this->addCheckBox($this->_("Use value before separator as a coupon"), self::USE_COUPON, $this->_('When this option is checked PAP expects that value in custom field before separator is a coupon code. In paypal button code put the coupon code into "custom" field and then use code to set correct separator PostAffTracker.setAppendValuesToField(\'YOUR_SEPARATOR\');'));
        $this->addCheckBox($this->_("Discount tax"), self::DISCOUNT_TAX, $this->_('Discounts tax from total cost value.'));
        $this->addCheckBox($this->_("Discount fee"), self::DISCOUNT_FEE, $this->_('Discounts fee from total cost value.'));
        $this->addCheckBox($this->_("Discount shipping"), self::DISCOUNT_SHIPPING, $this->_('Discounts shippings from total cost value.'));
        $this->addCheckBox($this->_("Discount handling"), self::DISCOUNT_HANDLING, $this->_('Discounts handling from total cost value.'));
        $this->addCheckBox($this->_("Register new affiliate with every occured event"), self::REGISTER_AFFILIATE, $this->_('When this checked, with every event new affiliate will be created from credentials that were set in PayPal submit form.'));
        $this->addCheckBox($this->_("Save only matched recurring commission"), self::USE_RECURRING_COMMISSION_SETTINGS, $this->_('This setting causes, that every recurring payment will be saved only if orderid (subscription id) will be matched with orderid in recurring commissions. If orderid will not be matched, than initialize recurring commission will not be created.'));
        $this->addCheckBox($this->_("Use general sale as recurring commission"), self::NORMAL_COMMISSION_AS_RECURRING_COMMISSION, $this->_('When this is checked, general sale will be saved as recurring commission.'));
        $this->addCheckBox($this->_("Process whole cart as one transaction"), self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, $this->_('Process all items in cart as one big transaction.'));
        $this->addCheckBox($this->_("Approve affiliate after successfull payment"), self::APPROVE_AFFILIATE, $this->_('When this is checked, every pending affiliate will be approved after successfull payment.'));
        $this->addCheckBox($this->_("Test mode"), self::TEST_MODE, $this->_('Skip back verification.'));
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
        Gpf_Settings::set(self::USE_COUPON, $form->getFieldValue(self::USE_COUPON));
        Gpf_Settings::set(self::DISCOUNT_TAX, $form->getFieldValue(self::DISCOUNT_TAX));
        Gpf_Settings::set(self::DISCOUNT_FEE, $form->getFieldValue(self::DISCOUNT_FEE));
        Gpf_Settings::set(self::DISCOUNT_SHIPPING, $form->getFieldValue(self::DISCOUNT_SHIPPING));
        Gpf_Settings::set(self::DISCOUNT_HANDLING, $form->getFieldValue(self::DISCOUNT_HANDLING));
        Gpf_Settings::set(self::REGISTER_AFFILIATE, $form->getFieldValue(self::REGISTER_AFFILIATE));
        Gpf_Settings::set(self::USE_RECURRING_COMMISSION_SETTINGS, $form->getFieldValue(self::USE_RECURRING_COMMISSION_SETTINGS));
        Gpf_Settings::set(self::NORMAL_COMMISSION_AS_RECURRING_COMMISSION, $form->getFieldValue(self::NORMAL_COMMISSION_AS_RECURRING_COMMISSION));
        Gpf_Settings::set(self::TEST_MODE, $form->getFieldValue(self::TEST_MODE));
        Gpf_Settings::set(self::APPROVE_AFFILIATE, $form->getFieldValue(self::APPROVE_AFFILIATE));
        Gpf_Settings::set(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, $form->getFieldValue(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION));
        if (Gpf_Settings::get(self::CUSTOM_SEPARATOR) == '' && Gpf_Settings::get(self::USE_COUPON) == Gpf::YES) {
           $form->setErrorMessage($this->_('You need insert Custom value separator for tracking by coupons'));
        }
        $form->setInfoMessage($this->_('Paypal settings saved'));
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
        $form->addField(self::USE_COUPON, Gpf_Settings::get(self::USE_COUPON));
        $form->addField(self::DISCOUNT_TAX, Gpf_Settings::get(self::DISCOUNT_TAX));
        $form->addField(self::DISCOUNT_FEE, Gpf_Settings::get(self::DISCOUNT_FEE));
        $form->addField(self::DISCOUNT_SHIPPING, Gpf_Settings::get(self::DISCOUNT_SHIPPING));
        $form->addField(self::DISCOUNT_HANDLING, Gpf_Settings::get(self::DISCOUNT_HANDLING));
        $form->addField(self::REGISTER_AFFILIATE, Gpf_Settings::get(self::REGISTER_AFFILIATE));
        $form->addField(self::USE_RECURRING_COMMISSION_SETTINGS, Gpf_Settings::get(self::USE_RECURRING_COMMISSION_SETTINGS));
        $form->addField(self::NORMAL_COMMISSION_AS_RECURRING_COMMISSION, Gpf_Settings::get(self::NORMAL_COMMISSION_AS_RECURRING_COMMISSION));
        $form->addField(self::TEST_MODE, Gpf_Settings::get(self::TEST_MODE));
        $form->addField(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION, Gpf_Settings::get(self::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION));
        $form->addField(self::APPROVE_AFFILIATE, Gpf_Settings::get(self::APPROVE_AFFILIATE));
        return $form;
    }
}

?>
