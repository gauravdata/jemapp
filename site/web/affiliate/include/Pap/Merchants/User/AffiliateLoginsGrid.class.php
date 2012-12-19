<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Merchants_User_AffiliateLoginsGrid extends Gpf_View_GridService {
   protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_LoginsHistory::LOGIN, $this->_("Logged in"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, $this->_("Last Request"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Gpf_Db_Table_LoginsHistory::LOGOUT, $this->_("Logged out"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Gpf_Db_Table_LoginsHistory::IP, $this->_("IP"), true, Gpf_View_ViewColumn::TYPE_IP);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('l.'.Gpf_Db_Table_LoginsHistory::ID);
        $this->addDataColumn(Gpf_Db_Table_AuthUsers::USERNAME, 'au.'.Gpf_Db_Table_AuthUsers::USERNAME);
        $this->addDataColumn(Gpf_Db_Table_LoginsHistory::LOGIN, 'l.'.Gpf_Db_Table_LoginsHistory::LOGIN);
        $this->addDataColumn(Gpf_Db_Table_LoginsHistory::LOGOUT, 'l.'.Gpf_Db_Table_LoginsHistory::LOGOUT);
        $this->addDataColumn(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, 'l.'.Gpf_Db_Table_LoginsHistory::LAST_REQUEST);
        $this->addDataColumn(Gpf_Db_Table_LoginsHistory::IP, 'l.'.Gpf_Db_Table_LoginsHistory::IP);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_LoginsHistory::IP, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_LoginsHistory::LOGIN, '40px', 'D');
        $this->addDefaultViewColumn(Gpf_Db_Table_LoginsHistory::LOGOUT, '40px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, '40px', 'N');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Gpf_Db_Table_LoginsHistory::getName(), 'l');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'u', 'l.accountuserid=u.accountuserid');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au', 'u.authid=au.authid');
    }

    /**
     * @service online_user read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service online_user export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
