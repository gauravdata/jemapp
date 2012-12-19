<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Statement.class.php 27014 2010-01-29 11:47:34Z jsimon $
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
class Gpf_DbEngine_Driver_Mysql_Statement extends Gpf_Object {
    private $_handle;
    private $_statement;
    private $result;
    private $autoId = 0;
    
    function execute() {
        $this->result = mysql_query($this->_statement, $this->_handle);
        return $this->result;
    }
    
    public function getAutoIncrementId() {
        return $this->autoId;
    }
    
    public function loadAutoIncrementId() {
        $this->autoId = mysql_insert_id($this->_handle);
    }
    
    function init($statement, $handle) {
        $this->_statement = $statement;
        $this->_handle = $handle;
    }

    function getNames() {
        $numFields = mysql_num_fields($this->result);
        $names = array();
        for($i=0; $i<$numFields; $i++) {
            $names[] = mysql_field_name($this->result, $i);
        }
        return $names;
    }

    function getTypes() {
        $numFields = mysql_num_fields($this->result);
        $types = array();
        for($i=0; $i<$numFields; $i++) {
            $types[] = $this->translateType(mysql_field_type($this->result, $i));
        }
        return $types;
    }

    function fetchArray() {
        return mysql_fetch_assoc($this->result);
    }

    function fetchRow() {
        return mysql_fetch_row($this->result);
    }

    function fetchAllRows() {
        $rows = array();
        while($row = $this->fetchRow()) {
            $rows[] = $row;
        }
        return $rows;
    }

    function rowCount() {
        return mysql_num_rows($this->result);
    }

    function affectedRows() {
        return mysql_affected_rows($this->_handle);
    }

    function move($rowNumber) {
        return mysql_data_seek($this->result, $rowNumber);
    }

    function getErrorMessage() {
        switch(mysql_errno($this->_handle)) {
            case 1062:
                return 'Duplicate record' . ' ' . $this->_statement;
                break;

        }
        return mysql_error($this->_handle);
    }

    function getErrorCode() {
        return mysql_errno($this->_handle);
    }
    
    public function getStatement() {
        return $this->_statement;
    }
}
?>
