<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: LoggingForm.class.php 18882 2008-06-27 12:15:52Z mfric $
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
class Pap_Affiliates_Profile_SendToFriendForm extends Pap_Mail_SendMassMail {
    const PURPOSE_SEND_TO_FRIEND = 'AffiliateSendMailToFriend';
    /**
     * @service affiliate_email_notification read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $this->attribute = Gpf_Db_Table_UserAttributes::getInstance();
        $this->attribute->loadAttributes(Gpf_Session::getAuthUser()->getAccountUserId());

        $data = new Gpf_Db_UserAttribute();
        $data->setAccountUserId(Gpf_Session::getAuthUser()->getUserId());
        $data->setName('RecipientsList');
        try {
            $data->loadFromData(array(Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID,Gpf_Db_Table_UserAttributes::NAME));
            $form->setField("recipients",$data->getValue());

        } catch(Gpf_DbEngine_NoRowException $e) {
            $form->setField("recipients","");
        }
        $form->setField('affEmail',Gpf_Session::getAuthUser()->getUsername());

       	return $form;
    }

    /**
     * @service affiliate_email_notification write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $bannerFactory = new Pap_Common_Banner_Factory();
        $banner = $bannerFactory->getBanner($form->getFieldValue('bannerId'));

        $bodyHtml = $banner->getPreview($this->getUser());

        if($form->existsField('personalMessage')){
            $bodyHtml = $this->replacePersonalMessage($form,$bodyHtml);
        }

        $form->setField('body_html',$bodyHtml);

        $recipientsList = $this->makeRecepientsList($form);

        if(count($recipientsList) == 0) {
            $form->setErrorMessage($this->_("Please add recepients."));
            return $form;
        }

        $this->saveRecipientsList($form->getFieldValue('recipients'));
        $task = $this->createLongTask($form, $this->createMassMailTemplate($form,'Pap_Mail_AffiliateMailTemplate'), $recipientsList);
        $task->insertTask();

        $form->setInfoMessage($this->_("Thank you, your mail will be delivered within next few minutes."));
        return $form;
    }

    /**
     * Create long task for generating of mails
     * @param $form
     * @param $dbTemplate
     * @return Pap_Mail_GenerateMassMailsTask
     */
    protected function createLongTask(Gpf_Rpc_Form $form, Gpf_Db_MailTemplate $dbTemplate, $recipientsList) {
        $sender = '';
        if($form->getFieldValue('from') == "affEmail") {
            $sender = Gpf_Session::getAuthUser()->getUsername();
        }

        $task = new Pap_Mail_GenerateMassMailsTask();
        $task->setMassMailParams($dbTemplate->getId(), 'custom', implode(",",$recipientsList),gpf_session::getAuthUser()->getUserId(), self::PURPOSE_SEND_TO_FRIEND, $sender);
        return $task;
    }

    private function getUser() {
        $user = new Pap_Affiliates_User();
        $user->setId(Gpf_Session::getAuthUser()->getPapUserId());
        $user->load();

        return $user;
    }

    private function saveRecipientsList($recipients){
        $data = new Gpf_Db_UserAttribute();
        $data->setAccountUserId(Gpf_Session::getAuthUser()->getUserId());
        $data->setName('RecipientsList');

        try {
            $data->loadFromData(array(Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID,Gpf_Db_Table_UserAttributes::NAME));
            $data->setValue($recipients);
            $data->save();

        } catch(Gpf_DbEngine_NoRowException $e) {
            $data->setValue($recipients);
            $data->insert();
        }
    }

    private function replacePersonalMessage($form,$bodyHtml){
        $personalMessage = str_replace("\n","<br>",htmlspecialchars($form->getFieldValue('personalMessage')));
        return str_replace('{$personal_message}',$personalMessage,$bodyHtml);
    }

    private function makeRecepientsList(Gpf_Rpc_Form $form) {
        $recipients = explode(",",str_replace(array("\n",";"," "),",",$form->getFieldValue('recipients')));
        $recipientsList = array();
        foreach($recipients as $emailAdress){
            if(preg_match("/.*@.*\..*/",$emailAdress)){
                $recipientsList[] = $emailAdress;
            }
        }
        return $recipientsList;
    }
}

?>
