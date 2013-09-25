<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Merchants_Transaction_TransactionLogs extends Gpf_Object implements Gpf_Rpc_TableData {
    
    /**
     * @service
     * @anonym
     * @return Gpf_Data_RecordSet
     */
    public function getRow(Gpf_Rpc_Params $params) {
        throw new Gpf_Exception('Pap_Merchants_Transaction_TransactionLogs::getRow() Unimplemented');
    }
    
    /**
     * @service
     * @anonym
     * @return Gpf_Data_Table
     */
    public function getRows(Gpf_Rpc_Params $params) {
        $data = new Gpf_Data_Table($params);
        $select = $this->createLogsSelect($params->get(Gpf_Rpc_TableData::SEARCH));
        $data->fill($select->getAllRows());
        return $data;
    }
    
    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    private function createLogsSelect($groupId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Gpf_Db_Table_Logs::MESSAGE);
        $select->from->add(Gpf_Db_Table_Logs::getName());
        $select->where->add(Gpf_Db_Table_Logs::GROUP_ID, '=', $groupId);
        $select->orderBy->add(Gpf_Db_Table_Logs::ID);
        return $select;
    }
}
?>
