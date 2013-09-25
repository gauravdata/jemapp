<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: OrderByClause.class.php 29376 2010-09-23 09:02:58Z iivanco $
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
class Gpf_SqlBuilder_OrderByClause extends Gpf_Object {
    private $clause = array();

    public function add($columnName, $asc = true, $tableName = '') {
        $this->clause[] = new Gpf_SqlBuilder_OrderByColumn($columnName, $asc, $tableName);
    }

    public function removeByName($columnName) {
        $clause = $this->clause;
        foreach ($clause as $key => $columnObj) {
            if($columnName == $columnObj->name) {
                unset($this->clause[$key]);
            }
        }
    }
    
    public function getAllOrderColumns() {
        return $this->clause;
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
        return "ORDER BY $out ";
    }
    
    public function isEmpty() {
        return count($this->clause) == 0;
    }
}

?>
