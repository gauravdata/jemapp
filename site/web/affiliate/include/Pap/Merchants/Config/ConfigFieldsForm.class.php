<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: FormFieldForm.class.php 20176 2008-08-26 13:55:39Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
abstract class Pap_Merchants_Config_ConfigFieldsForm extends Gpf_View_FormService {

    /**
     * @return Gpf_Db_FormField
     */
    protected function createDbRowObject() {
        return new Gpf_Db_FormField();
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Field");
    }
     
    /**
     * @param Gpf_DbEngine_Row $dbRow
     */
    protected function setDefaultDbRowObjectValues(Gpf_DbEngine_Row $dbRow) {
        $dbRow->set(Gpf_Db_Table_Accounts::ID, Gpf_Session::getAuthUser()->getAccountId());
    }

    protected function saveFieldsRpc(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to save %s field(s) in %s(s)', '%s', $this->getDbRowObjectName()));
        $action->setInfoMessage($this->_('%s field(s) in %s(s) successfully saved', '%s', $this->getDbRowObjectName()));

        $fields = new Gpf_Data_RecordSet();
        $fields->loadFromArray($action->getParam("fields"));


        foreach ($fields as $field) {
            $dbRow = $this->createDbRowObject();
            $dbRow->setPrimaryKeyValue($field->get(Gpf_Db_Table_FormFields::ID));
            $dbRow->load();
            $dbRow->setCode($field->get(Gpf_Db_Table_FormFields::CODE));
            $dbRow->setName($field->get(Gpf_Db_Table_FormFields::NAME));
            $dbRow->setStatus($field->get(Gpf_Db_Table_FormFields::STATUS));
            $dbRow->setType($field->get(Gpf_Db_Table_FormFields::TYPE));
            if ($field->get(Gpf_Db_Table_FormFields::AVAILABLEVALUES) != null) {
                $dbRow->setAvailableValues($field->get(Gpf_Db_Table_FormFields::AVAILABLEVALUES));
            }
            $dbRow->save();
            $action->addOk();
        }

        return $action;
    }

    public function loadFieldsFromFormIDRpc(Gpf_Rpc_Params $params) {
        $this->getFormDefinition()->check();
        $form = new Gpf_Rpc_Form($params);
        $form->setField('dynamicFields', '', $this->loadFields()->toObject());
        return $form;
    }

    /**
     * @return Gpf_Data_RecordSet
     */
    public function loadFields(){
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Gpf_Db_Table_FormFields::ID);
        $select->select->add(Gpf_Db_Table_FormFields::CODE);
        $select->select->add(Gpf_Db_Table_FormFields::NAME);
        $select->select->add(Gpf_Db_Table_FormFields::TYPE);
        $select->select->add(Gpf_Db_Table_FormFields::STATUS);
        $select->select->add(Gpf_Db_Table_FormFields::AVAILABLEVALUES);
        $select->from->add(Gpf_Db_Table_FormFields::getName());
        $select->where->add(Gpf_Db_Table_FormFields::FORMID, '=', $this->getFormId());
        return $select->getAllRows();
    }

    protected abstract function getFormDefinition();

    protected abstract function getFormId();
}

?>
