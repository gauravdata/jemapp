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
class ccBill_Tracker extends Pap_Tracking_CallbackTracker {
    /**
     * @return ccBill_Tracker
     */
    public function getInstance() {
        $tracker = new ccBill_Tracker();
        $tracker->setTrackerName("ccBill");
        return $tracker;
    }

    public function process() {
        $this->debug("------------------- started -------------------");

        $this->readRequestVariables();

        $this->registerCommission();

        if ($this->isAffiliateRegisterAllowed()) {
            $this->registerAffiliate();
        }

        $this->debug("------------------- ended -------------------");
    }

    public function readRequestVariables() {
        $request = new Pap_Tracking_Request();

        // assign posted variables to local variables
        $this->setTotalCost(str_replace(',', '.', $request->getRequestParameter('initialPrice')));
        $this->setTransactionID($request->getRequestParameter('subscription_id'));
        $this->setProductID($request->getRequestParameter('ProductID'));
        $this->setSubscriptionID($request->getRequestParameter('subscription_id'));
        $this->setCookie($request->getRequestParameter('PAP_COOKIE'));
        
        $this->readRequestAffiliateVariables($request);
    }

    public function readRequestAffiliateVariables(Pap_Tracking_Request $request) {
        $this->setUserFirstName($request->getRequestParameter('customer_fname'));
        $this->setUserLastName($request->getRequestParameter('customer_lname'));
        $this->setUserEmail($request->getRequestParameter('email'));
        $this->setUserCity($request->getRequestParameter('city'));
        $this->setUserAddress($request->getRequestParameter('address1'));
    }

    protected function registerCommission() {
        $this->debug("Start registering sale, params PAP_COOKIE='".$this->getCookie()."', TotalCost='".$this->getTotalCost()."', OrderID='".$this->getOrderID()."', ProductID='".$this->getProductID()."'");

        $saleTracker = Pap_Tracking_ActionTracker::getInstance();
        $sale = $saleTracker->createSale();
        $sale->setTotalCost($this->getTotalCost());
        $sale->setOrderID($this->getOrderID());
        $sale->setProductID($this->getProductID());

        $saleTracker->setVisitorId($this->getCookie());
        $saleTracker->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
        $saleTracker->register();

        $this->debug("End registering sale");
    }

    protected function isAffiliateRegisterAllowed() {
        return (Gpf_Settings::get(ccBill_Config::REGISTER_AFFILIATE) == Gpf::YES);
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
