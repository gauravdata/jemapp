<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric, Michal Bebjak
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
class Pap_Stats_Params extends Gpf_Object {
    private $accountId = "";
    private $campaignId = "";
    private $affiliateId = "";
    private $bannerId = "";
    private $orderId = "";
    private $countryCode = '';
    private $payoutStatus = null;
    /**
     * @var Gpf_DateTime
     */
    private $dateFrom = null;
    /**
     * @var Gpf_DateTime
     */
    private $dateTo = null;
    /**
     * @var Gpf_DateTime
     */
    private $dateApprovedFrom = null;
    /**
     * @var Gpf_DateTime
     */
    private $dateApprovedTo = null;
    private $channel = "";
    private $destinationURL = "";

    public function __construct($dateFrom = null) {
        $this->dateFrom = $dateFrom;
    }
    
    /**
     * @params $preffix table preffix (without .)
     */
    public function addTo(Gpf_SqlBuilder_SelectBuilder $select, $preffix = '') {
        $preffix = rtrim($preffix, '.');
        if ($preffix == 'b') {
            throw new Gpf_Exception('Table preffix in Pap_Stats_Params::addTo() can not be \'b\'');
        }

        if ($preffix != '') {
            $preffix .= '.';
        }

        if ($this->isCampaignIdDefined()) {
            $select->where->add($preffix.Pap_Stats_Table::CAMPAIGNID, '=', $this->getCampaignId());
        }
        
        if ($this->isOrderIdDefined()) {
            if (is_array($this->getOrderId())) {
                $select->where->add($preffix.Pap_Stats_Table::ORDERID, 'IN', $this->getOrderId());
            } else {
                $select->where->add($preffix.Pap_Stats_Table::ORDERID, '=', $this->getOrderId());
            }
        }

        if($this->isAffiliateIdDefined()) {
            $select->where->add($preffix.Pap_Stats_Table::USERID, '=', $this->getAffiliateId());
        }

        if ($this->isCountryCodeDefined()) {
            $select->where->add($preffix.Pap_Stats_Table::COUNTRYCODE, '=', $this->getCountryCode());
        }

        if($this->isBannerIdDefined()) {
            $select->where->addCondition($this->createBannerCondition($preffix));
        }

        if($this->isChannelDefined()) {
            $select->where->add($preffix.Pap_Stats_Table::CHANNEL, '=', $this->getChannel());
        }

        if ($this->isDestinationURLDefined()) {
            $select->from->addLeftJoin(Pap_Db_Table_Banners::getName(), 'b', $preffix.Pap_Stats_Table::BANNERID.'=b.'.Pap_Db_Table_Banners::ID);
            $select->where->add('b.'.Pap_Db_Table_Banners::DESTINATION_URL, '=', $this->getDestinationURL());
        }

        if ($this->isDateFromDefined()) {
            $select->where->add($preffix.Pap_Stats_Table::DATEINSERTED, '>=', $this->getDateFrom()->toDateTime());
        }
        if ($this->isDateToDefined()) {
            $select->where->add($preffix.Pap_Stats_Table::DATEINSERTED, '<=', $this->getDateTo()->toDateTime());
        }

        if ($this->isDateApprovedFromDefined()) {
            $select->where->add($preffix.Pap_Stats_Table::DATEAPPROVED, '>=', $this->getDateApprovedFrom()->toDateTime());
        }
        if ($this->isDateApprovedToDefined()) {
            $select->where->add($preffix.Pap_Stats_Table::DATEAPPROVED, '<=', $this->getDateApprovedTo()->toDateTime());
        }

        if ($this->isAccountIdDefined()) {
            $select->where->add($preffix.Pap_Stats_Table::ACCOUNTID, '=', $this->getAccountId());
        }
    }

    public function setPayoutStatus($status) {
        $this->payoutStatus = $status;
    }

    public function getPayoutStatus() {
        return $this->payoutStatus;
    }

    public function isPayoutStatusDefined() {
        return $this->isParamDefined($this->payoutStatus);
    }

    public function getCampaignId() {
        return $this->campaignId;
    }

    public function setCampaignId($id) {
        if($id == null) {
            $id = "";
        }
        $this->campaignId = $id;
    }
    
    public function isOrderIdDefined() {
        return $this->isParamDefined($this->orderId);
    }

    public function isCampaignIdDefined() {
        return $this->isParamDefined($this->campaignId);
    }

    public function setAccountId($value) {
        $this->accountId = $value;
    }

    public function getAccountId() {
        return $this->accountId;
    }

    public function isAccountIdDefined() {
        return $this->isParamDefined($this->accountId);
    }

    public function getAffiliateId() {
        if (Gpf_Session::getAuthUser()->isAffiliate()) {
            return Gpf_Session::getAuthUser()->getPapUserId();
        }
        return $this->affiliateId;
    }

    public function setAffiliateId($id) {
        if($id == null) {
            $id = "";
        }
        $this->affiliateId = $id;
    }

    public function isAffiliateIdDefined() {
        if (Gpf_Session::getAuthUser()->isAffiliate()) {
            return true;
        }
        return $this->isParamDefined($this->affiliateId);
    }

    public function setChannel($channel) {
        if($channel == null) {
            $channel = "";
        }
        $this->channel = $channel;
    }

    public function getChannel() {
        return $this->channel;
    }

    public function isChannelDefined() {
        return $this->isParamDefined($this->channel);
    }

    public function getBannerId() {
        return $this->bannerId;
    }

    public function getOrderId() {
        return $this->orderId;
    }

    public function setOrderId($orderId) {
        $this->orderId = $orderId;
    }

    public function setBannerId($id) {
        if($id == null) {
            $id = "";
        }
        $this->bannerId = $id;
    }

    public function isBannerIdDefined() {
        return $this->isParamDefined($this->bannerId);
    }

    /**
     *
     * @param $from timestamp or datetime string
     * @param $to timestamp or datetime string
     */
    public function setDateRange($from, $to) {
        $this->setDateFrom(new Gpf_DateTime($from));
        $this->setDateTo(new Gpf_DateTime($to));
    }

    /**
     *
     * @param $from timestamp or datetime string
     * @param $to timestamp or datetime string
     */
    public function setRange(Gpf_DateTime_Range $range) {
        $this->setDateFrom($range->getFrom());
        $this->setDateTo($range->getTo());
    }

    /**
     * @return Gpf_DateTime
     * @throws Gpf_Exception
     */
    public function getDateFrom() {
        if (!$this->isDateFromDefined()) {
            throw new Gpf_Exception('date from is not defined in StatsParams');
        }
        return $this->dateFrom;
    }

    public function isDateFromDefined() {
        return !is_null($this->dateFrom);
    }

    public function setDateFrom(Gpf_DateTime $date) {
        $this->dateFrom = $date;
    }

    /**
     * @return Gpf_DateTime
     * @throws Gpf_Exception
     */
    public function getDateTo() {
        if (!$this->isDateToDefined()) {
            throw new Gpf_Exception('date to is not defined in StatsParams');
        }
        return $this->dateTo;
    }

    public function isDateToDefined() {
        return !is_null($this->dateTo);
    }

    public function setDateTo(Gpf_DateTime $date) {
        $this->dateTo = $date;
    }

    /**
     * @return Gpf_DateTime
     * @throws Gpf_Exception
     */
    public function getDateApprovedFrom() {
        if (!$this->isDateApprovedFromDefined()) {
            throw new Gpf_Exception('date approved from is not defined in StatsParams');
        }
        return $this->dateApprovedFrom;
    }

    public function isDateApprovedFromDefined() {
        return !is_null($this->dateApprovedFrom);
    }

    public function setDateApprovedFrom(Gpf_DateTime $date) {
        $this->dateApprovedFrom = $date;
    }

    /**
     * @return Gpf_DateTime
     * @throws Gpf_Exception
     */
    public function getDateApprovedTo() {
        if (!$this->isDateApprovedToDefined()) {
            throw new Gpf_Exception('date approved to is not defined in StatsParams');
        }
        return $this->dateApprovedTo;
    }

    public function isDateApprovedToDefined() {
        return !is_null($this->dateApprovedTo);
    }

    public function setDateApprovedTo(Gpf_DateTime $date) {
        $this->dateApprovedTo = $date;
    }

    public function isDestinationURLDefined() {
        return $this->isParamDefined($this->destinationURL);
    }

    public function getDestinationURL() {
        return $this->destinationURL;
    }

    public function setDestinationURL($destinationURL) {
        if (!is_null($destinationURL)) {
            $this->destinationURL = $destinationURL;
        }
    }

    public function isCountryCodeDefined() {
        return $this->isParamDefined($this->countryCode);
    }

    public function getCountryCode() {
        return $this->countryCode;
    }

    public function setCountryCode($countryCode) {
        if (!is_null($countryCode)) {
            $this->countryCode = $countryCode;
        }
    }

    public function initFrom(Gpf_Rpc_FilterCollection $filterCollection) {
        $dateFilter = array('dateFrom' => null,
                            'dateTo' => null);
        foreach ($filterCollection as $filter) {
            switch ($filter->getCode()) {
                case 'channel':
                    if ($filter->getValue() != 'none') {
                        $this->setChannel($filter->getValue());
                    }
                    break;
                case 'datetime':        $dateFilter = $filter->addDateValueToArray($dateFilter); break;
                case 'campaignid':      $this->setCampaignId($filter->getValue()); break;
                case 'userid':          $this->setAffiliateId($filter->getValue()); break;
                case 'destinationurl':  $this->setDestinationURL($filter->getValue()); break;
                case 'bannerid':        $this->setBannerId($filter->getValue()); break;
                case 'rstatus':         $this->setStatus($filter->getValue()); break;
                case 'countrycode':     $this->setCountryCode($filter->getValue()); break;
                case 'accountid':     	$this->setAccountId($filter->getValue()); break;
                case 'dateinserted':    $this->decodeDateRangeFromPreset($filter); break;
            }
        }
        if ($dateFilter['dateFrom'] != null) {
            $this->setDateFrom(new Gpf_DateTime($dateFilter['dateFrom']));
        }
        if ($dateFilter['dateTo'] != null) {
            $this->setDateTo(new Gpf_DateTime($dateFilter['dateTo']));
        }
    }

    /**
     * @return Gpf_SqlBuilder_CompoundWhereCondition
     */
    protected function createBannerCondition($preffix) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $condition->add($preffix.Pap_Stats_Table::BANNERID, '=', $this->getBannerId(), 'OR');
        $condition->add($preffix.Pap_Stats_Table::PARENTBANNERID, '=', $this->getBannerId(), 'OR');
        return $condition;
    }

    private function decodeDateRangeFromPreset($filter) {
        $data = $filter->addDateValueToArray(array());
        if (isset($data['dateFrom'])) {
            $this->setDateFrom(new Gpf_DateTime($data['dateFrom']));
        }
        if (isset($data['dateTo'])) {
            $this->setDateTo(new Gpf_DateTime($data['dateTo']));
        }
    }

    private function isParamDefined($param) {
        if (!is_null($param) && $param !== '') {
            return true;
        }
        return false;
    }

    //--------------------------------------------------------------

    private $status = "";
    private $type = "";
    private $commTypeId = "";

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        if($status != null){
            $this->status = $status;
        }
    }

    public function isStatusDefined() {
        return $this->isParamDefined($this->status);
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        if (!is_null($type)) {
            $this->type = $type;
        }
    }

    public function isTypeDefined() {
        return $this->isParamDefined($this->type);
    }

    public function getCommTypeId() {
        return $this->commTypeId;
    }

    public function setCommTypeId($commTypeId) {
        if (!is_null($commTypeId)) {
            $this->commTypeId = $commTypeId;
        }
    }

    public function isCommTypeIdDefined() {
        return $this->isParamDefined($this->commTypeId);
    }
}

?>
