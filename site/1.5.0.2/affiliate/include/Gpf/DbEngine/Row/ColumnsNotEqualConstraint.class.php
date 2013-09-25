<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon, Maros Galik
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UniqueConstraint.class.php 18645 2008-06-19 12:45:06Z mbebjak $
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
class Gpf_DbEngine_Row_ColumnsNotEqualConstraint extends Gpf_Object implements Gpf_DbEngine_Row_Constraint {

    private $columnName;
    private $columnsNotEqualNames;
    private $message;

    /**
     * @param array $columnNames
     */
    public function __construct($columnName, $columnsNotEqualNames, $message = "") {
        $this->columnName = $columnName;
        $this->columnsNotEqualNames = $columnsNotEqualNames;
        $this->message = $message;
    }

    /**
     * Validate Db_Row
     *
     * @param Gpf_DbEngine_Row $row
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function validate(Gpf_DbEngine_Row $row) {
        
        $select = new Gpf_SqlBuilder_SelectBuilder();
        
        $select->select->add('COUNT(*)','cnt');
        $select->from->add($row->getTable()->name());     
        
        foreach ($row->getPrimaryColumns() as $primaryColumn) {
            $select->where->add($primaryColumn->getName(), '<>', $row->get($primaryColumn->getName()));    
        }   
        
        $conditionNotEqalColumns  = new Gpf_SqlBuilder_CompoundWhereCondition();
        foreach ($this->columnsNotEqualNames as $columnNotEqualName) {
            $conditionNotEqalColumns->add($columnNotEqualName, '=', $row->get($this->columnName), 'OR');
        }
        
        $select->where->addCondition($conditionNotEqalColumns);
        
        $select->limit->set(0, 1);

        if ($select->getOneRow()->get('cnt') > 0) {
            $this->throwException();
        }
    }

    private function throwException() {
        if ($this->message == "") {
            throw new Gpf_DbEngine_Row_ConstraintException($this->columnName,
            $this->columnName. $this->_(' can not equals to: ').
            implode(",", $this->columnsNotEqualNames));
        }
        throw new Gpf_DbEngine_Row_ConstraintException($this->columnName, $this->message);
    }
}
