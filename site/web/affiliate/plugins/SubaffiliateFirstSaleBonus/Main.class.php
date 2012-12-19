<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @author Juraj Simon
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
 * @package PostAffiliatePro plugins
 */
class SubaffiliateFirstSaleBonus_Main extends Gpf_Plugins_Handler {

    /**
     * @return SubaffiliateFirstSaleBonus_Main
     */
    public static function getHandlerInstance() {
        return new SubaffiliateFirstSaleBonus_Main();
    }

    public function initSettings($context) {
        $context->addDbSetting(SubaffiliateFirstSaleBonus_Config::VALIDITY_DAYS, '90');
        $context->addDbSetting(SubaffiliateFirstSaleBonus_Config::BONUS_TYPE, '%');
        $context->addDbSetting(SubaffiliateFirstSaleBonus_Config::BONUS_AMOUNT, '0');
    }

    public function process(Pap_Common_TransactionCompoundContext $context) {

        $transaction = $context->getTransaction();
        $context->getContext()->debug("SubaffiliateFirstSaleBonus started");
        if($transaction->getTier() != 2) {
            $context->getContext()->debug("SubaffiliateFirstSaleBonus ended - Not 2. tier");
            return;
        }

        $row = $this->getChildAffiliateInfo($transaction);

        $date = new Gpf_DateTime(time());
        $date->addDay(-Gpf_Settings::get(SubaffiliateFirstSaleBonus_Config::VALIDITY_DAYS));

        if(Gpf_Common_DateUtils::getDateTime($date->toTimeStamp()) > $row->get(Pap_Db_Table_Users::DATEINSERTED)) {
            $context->getContext()->debug("SubaffiliateFirstSaleBonus ended - Date Inserted is older than defined days.");
            return;
        }

        if($this->getChildAffiliateTransactionsCount($row->get(Pap_Db_Table_Transactions::USER_ID)) > 1) {
            $context->getContext()->debug("SubaffiliateFirstSaleBonus ended - Not user's first transaction.");
            return;
        }

        if(Gpf_Settings::get(SubaffiliateFirstSaleBonus_Config::BONUS_TYPE) == '%') {
            $transaction->setCommission($transaction->getCommission()+(Gpf_Settings::get(SubaffiliateFirstSaleBonus_Config::BONUS_AMOUNT)/100)*$transaction->getTotalCost());
        } else{
            $transaction->setCommission(Gpf_Settings::get(SubaffiliateFirstSaleBonus_Config::BONUS_AMOUNT)+$transaction->getCommission());
        }
        $context->getContext()->debug("SubaffiliateFirstSaleBonus ended - Success.");
        return;
    }

    /**
     *
     * @param $transaction
     * @return Gpf_Data_Record
     */
    protected function getChildAffiliateInfo(Pap_Common_Transaction $transaction){
        $userSelect = new Gpf_SqlBuilder_SelectBuilder();
        $userSelect->select->add('t.'.Pap_Db_Table_Transactions::USER_ID);
        $userSelect->select->add('u.'.Pap_Db_Table_Users::DATEINSERTED);
        $userSelect->from->add(Pap_Db_Table_Transactions::getName(),'t');
        $userSelect->from->addInnerJoin(Pap_Db_Table_Users::getName(),'u','t.userid=u.userid');
        $userSelect->where->add('t.'.Pap_Db_Table_Transactions::TRANSACTION_ID,'=',$transaction->getParentTransactionId());
        return $userSelect->getOneRow();
    }

    protected function getChildAffiliateTransactionsCount($userId){
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('count('.Pap_Db_Table_Transactions::USER_ID.')','numberOfTransactions');
        $select->from->add(Pap_Db_Table_Transactions::getName());
        $select->where->add(Pap_Db_Table_Transactions::USER_ID,'=',$userId);
        $row = $select->getOneRow();
        return $row->get('numberOfTransactions');
    }
}
?>
