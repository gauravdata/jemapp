<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro plugins
 */

class ccBill_CheckRebill extends Gpf_Tasks_LongTask {
    public function getName() {
        return $this->_('Check ccBill refunds and chargebacks');
    }

    private function buildURLrequest ($time) {
        $url = 'https://datalink.ccbill.com/data/main.cgi?startTime=';
        $url .= strftime('%Y%m%d000000',$time);
        $url .= '&endTime=';
        $url .= strftime('%Y%m%d235959',$time);
        $url .= '&transactionTypes=REBILL';
        $url .= '&clientAccnum='.Gpf_Settings::get(ccBill_Config::CCBILL_ACCOUNT_NUMBER).'&clientSubacc='.Gpf_Settings::get(ccBill_Config::CCBILL_SUBACCOUNT_NUMBER).'&username='.Gpf_Settings::get(ccBill_Config::CCBILL_ACCOUNT_USERNAME).'&password=' . Gpf_Settings::get(ccBill_Config::CCBILL_ACCOUNT_PASSWORD);
        return $url;
    }

    protected function getTransactionsListForPeriod($time) {
        $ch = curl_init();
        $request = $this->buildURLrequest($time);
        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $answer = curl_exec($ch);
        $curlErr = curl_error($ch);
        if (strlen($curlErr)) {
            Gpf_Log::error('Error during recieving transaction list from ccBill, request: ' . $request);
        }
        if (strpos($answer, 'Authentication failed')) {
            $message = 'Authentification failed, request: ' . $request;
            Gpf_Log::error($message);
            throw new Gpf_Exception($message);
        }
        return $answer;
    }

    private function getTimeProgress() {
        return strftime('%Y%m%d', $this->getProgress());
    }

    private function getNextTimeProgress($timestamp) {
        $time = new Gpf_DateTime((int)$timestamp);
        $time->addDay(-1);
        return $time->toTimeStamp();
    }

    /*
     * @description Sepcific externel csv parser function - because csv rows can have different numbers of collumns
     */
    protected function csvStringToArray($string, $CSV_SEPARATOR = ';', $CSV_ENCLOSURE = '"', $CSV_LINEBREAK = "\r\n") {
        $array1 = array();
        $array2= array();
        $array1=preg_split('#'.$CSV_LINEBREAK.'#',$string);
        for($i=0;$i<count($array1);$i++){
            for($o=0;$o<strlen($array1[$i]);$o++){
                if(preg_match('#^'.$CSV_ENCLOSURE.'#',
                substr($array1[$i],$o))){
                    if(!preg_match('#^"(([^'.
                    $CSV_ENCLOSURE.']*('.
                    $CSV_ENCLOSURE.$CSV_ENCLOSURE
                    .')?[^'.$CSV_ENCLOSURE.']*)*)'.
                    $CSV_ENCLOSURE.$CSV_SEPARATOR.'#i',substr($array1[$i],$o,
                    strlen($array1[$i])),$mot)){
                        $mot[1]=substr(substr($array1[$i],$o,strlen($array1[$i])),1,-1);
                    }$o++;$o++;
                }
                else{if(!preg_match('#^([^'.$CSV_ENCLOSURE.
                $CSV_SEPARATOR.']*)'.$CSV_SEPARATOR.'#i',
                substr($array1[$i],$o,strlen($array1[$i])),$mot)){
                    $mot[1]=substr($array1[$i],$o,strlen($array1[$i]));
                }
                }
                $o=$o+strlen($mot[1]);
                $array2[$i][]=str_replace($CSV_ENCLOSURE.$CSV_ENCLOSURE,
                $CSV_ENCLOSURE,$mot[1]);
            }
        }
        return $array2;
    }

    /**
     * @return Pap_Common_Transaction
     */
    protected function findTransaction($subscriptionId, $type = Pap_Common_Constants::TYPE_SALE) {
        $transaction = new Pap_Common_Transaction();
        $transaction->setOrderId($subscriptionId);
        $transaction->setType($type);
        $transaction->setTier(1);
        $transaction->loadFromData(array(Pap_Db_Table_Transactions::ORDER_ID, Pap_Db_Table_Transactions::TIER, Pap_Db_Table_Transactions::R_TYPE));
        return $transaction;
    }

    private function rebillExists($transactionId) {
        try {
            $transaction = $this->findTransaction($transactionId);
        } catch (Gpf_Exception $e) {
            Gpf_Log::debug('Rebill exists error:' . $e->getMessage());
            return false;
        }
        return $true;
    }

    private function processRebill($rebill) {
        if ((Gpf_Settings::get(ccBill_Config::USE_RECURRING_COMMISSION)==Gpf::YES) && class_exists('Pap_Features_RecurringCommissions_RecurringCommissionsForm')) {
            $form = new Pap_Features_RecurringCommissions_RecurringCommissionsForm();
            try {
                $form->createCommissionsNoRpc($rebill[3]);
                Gpf_Log::debug('Ok - rebil processed by recurring commissions plugin');
                return;
            } catch (Exception $e) {
                Gpf_Log::debug('Processing by recurring commissions plugin failed: ' . $e->getMessage());
                return;
            }
        } else {
            Gpf_Log::debug('Processing by recurring commissions is DISABLED. Continuing the ordinary way...');
        }
        if ($this->rebillExists($rebill[5])) {
            Gpf_Log::debug('Rebill transaction id = ' . $rebill[5] . ' already exists - skipping');
            return;
        }
        try {
            $transaction = $this->findTransaction($rebill[3]);
        } catch (Gpf_Exception $e) {
            Gpf_Log::debug('Can not find original transaction for rebilling for transaction =' . $rebill[3] . ', reason: ' .$e->getMessage());
            return;
        }

        $newTransaction = new Pap_Common_Transaction();
        $newTransaction = clone $transaction;

        $date = new Gpf_DateTime(strtotime($rebill[4]));

        $newTransaction->setId('_NULL_');
        $newTransaction->setDateInserted($date->toDateTime());
        $newTransaction->setDateApproved($date->toDateTime());
        $newTransaction->setOrderId($rebill[5]);
        $newTransaction->setMerchantNote($this->_('Processed rebill from ccBill transaction: ' . $rebill[3]));
        $newTransaction->insert();

        $form = new Pap_Merchants_Transaction_TransactionsForm();
        $form->addMultiTierTransaction($transaction->getTotalCost(), $transaction->getCampaignId(), $transaction->getUserId(), $transaction->getCommissionTypeId(), $transaction->getStatus(), $newTransaction);
    }

    protected function processCsvList($csv) {
        $list = $this->csvStringToArray($csv, ',');
        if (count($list) == 0) {
            Gpf_Log::info('No rebills found for period: ' . $this->getTimeProgress());
            return;
        }
        foreach ($list as $item) {
            $time = new Gpf_DateTime();
            Gpf_Log::debug('CCBILL LOG: ' . print_r($item, true));
            if ($item[0] == 'REBILL') {
                $this->processRebill($item);
            } else {
                Gpf_Log::debug('Unsupported row: ' . print_r($item, true));
            }
        }
    }

    private function processAllRefunds() {
        $targetTime = new Gpf_DateTime();
        $targetTime->addMonth(-Gpf_Settings::get(ccBill_Config::PROCESS_REBILL_TIMEFRAME));
        $targetTimeFlag = strftime('%Y%m%d', $targetTime->toTimeStamp());
        while ($targetTimeFlag != $this->getTimeProgress()) {
            Gpf_Log::debug('ccBill rebill process task progress: ' . $this->getTimeProgress());
            $progress = $this->getProgress();
            $ansver = $this->getTransactionsListForPeriod($progress);
            if (strpos($ansver, 'Too many requests')) {
                Gpf_Log::debug('Postponding task for one more hour');
                $this->interrupt(3700);
            } else {
                $this->processCsvList($ansver);
            }
            $this->setProgress($this->getNextTimeProgress($progress));
        }
    }

    protected function execute() {
        Gpf_Log::debug('Starting ccBill rebill process task');
        if ($this->isPending('start')) {
            $this->setProgress(time());
        }
        $this->processAllRefunds();
        Gpf_Log::debug('Finished ccBill rebill process task');
        $this->setDone();
    }
}
?>
