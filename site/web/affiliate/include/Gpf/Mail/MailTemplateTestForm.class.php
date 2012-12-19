<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: EmailSettingsForms.class.php 18318 2008-06-02 10:18:29Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Mail_MailTemplateTestForm extends Gpf_View_FormService {

    /**
     * Load template db object
     *
     * @param string $templateId
     * @return Gpf_Db_MailTemplate
     */
    private function loadMailTemplate($templateId) {
        $mailTemplate = new Gpf_Db_MailTemplate();
        $mailTemplate->setPrimaryKeyValue($templateId);
        $this->loadRow($mailTemplate);
        $mailTemplate->setBodyHtmlOnPreviewImages();
        return $mailTemplate;
    }

    /**
     * @service mail_template write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return $this->save($params);
    }
    /**
     * @service mail_template write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        try {
            $mailTemplate = $this->loadMailTemplate($form->getFieldValue('templateid'));
        } catch (Gpf_Exception $e) {
            $form->setErrorMessage($this->_('Failed to load mail template with error: %s', $e->getMessage()));
            return $form;
        }

        try {
            $className = $mailTemplate->get(Gpf_Db_Table_MailTemplates::CLASS_NAME);
            $objTemplate = new $className();

            $templateVariables = new Gpf_Data_RecordSet();
            $templateVariables->setHeader(array('id', 'value'));
            foreach ($objTemplate->getTemplateVariables() as $code => $name) {
                $record = $templateVariables->createRecord();
                $record->set('id', $code);
                $record->set('value', $form->getFieldValue('var_' . $code));
                $templateVariables->add($record);
            }
            $objTemplate->setCachedVariableValues($templateVariables);

            $objTemplate->addRecipient($form->getFieldValue('recipient'));
            $objTemplate->sendNow();

        } catch(Gpf_Exception $e) {
            $form->setErrorMessage($this->_('Failed to send test mail with error: %s', $e->getMessage()));
            return $form;
        }
        $form->setInfoMessage($this->_('Test mail was successfully queued'));
        return $form;
    }

    /**
     * @service mail_template read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        throw new Gpf_Exception("Load not implemented");
    }

    protected function createDbRowObject() {
        return new Gpf_Db_MailTemplate();
    }
}
?>
