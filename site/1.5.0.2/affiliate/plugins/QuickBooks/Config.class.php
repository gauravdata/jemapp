<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Matej Kendera
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
class QuickBooks_Config extends Gpf_Plugins_Config {
    const ADD_ACCOUNT = 'QuickBooksAddAccount';
    const ACCOUNT_NAME = 'QuickBooksAccountName';
    const ACCOUNT_TYPE = 'QuickBooksAccountType';
    const TRNS_ACCOUNT_TYPE = 'QuickBooksTrnsAccountType';
    const SPL_ACCOUNT_TYPE = 'QuickBooksSplAccountType';
    const TRNS_TOPRINT = 'QuickBooksTrnsToprint';
    
    protected function initFields() {
        $this->addCheckBox($this->_('Add account'), self::ADD_ACCOUNT, $this->_('Into exported file will be added also account'));
        $this->addTextBox($this->_('Account name'), self::ACCOUNT_NAME, $this->_('Name of QuickBooks account'));
        $this->addTextBox($this->_('Account type'), self::ACCOUNT_TYPE, $this->_('Type of QuickBooks account'));
        $this->addTextBox($this->_('Account type of TRNS'), self::TRNS_ACCOUNT_TYPE, $this->_('Type of QuickBooks transactions account'));
        $this->addTextBox($this->_('Account type of SPL'), self::SPL_ACCOUNT_TYPE, $this->_('Type of QuickBooks SPL account'));
        $this->addCheckBox($this->_('TOPRINT'), self::TRNS_TOPRINT, $this->_('TOPRINT Y/N in transactions'));
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::ADD_ACCOUNT, $form->getFieldValue(self::ADD_ACCOUNT));
        Gpf_Settings::set(self::ACCOUNT_NAME, $form->getFieldValue(self::ACCOUNT_NAME));
        Gpf_Settings::set(self::ACCOUNT_TYPE, $form->getFieldValue(self::ACCOUNT_TYPE));
        Gpf_Settings::set(self::TRNS_ACCOUNT_TYPE, $form->getFieldValue(self::TRNS_ACCOUNT_TYPE));
        Gpf_Settings::set(self::SPL_ACCOUNT_TYPE, $form->getFieldValue(self::SPL_ACCOUNT_TYPE));
        Gpf_Settings::set(self::TRNS_TOPRINT, $form->getFieldValue(self::TRNS_TOPRINT));
        $form->setInfoMessage($this->_('QuickBooks settings saved'));
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
        $form->addField(self::ADD_ACCOUNT, Gpf_Settings::get(self::ADD_ACCOUNT));
        $form->addField(self::ACCOUNT_NAME, Gpf_Settings::get(self::ACCOUNT_NAME));
        $form->addField(self::ACCOUNT_TYPE, Gpf_Settings::get(self::ACCOUNT_TYPE));
        $form->addField(self::TRNS_ACCOUNT_TYPE, Gpf_Settings::get(self::TRNS_ACCOUNT_TYPE));
        $form->addField(self::SPL_ACCOUNT_TYPE, Gpf_Settings::get(self::SPL_ACCOUNT_TYPE));
        $form->addField(self::TRNS_TOPRINT, Gpf_Settings::get(self::TRNS_TOPRINT));
        return $form;
    }
}

?>
