<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro plugins
 */
class StormPay_Tracker extends Pap_Tracking_CallbackTracker {
    
    /**
     * @return StormPay_Tracker
     */
	public function getInstance() {
 		$tracker = new StormPay_Tracker();
 		$tracker->setTrackerName("StormPay");
 		return $tracker;
 	}
 	
 	public function checkStatus() {
		if($_REQUEST['status'] != 'SUCCESS') {
 			$this->debug("status != SUCCESS");
 			return false;
		}
		
		return true;
 	}

 	public function readRequestVariables() {
 		$request = new Pap_Tracking_Request();
 		
 		// assign posted variables to local variables
        $this->setCookie(stripslashes($request->getRequestParameter('user1')));
        $this->setTotalCost($request->getRequestParameter('amount'));
        $this->setTransactionID($request->getRequestParameter('transaction_id'));
        $this->setProductID($request->getRequestParameter('transaction_ref'));
        $this->setSubscriptionID($request->getRequestParameter('subscription_id'));
 	}
 	
 	public function isRecurring() {
 		if(isset($_REQUEST['subscription']) && ($_REQUEST['subscription']=="YES" || $_REQUEST['subscription']=="Y")) {
 			return true;
 		} else {
 			return false;
 		}
 	}
 	 	
 	public function getOrderID() {
 		if($this->isRecurring()) {
 			return $this->getSubscriptionID();
 		} else {
			return $this->getTransactionID();
 		}
	}
}
?>
