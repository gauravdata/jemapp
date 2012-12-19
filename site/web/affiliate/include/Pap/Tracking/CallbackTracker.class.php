<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
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
class Pap_Tracking_CallbackTracker extends Gpf_Object {
    const ACCOUNT_ID_LENGTH = 8;

    private $_cookie = '';
    private $_totalCost = '';
    private $_productId = '';
    private $_transactionId = '';
    private $_subscriptionId = '';
    private $_status = '';
    private $_paymentStatus = '';
    private $_type = '';
    private $_email = '';
    private $_affiliateId = '';
    private $_campaignId = '';
    private $_trackerName = 'CallbackTracker';
    private $_data1;
    private $_data2;
    private $_data3;
    private $_data4;
    private $_data5;
    private $_couponCode = '';
    private $_currency = '';
    private $_accountId = '';
    private $_visitorId = '';
    private $_channelId = '';

    private $userFirstName = "";
    private $userLastName = "";
    private $userEmail = "";
    private $userNotificationEmail = "";
    private $userCity = "";
    private $userAddress = "";
    
    private $dateTime = '';

    /**
     * override this function to check if it is recurring payment or not
     *
     * @return boolean true/false
     */
    public function isRecurring() {
        return false;
    }

    /**
     * override this function to check if the status is correct or not
     *
     * @return boolean true/false
     */
    public function checkStatus() {
        return false;
    }

    /**
     * override this function to read request variables into internal format
     */
    public function readRequestVariables() {
    }

    /**
     * override this function to get Order ID
     */
    public function getOrderID() {
        return '';
    }
    
    /**
     * checks if cookie exists and if it is in correct format
     *
     * @return unknown
     */
    public function checkCookie() {
        return true;
    }

    public function process() {
        $this->debug("------------------- started -------------------");
        $this->readRequestVariables();
         
        if($this->checkCookie()) {
            $this->debug("Checking payment status");
            if($this->checkStatus()) {
                $this->debug("  Processing callback started");
                 
                if($this->isRecurring()) {
                    $this->processSubscriptionPayment();
                } else {
                    $this->registerCommission();
                }

                if ($this->isAffiliateRegisterAllowed()) {
                    $this->registerAffiliate();
                }
                $this->debug("  Processing callback ended");
            } else {
                $this->debug("Transaction was not in success state, stopping");
            }
        }

        $this->debug("------------------- ended -------------------");
    }

    /**
     * override this function to check if is turn on register affiliate
     *
     * @return boolean true/false
     */
    protected function isAffiliateRegisterAllowed() {
        return false;
    }

    protected function addField(Gpf_Data_RecordSet $fields, $name, $value) {
        $r1 = $fields->createRecord();
        $r1->set('name', $name);
        $r1->set('value', $value);
        $fields->addRecord($r1);
    }

    /**
     * @return Gpf_Rpc_Params
     */
    protected function createSignupParams() {
        $fields = new Gpf_Data_RecordSet();
        $fields->setHeader(new Gpf_Data_RecordHeader(array('name', 'value')));
        $this->fillSignupParams($fields);
        
        $params = new Gpf_Rpc_Params();
        $params->add('fields', $fields->toObject());
        
        return $params;
    }

    protected function fillSignupParams(Gpf_Data_RecordSet $fields) {
        $this->addField($fields, 'username', $this->getUserEmail());
        $this->addField($fields, 'Id', '');
        $this->addField($fields, 'data3', $this->getUserAddress());
        $this->addField($fields, 'data4', $this->getUserCity());
        for ($i=1; $i<=2; $i++) {
            $this->addField($fields, 'data' . $i, '');
        }
        for ($i=5; $i<=25; $i++) {
            $this->addField($fields, 'data' . $i, '');
        }
        $this->addField($fields, 'parentuserid', $this->getParentAffiliateId());
        $this->addField($fields, 'firstname', $this->getUserFirstName());
        $this->addField($fields, 'lastname', $this->getUserLastName());
        $this->addField($fields, 'refid', substr(md5($this->getUserEmail()),0,8));
        $this->addField($fields, 'agreeWithTerms', Gpf::YES);
        $this->addField($fields, 'visitorId', $this->getCookie());
        if ($this->getUserNotificationEmail() != "") {
            $this->addField($fields, 'notificationemail', $this->getUserNotificationEmail());
        }
    }

    protected function allRequireValues() {
        if (($this->getUserEmail() != "") and ($this->getUserFirstName() != "") and ($this->getUserLastName() != "")) {
            return true;
        }
        return false;
    }

    protected function registerAffiliate() {
        $this->debug("Start registering affiliate, params Name='".$this->getUserFirstName()."', Surname='".$this->getUserLastName()."', username='".$this->getUserEmail()."'");
        
        if ($this->allRequireValues()) {
            try {
                $signupForm = new Pap_Signup_AffiliateForm();
                $signupForm->add($this->createSignupParams());
                
                $this->debug("Affiliate created successfully");
            } catch (Gpf_Exception $e) {
                $this->debug("Error while saving affiliate: " . $e->getMessage());
            }
        } else {
            $this->debug("Some essential values for creating new affiliate account is missing, process stopped");
        }
        $this->debug("End registering affiliate");
    }

    protected function registerCommission() {
        $this->debug("Start registering sale, params TotalCost='".$this->getTotalCost().
                     "', OrderID='".$this->getOrderID().
                     "', ProductID='".$this->getProductID().
                     "', VisitorID='".$this->getCookie()."'");
            
        $saleTracker = new Pap_Tracking_ActionTracker();
        try {
            $this->prepareSales($saleTracker);
        } catch (Gpf_Exception $e) {
            $this->debug($e->getMessage());
            return;
        }
        $this->registerAllSales($saleTracker);
    }

    protected function prepareSales(Pap_Tracking_ActionTracker $saleTracker) {
    	$this->debug('Beginning preparing sales...');
        $sale = $saleTracker->createSale();
        $sale->setTotalCost($this->getTotalCost());
        $sale->setOrderID($this->getOrderID());
        $sale->setProductID($this->getProductID());
        $sale->setData1($this->_data1);
        $sale->setData2($this->_data2);
        $sale->setData3($this->_data3);
        $sale->setData4($this->_data4);
        $sale->setData5($this->_data5);
        $sale->setCoupon($this->_couponCode);
        $sale->setCurrency($this->_currency);
        $sale->setChannelId($this->_channelId);
        if ($this->dateTime != '') {
            $sale->setTimeStamp(Gpf_Common_DateUtils::getTimestamp($this->dateTime));
        }
        
        if ($this->getStatus()!='') {
            $sale->setStatus($this->getStatus());
        }
        if($this->getAffiliateID() != '' && $this->getCampaignID() != '') {
            $sale->setAffiliateID($this->getAffiliateID());
            $sale->setCampaignID($this->getCampaignID());
        }

        $this->setVisitorAndAccount($saleTracker, $this->getAffiliateID(), $this->getCampaignID(), $this->getCookie());
        $this->debug('Finish preparing sales...');
    }

    protected function setVisitorAndAccount(Pap_Tracking_ActionTracker $saleTracker, $affiliateID, $campaignID, $cookie) {
        if($affiliateID != '' && $campaignID != '') {
            try {
                $saleTracker->setAccountId($this->getAccountIdFromCampaign($campaignID));
            } catch (Gpf_Exception $e) {
                throw new Gpf_Exception('Can not get accountId from campaign, error message:' . $e->getMessage());
            }
        } else {
            if ($this->isOldCookies($cookie)) {
                $saleTracker->setCookieValue($cookie);
                $newVisitorId = $this->generateNewVisitorId();
                try {
                    $cookieObj = new Pap_Tracking_Cookie_Sale();
                    $cookieObj->decode($cookie);
                    $this->fillAccountIdAndVisitorId($saleTracker, $this->getAccountIdFromCampaign($cookieObj->getCampaignId()), $newVisitorId);
                } catch (Gpf_Exception $e) {
                    $this->debug('Can not get accountId from campaign in old cookie format, error message:' . $e->getMessage() . '. Setting default one.');
                    $this->fillAccountIdAndVisitorId($saleTracker, Gpf_Db_Account::DEFAULT_ACCOUNT_ID, $newVisitorId);
                }
            } else {
                $this->processAccountIdAndVisitorId($saleTracker, $cookie);
            }
        }
        $this->debug("End registering sale");
    }
    
    private function fillAccountIdAndVisitorId(Pap_Tracking_ActionTracker $saleTracker, $accountId, $visitorId) {
        $saleTracker->setAccountId($accountId);
        $saleTracker->setVisitorId($visitorId);
        
        $this->setAccountID($accountId);
        $this->setVisitorID($visitorId);
    }

    protected function parseCookie($cookieValue) {
        try {
            $customSeparator = Gpf_Settings::get(GoogleCheckout_Config::CUSTOM_SEPARATOR);
            if ($customSeparator != '') {
                $explodedCookieValue = explode($customSeparator, $cookieValue, 2);
                if (count($explodedCookieValue) == 2) {
                    return $explodedCookieValue[1];
                }
            }
        } catch (Gpf_Exception $e) {
        }
        return $cookieValue;
    }

    protected function allowUseRecurringCommissionSettings() {
        return false;
    }

    protected function registerAllSales(Pap_Tracking_ActionTracker $saleTracker) {
    	$this->debug('Registering all sales...');
        $saleTracker->register();
        $this->debug('All sales registered...');
    }

    protected function getAccountIdFromCampaign($campaignId) {
        $campaign = new Pap_Db_Campaign();
        $campaign->setId($campaignId);
        $campaign->load();
        return $campaign->getAccountId();
    }

    protected function processAccountIdAndVisitorId(Pap_Tracking_ActionTracker $saleTracker, $cookie) {
        if (strlen($cookie) == Pap_Tracking_Visit_Processor::getVisitorIdLength()) {
            $this->fillAccountIdAndVisitorId($saleTracker, Gpf_Db_Account::DEFAULT_ACCOUNT_ID, $cookie);
            $this->debug('VisitorId ' . $cookie . ' recognized, no accountId recognized, assigning to default.');
            return;
        }
        if (strlen($cookie) == Pap_Tracking_Visit_Processor::getVisitorIdLength() + self::ACCOUNT_ID_LENGTH) {
            $this->fillAccountIdAndVisitorId($saleTracker, substr($cookie, 0, self::ACCOUNT_ID_LENGTH), substr($cookie, self::ACCOUNT_ID_LENGTH, Pap_Tracking_Visit_Processor::getVisitorIdLength()));
            $this->debug('VisitorId ' . $saleTracker->getVisitorId() .
                         ' recognized and accountId ' . $saleTracker->getAccountId() . ' recognized.');
            return;
        }
        if (strlen($cookie) == self::ACCOUNT_ID_LENGTH) {
            $this->fillAccountIdAndVisitorId($saleTracker, $cookie, $this->generateNewVisitorId());
            $this->debug('AccountId ' . $saleTracker->getAccountId() . ' recognized, no visitorId recognized, generating new one.');
            return;
        }
        $this->debug("VisitorId has wrong size, generating new one.");
        $this->fillAccountIdAndVisitorId($saleTracker, Gpf_Db_Account::DEFAULT_ACCOUNT_ID, $this->generateNewVisitorId());
    }

    private function recognizeVisitorParams($cookie) {
        if (strlen($cookie) == Pap_Tracking_Visit_Processor::getVisitorIdLength() ) {
            $this->setVisitorParams($cookie, '', true);
            return;
        }
        if (strlen($cookie) == Pap_Tracking_Visit_Processor::getVisitorIdLength() + self::ACCOUNT_ID_LENGTH) {
            $this->setVisitorParams(substr($cookie, self::ACCOUNT_ID_LENGTH, Pap_Tracking_Visit_Processor::getVisitorIdLength()),
            substr($cookie, 0, self::ACCOUNT_ID_LENGTH), true);
            return;
        }
        if (strlen($cookie) == self::ACCOUNT_ID_LENGTH ) {
            $this->setVisitorParams($this->getVisitorId(), $cookie);
            return;
        }
        $this->setVisitorParams($this->getVisitorId());
    }

    protected function generateNewVisitorId() {
        return substr('caTrVi' . md5(uniqid(null, true)), 0, Pap_Tracking_Visit_Processor::getVisitorIdLength());
    }

    private function isOldCookies($cookies) {
        if (strpos($cookies, '{') !== false) {
            return true;
        }
        return false;
    }

    protected function processSubscriptionPayment() {
        $this->debug("Start registering recurring payment / subscription");

        $form = new Pap_Features_RecurringCommissions_RecurringCommissionsForm();
        try {
            $form->createCommissionsNoRpc($this->getSubscriptionID());
            $this->debug("Recurring commission processed.");
        } catch (Gpf_Exception $e) {
            $this->debug("Error occurred during launching recurring commission: " . $e->getMessage());
            if (!$this->allowUseRecurringCommissionSettings()) {
                $this->debug("Registering new recurring commission.");
                $this->findPaymentBySubscriptionID();
                $this->registerCommission();
            } else {
                $this->debug("New inicialize recurring commission was not created, enabled setting - save only matched commissions.");
            }
        }
        $this->debug("End registering recurring payment / subscription");
    }

    /**
     * @returns Pap_Common_Transaction
     */
    protected function getTransactionObject($orderId) {
        return Pap_Contexts_Action::getContextInstance()->getTransactionObject()->getFirstRecordWith(Pap_Db_Table_Transactions::ORDER_ID, $orderId,
        array(Pap_Common_Constants::STATUS_APPROVED, Pap_Common_Constants::STATUS_PENDING));
    }

    protected function checkTransactionObjectIsNull() {
        return (Pap_Contexts_Action::getContextInstance()->getTransactionObject() == null);
    }

    protected function findPaymentBySubscriptionID() {
        $subscrId = $this->getSubscriptionID();
        $this->debug("");
        $this->debug("Looking for transaction with order ID: '$subscrId'");
        try {
            if ($this->checkTransactionObjectIsNull()) {
                $this->debug("No transaction object set");
                return false;
            }
            $transObj = $this->getTransactionObject($subscrId);
            $this->setCampaignID($transObj->getCampaignId());
            $this->setAffiliateID($transObj->getUserId());

            $this->debug("Found transaction ID: '".$transObj->getId()."' for this order ID: '$subscrId'");
             
            return true;

        } catch(Gpf_DbEngine_NoRowException $e) {
            $this->debug("Transaction not found");
            return false;
        } catch(Exception $e) {
            $this->debug("Exception when looking for subscription record: ".$e->getMessage());
        }
         
        return false;
    }
    
    protected function computeAffiliateId($visitorId, $accountId) {
        $singleVisitorProcessor = new Pap_Tracking_Visit_SingleVisitorProcessor($visitorId, $accountId);
        $params = new Gpf_Rpc_Params();
        $params->add('visitorId', $visitorId);
        $params->add('accountId', $accountId);
        $affiliateData = $singleVisitorProcessor->getAffiliate($params);
        return $affiliateData->getValue('userid');
    }
    
    protected function getParentAffiliateId() {
        $this->debug("Start recognizing parent affiliate");
        if ($this->getAffiliateID() != '') {
            $parentid = $this->getAffiliateID();
            $this->debug("Parent found from affiliate param: " . $parentid);
            $this->debug("End recognizing parent affiliate");
            return $parentid;
        }
        $parentid = '';
        try {
            $parentid = $this->computeAffiliateId($this->getVisitorID(), $this->getAccountID());
            $this->debug("Parent found: " . $parentid);
        } catch (Gpf_Exception $e) {
            $this->debug("Can not load parent affiliate: " . $e->getMessage());
        }
        $this->debug("End recognizing parent affiliate");
        return $parentid;
    }

    public function setTrackerName($name) {
        $this->_trackerName = $name;
    }
    public function getTrackerName() {
        return $this->_trackerName;
    }

    public function debug($message) {
        if($message == "") {
            Pap_Contexts_Action::getContextInstance()->debug("");
        } else {
            Pap_Contexts_Action::getContextInstance()->debug($this->getTrackerName().': '.$message);
        }
    }

    public function setCurrency($value) {
        $this->_currency = $value;
    }

    public function getCurrency() {
        return $this->_currency;
    }

    public function setTotalCost($value) {
        $this->_totalCost = $value;
    }
    public function getTotalCost() {
        return $this->_totalCost;
    }

    public function setProductID($value) {
        $this->_productId = $value;
    }
    public function getProductID() {
        return $this->_productId;
    }

    public function setStatus($value) {
        $this->_status = $value;
    }
    public function getStatus() {
        return $this->_status;
    }

    public function setPaymentStatus($value) {
        $this->_paymentStatus = $value;
    }
    public function getPaymentStatus() {
        return $this->_paymentStatus;
    }

    public function setType($value) {
        $this->_type = $value;
    }
    public function getType() {
        return $this->_type;
    }

    public function setTransactionID($value) {
        $this->_transactionId = $value;
    }
    public function getTransactionID() {
        return $this->_transactionId;
    }

    public function setSubscriptionID($value) {
        $this->_subscriptionId = $value;
    }
    public function getSubscriptionID() {
        return $this->_subscriptionId;
    }

    public function setEmail($value) {
        $this->_email = $value;
    }
    public function getEmail() {
        return $this->_email;
    }

    public function setCookie($value) {
        $this->_cookie = $value;
    }
    public function getCookie() {
        return $this->_cookie;
    }

    public function setAffiliateID($value) {
        $this->_affiliateId = $value;
    }
    public function getAffiliateID() {
        return $this->_affiliateId;
    }
    
    public function setVisitorID($value) {
        $this->_visitorId = $value;
    }
    public function getVisitorID() {
        return $this->_visitorId;
    }
    
    public function setAccountID($value) {
        $this->_accountId = $value;
    }
    public function getAccountID() {
        return $this->_accountId;
    }

    public function setCampaignID($value) {
        $this->_campaignId = $value;
    }
    
    public function getCampaignID() {
        return $this->_campaignId;
    }
    
    public function getCouponCode() {
        return $this->_couponCode;
    }

    public function setData1($value) {
        $this->_data1 = $value;
    }

    public function setData2($value) {
        $this->_data2 = $value;
    }

    public function setData3($value) {
        $this->_data3 = $value;
    }

    public function setData4($value) {
        $this->_data4 = $value;
    }

    public function setData5($value) {
        $this->_data5 = $value;
    }

    public function getData1() {
        return $this->_data1;
    }

    public function getData2() {
        return $this->_data2;
    }

    public function getData3() {
        return $this->_data3;
    }

    public function getData4() {
        return $this->_data4;
    }

    public function getData5() {
        return $this->_data5;
    }

    public function getChannelId() {
        return $this->_channelId;
    }

    public function setChannelId($channelId) {
        $this->_channelId = $channelId;
    }

    public function setCoupon($couponCode) {
        $this->_couponCode = $couponCode;
    }

    public function setUserFirstName($value) {
        $this->userFirstName = $value;
    }

    public function setUserLastName($value) {
        $this->userLastName = $value;
    }

    public function setUserEmail($value) {
        $this->userEmail = $value;
    }

    public function setUserNotificationEmail($value) {
        $this->userNotificationEmail = $value;
    }
    
    public function setUserCity($value) {
        $this->userCity = $value;
    }

    public function setUserAddress($value) {
        $this->userAddress = $value;
    }
    
    public function getUserNotificationEmail() {
        return $this->userNotificationEmail;
    }

    public function getUserFirstName() {
        return $this->userFirstName;
    }

    public function getUserLastName() {
        return $this->userLastName;
    }

    public function getUserEmail() {
        return $this->userEmail;
    }

    public function getUserCity() {
        return $this->userCity;
    }

    public function getUserAddress() {
        return $this->userAddress;
    }
    
    public function setDateTime($dateTime) {
        $this->dateTime = $dateTime;
    }
}
?>
