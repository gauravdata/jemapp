<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro 
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 23478 2009-02-12 14:48:17Z aharsani $
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
class Pap_Merchants_TopUrlGrid extends Pap_Common_TopUrlGridBase {

    protected function initDataColumns() {
        parent::initDataColumns();
        $this->addDataColumn('userid', 'r.userid');
        $this->addDataColumn('accountid', 'c.accountid');
    }
    
    protected function buildFrom() {
    	parent::buildFrom();
    	$this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'c', 
    	'c.'.Pap_Db_Table_Campaigns::ID.'=r.'.Pap_Db_Table_Transactions::CAMPAIGN_ID);
    }
    
    protected function buildWhere() {
    	parent::buildWhere();    	
    	Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyWhere', 
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder, new Gpf_Data_Record(array('columnPrefix'), array('c'))));
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
}
?>
