<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: InsertBuilder.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_SqlBuilder_InsertBuilder extends Gpf_SqlBuilder_ModifyBuilder {
    private $columns = array();
    private $tableName;
    
    /**
     * @var Gpf_DbEngine_Table
     */
    private $table;

    private $fromSelect = null;

    function __construct() {
    }

    public function add($column, $value, $doQuote = true) {
        $i = count($this->columns);
        $this->columns[$i]['column'] = $column;
        $this->columns[$i]['value']  = $value;
        $this->columns[$i]['doQuote']  = $doQuote;
    }

    public function addDontQuote($column, $value) {
        $this->add($column, $value, false);
    }

    public function setTable(Gpf_DbEngine_Table $table) {
        $this->tableName = $table->name();
        $this->table = $table;
    }

    public function toString() {
        $out =  "INSERT INTO $this->tableName (";
        foreach ($this->columns as $column) {
            $out .= $column['column'] . ',';
        }
        $out = rtrim($out, ',') . ') ';
        if(strlen($this->fromSelect)) {
            return $out . $this->fromSelect;
        }
        $out .= ' VALUES (';
        foreach ($this->columns as $column) {
            $value = $this->createDatabase()->escapeString($column['value']);
            if ($column['doQuote']) {
                $out .= "'" . $value . "'";
            } else {
                if ($value === null) {
                    $out .= "NULL";
                } else {
                    $out .= $value;
                }
            }
            $out .= ',';
        }
        return rtrim($out, ',') . ')';
    }

    public function insertAutoincrement() {
        return $this->createDatabase()->execute($this->toString(), true);
    }

    public function insert() {
        try {
            return $this->execute();
        } catch (Gpf_DbEngine_SqlException $e) {
            if($e->isDuplicateEntryError()) {
                throw new Gpf_DbEngine_DuplicateEntryException($e->getMessage());
            }
            throw $e;
        }
    }

    public function fromSelect(Gpf_SqlBuilder_SelectBuilder $selectBuilder) {
        $this->fromSelect = $selectBuilder->toString();
        foreach ($selectBuilder->select->getColumns() as $column) {
            if($this->table !== null && !$this->table->hasColumn($column->getAlias())) {
                throw new Gpf_Exception('Column ' . $column->getAlias() 
                    . " doesn't exist in $this->tableName.");
            }
            $i = count($this->columns);
            $this->columns[$i]['column'] = $column->getAlias();
        }
    }
}
?>
