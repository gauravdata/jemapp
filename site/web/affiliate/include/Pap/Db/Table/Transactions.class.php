<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: Transactions.class.php 30645 2011-01-04 08:16:27Z mkendera $
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
class Pap_Db_Table_Transactions extends Gpf_DbEngine_Table implements Pap_Stats_Table {
	const TRANSACTION_ID = 'transid';
	const ACCOUNT_ID = Pap_Stats_Table::ACCOUNTID;
	const USER_ID = Pap_Stats_Table::USERID;
	const BANNER_ID = Pap_Stats_Table::BANNERID;
	const PARRENT_BANNER_ID = Pap_Stats_Table::PARENTBANNERID;
	const CAMPAIGN_ID = Pap_Stats_Table::CAMPAIGNID;
	const COUNTRY_CODE = Pap_Stats_Table::COUNTRYCODE;
	const PARRENT_TRANSACTION_ID = 'parenttransid';
	const R_STATUS = 'rstatus';
	const R_TYPE = 'rtype';
	const DATE_INSERTED = Pap_Stats_Table::DATEINSERTED;
	const DATE_APPROVED = 'dateapproved';
	const PAYOUT_STATUS = 'payoutstatus';
	const REFERER_URL = 'refererurl';
	const IP = 'ip';
	const BROWSER = 'browser';
	const COMMISSION = 'commission';
	const RECURRING_COMM_ID = 'recurringcommid';
	const PAYOUTHISTORY_ID = 'payouthistoryid';
	const FIRST_CLICK_TIME = 'firstclicktime';
	const FIRST_CLICK_REFERER = 'firstclickreferer';
	const FIRST_CLICK_IP = 'firstclickip';
	const FIRST_CLICK_DATA1 = 'firstclickdata1';
	const FIRST_CLICK_DATA2 = 'firstclickdata2';
	const CLICK_COUNT = 'clickcount';
	const LAST_CLICK_TIME = 'lastclicktime';
	const LAST_CLICK_REFERER = 'lastclickreferer';
	const LAST_CLICK_IP = 'lastclickip';
	const LAST_CLICK_DATA1 = 'lastclickdata1';
	const LAST_CLICK_DATA2 = 'lastclickdata2';
	const TRACK_METHOD = 'trackmethod';
	const ORDER_ID = 'orderid';
	const PRODUCT_ID = 'productid';
	const TOTAL_COST = 'totalcost';
	const FIXED_COST = 'fixedcost';
	const DATA1 = 'data1';
    const DATA2 = 'data2';
	const DATA3 = 'data3';
	const DATA4 = 'data4';
	const DATA5 = 'data5';
	const ORIGINAL_CURRENCY_ID = 'originalcurrencyid';
	const ORIGINAL_CURRENCY_VALUE = 'originalcurrencyvalue';
	const ORIGINAL_CURRENCY_RATE = 'originalcurrencyrate';
	const TIER = 'tier';
	const COMMISSIONTYPEID = 'commtypeid';
	const COMMISSIONGROUPID = 'commissiongroupid';
	const MERCHANTNOTE = 'merchantnote';
	const SYSTEMNOTE = 'systemnote';
	const COUPON_ID = 'couponid';
	const VISITOR_ID = 'visitorid';
	const SALE_ID = 'saleid';
	const SPLIT = 'split';
	const LOGGROUPID = 'loggroupid';
    const ALLOW_FIRST_CLICK_DATA = 'allowfirstclickdata';
    const ALLOW_LAST_CLICK_DATA = 'allowlastclickdata';
	private static $instance;

	/**
	 * @return Pap_Db_Table_Transactions
	 */
	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	protected function initName() {
		$this->setName('pap_transactions');
	}

	public static function getName() {
		return self::getInstance()->name();
	}

	protected function initColumns() {
		$this->createPrimaryColumn(self::TRANSACTION_ID, 'char', 8, true);
		$this->createColumn(Pap_Stats_Table::ACCOUNTID, 'char', 8);
		$this->createColumn(self::USER_ID, 'char', 8);
		$this->createColumn(self::BANNER_ID, 'char', 8);
		$this->createColumn(self::PARRENT_BANNER_ID, 'char', 8);
		$this->createColumn(self::CAMPAIGN_ID, 'char', 8);
		$this->createColumn(self::COUNTRY_CODE, 'char', 2);
		$this->createColumn(self::PARRENT_TRANSACTION_ID, 'char', 8);
		$this->createColumn(self::R_STATUS, 'char', 1);
		$this->createColumn(self::R_TYPE, 'char', 1);
		$this->createColumn(self::DATE_INSERTED, 'datetime');
		$this->createColumn(self::DATE_APPROVED, 'datetime');
		$this->createColumn(self::PAYOUT_STATUS, 'char', 1);
		$this->createColumn(self::PAYOUTHISTORY_ID, 'char', 8);
		$this->createColumn(self::REFERER_URL, 'char');
		$this->createColumn(self::IP, 'char', 39);
		$this->createColumn(self::BROWSER, 'char', 6);
		$this->createColumn(self::COMMISSION, 'float');
		$this->createColumn(self::RECURRING_COMM_ID, 'char', 8);
		$this->createColumn(self::FIRST_CLICK_TIME, 'datetime');
		$this->createColumn(self::FIRST_CLICK_REFERER, 'char');
		$this->createColumn(self::FIRST_CLICK_IP, 'char', 39);
		$this->createColumn(self::FIRST_CLICK_DATA1, 'char', 255);
		$this->createColumn(self::FIRST_CLICK_DATA2, 'char', 255);
		$this->createColumn(self::CLICK_COUNT, 'int', 10);
		$this->createColumn(self::LAST_CLICK_TIME, 'datetime');
		$this->createColumn(self::LAST_CLICK_REFERER, 'char');
		$this->createColumn(self::LAST_CLICK_IP, 'char', 39);
		$this->createColumn(self::LAST_CLICK_DATA1, 'char', 255);
		$this->createColumn(self::LAST_CLICK_DATA2, 'char', 255);
		$this->createColumn(self::TRACK_METHOD, 'char', 1);
		$this->createColumn(self::ORDER_ID, 'char', 200);
		$this->createColumn(self::PRODUCT_ID, 'char', 200);
		$this->createColumn(self::TOTAL_COST, 'float');
		$this->createColumn(self::FIXED_COST, 'float');
		$this->createColumn(self::DATA1, 'char', 255);
		$this->createColumn(self::DATA2, 'char', 255);
		$this->createColumn(self::DATA3, 'char', 255);
		$this->createColumn(self::DATA4, 'char', 255);
		$this->createColumn(self::DATA5, 'char', 255);
		$this->createColumn(self::ORIGINAL_CURRENCY_ID, 'char', 8);
		$this->createColumn(self::ORIGINAL_CURRENCY_VALUE, 'float');
		$this->createColumn(self::ORIGINAL_CURRENCY_RATE, 'float');
		$this->createColumn(self::TIER, 'int', 10);
		$this->createColumn(self::COMMISSIONTYPEID, 'char', 8);
		$this->createColumn(self::COMMISSIONGROUPID, 'char', 8);
		$this->createColumn(self::MERCHANTNOTE, 'char', 250);
		$this->createColumn(self::SYSTEMNOTE, 'char', 250);
		$this->createColumn(self::CHANNEL, self::CHAR, 10);
		$this->createColumn(self::COUPON_ID, self::CHAR, 8);
		$this->createColumn(self::VISITOR_ID, self::CHAR, 36);
		$this->createColumn(self::SALE_ID, self::CHAR, 8);
		$this->createColumn(self::SPLIT, 'float');
		$this->createColumn(self::LOGGROUPID, self::CHAR, 16);
		$this->createColumn(self::ALLOW_FIRST_CLICK_DATA, self::CHAR);
		$this->createColumn(self::ALLOW_LAST_CLICK_DATA, self::CHAR);
	}

	/**
	 *
	 * Pap alert application handle, do not modify this source!
	 *
	 * @param String $dateFrom
	 * @param String $dateTo
	 * @param String $userId
	 * @return Gpf_Data_RecordSet
	 */
	public static function getTransactions(Pap_Stats_Params $statsParams) {
		$select = new Gpf_SqlBuilder_SelectBuilder();
		$select->select->add('tr.'.Pap_Db_Table_Transactions::USER_ID, 'userid');
		$select->select->add('au.'.Gpf_Db_Table_AuthUsers::FIRSTNAME, 'name');
		$select->select->add('au.'.Gpf_Db_Table_AuthUsers::LASTNAME, 'surname');
		$select->select->add('au.'.Gpf_Db_Table_AuthUsers::USERNAME, 'username');
		$select->select->add('pu.data1', 'weburl');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::TRANSACTION_ID, 'transid');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::TOTAL_COST, 'totalcost');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::FIXED_COST, 'fixedcost');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::ORDER_ID, 'orderid');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::PRODUCT_ID, 'productid');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::DATE_INSERTED, 'dateinserted');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::R_STATUS, 'rstatus');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::R_TYPE, 'transtype');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, 'transkind');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::PAYOUT_STATUS, 'payoutstatus');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::DATE_APPROVED, 'dateapproved');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::COMMISSION, 'commission');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::REFERER_URL, 'refererurl');
		$select->select->add('c.'.Pap_Db_Table_Campaigns::ID, 'campcategoryid');
        $select->select->add('c.'.Pap_Db_Table_Campaigns::NAME, 'campaign');
		$select->select->add('tr.data1', 'data1');
		$select->select->add('tr.data2', 'data2');
		$select->select->add('tr.data3', 'data3');
		$select->select->add('tr.'.Pap_Db_Table_Transactions::COUNTRY_CODE, 'countrycode');
		$select->from->add(Pap_Db_Table_Transactions::getName(), 'tr');
		$select->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'c',
            'tr.'.Pap_Db_Table_Transactions::CAMPAIGN_ID.'=c.'.Pap_Db_Table_Campaigns::ID);
		$select->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'pu',
            'tr.'.Pap_Db_Table_Transactions::USER_ID.'=pu.'.Pap_Db_Table_Users::ID);
		$select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu',
            'gu.'.Gpf_Db_Table_Users::ID.'=pu.'.Pap_Db_Table_Users::ACCOUNTUSERID);
		$select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au',
            'gu.'.Gpf_Db_Table_Users::AUTHID.'=au.'.Gpf_Db_Table_AuthUsers::ID);

		if ($statsParams->isDateFromDefined()) {
			$select->where->add('tr.'.Pap_Db_Table_Transactions::DATE_INSERTED, '>=', $statsParams->getDateFrom()->toDateTime());
		}
		if ($statsParams->isDateToDefined()) {
			$select->where->add('tr.'.Pap_Db_Table_Transactions::DATE_INSERTED, '<=', $statsParams->getDateTo()->toDateTime());
		}
		if ($statsParams->getAffiliateId() != '') {
			$select->where->add('tr.'.Pap_Db_Table_Transactions::USER_ID, '=', $statsParams->getAffiliateId());
		}

		return $select->getAllRows();
	}
	
	 /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    public function getStatsSelect(Pap_Stats_Params $statParams, $groupColumn, $groupColumnAlias) {
        $stats = new Pap_Stats_Computer_TransactionsStatsBuilder($statParams, $groupColumn, $groupColumnAlias);
        return $stats->getStatsSelect();
    }
}

?>
