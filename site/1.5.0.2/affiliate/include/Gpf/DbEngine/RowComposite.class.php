<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: RowComposite.class.php 26019 2009-11-07 22:58:01Z vzeman $
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
abstract class Gpf_DbEngine_RowComposite extends Gpf_DbEngine_RowBase implements IteratorAggregate  {
    protected $rowObjects;
    private $mainRow;

    protected function __construct(Gpf_DbEngine_RowBase $mainRow, $alias = '') {
        $this->mainRow = $mainRow;
        $this->addRowObject($mainRow, $alias);
    }

    protected function addRowObject(Gpf_DbEngine_RowBase $row, $alias = '') {
        if ($alias != '') {
            $this->rowObjects[$alias . '_'] = $row;
        } else {
            $this->rowObjects[] = $row;
        }
    }

    /**
     * Fills Db_Row from a record
     * Fields that are not part of the Db_Row are ignored
     *
     * @param Gpf_Data_Record $record
     */
    public function fillFromRecord(Gpf_Data_Record $record) {
        foreach ($this->rowObjects as $alias => $rowObject) {
            $rowObject->fillFromRecord($record);
        }
    }

    public function toArray() {
        $array = array();
        foreach ($this->rowObjects as $alias => $rowObject) {
            $array = array_merge($array, $rowObject->toArray());
        }
        return $array;
    }

    public function fillFromSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $collection = array();
        $this->prepareSelectClause($select);
        foreach ($select->getAllRowsIterator() as $rowRecord) {
            $row = new $this;
            $row->fillFromRecord($rowRecord);
            $row->setPersistent(true);
            $collection[] = $row;
        }
        return $collection;
    }

    public function prepareSelectClause(Gpf_SqlBuilder_SelectBuilder $select, $aliasPrefix = '') {
        foreach ($this->rowObjects as $alias => $rowObject) {
            $rowObject->prepareSelectClause($select, $alias);
        }
    }

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function getRowObject($alias) {
        return $this->rowObjects[$alias];
    }

    public function get($name) {
        foreach ($this->rowObjects as $row) {
            try {
                return $row->get($name);
            } catch (Gpf_Exception $e) {
            }
        }
        throw new Gpf_Exception("Column '$name' is not valid in row composite");
    }

    public function set($name, $value) {
        $success = false;
        foreach ($this->rowObjects as $row) {
            try {
                $row->set($name, $value);
                $success = true;
            } catch (Gpf_Exception $e) {
            }
        }
        if (!$success) {
            throw new Gpf_Exception("Column '$name' is not valid in row composite");
        }
    }

    public function getAttributes() {
        $attributes = array();
        foreach ($this->rowObjects as $alias => $rowObject) {
            $attributes = array_merge($attributes, $rowObject->getAttributes());
        }
        return $attributes;
    }

    public function getIterator() {
        $columns = array();
        foreach ($this->rowObjects as $row) {
            foreach ($row as $columnName => $columnValue) {
                if (isset($columns[$columnName])) {
                    continue;
                }
                $columns[$columnName] = $columnValue;
            }
        }
        return new ArrayIterator($columns);
    }

    /**
     * Performs explicit check on Db_Row
     *
     * @throws Gpf_DbEngine_Row_CheckException if there is some error
     */
    public function check() {
        $constraintExceptions = array();
        foreach ($this->rowObjects as $rowObject) {
            try {
                $rowObject->check();
            } catch (Gpf_DbEngine_Row_CheckException $e) {
                foreach ($e as $constraintException) {
                    $constraintExceptions[] = $constraintException;
                }
            }
        }
        if (count($constraintExceptions) > 0) {
            throw new Gpf_DbEngine_Row_CheckException($constraintExceptions);
        }
    }
}

?>
