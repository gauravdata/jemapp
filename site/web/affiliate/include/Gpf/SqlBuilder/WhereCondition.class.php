<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: WhereCondition.class.php 32027 2011-04-08 13:12:18Z mkendera $
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
class Gpf_SqlBuilder_WhereCondition extends Gpf_Object {
    private $operand;
    private $operator;
    private $secondOperand;
    public $doQuote = true;
    private $binaryComparision = false;

    function __construct($operand, $operator, $secondOperand = '', $binaryComparision = false) {
        $this->operand = $operand;
        $this->operator = $operator;
        $this->secondOperand = $secondOperand;
        $this->binaryComparision = $binaryComparision;
    }

    public function toString() {
        $out = $this->binaryComparision ? 'BINARY ' : '';
        $out .= $this->operand . ' ' . $this->operator . ' ';
        if($this->secondOperand === null) {
            if($this->operator == '=') {
                return $this->operand . ' IS NULL';
            }
            if($this->operator == '!=') {
                return $this->operand . ' IS NOT NULL';
            }
        }
        if (strtoupper($this->operator) == 'IN' || strtoupper($this->operator) == 'NOT IN') {
            $out .= '(' . $this->operandToInValue($this->secondOperand) . ') ';
        } else {
            if($this->doQuote) {
                $r = "'" . $this->createDatabase()->escapeString($this->secondOperand) . "'";
            } else {
                $r = $this->secondOperand;
            }
            $out .= ' ' .$r . ' ';
        }
        return $out;
    }

    public function operandToInValue($inValue) {
        if(is_array($inValue)) {
            $out = '';
            foreach ($inValue as $value) {
                $out .= "'" . $this->createDatabase()->escapeString($value) . "',";
            }
            return rtrim($out, ',');
        }

        if ($inValue instanceof Gpf_SqlBuilder_SelectBuilder) {
            return $inValue->toString();
        }

        return '';
    }
    
    /**
     * @return array of table preffixes used in where clause
     */
    public function getUniqueTablePreffixes() {
        $preffixes = array();
        if (is_string($this->operand) && ($pos = strpos($this->operand, '.')) !== false) {
            $preffix = substr($this->operand, 0, $pos);
            $preffix = $this->fixPreffix($preffix);
            $preffixes[$preffix] = $preffix;
        }
        if (is_string($this->secondOperand) && ($pos = strpos($this->secondOperand, '.')) !== false) {
            $preffix = substr($this->secondOperand, 0, $pos);
            $preffix = $this->fixPreffix($preffix);
            $preffixes[$preffix] = $preffix;
        }
        return $preffixes;
    }
    
    private function fixPreffix($preffix) {
        if (($pos = strpos($preffix, '(')) !== false) {
            $preffix = substr($preffix, $pos+1);
        }
        return $preffix;
    }
}

?>
