<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
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
class Pap_Contexts_Action extends Pap_Contexts_Tracking {

    /**
     * @var Pap_Tracking_Action_RequestActionObject
     */
    private $requestActionObject;

    public function __construct(Pap_Tracking_Action_RequestActionObject $requestActionObject = null, Pap_Db_Visit $visit = null) {
        if ($requestActionObject == null) {
            $requestActionObject = new Pap_Tracking_Action_RequestActionObject();
        }
        $this->requestActionObject = $requestActionObject;

        $this->visit = $visit;
        if ($visit != null) {
            $this->setVisitorId($visit->getVisitorId());
        }

        $this->setTransactionObject(new Pap_Common_Transaction());
        parent::__construct();
    }

    function __clone() {
        $transaction = clone $this->transactions[1];
        $this->transactions = array();
        $this->transactions[1] = $transaction;
    }

    /**
     * @return Pap_Contexts_Action
     */
    public static function getContextInstance() {
        if (self::$instance == null) {
            self::$instance = new Pap_Contexts_Action();
        }
        return self::$instance;
    }

    /**
     * @return Pap_Tracking_Action_RequestActionObject
     */
    public function getRequestActionObject() {
        return $this->getRequestActionObject();
    }

    protected function getActionTypeConstant() {
        return Pap_Common_Constants::TYPE_ACTION;
    }

    /**
     * gets client tracking method used
     * @return string
     */

    public function getClientTrackingMethod() {
        return $this->get("clientTrackingMethod");
    }

    /**
     * sets client tracking method used
     */
    public function setClientTrackingMethod($value) {
        $this->set("clientTrackingMethod", $value);
    }

    /**
     * gets tracking method used
     * @return string
     */
    public function getTrackingMethod() {
        return $this->get("realTrackingMethod");
    }

    /**
     * sets tracking method used
     */
    public function setTrackingMethod($value) {
        $this->set("realTrackingMethod", $value);
    }
    
    public function isTrackingMethodSet() {
        return $this->getTrackingMethod() != '';
    }

    /**
     * sets fixed cost
     */
    public function setFixedCost($value) {
        $this->requestActionObject->setFixedCost($value);
    }

    /**
     * gets fixed cost
     */
    public function getFixedCost() {
        return $this->requestActionObject->getFixedCost();
    }

    /**
     * gets ID of parent transaction
     * @return string
     */
    public function getParentTransactionId() {
        return $this->get("parentTransactionId");
    }

    /**
     * sets ID of parent transaction
     */
    public function setParentTransactionId($value) {
        $this->set("parentTransactionId", $value);
    }

    /**
     * gets fraud protection status
     * @return string
     */
    public function getFraudProtectionStatus() {
        return $this->get("fraudProtectionStatus");
    }

    /**
     * sets fraud protection status
     */
    public function setFraudProtectionStatus($value) {
        $this->set("fraudProtectionStatus", $value);
    }

    public function getAffiliateIdFromRequest() {
        return $this->requestActionObject->getAffiliateId();
    }

    public function getCouponFromRequest() {
        return $this->requestActionObject->getCouponCode();
    }

    public function getCampaignIdFromRequest() {
        return $this->requestActionObject->getCampaignId();
    }
    
    public function getBannerIdFromRequest() {
        return $this->requestActionObject->getBannerId();
    }

    public function getChannelIdFromRequest() {
        return $this->requestActionObject->getChannelId();
    }

    public function getProductIdFromRequest() {
        return $this->requestActionObject->getProductId();
    }

    public function getOrderIdFromRequest() {
        return $this->requestActionObject->getOrderId();
    }

    /**
     * gets real total cost
     */
    public function getRealTotalCost() {
        return $this->get("realTotalCost");
    }

    /**
     * sets real total cost
     */
    public function setRealTotalCost($value) {
        $this->set("realTotalCost", $value);
    }

    public function getTotalCostFromRequest() {
        return $this->requestActionObject->getTotalCost();
    }

    public function setTotalCost($value) {
        $this->requestActionObject->setTotalCost($value);
    }

    public function getExtraDataFromRequest($i) {
        return $this->requestActionObject->getData($i);
    }

    public function getActionCodeFromRequest() {
        return $this->requestActionObject->getActionCode();
    }

    public function getCurrencyFromRequest() {
        return $this->requestActionObject->getCurrency();
    }

    public function getCustomCommissionFromRequest() {
        return $this->requestActionObject->getCustomCommission();
    }

    public function getFixedCostFromRequest() {
        return $this->requestActionObject->getFixedCost();
    }

    public function getCustomStatusFromRequest() {
        return $this->requestActionObject->getStatus();
    }

    private $channelIdFromIp;

    public function setChannelIdByIp($id) {
        $this->channelIdFromIp = $id;
    }

    public function getChannelIdByIp() {
        return $this->channelIdFromIp;
    }

    public function getCustomTimeStampFromRequest() {
        return $this->requestActionObject->getTimeStamp();
    }

    /**
     * @return string datetime in standard format
     */
    public function getVisitDateTime() {
        if (strlen($timeStamp = $this->getCustomTimeStampFromRequest())) {
            return Gpf_Common_DateUtils::getDateTime($timeStamp);
        }
        return parent::getVisitDateTime();
    }

    public function getIp() {
        if ($this->getVisit() == null) {
            return null;
        }
        return $this->getVisit()->getIp();
    }
}
?>
