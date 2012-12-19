<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
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
class Pap_Mail_SendMassMail extends Gpf_Object {

    /**
     * Load list of template variables for custom template
     *
     * @service mail_template read
     * @param Gpf_Rpc_Params $params
     */
    public function getTemplateVariables(Gpf_Rpc_Params $params) {
        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->setHeader(array('code', 'name'));

        $objTemplate = new Pap_Mail_UserMail();

        foreach ($objTemplate->getTemplateVariables() as $code => $name) {
            $recordSet->add(array($code, $name));
        }

        return $recordSet;
    }

    /**
     * @service mass_email write
     */
    public function add(Gpf_Rpc_Params $params) {
        return $this->save($params);
    }

    /**
     * @service mass_email write
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $task = $this->createLongTask($form, $this->createMassMailTemplate($form));
        $task->insertTask();

        $form->setInfoMessage($this->_('Mails will be created and delivered in background process.'));
        return $form;
    }

    /**
     * Create Mail template for this mass mail
     *
     * @param $form
     * @return Gpf_Db_MailTemplate
     */
    protected function createMassMailTemplate(Gpf_Rpc_Form $form, $className = 'Pap_Mail_MassMailTemplate') {
        $dbTemplate = new Gpf_Db_MailTemplate();
        $dbTemplate->setIsCustom(true);
        $dbTemplate->setSubject($form->getFieldValue('subject'));
        $dbTemplate->setBodyHtml($form->getFieldValue('body_html'));
        if($form->existsField('body_text')){
            $dbTemplate->setBodyText($form->getFieldValue('body_text'));
        }
        $dbTemplate->setClassName($className);
        $dbTemplate->setTemplateName($dbTemplate->getSubject());
        $dbTemplate->setUserId(Gpf_Session::getAuthUser()->getUserId());
        $dbTemplate->insert();
        $this->addAttachements($dbTemplate, $form);
        return $dbTemplate;
    }

    /**
     * Add attachments to mail template
     *
     * @param $dbTemplate
     * @param $form
     */
    private function addAttachements(Gpf_Db_MailTemplate $dbTemplate, Gpf_Rpc_Form $form) {
        if (!$form->existsField('uploadedFiles') || !strlen(trim($form->getFieldValue('uploadedFiles')))) {
            return;
        }

        $imageIds = Gpf_Db_MailTemplate::getIncludedImageFileIds($form->getFieldValue('body_html'));

        $uploads = explode(',', $form->getFieldValue('uploadedFiles'));
        foreach ($uploads as $uploadFileId) {
            $dbTemplateAttachment = new Gpf_Db_MailTemplateAttachment();
            $dbTemplateAttachment->setFileId($uploadFileId);
            $dbTemplateAttachment->setTemplateId($dbTemplate->getId());
            $dbTemplateAttachment->setIsIncludedImage(in_array($uploadFileId, $imageIds));
            $dbTemplateAttachment->insert();
        }
    }


    /**
     * Create long task for generating of mails
     * @param $form
     * @param $dbTemplate
     * @return Pap_Mail_GenerateMassMailsTask
     */
    protected function createLongTask(Gpf_Rpc_Form $form, Gpf_Db_MailTemplate $dbTemplate) {
        $task = new Pap_Mail_GenerateMassMailsTask();
        $affiliateId = null;
        if(Gpf_Session::getRoleType() == 'A'){ 
            $form->setField('affiliatesFilter','custom');
            $form->setField('includeCustomMails','');
            $affiliateId = Gpf_Session::getAuthUser()->getUserId();
        }
        $task->setMassMailParams($dbTemplate->getId(), $form->getFieldValue('affiliatesFilter'), $form->getFieldValue('includeCustomMails'), $affiliateId);
        return $task;
    }


}
