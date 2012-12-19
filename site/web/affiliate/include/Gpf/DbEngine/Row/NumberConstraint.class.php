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
class Gpf_DbEngine_Row_NumberConstraint extends Gpf_Object implements Gpf_DbEngine_Row_Constraint {
    
    private $columnName;
    private $length;
    
    /**
     * @param string $columnNames
     */
    public function __construct($columnName) {
        $this->columnName = $columnName;
    }
    
    /**
     * Validate Db_Row
     *
     * @param Gpf_DbEngine_Row $row
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function validate(Gpf_DbEngine_Row $row) {
        $value = $row->get($this->columnName);
        if ($value === null || $value == '') {
            return;
        }
        if (!is_numeric($value)) {
            throw new Gpf_DbEngine_Row_ConstraintException($this->columnName,
                $this->_("Column %s must be number (%s given)",
                         $this->columnName, $value));
        }
    }
}
