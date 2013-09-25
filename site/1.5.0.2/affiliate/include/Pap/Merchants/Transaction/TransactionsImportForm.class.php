<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Ivan Ivanco, Maros Galik
 *   @since Version 1.0.0
 *   $Id: TransactionsForm.class.php 27999 2010-05-04 11:39:31Z mgalik $
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
class Pap_Merchants_Transaction_TransactionsImportForm extends Gpf_Object {
    const SUCCESS = 'success';
    /**
     * @var Gpf_Data_RecordSet
     */
    private $names;

    /**
     * @service transaction write
     */
    public function importCSV(Gpf_Rpc_Params $params){
        $form = new Gpf_Rpc_Form($params);
        $importTask = new Pap_Merchants_Transaction_TransactionsImportTask($form, 'Pap_Merchants_Transaction_TransactionsImportTask');
        try {
            $importTask->run();
            $form->setField(self::SUCCESS, Gpf::YES);
        } catch (Gpf_Tasks_LongTaskInterrupt $e) {
            $form->setField(self::SUCCESS, Gpf::NO);
            $form->setInfoMessage($e->getMessage());
        } catch (Exception $e) {
            $importTask->forceFinishTask();
            $form->setField(self::SUCCESS, Gpf::YES);
            $form->setErrorMessage($this->_('Error during Import') . ' (' . $e->getMessage() . ') ');
        }
        return $form;
    }

    /**
     *
     * @service transaction write
     * @return Gpf_Data_RecordSet
     */
    public function getColumnNames() {
        $this->names = new Gpf_Data_RecordSet();
        $this->names->setHeader(new Gpf_Data_RecordHeader(array('code', 'view')));

        $this->addColumn(Pap_Db_Table_Transactions::TRANSACTION_ID, $this->_("ID"));
        $this->addColumn(Pap_Db_Table_Transactions::ACCOUNT_ID, $this->_("Account ID"));
        $this->addColumn(Pap_Db_Table_Transactions::USER_ID, $this->_("User ID"));
        $this->addColumn(Pap_Db_Table_Transactions::BANNER_ID, $this->_("Banner ID"));
        $this->addColumn(Pap_Db_Table_Transactions::PARRENT_BANNER_ID, $this->_("Parent Banner ID"));
        $this->addColumn(Pap_Db_Table_Transactions::CAMPAIGN_ID, $this->_("Campaign ID"));
        $this->addColumn(Pap_Db_Table_Transactions::COUNTRY_CODE, $this->_("Country Code"));
        $this->addColumn(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, $this->_("Parent Transaction"));
        $this->addColumn(Pap_Db_Table_Transactions::R_STATUS, $this->_("Status"));
        $this->addColumn(Pap_Db_Table_Transactions::R_TYPE, $this->_("Type"));
        $this->addColumn(Pap_Db_Table_Transactions::DATE_INSERTED, $this->_("Date inserted"));
        $this->addColumn(Pap_Db_Table_Transactions::DATE_APPROVED, $this->_("Date approved"));
        $this->addColumn(Pap_Db_Table_Transactions::PAYOUT_STATUS, $this->_("Payout status"));
        $this->addColumn(Pap_Db_Table_Transactions::REFERER_URL, $this->_("Referrer URL"));
        $this->addColumn(Pap_Db_Table_Transactions::IP, $this->_("IP"));
        $this->addColumn(Pap_Db_Table_Transactions::BROWSER, $this->_("Browser"));
        $this->addColumn(Pap_Db_Table_Transactions::COMMISSION, $this->_("Commission"));
        $this->addColumn(Pap_Db_Table_Transactions::RECURRING_COMM_ID, $this->_("Recurring Commission ID"));
        $this->addColumn(Pap_Db_Table_Transactions::CLICK_COUNT, $this->_("Click Count"));
        $this->addColumn(Pap_Db_Table_Transactions::TRACK_METHOD, $this->_("Tracking Method"));
        $this->addColumn(Pap_Db_Table_Transactions::ORDER_ID, $this->_("Order ID"));
        $this->addColumn(Pap_Db_Table_Transactions::PRODUCT_ID, $this->_("Product ID"));
        $this->addColumn(Pap_Db_Table_Transactions::TOTAL_COST, $this->_("Total cost"));
        $this->addColumn(Pap_Db_Table_Transactions::FIXED_COST, $this->_("Fixed cost"));
        $this->addColumn(Pap_Db_Table_Transactions::DATA1, $this->_("Extra data 1"));
        $this->addColumn(Pap_Db_Table_Transactions::DATA2, $this->_("Extra data 2"));
        $this->addColumn(Pap_Db_Table_Transactions::DATA3, $this->_("Extra data 3"));
        $this->addColumn(Pap_Db_Table_Transactions::DATA4, $this->_("Extra data 4"));
        $this->addColumn(Pap_Db_Table_Transactions::DATA5, $this->_("Extra data 5"));
        $this->addColumn(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_ID, $this->_("Original currency ID"));
        $this->addColumn(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_VALUE, $this->_("Original currency value"));
        $this->addColumn(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_RATE, $this->_("Original currency rate"));
        $this->addColumn(Pap_Db_Table_Transactions::COMMISSIONTYPEID, $this->_("Commission type ID"));
        $this->addColumn(Pap_Db_Table_Transactions::MERCHANTNOTE, $this->_("Merchant note"));
        $this->addColumn(Pap_Db_Table_Transactions::SYSTEMNOTE, $this->_("System note"));
        $this->addColumn(Pap_Db_Table_Transactions::COUPON_ID, $this->_("Coupon ID"));
        $this->addColumn(Pap_Db_Table_Transactions::VISITOR_ID, $this->_("Visitor ID"));
        $this->addColumn(Pap_Db_Table_Transactions::SALE_ID, $this->_("Sale ID"));
        $this->addColumn(Pap_Db_Table_Transactions::SPLIT, $this->_("Split"));
        $this->addColumn(Pap_Db_Table_Transactions::LOGGROUPID, $this->_("Log group ID"));

        return $this->names;
    }

    private function addColumn($code, $view) {
        $name = $this->names->createRecord();
        $name->set('code', $code);
        $name->set('view', $view);
        $this->names->add($name);
    }
}

?>
