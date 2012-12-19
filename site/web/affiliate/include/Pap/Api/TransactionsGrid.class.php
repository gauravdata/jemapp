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

class Pap_Api_TransactionsGrid extends Gpf_Rpc_GridRequest {
	
    const REFUND_MERCHANT_NOTE = 'merchant_note';
    const REFUND_TYPE = 'status';
    const REFUND_FEE = 'fee';
    const TYPE_REFUND = 'R';
    const TYPE_CHARGEBACK = 'H';

	private $dataValues = null;
	
    public function __construct(Gpf_Api_Session $session) {
    	if($session->getRoleType() == Gpf_Api_Session::AFFILIATE) {
    		$className = "Pap_Affiliates_Reports_TransactionsGrid";
    	} else {
    		$className = "Pap_Merchants_Transaction_TransactionsGrid";
    	}
    	parent::__construct($className, "getRows", $session);
    }

    public function refund($note = '', $fee = 0) {
        return $this->makeRefundChargeback(self::TYPE_REFUND, $note, $fee);
    }

    public function chargeback($note = '', $fee = 0) {
        return $this->makeRefundChargeback(self::TYPE_CHARGEBACK, $note, $fee);
    }

    private function makeRefundChargeback($type, $note, $fee) {        
        if ($this->apiSessionObject->getRoleType() == Gpf_Api_Session::AFFILIATE) {
            throw new Exception("This method can be used only by merchant!"); 
        }
        if ($this->getFiltersParameter()->getCount() == 0) {
            throw new Exception("Refund / Chargeback in transactions grid is possible to make only with filters!");
        }

        $request = new Gpf_Rpc_ActionRequest('Pap_Merchants_Transaction_TransactionsForm', 'makeRefundChargebackByParams', $this->apiSessionObject);
        $request->addParam('filters', $this->getFiltersParameter());
        $request->addParam(self::REFUND_MERCHANT_NOTE, $note);
        $request->addParam(self::REFUND_TYPE, $type);
        $request->addParam(self::REFUND_FEE, $fee);

        $request->sendNow();

        return $request->getAction();
    }
}
?>
