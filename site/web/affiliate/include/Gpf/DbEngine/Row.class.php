<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani, Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Row.class.php 28546 2010-06-17 11:00:44Z jkudlac $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * This class represents one row in DB Table
 *
 * @package GwtPhpFramework
 */
class Gpf_DbEngine_Row extends Gpf_DbEngine_RowBase implements Iterator, Gpf_Rpc_Serializable, Gpf_Templates_HasAttributes  {
    const NULL = '_NULL_';

    /**
     * @var array
     */
    private $columns;
    /**
     * @var Gpf_DbEngine_Table
     */
    private $table;

    /**
     * @var Gpf_DbEngine_Database
     */
    private $db;


    /**
     * @var boolean
     */
    private $recordChanged = true;

    /**
     * iterator position
     *
     * @var int
     */
    private $position = 0;

    /**
     * @var array of Gpf_DbEngine_Row_Constraint
     */
    private $constraints = array();

    private $tableColumns;

    /**
     * Creates instance of Db_Row object and generates new primary key value
     */
    public function __construct() {
        $this->db = $this->createDatabase();
        $this->init();
    }

    /**
     * @return string text representation of Db_Row object
     */
    public function __toString() {
        return get_class($this) . " (" . $this->toText() . ')';
    }

    /**
     * Return array of attributes in form column -> value
     *
     * @return array
     */
    public function toArray() {
        $array = array();
        foreach ($this as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }

    /**
     * Deletes row. Primary key value must be set before this function is called
     */
    public function delete() {
        if($this->isPrimaryKeyEmpty()) {
            throw new Gpf_Exception("Could not delete Row. Primary key values are empty");
        }

        foreach ($this->table->getDeleteConstraints() as $deleteConstraint) {
            $deleteConstraint->execute($this);
        }

        $deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBuilder->from->add($this->table->name());
        $deleteBuilder->where = $this->getPrimaryWhereClause();
         
        $deleteBuilder->deleteOne();
    }

    /**
     * Updates row. Primary key value must be set before this function is called
     *
     * @param array $updateColumns list of columns that should be updated. if not set, all modified columns are update
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function update($updateColumns = array()) {
        if($this->isPrimaryKeyEmpty()) {
            throw new Gpf_Exception("Could not update Row. Primary key values are empty");
        }

        $this->beforeSaveCheck();

        $this->beforeSaveAction();

        $this->updateRow($updateColumns);
    }

    /**
     * Inserts row
     *
     * @throws Gpf_DbEngine_Row_ConstraintException
     * @throws Gpf_DbEngine_DuplicateEntryException
     */
    public function insert() {
        $this->beforeSaveCheck();

        $this->beforeSaveAction();

        $this->insertRow();
    }

    /**
     * Saves row. If row exists in table (was loaded before) it is updated,
     * otherwise new row is added
     *
     * @throws Gpf_DbEngine_Row_ConstraintException
     * @throws Gpf_DbEngine_DuplicateEntryException
     */
    public function save() { 	
        if ($this->isPersistent()) {
            if ($this->isChanged()) {
                $this->update();
            }
        } else {
            $this->insert();
        }
    }

    /**
     * Loads row by primary key value
     *
     * @throws Gpf_DbEngine_NoRowException if selected row does not exist
     */
    public function load() {
        $this->loadRow($this->getPrimaryColumns());
    }

    /**
     * Loads row by attribute values that have been already set
     * If $loadColumns parameter is set, row is loaded by values in columns specified by $loadColumns parameter
     *
     * @param array $loadColumns list of column names
     * @throws Gpf_DbEngine_NoRowException
     * @throws Gpf_DbEngine_TooManyRowsException
     */
    public function loadFromData(array $loadColumns = array()) {
        $this->loadRow($this->getLoadKey($loadColumns), true);
    }

    /**
     * Loads collection of row objects by attribute values that have been already set
     * If $loadColumns parameter is set, collection is loaded by values in columns specified by $loadColumns parameter
     *
     * @param array $loadColumns
     * @return Gpf_DbEngine_Row_Collection
     */
    public function loadCollection(array $loadColumns = array()) {
        $select = $this->getLoadSelect($this->getLoadKey($loadColumns), true);
        return $this->loadCollectionFromRecordset($select->getAllRows());
    }

    /**
     * @param $rowsRecordSet
     * @return Gpf_DbEngine_Row_Collection
     */
    public function loadCollectionFromRecordset(Gpf_Data_RecordSet $rowsRecordSet) {
        return $this->fillCollectionFromRecordset(new Gpf_DbEngine_Row_Collection(), $rowsRecordSet);
    }

    /**
     * @return Gpf_DbEngine_Row_Collection
     */
    protected function fillCollectionFromRecordset(Gpf_DbEngine_Row_Collection $collection, Gpf_Data_RecordSet $rowsRecordSet) {
        foreach ($rowsRecordSet as $rowRecord) {
            $dbRow = clone $this;
            $dbRow->fillFromRecord($rowRecord);
            $dbRow->isPersistent = true;
            $collection->add($dbRow);
        }
        return $collection;
    }

    /**
     * Checks if row with primary key already exists
     *
     * @return true if row exists, otherwise false
     */
    public function rowExists() {
        try {
            $select = $this->getLoadSelect($this->getPrimaryColumns());
            $select->getOneRow();
        } catch (Gpf_Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Fills Db_Row from a record
     * Fields that are not part of the Db_Row are ignored
     *
     * @param Gpf_Data_Record $record
     */
    public function fillFromRecord(Gpf_Data_Record $record) {
        foreach ($this->tableColumns as $column) {
            $name = $column->name;
            try {
                $this->set($name, $record->get($name));
            } catch (Gpf_Exception $e) {
            }
        }
        $this->afterLoad();
    }

    /**
     * Fills Db_Row from select. Select should return one row.
     *
     * @param Gpf_SqlBuilder_SelectBuilder $select
     * @throws Gpf_DbEngine_NoRowException
     * @throws Gpf_DbEngine_TooManyRowsException
     */
    public function fillFromSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $this->fillFromRecord($select->getOneRow());
        $this->isPersistent = true;
    }

    /**
     * Sets value of the primary key
     *
     * @param string $value
     * @throws Gpf_Exception if row has more than a one primary key
     */
    public function setPrimaryKeyValue($value) {
        $this->set($this->getSinglePrimaryKeyColumn()->getName(), $value);
    }

    /**
     * Gets value of the primary key
     *
     * @throws Gpf_Exception if row has more than a one primary key
     * @return string
     */
    public function getPrimaryKeyValue() {
        return $this->get($this->getSinglePrimaryKeyColumn()->getName());
    }

    /**
     * Performs explicit check on Db_Row
     *
     * @throws Gpf_DbEngine_Row_CheckException if there is some error
     */
    public function check() {
        $constraintExceptions = array();

        foreach ($this->table->getConstraints() as $constraint) {
            try {
                $constraint->validate($this);
            } catch (Gpf_DbEngine_Row_ConstraintException $e) {
                $constraintExceptions[] = $e;
            }
        }
        if (count($constraintExceptions) > 0) {
            throw new Gpf_DbEngine_Row_CheckException($constraintExceptions);
        }
    }

    /**
     * Sets value of the field to SQL NULL
     *
     * @param string $name
     * @throws Gpf_DbEngine_Row_MissingFieldException
     */
    public function setNull($name) {
        $this->set($name, self::NULL);
    }

    public function isPrimaryKeyEmpty() {
        return $this->isRowKeyEmpty($this->getPrimaryColumns());
    }

    /**
     *
     * @return array
     */
    public function getPrimaryColumns() {
        return $this->table->getPrimaryColumns();
    }

    public function prepareSelectClause(Gpf_SqlBuilder_SelectBuilder $select, $aliasPrefix = '') {
        $alias = rtrim($aliasPrefix, '_');
        foreach($this->tableColumns as $column) {
            if($aliasPrefix != '') {
                $select->select->add($column->name, $aliasPrefix . $column->name, $alias);
            } else {
                $select->select->add($column->name);
            }
        }
    }

    /**
     * @return Gpf_DbEngine_Table
     */
    public function getTable() {
        return $this->table;
    }

    /*************************************************************************/
    /********************** Interface: Gpf_Data_Row ************************/
    /*************************************************************************/

    /**
     * Sets value of the field
     *
     * @param string $name
     * @param mixed $value
     * @throws Gpf_DbEngine_Row_MissingFieldException
     */
    public function set($name, $value) {
        if (is_object($value)) {
            throw new Gpf_Exception("Value of column $name cannot be an object");
        }
        $value = (string) $value;
        if($this->get($name) === $value) {
            return;
        }
        $this->recordChanged = true;

        if ($value === '' && in_array($this->tableColumns[$name]->getType(),
        array(Gpf_DbEngine_Column::TYPE_NUMBER, Gpf_DbEngine_Column::TYPE_DATE))) {
            $this->setNull($name);
        } else {
            $this->columns[$name] = $value;
        }
    }

    public function setChanged($value) {
        $this->recordChanged = $value;
    }

    /**
     * Returns value of the field
     *
     * @param string $name name of the field
     * @return string
     * @throws Gpf_DbEngine_Row_MissingFieldException
     */
    public function get($name) {
        $value = $this->getInternalValue($name);
        if ($value == self::NULL) {
            return null;
        }
        return $value;
    }

    /*************************************************************************/
    /******************* Interface: Gpf_Rpc_Serializable ***********************/
    /*************************************************************************/

    public function toObject() {
        $obj = new stdClass();
        foreach ($this as $id => $val) {
            $obj->$id = $val;
        }
        return $obj;
    }

    public function toText() {
        $text = "";
        foreach ($this as $id => $value) {
            $text .= "$id = $value, ";
        }
        return rtrim($text, ", ");
    }

    /*************************************************************************/
    /************* Interface: Gpf_Templates_HasAttributes ******************/
    /*************************************************************************/
     
    public function getAttributes() {
        return $this->toArray();
    }

    /*************************************************************************/
    /************************* Interface: Iterator ***************************/
    /*************************************************************************/
     

    public function current() {
        $columns = $this->tableColumns;
        return $this->get($this->key());
    }

    public function key() {
        $columns = $this->tableColumns;
        $i=0;
        foreach ($columns as $id => $column) {
            if ($this->position == $i) {
                return $id;
            }
            $i++;
        }
        return false;
    }

    public function next() {
        $this->position++;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function valid() {
        return $this->position < count($this->tableColumns);
    }

    /**
     * Sets table of the Db_Row object
     *
     * @param Gpf_DbEngine_Table $table
     */
    protected function setTable(Gpf_DbEngine_Table $table) {
        $this->table = $table;
        $this->tableColumns = $table->getColumns();
    }

    /**
     * Inits Db_Row object
     *
     */
    protected function init() {
        $this->columns = array();
        $this->isPersistent = false;
    }

    /**
     * Generates new primary key value
     * Keys with already set values, don't change
     */
    protected function generatePrimaryKey() {
        foreach($this->table->getPrimaryColumns() as $column) {
            if($column->isAutogenerated() && $column->type == "String" && !strlen($this->get($column->name))) {
                $this->set($column->name, Gpf_Common_String::generateId($column->length));
            }
        }
    }

    /**
     * This method is executed after row object is loaded from database
     */
    protected function afterLoad() {
    }

    /**
     * Performs any additional actions that are needed before row is saved
     */
    protected function beforeSaveAction() {
    }

    /**
     * Performs check before row is saved
     *
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    protected function beforeSaveCheck() {
        foreach ($this->table->getConstraints() as $constraint) {
            $constraint->validate($this);
        }
    }

    /**
     * @param string $name name of the field
     * @return string, null, self::NULL
     *   - null is returned when value for this field has not been set so far
     *   - self::NULL is returned when value of this field has to be set to null in DB
     * @throws Gpf_DbEngine_Row_MissingFieldException
     */
    private function getInternalValue($name) {
        if (@$this->tableColumns[$name] === null) {
            throw new Gpf_DbEngine_Row_MissingFieldException($name, get_class($this));
        }
        return @$this->columns[$name];
    }

    private function getPrimaryWhereClause() {
        return $this->getRowKeyWhereClause($this->getPrimaryColumns());
    }

    private function clearPrimaryKey() {
        $primaryKeyColumns = $this->getPrimaryColumns();
        foreach ($primaryKeyColumns as $column) {
            $this->set($column->getName(), null);
        }
    }

    private function getLoadKey(array $loadColumns = array()) {
        $rowKey = array();
        if (is_array($loadColumns) && count($loadColumns)) {
            foreach ($loadColumns as $columnName) {
                $rowKey[] = $this->table->getColumn($columnName);
            }
        } else {
            foreach ($this->tableColumns as $index => $column) {
                if($this->getInternalValue($column->name) !== null) {
                    $rowKey[$column->name] = $column;
                }
            }
        }
        return $rowKey;
    }

    protected function getRowKeyWhereClause($rowKey) {
        $builder = new Gpf_SqlBuilder_SelectBuilder();
        foreach($rowKey as $column) {
            if($this->getInternalValue($column->name) == self::NULL) {
                $builder->where->add($column->name, 'is', 'NULL', 'AND', false);
            } else {
                $builder->where->add($column->name, '=', $this->get($column->name));
            }
        }
        return $builder->where;
    }


    /**
     * @throws Gpf_DbEngine_NoRowException
     * @throws Gpf_DbEngine_TooManyRowsException
     * @throws Gpf_Exception
     */
    private function loadRow($rowKey, $withAlternate = false) {
        $select = $this->getLoadSelect($rowKey, $withAlternate);
        $this->fillFromSelect($select);
        $this->recordChanged = false;
    }

    private function isRowKeyEmpty($rowKey) {
        foreach($rowKey as $column) {
            if($this->get($column->name) === null || $this->get($column->name) == "") {
                return true;
            }
        }
        return false;
    }

    /**
     * @return Gpf_DbEngine_Column
     * @throws Gpf_Exception if row has more than a one primary key
     */
    private function getSinglePrimaryKeyColumn() {
        $primaryKeys = $this->getPrimaryColumns();
        if (count($primaryKeys) != 1) {
            throw new Gpf_Exception("Can not use setPrimaryKeyValue() method as "
            . get_class($this) . " has multiple column primary key");
        }
        reset($primaryKeys);
        return current($primaryKeys);
    }

    private function isChanged() {
        return $this->recordChanged;
    }

    private function hasAutoIncrementedKey() {
        return $this->table->hasAutoIncrementedKey();
    }

    /**
     *
     * @return Gpf_DbEngine_Column
     */
    private function getAutoIncrementedColumn() {
        return $this->table->getAutoIncrementedColumn();
    }

    private function hasAutogeneratedKey() {
        foreach($this->table->getPrimaryColumns() as $column) {
            if($column->isAutogenerated() && $column->type == Gpf_DbEngine_Column::TYPE_STRING) {
                return true;
            }
        }
        return false;
    }

    /**
     * @throws Gpf_Exception
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function getLoadSelect($rowKey, $withAlternate = false) {
        if(!$withAlternate && $this->isRowKeyEmpty($rowKey)) {
            throw new Gpf_Exception("Could not load Row. Primary key values empty");
        }

        $select = $this->prepareLoadSelect();
        $select->where = $this->getRowKeyWhereClause($rowKey);
        return $select;
    }

    private $loadSelect = null;

    private function prepareLoadSelect() {
        if ($this->loadSelect === null) {
            $this->loadSelect = new Gpf_SqlBuilder_SelectBuilder();
            $this->prepareSelectClause($this->loadSelect);
            $this->loadSelect->from->add($this->table->name());
            return $this->loadSelect;
        }
        return clone $this->loadSelect;
    }

    /**
     * @return Gpf_SqlBuilder_UpdateBuilder
     */
    protected function createUpdateBuilder() {
        return new Gpf_SqlBuilder_UpdateBuilder();
    }

    private function updateRow($updateColumns = array()) {
        $updateBuilder = $this->createUpdateBuilder();
        $updateBuilder->from->add($this->table->name());

        foreach($this->tableColumns as $column) {
            if(count($updateColumns) > 0 && !in_array($column->name, $updateColumns, true)) {
                continue;
            }
            $columnValue = $this->getInternalValue($column->name);
            if(!$this->table->isPrimary($column->name) &&  $columnValue !== null) {
                if($columnValue == self::NULL) {
                    $updateBuilder->set->add($column->name, 'NULL', false);
                } else {
                    $updateBuilder->set->add($column->name, $columnValue, $column->doQuote());
                }
            }
        }

        $updateBuilder->where = $this->getPrimaryWhereClause();
        
        $updateBuilder->updateOne();
    }
    
    /**
     * @throws Gpf_DbEngine_DuplicateEntryException
     */
    private function insertRow() {
        if ($this->isPrimaryKeyEmpty()) {
            $this->generatePrimaryKey();
        }

        $this->executeInsertRow();
        $this->isPersistent = true;
    }

    /**
     * @return Gpf_SqlBuilder_InsertBuilder()
     */
    protected function createInsertBuilder() {
        return new Gpf_SqlBuilder_InsertBuilder();
    }

    /**
     * @throws Gpf_DbEngine_DuplicateEntryException
     */
    private function executeInsertRow() {
        $insertBuilder = $this->createInsertBuilder();
        $insertBuilder->setTable($this->table);
         
        if($this->hasAutoIncrementedKey() && !$this->get($this->getAutoIncrementedColumn()->getName())) {
            $this->set($this->getAutoIncrementedColumn()->getName(), 0);
        }
        foreach($this->tableColumns as $column) {
            $value = $this->getInternalValue($column->name);
            if ($value === null) {
                continue;
            }
            if ($value == self::NULL) {
                $insertBuilder->add($column->name, 'NULL', false);
                continue;
            }
            $insertBuilder->add($column->name, $value, $column->doQuote());
        }
        if($this->hasAutoIncrementedKey() && !$this->get($this->getAutoIncrementedColumn()->getName())) {
            $statement = $insertBuilder->insertAutoincrement();
            $this->set($this->getAutoIncrementedColumn()->getName(), $statement->getAutoIncrementId());
        } else {
            $insertBuilder->insert();
        }
    }
}

?>
