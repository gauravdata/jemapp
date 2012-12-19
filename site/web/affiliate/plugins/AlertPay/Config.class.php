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
class AlertPay_Config extends Gpf_Plugins_Config {
    const CUSTOM_FIELD_NUMBER = 'AlertPayCustomFieldNumber';
    const SECURITY_CODE = 'AlertPaySecurityCode';
    const ALLOW_TEST_SALES = 'AlertPayTestSales';
    const DIFF_RECURRING_COMMISSIONS = 'AlertPayDifferentRecComm';
    
    protected function initFields() {
        $this->addTextBox($this->_("Custom field number (1-6)"), self::CUSTOM_FIELD_NUMBER, $this->_("Custom field number that can be used by %s.", Gpf_Settings::get(Pap_Settings::BRANDING_QUALITYUNIT_PAP)));
        $this->addTextBox($this->_("Security code"), self::SECURITY_CODE, $this->_("Security code is used to prevent IPN frauds."));
        $this->addCheckBox($this->_("Allow test sales"), self::ALLOW_TEST_SALES, $this->_("Register also test sales. This setting should be off in live system."));
        $this->addCheckBox($this->_("Different commissions for recurring sales"), self::DIFF_RECURRING_COMMISSIONS, $this->_("If checked, then the recurring commission settings will be used for recurring sales. Otherwise recurring sales have same commission as first sale. Set reccurence type to 'Varied' in recurring commission settings."));
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::CUSTOM_FIELD_NUMBER, $form->getFieldValue(self::CUSTOM_FIELD_NUMBER));
        Gpf_Settings::set(self::SECURITY_CODE, $form->getFieldValue(self::SECURITY_CODE));
        Gpf_Settings::set(self::ALLOW_TEST_SALES, $form->getFieldValue(self::ALLOW_TEST_SALES));
        Gpf_Settings::set(self::DIFF_RECURRING_COMMISSIONS, $form->getFieldValue(self::DIFF_RECURRING_COMMISSIONS));
        $form->setInfoMessage($this->_('AlertPay plugin configuration saved'));
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
        $form->addField(self::SECURITY_CODE, Gpf_Settings::get(self::SECURITY_CODE));
        $form->addField(self::ALLOW_TEST_SALES, Gpf_Settings::get(self::ALLOW_TEST_SALES));
        $form->addField(self::DIFF_RECURRING_COMMISSIONS, Gpf_Settings::get(self::DIFF_RECURRING_COMMISSIONS));
        return $form;
    }
}

?>
