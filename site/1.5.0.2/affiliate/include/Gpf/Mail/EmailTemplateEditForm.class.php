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
class Gpf_Mail_EmailTemplateEditForm extends Gpf_View_FormService {

    /**
     * Return list of files assigned to mail template
     * @service mail_template read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    public function getUploadedFiles(Gpf_Rpc_Params $params) {
        $templateId = $params->get("templateId");
        $dbRow = $this->loadMailTemplate($templateId);

        $sql = new Gpf_SqlBuilder_SelectBuilder();
        $sql->select->add(Gpf_Db_Table_Files::ID, Gpf_Db_Table_Files::ID, 'f');
        $sql->select->add(Gpf_Db_Table_Files::FILE_NAME);
        $sql->select->add(Gpf_Db_Table_Files::FILE_TYPE);
        $sql->select->add(Gpf_Db_Table_Files::FILE_SIZE);
        $sql->select->add(Gpf_Db_Table_MailTemplateAttachments::IS_INCLUDED_IMAGE);

        $sql->from->add(Gpf_Db_Table_MailTemplateAttachments::getName(), 'ma');
        $sql->from->addInnerJoin(Gpf_Db_Table_Files::getName(), 'f', 'f.fileid=ma.fileid');
        $sql->where->add(Gpf_Db_Table_MailTemplates::ID, '=', $templateId);
        return $sql->getAllRows();
    }

    /**
     * Load list of template variables for template specified in input parameter templateId
     *
     * @service mail_template read
     * @param Gpf_Rpc_Params $params
     */
    public function getTemplateVariables(Gpf_Rpc_Params $params) {
        $dbRow = $this->loadMailTemplate($params->get("templateId"));

        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->setHeader(array('code', 'name'));

        $className = $dbRow->get(Gpf_Db_Table_MailTemplates::CLASS_NAME);
        $objTemplate = new $className();

        foreach ($objTemplate->getTemplateVariables() as $code => $name) {
            $recordSet->add(array($code, $name));
        }

        return $recordSet;
    }

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
    public function save(Gpf_Rpc_Params $params) {
        return $this->unEscapeStyleTags(parent::save($params));
    }

    private function unEscapeStyleTags(Gpf_Rpc_Form $form) {
        //smarty template needs to have escaped body of <style> tag by {literal} tag
        $pattern = array('/\<style(.*?)\>\{literal\}/ms', '/\{\/literal\}\<\/style\>/ms');
        $replacement = array('<style${1}>', '</style>');
        $form->setField('body_html', preg_replace($pattern, $replacement, $form->getFieldValue('body_html')));
        return $form;
    }

    private function escapeStyleTags(Gpf_Rpc_Form $form) {
        //smarty template needs to have escaped body of <style> tag by {literal} tag
        $pattern = array('/\<style(.*?)\>/ms', '/\<\/style\>/ms');
        $replacement = array('<style${1}>{literal}', '{/literal}</style>');
        $form->setField('body_html', preg_replace($pattern, $replacement, $form->getFieldValue('body_html')));
        return $form;
    }

    /**
     * @service mail_template read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = parent::load($params);
        return $this->unEscapeStyleTags($form);
    }

    protected function createDbRowObject() {
        return new Gpf_Db_MailTemplate();
    }

    protected function updateRow(Gpf_Rpc_Form $form, Gpf_Db_MailTemplate $mailTemplate) {
        parent::updateRow($form, $mailTemplate);
    }

    protected function fillSave(Gpf_Rpc_Form $form, Gpf_Db_MailTemplate $mailTemplate) {
        $this->fillRowFromForm($form, $mailTemplate);
    }

    protected function fillAdd(Gpf_Rpc_Form $form, Gpf_Db_MailTemplate $mailTemplate) {
        $this->fillRowFromForm($form, $mailTemplate);
    }

    private function fillRowFromForm(Gpf_Rpc_Form $form, Gpf_Db_MailTemplate $mailTemplate) {
        $form = $this->escapeStyleTags($form);
        foreach ($form->getFields() as $field) {
            try {
                $fieldName = $field->get(Gpf_Rpc_Form::FIELD_NAME);
                $fieldValue = $field->get(Gpf_Rpc_Form::FIELD_VALUE);

                if ($this->isMappedMethodForField($fieldName)) {
                    $this->callMappedMethod($fieldName, $fieldValue, $mailTemplate);
                    continue;
                }

                $mailTemplate->set($fieldName, $fieldValue);
            } catch (Exception $e) {
            }
        }
    }

    private function isMappedMethodForField($fieldName) {
        return in_array($fieldName, array_keys(Gpf_Db_MailTemplate::getSetMethodMap()));
    }

    private function callMappedMethod($fieldName, $fieldValue, Gpf_Db_MailTemplate $mailTemplate) {
        $map = $mailTemplate->getSetMethodMap();
        $method = $map[$fieldName];
        $mailTemplate->$method($fieldValue);
        $mailTemplate->setChanged(true);
    }
}
?>
