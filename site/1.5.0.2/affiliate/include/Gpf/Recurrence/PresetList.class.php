<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Gadget.class.php 19129 2008-07-15 09:35:17Z mjancovic $
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
class Gpf_Recurrence_PresetList extends Gpf_Ui_RichListBoxService {
	
   /**
     * @service recurrence read
     * @param $id, $search, $from, $rowsPerPage
     * @return Gpf_Rpc_Object
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }

    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createSelectBuilder() {
    	$select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Gpf_Db_Table_RecurrencePresets::ID, self::ID);
        $select->select->add(Gpf_Db_Table_RecurrencePresets::NAME, self::VALUE);
        $select->from->add(Gpf_Db_Table_RecurrencePresets::getName());
    
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add(Gpf_Db_Table_RecurrencePresets::ACCOUNTID, 'is', 'NULL', 'OR', false);
        $condition->add(Gpf_Db_Table_RecurrencePresets::ACCOUNTID, '=', Gpf_Session::getAuthUser()->getAccountId(), 'OR');
        $select->where->addCondition($condition);    
        $select->orderBy->add(Gpf_Db_Table_RecurrencePresets::NAME);
        
        return $select;
    }
     
    /**
     * @param $selectBuilder
     * @param $search
     */
    protected function addSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $search) {
    	$selectBuilder->where->add(Gpf_Db_Table_RecurrencePresets::NAME, 'LIKE', '%'.$search.'%');
    }
    
    /**
     * @param $selectBuilder
     * @param $id
     */
    protected function addIdSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $id) {
    	$selectBuilder->where->add(Gpf_Db_Table_RecurrencePresets::ID, '=', $id);
    }
}

?>
