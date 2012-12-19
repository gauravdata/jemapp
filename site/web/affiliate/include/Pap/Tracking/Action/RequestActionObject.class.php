<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
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
class Pap_Tracking_Action_RequestActionObject extends Gpf_Rpc_JsonObject {
    public $ac = ''; // actionCode
    public $t  = ''; // totalCost
    public $f  = ''; // fixedCost
    public $o  = ''; // order ID
    public $p  = ''; // product ID
    public $d1 = ''; // data1
    public $d2 = ''; // data2
    public $d3 = ''; // data3
    public $d4 = ''; // data4
    public $d5 = ''; // data5
    public $a  = ''; // affiliate ID
    public $c  = ''; // campaign ID
    public $b  = ''; // banner ID
    public $ch = ''; // channel ID
    public $cc = ''; // custom commission
    public $s  = ''; // status
    public $cr = ''; // currency
    public $cp = ''; // coupon code
    public $ts = ''; // time stamp
    
    public function __construct($object = null) {
        parent::__construct($object);
    }

    public function getActionCode() {
        return $this->ac;
    }

    public function getTotalCost() {
        return $this->t;
    }

    public function getFixedCost() {
        return $this->f;
    }

    public function getOrderId() {
        return $this->o;
    }

    public function getProductId() {
        return $this->p;
    }

    public function getData1() {
        return $this->d1;
    }

    public function getData2() {
        return $this->d2;
    }

    public function getData3() {
        return $this->d3;
    }

    public function getData4() {
        return $this->d4;
    }

    public function getData5() {
        return $this->d5;
    }

    public function getData($i) {
        $dataVar = 'd'.$i;
        return $this->$dataVar;
    }

    public function setData($i, $value) {
        $dataVar = 'd'.$i;
        $this->$dataVar = $value;
    }

    public function getAffiliateId() {
        return $this->a;
    }

    public function getCampaignId() {
        return $this->c;
    }
    
    public function getBannerId() {
        return $this->b;
    }

    public function getChannelId() {
        return $this->ch;
    }

    public function getCustomCommission() {
        return $this->cc;
    }

    public function getStatus() {
        return $this->s;
    }

    public function getCurrency() {
        return $this->cr;
    }

    public function getCouponCode() {
        return $this->cp;
    }

    public function getTimeStamp() {
        return $this->ts;
    }

    public function setActionCode($value) {
        $this->ac = $value;
    }

    public function setTotalCost($value) {
        $this->t = $value;
    }

    public function setFixedCost($value) {
        $this->f = $value;
    }

    public function setOrderId($value) {
        $this->o = $value;
    }

    public function setProductId($value) {
        $this->p = $value;
    }

    public function setData1($value) {
        $this->d1 = $value;
    }

    public function setData2($value) {
        $this->d2 = $value;
    }

    public function setData3($value) {
        $this->d3 = $value;
    }

    public function setData4($value) {
        $this->d4 = $value;
    }

    public function setData5($value) {
        $this->d5 = $value;
    }

    public function setAffiliateId($value) {
        $this->a = $value;
    }

    public function setCampaignId($value) {
        $this->c = $value;
    }
    
    public function setBannerId($value) {
        $this->b = $value;
    }

    public function setChannelId($value) {
        $this->ch = $value;
    }

    public function setCustomCommission($value) {
        $this->cc = $value;
    }

    public function setStatus($value) {
        $this->s = $value;
    }

    public function setCurrency($value) {
        $this->cr = $value;
    }

    public function setCouponCode($value) {
        $this->cp = $value;
    }

    public function setTimeStamp($value) {
        $this->ts = $value;
    }

}
?>
