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
 * @package PostAffiliate
 */
abstract class Pap_Contexts_Tracking extends Gpf_Plugins_Context {

    const ACCOUNT_RECOGNIZED_FROM_CAMPAIGN = 'C';
    const ACCOUNT_RECOGNIZED_FROM_FORCED_PARAMETER = 'F';
    const ACCOUNT_RECOGNIZED_DEFAULT = 'D';
    
    /**
     * @var Pap_Tracking_Request
     * @deprecated
     */
    private $requestObject;

    /**
     * @var Pap_Tracking_Response
     * @deprecated
     */
    private $responseObject;

    /**
     * @var Pap_Db_Visit
     */
    protected $visit;

    /**
     * @var Gpf_Log_Logger
     */
    private $_logger = null;

    /**
     * @var instance
     */
    static protected $instance = null;

    /**
     * @var Pap_Db_VisitorAffiliate
     */
    private $visitorAffiliate;

    /**
     * @var boolean
     */
    private $containsRequiredParameters, $doTrackerSave, $doCommissionsSave;

    private $countryCode = null;
    
    private $accountRecognizeMethod = null;
    
    private $manualAddMode = false;

    /**
     * constructs context instance
     * It creates debug logger if there are parameters for it
     *
     */
    protected function __construct() {
        Gpf_Session::getAuthUser()->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);

        $this->setActionType($this->getActionTypeConstant());

        $this->initDebugLogger();

        $this->setRequestObject( new Pap_Tracking_Request() );
        $this->setResponseObject( new Pap_Tracking_Response() );

        $cookieObj = new Pap_Tracking_Cookie();
        $cookieObj->setLogger($this->getLogger());
        $this->setCookieObject( $cookieObj );
    }

    /**
     * @param $rowWithIp Gpf_DbEngine_Row
     * @return String or null
     */
    protected function initCountryCode($rowWithIp) {
        if (is_null($this->countryCode) && !is_null($rowWithIp)) {
            $context = new Gpf_Data_Record(array(Pap_Db_Table_RawImpressions::IP, Pap_Db_Table_Impressions::COUNTRYCODE),
            array($rowWithIp->get('ip'), ''));
            Gpf_Plugins_Engine::extensionPoint('Tracker.request.getCountryCode', $context);
            $this->countryCode = $context->get(Pap_Db_Table_Impressions::COUNTRYCODE);
        }
        return $this->countryCode;
    }

    public function getReferrerUrl() {
        if ($this->visit == null) {
            return null;
        }

        return $this->visit->getReferrerUrl();
    }

    /**
     * @return Pap_Db_Visit
     */
    public function getVisit() {
        return $this->visit;
    }

    public function setVisit(Pap_Db_Visit $value) {
        $this->visit = $value;
    }
    
    private function setAccountRecognizeMethod($method) {
        $this->accountRecognizeMethod = $method;
    }
    
    public function getAccountRecognizeMethod() {
        return $this->accountRecognizeMethod;
    }

    public function getRealTotalCost() {
        return 0;
    }

    public function getFixedCost() {
        return 0;
    }

    public function isManualAddMode() {
        return $this->manualAddMode;
    }

    public function setManualAddMode($isManualAddMode) {
        $this->manualAddMode = $isManualAddMode;
    }

    /**
     * override this function and return the correct
     * Pap_Common_Constants::TYPE_XXXX type of your transaction to enable logging
     */
    protected function getActionTypeConstant() {
        return '';
    }

    protected function initDebugLogger() {
        $logger = Pap_Logger::create($this->getActionTypeConstant());
        if($logger != null) {
            $this->setLogger($logger);
        }
    }

    /**
     * @throws Gpf_Exception
     * @return Pap_Db_VisitorAffiliate
     */
    public function getVisitorAffiliate() {
        if ($this->isVisitorAffiliateRecognized()) {
            return $this->visitorAffiliate;
        }
        throw new Gpf_Exception('Visitor affiliate not recognized');
    }

    public function setVisitorAffiliate(Pap_Db_VisitorAffiliate $value) {
        $this->visitorAffiliate = $value;
    }

    public function isVisitorAffiliateRecognized() {
        return $this->visitorAffiliate != null;
    }


    /**
     * returns true if request contains all required parameters
     * @return boolean
     */
    public function getContainsRequiredParameters() {
        return $this->containsRequiredParameters;
    }

    /**
     * sets if request contains all required parameters
     * @param boolean $value
     */
    public function setContainsRequiredParameters($value) {
        $this->containsRequiredParameters = $value;
    }

    /**
     * returns true if transaction should be saved by Tracker
     * @return boolean
     */
    public function getDoTrackerSave() {
        return $this->doTrackerSave;
    }

    /**
     * sets if transaction should be saved by Tracker
     * @param boolean $value
     */
    public function setDoTrackerSave($value) {
        $this->doTrackerSave = $value;
    }

    /**
     * returns true if commission should be saved by Tracker
     * @return boolean
     */
    public function getDoCommissionsSave() {
        return $this->doCommissionsSave;
    }

    /**
     * sets if commission should be saved by Tracker
     * @param boolean $value
     */
    public function setDoCommissionsSave($value) {
        $this->doCommissionsSave = $value;
    }

    /**
     * gets request object (instance of Pap_Tracking_Request)
     * @return Pap_Tracking_Request
     * @deprecated
     */
    public function getRequestObject() {
        return $this->requestObject;
    }

    /**
     * sets request object (instance of Pap_Tracking_Request)
     */
    public function setRequestObject(Pap_Tracking_Request $value) {
        $this->requestObject = $value;
    }

    /**
     * gets response object (instance of Pap_Tracking_Response)
     * @return Pap_Tracking_Response
     */
    public function getResponseObject() {
        return $this->responseObject;
    }

    /**
     * sets response object (instance of Pap_Tracking_Response)
     */
    public function setResponseObject(Pap_Tracking_Response $value) {
        $this->responseObject = $value;
    }

    /**
     * gets user object (instance of Pap_Common_User)
     * @return Pap_Common_User
     */
    public function getUserObject() {
        return $this->get("userObject");
    }

    /**
     * sets user object (instance of Pap_Common_User)
     */
    public function setUserObject(Pap_Common_User $value = null) {
        $this->set("userObject", $value);
    }

    /**
     * @return Pap_Common_Banner
     */
    public function getBannerObject() {
        return $this->get("bannerObject");
    }

    /**
     * sets banner object (instance of Pap_Common_Banner)
     */
    public function setBannerObject($value) {
        $this->set("bannerObject", $value);
    }

    /**
     * gets cookie object (instance of Pap_Tracking_Cookie)
     * @return Pap_Tracking_Cookie
     */
    public function getCookieObject() {
        return $this->get("cookieObject");
    }

    /**
     * sets cookie object (instance of Pap_Tracking_Cookie)
     */
    public function setCookieObject(Pap_Tracking_Cookie $value) {
        $this->set("cookieObject", $value);
    }

    /**
     * gets campaign object (instance of Pap_Common_Campaign)
     * @return Pap_Common_Campaign
     */
    public function getCampaignObject() {
        return $this->get("campaignObject");
    }
    /**
     * gets commission group object (instance of Pap_Db_CommissionGroup)
     * @return Pap_Db_CommissionGroup
     */
    public function getCommissionGroup() {
        return $this->get('commissionGroup');
    }

    /**
     * sets campaign object (instance of Pap_Common_Campaign)
     */
    public function setCampaignObject(Pap_Common_Campaign $value = null) {
        $this->set("campaignObject", $value);
    }

    /**
     * sets commission group object (instance of Pap_Db_CommissionGroup)
     */
    public function setCommissionGroup($commGroup) {
        $this->set('commissionGroup', $commGroup);
    }

    /**
     * gets channel object (instance of Pap_Db_Channel)
     * @return Pap_Db_Channel
     */
    public function getChannelObject() {
        return $this->get("channelObject");
    }

    /**
     * sets channel object (instance of Pap_Db_Channel)
     */
    public function setChannelObject(Pap_Db_Channel $value = null) {
        $this->set("channelObject", $value);
    }

    /**
     * gets commission type object (instance of Pap_Db_CommissionType)
     * @return Pap_Db_CommissionType
     */
    public function getCommissionTypeObject() {
        return $this->get("commissionTypeObject");
    }

    /**
     * sets commission type object (instance of Pap_Db_CommissionType)
     */
    public function setCommissionTypeObject(Pap_Db_CommissionType $value) {
        $this->set("commissionTypeObject", $value);
    }

    /**
     * gets currency object (instance of Gpf_Db_Currency)
     * @return Gpf_Db_Currency
     */
    public function getDefaultCurrencyObject() {
        return $this->get("defaultCurrencyObject");
    }

    /**
     * gets currency object (instance of Gpf_Db_Currency)
     * @return Pap_Db_CommissionType
     */
    public function setDefaultCurrencyObject(Gpf_Db_Currency $value) {
        $this->set("defaultCurrencyObject", $value);
    }

    /**
     * @var array<Pap_Common_Transaction>
     */
    protected $transactions = array();

    /**
     * @return Pap_Common_Transaction
     */
    public function getTransaction($tier = 1) {
        return $this->getTransactionObject($tier);
    }

    /**
     * @return Pap_Common_Transaction
     */
    public function getTransactionObject($tier = 1) {
        if (array_key_exists($tier, $this->transactions)) {
            return $this->transactions[$tier];
        }
        return null;
    }

    public function setTransactionObject(Pap_Common_Transaction $transaction, $tier = 1) {
        $this->transactions[$tier] = $transaction;
    }

    /**
     * @var array <Pap_Tracking_Common_Commission>
     * first index: commission subtype
     * second index: commission tier
     */
    private $commissions = array();

    /**
     * add commission
     */
    public function addCommission(Pap_Tracking_Common_Commission $commission) {
        $this->commissions[$commission->getSubType()][$commission->getTier()] = $commission;
    }

    /**
     * remove commission
     */
    public function removeCommission($tier, $subtype = Pap_Db_Table_Commissions::SUBTYPE_NORMAL) {
        if (array_key_exists($subtype, $this->commissions) &&
        array_key_exists($tier, $this->commissions[$subtype])) {
            unset($this->commissions[$subtype][$tier]);
        }
    }

    /**
     * gets commissions for given tier
     *
     * @param int $tier
     * @return Pap_Tracking_Common_Commission
     */
    public function getCommission($tier, $subtype = Pap_Db_Table_Commissions::SUBTYPE_NORMAL) {
        if (array_key_exists($subtype, $this->commissions) &&
        array_key_exists($tier, $this->commissions[$subtype])) {
            return $this->commissions[$subtype][$tier];
        }
        return null;
    }

    public function setStatusForAllCommissions($status) {
        foreach ($this->commissions as $subTypeCommissions) {
            foreach ($subTypeCommissions as $commission) {
                $commission->setStatus($status);
            }
        }
    }

    /**
     * gets action type
     * @return string
     */
    public function getActionType() {
        return $this->get("actionType");
    }

    /**
     * sets action type
     */
    public function setActionType($value) {
        $this->set("actionType", $value);
    }

    public function getVisitorId() {
        return $this->get("visitorId");
    }

    public function setVisitorId($value) {
        $this->set("visitorId", $value);
    }

    public function getDateCreated() {
        return $this->get("dateCreated");
    }

    public function setDateCreated($value) {
        $this->set("dateCreated", $value);
    }

    /**
     * @return string datetime in standard format
     */
    public function getVisitDateTime() {
        if (($visit = $this->getVisit()) != null) {
            return $visit->getDateVisit();
        }
        if (!is_null($this->getDateCreated()) && $this->getDateCreated() !== '') {
            return $this->getDateCreated();
        }
        return Gpf_Common_DateUtils::now();
    }

    public function getCountryCode() {
    	if ($this->getVisit() == null) {
    		return '';
    	}
        return ($this->getVisit()->getCountryCode());
    }

    public function getAccountId() {
        return $this->get("accountId");
    }

    public function setAccountId($value, $method) {
        $this->set("accountId", $value);
        $this->accountRecognizeMethod = $method;
    }
}
?>
