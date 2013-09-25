<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: SelectBuilder.class.php 27752 2010-04-13 13:53:11Z vzeman $
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
class Gpf_SqlBuilder_SelectBuilder extends Gpf_Object {
    public $tableName;

    /**
     * @var Gpf_SqlBuilder_SelectClause
     */
    public $select;
    /**
     * @var Gpf_SqlBuilder_FromClause
     */
    public $from;
    /**
     * @var Gpf_SqlBuilder_WhereClause
     */
    public $where;
    /**
     * @var Gpf_SqlBuilder_GroupByClause
     */
    public $groupBy;
    /**
     * @var Gpf_SqlBuilder_OrderByClause
     */
    public $orderBy;
    /**
     * @var Gpf_SqlBuilder_LimitClause
     */
    public $limit;
    /**
     * @var Gpf_SqlBuilder_HavingClause
     */
    public $having;

    function __construct() {
        $this->select = new Gpf_SqlBuilder_SelectClause();
        $this->from = new Gpf_SqlBuilder_FromClause();
        $this->where = new Gpf_SqlBuilder_WhereClause();
        $this->groupBy = new Gpf_SqlBuilder_GroupByClause();
        $this->orderBy = new Gpf_SqlBuilder_OrderByClause();
        $this->limit = new Gpf_SqlBuilder_LimitClause();
        $this->having = new Gpf_SqlBuilder_HavingClause();
        $this->initSelect();
    }

    public function cloneObj($obj) {
        $this->select = clone $obj->select;
        $this->from = clone $obj->from;
        $this->where = clone $obj->where;
        $this->having = clone $obj->having;
        $this->groupBy = clone $obj->groupBy;
        $this->orderBy = clone $obj->orderBy;
        $this->limit = clone $obj->limit;
    }

    /**
     * @throws Gpf_DbEngine_TooManyRowsException
     * @throws Gpf_DbEngine_NoRowException
     * @return Gpf_Data_Record
     */
    public function getOneRow() {
        $sth = $this->execute();

        if ($sth->rowCount() > 1) {
            throw new Gpf_DbEngine_TooManyRowsException($this);
        }

        $row = $sth->fetchArray();
        return new Gpf_Data_Record(array_keys($row), array_values($row));
    }

    /**
     * @throws Gpf_DbEngine_NoRowException
     * @return Gpf_DbEngine_Driver_Mysql_Statement
     */
    private function execute() {
        $sth = $this->createDatabase()->execute($this->toString());
        if ($sth->rowCount() < 1) {
            throw new Gpf_DbEngine_NoRowException($this);
        }
        return $sth;
    }

    private function getAllRowsRecordSet(Gpf_Data_RecordSet $recordSet) {
        try {
            $sth = $this->execute();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return $recordSet;
        }
        $row = $sth->fetchArray();
        $recordSet->setHeader(array_keys($row));
        $recordSet->add(array_values($row));

        while ($row = $sth->fetchRow()) {
            $recordSet->add($row);
        }

        return $recordSet;
    }

    /**
     * @return Gpf_SqlBuilder_SelectIterator
     */
    public function getAllRowsIterator() {
    	return new Gpf_SqlBuilder_SelectIterator($this->toString());
    }

    /**
     * @return Gpf_Data_RecordSet
     */
    public function getAllRows() {
        return $this->getAllRowsRecordSet(new Gpf_Data_RecordSet());
    }

    /**
     * @return Gpf_Data_IndexedRecordSet
     */
    public function getAllRowsIndexedBy($keyColumn) {
        return $this->getAllRowsRecordSet(new Gpf_Data_IndexedRecordSet($keyColumn));
    }

    /**
     * @param Gpf_SqlBuilder_SelectBuilder $selectBuilder
     * @return boolean
     */
    public function equals(Gpf_SqlBuilder_SelectBuilder	$selectBuilder) {
        return $selectBuilder->toString() == $this->toString();
    }

    public function toString() {
        return $this->select->toString().
        ($this->from->isEmpty() ? '' : "FROM ". $this->from->toString()).
        $this->where->toString().
        $this->groupBy->toString().
        $this->having->toString().
        $this->orderBy->toString().
        $this->limit->toString();
    }

    private function initSelect() {
        if(!empty($this->tableName)) {
            $this->from->add($this->tableName);
        }
    }
}

?>
