<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro 
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
class Pap_Affiliates_Reports_PayoutsGrid extends Pap_Common_Reports_PayoutsGridBase {
    
    function __construct() {
        parent::__construct();
    }
    
    protected function initDefaultView() {
    	$this->addDefaultViewColumn("dateinserted", '', 'A');  
    	$this->addDefaultViewColumn("amount", '', 'A');  
    	$this->addDefaultViewColumn("affiliatenote", '', 'A');
    	$this->addActionView(); 
    }
        
    protected function buildWhere() {
        parent::buildFilter();
        $this->_selectBuilder->where->add("p.userid", "=", Gpf_Session::getAuthUser()->getPapUserId());
    }
    
    /**
     * @service payout read_own
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service payout export_own
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
}
?>
