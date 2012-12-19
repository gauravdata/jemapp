<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package ShopMachine
 *   @since Version 1.0.0
 *   $Id: ActionParser.class.php 16620 2008-03-21 09:21:07Z aharsani $
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
class Gpf_Newsletter_GenerateBroadcastMailsTask extends Gpf_Tasks_LongTask {

    public function getName() {
        $broadcast = new Gpf_Db_Broadcast();
        $broadcast->setId($this->getParams());
        $broadcast->load();

        $dbTemplate = new Gpf_Db_MailTemplate();
        $dbTemplate->setId($broadcast->getTemplateId());
        $dbTemplate->load();
        return $this->_('Generate mails with subject: %s', $dbTemplate->getSubject());
    }

    public function setBroadcastParams($broadcastId) {
        $this->setParams($broadcastId);
    }

    protected function execute() {
        $this->processRecipients($this->getParams());
    }
    
    private function interruptIfMemoryFull() {
       if ($this->checkIfMemoryIsFull(memory_get_usage())) {
            Gpf_Log::warning('Be carefull, memory was filled up so im interrupting Gpf_Newsletter_GenerateBroadcastMailsTask task.');
            $this->setDone();
            $this->interrupt();
       }
    }

    private function processRecipients($broadcastId) {
        $recipients = $this->getRecipients($filterId, $customMails, $from);
        if ($recipients->getSize() == 0) return;

        $rowNr = 0;
        foreach ($recipients as $userRecord) {
            $this->interruptIfMemoryFull();
            $this->changeProgress($from + $rowNr, $this->_('Scheduled %s mails', $from + $rowNr));
            $this->checkInterruption();

            $this->sendMail($templateId, $userRecord);
            $this->setDone();

            $rowNr++;
        }
        $this->updateTask();
        //clear variable before recursion
        $recipients = null;
        //recursive call
        $this->processRecipients($filterId, $customMails, $from+$rowNr, $templateId);
    }

    private function sendMail($templateId, Gpf_Data_Record $userRecord) {
        $user = new Pap_Common_User();
        $user->fillFromRecord($userRecord);
        $user->setPassword($userRecord->get('password'));

        $template = new Pap_Mail_MassMailTemplate();
        $template->setTemplateId($templateId);
        $template->setUser($user);
        $template->addRecipient($user->getEmail());
        $template->send();
    }


    /**
     * Get recordset of recipients
     *
     * @param Gpf_Rpc_Form $form
     * @return Gpf_Data_RecordSet
     */
    private function getRecipients($filterId, $customMails, $fromRowNr) {
        $sql = new Gpf_SqlBuilder_SelectBuilder();
//        $sql->from->add(Gpf_Db_Table_UserBroadcasts::getName(), 'ub');
        return $grid->getResult();
    }

    /**
     * (non-PHPdoc)
     * @see include/Gpf/Tasks/Gpf_Tasks_LongTask#canUserDeleteTask()
     */
    public function canUserDeleteTask() {
        return true;
    }

}
