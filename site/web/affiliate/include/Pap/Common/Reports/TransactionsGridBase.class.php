<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
class Pap_Common_Reports_TransactionsGridBase extends Gpf_View_GridService {
    
    /**
     * @var Gpf_SqlBuilder_CompoundWhereCondition
     */
    protected $_affiliateCondition;

    protected function initViewColumns() {
        $this->addViewColumn(Pap_Db_Table_Transactions::TRANSACTION_ID, $this->_("ID"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::COMMISSION, $this->_("Commission"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::TOTAL_COST, $this->_("Total cost"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::FIXED_COST, $this->_("Fixed cost"), true, Gpf_View_ViewColumn::TYPE_CURRENCY);
        $this->addViewColumn('t_'.Pap_Db_Table_Transactions::ORDER_ID, $this->_("Order ID"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::PRODUCT_ID, $this->_("Product ID"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::COUNTRY_CODE, $this->_("Country Code"), true, Gpf_View_ViewColumn::TYPE_COUNTRYCODE);
        $this->addViewColumn(Pap_Db_Table_Transactions::DATE_INSERTED, $this->_("Created"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Pap_Db_Table_Transactions::DATE_APPROVED, $this->_("Approved"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn('banner', $this->_('Banner'), true);
        $this->addViewColumn(Pap_Db_Table_Campaigns::NAME, $this->_("Campaign Name"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::R_TYPE, $this->_("Type"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::TIER, $this->_("Tier"), true);
        $this->addViewColumn('username', $this->_("Affiliate username"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::R_STATUS, $this->_("Status"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::PAYOUT_STATUS, $this->_("Paid"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::USER_ID, $this->_("Affiliate"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::IP, $this->_("Ip"), true, Gpf_View_ViewColumn::TYPE_IP);
        $this->addViewColumn(Pap_Db_Table_Transactions::REFERER_URL, $this->_("Referer"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::COMMISSION, $this->_("Commision"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::RECURRING_COMM_ID, $this->_("Recurring commison id"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, $this->_("Payout history id"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::CLICK_COUNT, $this->_("Click count"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::FIRST_CLICK_TIME, $this->_("First click time"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Pap_Db_Table_Transactions::FIRST_CLICK_REFERER, $this->_("First click referer"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::FIRST_CLICK_IP, $this->_("First click ip"), true, Gpf_View_ViewColumn::TYPE_IP);
        $this->addViewColumn(Pap_Db_Table_Transactions::FIRST_CLICK_DATA1, $this->_("First click data 1"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::FIRST_CLICK_DATA2, $this->_("First click data 2"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::LAST_CLICK_TIME, $this->_("Last click time"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn(Pap_Db_Table_Transactions::LAST_CLICK_REFERER, $this->_("Last click referer"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::LAST_CLICK_IP, $this->_("Last click ip"), true, Gpf_View_ViewColumn::TYPE_IP);
        $this->addViewColumn(Pap_Db_Table_Transactions::LAST_CLICK_DATA1, $this->_("Last click data 1"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::LAST_CLICK_DATA2, $this->_("Last click data 2"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::DATA1, $this->_("Extra data 1"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::DATA2, $this->_("Extra data 2"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::DATA3, $this->_("Extra data 3"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::DATA4, $this->_("Extra data 4"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::DATA5, $this->_("Extra data 5"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_ID, $this->_("Original currency ID"), true);
        $this->addViewColumn('original_currency_code', $this->_("Original currency code"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_RATE, $this->_("Original currency rate"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_VALUE, $this->_("Original currency value"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::MERCHANTNOTE, $this->_("Merchant note"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::SYSTEMNOTE, $this->_("System note"), true);
        $this->addViewColumn(Pap_Db_Table_Transactions::CHANNEL, $this->_("Channel"), true);
        $this->addViewColumn('payoutdate', $this->_("Payout date"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('t.' . Pap_Db_Table_Transactions::TRANSACTION_ID);
        $this->addDataColumn(Pap_Db_Table_Transactions::USER_ID,   "t.".Pap_Db_Table_Transactions::USER_ID);
        $this->addDataColumn(Pap_Db_Table_Transactions::CAMPAIGN_ID,   "t.".Pap_Db_Table_Transactions::CAMPAIGN_ID);
        $this->addDataColumn(Pap_Db_Table_Transactions::TRANSACTION_ID,   "t.".Pap_Db_Table_Transactions::TRANSACTION_ID);
        $this->addDataColumn(Pap_Db_Table_Transactions::COMMISSION,       "t.".Pap_Db_Table_Transactions::COMMISSION);
        $this->addDataColumn(Pap_Db_Table_Transactions::TOTAL_COST,       "t.".Pap_Db_Table_Transactions::TOTAL_COST);
        $this->addDataColumn(Pap_Db_Table_Transactions::FIXED_COST,       "t.".Pap_Db_Table_Transactions::FIXED_COST);
        $this->addDataColumn('t_'.Pap_Db_Table_Transactions::ORDER_ID,         "t.".Pap_Db_Table_Transactions::ORDER_ID);
        $this->addDataColumn(Pap_Db_Table_Transactions::ORDER_ID,         "t.".Pap_Db_Table_Transactions::ORDER_ID);
        $this->addDataColumn(Pap_Db_Table_Transactions::PRODUCT_ID,       "t.".Pap_Db_Table_Transactions::PRODUCT_ID);
        $this->addDataColumn(Pap_Db_Table_Transactions::DATE_INSERTED,    "t.".Pap_Db_Table_Transactions::DATE_INSERTED);
        $this->addDataColumn(Pap_Db_Table_Transactions::DATE_APPROVED,    "t.".Pap_Db_Table_Transactions::DATE_APPROVED);
        $this->addDataColumn(Pap_Db_Table_Transactions::COUNTRY_CODE,    "t.".Pap_Db_Table_Transactions::COUNTRY_CODE);
        $this->addDataColumn(Pap_Db_Table_Campaigns::NAME,                "c.".Pap_Db_Table_Campaigns::NAME);
        $this->addDataColumn(Pap_Db_Table_Transactions::R_TYPE,           "t.".Pap_Db_Table_Transactions::R_TYPE);
        $this->addDataColumn('commissionTypeName',                        "ct.".Pap_Db_Table_CommissionTypes::NAME);
        $this->addDataColumn(Pap_Db_Table_Transactions::TIER,             "t.".Pap_Db_Table_Transactions::TIER);
        $this->addDataColumn('username',                                        "au.username");
        $this->addDataColumn('firstname',                                        "au.firstname");
        $this->addDataColumn('lastname',                                        "au.lastname");
        $this->addDataColumn(Pap_Db_Table_Transactions::R_STATUS,         "t.".Pap_Db_Table_Transactions::R_STATUS);
        $this->addDataColumn(Pap_Db_Table_Transactions::PAYOUT_STATUS,    "t.".Pap_Db_Table_Transactions::PAYOUT_STATUS);
        $this->addDataColumn(Pap_Db_Table_Transactions::IP,               "t.".Pap_Db_Table_Transactions::IP);
        $this->addDataColumn(Pap_Db_Table_Transactions::REFERER_URL,      "t.".Pap_Db_Table_Transactions::REFERER_URL);
        $this->addDataColumn(Pap_Db_Table_Transactions::BROWSER, "t.".Pap_Db_Table_Transactions::BROWSER);
        $this->addDataColumn(Pap_Db_Table_Transactions::COMMISSION, "t.".Pap_Db_Table_Transactions::COMMISSION);
        $this->addDataColumn(Pap_Db_Table_Transactions::RECURRING_COMM_ID, "t.".Pap_Db_Table_Transactions::RECURRING_COMM_ID);
        $this->addDataColumn(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, "t.".Pap_Db_Table_Transactions::PAYOUTHISTORY_ID);
        $this->addDataColumn(Pap_Db_Table_Transactions::CLICK_COUNT, "t.".Pap_Db_Table_Transactions::CLICK_COUNT);
        $this->addDataColumn(Pap_Db_Table_Transactions::FIRST_CLICK_TIME, "t.".Pap_Db_Table_Transactions::FIRST_CLICK_TIME);
        $this->addDataColumn(Pap_Db_Table_Transactions::FIRST_CLICK_REFERER, "t.".Pap_Db_Table_Transactions::FIRST_CLICK_REFERER);
        $this->addDataColumn(Pap_Db_Table_Transactions::FIRST_CLICK_IP, "t.".Pap_Db_Table_Transactions::FIRST_CLICK_IP);
        $this->addDataColumn(Pap_Db_Table_Transactions::FIRST_CLICK_DATA1, "t.".Pap_Db_Table_Transactions::FIRST_CLICK_DATA1);
        $this->addDataColumn(Pap_Db_Table_Transactions::FIRST_CLICK_DATA2, "t.".Pap_Db_Table_Transactions::FIRST_CLICK_DATA2);
        $this->addDataColumn(Pap_Db_Table_Transactions::LAST_CLICK_TIME, "t.".Pap_Db_Table_Transactions::LAST_CLICK_TIME);
        $this->addDataColumn(Pap_Db_Table_Transactions::LAST_CLICK_REFERER, "t.".Pap_Db_Table_Transactions::LAST_CLICK_REFERER);
        $this->addDataColumn(Pap_Db_Table_Transactions::LAST_CLICK_IP, "t.".Pap_Db_Table_Transactions::LAST_CLICK_IP);
        $this->addDataColumn(Pap_Db_Table_Transactions::LAST_CLICK_DATA1, "t.".Pap_Db_Table_Transactions::LAST_CLICK_DATA1);
        $this->addDataColumn(Pap_Db_Table_Transactions::LAST_CLICK_DATA2, "t.".Pap_Db_Table_Transactions::LAST_CLICK_DATA2);
        $this->addDataColumn(Pap_Db_Table_Transactions::DATA1, "t.".Pap_Db_Table_Transactions::DATA1);
        $this->addDataColumn(Pap_Db_Table_Transactions::DATA2, "t.".Pap_Db_Table_Transactions::DATA2);
        $this->addDataColumn(Pap_Db_Table_Transactions::DATA3, "t.".Pap_Db_Table_Transactions::DATA3);
        $this->addDataColumn(Pap_Db_Table_Transactions::DATA4, "t.".Pap_Db_Table_Transactions::DATA4);
        $this->addDataColumn(Pap_Db_Table_Transactions::DATA5, "t.".Pap_Db_Table_Transactions::DATA5);
        $this->addDataColumn(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_ID, "t.".Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_ID);
        $this->addDataColumn('original_currency_code', 'cs.'.Gpf_Db_Table_Currencies::NAME);
        $this->addDataColumn(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_RATE, "t.".Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_RATE);
        $this->addDataColumn(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_VALUE, "t.".Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_VALUE);
        $this->addDataColumn(Pap_Db_Table_Transactions::MERCHANTNOTE, "t.".Pap_Db_Table_Transactions::MERCHANTNOTE);
        $this->addDataColumn(Pap_Db_Table_Transactions::SYSTEMNOTE, "t.".Pap_Db_Table_Transactions::SYSTEMNOTE);
        $this->addDataColumn('banner_name', 'b.'.Pap_Db_Table_Banners::NAME);
        $this->addDataColumn('banner_type', 'b.'.Pap_Db_Table_Banners::TYPE);
        $this->addDataColumn('banner_id', 'b.'.Pap_Db_Table_Banners::ID);
        $this->addDataColumn(Pap_Db_Table_Transactions::CHANNEL, 'ch.'.Pap_Db_Table_Channels::NAME);
        $this->addDataColumn('payoutdate', 'ph.'.Pap_Db_Table_PayoutsHistory::DATEINSERTED);
        $this->addDataColumn(Pap_Db_Table_Transactions::ALLOW_FIRST_CLICK_DATA, 't.'.Pap_Db_Table_Transactions::ALLOW_FIRST_CLICK_DATA);
        $this->addDataColumn(Pap_Db_Table_Transactions::ALLOW_LAST_CLICK_DATA, 't.'.Pap_Db_Table_Transactions::ALLOW_LAST_CLICK_DATA);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::COMMISSION, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::TOTAL_COST, '', 'N');
        $this->addDefaultViewColumn('t_'. Pap_Db_Table_Transactions::ORDER_ID, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::PRODUCT_ID, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::CHANNEL, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::DATE_INSERTED, '', 'D');
        $this->addDefaultViewColumn(Pap_Db_Table_Campaigns::NAME, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::USER_ID, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::R_TYPE, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::R_STATUS, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_Transactions::PAYOUT_STATUS, '', 'N');
    }

    function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_Transactions::getName(), "t");
        $onCondition = "t.".Pap_Db_Table_Transactions::CAMPAIGN_ID." = c.".Pap_Db_Table_Campaigns::ID;
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Campaigns::getName(), 'c', $onCondition);
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), "pu", "t.userid = pu.userid");
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "pu.accountuserid = gu.accountuserid");
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), "au", "gu.authid = au.authid");
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Channels::getName(), "ch", "t.channel = ch.channelid");
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_CommissionTypes::getName(), "ct", "t.commtypeid = ct.commtypeid");
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_Currencies::getName(), 'cs', 't.originalcurrencyid = cs.currencyid');
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Banners::getName(), 'b', 'b.bannerid = t.bannerid');
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_PayoutsHistory::getName(), 'ph',
            'ph.'.Pap_Db_Table_PayoutsHistory::ID.' = t.'.Pap_Db_Table_Transactions::PAYOUTHISTORY_ID);
    }

    protected function loadResultData() {
        $this->doMossoHack(Pap_Db_Table_Transactions::getInstance(), 't', Pap_Db_Table_Transactions::TRANSACTION_ID);
        return parent::loadResultData();
    }

     protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        switch ($filter->getCode()) {
            case "search":
                $this->addSearch($filter);
                break;
            case "orderId":
                $this->addOrderId($filter);
                break;
            case "channelValue":
            	$this->addChannel($filter);
        }
        
        $context = new Gpf_Plugins_ValueContext('');
        $context->setArray(array(
                'filter' => $filter,
                'whereClause' => $this->_selectBuilder->where
        ));
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Common_Reports_TransactionsGridBase.addFilter', $context);
    }
    
    protected function createCountSelect(Gpf_SqlBuilder_SelectBuilder $select) {
        $preffixes = $select->where->getUniqueTablePreffixes();
        $preffixes['t'] = 't';
    
        $count = new Gpf_SqlBuilder_SelectBuilder();
        $count->select->add('count(*)', 'count');
        $count->from = clone $select->from;
        $count->from->prune($preffixes);
        $count->where = $select->where;
        $count->groupBy = $select->groupBy;
        $count->having = $select->having;
        return $count;
    }

    private function addChannel(Gpf_SqlBuilder_Filter $filter) {
    	if ($filter->getValue() != "none") {
    	   $this->_selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::CHANNEL, "=", $filter->getValue());
    	}
    }

    private function addOrderId(Gpf_SqlBuilder_Filter $filter) {
        $orderIds = preg_split("/[,;(\n)]/", $filter->getValue());
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();

        for ($i = 0; $i < count($orderIds); $i++) {
        	if(trim($orderIds[$i]) != '') {
            	$condition->add('t.orderid', 'LIKE', '%'.trim($orderIds[$i]).'%', 'OR');
        	}
        }

        $this->_selectBuilder->where->addCondition($condition);
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $this->initSearchAffiliateCondition($filter);
        if (!is_null($this->_affiliateCondition)) {
            $condition->addCondition($this->_affiliateCondition, 'OR');
        }
        $condition->add('t.'.Pap_Db_Table_Transactions::TRANSACTION_ID, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('t.'.Pap_Db_Table_Transactions::ORDER_ID, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('t.'.Pap_Db_Table_Transactions::PRODUCT_ID, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('t.'.Pap_Db_Table_Transactions::TOTAL_COST, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('t.'.Pap_Db_Table_Transactions::IP, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('t.'.Pap_Db_Table_Transactions::REFERER_URL, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('t.'.Pap_Db_Table_Transactions::SYSTEMNOTE, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('t.'.Pap_Db_Table_Transactions::MERCHANTNOTE, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('t.'.Pap_Db_Table_Transactions::DATA1, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('t.'.Pap_Db_Table_Transactions::DATA2, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('t.'.Pap_Db_Table_Transactions::DATA3, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('t.'.Pap_Db_Table_Transactions::DATA4, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $condition->add('t.'.Pap_Db_Table_Transactions::DATA5, 'LIKE', '%'.$filter->getValue().'%', 'OR');

        $this->_selectBuilder->where->addCondition($condition);
    }
    
    protected function initSearchAffiliateCondition(Gpf_SqlBuilder_Filter $filter) {
        $this->_affiliateCondition = new Gpf_SqlBuilder_CompoundWhereCondition(); 
        $this->_affiliateCondition->add('au.'.Gpf_Db_Table_AuthUsers::USERNAME, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $this->_affiliateCondition->add('au.'.Gpf_Db_Table_AuthUsers::FIRSTNAME, 'LIKE', '%'.$filter->getValue().'%', 'OR');
        $this->_affiliateCondition->add('au.'.Gpf_Db_Table_AuthUsers::LASTNAME, 'LIKE', '%'.$filter->getValue().'%', 'OR');
    }

    /**
     * @anonym
     * @service
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    public function getCustomFilterFields(Gpf_Rpc_Params $params) {
        $filterFields = new Gpf_View_CustomFilterFields();
        $filterFields->addStringField(Pap_Db_Table_Transactions::TRANSACTION_ID, $this->_("ID"));
        $filterFields->addNumberField(Pap_Db_Table_Transactions::COMMISSION, $this->_("Commission"));
        $filterFields->addNumberField(Pap_Db_Table_Transactions::TOTAL_COST, $this->_("Total cost"));
        $filterFields->addStringField('t_'.Pap_Db_Table_Transactions::ORDER_ID, $this->_("Order ID"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::PRODUCT_ID, $this->_("Product ID"));
        $filterFields->addDateField(Pap_Db_Table_Transactions::DATE_INSERTED, $this->_("Created"));
        $filterFields->addDateField(Pap_Db_Table_Transactions::DATE_APPROVED, $this->_("Approved"));
        $filterFields->addStringField(Pap_Db_Table_Campaigns::NAME, $this->_("Campaign Name"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::COUNTRY_CODE, $this->_("Country code"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::R_TYPE, $this->_("Type"));
        $filterFields->addStringField('username', $this->_("Affiliate username"));
        $filterFields->addStringField('firstname', $this->_("Affiliate first name"));
        $filterFields->addStringField('lastname', $this->_("Affiliate last name"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::R_STATUS, $this->_("Status"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::PAYOUT_STATUS, $this->_("Paid"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::IP, $this->_("Ip"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::REFERER_URL, $this->_("Referer"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::RECURRING_COMM_ID, $this->_("Recurring commison id"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, $this->_("Payout history id"));
        $filterFields->addNumberField(Pap_Db_Table_Transactions::CLICK_COUNT, $this->_("Click count"));
        $filterFields->addDateField(Pap_Db_Table_Transactions::FIRST_CLICK_TIME, $this->_("First click time"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::FIRST_CLICK_REFERER, $this->_("First click referer"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::FIRST_CLICK_IP, $this->_("First click ip"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::FIRST_CLICK_DATA1, $this->_("First click data 1"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::FIRST_CLICK_DATA2, $this->_("First click data 2"));
        $filterFields->addDateField(Pap_Db_Table_Transactions::LAST_CLICK_TIME, $this->_("Last click time"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::LAST_CLICK_REFERER, $this->_("Last click referer"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::LAST_CLICK_IP, $this->_("Last click ip"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::LAST_CLICK_DATA1, $this->_("Last click data 1"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::LAST_CLICK_DATA2, $this->_("Last click data 2"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::DATA1, $this->_("Extra data 1"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::DATA2, $this->_("Extra data 2"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::DATA3, $this->_("Extra data 3"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::DATA4, $this->_("Extra data 4"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::DATA5, $this->_("Extra data 5"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_ID, $this->_("Original currency ID"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_RATE, $this->_("Original currency rate"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_VALUE, $this->_("Original currency value"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::MERCHANTNOTE, $this->_("Merchant note"));
        $filterFields->addStringField(Pap_Db_Table_Transactions::SYSTEMNOTE, $this->_("System note"));
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Common_Reports_TransactionsGridBase.getCustomFilterFields', $filterFields);
        return $filterFields->getRecordSet();
    }
}
?>
