<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
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
 * @package PostAffiliatePro
 */

class Pap3Compatibility_Migration_TaskTransactions extends Gpf_Tasks_LongTask {

    public function getName() {
        return $this->_('Migrate transactions');
    }

    public function execute() {
		Pap3Compatibility_Migration_OutputWriter::logOnce("Migrating transactions<br/>");

   		$time1 = microtime();

    	$this->migrateCommissions();
    	$this->migrateClicks();
    	$this->migrateImpressions();

    	$time2 = microtime();
		Pap3Compatibility_Migration_OutputWriter::logDone($time1, $time2);
    }
    
    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }

    private function migrateCommissions() {
    	if($this->isPending('Pap3Compatibility_Migration_TaskSalesCommissions')) {
    		$task = new Pap3Compatibility_Migration_TaskSalesCommissions("sales commissions", false);
    		$task->run();
    		$this->setDone();
    	}

    	if($this->isPending('Pap3Compatibility_Migration_TaskClicksImpsCommissionsclicks')) {
    		$task = new Pap3Compatibility_Migration_TaskClicksImpsCommissions("clicks commissions", false, Pap3Compatibility_Migration_Pap3Constants::TRANSTYPE_CLICK);
    		$task->run();
    		$this->setDone();
    	}

    	if($this->isPending('Pap3Compatibility_Migration_TaskClicksImpsCommissionscpm')) {
    		$task = new Pap3Compatibility_Migration_TaskClicksImpsCommissions("cpm commissions", false, Pap3Compatibility_Migration_Pap3Constants::TRANSTYPE_CPM);
    		$task->run();
    		$this->setDone();
    	}

        if($this->isPending('Pap3Compatibility_Migration_TaskSpecialCommissions')) {
    		$task = new Pap3Compatibility_Migration_TaskSpecialCommissions("special commissions", false);
    		$task->run();
    		$this->setDone();
    	}
    }

	private function migrateClicks() {
    	if($this->isPending('Pap3Compatibility_Migration_TaskClickCounts')) {
			$task = new Pap3Compatibility_Migration_TaskClickCounts("click counts", false);
    		$task->run();
    		$this->setDone();
    	}
	}

	private function migrateImpressions() {
    	if($this->isPending('Pap3Compatibility_Migration_TaskImpCounts')) {
			$task = new Pap3Compatibility_Migration_TaskImpCounts("imp counts", false);
    		$task->run();
    		$this->setDone();
    	}
	}
}
?>
