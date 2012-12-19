<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Matej Kendera
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Logs.class.php 27761 2010-04-14 07:45:41Z vzeman $
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
class Pap_Merchants_Tools_ViewsGrid extends Gpf_View_GridService implements Gpf_View_Grid_HasRowFilter {

    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_ActiveViews::VIEWTYPE, $this->_('View type'), true);
        $this->addViewColumn(Gpf_Db_Table_Views::NAME, $this->_('View name'), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('av.'.Gpf_Db_Table_ActiveViews::VIEWTYPE);
        $this->addDataColumn(Gpf_Db_Table_ActiveViews::VIEWTYPE, 'av.'.Gpf_Db_Table_ActiveViews::VIEWTYPE);
        $this->addDataColumn(Gpf_Db_Table_ActiveViews::ACTIVEVIEWID, 'av.'.Gpf_Db_Table_ActiveViews::ACTIVEVIEWID);
        $this->addDataColumn(Gpf_Db_Table_Views::NAME, 'v.'.Gpf_Db_Table_Views::NAME);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_ActiveViews::VIEWTYPE, '', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Views::NAME, '', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '');
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        switch ($filter->getCode()) {
            case "search":
                $this->addSearch($filter);
                break;
        }
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Gpf_Db_Table_ActiveViews::getName(), 'av');
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_Views::getName(), 'v', 'av.'.Gpf_Db_Table_ActiveViews::ACTIVEVIEWID.' = v.'.Gpf_Db_Table_Views::ID);
        $this->_selectBuilder->where->add('av.'.Gpf_Db_Table_ActiveViews::ACCOUNTUSERID, '=', Gpf_Session::getAuthUser()->getUserData()->get(Gpf_Db_Table_Users::ID));
        
        $whereCond = new Gpf_SqlBuilder_CompoundWhereCondition();
        $whereCond->add('v.'.Gpf_Db_Table_Views::ACCOUNTUSERID, '!=', '', 'OR');
        $whereCond->add('av.'.Gpf_Db_Table_ActiveViews::ACTIVEVIEWID, '=', Gpf_View_ViewService::DEFAULT_VIEW_ID, 'OR');
        
        $this->_selectBuilder->where->addCondition($whereCond);
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $whereCond = new Gpf_SqlBuilder_CompoundWhereCondition();
        $whereCond->add('av.'.Gpf_Db_Table_ActiveViews::VIEWTYPE, 'LIKE', '%' . $filter->getValue() .'%', 'OR');
        $whereCond->add('av.'.Gpf_Db_Table_ActiveViews::ACTIVEVIEWID, 'LIKE', '%' . $filter->getValue() .'%', 'OR');
        $whereCond->add('v.'.Gpf_Db_Table_Views::NAME, 'LIKE', '%' . $filter->getValue() .'%', 'OR');
        
        $this->_selectBuilder->where->addCondition($whereCond);
    }

    /**
     * @param $row
     * @return DataRow or null
     */
    public function filterRow(Gpf_Data_Row $row) {
        if ($row->get(Gpf_Db_Table_ActiveViews::ACTIVEVIEWID) == Gpf_View_ViewService::DEFAULT_VIEW_ID) {
            $row->set(Gpf_Db_Table_Views::NAME, Gpf_View_ViewService::DEFAULT_VIEW_NAME);
        }

        return $row;
    }

    protected function createCountSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $preffixes = $select->where->getUniqueTablePreffixes();
        $preffixes['av'] = 'av';

        $count = new Gpf_SqlBuilder_SelectBuilder();
        $count->select->add('count(*)', 'count');
        $count->from = clone $select->from;
        $count->from->prune($preffixes);
        $count->where = $select->where;
        $count->groupBy = $select->groupBy;
        $count->having = $select->having;
        return $count;
    }

    /**
     * @service views read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
}
?>
