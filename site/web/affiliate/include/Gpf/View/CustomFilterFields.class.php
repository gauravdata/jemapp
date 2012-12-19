<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: CustomFilterFields.class.php 25788 2009-10-23 08:54:24Z mbebjak $
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
class Gpf_View_CustomFilterFields extends Gpf_Object {
    
    /**
     * @var Gpf_Data_RecordSet
     */
    private $fieldsRecordSet;
    /**
     * @var Gpf_SqlBuilder_Filter
     */
    private $filter;
    
    const CODE = 'code';
    const NAME = 'name';
    const TYPE = 'type';
    const OPERATORS = 'operators';
    
    const OPERATOR_CODE = 'id';
    const OPERATOR_NAME = 'value';

    function __construct() {
        $this->fieldsRecordSet = new Gpf_Data_RecordSet();
        $header = new Gpf_Data_RecordHeader();
        $header->add(self::CODE);
        $header->add(self::NAME);
        $header->add(self::TYPE);
        $header->add(self::OPERATORS);
        $this->fieldsRecordSet->setHeader($header);
        $this->filter = new Gpf_SqlBuilder_Filter();
    }
    
    public function addStringField($sqlCode, $name) {
        $this->addField($sqlCode, $name,
            Gpf_SqlBuilder_Filter::STRING,
            $this->filter->getStringOperators());
    }
     
    public function addDateField($sqlCode, $name) {
        $this->addField($sqlCode, $name,
            Gpf_SqlBuilder_Filter::DATETIME,
            $this->filter->getDateTimeOperators());
    }
     
    public function addNumberField($sqlCode, $name) {
        $this->addField($sqlCode, $name,
            Gpf_SqlBuilder_Filter::NUMBER,
            $this->filter->getNumberOperators());
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    public function getRecordSet() {
        return $this->fieldsRecordSet;
    }
    
    /**
     *
     * @param String $sqlCode
     * @param String $name
     * @param Array $operators
     */
    private function addField($sqlCode, $name, $type, $operators) {
        $record = $this->fieldsRecordSet->createRecord();
        $record->set(self::CODE, $sqlCode);
        $record->set(self::NAME, $name);
        $record->set(self::TYPE, $type);
        $record->set(self::OPERATORS, $this->createOperatorsRecordSet($operators)->toObject());
        $this->fieldsRecordSet->addRecord($record);
    }
    
    /**
     * @param array<Gpf_SqlBuilder_Operator> $operators
     * @return Gpf_Data_RecordSet
     */
    private function createOperatorsRecordSet($operators) {
        $recordSet = new Gpf_Data_RecordSet();
        $header = new Gpf_Data_RecordHeader();
        $header->add(self::OPERATOR_CODE);
        $header->add(self::OPERATOR_NAME);
        $recordSet->setHeader($header);
        foreach ($operators as $operator) {
            $record = $recordSet->createRecord();
            $record->set(self::OPERATOR_CODE, $operator->getCode());
            $record->set(self::OPERATOR_NAME, $operator->getName());
            $recordSet->addRecord($record);
        }
        return $recordSet;
    }
}
?>
