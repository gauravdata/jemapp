<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DBMaintenance.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
final class Gpf_SqlBuilder_DBMaintenance  extends Gpf_Object {

    private $tableNames;
    private $option;

    const REPAIR_TABLE = "REPAIR TABLE";
    const OPTIMIZE_TABLE = "OPTIMIZE TABLE";
    const CHECK_TABLE = "CHECK TABLE";
    const ANALYZE_TABLE = "ANALYZE TABLE";

    function __construct($tableNames = array()) {
        $this->tableNames = $tableNames;
    }

    /**
     *
     * @param String $tableName
     */
    public function addTable($tableName) {
        if ($tableName != null) {
            $this->tableNames[] = $tableName;
        } else {
            throw new Gpf_Exception($this->_("Undefined table name"));
        }
    }

    /**
     * Maintenance one tables
     *
     * @param (REPAIR_TABLE, OPTIMIZE_TABLE, CHECK_TABLE, ANALYZE_TABLE) $option
     * @return Gpf_Data_Record with columns (Table, Op, Msg_type, Msg_text)
     */
    public function maintenanceOne($option = null) {
        if ($option == null) {
            throw new Gpf_Exception($this->_("Undefined \$option"));
        }

        $this->option = $option;

        $result = $this->getAllRowsRecordSet(new Gpf_Data_RecordSet());
        return $result->get($result->getSize() - 1);
    }

    /**
     * Maintenance all tables
     *
     * @param (REPAIR_TABLE, OPTIMIZE_TABLE, CHECK_TABLE, ANALYZE_TABLE) $option
     * @return Gpf_Data_RecordSet with columns (Table, Op, Msg_type, Msg_text)
     */
    public function maintenanceAll($option = null) {
        if ($option == null) {
            throw new Gpf_Exception($this->_("Undefined \$option"));
        }
        $this->option = $option;
        
        return $this->getAllRowsRecordSet(new Gpf_Data_RecordSet());
    }

    private function execute() {
        $sth = $this->createDatabase()->execute($this->toString());
        if ($sth->rowCount() < 1) {
            throw new Gpf_DbEngine_NoRowException($this);
        }
        return $sth;
    }

    private function getAllRowsRecordSet(Gpf_Data_RecordSet $recordSet) {
        try {
            $sth = $this->execute();
        } catch (Gpf_DbEngine_NoRowException $e) {
            return $recordSet;
        }
        $row = $sth->fetchArray();
        $recordSet->setHeader(array_keys($row));
        $recordSet->add(array_values($row));

        while ($row = $sth->fetchRow()) {
            $recordSet->add($row);
        }

        return $recordSet;
    }

    private function tableNames() {
        $tableNames = "";
         
        foreach ($this->tableNames as $tableName) {
            $tableNames .= " `".$tableName."`,";
        }
         
        $tableNames = substr($tableNames, 0, -1);
         
        return $tableNames;
    }

    private function toString() {
        switch ($this->option) {
            case self::CHECK_TABLE:
                return self::CHECK_TABLE.$this->tableNames();
            case self::ANALYZE_TABLE:
                return self::ANALYZE_TABLE.$this->tableNames();
            case self::OPTIMIZE_TABLE:
                return self::OPTIMIZE_TABLE.$this->tableNames();
            case self::REPAIR_TABLE:
                return self::REPAIR_TABLE.$this->tableNames();
        }
    }
}

?>
