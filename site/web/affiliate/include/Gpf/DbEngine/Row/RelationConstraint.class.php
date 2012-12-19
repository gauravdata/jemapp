<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: RelationConstraint.class.php 18859 2008-06-26 14:57:55Z mbebjak $
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
class Gpf_DbEngine_Row_RelationConstraint extends Gpf_Object implements Gpf_DbEngine_Row_Constraint {
    
    private $columnMapping;
    private $parentRow;
    private $mandatoryParent;
    private $message;
    
    public function __construct(array $columnMapping, Gpf_DbEngine_Row $parentRow, $mandatoryParent = true, $message = null) {
        foreach ($parentRow->getPrimaryColumns() as $column) {
            if(!in_array($column->getName(), $columnMapping)) {
                throw new Gpf_Exception("Column %s is not part of primary key", 
                    $column->getName());
            }
        }
        $this->columnMapping = $columnMapping;
        $this->parentRow = $parentRow;
        $this->mandatoryParent = $mandatoryParent;
        if ($message != null) {
            $this->message = $message;
        } else {
            $this->message = $this->_("Relation constraint");
        }
    }
    
    /**
     * Validate Db_Row
     *
     * @param Gpf_DbEngine_Row $row
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function validate(Gpf_DbEngine_Row $row) {
        foreach ($this->columnMapping as $column => $parentColumn) {
            $this->parentRow->set($parentColumn, $row->get($column));
        }
        
        if($this->parentRow->isPrimaryKeyEmpty() && !$this->mandatoryParent) {
            return;
        }

        try {
            $this->parentRow->load();
        } catch (Gpf_Exception $e) {
            //TODO: create new Exception class
            throw new Gpf_DbEngine_Row_ConstraintException('', $this->message);
        }
    }
}
