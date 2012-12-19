<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Mail.class.php 31079 2011-02-07 13:28:04Z mkendera $
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
class Gpf_Db_Mail extends Gpf_DbEngine_Row {
    /**
     * @var Gpf_Db_MailAccount
     */
    protected $mailAccount = null;

    /**
     * @var Gpf_Db_MailAccount
     */
    private static $defaultMailAccount;

    /**
     * @var Gpf_Templates_Template
     */
    private $templateObject;

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_Mails::getInstance());
        parent::init();
    }

    public function getId() {
        return $this->get(Gpf_Db_Table_Mails::ID);
    }

    /**
     * Replace all variables in text with variable values
     *
     * @param string $text
     * @return string
     */
    private function replaceVariables($text) {
        if (isset($this->templateObject)) {
            return $this->templateObject->getHTML($text);
        }
        return $text;
    }

    /**
     * Set custom template object if shouldn't be used default
     *
     * @param Gpf_Templates_Template $templateObject
     */
    public function setTemplateObject(Gpf_Templates_Template $templateObject) {
        $this->templateObject = $templateObject;
    }

    /**
     * Set mail variable, which can be replaced in subject or body of mail
     *
     * @param string $id
     * @param string $value
     */
    public function setVariable($id, $value) {
        if (!isset($this->templateObject)) {
            $this->templateObject = new Gpf_Templates_Template('gpf_db_mail.tpl');
        }
        $this->templateObject->assign($id, $value);
    }

    public function insert(){
        if (strlen($this->getHtmlBody())) {
            $this->setHtmlBody($this->replaceVariables($this->getHtmlBody()));
        }
        if (strlen($this->getTextBody())) {
            $this->setTextBody($this->replaceVariables($this->getTextBody()));
        }
        $this->setSubject($this->replaceVariables($this->getSubject()));
        $this->set('created', $this->createDatabase()->getDateString());
        $this->set('hdr_message_id', $this->getUniqueMd5());

        if (!strlen($this->get('accountuserid'))) {
            try {
                $userId = Gpf_Session::getAuthUser()->getUserId();
            } catch (Exception $e) {
                $userId = null;
            }
            $this->set('accountuserid', $userId);
        }

        if (!strlen($this->getFromMail())) {
            $this->setFromMail($this->getMailAccountFromEmail());
        }
        $this->set('unique_message_id', $this->getUniqueMd5() . '@gwtphp.com');
        parent::insert();
    }

    private function getUniqueMd5() {
        return md5('Gpf_Db_Mail' . rand());
    }

    /**
     * Schedule mail now to queue and return Outbox object
     *
     */
    public function scheduleNow($sendNow = false, $delayMinutes = 0) {
        $outbox = new Gpf_Db_MailOutbox();
        $valueContext = new Gpf_Plugins_ValueContext($delayMinutes);
        $outboxArray = array();
        
        $outbox->set('mailid', $this->getId());
        $outbox->set('mailaccountid', $this->getMailAccount()->getId());
        $outboxArray[0] = $outbox;
        $valueContext->setArray($outboxArray);
        Gpf_Plugins_Engine::extensionPoint('Gpf_Db_Mail.scheduleNow', $valueContext);
        $outbox->insert();

        if ($sendNow || !$this->isCronRunning()) {
            $outbox->sendNow();
        }
    }
    
    private function isCronRunning() {
        $taskRunner = new Gpf_Tasks_Runner();
        return $taskRunner->isRunningOK();
    }

    public function getHtmlBody() {
        return $this->get('body_html');
    }

    public function setHtmlBody($htmlBody) {
        $this->set('body_html', $htmlBody);
    }

    public function getTextBody() {
        return $this->get('body_text');
    }

    public function setTextBody($htmlBody) {
        $this->set('body_text', $htmlBody);
    }

    public function setSubject($subject) {
        $this->set('subject', $subject);
    }

    public function getSubject() {
        return $this->get('subject');
    }

    public function setRecipients($toRecipients) {
        $this->set('to_recipients', $toRecipients);
    }

    public function setBccRecipients($bccRecipients) {
        $this->set('bcc_recipients', $bccRecipients);
    }


    public function setFromMail($fromMail) {
        $this->set('from_mail', $fromMail);
    }

    public function getFromMail() {
        return $this->get('from_mail');
    }


    private function formatFromMail($email, $name) {
        if (!strlen($name)) {
            return $email;
        }
        return  $name . " <" . $email . ">";
    }

    /**
     * Set custom mail account
     *
     * @param Gpf_Db_MailAccount $account
     */
    public function setMailAccount(Gpf_Db_MailAccount $account) {
        $this->mailAccount = $account;
    }

    /**
     * @return Gpf_Db_MailAccount
     */
    public function getMailAccount() {
        if ($this->mailAccount === null || $this->mailAccount->getId() === null) {
            $this->mailAccount = $this->loadDefaultMailAccount();
        }
        return $this->mailAccount;
    }

    /**
     * Load default mail account
     *
     * @return Gpf_Db_MailAccount
     */
    private function loadDefaultMailAccount() {
        if (self::$defaultMailAccount === null) {
            $mailAccountsTable = Gpf_Db_Table_MailAccounts::getInstance();
            self::$defaultMailAccount = $mailAccountsTable->getDefaultMailAccount();
        }
        return self::$defaultMailAccount;
    }

    private function getMailAccountFromEmail() {
        return $this->formatFromMail($this->getMailAccount()->getAccountEmail(), $this->getMailAccount()->getFromName());
    }
    
    /**
     * @return Gpf_DbEngine_Row_Collection<Gpf_Db_File>
     */
    public function getAttachements() {
    	$select = new Gpf_SqlBuilder_SelectBuilder();
    	$select->select->addAll(Gpf_Db_Table_Files::getInstance(),'f');
        $select->from->add(Gpf_Db_Table_MailAttachments::getName(), 'ma');
        $select->from->addInnerJoin(Gpf_Db_Table_Files::getName(), 'f', 'ma.'.Gpf_Db_Table_MailAttachments::FILE_ID.' = f.'.Gpf_Db_Table_Files::ID);
        $select->where->add("ma.".Gpf_Db_Table_Mails::ID, "=", $this->getId());
        
        $file = new Gpf_Db_File();
        return $file->loadCollectionFromRecordset($select->getAllRows());
    }
    
    public function setReplyTo($replyTo) {
        $this->set(Gpf_Db_Table_Mails::REPLY_TO,$replyTo);
    }
}
?>
