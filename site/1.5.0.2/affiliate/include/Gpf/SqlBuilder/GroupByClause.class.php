<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: GroupByClause.class.php 23478 2009-02-12 14:48:17Z aharsani $
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
class Gpf_SqlBuilder_GroupByClause extends Gpf_Object {
    private $clause = array();

    public function add($columnName) {
    	if($columnName != '') {
        	$this->clause[] = $columnName;
    	}
    }
    
    public function addAll(Gpf_DbEngine_Table $table, $tableAlias = '') {
        $alias = '';
        if($tableAlias != '') {
            $alias = $tableAlias . '.';
        }
        foreach ($table->getColumns() as $column) {
            $this->add($alias . $column->getName());            
        }
    }
    
    public function removeByName($columnName) {
        $clause = $this->clause;
        foreach ($clause as $key => $column) {
            if($columnName == $column) {
                unset($this->clause[$key]);
            }
        }
    }

    public function toString() {
        $out = '';
        foreach ($this->clause as $key => $columnName) {
            $out .= $out ? ',' : '';
            $out .= $columnName;
        }
        if(empty($out)) {
            return '';
        }
        return "GROUP BY $out ";
    }
    
    public function equals(Gpf_SqlBuilder_GroupByClause $groupBy) {
        return $groupBy->toString() == $this->toString();
    }
}

?>
