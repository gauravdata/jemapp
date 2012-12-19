<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Logs.class.php 32969 2011-06-01 11:36:32Z mkendera $
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
class Gpf_Log_Logs extends Gpf_View_GridService {
    const LOGS_DIR = 'log/';

    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_Logs::ID, $this->_("ID"), true);
        $this->addViewColumn(Gpf_Db_Table_Logs::CREATED, $this->_("Created"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Gpf_Db_Table_Logs::LEVEL, $this->_("Level"), true);
        $this->addViewColumn(Gpf_Db_Table_Logs::TYPE, $this->_("Type"), true);
        $this->addViewColumn(Gpf_Db_Table_Logs::MESSAGE, $this->_("Message"), true);
        $this->addViewColumn(Gpf_Db_Table_Logs::IP, $this->_("IP"), true, Gpf_View_ViewColumn::TYPE_IP);
        $this->addViewColumn(Gpf_Db_Table_Logs::FILENAME, $this->_("File"), true);
        $this->addViewColumn(Gpf_Db_Table_Logs::LINE, $this->_("Line"), true);
        $this->addViewColumn(Gpf_Db_Table_Logs::GROUP_ID, $this->_("Group ID"), true);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Gpf_Db_Table_Logs::ID);
        $this->addDataColumn(Gpf_Db_Table_Logs::ID, Gpf_Db_Table_Logs::ID);
        $this->addDataColumn(Gpf_Db_Table_Logs::LINE, Gpf_Db_Table_Logs::LINE);
        $this->addDataColumn(Gpf_Db_Table_Logs::FILENAME, Gpf_Db_Table_Logs::FILENAME);
        $this->addDataColumn(Gpf_Db_Table_Logs::CREATED, Gpf_Db_Table_Logs::CREATED);
        $this->addDataColumn(Gpf_Db_Table_Logs::LEVEL, Gpf_Db_Table_Logs::LEVEL);
        $this->addDataColumn(Gpf_Db_Table_Logs::TYPE, Gpf_Db_Table_Logs::TYPE);
        $this->addDataColumn(Gpf_Db_Table_Logs::MESSAGE, Gpf_Db_Table_Logs::MESSAGE);
        $this->addDataColumn(Gpf_Db_Table_Logs::IP, Gpf_Db_Table_Logs::IP);
        $this->addDataColumn(Gpf_Db_Table_Logs::GROUP_ID, Gpf_Db_Table_Logs::GROUP_ID);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_Logs::ID, '40px', 'A');
        $this->addDefaultViewColumn(Gpf_Db_Table_Logs::CREATED, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Logs::LEVEL, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Logs::MESSAGE, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Logs::IP, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Logs::FILENAME, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Logs::LINE, '40px', 'N');
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        switch ($filter->getCode()) {
            case "search":
                $this->addSearch($filter);
                break;

        }
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Gpf_Db_Table_Logs::getName());
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $this->_selectBuilder->where->add('message', 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $this->_selectBuilder->where->add(Gpf_Db_Table_Logs::IP, 'LIKE', '%' . $filter->getValue() .'%', "OR");
        $this->_selectBuilder->where->add(Gpf_Db_Table_Logs::GROUP_ID, 'LIKE', '%' . $filter->getValue() .'%', "OR");
    }

    /**
     * @service log read
     * @param Gpf_Rpc_Params $params
     */
    public function getCustomFilterFields(Gpf_Rpc_Params $params) {
        $filterFields = new Gpf_View_CustomFilterFields();
        $filterFields->addNumberField(Gpf_Db_Table_Logs::ID, $this->_("Id"));
        $filterFields->addStringField(Gpf_Db_Table_Logs::MESSAGE, $this->_("Message"));
        $filterFields->addStringField(Gpf_Db_Table_Logs::IP, $this->_("IP"));
        $filterFields->addStringField(Gpf_Db_Table_Logs::GROUP_ID, $this->_("Group ID"));

        return $filterFields->getRecordSet();
    }

    protected function createCountSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $count = new Gpf_SqlBuilder_SelectBuilder();
        $count->select->add('count(*)', 'count');
        $count->from = $select->from;
        $count->where = $select->where;
        $count->groupBy = $select->groupBy;
        $count->having = $select->having;
        return $count;
    }

    /**
     * @service log read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service log export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
