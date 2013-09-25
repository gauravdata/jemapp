<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.6
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
class Gpf_Newsletter_NewslettersGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_Newsletters::ID, $this->_("Id"), true);
        $this->addViewColumn(Gpf_Db_Table_Newsletters::NAME, $this->_("Name"), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Gpf_Db_Table_Newsletters::ID);
        $this->addDataColumn(Gpf_Db_Table_Newsletters::ID, Gpf_Db_Table_Newsletters::ID);
        $this->addDataColumn(Gpf_Db_Table_Newsletters::NAME, Gpf_Db_Table_Newsletters::NAME);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_Newsletters::ID, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_Newsletters::NAME, '200px', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '40px', 'N');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Gpf_Db_Table_Newsletters::getName());
    }

    /**
     * @service newsletter read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
     
    /**
     * @service newsletter export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
