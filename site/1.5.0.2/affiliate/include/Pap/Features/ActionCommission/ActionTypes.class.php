<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Pap_Features_ActionCommission_ActionTypes extends Gpf_Ui_RichListBoxService {
   
   /**
     * @service commission read
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
        $select->select->add("DISTINCT(".Pap_Db_Table_CommissionTypes::CODE.")", self::ID);
        $select->select->add(Pap_Db_Table_CommissionTypes::NAME, self::VALUE);
        $select->from->add(Pap_Db_Table_CommissionTypes::getName());
        $select->where->add(Pap_Db_Table_CommissionTypes::TYPE,
            '=', Pap_Common_Constants::TYPE_ACTION);
        $select->orderBy->add(Pap_Db_Table_CommissionTypes::NAME);
        
        return $select;
    }
     
    /**
     * @param $selectBuilder
     * @param $search
     */
    protected function addSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $search) {
    	$selectBuilder->where->add(Pap_Db_Table_CommissionTypes::NAME, 'LIKE', '%'.$search.'%');
    }
    
    /**
     * @param $selectBuilder
     * @param $id
     */
    protected function addIdSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $id) {   	
        $selectBuilder->where->add(Pap_Db_Table_CommissionTypes::CODE, "=", $id);
    }
}
?>
