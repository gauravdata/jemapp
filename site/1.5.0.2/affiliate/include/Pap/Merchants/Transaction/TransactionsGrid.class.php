<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: TransactionsGrid.class.php 32844 2011-05-25 10:21:47Z mkendera $
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
class Pap_Merchants_Transaction_TransactionsGrid extends Pap_Common_Reports_TransactionsGridBase {
    
    function __construct() {
        parent::__construct();
    }
    
    protected function initViewColumns() {
    	parent::initViewColumns();
    	$this->addViewColumn(Pap_Db_Table_Transactions::LOGGROUPID, $this->_('Log group'));
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);

        Gpf_Plugins_Engine::extensionPoint('TransactionsGrid.initViewColumns', $this);
    }
    
    protected function initDataColumns() {
    	parent::initDataColumns();
    	$this->addDataColumn(Pap_Db_Table_Transactions::LOGGROUPID, 't.'.Pap_Db_Table_Transactions::LOGGROUPID);
    	$this->addDataColumn('accountid', 't.'.Pap_Db_Table_Campaigns::ACCOUNTID);

    	Gpf_Plugins_Engine::extensionPoint('TransactionsGrid.initDataColumns', $this);
    }
    
    protected function initDefaultView() {
        parent::initDefaultView();
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::TRANSACTION_ID, '', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '', 'N');
    }
    
    protected function buildWhere() {
    	parent::buildWhere();
    	Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyWhere',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder, new Gpf_Data_Record(array('columnPrefix'), array('t'))));
    }
    
    protected function buildOrder() {
        parent::buildOrder();
    	if($this->_sortColumn == Pap_Db_Table_Transactions::R_TYPE) {
            if (array_key_exists(Pap_Db_Table_Transactions::TIER, $this->dataColumns)) {
                $this->_selectBuilder->orderBy->add(Pap_Db_Table_Transactions::TIER, $this->_sortAsc);
            }
        }
    }

    protected function initSearchAffiliateCondition(Gpf_SqlBuilder_Filter $filter) {
        parent::initSearchAffiliateCondition($filter);

        Gpf_Plugins_Engine::extensionPoint('TransactionsGrid.initSearchAffiliateCondition', $this);
    }

    public function clearSearchAffiliateCondition() {
        $this->_affiliateCondition = null;
    }

    /**
     * @service transaction read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    /**
     * @service transaction export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
    
    /**
     * @service transaction read
     * @return Gpf_Rpc_Serializable
     */
    public function getRowCount(Gpf_Rpc_Params $params) {
        return parent::getRowCount($params);
    }

    public function removeViewColumn($id) {
        unset($this->viewColumns[$id]);
    }

    public function removeDataColumn($id) {
        unset($this->dataColumns[$id]);
    }
}
?>
