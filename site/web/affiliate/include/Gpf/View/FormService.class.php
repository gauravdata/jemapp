<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: FormService.class.php 26254 2009-11-25 13:15:49Z jsimon $
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
abstract class Gpf_View_FormService extends Gpf_Object implements Gpf_FormHandler {
    const ADD = "add";
    const EDIT = "edit";
    const ID = "Id";

    /**
     * @return Gpf_DbEngine_RowBase
     */
    protected abstract function createDbRowObject();

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_('Row');
    }
     
    /**
     * @param Gpf_DbEngine_RowBase $dbRow
     */
    protected function setDefaultDbRowObjectValues(Gpf_DbEngine_RowBase $dbRow) {
    }

    protected function getId(Gpf_Rpc_Form $form) {
        return $form->getFieldValue(self::ID);
    }


    protected function loadRow(Gpf_DbEngine_RowBase $row) {
        try {
            $row->load();
        } catch (Gpf_DbEngine_NoRow $e) {
            throw new Gpf_Exception($this->_("%s does not exist", $this->getDbRowObjectName()));
        }
    }

    protected function loadForm(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $dbRow) {
        $form->load($dbRow);
    }


    /**
     *
     * @service
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $dbRow = $this->createDbRowObject();
        $dbRow->setPrimaryKeyValue($this->getId($form));
        $this->loadRow($dbRow);
        $this->loadForm($form, $dbRow);
        return $form;
    }

    protected function updateRow(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $row) {
        //TODO: here should be update() instead of save()
        $row->save();
    }

    protected function fillSave(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $dbRow) {
        $form->fill($dbRow);
    }

    protected function fillAdd(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $dbRow) {
        $form->fill($dbRow);
    }

    /**
     *
     * @service
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $dbRow = $this->createDbRowObject();
        $dbRow->setPrimaryKeyValue($this->getId($form));

        try {
            $this->loadRow($dbRow);
        } catch (Exception  $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $this->fillSave($form, $dbRow);
        $dbRow->setPrimaryKeyValue($this->getId($form));

        if(!$this->checkBeforeSave($dbRow, $form, self::EDIT)) {
            return $form;
        }
        try {
            $this->updateRow($form, $dbRow);
            $this->afterSave($dbRow, self::EDIT);
        } catch (Gpf_DbEngine_Row_CheckException $checkException) {
            foreach ($checkException as $contstraintException) {
                if ($form->existsField($contstraintException->getFieldCode())) {
                    $form->setFieldError($contstraintException->getFieldCode(), $contstraintException->getMessage());
                }
            }
            $form->setErrorMessage($checkException->getMessage());
            return $form;
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $this->loadForm($form, $dbRow);
        $form->setInfoMessage($this->_("%s saved", $this->getDbRowObjectName()));
        return $form;
    }

    /**
     * Checks fields before saving
     *
     * @override
     * @return true/false
     */
    protected function checkBeforeSave(Gpf_DbEngine_RowBase $row, Gpf_Rpc_Form $form, $operationType = self::EDIT) {
        return true;
    }


    protected function addRow(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $row) {
        $row->insert();
    }

    /**
     *
     * @service
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $dbRow = $this->createDbRowObject();
        $this->setDefaultDbRowObjectValues($dbRow);

        $this->fillAdd($form, $dbRow);

        //TODO: remove following lines and all dependencies
        if(!$this->checkBeforeSave($dbRow, $form, self::ADD)) {
            return $form;
        }

        try {
            $this->addRow($form, $dbRow);
            //TODO: remove following line and all dependencies
            $this->afterSave($dbRow, self::ADD);
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $form->load($dbRow);
        $form->setField("Id", $dbRow->getPrimaryKeyValue());
        $form->setInfoMessage($this->_("%s was successfuly added", $this->getDbRowObjectName()));
        $form->setSuccessful();
        return $form;
    }

    /**
     * called after the object is saved
     *
     * @param unknown_type $dbRow
     */
    protected function afterSave($dbRow, $saveType) {
    }

    /**
     * @service
     *
     * @param $fields
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to save %s field(s) in %s(s)', '%s', $this->getDbRowObjectName()));
        $action->setInfoMessage($this->_('%s field(s) in %s(s) successfully saved', '%s', $this->getDbRowObjectName()));

        $fields = new Gpf_Data_RecordSet();
        $fields->loadFromArray($action->getParam("fields"));


        foreach ($fields as $field) {
            $dbRow = $this->createDbRowObject();
            $dbRow->setPrimaryKeyValue($field->get('id'));
            $dbRow->load();
            $dbRow->set($field->get("name"), $field->get("value"));
            $dbRow->save();
            $action->addOk();
        }

        return $action;
    }

    /**
     * @service
     * @param $ids
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $errorMessages = "";
        foreach ($action->getIds() as $id) {
            try {
                $row = $this->createDbRowObject();
                $row->setPrimaryKeyValue($id);
                $this->deleteRow($row);
                $action->addOk();
            } catch (Exception $e) {
                $action->addError();
                $errorMessages .= '<br/>' . $e->getMessage();
            }
        }
         
        $action->setErrorMessage($this->_('Failed to delete %s %s(s)', '%s', $this->getDbRowObjectName()) .
                                    '<br/>' .
        $this->_('Error details: %s', $errorMessages));
        $action->setInfoMessage($this->_('%s %s(s) successfully deleted', '%s', $this->getDbRowObjectName()));
         
        return $action;
    }
    
    protected function deleteRow(Gpf_DbEngine_RowBase $row) {
        $row->delete();
    }
}

?>
