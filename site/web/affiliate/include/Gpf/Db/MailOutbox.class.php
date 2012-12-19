<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailOutbox.class.php 24359 2009-05-11 12:00:06Z vzeman $
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
class Gpf_Db_MailOutbox extends Gpf_DbEngine_Row {

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_MailOutbox::getInstance());
        parent::init();
    }

    public function insert() {
        if (!$this->get('scheduled_at')) {
            $this->set('scheduled_at', $this->createDatabase()->getDateString());
        }
        if (!$this->get('status')) {
            $this->set('status', Gpf_Db_Table_MailOutbox::STATUS_PENDING);
        }
        $this->set('retry_nr', 0);
        $this->set('error_msg', '');
        parent::insert();
    }

    public function getId() {
        return $this->get(Gpf_Db_Table_MailOutbox::ID);
    }

    public function sendNow() {
        try {
            //start outbox runner on only one mail
            $outboxRunner = new Gpf_Mail_OutboxRunner();
            $outboxRunner->execute(array($this->getId()));
        } catch (Gpf_Tasks_LongTaskInterrupt $e) {
        }
    }
}
?>
