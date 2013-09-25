<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailOutbox.class.php 29662 2010-10-25 08:32:20Z mbebjak $
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
class Gpf_Db_Table_MailOutbox extends Gpf_DbEngine_Table {
    const STATUS_PENDING = 'p';
    const STATUS_SENDING = 's';
    const STATUS_READY = 'r';

    const MAX_RETRY_NR = 10;

    const ID = "outboxid";
    const MAILACCOUNTID = "mailaccountid";
    const SCHNEDULET_AT = "scheduled_at";
    const STATUS = "status";
    const LASTR_RETRY = "last_retry";
    const RETRY_NR = "retry_nr";
    const ERROR_MSG = "error_msg";
    const MAIL_ID = "mailid";


    private static $instance;

    /**
     * @return Gpf_Db_Table_MailOutbox
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('g_mail_outbox');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'int', 0, true);
        $this->createColumn(self::MAILACCOUNTID, 'char', 8);
        $this->createColumn(self::SCHNEDULET_AT, 'datetime');
        $this->createColumn(self::STATUS, 'char', 1);
        $this->createColumn(self::LASTR_RETRY, 'datetime');
        $this->createColumn(self::RETRY_NR, 'int');
        $this->createColumn(self::ERROR_MSG, 'text');
        $this->createColumn(self::MAIL_ID, 'int');
    }

    protected function initConstraints() {
        $this->addCascadeDeleteConstraint(self::MAIL_ID, Gpf_Db_Table_Mails::ID, new Gpf_Db_Mail());
    }
}
?>
