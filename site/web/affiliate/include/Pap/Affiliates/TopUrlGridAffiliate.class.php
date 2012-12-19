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
class Pap_Affiliates_TopUrlGridAffiliate extends Pap_Common_TopUrlGridBase {
   
    protected function buildWhere() {
		parent::buildWhere();
		$this->_selectBuilder->where->add('r.'.Pap_Db_Table_Transactions::USER_ID, '=', Gpf_Session::getAuthUser()->getPapUserId());
    }
        
    /**
     * @service transaction read_own
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
    
    /**
     * @service transaction export_own
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
    

}
?>
