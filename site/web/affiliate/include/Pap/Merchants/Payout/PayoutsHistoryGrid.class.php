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
class Pap_Merchants_Payout_PayoutsHistoryGrid extends Gpf_View_GridService {

    private $searchString;

    protected function initViewColumns() {
        $this->addViewColumn(Pap_Db_Table_PayoutsHistory::ID, $this->_("Id", true));
        $this->addViewColumn(Pap_Db_Table_PayoutsHistory::DATEINSERTED, $this->_("Date paid"), true, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn("users", $this->_("Payees"), false);
        $this->addViewColumn(Pap_Db_Table_Payouts::AMOUNT, $this->_("Total amount"), false);
        $this->addViewColumn(Pap_Db_Table_PayoutsHistory::MERCHANT_NOTE, $this->_("Merchant note"), true);
        $this->addViewColumn(Pap_Db_Table_PayoutsHistory::AFFILIATE_NOTE, $this->_("Affiliate note"), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn("ph.".Pap_Db_Table_PayoutsHistory::ID);
        $this->addDataColumn(Pap_Db_Table_PayoutsHistory::ID, "ph.".Pap_Db_Table_PayoutsHistory::ID);
        $this->addDataColumn(Pap_Db_Table_PayoutsHistory::DATEINSERTED, "ph.".Pap_Db_Table_PayoutsHistory::DATEINSERTED);
        $this->addDataColumn(Pap_Db_Table_PayoutsHistory::MERCHANT_NOTE, "ph.".Pap_Db_Table_PayoutsHistory::MERCHANT_NOTE);
        $this->addDataColumn(Pap_Db_Table_PayoutsHistory::AFFILIATE_NOTE, "ph.".Pap_Db_Table_PayoutsHistory::AFFILIATE_NOTE);
        $this->addDataColumn(Pap_Db_Table_PayoutsHistory::ACCOUNTID, "ph.".Pap_Db_Table_PayoutsHistory::ACCOUNTID);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Pap_Db_Table_PayoutsHistory::DATEINSERTED, '', 'A');
        $this->addDefaultViewColumn(Pap_Db_Table_Payouts::AMOUNT, '', 'A');
        $this->addDefaultViewColumn("users", '', 'A');
        $this->addDefaultViewColumn(Pap_Db_Table_PayoutsHistory::MERCHANT_NOTE, '', 'N');
        $this->addDefaultViewColumn(Pap_Db_Table_PayoutsHistory::AFFILIATE_NOTE, '', 'N');
        $this->addDefaultViewColumn(self::ACTIONS, '', 'N');
    }

    protected function afterExecute(Gpf_Data_RecordSet $inputResult) {
        $inputResult->addColumn(Pap_Db_Table_Payouts::AMOUNT);
        $inputResult->addColumn('users');

        $payoutHistoryIds = array();
        foreach ($inputResult as $record) {
            $payoutHistoryIds[] = $record->get('id');
        }

        $outputResult = $inputResult->toShalowRecordSet();

        $payeeData = $this->getPayeeData($payoutHistoryIds);

        foreach ($inputResult as $record) {
            $payoutHistoryId = $record->get('id');
            if(!isset($payeeData[$payoutHistoryId])) {
                $record->set(Pap_Db_Table_Payouts::AMOUNT, 0);
                $record->set("users", "");
            } else {
                if (!$this->matchesSearchCriteria($record, $payeeData[$payoutHistoryId])) {
                    $this->_count--;
                    continue;
                }
                $record->set("amount", $payeeData[$payoutHistoryId]['amount']);
                $record->set("users", $payeeData[$payoutHistoryId]['usersCount'].' '.
                ($payeeData[$payoutHistoryId]['usersCount'] > 1 ? $this->_("payees") : $this->_("payee")));
            }
            $outputResult->addRecord($record);
        }
        return $outputResult;
    }

    private function matchesSearchCriteria(Gpf_Data_Record $payoutHistory, $payoutHistoryData) {
        if ($this->searchString == '' && !$this->filters->isFilter('userid')) {
            return true;
        }
        if (strstr($payoutHistory->get(Pap_Db_Table_PayoutsHistory::MERCHANT_NOTE), $this->searchString) != false ||
            strstr($payoutHistory->get(Pap_Db_Table_PayoutsHistory::AFFILIATE_NOTE), $this->searchString) != false) {
            return true;
        }
        if (strstr($payoutHistory->get('id'), $this->searchString) != false ||
            strstr($this->searchString, $payoutHistory->get('id')) != false) {
            return true;
        }
        foreach ($payoutHistoryData["users"] as $user) {
            if ((strstr($user[Gpf_Db_Table_AuthUsers::USERNAME], $this->searchString) !== false) ||
                (strstr($user[Gpf_Db_Table_AuthUsers::FIRSTNAME], $this->searchString) !== false) ||
                (strstr($user[Gpf_Db_Table_AuthUsers::LASTNAME], $this->searchString) !== false) ||
                (strstr($user[Pap_Db_Table_Users::ID], $this->searchString) !== false) ||
                $this->isInAffiliateFilter($user[Pap_Db_Table_Users::ID])) {
                    return true;
            }
        }
        return false;
    }

    private function isInAffiliateFilter($userId) {
        if ($this->filters->isFilter('userid')) {
            if ($userId == $this->filters->getFilterValue('userid')) {
                return true;
            }
        }
        return false;
    }

    function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_PayoutsHistory::getName(), "ph");
    }

    protected function buildWhere() {
        parent::buildWhere();
        Gpf_Plugins_Engine::extensionPoint('AffiliateNetwork.modifyWhere',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder, new Gpf_Data_Record(array('columnPrefix'), array('ph'))));
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        switch ($filter->getCode()) {
            case "search":
                $this->addSearch($filter);
                break;
        }
    }

    private function addSearch(Gpf_SqlBuilder_Filter $filter) {
        $this->searchString = $filter->getValue();
    }

    // TODO - IIF Export Format - easy solution for adding other export button than CSV, if needed other formats need to refactor
    protected function addAffiliateInfo(Gpf_Data_Record $row) {
        $row->set('users', $this->getPayeeData(array($row->get('payouthistoryid'))));
        return $row;
    }

    private function getPayeeData($payoutHistoryIds) {
        if(!is_array($payoutHistoryIds) || count($payoutHistoryIds) == 0) {
            return array();
        }

        $rsPayouts = $this->getPayoutsForHistoryIds($payoutHistoryIds);
        $payeeData = array();
        foreach ($rsPayouts as $record) {
            if(!isset($payeeData[$record->get("payouthistoryid")])) {
                $payeeData[$record->get(Pap_Db_Table_PayoutsHistory::ID)]['users'] = array();
                $payeeData[$record->get(Pap_Db_Table_PayoutsHistory::ID)]['usersCount'] = 0;
                $payeeData[$record->get(Pap_Db_Table_PayoutsHistory::ID)]['amount'] = 0;
            }

            $payeeData[$record->get("payouthistoryid")]['users'][] = $this->createUserArrayFromRecord($record);
            $payeeData[$record->get("payouthistoryid")]['usersCount']++;
            $payeeData[$record->get("payouthistoryid")]['amount'] += $record->get('amount');
        }

        return $payeeData;
    }

    /**
     * @param Gpf_Data_Record $record
     * @return array
     */
    private function createUserArrayFromRecord(Gpf_Data_Record $record) {
        $userArray = array();
        $userArray[Pap_Db_Table_Users::ID] = $record->get(Pap_Db_Table_Users::ID);
        $userArray[Pap_Db_Table_Users::getDataColumnName(3)] = $record->get(Pap_Db_Table_Users::getDataColumnName(3));
        $userArray[Pap_Db_Table_Users::getDataColumnName(4)] = $record->get(Pap_Db_Table_Users::getDataColumnName(4));
        $userArray[Pap_Db_Table_Users::getDataColumnName(5)] = $record->get(Pap_Db_Table_Users::getDataColumnName(5));
        $userArray[Pap_Db_Table_Users::getDataColumnName(6)] = $record->get(Pap_Db_Table_Users::getDataColumnName(6));
        $userArray[Pap_Db_Table_Users::getDataColumnName(7)] = $record->get(Pap_Db_Table_Users::getDataColumnName(7));
        $userArray[Pap_Db_Table_Users::getDataColumnName(8)] = $record->get(Pap_Db_Table_Users::getDataColumnName(8));
        $userArray[Pap_Db_Table_Users::getDataColumnName(9)] = $record->get(Pap_Db_Table_Users::getDataColumnName(9));
        $userArray[Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL] = $record->get(Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL);
        $userArray[Gpf_Db_Table_AuthUsers::USERNAME] = $record->get(Gpf_Db_Table_AuthUsers::USERNAME);
        $userArray[Gpf_Db_Table_AuthUsers::FIRSTNAME] = $record->get(Gpf_Db_Table_AuthUsers::FIRSTNAME);
        $userArray[Gpf_Db_Table_AuthUsers::LASTNAME] = $record->get(Gpf_Db_Table_AuthUsers::LASTNAME);
        $userArray[Pap_Db_Table_Payouts::AMOUNT] = $record->get(Pap_Db_Table_Payouts::AMOUNT);
        $userArray[Gpf_Db_Table_Currencies::SYMBOL] = $record->get(Gpf_Db_Table_Currencies::SYMBOL);
        $userArray[Gpf_Db_Table_Currencies::WHEREDISPLAY] = $record->get(Gpf_Db_Table_Currencies::WHEREDISPLAY);

        return $userArray;
    }

    private function getUsersFromPayouts($payoutHistoryIds) {
        if(!is_array($payoutHistoryIds) || count($payoutHistoryIds) == 0) {
            return array();
        }

        $rsPayouts = $this->getPayoutsForHistoryIds($payoutHistoryIds);
        $users = array();
        foreach ($rsPayouts as $record) {

            $userArray = $this->createUserArrayFromRecord($record);
            
            if (!$this->isUserInArray($users, $userArray['username'])) {
                $users[] = $userArray; 
            }
        }
        return $users;
    }

    private function isUserInArray($users, $username) {
        foreach ($users as $user) {
            if ($user['username'] == $username) {
                return true;
            }
        }
        return false;
    }

    private function getPayoutsForHistoryIds($payoutHistoryIds) {
        $result = new Gpf_Data_RecordSet();

        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(Pap_Db_Table_Payouts::PAYOUT_HISTORY_ID, Pap_Db_Table_Payouts::PAYOUT_HISTORY_ID);
        $selectBuilder->select->add(Pap_Db_Table_Payouts::AMOUNT, Pap_Db_Table_Payouts::AMOUNT);
        $selectBuilder->select->add(Gpf_Db_Table_AuthUsers::USERNAME, Gpf_Db_Table_AuthUsers::USERNAME, 'au');
        $selectBuilder->select->add(Gpf_Db_Table_AuthUsers::FIRSTNAME, Gpf_Db_Table_AuthUsers::FIRSTNAME, 'au');
        $selectBuilder->select->add(Gpf_Db_Table_AuthUsers::LASTNAME, Gpf_Db_Table_AuthUsers::LASTNAME, 'au');
        $selectBuilder->select->add(Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL, Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL, 'au');
        $selectBuilder->select->add(Pap_Db_Table_Users::ID,  Pap_Db_Table_Users::ID, 'pu');
        $selectBuilder->select->add(Pap_Db_Table_Users::getDataColumnName(3), Pap_Db_Table_Users::getDataColumnName(3), 'pu');
        $selectBuilder->select->add(Pap_Db_Table_Users::getDataColumnName(4), Pap_Db_Table_Users::getDataColumnName(4), 'pu');
        $selectBuilder->select->add(Pap_Db_Table_Users::getDataColumnName(5), Pap_Db_Table_Users::getDataColumnName(5), 'pu');
        $selectBuilder->select->add(Pap_Db_Table_Users::getDataColumnName(6), Pap_Db_Table_Users::getDataColumnName(6), 'pu');
        $selectBuilder->select->add(Pap_Db_Table_Users::getDataColumnName(7), Pap_Db_Table_Users::getDataColumnName(7), 'pu');
        $selectBuilder->select->add(Pap_Db_Table_Users::getDataColumnName(8), Pap_Db_Table_Users::getDataColumnName(8), 'pu');
        $selectBuilder->select->add(Pap_Db_Table_Users::getDataColumnName(9), Pap_Db_Table_Users::getDataColumnName(9), 'pu');
        $selectBuilder->select->add(Gpf_Db_Table_Currencies::SYMBOL, Gpf_Db_Table_Currencies::SYMBOL, 'c');
        $selectBuilder->select->add(Gpf_Db_Table_Currencies::WHEREDISPLAY, Gpf_Db_Table_Currencies::WHEREDISPLAY, 'c');
        $selectBuilder->from->add(Pap_Db_Table_Payouts::getName(), 'p');
        $selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'pu', 'p.userid = pu.userid');
        $selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu', 'pu.accountuserid = gu.accountuserid');
        $selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au', 'gu.authid = au.authid');
        $selectBuilder->from->addLeftJoin(Gpf_Db_Table_Currencies::getName(), 'c', 'p.currencyid = c.currencyid');

        $selectBuilder->where->add(Pap_Db_Table_Payouts::PAYOUT_HISTORY_ID, 'IN', $payoutHistoryIds);

        $result->load($selectBuilder);

        return $result;
    }

    /**
     * @service pay_affiliate read
     * @param $fields
     * @throws Gpf_Exception
     */
    public function payeesDetails(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);
        $idFilter = $data->getFilters()->getFilter('id');
        if (sizeof($data) != 1) {
            throw new Gpf_Exception('No id specified');
        }
        $id = $idFilter[0]->getValue();
        $payeeData = $this->getPayeeData(array($id));
        $i = 0;
        foreach($payeeData as $id => $payee) {
            foreach($payee['users'] as $user) {
                $payeeDataObject = new Gpf_Rpc_Data();
                $payeeDataObject->setValue(Gpf_Db_Table_AuthUsers::USERNAME, $user[Gpf_Db_Table_AuthUsers::USERNAME]);
                $payeeDataObject->setValue(Gpf_Db_Table_AuthUsers::FIRSTNAME, $user[Gpf_Db_Table_AuthUsers::FIRSTNAME]);
                $payeeDataObject->setValue(Gpf_Db_Table_AuthUsers::LASTNAME, $user[Gpf_Db_Table_AuthUsers::LASTNAME]);
                $payeeDataObject->setValue(Pap_Db_Table_Payouts::AMOUNT, $user[Pap_Db_Table_Payouts::AMOUNT]);
                $payeeDataObject->setValue(Pap_Db_Table_Users::getDataColumnName(3), $user[Pap_Db_Table_Users::getDataColumnName(3)]);
                $payeeDataObject->setValue(Pap_Db_Table_Users::getDataColumnName(4), $user[Pap_Db_Table_Users::getDataColumnName(4)]);
                $payeeDataObject->setValue(Pap_Db_Table_Users::getDataColumnName(5), $user[Pap_Db_Table_Users::getDataColumnName(5)]);
                $payeeDataObject->setValue(Pap_Db_Table_Users::getDataColumnName(6), $user[Pap_Db_Table_Users::getDataColumnName(6)]);
                $payeeDataObject->setValue(Pap_Db_Table_Users::getDataColumnName(7), $user[Pap_Db_Table_Users::getDataColumnName(7)]);
                $payeeDataObject->setValue(Pap_Db_Table_Users::getDataColumnName(8), $user[Pap_Db_Table_Users::getDataColumnName(8)]);
                $payeeDataObject->setValue(Pap_Db_Table_Users::getDataColumnName(9), $user[Pap_Db_Table_Users::getDataColumnName(9)]);
                $data->setValue("user$i", $payeeDataObject->toObject());
                $i++;
            }
        }

        return $data;
    }

    /**
     * @service payout_history read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }

    /**
     * @service payout_history read
     * @return Gpf_Rpc_Serializable
     */
    public function getRowCount(Gpf_Rpc_Params $params) {
        $this->getRows($params);
        return $this->createGridResponse(new Gpf_Data_RecordSet());
    }

    /**
     * @service payout_history export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        $this->initParamsForCSVFile($params);

        $views = $params->get("columns");
        $header = array();
        for ($i = 2; $i < count($views); $i++) {
            if ($views[$i][0] === self::ACTIONS) {
                continue;
            }
            $header[] = $views[$i][0];
        }
        if ($this->_fileName != null) {
            $fileName = $this->_fileName.".csv";
        } else {
            $fileName = "grid.csv";
        }

        $viewResult = new Gpf_Data_RecordSet();
        $viewResult->setHeader($header);

        $result = $this->getResultForCSV();

        $csvGenerator = new Gpf_Csv_GeneratorResponse($fileName, $header, null);
        if ($result->getSize() > 0) {
            foreach ($result as $record) {
               $row = new Gpf_Data_Record($header);
               foreach ($header as $column) {
                  $row->set($column, $record->get($column));
               }
               $csvGenerator->add($row);
            }
        }

        return $csvGenerator->getFile();
    }

    // TODO - IIF Export Format - easy solution for adding other export button than CSV, if needed other formats need to refactor
    /**
     * @service payout_history export
     * @return Gpf_Rpc_Serializable
     */
    public function getIIFFile(Gpf_Rpc_Params $params) {
        $this->initParamsForCSVFile($params);

        $views = $params->get("columns");
        $header = array();
        for ($i = 2; $i < count($views); $i++) {
            if ($views[$i][0] === self::ACTIONS) {
                continue;
            }
            $header[] = $views[$i][0];
        }
        if (!in_array('payouthistoryid', $header)) {
            $header[] = 'payouthistoryid';
        }
        if (!in_array('amount', $header)) {
            $header[] = 'amount';
        }
        if (!in_array('merchantnote', $header)) {
            $header[] = 'merchantnote';
        }
        if (!in_array('affiliatenote', $header)) {
            $header[] = 'affiliatenote';
        }

        $viewResult = new Gpf_Data_RecordSet();
        $viewResult->setHeader($header);

        $payoutHistoryIds = array();

        $result = $this->getResultForCSV();

        if ($result->getSize() > 0) {
            foreach ($result as $record) {
               $row = new Gpf_Data_Record($header);
               foreach ($header as $column) {
                  $row->set($column, $record->get($column));
               }
               $row = $this->addAffiliateInfo($row);
               $viewResult->addRecord($row);
               $payoutHistoryIds[] = $row->get('payouthistoryid');
            }
        }

        if ($this->_fileName != null) {
            $fileName = $this->_fileName.".iif";
        } else {
            $fileName = "grid.iif";
        }

        $affiliates = $this->getUsersFromPayouts($payoutHistoryIds);

        $iifGenerator = new QuickBooks_GeneratorResponse($fileName, $viewResult, $affiliates);
        return $iifGenerator->generateFile();
    }
}
?>
