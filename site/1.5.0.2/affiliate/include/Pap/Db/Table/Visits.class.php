<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
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
class Pap_Db_Table_Visits extends Gpf_DbEngine_Table {
    const ID = 'visitid';
    const ACCOUNTID = 'accountid';
    const RSTATUS = 'rstatus';
    const VISITORID = 'visitorid';
    const VISITORID_HASH = 'visitoridhash';
    const DATEVISIT = 'datevisit';
	const TRACKMETHOD = 'trackmethod';
    const URL = 'url';
    const REFERRERURL = 'referrerurl';
    const GET_PARAMS = 'get';
    const ANCHOR = 'anchor';
    const SALE_PARAMS = 'sale';
	const COOKIES = 'cookies';
    const IP = 'ip';
    const USER_AGENT = "useragent";

    private static $instance;

	private $index;

	public static function getInstance($index) {
		if(@self::$instance[$index] === null) {
			self::$instance[$index] = new self;
			self::$instance[$index]->index = $index;
        }
		return self::$instance[$index];
    }

    protected function initName() {
        $this->setName('pap_visits');
    }
    
    public function name() {
        return parent::name() . $this->index;
    }

    public static function getName($index) {
        return self::getInstance($index)->name();
    }

    protected function initColumns() {
		$this->createPrimaryColumn(self::ID, self::INT);
		$this->createColumn(self::ACCOUNTID, self::CHAR, 8);
		$this->createColumn(self::RSTATUS, self::CHAR, 1);
		$this->createColumn(self::VISITORID, self::CHAR, 36, true);
		$this->createColumn(self::VISITORID_HASH, self::INT);
		$this->createColumn(self::DATEVISIT, self::DATETIME);
		$this->createColumn(self::TRACKMETHOD, self::CHAR, 1);
		$this->createColumn(self::URL, self::CHAR);
		$this->createColumn(self::REFERRERURL, self::CHAR);
		$this->createColumn(self::GET_PARAMS, self::CHAR);
		$this->createColumn(self::ANCHOR, self::CHAR);
		$this->createColumn(self::SALE_PARAMS, self::CHAR);
		$this->createColumn(self::COOKIES, self::CHAR);
		$this->createColumn(self::IP, self::CHAR, 39);
		$this->createColumn(self::USER_AGENT, self::CHAR);	
    }

}
?>
