<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Affiliate.class.php 22593 2008-12-01 12:56:47Z mbebjak $
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

class Pap_Api_PayoutsHistoryGrid extends Gpf_Rpc_GridRequest {
    public function __construct(Gpf_Api_Session $session) {
    	if($session->getRoleType() == Gpf_Api_Session::AFFILIATE) {
            throw new Gpf_Exception('Only merchant can view payouts history. Please login as merchant.');
        }
        parent::__construct('Pap_Merchants_Payout_PayoutsHistoryGrid', 'getRows', $session);
    }
    
    public function getPayeesDeatilsInfo($payoutId) {
        $this->checkMerchantRole();
        $request = new Gpf_Rpc_DataRequest('Pap_Merchants_Payout_PayoutsHistoryGrid', 'payeesDetails', $this->apiSessionObject);
        $request->addFilter('id', 'E', $payoutId);
        $request->sendNow();
        $results = $request->getData();
        
        $output = array();
        
        for ($i=0; $i<$results->getSize(); $i++) {
            $userinfo = $results->getValue('user' . $i);
            $data = new Gpf_Rpc_Data();
            $data->loadFromObject($userinfo);
            $output[] = $data;
        }
        return $output;
    }
    
    private function checkMerchantRole() {
        if($this->apiSessionObject->getRoleType() == Gpf_Api_Session::AFFILIATE) {
            throw new Gpf_Exception('Only merchant is allowed to to view payee details.');
        }
        return true;
    }
}
?>
