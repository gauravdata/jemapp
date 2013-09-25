<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: ActiveViews.class.php 18002 2008-05-13 18:31:39Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Db_Table_Invoices extends Gpf_DbEngine_Table {

    const ID = 'invoiceid';
    const ACCOUNTID = 'accountid';
    const DATE_CREATED = 'datecreated';
    const DUE_DATE = 'duedate';
    const DATE_FROM = 'datefrom';
    const DATE_TO = 'dateto';
    const STATUS = 'rstatus';
    const NUMBER = 'number';
    const AMOUNT = 'amount';
    const MERCHANT_NOTE = 'merchantnote';
    const SYSTEM_NOTE = "systemnote";
    const PROFORMA_TEXT = 'proformatext';
    const INVOICE_TEXT = 'invoicetext';
    const DATE_PAID = 'datepaid';

    private static $instance;

    /**
     * @return Pap_Db_Table_Invoices
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * @throws Gpf_DbEngine_NoRowException
     * @param $accountId
     * @return String
     */
    public function getLastInvoiceDateCreated($accountId) {
        return $this->getLastInvoiceVariable($accountId, self::DATE_CREATED);
    }
    
    /**
     * @throws Gpf_DbEngine_NoRowException
     * @param $accountId
     * @return String
     */
    public function getNextInvoiceDateFrom($accountId) {
		$lastInvoiceDateTo = $this->getLastInvoiceVariable($accountId, self::DATE_TO);
		$date = new Gpf_DateTime($lastInvoiceDateTo);
        $date->addDay(1);
        return $date->getDayStart()->toDateTime();
    }

    protected function initName() {
        $this->setName('pap_invoices');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::ACCOUNTID, self::CHAR, 8);
        $this->createColumn(self::DATE_CREATED, self::DATETIME);
        $this->createColumn(self::DUE_DATE, self::DATETIME);
        $this->createColumn(self::DATE_FROM, self::DATETIME);
        $this->createColumn(self::DATE_TO, self::DATETIME);
        $this->createColumn(self::STATUS, self::CHAR, 1);
        $this->createColumn(self::NUMBER, self::CHAR, 40);
        $this->createColumn(self::AMOUNT, self::FLOAT);
        $this->createColumn(self::MERCHANT_NOTE, self::CHAR);
        $this->createColumn(self::SYSTEM_NOTE, self::CHAR);
        $this->createColumn(self::PROFORMA_TEXT, self::CHAR);
        $this->createColumn(self::INVOICE_TEXT, self::CHAR);
        $this->createColumn(self::DATE_PAID, self::DATETIME);
    }

    protected function initConstraints() {
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Accountings::INVOICEID, new Pap_Db_Accounting());
    }
    
    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    public function getLastAccountingCommissionSelect() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('i.'.Pap_Db_Table_Invoices::ACCOUNTID, 'aid');
        $select->select->add('MAX(i.'.Pap_Db_Table_Invoices::DATE_TO.')', 'lastDate');
        $select->from->add(Pap_Db_Table_Accountings::getName(), 'a');
        $select->from->addInnerJoin(Pap_Db_Table_Invoices::getName(), 'i',
            'a.'.Pap_Db_Table_Accountings::INVOICEID.'=i.'.Pap_Db_Table_Invoices::ID);
        $select->where->add(Pap_Db_Table_Accountings::TYPE, '=', Pap_Db_Accounting::TYPE_COMMISSSION);
        $select->groupBy->add('i.'.Pap_Db_Table_Invoices::ACCOUNTID);
        return $select;
    }
    
    /**
     * @throws Gpf_DbEngine_NoRowException
     * @param $accountId
     * @param $columnName
     * @return String
     */    
    private function getLastInvoiceVariable($accountId, $columnName) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add($columnName);
        $select->from->add(Pap_Db_Table_Invoices::getName());
        $select->where->add(Pap_Db_Table_Invoices::ACCOUNTID, '=', $accountId);
        $select->orderBy->add($columnName, false);
        $select->limit->set(0, 1);
        return $select->getOneRow()->get($columnName);
    }
}

?>
