<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: WhereClause.class.php 33349 2011-06-16 13:54:44Z mkendera $
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
class Gpf_SqlBuilder_WhereClause extends Gpf_Object {
    protected $clause = array();

    public function add($operand, $operator, $secondOperand = '', $logicOperator = 'AND', $doQuote = true, $binaryComparision = false) {
        $i = count($this->clause);
        $this->clause[$i]['obj'] = new Gpf_SqlBuilder_WhereCondition($operand, $operator, $secondOperand, $binaryComparision);
        $this->clause[$i]['operator'] = $logicOperator;
        $this->clause[$i]['obj']->doQuote = $doQuote;
    }

    public function addDontQuote($operand, $operator, $secondOperand = '', $logicOperator = 'AND', $binaryComparision = false) {
        $this->add($operand, $operator, $secondOperand, $logicOperator, false, $binaryComparision);
    }

    public function addCondition($condition, $logicOperator = 'AND') {
        $i = count($this->clause);
        $this->clause[$i]['obj'] = $condition;
        $this->clause[$i]['operator'] = $logicOperator;
    }
    
    public function getClause() {
    	return $this->clause;
    }

    public function toString() {
        $out = '';
        foreach ($this->clause as $key => $columnObj) {
            $out .= $out ? $columnObj['operator'] . ' ' : '';
            $out .= $columnObj['obj']->toString() . ' ';
        }
        if(empty($out)) {
            return '';
        }
        return "WHERE $out ";
    }

    public function equals(Gpf_SqlBuilder_WhereClause  $where) {
        return $where->toString() == $this->toString();
    }

    /**
     * @return array of table preffixes used in where clause
     */
    public function getUniqueTablePreffixes() {
        $preffixes = array();
        foreach ($this->clause as $clause) {
            $preffixes = array_merge($preffixes, $clause['obj']->getUniqueTablePreffixes());
        }
        return $preffixes;
    }
}

?>
