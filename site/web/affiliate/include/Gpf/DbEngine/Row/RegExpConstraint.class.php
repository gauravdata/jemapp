<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: LengthConstraint.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_DbEngine_Row_RegExpConstraint extends Gpf_Object implements Gpf_DbEngine_Row_Constraint {
    
    private $columnName;
    private $regExp;
    private $message;
    
    /**
     * @param string $columnName
     * @param string $regExp
     */
    public function __construct($columnName, $regExp, $message = "") {
        $this->columnName = $columnName;
        $this->regExp = $regExp;
        $this->message = $message;
    }
    
    /**
     * Validate Db_Row
     *
     * @param Gpf_DbEngine_Row $row
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function validate(Gpf_DbEngine_Row $row) {
        $value = $row->get($this->columnName); 
        if ($value == null || $value == "") {
            return;
        }
        if (preg_match($this->regExp, $value) != 1) {
            if ($this->message == "") {
                throw new Gpf_DbEngine_Row_ConstraintException($this->columnName,
                                $this->_("Column %s contains unallowed characters",
                                $this->columnName));
            }
            throw new Gpf_DbEngine_Row_ConstraintException($this->columnName, Gpf_Lang::_replaceArgs($this->message, $value));
            
        }
    }
}
