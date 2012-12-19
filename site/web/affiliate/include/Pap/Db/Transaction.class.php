<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Transaction.class.php 30645 2011-01-04 08:16:27Z mkendera $
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
class Pap_Db_Transaction extends Gpf_DbEngine_Row {

    const TYPE_SALE = 'S';
    const TYPE_SIGNUP_BONUS = 'B';
    const TYPE_REFUND = 'R';
    const TYPE_CLICK = 'C';
    const TYPE_CPM = 'I';
    const TYPE_EXTRA_BONUS = 'E';
    const TYPE_CHARGE_BACK = 'H';
    const TYPE_REFERRAL = 'F';

    function __construct(){
        parent::__construct();
        $this->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
        $this->setDateInserted(Gpf_Common_DateUtils::now());
        $this->setChannel('');
        $this->setSplit(1);
        $this->setTier(1);
    }

    function init() {
        $this->setTable(Pap_Db_Table_Transactions::getInstance());
        parent::init();
    }

    protected function beforeSaveAction() {
        if ($this->getSaleId() == null) {
            $this->setSaleId(Gpf_Common_String::generateId(8));
        }
    }

    public function setAccountId($id) {
        $this->set(Pap_Db_Table_Transactions::ACCOUNT_ID, $id);
    }
    
    public function getAccountId() {
        return $this->get(Pap_Db_Table_Transactions::ACCOUNT_ID);
    }
    
    public function setDateInserted($dateInserted) {
        $this->set(Pap_Db_Table_Transactions::DATE_INSERTED, $dateInserted);
    }

    public function setOriginalCurrencyId($id) {
        $this->set(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_ID, $id);
    }

    public function getCommissionGroupId() {
        return $this->get(Pap_Db_Table_Transactions::COMMISSIONGROUPID);
    }

    public function setCommissionGroupId($groupid) {
        $this->set(Pap_Db_Table_Transactions::COMMISSIONGROUPID, $groupid);
    }

    public function setOriginalCurrencyRate($rate) {
        $this->set(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_RATE, $rate);
    }

    public function setOriginalCurrencyValue($value) {
        $this->set(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_VALUE, $value);
    }

    public function setOrderId($value) {
        $this->set(Pap_Db_Table_Transactions::ORDER_ID, $value);
    }

    public function getOrderId() {
        return $this->get(Pap_Db_Table_Transactions::ORDER_ID);
    }

    public function setRefererUrl($value) {
        $this->set(Pap_Db_Table_Transactions::REFERER_URL, $value);
    }

    public function getTransactionId() {
        return $this->get(Pap_Db_Table_Transactions::TRANSACTION_ID);
    }

    public function setProductId($value) {
        $this->set(Pap_Db_Table_Transactions::PRODUCT_ID, $value);
    }

    public function getProductId() {
        return $this->get(Pap_Db_Table_Transactions::PRODUCT_ID);
    }
    
    public function getPayoutHistoryId() {
        return $this->get(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID);
    }
    
    public function setPayoutHistoryId($value) {
        return $this->set(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, $value);
    }

    public function setDateApproved($value) {
        $this->set(Pap_Db_Table_Transactions::DATE_APPROVED, $value);
    }

    public function getDateApproved() {
        return $this->get(Pap_Db_Table_Transactions::DATE_APPROVED);
    }

    public function getDateInserted() {
        return $this->get(Pap_Db_Table_Transactions::DATE_INSERTED);
    }

    public function setTotalCost($value) {
        if($value == null || $value == '') {
            $value = 0;
        }
        $this->set(Pap_Db_Table_Transactions::TOTAL_COST, $value);
    }

    public function getSplit() {
        return $this->get(Pap_Db_Table_Transactions::SPLIT);
    }

    public function setSplit($value) {
        $this->set(Pap_Db_Table_Transactions::SPLIT, $value);
    }

    public function getTotalCost() {
        return $this->get(Pap_Db_Table_Transactions::TOTAL_COST);
    }

    public function getTotalCostAsText() {
        return round($this->getTotalCost(),Pap_Common_Utils_CurrencyUtils::getDefaultCurrencyPrecision());
    }

    public function setFixedCost($value) {
        $this->set(Pap_Db_Table_Transactions::FIXED_COST, $value);
    }

    public function getFixedCost() {
        return $this->get(Pap_Db_Table_Transactions::FIXED_COST);
    }
    
    public function getFirstClickReferer() {
        return $this->get(Pap_Db_Table_Transactions::FIRST_CLICK_REFERER);
    }
    
    public function getLastClickReferer() {
        return $this->get(Pap_Db_Table_Transactions::LAST_CLICK_REFERER);
    }

    public function setType($value) {
        $this->set(Pap_Db_Table_Transactions::R_TYPE, $value);
    }

    public function getType() {
        return $this->get(Pap_Db_Table_Transactions::R_TYPE);
    }

    public function setUserId($value) {
        $this->set(Pap_Db_Table_Transactions::USER_ID, $value);
    }

    public function getUserId() {
        return $this->get(Pap_Db_Table_Transactions::USER_ID);
    }

    public function setCampaignId($value) {
        $this->set(Pap_Db_Table_Transactions::CAMPAIGN_ID, $value);
    }

    public function setCouponId($couponID) {
        $this->set(Pap_Db_Table_Transactions::COUPON_ID, $couponID);
    }

    public function getCampaignId() {
        return $this->get(Pap_Db_Table_Transactions::CAMPAIGN_ID);
    }

    public function setBannerId($value) {
        $this->set(Pap_Db_Table_Transactions::BANNER_ID, $value);
    }

    public function getBannerId() {
        return $this->get(Pap_Db_Table_Transactions::BANNER_ID);
    }

    public function setParentBannerId($value) {
        $this->set(Pap_Db_Table_Transactions::PARRENT_BANNER_ID, $value);
    }

    public function getParentBannerId() {
        return $this->get(Pap_Db_Table_Transactions::PARRENT_BANNER_ID);
    }

    public function setCountryCode($value) {
        $this->set(Pap_Db_Table_Transactions::COUNTRY_CODE, $value);
    }

    public function getCountryCode() {
        return $this->get(Pap_Db_Table_Transactions::COUNTRY_CODE);
    }

    public function setTier($value) {
        $this->set(Pap_Db_Table_Transactions::TIER, $value);
    }

    public function getTier() {
        return $this->get(Pap_Db_Table_Transactions::TIER);
    }

    public function setCommission($value) {
        $this->set(Pap_Db_Table_Transactions::COMMISSION, $value);
    }

    public function getCommission() {
        return $this->get(Pap_Db_Table_Transactions::COMMISSION);
    }

    public function getCommissionAsText() {
        return round($this->getCommission(), Pap_Common_Utils_CurrencyUtils::getDefaultCurrencyPrecision());
    }

    public function getCouponID() {
        return $this->get(Pap_Db_Table_Transactions::COUPON_ID);
    }

    public function setPayoutStatus($value) {
        $this->set(Pap_Db_Table_Transactions::PAYOUT_STATUS, $value);
    }

    public function getPayoutStatus() {
        return $this->get(Pap_Db_Table_Transactions::PAYOUT_STATUS);
    }

    public function setParentTransactionId($id) {
        $this->set(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, $id);
    }

    public function getParentTransactionId() {
        return $this->get(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID);
    }

    public function setStatus($value) {
        $this->set(Pap_Db_Table_Transactions::R_STATUS, $value);
    }

    public function getStatus() {
        return $this->get(Pap_Db_Table_Transactions::R_STATUS);
    }

    public function setMerchantNote($value) {
        $this->set(Pap_Db_Table_Transactions::MERCHANTNOTE, $value);
    }

    public function getMerchantNote() {
        return $this->get(Pap_Db_Table_Transactions::MERCHANTNOTE);
    }

    public function setChannel($value) {
        $this->set(Pap_Db_Table_Transactions::CHANNEL, $value);
    }

    public function getChannel() {
        return $this->get(Pap_Db_Table_Transactions::CHANNEL);
    }

    public function setSystemNote($value) {
        $this->set(Pap_Db_Table_Transactions::SYSTEMNOTE, $value);
    }

    public function setClickCount($value) {
        $this->set(Pap_Db_Table_Transactions::CLICK_COUNT, $value);
    }

    public function getClickCount() {
        return $this->get(Pap_Db_Table_Transactions::CLICK_COUNT);
    }

    public function setId($id) {
        $this->set(Pap_Db_Table_Transactions::TRANSACTION_ID, $id);
    }

    public function setCommissionTypeId($commTypeId) {
        $this->set(Pap_Db_Table_Transactions::COMMISSIONTYPEID, $commTypeId);
    }

    public function getCommissionTypeId() {
        return $this->get(Pap_Db_Table_Transactions::COMMISSIONTYPEID);
    }

    public function getId() {
        return $this->get(Pap_Db_Table_Transactions::TRANSACTION_ID);
    }

    public function setData1($data1) {
        $this->set(Pap_Db_Table_Transactions::DATA1, $data1);
    }

    public function setData2($data2) {
        $this->set(Pap_Db_Table_Transactions::DATA2, $data2);
    }

    public function setData3($data3) {
        $this->set(Pap_Db_Table_Transactions::DATA3, $data3);
    }

    public function setData4($data4) {
        $this->set(Pap_Db_Table_Transactions::DATA4, $data4);
    }

    public function setData5($data5) {
        $this->set(Pap_Db_Table_Transactions::DATA5, $data5);
    }
    
    public function getData1() {
        return $this->get(Pap_Db_Table_Transactions::DATA1);
    }

    public function getData2() {
        return $this->get(Pap_Db_Table_Transactions::DATA2);
    }

    public function getData3() {
        return $this->get(Pap_Db_Table_Transactions::DATA3);
    }

    public function getData4() {
        return $this->get(Pap_Db_Table_Transactions::DATA4);
    }

    public function getData5() {
        return $this->get(Pap_Db_Table_Transactions::DATA5);
    }

    public function setFirstClickTime($value) {
        $this->set(Pap_Db_Table_Transactions::FIRST_CLICK_TIME, $value);
    }

    public function setFirstClickReferer($value) {
        $this->set(Pap_Db_Table_Transactions::FIRST_CLICK_REFERER, $value);
    }

    public function setFirstClickIp($value) {
        $this->set(Pap_Db_Table_Transactions::FIRST_CLICK_IP, $value);
    }

    public function setFirstClickData1($value) {
        $this->set(Pap_Db_Table_Transactions::FIRST_CLICK_DATA1, $value);
    }

    public function setFirstClickData2($value) {
        $this->set(Pap_Db_Table_Transactions::FIRST_CLICK_DATA2, $value);
    }

    public function setLastClickTime($value) {
        $this->set(Pap_Db_Table_Transactions::LAST_CLICK_TIME, $value);
    }

    public function setLastClickReferer($value) {
        $this->set(Pap_Db_Table_Transactions::LAST_CLICK_REFERER, $value);
    }

    public function setLastClickIp($value) {
        $this->set(Pap_Db_Table_Transactions::LAST_CLICK_IP, $value);
    }

    public function setLastClickData1($value) {
        $this->set(Pap_Db_Table_Transactions::LAST_CLICK_DATA1, $value);
    }

    public function setLastClickData2($value) {
        $this->set(Pap_Db_Table_Transactions::LAST_CLICK_DATA2, $value);
    }

    public function setTrackMethod($value) {
        $this->set(Pap_Db_Table_Transactions::TRACK_METHOD, $value);
    }

    public function setIp($value) {
        $this->set(Pap_Db_Table_Transactions::IP, $value);
    }

    public function setVisitorId($value) {
        $this->set(Pap_Db_Table_Transactions::VISITOR_ID, $value);
    }

    public function setSaleId($value) {
        $this->set(Pap_Db_Table_Transactions::SALE_ID, $value);
    }

    public function getSaleId() {
        return $this->get(Pap_Db_Table_Transactions::SALE_ID);
    }

    public function setLogGroupId($value) {
        $this->set(Pap_Db_Table_Transactions::LOGGROUPID, $value);
    }

    public function getLogGroupId() {
        return $this->get(Pap_Db_Table_Transactions::LOGGROUPID);
    }

    public function getIp() {
        return $this->get(Pap_Db_Table_Transactions::IP);
    }

    public function getRefererUrl() {
        return $this->get(Pap_Db_Table_Transactions::REFERER_URL);
    }
    
    public function getLastClickTime() {
        return $this->get(Pap_Db_Table_Transactions::LAST_CLICK_TIME);
    }
    
    
    public function setAllowLastClickData($value) {
        $this->set(Pap_Db_Table_Transactions::ALLOW_LAST_CLICK_DATA, $value);
    }
    
    public function getAllowLastClickData() {
        return $this->get(Pap_Db_Table_Transactions::ALLOW_LAST_CLICK_DATA);
    }
    
    public function setAllowFirstClickData($value) {
        $this->set(Pap_Db_Table_Transactions::ALLOW_FIRST_CLICK_DATA, $value);
    }
    
    public function getAllowFirstClickData() {
        return $this->get(Pap_Db_Table_Transactions::ALLOW_FIRST_CLICK_DATA);
    }
   
    public function recompute(Pap_Db_Commission $commission) {
        $commissionType = $commission->getCommissionType();
        $commissionValue = $commission->getCommissionValue();
        $realTotalCost = $this->getTotalCost() - $this->getFixedCost();

        if ($commissionType == Pap_Db_Commission::COMMISSION_TYPE_PERCENTAGE) {
            $newValue = $realTotalCost * ($commissionValue/100);
        } else if($commissionType == Pap_Db_Commission::COMMISSION_TYPE_FIXED) {
            $newValue = $commissionValue;
        } else {
            return;
        }
        $newValue = $newValue * $this->getSplit();

        if ($this->getType() == Pap_Db_Transaction::TYPE_REFUND ||
                $this->getType() == Pap_Db_Transaction::TYPE_CHARGE_BACK) {
            $newValue *= -1;
        }

        $this->setCommission($newValue);
    }

    /**
     * @return Pap_Db_Transaction
     */
    public function getRefundOrChargebackTransaction() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Pap_Db_Table_Transactions::getInstance());
        $select->from->add(Pap_Db_Table_Transactions::getName());
        $select->where->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, '=', $this->getId());
        $select->where->add(Pap_Db_Table_Transactions::R_TYPE, 'IN', array(Pap_Db_Transaction::TYPE_REFUND, Pap_Db_Transaction::TYPE_CHARGE_BACK));
        try {
            $record = $select->getOneRow();
            $transaction = new Pap_Db_Transaction();
            $transaction->fillFromRecord($record);
            return $transaction;
        } catch (Gpf_Exception $e) {
            return null;
        }
    }
}

?>
