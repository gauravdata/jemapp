<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: RawClicks.class.php 27379 2010-02-24 09:21:35Z mjancovic $
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
class Pap_Db_Table_RawClicks extends Gpf_DbEngine_Table {

	const ID = "clickid";
	const USERID = "userid";
	const BANNERID = "bannerid";
	const CAMPAIGNID = "campaignid";
	const PARENTBANNERID = "parentbannerid";
	const COUNTRYCODE = "countrycode";
	const RTYPE = "rtype";
	const DATETIME = 'datetime';
	const REFERERURL = "refererurl";
	const IP = "ip";
	const BROWSER = "browser";
	const DATA1 = "cdata1";
	const DATA2 = "cdata2";
    const CHANNEL = "channel";
	
	const RSTATUS = "rstatus";

	private static $instance;
	    
	public static function getInstance() {
	    if(self::$instance === null) {
	        self::$instance = new self;
	    }
	    return self::$instance;
	}
	    
	protected function initName() {
	    $this->setName('pap_rawclicks');
	}
    
	public static function getName() {
        return self::getInstance()->name();
    }
	
	function initColumns() {
		$this->createPrimaryColumn(self::ID, 'int');
		$this->createColumn(self::USERID, 'char', 8);
		$this->createColumn(self::BANNERID, 'char', 8);
		$this->createColumn(self::CAMPAIGNID, 'char', 8);
		$this->createColumn(self::PARENTBANNERID, 'char', 8);
		$this->createColumn(self::COUNTRYCODE, 'char', 2);
		$this->createColumn(self::RTYPE, 'char', 1);
		$this->createColumn(self::DATETIME, 'datetime');
		$this->createColumn(self::REFERERURL, 'char', 250);
		$this->createColumn(self::IP, 'char', 39);
		$this->createColumn(self::BROWSER, 'char', 6);
		$this->createColumn(self::DATA1, 'char', 255);
		$this->createColumn(self::DATA2, 'char', 255);
        $this->createColumn(self::CHANNEL, 'char', 10);
		$this->createColumn(self::RSTATUS, 'char', 1);
	}
}

?>
