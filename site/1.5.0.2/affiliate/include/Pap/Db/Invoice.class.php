<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Db_Invoice extends Gpf_DbEngine_Row {

    public function __construct() {
        parent::__construct();
        $date = new Gpf_DateTime();
        $this->setDateCreated($date->toDateTime());
        $this->setStatus('U');
    }

    function init() {
        $this->setTable(Pap_Db_Table_Invoices::getInstance());
        parent::init();
    }

    public function setId($invoiceId) {
        $this->set(Pap_Db_Table_Invoices::ID, $invoiceId);
    }

    public function getId() {
        return $this->get(Pap_Db_Table_Invoices::ID);
    }

    public function setAccountId($value) {
        $this->set(Pap_Db_Table_Invoices::ACCOUNTID, $value);
    }

    public function getAccountId() {
        return $this->get(Pap_Db_Table_Invoices::ACCOUNTID);
    }

    public function setAmount($amount) {
        $this->set(Pap_Db_Table_Invoices::AMOUNT, $amount);
    }

    public function setDateCreated($dateCreated) {
        $this->set(Pap_Db_Table_Invoices::DATE_CREATED, $dateCreated);
    }

    public function getDateFrom() {
        return $this->get(Pap_Db_Table_Invoices::DATE_FROM);
    }

    public function setDateFrom($dateFrom) {
        $this->set(Pap_Db_Table_Invoices::DATE_FROM, $dateFrom);
    }

    public function setDateTo($dateTo) {
        $this->set(Pap_Db_Table_Invoices::DATE_TO, $dateTo);
    }

    public function setStatus($status) {
        $this->set(Pap_Db_Table_Invoices::STATUS, $status);
    }

    public function getDueDate() {
        return $this->get(Pap_Db_Table_Invoices::DUE_DATE);
    }

    public function getDateTo() {
        return $this->get(Pap_Db_Table_Invoices::DATE_TO);
    }

    public function getNumber() {
        return $this->get(Pap_Db_Table_Invoices::NUMBER);
    }

    public function getAmount() {
        return $this->get(Pap_Db_Table_Invoices::AMOUNT);
    }

    public function getMerchantNote() {
        return $this->get(Pap_Db_Table_Invoices::MERCHANT_NOTE);
    }

    public function getProformaText() {
        return $this->get(Pap_Db_Table_Invoices::PROFORMA_TEXT);
    }

    public function getInvoiceText() {
        return $this->get(Pap_Db_Table_Invoices::INVOICE_TEXT);
    }

    public function getStatus() {
        return $this->get(Pap_Db_Table_Invoices::STATUS);
    }

    public function setNumber($number) {
        $this->set(Pap_Db_Table_Invoices::NUMBER, $number);
    }

    public function setDueDate($dueDate) {
        $this->set(Pap_Db_Table_Invoices::DUE_DATE, $dueDate);
    }

    public function setMerchantNote($merchantNote) {
        $this->set(Pap_Db_Table_Invoices::MERCHANT_NOTE, $merchantNote);
    }

    public function setProformaText($proformaText) {
        $this->set(Pap_Db_Table_Invoices::PROFORMA_TEXT, $proformaText);
    }

    public function setInvoiceText($invoiceText) {
        $this->set(Pap_Db_Table_Invoices::INVOICE_TEXT, $invoiceText);
    }
}

?>
