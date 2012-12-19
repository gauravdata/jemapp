<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: ChannelsGrid.class.php 18754 2008-06-24 09:57:27Z mfric $
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
class Pap_Affiliates_Promo_ChannelsGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn(Pap_Db_Table_Channels::ID, $this->_("ID"), true);
        $this->addViewColumn(Pap_Db_Table_Channels::NAME, $this->_("Channel"), true);
        $this->addViewColumn(Pap_Db_Table_Channels::VALUE, $this->_("Code"), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Pap_Db_Table_Channels::ID);
        $this->addDataColumn(Pap_Db_Table_Channels::USER_ID);
        $this->addDataColumn(Pap_Db_Table_Channels::NAME);
        $this->addDataColumn(Pap_Db_Table_Channels::VALUE);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Pap_Db_Table_Channels::VALUE, '100', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Channels::NAME, '400', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '40', 'N');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_Channels::getName());
    }

    protected function buildWhere() {
        $this->_selectBuilder->where->add(Pap_Db_Table_Channels::USER_ID, '=', Gpf_Session::getAuthUser()->getPapUserId());
        parent::buildWhere();
    }

    /**
     * @return Gpf_DbEngine_Row
     */
    protected function createEmptyRow(Gpf_Rpc_Params $params) {
        $row = new Pap_Db_Channel();

        $userId = Gpf_Session::getAuthUser()->getPapUserId();

        $row->set(Pap_Db_Table_Channels::USER_ID, $userId);
        $row->set(Pap_Db_Table_Channels::NAME, $this->_("Channel name"));
        $row->set(Pap_Db_Table_Channels::VALUE, $this->generateDefaultValue($userId));
        return $row;
    }

    private function generateDefaultValue($userId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('MAX(CAST(SUBSTR('.Pap_Db_Table_Channels::VALUE.', 5) AS UNSIGNED))', 'max');
        $select->from->add(Pap_Db_Table_Channels::getName());
        $select->where->add(Pap_Db_Table_Channels::VALUE, 'like', 'code%');
        $select->where->add(Pap_Db_Table_Channels::USER_ID, '=', $userId);
        try {
            $result = $select->getOneRow();
            $max = $result->get('max')+1;
        } catch (Gpf_DbEngine_NoRowException $e) {
            $max = 1;
        } catch (Gpf_DbEngine_TooManyRowsException $e) {
            Gpf_Log::error('Database not in consistent state. Error in qu_pap_channels.');
        }
        if (isset($max)) {
            return 'code'.$max;
        }
        return 'code';
    }

    /**
     * @service channel read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service channel add
     * @return Gpf_Rpc_Serializable
     */
    public function getRowsAddNew(Gpf_Rpc_Params $params) {
        return parent::getRowsAddNew($params);
    }

    /**
     * @service channel export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
