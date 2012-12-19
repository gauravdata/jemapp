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
class WorldPay_Tracker extends Pap_Tracking_CallbackTracker {

    private $description;

    private function getDescription() {
        return $this->description;
    }

    private function setDescription($desc) {
        $this->description = $desc;
    }

    /**
     *
     * @return Pap_Common_Transaction
     */
    protected function getParentTransaction($futurePayId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
         
        $select->select->addAll(Pap_Db_Table_Transactions::getInstance());
        $select->from->add(Pap_Db_Table_Transactions::getName());
        $select->where->add(Pap_Db_Table_Transactions::DATA5, "=", $futurePayId);

        $select->where->add(Pap_Db_Table_Transactions::R_TYPE, "IN", array(Pap_Common_Constants::TYPE_SALE, Pap_Common_Constants::TYPE_ACTION, Pap_Common_Constants::TYPE_LEAD));
        $select->where->add(Pap_Db_Table_Transactions::TIER, "=", "1");
         
        $select->limit->set(0, 1);
        $t = new Pap_Common_Transaction();
        $t->fillFromRecord($select->getOneRow());

        return $t;
    }

    protected function getTransactionObject($param) {
        return $this->getParentTransaction($this->parseFuturePayIdFromDescription($this->getDescription()));
    }

    /**
     * @return WorldPay_Tracker
     */
    public function getInstance() {
        $tracker = new WorldPay_Tracker();
        $tracker->setTrackerName("WorldPay");
        return $tracker;
    }
    
    private function parseFromDesc($regexp, $desc) {
    	$matches = array();
        $matchesNum = array();
        
    	preg_match($regexp, $desc, $matches);
        if (count($matches)==0) {
            return false;
        }
        preg_match('/[0-9]+$/', $matches[0], $matchesNum);
        if (count($matchesNum)!=0) {
            return $matchesNum[0];
        }
        return false;
    }
    
    protected function parsePaymentNumberFromDescription($desc) {
    	return $this->parseFromDesc('/Payment [0-9]+/i', $desc);    	
    }

    protected function parseFuturePayIdFromDescription($desc) {
    	return $this->parseFromDesc('/FuturePay agreement ID [0-9]+/i', $desc);        
    }

    public function isRecurring() {
        if (($this->getDescription()!='')&&(is_numeric($this->parseFuturePayIdFromDescription($this->getDescription())) !== false)&&($this->getCookie()=='')) {
            return true;
        }
        return false;
    }

    public function checkStatus() {
        if($_REQUEST['transStatus'] != 'Y') {
            $this->debug("transStatus != Y");
            return false;
        }

        return true;
    }

    public function getOrderID() {
        return $this->getTransactionID();
    }

    public function readRequestVariables() {
        $request = Pap_Contexts_Action::getContextInstance()->getRequestObject();

        // assign posted variables to local variables
        $this->setCookie(stripslashes($request->getRequestParameter('M_aid')));
        $this->setTotalCost($request->getRequestParameter('amount'));
        $this->setTransactionID($request->getRequestParameter('transId'));
        $this->setProductID($request->getRequestParameter('M_ProductID'));
        $this->setDescription($request->getRequestParameter('desc'));
        $this->setCurrency($request->getRequestParameter('authCurrency'));
        if ($this->parseFuturePayIdFromDescription($request->getRequestParameter('desc'))===false) {
            $this->setData5($request->getRequestParameter('futurePayId'));
        } else {
            $this->setData4($this->parseFuturePayIdFromDescription($request->getRequestParameter('desc')));
            $this->setData3($this->parsePaymentNumberFromDescription($request->getRequestParameter('desc')));
        }
    }
}
?>
