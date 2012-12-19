<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Ivan Ivanco, Maros Galik
 *   @since Version 1.0.0
 *   $Id: TransactionsForm.class.php 27999 2010-05-04 11:39:31Z mgalik $
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
class Pap_Merchants_Transaction_TransactionsImportTask extends Gpf_Tasks_LongTask {

    const PARAM_COLUMNS = 'columns';
    const DELIMITER = 'delimiter';
    const FILENAME = 'filename';
    const SKIP_FIRST_ROW = 'skipFirstRow';
    const TRANSACTION_TYPE = 'transactionType';
    const COMPUTE_AUTOMATICALY = 'computeAutomaticaly';
    const MATCH_TRANSACTION = 'matchTransaction';
    const TRANSACTION_STATUS = 'transactionStatus';
    const SUCCESS = 'success';
    const ERROR_FILE = 'export/importErrors.csv';
    /**
     * @var Gpf_Data_RecordSet
     */
    private $names;

    /**
     * @var Gpf_Rpc_Form
     */
    protected $form;

    /**
     * @var Array
     */
    protected $errorHeader = array();

    protected $cache;
    protected $errorFile;
    protected $row = array();
    protected $mandatoryFields = array(
        Pap_Db_Table_Transactions::USER_ID => array('class'=>'Pap_Db_User','name'=>'user'),
        Pap_Db_Table_Transactions::CAMPAIGNID => array('class'=>'Pap_Db_Campaign','name'=>'campaign')
    );

    public $lastRowIndex;
    public function __construct(Gpf_Rpc_Form $form){
        $this->form = $form;
        $this->setParams($this->form->getFieldValue(self::FILENAME));
        $this->errorFile = $this->getCsvFile(Gpf_Paths::getInstance()->getCacheAccountDirectory().self::ERROR_FILE , 'a',Gpf_Csv_ImportExportService::getDelimiter($this->form->getFieldValue(self::DELIMITER)));
    }

    public function getLastRowIndex(){
        return $lastRowIndex;
    }

    protected function execute() {
        if ($this->isPending('initImport', $this->_('Initialize import'))) {
            $this->initImport();
            $this->setDone();
        }
        $this->importCsv();
    }

    public function getName() {
        return $this->_('Import Transactions');
    }

    /**
     * @return Pap_Common_Transaction
     */
    protected function getTransactionObject() {
        return new Pap_Common_Transaction();
    }

    private function initImport(){
        Gpf_Log::info('Initializing import: Deleting old error file.');
        $file = $this->getCsvFile(Gpf_Paths::getInstance()->getCacheAccountDirectory().self::ERROR_FILE);
        $file->delete();
        Gpf_Log::info('Init complete.');
    }

    /**
     * @return Gpf_Csv_File
     *
     */
    protected function getCsvFile($fileName,$mode = 'r',$delimiter = ",") {
        $csvFile = new Gpf_Csv_File($fileName, $mode);
        $csvFile->setDelimiter($delimiter);
        return $csvFile;
    }

    /**
     * @throws Gpf_DbEngine_DuplicateEntryException
     *
     */
    protected function saveTransaction(Pap_Common_Transaction $transaction, $transactionHeader, $transactionData) {
        Gpf_Log::info('Saving transaction...');
        if($this->form->getFieldValue(self::MATCH_TRANSACTION) == 'O'){
            $transaction = $this->matchTransaction($transaction, $transactionHeader, $transactionData);
        }
        Gpf_Log::info('Transaction data: ' . print_r($transactionData, true));

        foreach ($transactionHeader as $id => $column) {
            if(isset($this->mandatoryFields[$column])){
                if(!$this->validateColumn($this->mandatoryFields[$column],$transactionData[$id])){
                    return false;
                }
            }
            $transaction->set($column, $transactionData[$id]);
        }
        if($this->form->getFieldValue(self::COMPUTE_AUTOMATICALY) == Gpf::YES){
            $commission = $this->computeCommissionAutomaticaly($transactionHeader, $transactionData);
            $transaction->setCommission($commission['commission']);
            $transaction->setFixedCost($commission['fixedcost']);
        }
        $transaction = $this->setTransactionStatus($transaction, $transactionHeader, $transactionData);
        $transaction->setType(Pap_Common_Constants::TYPE_SALE);
        $transaction->save();
        Gpf_Log::info('Save complete');
    }

    protected function validateColumn($mandatoryData,$value){
        if(!$this->cache->isInCache($value,$mandatoryData['name'])){
            $object = new $mandatoryData['class']();
            $object->setId($value);
            try{
                $object->load();
                $this->cache->addToCache($value,$mandatoryData['name']);
                return true;
            } catch(Gpf_DbEngine_NoRowException $e){
                $this->addError($this->_("No " . $mandatoryData['name'] . " with Id: " . $value));
                return false;
            }
        }
        return true;
    }

    private function setTransactionStatus(Pap_Common_Transaction $transaction, $transactionHeader, $transactionData) {
        if($this->form->getFieldValue(self::TRANSACTION_STATUS) != 'F') {
            Gpf_Log::info('Setting status form file to: ' . $this->form->getFieldValue(self::TRANSACTION_STATUS));
            $transaction->setStatus($this->form->getFieldValue(self::TRANSACTION_STATUS));
            return $transaction;
        }

        $data = array_flip($transactionHeader);
        $stat = $transactionData[$data[Pap_Db_Table_Transactions::R_STATUS]];
        if($stat == Pap_Common_Constants::STATUS_PENDING || $stat == Pap_Common_Constants::STATUS_APPROVED || $stat == Pap_Common_Constants::STATUS_DECLINED){
            Gpf_Log::info('Setting status form preset to: ' . $stat);
            $transaction->set(Pap_Db_Table_Transactions::R_STATUS, $stat);
            return $transaction;
        }
        Gpf_Log::error('Invalid transaction status in import data');
        throw new Gpf_Exception('Invalid transaction status in import data');
    }

    private function interruptIfMemoryFull() {
        if ($this->checkIfMemoryIsFull(memory_get_usage())) {
            Gpf_Log::warning('Be carefull, memory was filled up so im interrupting Pap_Merchants_Transaction_TransactionsImportTask task.');
            $this->setDone();
            $this->interrupt();
        }
    }

    private function matchTransaction(Pap_Common_Transaction $transaction, $transactionHeader, $transactionData) {
        $data = array_flip($transactionHeader);
        $orderId = $transactionData[$data[Pap_Db_Table_Transactions::ORDER_ID]];

        if(trim($orderId)!=''){
            $transaction->setOrderId($orderId);
            try{
                $transaction->loadFromData(array(Pap_Db_Table_Transactions::ORDER_ID));
            } catch (Gpf_DbEngine_NoRowException $e) {
            } catch (Gpf_DbEngine_TooManyRowsException $e) {
                throw new Gpf_Exception($this->_('Too many transactions with order Id: '.$orderId));
            }
        }
        return $transaction;
    }

    private function computeCommissionAutomaticaly($transactionHeader, $transactionData) {
        $data = array_flip($transactionHeader);
        if (!isset($data[Pap_Db_Table_Transactions::CAMPAIGN_ID])) {
            throw new Gpf_Exception($this->_('Campaign ID is mandatory for automatic commissions computing'));
        }

        if (!isset($data[Pap_Db_Table_Transactions::USER_ID])) {
            throw new Gpf_Exception($this->_('User ID is mandatory for automatic commissions computing'));
        }

        $campaignId = $transactionData[$data[Pap_Db_Table_Transactions::CAMPAIGN_ID]];

        $commissionType = $this->getCommissionType($campaignId, 'S');
        $totalCost = '';
        if (isset($data[Pap_Db_Table_Transactions::TOTAL_COST])) {
            $totalCost=$transactionData[$data[Pap_Db_Table_Transactions::TOTAL_COST]];
        }

        $fixedCost = '';
        if (isset($data[Pap_Db_Table_Transactions::FIXED_COST])) {
            $fixedCost=$transactionData[$data[Pap_Db_Table_Transactions::FIXED_COST]];
        }

        $campaignForm = $this->createCampaignForm();
        $userId = $transactionData[$data[Pap_Db_Table_Transactions::USER_ID]];
        $comissionTypeId = $commissionType->getId();

        return $campaignForm->computeAutomaticCommission(
        $campaignId,
        $userId,
        $comissionTypeId,
        $totalCost, $fixedCost);
    }

    /**
     * @throws Gpf_Exception
     * @return Pap_Db_CommissionType
     */
    protected function getCommissionType($campaignId, $transactionType) {
        $campaign = new Pap_Common_Campaign();
        $campaign->setId($campaignId);
        try {
            $campaign->load();
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception($this->_('Campaign with Campaign ID: '.$campaignId.' does not exist'));
        }
        return $campaign->getCommissionTypeObject($transactionType);
    }

    /**
     * @return Pap_Merchants_Campaign_CampaignForm
     */
    protected function createCampaignForm() {
        return new Pap_Merchants_Campaign_CampaignForm();
    }

    /**
     * @throws Gpf_Tasks_LongTaskInterrupt
     */
    public function importCSV(){
        Gpf_Log::info('Importing file' . $this->form->getFieldValue(self::FILENAME));
        
        $transactionHeader = $this->getHeader();
        
        $rowsList = $this->getDataFromFile();
        
        $this->cache = new Pap_Merchants_Transaction_TransactionsImportCache();
        $i = 0;
        while($this->row = $rowsList->readArray()) {
            
            $this->interruptIfMemoryFull();
            if ($this->isPending($i, $this->_('Importing transactions'))) {
                if ($i == 0 && $this->form->getFieldValue(self::SKIP_FIRST_ROW) == Gpf::YES) {
                    Gpf_Log::info('Skipping first row');
                    $this->row[]='errorMessage';
                    $this->errorHeader = $this->row;
                    $i++;
                    continue;
                }
                if(count($this->row)<=1) {
                    Gpf_Log::info('Skipping empty row ' . $i);
                    $i++;
                    continue;
                }
                $transaction = $this->getTransactionObject();
                try {
                    $this->saveTransaction($transaction, $transactionHeader, $this->row);
                } catch (Gpf_DbEngine_DuplicateEntryException $e) {
                    $this->addError($this->_('Duplicate row'));
                } catch (Gpf_Exception $e){
                    $this->addError($this->_($e->getMessage()));
                }
                $this->setDone();
            }
            $i++;
        }

        Gpf_Log::info('Import complete');
        if ($this->errorFile !== null) {
            $this->errorFile->close();
            if($this->errorFile->isExists()) {
                Gpf_Log::warning('Error occured during import, please check ' . Gpf_Paths::getInstance()->getCacheAccountDirectory().self::ERROR_FILE);
                $this->form->setInfoMessage("Import was not successful<br /><a href='" .
                Gpf_Paths::getInstance()->getCacheAccountDirectory().self::ERROR_FILE.
	            "'>" . $this->_("Download unimported .csv") . "</a><br />
	            (error messages are at the end of each line)");
            } else{
                $this->form->setInfoMessage("Import was successful");
            }
        }
        else{
            $this->form->setInfoMessage("Import was successful");
        }

        $this->form->setErrorMessage($this->_('Import was not successful.'));

        $this->form->setSuccessful();
        $this->cache->clearCache();
        return $this->form;
    }


    protected function getDataFromFile(){
        return $this->getCsvFile($this->form->getFieldValue(self::FILENAME), 'r', Gpf_Csv_ImportExportService::getDelimiter($this->form->getFieldValue(self::DELIMITER)));
    }

    private function getHeader(){
        $transactionHeader = $this->form->getFieldValue(self::PARAM_COLUMNS);
        array_shift($transactionHeader);
        foreach($transactionHeader as $key => $header){
            $header[0] = str_replace("field","",$header[0]);
            $transactionHeaderNew[(int)$header[0]]=$header[1];
        }
        return $transactionHeaderNew;
    }

    private function addError($message){
        if (!$this->errorFile->isExists()) {
            $this->getCsvFile($this->form->getFieldValue(self::FILENAME), 'a', Gpf_Csv_ImportExportService::getDelimiter($this->form->getFieldValue(self::DELIMITER)));
            $this->errorFile->writeArray($this->errorHeader);
        }
        $this->row[] = $message;
        $this->errorFile->writeArray($this->row);
    }
}
?>
