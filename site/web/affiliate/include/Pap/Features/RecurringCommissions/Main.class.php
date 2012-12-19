<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Features_RecurringCommissions_Main extends Gpf_Plugins_Handler {

	public static function getHandlerInstance() {
		return new Pap_Features_RecurringCommissions_Main();
	}

	public function loadRecurringCommissionsToForm(Pap_Merchants_Campaign_CommissionTypeRpcForm $commissionForm) {
	    $commissionForm->loadSubTypeCommissions(Pap_Db_Table_Commissions::SUBTYPE_RECURRING);
	}

    public function saveRecurringCommissionsFromForm(Pap_Merchants_Campaign_CommissionTypeRpcForm $commissionForm) {
        if (!$commissionForm->existsField('recurrencepresetid')
            || $commissionForm->getFieldValue('recurrencepresetid') == Pap_Db_CommissionType::RECURRENCE_NONE) {
            $commissionForm->deleteUnusedCommissions(0, Pap_Db_Table_Commissions::SUBTYPE_RECURRING);
        } else {
            $commissionForm->saveSubtypeCommissions(Pap_Db_Table_Commissions::SUBTYPE_RECURRING);
        }
    }

    public function saveCommissions(Pap_Contexts_Tracking $context) {
        $context->debug('Saving recurring commissions started');

        $commissionType = $context->getCommissionTypeObject();
        if ($commissionType == null) {
            $context->debug('No commission type defined. Recurring commissions saving ended');
            return;
        }
        
        if ($commissionType->getRecurrencePresetId() == Pap_Db_CommissionType::RECURRENCE_NONE
           || $commissionType->getRecurrencePresetId() == null) {
               $context->debug('Saving recurring commissions ended - No recurring commissions defined');
               return;
        }

        $tier = 1;
        $currentUser = $context->getUserObject();
        $currentCommission = $context->getCommission($tier, Pap_Db_Table_Commissions::SUBTYPE_RECURRING);

        if ($currentUser == null || $currentCommission == null) {
            $context->debug('Saving recurring commissions ended - current user or current commission is null');
            return;
        }
        $recurringCommission = new Pap_Db_RecurringCommission();
        $recurringCommission->setTransactionId($context->getTransaction()->getId());
        $recurringCommission->setRecurrencePresetId($commissionType->getRecurrencePresetId());
        $recurringCommission->setCommissionTypeId($commissionType->getId());
        $recurringCommission->setStatus($context->getTransaction()->getStatus());
        $recurringCommission->setLastCommissionDate($context->getTransaction()->getDateInserted());
        $recurringCommission->insert();
        $context->debug('Recurring commission successfully saved.');

        while($currentUser != null && $currentCommission != null && $tier < 100) {
            $rcEntry = new Pap_Db_RecurringCommissionEntry();
            $rcEntry->setRecurringCommissionId($recurringCommission->getId());
            $rcEntry->setUserId($currentUser->getId());
            $rcEntry->setTier($tier);
            $rcEntry->setCommission($currentCommission->getCommission($context->getRealTotalCost()));
            $rcEntry->insert();

            $tier++;
            $currentUser = $currentUser->getParentUser();
            $currentCommission = $context->getCommission($tier, Pap_Db_Table_Commissions::SUBTYPE_RECURRING);
        }


        $context->debug('Saving recurring commissions ended');
        $context->debug("");
    }

    public function addToMenu(Gpf_Menu $menu) {
        $menu->getItem('Transactions-Overview')->addItem('Recurring-Commissions', $this->_('Recurring Commissions'));
    }
    
	public function initTransactionTypes(Pap_Stats_TransactionTypeStats $transactionTypes) {
		$transactionTypes->addTransType(Pap_Common_Constants::TYPE_RECURRING);
    }
    
    public function initDataTypes(Pap_Common_Reports_StatisticsBase $statistics) {                
		$statistics->addDataType(new Pap_Common_Reports_Chart_TransactionDataType(
            'recurringCount', $this->_('Number of Recurring'), Pap_Stats_Computer_Graph_Transactions::COUNT, Pap_Common_Constants::TYPE_RECURRING));
        $statistics->addDataType(new Pap_Common_Reports_Chart_TransactionDataType(
            'recurringCommission', $this->_('Commission of Recurring'), Pap_Stats_Computer_Graph_Transactions::COMMISSION, Pap_Common_Constants::TYPE_RECURRING));
        $statistics->addDataType(new Pap_Common_Reports_Chart_TransactionDataType(
            'recurringTotalCost', $this->_('Revenue of Recurring'), Pap_Stats_Computer_Graph_Transactions::TOTALCOST, Pap_Common_Constants::TYPE_RECURRING));
    }

    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    public static function getRecurringSelect($orderId, $userId = null) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Pap_Db_Table_RecurringCommissions::getInstance(), 'rc');
        $select->from->add(Pap_Db_Table_RecurringCommissions::getName(), 'rc');
        $select->from->addInnerJoin(Pap_Db_Table_Transactions::getName(), 't',
                                    'rc.'.Pap_Db_Table_RecurringCommissions::TRANSACTION_ID.' = t.'.Pap_Db_Table_Transactions::TRANSACTION_ID);
        $select->where->add('t.'.Pap_Db_Table_Transactions::ORDER_ID, '=', $orderId);
        $select->where->add('rc.'.Pap_Db_Table_RecurringCommissions::STATUS, '=', Pap_Common_Constants::STATUS_APPROVED);
        if (!is_null($userId)) {
            $select->where->add('t.'.Pap_Db_Table_Transactions::USER_ID, '=', $userId);
        }
        return $select;
    }
}
?>
