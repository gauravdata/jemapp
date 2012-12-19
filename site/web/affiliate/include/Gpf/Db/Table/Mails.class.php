<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Mails.class.php 29662 2010-10-25 08:32:20Z mbebjak $
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
class Gpf_Db_Table_Mails extends Gpf_DbEngine_Table {
    const ID = "mailid";
    const HDR_MESSAGE_ID = "hdr_message_id";
    const UNIQUE_MESSAGE_ID = "unique_message_id";
    const SUBJECT = "subject";
    const HEADERS = "headers";
    const BODY_TEXT = "body_text";
    const BODY_HTML = "body_html";
    const CREATED = "created";
    const DELIVERED = "delivered";
    const FROM_MAIL = "from_mail";
    const TO_RECIPIENTS = "to_recipients";
    const CC_RECIPIENTS= "cc_recipients";
    const BCC_RECIPIENTS = "bcc_recipients";
    const ACCOUNTUSERID= "accountuserid";
    const REPLY_TO = "reply_to";

    private static $instance;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('g_mails');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'int', 0, true);
        $this->createColumn(self::HDR_MESSAGE_ID, 'char', 255);
        $this->createColumn(self::UNIQUE_MESSAGE_ID, 'char', 255);
        $this->createColumn(self::SUBJECT, 'text');
        $this->createColumn(self::HEADERS, 'text');
        $this->createColumn(self::BODY_TEXT, 'text');
        $this->createColumn(self::BODY_HTML, 'text');
        $this->createColumn(self::CREATED, 'datetime');
        $this->createColumn(self::DELIVERED, 'datetime');
        $this->createColumn(self::FROM_MAIL, 'text');
        $this->createColumn(self::TO_RECIPIENTS, 'text');
        $this->createColumn(self::CC_RECIPIENTS, 'text');
        $this->createColumn(self::BCC_RECIPIENTS, 'text');
        $this->createColumn(self::ACCOUNTUSERID, 'char', 8);
        $this->createColumn(self::REPLY_TO, 'char', 255);
    }

    protected function initConstraints() {
        $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_MailAttachments::MAIL_ID, new Gpf_Db_MailAttachment());
    }
}
?>
