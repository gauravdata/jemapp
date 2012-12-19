<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Campaigns.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Db_Table_PayoutsHistory extends Gpf_DbEngine_Table {
    const ID = 'payouthistoryid';
    const DATEINSERTED = 'dateinserted';
    const MERCHANT_NOTE = 'merchantnote';
    const AFFILIATE_NOTE = 'affiliatenote';
    const DATE_FROM = 'datefrom';
    const DATE_TO = 'dateto';
    const EXPORT_FILE = 'exportfile';
    const ACCOUNTID = 'accountid';

    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('pap_payouthistory');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::DATEINSERTED, self::DATETIME);
        $this->createColumn(self::MERCHANT_NOTE, self::CHAR);
        $this->createColumn(self::AFFILIATE_NOTE, self::CHAR);
        $this->createColumn(self::DATE_FROM, self::DATETIME);
        $this->createColumn(self::DATE_TO, self::DATETIME);
        $this->createColumn(self::EXPORT_FILE, self::CHAR, 200);
        $this->createColumn(self::ACCOUNTID, self::CHAR, 8);
    }
    
    protected function initConstraints() {
       $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, new Pap_Db_Transaction());
    }
}
?>
