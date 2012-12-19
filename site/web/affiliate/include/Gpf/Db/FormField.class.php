<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: FormField.class.php 26830 2010-01-13 16:06:29Z mbebjak $
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
class Gpf_Db_FormField extends Gpf_DbEngine_Row {

    const STATUS_MANDATORY = 'M';
    const STATUS_OPTIONAL = 'O';
    const STATUS_HIDDEN = 'H';
    const STATUS_DISABLED = 'D';

    const TYPE_TEXT = 'T';
    const TYPE_TEXT_WITH_DEFAULT = 'D';
    const TYPE_PASSWORD = 'P';
    const TYPE_NUMBER = 'N';
    const TYPE_CHECKBOX = 'B';
    const TYPE_LISTBOX = 'L';
    const TYPE_COUNTRY_LISTBOX = 'C';
    const TYPE_COUNTRY_LISTBOX_GWT = 'S';

    const DEFAULT_SECTION = "e2ce2502";

    /**
     * @var Gpf_Data_RecordSet
     */
    private $availableValues;

    function __construct(){
        parent::__construct();
    }
    
    function init() {
        $this->setTable(Gpf_Db_Table_FormFields::getInstance());
        parent::init();
    }

    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_FormFields::ACCOUNTID, $accountId);
    }
    
    public function setFormId($formId) {
        $this->set(Gpf_Db_Table_FormFields::FORMID, $formId);
    }

    public function setType($type) {
        $this->set(Gpf_Db_Table_FormFields::TYPE, $type);
    }

    public function setStatus($status) {
        $this->set(Gpf_Db_Table_FormFields::STATUS, $status);
    }

    public function setName($name) {
        $this->set(Gpf_Db_Table_FormFields::NAME, $name);
    }

    public function setCode($code) {
        $this->set(Gpf_Db_Table_FormFields::CODE, $code);
    }

    public function getType() {
        return $this->get(Gpf_Db_Table_FormFields::TYPE);
    }

    public function getCode() {
        return $this->get(Gpf_Db_Table_FormFields::CODE);
    }

    public function getId() {
        return $this->get(Gpf_Db_Table_FormFields::ID);
    }

    public function setAvailableValues($availableValues) {
        $this->set(Gpf_Db_Table_FormFields::AVAILABLEVALUES, $availableValues);
    }

    public function clearAvailableValues() {
        $this->availableValues = new Gpf_Data_RecordSet();
        $this->availableValues->addColumn('id');
        $this->availableValues->addColumn('name');
        $this->setAvailableValues("");
    }

    public function addAvailableValue($value, $label) {
        $record = $this->availableValues->createRecord();
        $record->set('id', $value);
        $record->set('name', $label);
        $this->availableValues->addRecord($record);
        $json = new Gpf_Rpc_Json();
        $this->setAvailableValues($json->encodeResponse($this->availableValues));
    }
}

?>
