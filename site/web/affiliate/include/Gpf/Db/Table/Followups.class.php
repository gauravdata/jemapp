<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.7
 *   $Id: Files.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_Table_Followups extends Gpf_DbEngine_Table {
    const ID = 'followupid';
    const DELAY_DAYS = 'delay_days';
    const MODIFIED = 'modified';
    const STATUS = 'followup_status';
    const DELIVERY_HOUR = 'delivery_hour';
    
    private static $instance;
        
    /**
     * @return Gpf_Db_Table_Followups
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('nl_followups');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::DELAY_DAYS, 'integer', 0, true);
        $this->createColumn(self::STATUS, 'char', 1, true);
        $this->createColumn(self::DELIVERY_HOUR, 'integer', 0, true);
        $this->createColumn(Gpf_Db_Table_Newsletters::ID, 'char', 8, true);
        $this->createColumn(Gpf_Db_Table_MailTemplates::ID, 'char', 8, true);
        $this->createColumn(self::MODIFIED, 'datetime', 0, true);
    }
}
?>
