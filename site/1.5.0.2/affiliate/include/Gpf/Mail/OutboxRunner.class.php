<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: OutboxRunner.class.php 33513 2011-06-28 13:43:07Z mkendera $
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
class Gpf_Mail_OutboxRunner extends Gpf_Tasks_LongTask {
    const BENCHMARK_OUTBOX = 'outbox';
    const MAX_MAIL_WORKERS_COUNT = 512;

    /**
     * Retry to send mail after defined time in seconds
     */
    const RETRY_TIME = 900;

    public function getName() {
        return $this->_('Send mails from outbox');
    }

    public function initParams() {
        $this->setWorkingArea(1, $this->getMaxWorkersCount());
        $this->setParams(1);
    }

    public function createWorker($workingRangeFrom, $workingRangeTo) {
        $task = new Gpf_Mail_OutboxRunner();
        $this->debug('Creating new worker Gpf_Mail_OutboxRunner for range:' . $workingRangeFrom . '-' . $workingRangeTo);
        $task->setWorkingArea($workingRangeFrom, $workingRangeTo);
        $task->setParams($this->task->getParams());
        $task->insertTask();
    }

    protected function getClassName() {
        return get_class();
    }

    protected function getMaxWorkersCount() {
        return self::MAX_MAIL_WORKERS_COUNT;
    }

    private function getFirstUnsentMailId($fromId = null) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('o.' . Gpf_Db_Table_MailOutbox::ID, Gpf_Db_Table_MailOutbox::ID);
        $select->from->add(Gpf_Db_Table_MailOutbox::getName(), 'o');
        $select->from->addInnerJoin(Gpf_Db_Table_MailAccounts::getName(), 'ma', 'o.' . Gpf_Db_Table_MailOutbox::MAILACCOUNTID . ' = ma.' . Gpf_Db_Table_MailAccounts::ID);
        $select->from->addInnerJoin(Gpf_Db_Table_Mails::getName(), 'm', 'o.' . Gpf_Db_Table_MailOutbox::MAIL_ID . ' = m.' . Gpf_Db_Table_Mails::ID);
        $select->where->add('o.' . Gpf_Db_Table_MailOutbox::STATUS, '=', Gpf_Db_Table_MailOutbox::STATUS_PENDING);
        $select->where->add('o.' . Gpf_Db_Table_MailOutbox::RETRY_NR, '<', Gpf_Db_Table_MailOutbox::MAX_RETRY_NR);
        $select->where->add('o.' . Gpf_Db_Table_MailOutbox::SCHNEDULET_AT, '<=', $this->createDatabase()->getDateString());
        if ($fromId != null) {
            $select->where->add('o.' . Gpf_Db_Table_MailOutbox::ID, '>=', $fromId);
        }
        $select->orderBy->add('o.' . Gpf_Db_Table_MailOutbox::ID);
        $select->limit->set(0,1);

        $this->debug('First unsent mail: ' . $select->toString());   
      
        $row = $select->getOneRow();
        return $row->get(Gpf_Db_Table_MailOutbox::ID);
    }

    protected function doMasterWorkWhenSyncPointReached() {
        $this->interrupt(30);
    }

    private function shiftWorkingArea($begining) {
        $this->task->setWorkingAreaFrom($begining);
        $this->task->setWorkingAreaTo($begining + $this->getMaxWorkersCount());
        $this->task->setParams($begining);
        $this->debug('Shifting my area to: ' . $this->task->getWorkingAreaFrom() . "-" . $this->task->getWorkingAreaTo());
        $this->task->update(array(Gpf_Db_Table_Tasks::WORKING_AREA_FROM, Gpf_Db_Table_Tasks::WORKING_AREA_TO, Gpf_Db_Table_Tasks::PARAMS));
    }

    protected function resetMyWorkingArea() {
        try {
            $unsentMailId = $this->getFirstUnsentMailId();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $this->interrupt(30);
        }
        $this->shiftWorkingArea($unsentMailId);
    }

    protected function imMasterWorker() {
        if ($this->task->getWorkingAreaFrom() == $this->task->getParams()) {
            return true;
        }
        return false;
    }

    protected function syncPointReached() {
        $lastEmailId = $this->task->getParams() + $this->getMaxWorkersCount() - 2;
         
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('count(*)', 'cnt');
        $select->from->add(Gpf_Db_Table_MailOutbox::getName(), 'o');
        $select->from->addInnerJoin(Gpf_Db_Table_MailAccounts::getName(), 'ma', 'o.' . Gpf_Db_Table_MailOutbox::MAILACCOUNTID . ' = ma.' . Gpf_Db_Table_MailAccounts::ID);
        $select->from->addInnerJoin(Gpf_Db_Table_Mails::getName(), 'm', 'o.' . Gpf_Db_Table_MailOutbox::MAIL_ID . ' = m.' . Gpf_Db_Table_Mails::ID);
        $select->where->add('o.' . Gpf_Db_Table_MailOutbox::STATUS, '=', Gpf_Db_Table_MailOutbox::STATUS_PENDING);
        $select->where->add('o.' . Gpf_Db_Table_MailOutbox::RETRY_NR, '<', Gpf_Db_Table_MailOutbox::MAX_RETRY_NR);
        $select->where->add('o.' . Gpf_Db_Table_MailOutbox::ID, 'BETWEEN', $this->task->getParams() . ' AND ' . $lastEmailId, 'AND', false);
        $select->where->add('o.' . Gpf_Db_Table_MailOutbox::SCHNEDULET_AT, '<=', $this->createDatabase()->getDateString());

        $this->debug('SYNC: ' . $select->toString());
        $record = $select->getOneRow();

        if ($record->get('cnt') > 0) {
            $this->debug('Sync point !not reached');
            return false;
        }
        $this->debug('Sync point reached');
        return true;
    }

    protected function doSlaveWorkAfterExecute() {
        $this->debug('Worker finished his work...');
        $this->setDone();
    }

    protected function doMasterWorkAfterExecute() {
        $this->debug('interrupting...');
        $this->interrupt(30);
    }

    protected function doSlaveWorkWhenSyncPointReached() {
        $this->setDone();
        $this->forceFinishTask();
        $this->interrupt();
    }

    /**
     * Send all scheduled mails from outbox
     */
    public function execute($outboxids = false) {
        if (Gpf_Application::isDemo()) {
            $this->interrupt(30);
        }

        do {
            $mails = $this->getPendingEmail($outboxids);
            if ($mails->getSize() > 0) {
                foreach ($mails as $mail) {
                    $this->debug('got some emails ('.$mails->getSize().')... processing');
                    $this->sendScheduledMail($mail);
                }
            } else {
                $this->debug('No mails to send by me... ');
                break;
            }
            $this->checkInterruption();
        } while ($outboxids == false);
    }

    /**
     *
     * @return Gpf_Data_RecordSet
     */
    private function getPendingEmail($outboxids) {
        $this->debug('Getting pending email...');
        $select = new Gpf_SqlBuilder_SelectBuilder();

        $select->select->add('o.' . Gpf_Db_Table_MailOutbox::ID, Gpf_Db_Table_MailOutbox::ID);
        $select->select->add('o.' . Gpf_Db_Table_MailOutbox::RETRY_NR, Gpf_Db_Table_MailOutbox::RETRY_NR);

        $select->select->add('m.*');
        $select->select->add('ma.*');

        $select->from->add(Gpf_Db_Table_MailOutbox::getName(), 'o');
        $select->from->addInnerJoin(Gpf_Db_Table_MailAccounts::getName(), 'ma', 'o.' . Gpf_Db_Table_MailOutbox::MAILACCOUNTID . ' = ma.' . Gpf_Db_Table_MailAccounts::ID);
        $select->from->addInnerJoin(Gpf_Db_Table_Mails::getName(), 'm', 'o.' . Gpf_Db_Table_MailOutbox::MAIL_ID . ' = m.' . Gpf_Db_Table_Mails::ID);

        if (!empty($outboxids)) {
            $select->where->add('o.' . Gpf_Db_Table_MailOutbox::ID, 'IN', $outboxids);
        } else {
            $select->where->add('o.' . Gpf_Db_Table_MailOutbox::ID, 'BETWEEN', $this->task->getWorkingAreaFrom() . ' AND ' . $this->task->getWorkingAreaTo(), 'AND', false);
        }

        //load just mails with status pending
        $select->where->add('o.' . Gpf_Db_Table_MailOutbox::STATUS, '=', Gpf_Db_Table_MailOutbox::STATUS_PENDING);

        // load just mails, which are already scheduled
        $select->where->add('o.' . Gpf_Db_Table_MailOutbox::SCHNEDULET_AT, '<=', $this->createDatabase()->getDateString());

        //if retry number is too high, don't repeat sending
        $select->where->add('o.' . Gpf_Db_Table_MailOutbox::RETRY_NR, '<', Gpf_Db_Table_MailOutbox::MAX_RETRY_NR);

        $this->debug('email select: ' . $select->toString());
        $select->limit->set(0,1);
        return $select->getAllRows();
    }

    protected function updateTask($sleepSeconds = 0) {
        if ($this->task != null) {
            $this->task->setProgress('pending');
            $this->task->setSleepTime($sleepSeconds);
            $this->task->setProgressMessage($this->_('Pending %s mails', $this->getStatusCount(Gpf_Db_Table_MailOutbox::STATUS_PENDING)));
            $this->task->updateTask();
        }
    }

    private function getStatusCount($status) {
        $sql = new Gpf_SqlBuilder_SelectBuilder();
        $sql->select->add('count(*)', 'cnt');
        $sql->from->add(Gpf_Db_Table_MailOutbox::getName());
        $sql->where->add(Gpf_Db_Table_MailOutbox::STATUS, '=', $status);
        $record = $sql->getOneRow();
        return $record->get('cnt');
    }

    /**
     * Send scheduled mail from outbox
     *
     * @param $mail
     */
    private function sendScheduledMail(Gpf_Data_Record $mail) {
        try {
            $this->startSending($mail);
            $this->sendMail($mail);
            $this->finishSending($mail);
        } catch (Gpf_Exception $e) {
            $this->failedSending($mail, $e->getMessage());
        }
    }

    /**
     * Try to send Outbox entry
     *
     * @param Gpf_Data_Record $mail
     */
    private function sendMail(Gpf_Data_Record $outbox) {
        //build mail
        $mail = new Gpf_Mail();

        //set transfer method (smtp or mail)
        if ($outbox->get('use_smtp') == Gpf::YES) {

            if ($outbox->get('smtp_ssl') == Gpf::YES) {
                $this->checkSSL();
            }

            $mail->setTransferMethod('smtp');
            $mail->setTransferParams(array(
            'host' => $outbox->get('smtp_server'),
            'port' => $outbox->get('smtp_port'),
            'auth' => ($outbox->get('smtp_auth') == Gpf::YES ? (strlen($outbox->get('smtp_auth_method')) ? $outbox->get('smtp_auth_method') : true) : false),
            'username' => $outbox->get('smtp_username'),
            'password' => $outbox->get('smtp_password'),

            //TODO the best will be to use any user customizable define for this value
            'localhost' => 'localhost',
            'timeout' => 30,
            'verp' => false,
            'debug' => false,
            'persist' => false
            ));
        } else {
            $mail->setTransferMethod('mail');
            $mail->setTransferParams(null);
        }

        $mail->setRecipients($outbox->get('to_recipients'));
        $mail->setCcRecipients($outbox->get('cc_recipients'));
        $mail->setBccRecipients($outbox->get('bcc_recipients'));
        $mail->setFullFromAddress($outbox->get('from_mail'));
        $mail->setHtmlBody($outbox->get('body_html'));
        $mail->setTxtBody($outbox->get('body_text'));
        if($outbox->get('reply_to') != '') {
            $mail->setFrom('',$outbox->get('from_mail'));
            $mail->setReplyTo($outbox->get('reply_to'));
        }
        else {
            $mail->setReplyTo($outbox->get('from_mail'));
        }
        $mail->setSubject($outbox->get('subject'));
        $mail->setUserAgent('Quality Unit Mail Services');

        //add attachments and inner images
        $attachments = Gpf_Db_Table_MailAttachments::getMailAttachments($outbox->get('mailid'));
        foreach ($attachments as $attachment) {
            if ($attachment->get('is_included_image') == Gpf::YES) {
                $mail->addImage($attachment->get('filename'), Gpf_Db_Table_FileContents::getFileContent($attachment->get('fileid')), $attachment->get('filetype'));
            } else {
                $mail->addAttachment($attachment->get('filename'), $attachment->get('filetype'), Gpf_Db_Table_FileContents::getFileContent($attachment->get('fileid')));
            }
        }

        $mail->send();
        return true;
    }

    /**
     * Mark outbox entry with status Sending
     *
     * @param Gpf_Data_Record $mail
     */
    private function startSending(Gpf_Data_Record $mail) {
        //mark outbox entry with status sending
        $outbox = new Gpf_Db_MailOutbox();
        $outbox->set('outboxid', $mail->get('outboxid'));
        $outbox->set('status', Gpf_Db_Table_MailOutbox::STATUS_SENDING);
        $outbox->set('last_retry', $this->createDatabase()->getDateString());
        $outbox->set('retry_nr', $mail->get('retry_nr') + 1);
        $outbox->update(array('status', 'last_retry', 'retry_nr'));
    }

    /**
     * Mark outbox entry as ready
     *
     * @param Gpf_Data_Record $mail
     */
    private function finishSending(Gpf_Data_Record $mail) {
        $outbox = new Gpf_Db_MailOutbox();
        $outbox->set('outboxid', $mail->get('outboxid'));
        $outbox->set('status', Gpf_Db_Table_MailOutbox::STATUS_READY);
        $outbox->update(array('status'));

        $mailObj = new Gpf_Db_Mail();
        $mailObj->set('mailid', $mail->get('mailid'));
        $mailObj->set('delivered', $this->createDatabase()->getDateString());
        $mailObj->update(array('delivered'));
    }

    /**
     * Mark outbox entry as pending + store error message why it failed
     *
     * @param Gpf_Data_Record $mail
     * @param string $error
     */
    private function failedSending(Gpf_Data_Record $mail, $error) {
        //mark outbox entry with status pending
        $outbox = new Gpf_Db_MailOutbox();
        $outbox->set('outboxid', $mail->get('outboxid'));
        $outbox->set('status', Gpf_Db_Table_MailOutbox::STATUS_PENDING);
        $outbox->set('error_msg', $error);
        $outbox->set('scheduled_at', $this->createDatabase()->getDateString(time() + self::RETRY_TIME));
        $outbox->update(array('status', 'error_msg', 'scheduled_at'));
    }

    private function checkSSL() {
        $version=explode(".",Gpf_Php::isFunctionEnabled("phpversion") ? phpversion() : "3.0.7");
        $php_version=intval($version[0])*1000000+intval($version[1])*1000+intval($version[2]);
        if($php_version<4003000) {
            throw new Gpf_Exception("To establishing SSL connections requires at least PHP version 4.3.0");
        }
        if(!Gpf_Php::isFunctionEnabled("extension_loaded") || !extension_loaded("openssl")) {
            throw new Gpf_Exception("Establishing SSL/TLS connections requires the OpenSSL extension enabled");
        }
    }
}
