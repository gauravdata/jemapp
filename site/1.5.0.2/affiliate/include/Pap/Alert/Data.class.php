<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: Account.class.php 21660 2008-10-16 13:14:12Z mbebjak $
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
class Pap_Alert_Data extends Gpf_Object {

    /**
     * @var Pap_Common_User
     */
    private $user;
    /**
     * @var Pap_Alert_Xml
     */
    private $xml;

    /**
     * client date from ts parameter (if set)
     *
     * @var unknown_type
     */
    private $clientDate = '';

    function __construct($username, $password) {
        $this->authenticate($username, $password);
        $this->xml = new Pap_Alert_Xml();
    }

    public function getNotification() {
        if ($this->user == null) {
            $this->xml->writeError("User doesn't exist");
            return $this->xml->toString();
        }

        $statsParams = $this->getStatsParams();
        $stats = $this->getStats($statsParams);

        $this->xml->write('stats', $stats);

        $transactions = $this->getTransactions($statsParams);

        $this->writeTransactions($transactions);

        return $this->xml->toString();
    }

    /**
     * sends transactions from the last one (if last one exists)
     *
     * @param $transaction
     */
    private function writeTransactions($transactions) {
        $lastTransId = $this->getLastTransaction($transactions);

        $reachedLastTransaction = false;
        if($lastTransId == '') {
            $reachedLastTransaction = true;
        }

        foreach ($transactions as $record) {
            $transaction = array();
            $record->rewind();

            while ($record->valid()) {
                $transaction[$record->key()] = $record->current();
                $record->next();
            }

            if($reachedLastTransaction) {
                $this->xml->write('transaction', $this->correctTransactionFormat($transaction));
            }
             
            if($transaction['transid'] == $lastTransId) {
                // from now on send all next transactions
                $reachedLastTransaction = true;
            }
        }
    }

    /**
     * returns last transaction ID
     * PAP Alert sends parameter lt (last transaction id) in URL, we check
     * if last transaction exists, otherwise it returns empty string
     *
     * @param $transactions
     * @return unknown
     */
    private function getLastTransaction($transactions) {
        if(!isset($_REQUEST['lt']) || $_REQUEST['lt'] == '') {
            return '';
        }

        foreach ($transactions as $record) {
            $transaction = array();
            $record->rewind();

            while ($record->valid()) {
                $transaction[$record->key()] = $record->current();
                $record->next();
            }

            if($transaction['transid'] == $_REQUEST['lt']) {
                return $transaction['transid'];
            }
        }

        return '';
    }

    /**
     * changes status constants from PAP4 to PAP3 types
     *
     * @param array $transaction
     */
    private function correctTransactionFormat(array $transaction) {
        $transaction['rstatus'] = $this->translateStatus($transaction['rstatus']);
        $transaction['transtype'] = $this->translateTransType($transaction['transtype']);
        $transaction['payoutstatus'] = $this->translatePayoutStatus($transaction['payoutstatus']);

        if($transaction['transkind'] == '') {
            $transaction['transkind'] = 1;
        }

        return $transaction;
    }

    public function translateStatus($pap3Status) {
        switch($pap3Status) {
            case Pap_Common_Constants::STATUS_PENDING: return 1;
            case Pap_Common_Constants::STATUS_APPROVED: return 2;
            case Pap_Common_Constants::STATUS_DECLINED: return 3;
            default:  return 1;
        }
    }

    public function translateTransType($pap3Type) {
        switch($pap3Type) {
            case Pap_Common_Constants::TYPE_SALE:
            case Pap_Common_Constants::TYPE_ACTION: return 4;
            case Pap_Common_Constants::TYPE_CLICK: return 1;
            case Pap_Common_Constants::TYPE_CPM: return 32;

            case Pap_Common_Constants::TYPE_RECURRING: return 8;
            case Pap_Common_Constants::TYPE_SIGNUP: return 16;
            case Pap_Common_Constants::TYPE_REFERRAL: return 64;
            case Pap_Common_Constants::TYPE_REFUND: return 128;
            case sPap_Common_Constants::TYPE_CHARGEBACK: return 256;

            default:  return 4;
        }
    }

    public function translatePayoutStatus($pap3Status) {
        switch($pap3Status) {
            case 'U': return 1;
            case 'P': return 2;
            default:  return 1;
        }
    }
    /**
     * @return Pap_Stats_Params
     */
    private function getStatsParams() {
        $todayDate = $this->getDateArray(new Gpf_SqlBuilder_Filter(array("", "DP", "T")));

        $statsParams = new Pap_Stats_Params();

        $statsParams->setDateFrom(new Gpf_DateTime($todayDate["dateFrom"]));
        $statsParams->setDateTo(new Gpf_DateTime($todayDate["dateTo"]));

        if ($this->user->getType() == Pap_Application::ROLETYPE_AFFILIATE) {
            $statsParams->setAffiliateId($this->user->getId());
        }

        return $statsParams;
    }

    private function getDateArray(Gpf_SqlBuilder_Filter $filter) {
        return $filter->addDateValueToArray(array());
    }
     
    private function getStats(Pap_Stats_Params $statsParams) {
        $stats = array();
        if($this->clientDate != '') {
            $stats['fordate'] = substr($this->clientDate, 0, 10); // get only date part of the date
        } else {
            $stats['fordate'] = $statsParams->getDateFrom()->toDate(); // get only date part of the date
        }

        if ($statsParams->getAffiliateId() != '') {
            $stats['usertype'] = 'affiliate';
        } else {
            $stats['usertype'] = 'merchant';
        }

        $clicks = new Pap_Stats_Clicks($statsParams);
        $imps = new Pap_Stats_Impressions($statsParams);
        $sales = new Pap_Stats_Sales($statsParams);

        $stats['today_clicks'] = $clicks->getCount()->getAll();
        $stats['today_impressions'] = $imps->getCount()->getAll();
        $stats['today_unique_impressions'] = $imps->getCount()->getUnique();
        $stats['today_sales_approved'] = $sales->getCount()->getApproved();
        $stats['today_sales_pending'] = $sales->getCount()->getPending();
        $stats['today_sales_declined'] = $sales->getCount()->getDeclined();

        if ($statsParams->getAffiliateId() != '') {
            $transactions = new Pap_Stats_Transactions($statsParams);
            $stats['approved'] = $transactions->getCommission()->getApproved();
            $stats['pending'] = $transactions->getCommission()->getPending();
            $stats['declined'] = $transactions->getCommission()->getDeclined();
        } else {
            $affiliateStats = $this->getAffiliateStats(new Pap_Stats_Params());

            $affiliateStats->rewind();
            while ($affiliateStats->valid()) {
                $stats[$affiliateStats->key()] = $this->correctNullValue($affiliateStats->current());
                $affiliateStats->next();
            }
        }

        $stats['today_leads_approved'] = 0;
        $stats['today_leads_pending'] = 0;
        $stats['today_leads_declined'] = 0;
        $stats['currency'] = 'U';

        return $stats;
    }

    private function correctNullValue($value) {
        if($value == null || $value == '') {
            return 0;
        }
        return $value;
    }

    private function getTransactions(Pap_Stats_Params $statsParams) {
        return Pap_Db_Table_Transactions::getTransactions($statsParams);
    }

    private function getAffiliateStats(Pap_Stats_Params $statsParams) {
        $dateFrom = '';
        try {
            $dateFrom = $statsParams->getDateFrom()->toDateTime();
        } catch (Gpf_Exception $e) {
        }
        return Pap_Db_Table_Users::getAffiliatesCount($dateFrom);
    }

    private function authenticate($username, $password) {
        try {
            $userId = $this->findUserId($username, $password);
            $this->user = new Pap_Common_User();
            $this->user->setId($userId);
            $this->user->load();
             
            Gpf_Session::create(new Pap_Tracking_ModuleBase());
            $this->computeTimeOffset();
             
        } catch (Gpf_Exception $e) {
            Gpf_Log::warning($e->getMessage());
            $this->user = null;
        }
    }

    private function findUserId($username, $password){
        $recordSet = $this->loadUser($username, $password)->getAllRows();
        if($recordSet->getSize() > 1){
            foreach ($recordSet as $record){
                if($record->get('roleid') == Pap_Application::DEFAULT_ROLE_MERCHANT){
                    return $record->get('userid');
                }
            }
        }else{
            return $recordSet->get(0)->get('userid');
        }
        throw new Gpf_Exception('User Not Found');
    }
    
    /**
     * parameter ts should contain client time in format
     * YYYY-MM-DD_HH:MM:SS
     *
     */
    private function computeTimeOffset() {
        if(!isset($_REQUEST['ts']) || $_REQUEST['ts'] == '') {
            return;
        }
        $serverTime = Gpf_Common_DateUtils::now();
        $serverTimestamp = strtotime($serverTime);
        $this->clientDate = str_replace('_', ' ', $_REQUEST['ts']);
        $clientTimestamp = strtotime($this->clientDate);

        Gpf_Session::getInstance()->setTimeOffset(abs($clientTimestamp - $serverTimestamp));
    }

    /**
     * @param String $userName
     * @param String $password
     *
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    private function loadUser($userName, $password) {
        $select = new Gpf_SqlBuilder_SelectBuilder();

        $select->select->add('pu.'.Pap_Db_Table_Users::ID, 'userid');
        $select->select->add('gu.'.Gpf_Db_Table_Users::ROLEID, 'roleid');

        $select->from->add(Pap_Db_Table_Users::getName(), 'pu');
        $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu',
		  'pu.'.Pap_Db_Table_Users::ACCOUNTUSERID.'=gu.'.Gpf_Db_Table_Users::ID);
        $select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(),
		  'au', 'gu.'.Gpf_Db_Table_Users::AUTHID.'=au.'.Gpf_Db_Table_AuthUsers::ID);
        $select->from->addInnerJoin(Gpf_Db_Table_Roles::getName(), 'r',
		  'gu.'.Gpf_Db_Table_Users::ROLEID.'=r.'.Gpf_Db_Table_Roles::ID);

        $select->where->add('au.'.Gpf_Db_Table_AuthUsers::USERNAME, '=', $userName);
        $select->where->add('au.'.Gpf_Db_Table_AuthUsers::PASSWORD, '=', $password);

        return $select;
    }
}
?>
