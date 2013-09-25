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
class Pap_Db_Table_Payouts extends Gpf_DbEngine_Table {
    const ID = 'payoutid';
    const USER_ID = 'userid';
    const PAYOUT_HISTORY_ID = 'payouthistoryid';
    const AFFILIATE_NOTE = 'affiliatenote';
    const AMOUNT = 'amount';
    const CURRENCY_ID = 'currencyid';
    const INVOICE = 'invoice';
    const INVOICENUMBER = 'invoicenumber';
    
    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('pap_payout');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
        $this->createColumn(self::USER_ID, self::CHAR, 8);
        $this->createColumn(self::PAYOUT_HISTORY_ID, self::CHAR, 8);
        $this->createColumn(self::AFFILIATE_NOTE, self::CHAR);
        $this->createColumn(self::AMOUNT, self::INT);
        $this->createColumn(self::CURRENCY_ID, self::CHAR, 8);
        $this->createColumn(self::INVOICE, self::CHAR);
        $this->createColumn(self::INVOICENUMBER, self::INT);
    }
}
?>
