<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Matej Kendera
 *   @since Version 1.0.0
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
class Pap_Merchants_Tools_ViewsListBox extends Gpf_Ui_RichListBoxService {

    /**
     * @service views read
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
        $filters = new Gpf_Rpc_FilterCollection($this->params);
        
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('v.'.Gpf_Db_Table_Views::ID, self::ID);
        $selectBuilder->select->add('v.'.Gpf_Db_Table_Views::NAME, self::VALUE);
        $selectBuilder->from->add(Gpf_Db_Table_Views::getName(), 'v');

        $whereCond = new Gpf_SqlBuilder_CompoundWhereCondition();
        $whereCond->add('v.' . Gpf_Db_Table_Views::ACCOUNTUSERID, '=', Gpf_Session::getAuthUser()->getUserData()->get(Gpf_Db_Table_Users::ID));
        $whereCond->add('v.' . Gpf_Db_Table_Views::ACCOUNTUSERID, '=', '', 'OR');
        
        $selectBuilder->where->addCondition($whereCond);
        if ($filters->isFilter('viewtype')) {
            $selectBuilder->where->add('v.' . Gpf_Db_Table_Views::VIEWTYPE, '=', $filters->getFilterValue('viewtype'));
        }
        return $selectBuilder;
    }
     
    /**
     * @param $selectBuilder
     * @param $search
     */
    protected function addSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $search) {
        $selectBuilder->where->add('v.'.Gpf_Db_Table_Views::NAME, 'LIKE', '%'.$search.'%');
    }

    /**
     * @param $selectBuilder
     * @param $id
     */
    protected function addIdSearchCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $id) {
        $selectBuilder->where->add('v.'.Gpf_Db_Table_Views::ID, '=', $id);
    }
}

?>
