<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
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
class Pap_Tasks_RepairTablesTask extends Gpf_Tasks_LongTask {

    private $dbMaintenance;
    
    public function getName() {
        return $this->_('Repair table task');
    }

    /**
     *
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    private function getTables() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('TABLE_NAME', 'name');
        $select->from->add("INFORMATION_SCHEMA.TABLES");
        $select->where->add("TABLE_SCHEMA", "=", Gpf_Settings::get(Gpf_Settings_Gpf::DB_DATABASE));
        return $select;
    }

    protected function execute() {
        foreach($this->getTables()->getAllRows() as $table) {
            if($this->isPending('check_' . $table->get('name'))) {
                $this->dbMaintenance = new Gpf_SqlBuilder_DBMaintenance();
                $this->dbMaintenance->addTable($table->get('name'));
                $result = $this->dbMaintenance->maintenanceOne(Gpf_SqlBuilder_DBMaintenance::CHECK_TABLE);
                if($result->get('Msg_text') != 'OK') {
                    $this->repairTable($table->get('name'));
                }
                $this->setDone();
            }
        }
    }
    
    private function repairTable($tableName) {
        $repairResult = $this->dbMaintenance->maintenanceOne(Gpf_SqlBuilder_DBMaintenance::REPAIR_TABLE);
        if($repairResult->get('Msg_text') != 'OK') {
            throw new Gpf_Exception('dbMaintenance: Unable to REPAIR mysql table ' . $tableName . ', msg: ' . $repairResult->get('Msg_text'));
        }
    }


}
