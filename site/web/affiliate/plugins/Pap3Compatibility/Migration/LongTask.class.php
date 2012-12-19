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

class Pap3Compatibility_Migration_LongTask extends Gpf_Tasks_LongTask {

	protected $param;
	protected $logName;
	protected $skip = false;

	public function __construct($logName, $skip = false, $param = '') {
		$this->logName = $logName;
		$this->skip = $skip;
		$this->param = $param;
	}
	
    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }

	public function getLogName() {
		return $this->logName;
	}

	public function getParam() {
		return $this->param;
	}

	public function getCount() {
        return 0;
	}

	public function getSkip() {
		return $this->skip;
	}

	/**
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	public function getSelect() {
		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        return $selectBuilder;
	}

    public function getName() {
        return $this->_('Data Migration');
    }

	public function execute() {
		$this->process();
	}

	/**
	 * @return Gpf_SqlBuilder_SelectBuilder
	 */
	public function getFullSelect() {
    	return null;
	}
	
	public function log($message){
	   Pap3Compatibility_Migration_OutputWriter::log($message);
	}
	
	public function logOnce($message){
	    Pap3Compatibility_Migration_OutputWriter::logOnce($message);
	}
	

    private function process() {
    	$logName = $this->getLogName();
    	$taskName = str_replace(' ', '', $logName);

        if($this->getSkip()) {
        	$this->logOnce("&nbsp;&nbsp;Migrating $logName.....".
        						"<span style=\"color:#0000ff\">SKIPPED to speed it up</span>..... DONE<br/>");
        	return;
        } else {
        	$this->logOnce("&nbsp;&nbsp;Migrating $logName.....");
        }

		if($this->isPending($taskName.'Start')) {
    		$totalCount = $this->getCount();
    		$this->log(" Total $logName count: $totalCount, starting migration ...<br/>");
    		$this->setDone();
		}

		$selectBuilder = $this->getFullSelect();
		$countPreffix = $taskName.'rec_';
		$progress = $this->getProgress();
        if (substr($progress, 0, strlen($countPreffix)) == $countPreffix) {
            $count = substr($progress, strlen($countPreffix));
            $selectBuilder->limit->set($count, '18446744073709551615');
        } else {
            $count = 0;
        }

        try {
        	$this->beforeRecordsProcessed();
        	foreach($selectBuilder->getAllRowsIterator() as $record) {
        		if($this->isPending($countPreffix.$count)) {
        			$this->processRecord($record);
        			$this->setDone();
        		}
        		$count++;
        	}

        	$this->afterRecordsProcessed();

        } catch(Gpf_Tasks_LongTaskInterrupt $e) {
        	$this->afterRecordsProcessed();
        	Pap3Compatibility_Migration_OutputWriter::log("&nbsp;&nbsp;&nbsp;&nbsp;- completed $count records...CONTINUING<br/>");
        	throw $e;
        }

        $this->afterAllRecordsProcessed();

        Pap3Compatibility_Migration_OutputWriter::log("&nbsp;&nbsp;&nbsp;&nbsp;- finished $count records..... DONE<br/>");
    }

    public function beforeRecordsProcessed() {
    }

    public function afterRecordsProcessed() {
    }

    public function afterAllRecordsProcessed() {
    }

    public function processRecord($record) {
    }
}
?>
