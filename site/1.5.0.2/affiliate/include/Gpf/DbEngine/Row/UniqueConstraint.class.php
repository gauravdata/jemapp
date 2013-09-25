<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UniqueConstraint.class.php 28406 2010-06-07 11:23:33Z mgalik $
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
class Gpf_DbEngine_Row_UniqueConstraint extends Gpf_Object implements Gpf_DbEngine_Row_Constraint {

    protected $uniqueColumnNames;
    protected $message;

    /**
     * @param array $columnNames
     */
    public function __construct($columnNames, $message = "") {
        $this->uniqueColumnNames = $columnNames;
        $this->message = $message;
    }

    /**
     * Validate Db_Row
     *
     * @param Gpf_DbEngine_Row $row
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function validate(Gpf_DbEngine_Row $row) {
        try {
            $tempRow = $this->loadRow($row);
        } catch (Gpf_DbEngine_NoRowException $e) {
            $this->doNoRowLoaded($row);
            return;
        } catch (Gpf_DbEngine_TooManyRowsException $e) {
            $this->throwException();
        }

        $this->doOneRowLoaded($row, $tempRow);
    }

    /**
     * @throws Gpf_Exception
     */
    protected function doOneRowLoaded(Gpf_DbEngine_Row $row, $tempRow) {
        $primaryColumns = $row->getPrimaryColumns();
        foreach ($primaryColumns as $column) {
            if ($tempRow->get($column->getName()) != $row->get($column->getName())) {
                $this->throwException();
            }
        }
    }

    /**
     * @throws Gpf_Exception
     */
    protected function doNoRowLoaded(Gpf_DbEngine_Row $row) {
    }

    /**
     * @return  Gpf_DbEngine_Row $row
     */
    protected function loadRow(Gpf_DbEngine_Row $row) {
        $tempRow = clone $row;
        $tempRow->loadFromData($this->uniqueColumnNames);
        return $tempRow;
    }

    protected function throwException() {
        if ($this->message == "") {
            throw new Gpf_DbEngine_Row_ConstraintException(implode(",", $this->uniqueColumnNames),
            implode(",", $this->uniqueColumnNames)." ".$this->_('must be unique'));
        }
        throw new Gpf_DbEngine_Row_ConstraintException(implode(",", $this->uniqueColumnNames), $this->message);
    }
}

?>
