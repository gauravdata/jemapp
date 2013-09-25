<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Affiliate.class.php 22593 2008-12-01 12:56:47Z mbebjak $
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

class Pap_Api_Transaction extends Pap_Api_Object {

    private $dataValues = null;

    public function __construct(Gpf_Api_Session $session) {
        if($session->getRoleType() == Gpf_Api_Session::AFFILIATE) {
            throw new Exception("This class can be used only by merchant!");
        } else {
            $this->class = "Pap_Merchants_Transaction_TransactionsForm";
        }
         
        parent::__construct($session);
    }

    public function getTransid() { return $this->getField("transid"); }
    public function setTransid($value) {
        $this->setField("transid", $value);
        $this->setField("Id", $value);
    }

    public function getType() { return $this->getField("rtype"); }
    public function setType($value) { $this->setField("rtype", $value); }

    public function getStatus() { return $this->getField("rstatus"); }
    public function setStatus($value) { $this->setField("rstatus", $value); }

    public function getMultiTierCreation() { return $this->getField("multiTier"); }
    public function setMultiTierCreation($value) { $this->setField("multiTier", $value); }

    public function getUserid() { return $this->getField("userid"); }
    public function setUserid($value) { $this->setField("userid", $value); }

    public function getBannerid() { return $this->getField("bannerid"); }
    public function setBannerid($value) { $this->setField("bannerid", $value); }

    public function getParentBannerid() { return $this->getField("parentbannerid"); }
    public function setParentBannerid($value) { $this->setField("parentbannerid", $value); }

    public function getCampaignid() { return $this->getField("campaignid"); }
    public function setCampaignid($value) { $this->setField("campaignid", $value); }

    public function getCountryCode() { return $this->getField("countrycode"); }
    public function setCountryCode($value) { $this->setField("countrycode", $value); }

    public function getDateInserted() { return $this->getField("dateinserted"); }
    public function setDateInserted($value) { $this->setField("dateinserted", $value); }

    public function getDateApproved() { return $this->getField("dateapproved"); }
    public function setDateApproved($value) { $this->setField("dateapproved", $value); }

    public function getPayoutStatus() { return $this->getField("payoutstatus"); }
    public function setPayoutStatus($value) { $this->setField("payoutstatus", $value); }

    public function getPayoutHistoryId() { return $this->getField("payouthistoryid"); }
    public function setPayoutHistoryId($value) { $this->setField("payouthistoryid", $value); }

    public function getRefererUrl() { return $this->getField("refererurl"); }
    public function setRefererUrl($value) { $this->setField("refererurl", $value); }

    public function getIp() { return $this->getField("ip"); }
    public function setIp($value) { $this->setField("ip", $value); }

    public function getBrowser() { return $this->getField("browser"); }
    public function setBrowser($value) { $this->setField("browser", $value); }

    public function getCommission() { return $this->getField("commission"); }
    public function setCommission($value) { $this->setField("commission", $value); }

    public function getOrderId() { return $this->getField("orderid"); }
    public function setOrderId($value) { $this->setField("orderid", $value); }

    public function getProductId() { return $this->getField("productid"); }
    public function setProductId($value) { $this->setField("productid", $value); }

    public function getTotalCost() { return $this->getField("totalcost"); }
    public function setTotalCost($value) { $this->setField("totalcost", $value); }

    public function getRecurringCommid() { return $this->getField("recurringcommid"); }
    public function setRecurringCommid($value) { $this->setField("recurringcommid", $value); }

    public function getFirstClickTime() { return $this->getField("firstclicktime"); }
    public function setFirstClickTime($value) { $this->setField("firstclicktime", $value); }

    public function getFirstClickReferer() { return $this->getField("firstclickreferer"); }
    public function setFirstClickReferer($value) { $this->setField("firstclickreferer", $value); }

    public function getFirstClickIp() { return $this->getField("firstclickip"); }
    public function setFirstClickIp($value) { $this->setField("firstclickip", $value); }

    public function getFirstClickData1() { return $this->getField("firstclickdata1"); }
    public function setFirstClickData1($value) { $this->setField("firstclickdata1", $value); }

    public function getFirstClickData2() { return $this->getField("firstclickdata2"); }
    public function setFirstClickData2($value) { $this->setField("firstclickdata2", $value); }

    public function getClickCount() { return $this->getField("clickcount"); }
    public function setClickCount($value) { $this->setField("clickcount", $value); }

    public function getLastClickTime() { return $this->getField("lastclicktime"); }
    public function setLastClickTime($value) { $this->setField("lastclicktime", $value); }

    public function getLastClickReferer() { return $this->getField("lastclickreferer"); }
    public function setLastClickReferer($value) { $this->setField("lastclickreferer", $value); }

    public function getLastClickIp() { return $this->getField("lastclickip"); }
    public function setLastClickIp($value) { $this->setField("lastclickip", $value); }

    public function getLastClickData1() { return $this->getField("lastclickdata1"); }
    public function setLastClickData1($value) { $this->setField("lastclickdata1", $value); }

    public function getLastClickData2() { return $this->getField("lastclickdata2"); }
    public function setLastClickData2($value) { $this->setField("lastclickdata2", $value); }

    public function getTrackMethod() { return $this->getField("trackmethod"); }
    public function setTrackMethod($value) { $this->setField("trackmethod", $value); }

    public function getOriginalCurrencyId() { return $this->getField("originalcurrencyid"); }
    public function setOriginalCurrencyId($value) { $this->setField("originalcurrencyid", $value); }

    public function getOriginalCurrencyValue() { return $this->getField("originalcurrencyvalue"); }
    public function setOriginalCurrencyValue($value) { $this->setField("originalcurrencyvalue", $value); }

    public function getOriginalCurrencyRate() { return $this->getField("originalcurrencyrate"); }
    public function setOriginalCurrencyRate($value) { $this->setField("originalcurrencyrate", $value); }

    public function getTier() { return $this->getField("tier"); }
    public function setTier($value) { $this->setField("tier", $value); }

    public function getChannel() { return $this->getField("channel"); }
    public function setChannel($value) { $this->setField("channel", $value); }

    public function getCommTypeId() { return $this->getField("commtypeid"); }
    public function setCommTypeId($value) { $this->setField("commtypeid", $value); }

    public function getMerchantNote() { return $this->getField("merchantnote"); }
    public function setMerchantNote($value) { $this->setField("merchantnote", $value); }

    public function getSystemNote() { return $this->getField("systemnote"); }
    public function setSystemNote($value) { $this->setField("systemnote", $value); }

    public function getData($index) {
        $this->checkIndex($index);
        return $this->getField("data$index");
    }
    public function setData($index, $value) {
        $this->checkIndex($index);
        $this->setField("data$index", $value);
    }

    /**
     * @param $note optional note that will be added to the refund/chargeback transaction
     * @param $fee that will be added to the refund/chargeback transaction
     * @return Gpf_Rpc_Action
     */
    public function chargeBack($note = '', $fee = 0, $refundMultiTier = false) {
        return $this->makeRefundChargeBack($note, 'H', $fee, $refundMultiTier);
    }

    /**
     * @param $note optional note that will be added to the refund/chargeback transaction
     * @param $fee that will be added to the refund/chargeback transaction
     * @return Gpf_Rpc_Action
     */
    public function refund($note = '', $fee = 0, $refundMultiTier = false) {
        return $this->makeRefundChargeBack($note, 'R', $fee, $refundMultiTier);
    }

    /**
     * @return Gpf_Rpc_Action
     */
    private function makeRefundChargeBack($note, $type, $fee, $refundMultiTier) {
        if ($this->getTransid() == '') {
            throw new Gpf_Exception("No transaction ID. Call setTransid() or load transaction before calling refund/chargeback");
        }
        $request = new Gpf_Rpc_ActionRequest($this->class, 'makeRefundChargeback', $this->getSession());
        $request->addParam('merchant_note', $note);
        $request->addParam('refund_multitier', $refundMultiTier ? 'Y' : 'N');
        $request->addParam('status', $type);
        $request->addParam('ids', new Gpf_Rpc_Map(array($this->getTransid())));
        $request->addParam('fee', $fee);
        $request->sendNow();
        return $request->getAction();
    }


    private function checkIndex($index) {
        if(!is_numeric($index) || $index > 5 || $index < 1) {
            throw new Exception("Incorrect index '$index', it must be between 1 and 5");
        }
         
        return true;
    }

    protected function fillEmptyRecord() {
        $this->setTransid("");
        if($this->getType() == '') {
            $this->setType("A");
        }
        if($this->getMultiTierCreation() == '') {
            $this->setMultiTierCreation('N');
        }
    }

    protected function getPrimaryKey() {
        return "transid";
    }

    protected function getGridRequest() {
        return new Pap_Api_TransactionsGrid($this->getSession());
    }
}
?>
