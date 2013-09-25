<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: SelectClause.class.php 27752 2010-04-13 13:53:11Z vzeman $
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
class Gpf_SqlBuilder_SelectClause extends Gpf_Object {
    private $clause = array();
    private $distinct = false;

    public function add($columnName, $columnAlias = '', $tablePrefix = '') {
        $column = new Gpf_SqlBuilder_SelectColumn($columnName, $columnAlias);
        $column->setTablePrefix($tablePrefix);
        if ($columnAlias == '') {
            $this->clause[] = $column;
        } else {
            $this->clause[$columnAlias] = $column;
        }
    }

    public function replaceColumn($oldColumnAlias, $columnName, $columnAlias = '', $tablePrefix = '') {
        if (array_key_exists($oldColumnAlias, $this->clause)) {
            $column = new Gpf_SqlBuilder_SelectColumn($columnName, $columnAlias);
            $column->setTablePrefix($tablePrefix);
            $this->clause[$oldColumnAlias] = $column;
        }
    }

    public function addConstant($constantValue, $alias = '') {
        $this->clause[] = new Gpf_SqlBuilder_SelectColumn($constantValue, $alias, true);
    }

    public function addAll(Gpf_DbEngine_Table $table, $tableAlias = '') {
        foreach ($table->getColumns() as $column) {
            $this->add($column->getName(), $column->getName(), $tableAlias);
        }
    }

    public function setDistinct() {
        $this->distinct = true;
    }

    public function toString() {
        $out = '';
        foreach ($this->clause as $key => $columnObj) {
            $out .= $out ? ',' : '';
            $out .= $columnObj->toString();
        }
        if(empty($out)) {
            return '';
        }
        $distinct = '';
        if($this->distinct) {
            $distinct = ' DISTINCT';
        }
        return "SELECT$distinct $out ";
    }

    public function getColumns() {
        return $this->clause;
    }

    public function existsAlias($alias) {
        return array_key_exists($alias, $this->clause);
    }

    public function equals(Gpf_SqlBuilder_SelectClause $select) {
        return $select->toString() == $this->toString();
    }
}

?>
