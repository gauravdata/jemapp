<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Template.class.php 32767 2011-05-23 15:20:04Z jsimon $
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
abstract class Gpf_Mail_Template extends Gpf_Object {
    protected $templateVariables = array();
    protected $mailTemplateFile;
    protected $templateName;
    protected $isHtmlMail = true;
    protected $subject;
    protected $fromMail = '';
    protected $fromName = '';
    protected $replyTo = '';
    protected $bccRecipient = null;
    private $recipients = array();

    /**
     * @var Gpf_Db_Mail
     */
    private $mail;


    /**
     * @var Gpf_Db_MailAccount
     */
    protected $mailAccount = null;

    const MAIL_TEMPLATE_DIR = 'mail/';

    /**
     * @var Gpf_Db_MailTemplate
     */
    protected $mailTemplate;
    /**
     * @var Gpf_Templates_Template
     */
    private $subjectTemplate;
    /**
     * @var Gpf_Templates_Template
     */
    private $bodyHtmlTemplate;

    /**
     * @var Gpf_Templates_Template
     */
    private $bodyTextTemplate;

    /**
     * records contain columns id and value
     * @var Gpf_Data_RecordSet
     */
    private $chachedVariables;

    public function __construct() {
        $this->initDefaultTemplateVariables();
        $this->initTemplateVariables();
    }

    public function setFromEmail($email) {
        $this->fromMail = $email;
    }

    public function setFromName($name) {
        $this->fromName = $name;
    }

    public function setup($accountId) {
        $dbTemplate = new Gpf_Db_MailTemplate();
        $dbTemplate->setAccountId($accountId);
        $dbTemplate->setClassName(get_class($this));
        try {
            $dbTemplate->loadFromData(array(Gpf_Db_Table_MailTemplates::ACCOUNT_ID, Gpf_Db_Table_MailTemplates::CLASS_NAME));
        } catch (Gpf_DbEngine_NoRowException $e) {
            $dbTemplate->setTemplateName($this->templateName);
            $dbTemplate->setBodyHtml($this->getTemplateFromFile());
            $dbTemplate->setSubject($this->subject);
            $dbTemplate->insert();
        } catch (Gpf_DbEngine_TooManyRowsException $e) {
        }
        return $dbTemplate->getId();
    }

    protected function getTemplateFromFile() {
        $tmpl = new Gpf_Templates_Template(self::MAIL_TEMPLATE_DIR . $this->mailTemplateFile, $this->getPanelName());
        return $tmpl->getTemplateSource();
    }

    /**
     * @return String
     */
    protected function getPanelName() {
        return '';
    }

    /**
     * Function should call function addVariable for each template variable
     *
     */
    abstract protected function initTemplateVariables();

    private function initDefaultTemplateVariables() {
        $this->addVariable('date', $this->_('Server Date'));
        $this->addVariable('time', $this->_('Server Time'));
    }

    /**
     * Set template variable values
     *
     */
    abstract protected function setVariableValues();

    /**
     * records contain columns id and value
     *
     * @param Gpf_Data_RecordSet $chachedVariables
     */
    public function setCachedVariableValues(Gpf_Data_RecordSet $chachedVariables) {
        $this->chachedVariables = $chachedVariables;
    }

    /**
     * In case there are cached variable values, use them, otherwise call setVariableValues
     */
    private function loadVariableValues($timeOffset) {
        if (isset($this->chachedVariables)) {
            foreach ($this->chachedVariables as $record) {
                $this->setVariable($record->get('id'), $record->get('value'));
            }
        } else {
            $this->setTimeVariableValues($timeOffset);
            $this->setVariableValues();
        }
    }

    protected function setTimeVariableValues($timeOffset = 0) {
        //TODO: Use date and time format as defined by customer in settings '
        $time = time() + $timeOffset;
        $this->setVariable('date', Gpf_Common_DateUtils::getDateInLocaleFormat($time));
        $this->setVariable('time', Gpf_Common_DateUtils::getTimeInLocaleFormat($time));
    }

    /**
     * Add variable to template
     *
     * @param string $id Variable Id used in template
     * @param string $title Name of variable
     * @param unknown_type $value Value of variable
     */
    public function addVariable($id, $title) {
        $this->templateVariables[$id] = $title;
    }

    public function variableExist($name) {
        return array_key_exists($name, $this->templateVariables);
    }

    /**
     * Set template variable value
     *
     * @param $id string Variable id
     * @param $value string Variable value
     */
    public function setVariable($id, $value) {
        if (!array_key_exists($id, $this->templateVariables)) {
            throw new Gpf_Exception('Setting value of undefined template variable (' . $id . ')');
        }
        $this->setVariableRaw($id, $value);
    }

    protected function setVariableRaw($id, $value) {
        $this->subjectTemplate->assign($id, $value);
        $this->bodyHtmlTemplate->assign($id, $value);
        $this->bodyTextTemplate->assign($id, $value);
    }

    /**
     *
     * @return array
     */
    public function getTemplateVariables() {
        return $this->templateVariables;
    }

    /**
     * Return name of template class
     *
     * @return string
     */
    protected function getClassName() {
        return get_class($this);
    }

    protected function loadTemplate() {
        $this->mailTemplate = new Gpf_Db_MailTemplate();
        $this->mailTemplate->setClassName($this->getClassName());
        $this->mailTemplate->setAccountId(Gpf_Application::getInstance()->getAccountId());
        try {
            $this->mailTemplate->loadFromData(array(Gpf_Db_Table_MailTemplates::CLASS_NAME,
            Gpf_Db_Table_Accounts::ID));
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception($this->_('Mail template not defined in database for class %s', $this->getClassName()));
        }
    }
    
    private function getBccRecipients($bccRecipients) {
        if (is_array($bccRecipients)) {
            return implode(',', $bccRecipients);
        }
        return $bccRecipients;
    }

    /**
     *
     * @return Gpf_Db_Mail
     */
    protected function createMail($toRecipients, $bccRecipients = null) {
        //save mail
        $mail = new Gpf_Db_Mail();
        $mail->setSubject($this->subjectTemplate->getHTML());
        $mail->setHtmlBody($this->bodyHtmlTemplate->getHTML());
        $mail->setTextBody($this->bodyTextTemplate->getHTML());
        $mail->setRecipients($toRecipients);
        if (!is_null($bccRecipients) || $bccRecipients != '') {
            $mail->setBccRecipients($bccRecipients);
        }
        if ($this->mailAccount !== null) {
            $mail->setMailAccount($this->mailAccount);
        }
        $mail->setFromMail($this->fromMail);
        $mail->setReplyTo($this->replyTo);
        $mail->insert();
        $this->createMailAttachments($this->mailTemplate, $mail);
        return $mail;
    }


    protected function createMailAttachments(Gpf_Db_MailTemplate $template, Gpf_Db_Mail $mail) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('fileid', 'fileid');
        $select->select->addConstant($mail->get('mailid'), 'mailid');
        $select->select->add('is_included_image', 'is_included_image');
        $select->from->add(Gpf_Db_Table_MailTemplateAttachments::getName());
        $select->where->add('templateid', '=', $template->get('templateid'));

        $insert = new Gpf_SqlBuilder_InsertBuilder();
        $insert->setTable(Gpf_Db_Table_MailAttachments::getInstance());
        $insert->fromSelect($select);
        $insert->execute();
    }


    /**
     * Set custom mail account used during sending of mail.
     * If not set, default mail account will be used
     *
     * @param Gpf_Db_MailAccount $accont
     */
    public function setMailAccount(Gpf_Db_MailAccount $accont) {
        $this->mailAccount = $accont;
    }

    public function sendNow() {
        $this->send(true);
    }

    /**
     * Send mail (schedule to outbox)
     * @throws Gpf_Exception
     * @param $now boolean if true, mail is sent immediately
     */
    public function send($now = false, $delayMinutes = 0) {
        if (count($this->recipients) == 0) {
            throw new Gpf_Exception('Failed to send mail, recipients empty.');
        }

        $recipients = $this->getRecipients();
        if (count($recipients) == 0) {
            Gpf_Log::error('Email will not be sent, recipients empty.');
            return;
        }

        $this->loadTemplate();
        foreach ($recipients as $language => $timeOffsets) {
            foreach ($timeOffsets as $timeOffset => $emails) {
                $this->subjectTemplate = new Gpf_Templates_Template(
                Gpf_Lang::_localizeRuntime($this->getSubject(), $language), '', Gpf_Templates_Template::FETCH_TEXT);
                $this->bodyHtmlTemplate = new Gpf_Templates_Template(
                Gpf_Lang::_localizeRuntime($this->getBodyHtml(), $language), '', Gpf_Templates_Template::FETCH_TEXT);
                $this->bodyTextTemplate = new Gpf_Templates_Template(
                Gpf_Lang::_localizeRuntime($this->getBodyText(), $language), '', Gpf_Templates_Template::FETCH_TEXT);
                $this->loadVariableValues($timeOffset);
                $mail = $this->createMail(implode(',', $emails), $this->getBccRecipients($this->bccRecipient));
                $mail->scheduleNow($now, $delayMinutes);
            }
        }
    }

    /**
     * Add bcc recipient of mail
     *
     * @param string $email Email address in form account@domain.com
     */
    public function addBccRecipient($email, $name = null) {
        $recipient = $email;
        if($name !== null) {
            $recipient = $name . ' <' . $email . '>';
        }

        $this->bccRecipient[$email] = $recipient;
    }
    
    /**
     * @return array {key == lang code, value == array {key == timeOffset, value == array of recipients}}
     */
    private function getRecipients() {
        $recipients = array();
        $emailValidator = new Gpf_Rpc_Form_Validator_EmailValidator();
        foreach ($this->recipients as $email => $recipient) {
            if (!$emailValidator->validate($email)) {
                Gpf_Log::warning('Email will not be sent to the address "' . $email . '". Address is not valid.');
                continue;
            }
            try {
                $authuser = new Gpf_Db_AuthUser();
                $authuser->setNotificationEmail($email);
                $authuser->loadFromData(array(Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL));
                $recipients = $this->insertRecipient($recipients, $recipient, $this->getAccountUser($authuser->getId()));
            } catch (Gpf_Exception $e) {
                try {
                    $authuser->setUsername($email);
                    $authuser->loadFromUsername();
                    $recipients = $this->insertRecipient($recipients, $recipient, $this->getAccountUser($authuser->getId()));
                } catch (Gpf_DbEngine_NoRowException $e) {
                    $recipients = $this->insertRecipient($recipients, $recipient);
                }
            }
        }
        return $recipients;
    }

    private function insertRecipient(array $recipients, $recipient, Gpf_Db_User $user = null) {
        if ($user != null) {
            $recipients = $this->insertRecipientWithLanguage($recipients, $recipient,
            $this->getUserAttribute(Gpf_Auth_Service::LANGUAGE, $user),
            $this->getUserAttribute(Gpf_Auth_Service::TIME_OFFSET, $user));
            return $recipients;
        }
        $recipients = $this->insertRecipientWithLanguage($recipients, $recipient);
        return $recipients;
    }

    private function getUserAttribute($attribute, Gpf_Db_User $user) {
        $setting = new Gpf_Db_UserAttribute();
        try {
            return $setting->getSetting($attribute, $user->getId());
        } catch (Gpf_Exception $e) {
        }
        return $attribute == Gpf_Auth_Service::TIME_OFFSET ? 0 : '';
    }

    private function insertRecipientWithLanguage(array $recipients, $recipient, $language = '', $timeOffset = 0) {
        if (!isset($recipients[$language])) {
            $recipients[$language] = array();
        }
        if (!isset($recipients[$language][$timeOffset])) {
            $recipients[$language][$timeOffset] = array();
        }
        $recipients[$language][$timeOffset][] = $recipient;
        return $recipients;
    }

    private function getAccountUser($authUserId) {
        $accountUser = new Gpf_Db_User();
        $accountUser->setAuthId($authUserId);
        try {
            $accountUser->loadFromData(array(Gpf_Db_Table_Users::AUTHID));
        } catch (Exception $e) {
            return null;
        }
        return $accountUser;
    }

    protected function getSubject() {
        return $this->mailTemplate->getSubject();
    }

    protected function getBodyHtml() {
        return $this->mailTemplate->getBodyHtml();
    }

    protected function getBodyText() {
        return $this->mailTemplate->getBodyText();
    }

    /**
     * Add recipient of mail
     *
     * @param string $email Email address in form account@domain.com
     */
    public function addRecipient($email, $name = null) {
        $recipient = $email;
        if($name !== null) {
            $recipient = $name . ' <' . $email . '>';
        }

        $this->recipients[$email] = $recipient;
    }

    /**
     * Clear list of email recipients
     */
    public function clearRecipients() {
        $this->recipients = array();
    }

    /**
     * Set custom subject of mail
     *
     * @param string $subject
     */
    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function setReplyTo($replyTo) {
        $this->replyTo = $replyTo;
    }
}
