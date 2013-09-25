<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: LengthConstraint.class.php 24476 2009-05-19 14:49:33Z mgalik $
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
class Gpf_DbEngine_Row_LengthConstraint extends Gpf_Object implements Gpf_DbEngine_Row_Constraint {
    
    private $columnName;
    private $minLength;
    private $maxLength;
    private $minMessage;
    private $maxMessage;
    
    /**
     * @param string $columnNames
     */
    public function __construct($columnName, $minLength, $maxLength, $minMessage = '', $maxMessage = '') {
        $this->columnName = $columnName;
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->minMessage = $minMessage;
        $this->maxMessage = $maxMessage;
    }
    
    /**
     * Validate Db_Row
     *
     * @param Gpf_DbEngine_Row $row
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function validate(Gpf_DbEngine_Row $row) {
        if ($this->minLength == 0 && $this->maxLength == 0) {
            return;
        }
        
        if ($this->minLength > 0 &&
            strlen($row->get($this->columnName)) < $this->minLength) {
            if ($this->minMessage == '') {
                throw new Gpf_DbEngine_Row_ConstraintException($this->columnName,
                    $this->_('Minimum length of %s in %s is %s', $this->columnName, get_class($row), $this->minLength-1));
            } else {
                throw new Gpf_DbEngine_Row_ConstraintException($this->columnName,
                    Gpf_Lang::_replaceArgs($this->minMessage, $this->minLength-1));   
            }
        }
        
        if ($this->maxLength > 0 &&
            strlen($row->get($this->columnName)) > $this->maxLength) {
            if ($this->maxMessage == '') {
                throw new Gpf_DbEngine_Row_ConstraintException($this->columnName,
                    $this->_('Maximum length of %s in %s is %s', $this->columnName, get_class($row), $this->maxLength));
            } else {
                throw new Gpf_DbEngine_Row_ConstraintException($this->columnName,
                    Gpf_Lang::_replaceArgs($this->maxMessage, $this->maxLength));    
            }
        }
    }
}
