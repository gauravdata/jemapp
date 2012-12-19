<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
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
class Pap_Db_Accounting extends Gpf_DbEngine_Row {

    const TYPE_FEE = 'F';
    const TYPE_COMMISSSION = 'C';
    
	public function __construct() {
		parent::__construct();
		$date = new Gpf_DateTime();
		$this->setDateInserted($date->toDateTime());
	}

	function init() {
		$this->setTable(Pap_Db_Table_Accountings::getInstance());
		parent::init();
	}

	public function setId($value) {
		$this->set(Pap_Db_Table_Accountings::ID, $value);
	}

	public function setAccountId($value) {
		$this->set(Pap_Db_Table_Accountings::ACCOUNTID, $value);
	}

	public function setAmount($amount) {
		$this->set(Pap_Db_Table_Accountings::AMOUNT, $amount);
	}

	public function setDateInserted($dateInserted) {
		$this->set(Pap_Db_Table_Accountings::DATE_INSTERTED, $dateInserted);
	}

	public function setType($type) {
		$this->set(Pap_Db_Table_Accountings::TYPE, $type);
	}

	public function setInvoiceId($invoiceId) {
		$this->set(Pap_Db_Table_Accountings::INVOICEID, $invoiceId);
	}
}

?>
