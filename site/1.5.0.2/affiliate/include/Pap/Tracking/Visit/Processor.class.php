<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik, Michal Bebjak
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
 * @package PostAffiliate
 */
class Pap_Tracking_Visit_Processor extends Gpf_Tasks_LongTask {

    const PROGRESS_START = "start";
    const MAX_WORKERS_COUNT = 256;

    private $visitorCache = array();

    /**
     * @var Pap_Tracking_Visit_VisitorAffiliateCache
     */
    protected $visitorAffiliateCache;

    /**
     * @var array<Pap_Tracking_Common_VisitProcessor>
     */
    protected $visitProcessors;

    /**
     * @var Pap_Tracking_BackwardCompatibility_BackwardCompatibilityProcessor
     */
    private $backwardCompatibilityProcessor;

    public function __construct() {
        $this->visitorAffiliateCache = new Pap_Tracking_Visit_VisitorAffiliateCache();
        $this->visitProcessors = $this->createProcessors($this->visitorAffiliateCache);
        $this->setProgress(self::PROGRESS_START);
    }

    public static function getVisitorIdLength() {
        if (defined('VISITOR_ID_LENGTH')) {
            return VISITOR_ID_LENGTH;
        }
        return 32;
    }

    public function createWorker($workingRangeFrom, $workingRangeTo) {
        $task = new Pap_Tracking_Visit_Processor();
        $this->debug('Creating new worker Pap_Tracking_Visit_Processor for range:' . $workingRangeFrom . '-' . $workingRangeTo);
        $task->setWorkingArea($workingRangeFrom, $workingRangeTo);
        $task->insertTask();
    }

    protected function splitMe() {
        $this->debug('I can not split my self');
    }

    private function otherThanMyVisitsExists() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->where->add(Pap_Db_Table_Visits::RSTATUS, '=', Pap_Db_Visit::UNPROCESSED);
        $selectBuilder->where->add(Pap_Db_Table_Visits::VISITORID_HASH, '>', 0);
        $count = $this->getTableRowsCount($selectBuilder, Pap_Db_Table_Visits::getName($this->computeProcessTable(Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT, true))));
        $this->debug('Visits that are for bigger hash than 0:' . $count);
        return $count > 0;
    }

    protected function createSlaves() {
        if (!$this->otherThanMyVisitsExists()) {
            $this->debug('No more slaves needed - skipping');
            return;
        }
        $this->task->setWorkingAreaFrom(0);
        $this->task->setWorkingAreaTo(0);
        $this->task->update();
        for ($a=1;$a<$this->getMaxWorkersCount(); $a++) {
            if (!$this->slaveExist($a,$a)) {
                $this->createWorker($a, $a);
            }
        }
    }

    protected function getClassName() {
        return get_class();
    }

    protected function getAvaliableWorkersCount() {
        return self::MAX_WORKERS_COUNT - $this->getActualWorkersCount();
    }

    protected function getMaxWorkersCount() {
        return self::MAX_WORKERS_COUNT;
    }

    /**
     * @param $visitorAffiliateCache
     * @return array<Pap_Tracking_Common_VisitProcessor>
     */
    protected function createProcessors(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
        $visitProcessors = array();
        $visitProcessors[] = new Pap_Tracking_BackwardCompatibility_BackwardCompatibilityProcessor($visitorAffiliateCache);
        $visitProcessors[] = new Pap_Tracking_Click_ClickProcessor($visitorAffiliateCache);
        $visitProcessors[] = new Pap_Tracking_Action_ActionProcessor($visitorAffiliateCache);
        return $visitProcessors;
    }

    public function getName() {
        return $this->_('Visit log processor');
    }

    public function runOnline(Pap_Db_Visit $visit) {
        $this->processVisit($visit);
        $this->saveVisitChanges();
    }

    protected function interrupt($sleepSeconds = 0) {
        $this->saveVisitChanges();
        parent::interrupt($sleepSeconds);
    }

    protected function saveVisitChanges() {
        foreach ($this->visitProcessors as $visitHandler) {
            $visitHandler->saveChanges();
        }
        $this->visitorAffiliateCache->saveChanges();
    }

    protected function doMasterWorkWhenSyncPointReached() {
        $this->debug('master at sync!!!');
        if ($this->getProgress() == self::PROGRESS_START ) {
            $this->optimizeTable($this->computeProcessTable(Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT, true)));
            $this->switchTables();
        }
    }

    protected function doSlaveWorkAfterExecute() {
        $this->debug('Worker finished his work...');
        try {
            $this->saveVisitChanges();
        } catch (Gpf_Exception $e) {
            $this->debug('Error when saving visit changes: ' . $e->getMessage());
        }
        $this->setDone();
        $this->debug('Worker finished his work...2');
    }

    protected function doMasterWorkAfterExecute() {
        $this->setProgress(self::PROGRESS_START);
        $this->debug('interrupting...');
        $this->interrupt(0);
    }

    protected function doAfterLongTaskInterrupt() {
        //do nothing - we do not want to update our task after long task interrupt occurs
    }

    protected function doSlaveWorkWhenSyncPointReached() {
        $this->setDone();
        $this->forceFinishTask();
        $this->interrupt();
    }

    private function computeProcessTable($inputTableNumber) {
        return ($inputTableNumber + 2) % 3;
    }

    protected function execute() {
        $this->debug('Starting visit processor');

        $this->setProgress(0);
        $processedTableIndex = $this->computeProcessTable(Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT, true));
        $this->debug('Processing visits from ' . $processedTableIndex);
        $this->processAllVisits($processedTableIndex);
        $this->debug('Processing is over now');
    }

    protected function syncPointReached() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->from->add(Pap_Db_Table_Visits::getName($this->computeProcessTable(Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT, true))));
        $select->select->add('count(*)', 'cnt');
        $select->where->add(Pap_Db_Table_Visits::RSTATUS, '=', Pap_Db_Visit::UNPROCESSED);
        $record = $select->getOneRow();
        if ($record->get('cnt') > 0) {
            return false;
        }
        return true;
    }

    protected function processAllVisits($processedTableIndex) {
        $progress = $this->getProgress();
        while (($visit = $this->getNextVisit($processedTableIndex)) !== false) {
            $this->setInCronProcessingStatus($visit);
            $this->processAndUpdateVisit($visit);
            $this->setProgress(++$progress);
            $this->checkInterruption();
        }
    }

    private function setInCronProcessingStatus(Pap_Db_Visit $visit) {
        $logger = Pap_Logger::create(Pap_Common_Constants::TYPE_ACTION);
        $logger->debug('Before visit processing - visitid: '.$visit->getId().' set status IN CRON PROCESSING');
        $visit->setInCronProcessing();
        $visit->update(array(Pap_Db_Table_Visits::RSTATUS));
    }

    protected function canBeSplit() {
        return $this->imMasterWorker(); 
    }

    protected function processAndUpdateVisit(Pap_Db_Visit $visit) {
        try {
            $this->processVisit($visit);
        } catch (Exception $e) {
            Gpf_Log::error("Visit processing failed ($e)");
        }
        $visit->delete();
    }

    /**
     * @throws Exception
     */
    protected function processVisit(Pap_Db_Visit $visit) {
        $visitorId = $visit->getVisitorId();
        if ($visit->getVisitorIdHash() >= self::MAX_WORKERS_COUNT) {
            $visit->setVisitorIdHash($visit->getVisitorIdHash() % self::MAX_WORKERS_COUNT);
        }

        $visit->setNewVisitor($this->isNewVisitor($visitorId));

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.PapTrackingVisitProcessor.processVisit', $visit);

        foreach ($this->visitProcessors as $visitHandler) {
            $visitHandler->process($visit);
        }
    }

    protected function isNewVisitor($visitorId) {
        if (isset($this->visitorCache[$visitorId])) {
            return false;
        }
        $this->visitorCache[$visitorId] = true;

        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->from->add(Pap_Db_Table_VisitorAffiliates::getName());
        $select->select->add(Pap_Db_Table_VisitorAffiliates::VISITORID);
        $select->where->add(Pap_Db_Table_VisitorAffiliates::VISITORID, '=', $visitorId);
        $select->limit->set(0, 1);
        try {
            $select->getOneRow();
            return false;
        } catch (Gpf_DbEngine_NoRowException $e) {
            return true;
        }
    }

    protected function optimizeTable($processedTableIndex) {
        $this->debug('Optimizing table num. ' . $processedTableIndex);
        Pap_Db_Table_Visits::getInstance($processedTableIndex)->optimize();
    }

    /**
     * switching tables for writing and processing impressions
     * table states: I - visits are written to this table
     *               W - table is waiting to be processed
     *               P - table should be processed
     *
     * this method switches: I -> W, W -> P, P -> I
     */
    protected function switchTables() {
        $inputTableTo = (Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT, true)+2) % 3;
        $this->debug('Setting input from '.Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT, true).' to '. $inputTableTo . '.');
        Gpf_Settings::set(Pap_Settings::VISITS_TABLE_INPUT, $inputTableTo, true);
    }

    /**
     * @return Pap_Db_Visit
     */
    protected function getNextVisit($processedTableIndex) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->from->add(Pap_Db_Table_Visits::getName($processedTableIndex));
        $selectBuilder->select->addAll(Pap_Db_Table_Visits::getInstance($processedTableIndex));
        $selectBuilder->where->add(Pap_Db_Table_Visits::RSTATUS, '=', Pap_Db_Visit::UNPROCESSED);
        $selectBuilder->where->add(Pap_Db_Table_Visits::VISITORID_HASH, '=', $this->task->getWorkingAreaFrom());
        $selectBuilder->limit->set(0, 1);
        $visit = new Pap_Db_Visit($processedTableIndex);
        try {
            $visit->fillFromSelect($selectBuilder);
            return $visit;
        } catch (Gpf_Exception $e) {
            return false;
        }
    }

    protected function loadTask() {
        $this->task->setClassName(get_class($this));
        $this->task->setNull(Gpf_Db_Table_Tasks::DATEFINISHED);
        $this->task->loadFromData();
    }

    protected function debug($message) {
        $message .= ' (WORKER_'.$this->task->getWorkingAreaFrom().'-'.$this->task->getWorkingAreaTo() . ')';
        parent::debug($message);
    }
}
?>
