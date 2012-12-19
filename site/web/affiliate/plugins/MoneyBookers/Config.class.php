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
class MoneyBookers_Config extends Gpf_Plugins_Config {
    const FIELD_NUMBER = 'MoneyBookersFieldNumber';
    const ALLOW_TEST_SALES = 'MoneyBookersAllowTestSales';
    const SECRET_WORD = 'MoneyBookersSecretWord';
    
    protected function initFields() {
        $this->addTextBox($this->_("Custom field number (1-5)"), self::FIELD_NUMBER, $this->_("Custom field number that can be used by PAP."));
        $this->addTextBox($this->_("Secret word"), self::SECRET_WORD, $this->_("Secret word set in MoneyBookers Merchant Tools used for notification."));        
        $this->addCheckBox($this->_("Allow test sales"), self::ALLOW_TEST_SALES, $this->_("Register also test sales. This setting should be off in live system."));
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::FIELD_NUMBER, $form->getFieldValue(self::FIELD_NUMBER));
        Gpf_Settings::set(self::SECRET_WORD, $form->getFieldValue(self::SECRET_WORD));
        Gpf_Settings::set(self::ALLOW_TEST_SALES, $form->getFieldValue(self::ALLOW_TEST_SALES));
        $form->setInfoMessage($this->_('MoneyBookers plugin configuration saved'));
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
        $form->addField(self::FIELD_NUMBER, Gpf_Settings::get(self::FIELD_NUMBER));
        $form->addField(self::SECRET_WORD, Gpf_Settings::get(self::SECRET_WORD));
        $form->addField(self::ALLOW_TEST_SALES, Gpf_Settings::get(self::ALLOW_TEST_SALES));
        return $form;
    }
}

?>
