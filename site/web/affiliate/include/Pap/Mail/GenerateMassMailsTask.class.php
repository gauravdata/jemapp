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
class Pap_Mail_GenerateMassMailsTask extends Gpf_Tasks_LongTask {

    const TEMPLATE_ID = 'tid';
    const FILTER_ID = 'fid';
    const CUSTOM_MAILS = 'cm';
    const AFFILITE_ID = 'affid';
    const PURPOSE = 'purpose';
    const SENDER = 'sender';
    private $childAffiliates = array();

    public function getName() {
        $params = unserialize($this->getParams());
        $dbTemplate = new Gpf_Db_MailTemplate();
        $dbTemplate->setId($params[self::TEMPLATE_ID]);
        $dbTemplate->load();
        return $this->_('Generate mails with subject: %s', $dbTemplate->getSubject());
    }

    public function setMassMailParams($templateId, $filterId, $customMails, $affiliateId = null, $purpose = '', $sender = '') {
        $params = array();
        $params[self::TEMPLATE_ID] = $templateId;
        $params[self::FILTER_ID] = $filterId;
        $params[self::CUSTOM_MAILS] = $customMails;
        $params[self::AFFILITE_ID] = $affiliateId;
        $params[self::PURPOSE] = $purpose;
        $params[self::SENDER] = $sender;
        $this->setParams(serialize($params));
    }

    protected function execute() {
        $params = unserialize($this->getParams());
        $from = $this->getProgress();
        
        if($this->isAffiliateMailToFriend($params)) {
            $this->sendCustomEmails($params);
            return;
        }
        if ($this->isAffiliateBroadcastEmail($params)) {
            if ($this->isPending('getAffChildsEmails', $this->_("Get Affiliate's childs e-mails"))) {
                $params = $this->initChildEmails($params);
                $from = '';
                $this->setDone();
            }
        }

        if (!is_numeric($from)) {
            $from = 0;
        } else {
            $from ++;
        }
        
        if (array_key_exists(self::AFFILITE_ID, $params)) {
            $affiliateid = $params[self::AFFILITE_ID];
        } else {
            $affiliateid = '';
        }

        $this->processRecipients($params[self::FILTER_ID], $params[self::CUSTOM_MAILS], $from, $params[self::TEMPLATE_ID], $affiliateid);
    }
    
    private function isAffiliateMailToFriend($params){
        return isset($params[self::PURPOSE]) && $params[self::PURPOSE] == Pap_Affiliates_Profile_SendToFriendForm::PURPOSE_SEND_TO_FRIEND;
    }
    
    private function isAffiliateBroadcastEmail($params){        
        return (array_key_exists(self::AFFILITE_ID, $params) && $params[self::AFFILITE_ID] !== null && $params[self::AFFILITE_ID] != '');
    }
    
    private function sendCustomEmails($params){
        
        $delaySetting = Gpf_Settings::get(Pap_Settings::AFF_SEND_EMAILS_PER_MINUTE_SETTING_NAME);
        $i=0;
        
        $recipients = explode(",",$params[self::CUSTOM_MAILS]);
        $template = new Pap_Mail_MassMailTemplate();
        $template->setTemplateId($params[self::TEMPLATE_ID]);
        if($params[self::SENDER] != "") {
            $template->setFromEmail($params[self::SENDER]);
        }
        
        foreach($recipients as $emailAddress){
            $this->interruptIfMemoryFull();
            if ($this->isPending($i, $this->_("Scheduled $emailAddress mail"))) {
            
                $this->changeProgress($i, $this->_("Scheduled $emailAddress mail"));
                $this->checkInterruption();
                
                $template->clearRecipients();
                $template->addRecipient($emailAddress);
                
                $template->send(false, (int)($i/$delaySetting));
                $this->setDone();
            }
            $i++;
        }
    }

    private function initChildEmails($params){
        if ($params[self::CUSTOM_MAILS] == '') {

            $parent = new Pap_Common_User();
            $parent->setAccountUserId($params[self::AFFILITE_ID]);
            $parent->loadFromData(array(Pap_Db_Table_Users::ACCOUNTUSERID));

            $this->getChildAffilitesMails($parent);
            $params[self::CUSTOM_MAILS] = implode(",",$this->childAffiliates);
            $this->setParams(serialize($params));
        }
        return $params;
    }

    private function interruptIfMemoryFull() {
        if ($this->checkIfMemoryIsFull(memory_get_usage())) {
            Gpf_Log::warning('Be carefull, memory was filled up so im interrupting Pap_Mail_GenerateMassMailsTask task.');
            $this->setDone();
            $this->interrupt();
        }
    }

    private function processRecipients($filterId, $customMails, $from, $templateId, $affiliateId) {
        $recipients = $this->getRecipients($filterId, $customMails, $from);    
        if (is_null($recipients) || $recipients->getSize() == 0) {
            return;
        }
        $rowNr = 0;
        foreach ($recipients as $userRecord) {
            $this->interruptIfMemoryFull();
            $this->changeProgress($from + $rowNr, $this->_('Scheduled %s mails', $from + $rowNr));
            $this->checkInterruption();

            $this->sendMail($templateId, $userRecord, $filterId, $affiliateId);
            $this->setDone();
            
            $rowNr++;
        }
        $this->updateTask(30);
        //clear variable before recursion
        $recipients = null;
        //recursive call
        $this->processRecipients($filterId, $customMails, $from+$rowNr, $templateId, $affiliateId);
    }

    private function getChildAffilitesMails($parent){
        $tree = new Pap_Common_UserTree();
        $children = $tree->getChildren($parent);
        foreach($children as $child){
            $this->childAffiliates[] = $child->getEmail();
            $this->getChildAffilitesMails($child);
        }
    }
    
    private function setFromEmailAndName(Pap_Mail_MassMailTemplate $template, $affiliateId) {
        $user = new Pap_Common_User();
        $user->setAccountUserId($affiliateId);
        try {
            $user->loadFromData(array(Pap_Db_Table_Users::ACCOUNTUSERID));
        } catch (Gpf_Exception $e) {
            Gpf_Log::debug('Unable to load sender information when sending broadcast mail from affiliateid: ' . $affiliateId . ', error: '. $e->getMessage());
            return;
        }      
        $email = $user->getUserName();
        $name = $user->getFirstName() . ' ' . $user->getLastName(); 
        $emailValidator = new Gpf_Rpc_Form_Validator_EmailValidator();
        if ($emailValidator->validate($email)) {            
            $template->setFromEmail($email);
            $template->setFromName($name);
        }
    }

    private function sendMail($templateId, Gpf_Data_Record $userRecord, $filterId, $affiliateId) {
        $user = new Pap_Common_User();
        $user->fillFromRecord($userRecord);
        $user->setPassword($userRecord->get('password'));

        $template = new Pap_Mail_MassMailTemplate();        

        if ($affiliateId != null && $affiliateId != '') {
            $this->setFromEmailAndName($template, $affiliateId);        
        }
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
        $grid = new Pap_Mail_MassMailAffiliatesGrid();
        $grid->setFromRowNr($fromRowNr);

        switch ($filterId) {
            case '':    //select all affiliates
                break;
            case 'custom':  //load users with specified emails
                if($customMails == '') {
                    return;
                }
                $recipients = str_replace(array(';', ' ', "\n"), ',', trim($customMails));
                $recipients = explode(',', $recipients);
                $emailValidator = new Gpf_Rpc_Form_Validator_EmailValidator();
                $recipients=array_filter($recipients, array($emailValidator, 'validate'));
                $grid->setRecipients($recipients);
            default:    //load filter conditions
                $grid->setFilterId($filterId);
        }
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
