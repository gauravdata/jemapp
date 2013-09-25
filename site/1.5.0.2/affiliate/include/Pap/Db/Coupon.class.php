<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Banner.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Db_Coupon extends Gpf_DbEngine_Row {

    function init() {
        $this->setTable(Pap_Db_Table_Coupons::getInstance());
        parent::init();
    }

    public function getId() {
        return $this->get(Pap_Db_Table_Coupons::ID);
    }

    public function setId($couponID) {
        $this->set(Pap_Db_Table_Coupons::ID, $couponID);
    }
    
    public function setStatus($status) {
        $this->set(Pap_Db_Table_Coupons::STATUS, $status);
    }
    
    public function setCode($code) {
        $this->set(Pap_Db_Table_Coupons::CODE, $code);
    }
    
    public function setUserID($userID) {
        $this->set(Pap_Db_Table_Coupons::USERID, $userID);
    }
    
    public function setBannerID($bannerID) {
        $this->set(Pap_Db_Table_Coupons::BANNERID, $bannerID);
    }
    
    public function getCode() {
        return $this->get(Pap_Db_Table_Coupons::CODE);
    }
    
    public function getBannerID() {
        return $this->get(Pap_Db_Table_Coupons::BANNERID);
    }
    
    public function getUserID() {
        return $this->get(Pap_Db_Table_Coupons::USERID);
    }
    
    public function generateCouponID() {
        $this->generatePrimaryKey();
    }
    /**
     * @return boolean
     */
    public function isValid() {        
        if ($this->isApproved() && $this->isValidDate() && $this->isApplicable()) {
            return true;
        }
        return false;
    }
    
    /**
     * @return boolean
     */
    protected function isApproved() {
        if ($this->getStaus() == Pap_Common_Constants::STATUS_APPROVED) {
            return true;
        }
        return false;
    }
    
    public function getStaus() {
        return $this->get(Pap_Db_Table_Coupons::STATUS);
    }
    
    public function getValidFrom() {
        return $this->get(Pap_Db_Table_Coupons::VALID_FROM);
    }
    
    public function getValidTo() {
        return $this->get(Pap_Db_Table_Coupons::VALID_TO);
    }
    
    /**
     * @return boolean
     */
    protected function isValidDate() {
        $date = new Gpf_DateTime();
        $timeStamp = $date->toTimeStamp();
        $validFrom = strtotime($this->getValidFrom());
        $validTo = strtotime($this->getValidTo());
        
        if ($validFrom == false || $validTo == false) {
            return false;
        }
        if ($timeStamp > $validFrom && $timeStamp < $validTo) {
            return true;
        }
        return false;
    }
    
    public function getMaxUseCount() {
        return $this->get(Pap_Db_Table_Coupons::MAX_USE_COUNT);
    }
    
    /**
     * @return boolean
     */
    protected function isApplicable() {
        if ($this->getMaxUseCount() == 0) {
            return true;
        }
        if ($this->getMaxUseCount() > $this->getSales()) {
            return true;
        }
        return false;
    }
    
    protected function generatePrimaryKey() {
        $this->setId(rand(10000000, 99999999));
    }
    
    /**
     * @throws Gpf_DbEngine_NoRowException
     * @throws Gpf_DbEngine_TooManyRowsException
     */
    public function loadFromCode() {
        $this->loadFromData(array(Pap_Db_Table_Coupons::CODE));
    }
    
    private function getSales() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('COUNT(' . Pap_Db_Table_Transactions::TRANSACTION_ID . ')', 'sales');
        $select->from->add(Pap_Db_Table_Transactions::getName());
        $select->where->add(Pap_Db_Table_Transactions::TIER, '=', '1');
        $select->where->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Common_Constants::TYPE_SALE);
        $select->where->add(Pap_Db_Table_Transactions::COUPON_ID, '=', $this->getId());
        return $select->getOneRow()->get('sales');
    }
}

?>
