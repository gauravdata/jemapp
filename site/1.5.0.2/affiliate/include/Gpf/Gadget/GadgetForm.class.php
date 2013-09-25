<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: GadgetForm.class.php 24612 2009-06-11 13:28:02Z aharsani $
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

class Gpf_Gadget_GadgetForm extends Gpf_View_FormService {

    const FORM_GADGET_NAME = 'gadgetName_dbn3jhk';

    /**
     * @var Gpf_Gadget
     */
    private $gadget;

    /**
     * @return Gpf_Gadget
     */
    protected function createDbRowObject() {
        $this->gadget = new Gpf_Gadget();
        return $this->gadget;
    }

    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Gadget");
    }

    /**
     *
     * @service gadget read
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $dbRow = $this->createDbRowObject();
        $dbRow->setPrimaryKeyValue($this->getId($form));
        try {
            $dbRow->load();
            $form->addField(self::FORM_GADGET_NAME, $dbRow->get(Gpf_Db_Table_Gadgets::NAME));
        } catch (Gpf_DbEngine_NoRow $e) {
            throw new Exception($this->getDbRowObjectName().$this->_(" does not exist"));
        }
        $properties = $this->gadget->getProperties();
        foreach ($properties as $property) {
            $form->setField($property->getName(), $property->getValue());
        }
        return $form;
    }

    /**
     *
     * @service gadget write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $dbRow = $this->createDbRowObject();
        $dbRow->setPrimaryKeyValue($this->getId($form));

        try {
            $dbRow->load();
        } catch (Gpf_DbEngine_NoRow $e) {
            $form->setErrorMessage($this->getDbRowObjectName().$this->_(" does not exist"));
            return $form;
        }

        $dbRow->set(Gpf_Db_Table_Gadgets::NAME, $form->getFieldValue(self::FORM_GADGET_NAME));

        if(!$this->checkBeforeSave($dbRow, $form, self::EDIT)) {
            return $form;
        }
        try {
            $dbRow->save();
            $this->afterSave($dbRow, self::EDIT);
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
            return $form;
        }

        $form->setField(self::FORM_GADGET_NAME, $dbRow->get(Gpf_Db_Table_Gadgets::NAME));
        $form->setInfoMessage($this->getDbRowObjectName().$this->_(" saved"));

        $formField = new Gpf_Db_FormField();
        $formField->setFormId($this->gadget->getPreferencesFormId());
        foreach ($formField->loadCollection() as $field) {
            $code = $field->getCode();
            $this->gadget->addProperty($code, $form->getFieldValue($code));
        }
        return $form;
    }

    /**
     * @service gadget add
     * @param Gpf_Rpc_Params $params
     */
    public function add(Gpf_Rpc_Params $params) {
        $gadgetManager = new Gpf_GadgetManager();
        $form = new Gpf_Rpc_Form($params);
        try {
            $gadget = $gadgetManager->addGadgetNoRpc(
            $form->getFieldValue('name'),
            $form->getFieldValue('url'),
            $form->getFieldValue('positiontype'));
            $form->setField('formId', $gadget->getPreferencesFormId());
            $form->setField("Id", $gadget->getId());
        } catch (Gpf_Exception $e) {
            $form->setErrorMessage($e->getMessage());
        }
        $form->setInfoMessage($this->_("Gadget added"));
        return $form;
    }

    /**
     * @service gadget delete
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Action
     */
    public function closeGadget(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_('Failed to close gadget'));
        $action->setInfoMessage($this->_('Gadget successfully closed'));

        foreach ($action->getIds() as $id) {
            try {
                $gadget = $this->createDbRowObject();
                $gadget->setPrimaryKeyValue($id);
                $gadget->delete();
                $action->addOk();
            } catch (Exception $e) {
                $action->addError();
            }
        }
        return $action;
    }

    /**
     * @service gadget write
     * @return Gpf_Rpc_Action
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        return parent::saveFields($params);
    }

    /**
     * @service gadget delete
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }
}
?>
