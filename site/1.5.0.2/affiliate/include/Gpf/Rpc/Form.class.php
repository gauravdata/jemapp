<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Form.class.php 24956 2009-07-16 08:10:07Z mbebjak $
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
class Gpf_Rpc_Form extends Gpf_Object implements Gpf_Rpc_Serializable, IteratorAggregate {
    const FIELD_NAME  = "name";
    const FIELD_VALUE = "value";
    const FIELD_ERROR = "error";
    const FIELD_VALUES = "values";

    private $isError = false;
    private $errorMessage = "";
    private $infoMessage = "";
    private $status;
    /**
     * @var Gpf_Data_IndexedRecordSet
     */
    private $fields;
    /**
     * @var Gpf_Rpc_Form_Validator_FormValidatorCollection
     */
    private $validators;

    public function __construct(Gpf_Rpc_Params $params = null) {
        $this->fields = new Gpf_Data_IndexedRecordSet(self::FIELD_NAME);

        $header = new Gpf_Data_RecordHeader();
        $header->add(self::FIELD_NAME);
        $header->add(self::FIELD_VALUE);
        $header->add(self::FIELD_VALUES);
        $header->add(self::FIELD_ERROR);
        $this->fields->setHeader($header);
        
        $this->validator = new Gpf_Rpc_Form_Validator_FormValidatorCollection($this);
        
        if($params) {
            $this->loadFieldsFromArray($params->get("fields"));
        }
    }

    /**
     * @param $validator
     * @param $fieldName
     * @param $fieldLabel
     */
    public function addValidator(Gpf_Rpc_Form_Validator_Validator $validator, $fieldName, $fieldLabel = null) {
        $this->validator->addValidator($validator, $fieldName, $fieldLabel);
    }
    
    /**
     * @return boolean
     */
    public function validate() {
        return $this->validator->validate();
    }
    
    public function loadFieldsFromArray($fields) {
        for ($i = 1; $i < count($fields); $i++) {
            $field = $fields[$i];
            $this->fields->add($field);
        }
    }
    
    /**
     *
     * @return ArrayIterator
     */
    public function getIterator() {
        return $this->fields->getIterator();
    }
    
    public function addField($name, $value) {
        $record = $this->fields->createRecord($name);
        $record->set(self::FIELD_VALUE, $value);
    }
    
    public function setField($name, $value, $values = null, $error = "") {
        $record = $this->fields->createRecord($name);
        $record->set(self::FIELD_VALUE, $value);
        $record->set(self::FIELD_VALUES, $values);
        $record->set(self::FIELD_ERROR, $error);
    }
    
    public function setFieldError($name, $error) {
        $this->isError = true;
        $record = $this->fields->getRecord($name);
        $record->set(self::FIELD_ERROR, $error);
    }
    
    public function getFieldValue($name) {
        $record = $this->fields->getRecord($name);
        return $record->get(self::FIELD_VALUE);
    }
    
    public function getFieldError($name) {
        $record = $this->fields->getRecord($name);
        return $record->get(self::FIELD_ERROR);
    }
    
    public function existsField($name) {
        return $this->fields->existsRecord($name);
    }
     
    public function load(Gpf_Data_Row $row) {
        foreach($row as $columnName => $columnValue) {
            $this->setField($columnName, $row->get($columnName));
        }
    }

    /**
     * @return Gpf_Data_IndexedRecordSet
     */
    public function getFields() {
        return $this->fields;
    }
    
    public function fill(Gpf_Data_Row $row) {
        foreach ($this->fields as $field) {
            try {
                $row->set($field->get(self::FIELD_NAME), $field->get(self::FIELD_VALUE));
            } catch (Exception $e) {
            }
        }
    }
    
    public function toObject() {
        $response = new stdClass();
        $response->fields = $this->fields->toObject();
        if ($this->isSuccessful()) {
            $response->success = Gpf::YES;
            $response->message = $this->infoMessage;
        } else {
            $response->success = "N";
            $response->message = $this->errorMessage;
        }
        return $response;
    }
    
    public function loadFromObject(stdClass $object) {
        if ($object->success == Gpf::YES) {
        	$this->setInfoMessage($object->message);
        } else {
        	$this->setErrorMessage($object->message);
        }
        
        $this->fields = new Gpf_Data_IndexedRecordSet(self::FIELD_NAME);
        $this->fields->loadFromObject($object->fields);
    }
    
    public function toText() {
        return var_dump($this->toObject());
    }

    public function setErrorMessage($message) {
        $this->isError = true;
        $this->errorMessage = $message;
    }
    
    public function getErrorMessage() {
        if ($this->isError) {
            return $this->errorMessage;
        }
        return "";
    }
    
    public function setInfoMessage($message) {
        $this->infoMessage = $message;
    }
    
    public function setSuccessful() {
        $this->isError = false;
    }
    
    public function getInfoMessage() {
        if ($this->isError) {
            return "";
        }
        return $this->infoMessage;
    }
    
    
    /**
     * @return boolean
     */
    public function isSuccessful() {
        return !$this->isError;
    }
    
    /**
     * @return boolean
     */
    public function isError() {
        return $this->isError;
    }
}

?>
