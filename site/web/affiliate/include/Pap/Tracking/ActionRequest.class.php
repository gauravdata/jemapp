<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Michal Bebjak
*   @since Version 1.0.0
*   $Id: ActionRequest.class.php 33104 2011-06-07 11:53:09Z mkendera $
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

class Pap_Tracking_ActionRequest extends Pap_Tracking_Request {
    const PARAM_ACTION_ACCOUNTID = 'AccountId';
    const PARAM_ACTION_ACTIONCODE = "ActionCode";
    const PARAM_ACTION_TOTALCOST = 'TotalCost';
    const PARAM_ACTION_FIXEDCOST = 'FixedCost';
    const PARAM_ACTION_ORDERID = 'OrderID';
    const PARAM_ACTION_PRODUCTID = 'ProductID';
    const PARAM_ACTION_ACTIONTYPE = 'ActionType';
    const PARAM_ACTION_AFFILIATEID = 'AffiliateID';
    const PARAM_ACTION_COOKIEVALUE = 'CookieValue';
    const PARAM_ACTION_CHANNELID = 'ChannelID';
    const PARAM_ACTION_CAMPAIGNID = 'CampaignID';
    const PARAM_ACTION_CURRENCY = 'Currency';
    const PARAM_ACTION_CUSTOM_COMMISSION = 'Commission';
    const PARAM_ACTION_CUSTOM_STATUS = 'PStatus';
    const PARAM_ACTION_TRACKING_METHOD = 'ptm';
    const PARAM_ACTION_CLIENT_SALE_COOKIE = 'fsc';
    const PARAM_ACTION_CLIENT_FIRST_CLICK = 'ffcc';
    const PARAM_ACTION_CLIENT_LAST_CLICK = 'flcc'; 
    const PARAM_ACTION_CUSTOM_TIMESTAMP = 'TimeStamp';
    const PARAM_ACTION_COUPON = 'Coupon';
    const PARAM_ACTION_VISITORID = 'visitorId';
    
    /**
     * @var Pap_Tracking_Cookie
     */
    private $cookie;
    
    function __construct() {
        parent::__construct();
        $this->cookie = new Pap_Tracking_Cookie();
    }
    
    public function getCookieValue() {
        return $this->getRequestParameter(self::PARAM_ACTION_COOKIEVALUE);
    }
    
    /**
     * gets action type (sale/lead) from parameter
     * @return string
     */
    public function getActionType() {
        if(isset($_REQUEST[self::PARAM_ACTION_ACTIONTYPE])) {
            if(in_array($_REQUEST[self::PARAM_ACTION_ACTIONTYPE], array('lead', 'sale')) ) {
                return $_REQUEST[self::PARAM_ACTION_ACTIONTYPE];
            }
        } 
        return '';
    }
    
    private function percentageTranslate($value) {
    	return str_replace('%25','%',$value);
    }

    public function getRawOrderId() {
        return $this->getRequestParameter(self::PARAM_ACTION_ORDERID);
    }
    
    public function setRawOrderId($value) {
        $_REQUEST[self::PARAM_ACTION_ORDERID] = $value;
    }

    public function setRawActionCode($value) {
        $_REQUEST[self::PARAM_ACTION_ACTIONCODE] = $value;
    }
    
    public function getRawActionCode() {
        return $this->getRequestParameter(self::PARAM_ACTION_ACTIONCODE);
    }
    
    public function getRawTotalCost() {
        return $this->getRequestParameter(self::PARAM_ACTION_TOTALCOST);
    }
    
    public function setRawTotalCost($value) {
        $_REQUEST[self::PARAM_ACTION_TOTALCOST] = $value;
    }
    
	public function getRawFixedCost() {
        return $this->percentageTranslate($this->getRequestParameter(self::PARAM_ACTION_FIXEDCOST));
    }
    
	public function getRawCurrency() {
        return $this->getRequestParameter(self::PARAM_ACTION_CURRENCY);
    }
    
    public function setRawFixedCost($value) {
        $_REQUEST[self::PARAM_ACTION_FIXEDCOST] = $value;
    }
    
    public function getRawAffiliateId() {
        return $this->getRequestParameter(self::PARAM_ACTION_AFFILIATEID);
    }
    
    public function getRawCoupon() {
        return $this->getRequestParameter(self::PARAM_ACTION_COUPON);
    }
    
    public function setRawAffiliateId($value) {
        $_REQUEST[self::PARAM_ACTION_AFFILIATEID] = $value;
    }
    
    
    public function getRawCampaignId() {
        return $this->getRequestParameter(self::PARAM_ACTION_CAMPAIGNID);
    }
    
    public function getRawChannelId() {
        return $this->getRequestParameter(self::PARAM_ACTION_CHANNELID);
    }
    
    public function setRawCampaignId($value) {
        $_REQUEST[self::PARAM_ACTION_CAMPAIGNID] = $value;
    }
    
    public function setRawCoupon($couponCode) {
        $_REQUEST[self::PARAM_ACTION_COUPON] = $couponCode;
    }
    
    
    public function getRawProductId() {
        return $this->getRequestParameter(self::PARAM_ACTION_PRODUCTID);
    }

    public function setRawProductId($value) {
        $_REQUEST[self::PARAM_ACTION_PRODUCTID] = $value;
    }
    
    
	/**
     * gets currency from parameter
     * @return string
     */
    public function getCurrency() {
        return $this->getRequestParameter(self::PARAM_ACTION_CURRENCY);
    }
    
    public function setCurrency($value) {
        $_REQUEST[self::PARAM_ACTION_CURRENCY] = $value;
    }
    
    public function getRawCustomCommission() {
         return $this->percentageTranslate($this->getRequestParameter(self::PARAM_ACTION_CUSTOM_COMMISSION));
    }
    
    public function setRawCustomCommission($value) {
        $_REQUEST[self::PARAM_ACTION_CUSTOM_COMMISSION] = $value;
    }
    
    public function getRawCustomStatus() {
        return $this->getRequestParameter(self::PARAM_ACTION_CUSTOM_STATUS);
    }
    
    public function setRawCustomStatus($value) {
        $_REQUEST[self::PARAM_ACTION_CUSTOM_STATUS] = $value;
    }
    
    public function getRawCustomTimeStamp() {
        return $this->getRequestParameter(self::PARAM_ACTION_CUSTOM_TIMESTAMP);
    }
    
    public function getRecognizedParameters() {
        $params = 'TotalCost='.$this->getRawTotalCost();
        $params .= ' ,AccountId='.$this->getAccountId();
        $params .= ' ,FixedCost='.$this->getRawFixedCost();
        $params .= ' ,OrderID='.$this->getRawOrderID();
        $params .= ' ,ProductID='.$this->getRawProductID();
        //$params .= ',ActionType='.$this->getActionType();
        $params .= ' ,Debug='.$this->getDebug();
        $params .= ' ,data1='.$this->getRawExtraData(1);
        $params .= ' ,data2='.$this->getRawExtraData(2);
        $params .= ' ,data3='.$this->getRawExtraData(3);
        $params .= ' ,data4='.$this->getRawExtraData(4);
        $params .= ' ,data5='.$this->getRawExtraData(5);
        $params .= ' ,AffiliateID='.$this->getRawAffiliateId();
        $params .= ' ,CampaignID='.$this->getRawCampaignId();
        $params .= ' ,Currency='.$this->getCurrency();
        $params .= ' ,Commission='.$this->getRawCustomCommission();
        $params .= ' ,Status='.$this->getRawCustomStatus();
        $params .= ' ,Coupon='.$this->getRawCoupon();
        
        return $params;
    }

    public function getTrackingMethod() {
        return $this->getRequestParameter(self::PARAM_ACTION_TRACKING_METHOD);
    }
    
    /**
     * @return Pap_Tracking_Cookie_Sale
     */
    public function getClientSaleCookie() {
        $cookie = new Pap_Tracking_Cookie_Sale();
        $cookie->decode($this->getRequestParameter(self::PARAM_ACTION_CLIENT_SALE_COOKIE));
        return $cookie;
    }    

    /**
     * @return Pap_Tracking_Cookie_ClickData
     */
    public function getFirstClickCookie() {
        $cookie = new Pap_Tracking_Cookie_ClickData();
        $cookie->decode($this->getRequestParameter(self::PARAM_ACTION_CLIENT_FIRST_CLICK));
        return $cookie;
    }     

    /**
     * @return Pap_Tracking_Cookie_ClickData
     */
    public function getLastClickCookie() {
        $cookie = new Pap_Tracking_Cookie_ClickData();
        $cookie->decode($this->getRequestParameter(self::PARAM_ACTION_CLIENT_LAST_CLICK));
        return $cookie;
    }  
    
    public function getAccountId() {
        return $this->getRequestParameter(self::PARAM_ACTION_ACCOUNTID);
    }

    public function getVisitorId() {
        return $this->getRequestParameter(self::PARAM_ACTION_VISITORID);
    }
}
?>
