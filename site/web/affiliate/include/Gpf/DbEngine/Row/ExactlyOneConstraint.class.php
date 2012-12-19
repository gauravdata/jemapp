<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
class Gpf_DbEngine_Row_ExactlyOneConstraint extends Gpf_DbEngine_Row_UniqueConstraint implements Gpf_DbEngine_Row_Constraint {
    
    /**
     * @throws Gpf_Exception
     */
    protected function doNoRowLoaded(Gpf_DbEngine_Row $row) {
        if (!$this->isAddingExactlyOneRow($row)) {
            $this->throwException();
        }
    }

    /**
     * @throws Gpf_Exception
     * @return Gpf_DbEngine_Row $row
     */
    protected function loadRow(Gpf_DbEngine_Row $row) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->from->add($row->getTable()->name());
        $selectBuilder->select->addAll($row->getTable());
        foreach ($this->uniqueColumnNames as $columnName=>$value) {
            if ($value === false) {
                $selectBuilder->where->add($columnName,'=',$row->get($columnName));
                continue;
            }
            $selectBuilder->where->add($columnName,'=',$value);
        }
        return $selectBuilder->getOneRow();
    }

    protected function doOneRowLoaded($row, $tempRow) {
        if ($this->isAddingExactlyOneRow($row)) {
            return parent::doOneRowLoaded($row, $tempRow);
        }
    }
    
    private function isAddingExactlyOneRow(Gpf_DbEngine_Row $row) {
        foreach ($this->uniqueColumnNames as $columnName => $value) {
            if ($value === false) {
                continue;
            }

            if ($row->get($columnName) != $value) {
                return false;
            }
        }
        return true;
    }

    protected function throwException() {
        if ($this->message == "") {
            throw new Gpf_DbEngine_Row_ConstraintException(
            array_keys($this->uniqueColumnNames),
            $this->_('There must be exactly one row object'));
        }
        throw new Gpf_DbEngine_Row_ConstraintException(array_keys($this->uniqueColumnNames), $this->message);
    }
}

?>
