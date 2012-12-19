<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.\n
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: RecordSet.class.php 30917 2011-01-28 14:00:50Z iivanco $
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
class Gpf_Data_RecordSet extends Gpf_Object implements IteratorAggregate, Gpf_Rpc_Serializable {

    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    protected $_array;
    /**
     * @var Gpf_Data_RecordHeader
     */
    private $_header;

    function __construct() {
        $this->init();
    }

    public function loadFromArray($rows) {
        $this->setHeader($rows[0]);

        for ($i = 1; $i < count($rows); $i++) {
            $this->add($rows[$i]);
        }
    }

    public function setHeader($header) {
        if($header instanceof Gpf_Data_RecordHeader) {
            $this->_header = $header;
            return;
        }
        $this->_header = new Gpf_Data_RecordHeader($header);
    }

    /**
     * @return Gpf_Data_RecordHeader
     */
    public function getHeader() {
        return $this->_header;
    }

    public function addRecord(Gpf_Data_Record $record) {
        $this->_array[] = $record;
    }

    /**
     * Adds new row to RecordSet
     *
     * @param array $record array of data for all columns in record
     */
    public function add($record) {
        $this->addRecord($this->getRecordObject($record));
    }

    /**
     * @return Gpf_Data_Record
     */
    public function createRecord() {
        return new Gpf_Data_Record($this->_header);
    }

    public function toObject() {
        $response = array();
        $response[] = $this->_header->toObject();
        foreach ($this->_array as $record) {
            $response[] = $record->toObject();
        }
        return $response;
    }

    public function loadFromObject($array) {
        if($array === null) {
            throw new Gpf_Exception('Array must be not NULL');
        }
        $this->_header = new Gpf_Data_RecordHeader($array[0]);
        for($i = 1; $i < count($array);$i++) {
            $record = new Gpf_Data_Record($this->_header);
            $record->loadFromObject($array[$i]);
            $this->loadRecordFromObject($record);
        }
    }

    public function sort($column, $sortType = 'ASC') {
        if (!$this->_header->contains($column)) {
            throw new Gpf_Exception('Undefined column');
        }
        $sorter = new Gpf_Data_RecordSet_Sorter($column, $sortType);
        $this->_array = $sorter->sort($this->_array);
    }

    protected function loadRecordFromObject(Gpf_Data_Record $record) {
        $this->_array[] = $record;
    }

    public function toArray() {
        $response = array();
        foreach ($this->_array as $record) {
            $response[] = $record->getAttributes();
        }
        return $response;
    }

    public function toText() {
        $text = '';
        foreach ($this->_array as $record) {
            $text .= $record->toText() . "<br>\n";
        }
        return $text;
    }

    /**
     * Return number of rows in recordset
     *
     * @return integer
     */
    public function getSize() {
        return count($this->_array);
    }

    /**
     * @return Gpf_Data_Record
     */
    public function get($i) {
        return $this->_array[$i];
    }

    /**
     * @param array/Gpf_Data_Record $record
     * @return Gpf_Data_Record
     */
    private function getRecordObject($record) {
        if(!($record instanceof Gpf_Data_Record)) {
            $record = new Gpf_Data_Record($this->_header->toArray(), $record);
        }
        return $record;
    }

    private function init() {
        $this->_array = array();
        $this->_header = new Gpf_Data_RecordHeader();
    }

    public function clear() {
        $this->init();
    }

    public function load(Gpf_SqlBuilder_SelectBuilder $select) {
        $this->init();

        foreach ($select->select->getColumns() as $column) {
            $this->_header->add($column->getAlias());
        }
        $statement = $this->createDatabase()->execute($select->toString());
        while($rowArray = $statement->fetchRow()) {
            $this->add($rowArray);
        }
    }

    /**
     *
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->_array);
    }

    public function getRecord($keyValue = null) {
        if(!array_key_exists($keyValue, $this->_array)) {
            return $this->createRecord();
        }
        return $this->_array[$keyValue];
    }

    public function addColumn($id, $defaultValue = "") {
        $this->_header->add($id);
        foreach ($this->_array as $record) {
            $record->add($id, $defaultValue);
        }
    }

    /**
     * Creates shalow copy of recordset containing only headers
     *
     * @return Gpf_Data_RecordSet
     */
    public function toShalowRecordSet() {
       $copy = new Gpf_Data_RecordSet();
       $copy->setHeader($this->_header->toArray());
       return $copy;
    }
}

class Gpf_Data_RecordSet_Sorter {

    private $sortColumn;
    private $sortType;

    function __construct($column, $sortType) {
        $this->sortColumn = $column;
        $this->sortType = $sortType;
    }

    public function sort(array $sortedArray) {
        usort($sortedArray, array($this, 'compareRecords'));
        return $sortedArray;
    }

    private function compareRecords($record1, $record2) {
        if ($record1->get($this->sortColumn) == $record2->get($this->sortColumn)) {
            return 0;
        }
        return $this->compare($record1->get($this->sortColumn), $record2->get($this->sortColumn));
    }

    private function compare($value1, $value2) {
        if ($this->sortType == Gpf_Data_RecordSet::SORT_ASC) {
            return ($value1 < $value2) ? -1 : 1;
        }
        return ($value1 < $value2) ? 1 : -1;
    }
}
?>
