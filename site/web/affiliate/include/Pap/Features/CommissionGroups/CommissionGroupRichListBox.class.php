<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: CustomersGridForm.class.php 19572 2008-08-01 16:43:19Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Features_CommissionGroups_CommissionGroupRichListBox extends Gpf_Ui_RichListBoxService {
	
   /**
     * @service commission_group read
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
        $select->select->add("cg.".Pap_Db_Table_CommissionGroups::ID, self::ID);
        $select->select->add("cg.".Pap_Db_Table_CommissionGroups::NAME, self::VALUE);
        $select->from->add(Pap_Db_Table_CommissionGroups::getName(), 'cg');
        if ($this->params->exists('campaignid')) {
            $select->where->add('cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, '=', $this->params->get('campaignid'));
        }
      
        return $select;
    }
     
    /**
     * @param $selectBuilder
     * @param $search
     */
    protected function addSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $search) {
    	$condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add('cg.'.Pap_Db_Table_CommissionGroups::NAME,
            'LIKE', '%'.$search.'%', 'OR');
        $selectBuilder->where->addCondition($condition);
    }
    
    /**
     * @param $selectBuilder
     * @param $id
     */
    protected function addIdSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $id) {
        $selectBuilder->where->add("cg.".Pap_Db_Table_CommissionGroups::ID, '=', $id);
    }
}
?>
