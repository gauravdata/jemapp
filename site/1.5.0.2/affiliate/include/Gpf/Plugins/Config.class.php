<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Context.class.php 21019 2008-09-19 12:40:08Z mfric $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the 'License'); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
abstract class Gpf_Plugins_Config extends Gpf_Object {

    /**
     * @var Gpf_Data_RecordSet
     */
    private $fieldsRecordset;

    public function __construct() {
        $this->fieldsRecordset = new Gpf_Data_RecordSet();
        $this->fieldsRecordset->setHeader(array('id', 'code', 'name', 'type', 'status', 'availablevalues', 'help'));
        $this->initFields();
    }

    abstract protected function initFields();
    abstract public function save(Gpf_Rpc_Params $params);
    abstract public function load(Gpf_Rpc_Params $params);

    public function addTextBox($caption, $code, $help='') {
        $this->addField($caption, $code, Gpf_Db_FormField::TYPE_TEXT, Gpf_Db_FormField::STATUS_OPTIONAL, $help);
    }

    public function addPasswordTextBox($caption, $code, $help='') {
        $this->addField($caption, $code, Gpf_Db_FormField::TYPE_PASSWORD, Gpf_Db_FormField::STATUS_OPTIONAL, $help);
    }

    public function addTextBoxWithDefault($caption, $code, $defaultValue, $defaultText, $help='', $setDefaultValue = false) {
        $this->addField($caption, $code, Gpf_Db_FormField::TYPE_TEXT_WITH_DEFAULT, Gpf_Db_FormField::STATUS_OPTIONAL, $help,
        array('defaultValue'=>$defaultValue, 'defaultText'=>$defaultText, 'setDefaultValue'=>($setDefaultValue?'Y':'N')));
    }

    private function addField($caption, $code, $type, $status, $help='', $values = array()) {
        $record = $this->fieldsRecordset->createRecord();
        $record->set('id', '0');
        $record->set('code', $code);
        $record->set('name', $caption);
        $record->set('help', $help);
        $record->set('type', $type);
        $record->set('status', $status);
        if (count($values) > 0) {
            $valuesRecordSet = new Gpf_Data_RecordSet();
            $valuesRecordSet->setHeader(array("id", "value"));
            foreach ($values as $id => $value) {
                if ($id != '') {
                    $valuesRecordSet->add(array($id, $value));
                }
            }
            $json = new Gpf_Rpc_Json();
            $record->set('availablevalues', $json->encode($valuesRecordSet->toObject()));
        }
        $this->fieldsRecordset->addRecord($record);
    }

    public function addCheckBox($caption, $code, $help='') {
        $this->addField($caption, $code, Gpf_Db_FormField::TYPE_CHECKBOX, Gpf_Db_FormField::STATUS_OPTIONAL, $help);
    }

    public function addListBox($caption, $code, $values, $help='') {
        $this->addField($caption, $code, Gpf_Db_FormField::TYPE_LISTBOX, Gpf_Db_FormField::STATUS_OPTIONAL, $help, $values);
    }

    /**
     * Returns list of configuration fields for the plugin
     *
     * @anonym
     * @service
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    public function getFields(Gpf_Rpc_Params $params) {
        return $this->fieldsRecordset;
    }
}
?>
