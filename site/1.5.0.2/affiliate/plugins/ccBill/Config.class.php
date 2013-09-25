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
class ccBill_Config extends Gpf_Plugins_Config {
    const REGISTER_AFFILIATE = 'ccBillRegisterAffiliate';
    const PROCESS_REBILL = 'ccBillProcessRefundChargeback';
    const USE_RECURRING_COMMISSION = 'ccBillUseRecurringCommission';
    const PROCESS_REBILL_TIMEFRAME = 'ccBillProcessRefundChargebackTimeframe';
    const CCBILL_ACCOUNT_NUMBER = 'ccBillAccountNumber';
    const CCBILL_SUBACCOUNT_NUMBER = 'ccBillSubAccountNumber';
    const CCBILL_ACCOUNT_USERNAME = 'ccBillAccountUsername';
    const CCBILL_ACCOUNT_PASSWORD = 'ccBillAccountPassword';
    
    protected function initFields() {
        $this->addCheckBox($this->_("Register new affiliate with every occured event"), self::REGISTER_AFFILIATE, $this->_('When this checked, with every event new affiliate will be created from credentials that were set in ccBill submit form.'));
        $this->addCheckBox($this->_("Process rebills"), self::PROCESS_REBILL, $this->_('If you want to process refunds or chargebacks, you need to set up your ccBill account'));
        $this->addCheckBox($this->_("Use recurring commission feature to handle the recurring payments"), self::USE_RECURRING_COMMISSION, $this->_('If recurring commission feature is avaliable, it will be used to handle all recurring payments from ccBill'));
        $this->addListBox($this->_("Refunds and chargebacks processing time frame"), self::PROCESS_REBILL_TIMEFRAME, array(Pap_Db_CommissionType::RECURRENCE_WEEKLY=>'One week',Pap_Db_CommissionType::RECURRENCE_MONTHLY=>'One month'), $this->_('Maximum timeframe, that will be checked for refunds or chargebacks changes'));
        $this->addTextBox($this->_("ccBill account number"), self::CCBILL_ACCOUNT_NUMBER, $this->_('Insert here your ccBill account number'));
        $this->addTextBox($this->_("ccBill subaccount number"), self::CCBILL_SUBACCOUNT_NUMBER, $this->_('Insert here your ccBill subaccount number'));
        $this->addTextBox($this->_("ccBill datalink username"), self::CCBILL_ACCOUNT_USERNAME, $this->_('Insert here your ccBill datalink username'));
        $this->addPasswordTextBox($this->_("ccBill datalink password"), self::CCBILL_ACCOUNT_PASSWORD, $this->_('Insert here your ccBill datalink password'));
    }
    
    private function getPlannedTask() {
    	$plannedTask = new Gpf_Db_PlannedTask();
        $plannedTask->setClassName('ccBill_CheckRebill');
        return $plannedTask;
    }
    
    /**
     * @anonym
     * @service ccbill_settings write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        Gpf_Settings::set(self::REGISTER_AFFILIATE, $form->getFieldValue(self::REGISTER_AFFILIATE));        
        Gpf_Settings::set(self::PROCESS_REBILL_TIMEFRAME, $form->getFieldValue(self::PROCESS_REBILL_TIMEFRAME));
        Gpf_Settings::set(self::CCBILL_ACCOUNT_NUMBER, $form->getFieldValue(self::CCBILL_ACCOUNT_NUMBER));
        Gpf_Settings::set(self::CCBILL_SUBACCOUNT_NUMBER, $form->getFieldValue(self::CCBILL_SUBACCOUNT_NUMBER));
        Gpf_Settings::set(self::CCBILL_ACCOUNT_USERNAME, $form->getFieldValue(self::CCBILL_ACCOUNT_USERNAME));
        Gpf_Settings::set(self::CCBILL_ACCOUNT_PASSWORD, $form->getFieldValue(self::CCBILL_ACCOUNT_PASSWORD));
        Gpf_Settings::set(self::PROCESS_REBILL, $form->getFieldValue(self::PROCESS_REBILL));
        Gpf_Settings::set(self::USE_RECURRING_COMMISSION, $form->getFieldValue(self::USE_RECURRING_COMMISSION));
        if ($form->getFieldValue(self::PROCESS_REBILL) == Gpf::YES) {
        	$plannedTask = $this->getPlannedTask();            
            try {
                $plannedTask->loadFromData(array(Gpf_Db_Table_PlannedTasks::CLASSNAME));
                $plannedTask->setRecurrencePresetId($form->getFieldValue(self::PROCESS_REBILL_TIMEFRAME));
                $plannedTask->update(array(Gpf_Db_Table_PlannedTasks::RECURRENCEPRESETID));
            } catch (Gpf_Exception $e) {
                $plannedTask->setRecurrencePresetId($form->getFieldValue(self::PROCESS_REBILL_TIMEFRAME));
                $plannedTask->insert();
            }                       
        } else {
        	try {
        	   $plannedTask = $this->getPlannedTask();
               $plannedTask->loadFromData(array(Gpf_Db_Table_PlannedTasks::CLASSNAME));
               $plannedTask->delete();
        	} catch (Gpf_Exception $e) {}
        }
        $form->setInfoMessage($this->_('ccBill settings saved'));
        return $form;
    }

    /**
     * @anonym
     * @service ccbill_settings read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addField(self::REGISTER_AFFILIATE, Gpf_Settings::get(self::REGISTER_AFFILIATE));
        $form->addField(self::PROCESS_REBILL, Gpf_Settings::get(self::PROCESS_REBILL));
        $form->addField(self::USE_RECURRING_COMMISSION, Gpf_Settings::get(self::USE_RECURRING_COMMISSION));
        $form->addField(self::PROCESS_REBILL_TIMEFRAME, Gpf_Settings::get(self::PROCESS_REBILL_TIMEFRAME));
        $form->addField(self::CCBILL_ACCOUNT_NUMBER, Gpf_Settings::get(self::CCBILL_ACCOUNT_NUMBER));
        $form->addField(self::CCBILL_SUBACCOUNT_NUMBER, Gpf_Settings::get(self::CCBILL_SUBACCOUNT_NUMBER));
        $form->addField(self::CCBILL_ACCOUNT_USERNAME, Gpf_Settings::get(self::CCBILL_ACCOUNT_USERNAME));
        $form->addField(self::CCBILL_ACCOUNT_PASSWORD, Gpf_Settings::get(self::CCBILL_ACCOUNT_PASSWORD));
        return $form;
    }
}

?>
