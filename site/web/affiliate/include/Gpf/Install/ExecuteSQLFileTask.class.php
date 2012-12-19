<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Install_ExecuteSQLFileTask extends Gpf_Tasks_LongTask {

	/**
	 * @var Gpf_Install_SqlFile
	 */
	private $sqlFile;

	public function __construct(Gpf_Install_SqlFile $sqlFile) {
		$this->sqlFile = $sqlFile;
		$this->setParams($sqlFile->getFileName());
	}

	/**
	 * @return Gpf_Tasks_Task
	 */
	protected function createTask() {
		return new Gpf_Tasks_SessionTask();
	}

    public function getName() {
        return $this->_('Create database');
    }

	protected function execute() {
		$this->executeSqlFile();
	}

	private function executeSqlFile() {
		$position = 0;
		foreach(explode(';', $this->sqlFile->getContent()) as $statement) {
			if(strlen(trim($statement))) {
				try {
					$this->executeSqlStatement(trim($statement), $position);
				} catch (Gpf_DbEngine_SqlException $e) {
					throw new Gpf_Exception($this->_('Error during database creation.') . $e->getMessage());
				}
				$position++;
			}
		}
	}

	private function executeSqlStatement($sqlStatement, $position) {
		if ($this->isPending($position, $this->_('Create database'))) {
			$this->createDatabase()->execute($sqlStatement);
			$this->setDone();
		}
	}
}
?>
