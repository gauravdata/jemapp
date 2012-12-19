<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Columns.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_View_Columns extends Gpf_Object {
    private $_columns;
    private $_sqlColumns;
    private $_dataColumns;
    private $_defaultSortColumn = "";
    private $_defaultSortAsc;

    function __construct(){
        $this->_columns = array();
        $this->_sqlColumns = array();
        $this->_dataColumns = array();
    }

    public function addColumn($id, $name, $sqlName, $inHeader = true, $sortable = true) {
        $column = new Gpf_View_Column($id, $name, $sqlName, $inHeader, $sortable);
        if($column->isSqlColumn()) {
            $this->_sqlColumns[] = $column;
        }
        if($column->isDataColumn()) {
            $this->_dataColumns[] = $column;
        }
        $this->_columns[$id] = $column;
        return $column;
    }

     

    /**
     * @return Gpf_Data_RecordSet
     */
    public function getResult() {
        $result = new Gpf_Data_IndexedRecordSet('id');

        $result->setHeader(Gpf_View_Column::getMetaResultArray());
        foreach ($this->_columns as $column) {
            if($column->isInHeader()) {
                $result->add($column->getResultArray());
            }
        }
        return $result;
    }

    public function getSqlColumns() {
        return $this->_sqlColumns;
    }

    public function getDataColumns() {
        return $this->_dataColumns;
    }
}

?>
