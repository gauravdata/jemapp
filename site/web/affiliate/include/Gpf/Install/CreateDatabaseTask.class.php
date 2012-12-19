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
class Gpf_Install_CreateDatabaseTask extends Gpf_Tasks_LongTask {

	private $hostname;
	private $username;
	private $password;
	private $dbname;

	public function setDBSettings($hostname, $username, $dbname, $password = '') {
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		if($this->password == '*****') {
			$this->password = Gpf_Settings::get(Gpf_Settings_Gpf::DB_PASSWORD);
		}
		$this->dbname = $dbname;
		$this->setParams($this->hostname);
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
	    if ($this->isDBSettingsValid()) {
            Gpf_DbEngine_Database::create($this->hostname, $this->username, $this->password, $this->dbname);
        }

        try {
            $this->checkDatabaseConnectable();
            if ($this->isDBSettingsValid() && $this->isNoAutoValueOnZeroDisabled() && $this->isPending('writeDBSettings', $this->_('Write DB settings'))) {
                $this->writeDbInfo();
                $this->setDone();
            }
        } catch (Exception $e) {
            throw new Gpf_Exception($this->_('Could not connect to database. Please retype your database information again. Message: %s', $e->getMessage()));
        }

		if ($this->isPending('setDatabase', $this->_('Set database'))) {
			$this->setDatabase();
			$this->setDone();
		}

		$this->setStorageEngine($this->createDatabase());
		
		if ($this->isPending('createFrameworkTables', $this->_('Create framework tables'))) {
			$this->executeCreateSqlFile();
			$this->setDone();
		}

		if ($this->isPending('createApplicationTables', $this->_('Create application tables'))) {
			$this->executeCreateSqlFile(Gpf_Application::getInstance()->getCode());
			$this->setDone();
		}

		if ($this->isPending('writeFrameworkVersion', $this->_('Write framework version'))) {
			$this->writeApplicationVersion();
			$this->setDone();
		}

		if ($this->isPending('writeApplicationVersion', $this->_('Write application version'))) {
			$this->writeApplicationVersion(Gpf_Application::getInstance()->getCode());
			$this->setDone();
		}

		Gpf_Application::getInstance()->initDatabase();
	}

	private function executeCreateSqlFile($application = Gpf::CODE) {
		$executeSQLFileTask = new Gpf_Install_ExecuteSQLFileTask($this->getSqlFile($application));
		try {
			$executeSQLFileTask->run();
		} catch (Gpf_DbEngine_Driver_Mysql_SqlException $e) {
			throw new Gpf_Exception($this->_('Could not create database. %s', $e->getMessage()));
		}
	}

	public static function setStorageEngine(Gpf_DbEngine_Database $db) {
        $sql = "SET storage_engine=MYISAM";
        $db->execute($sql);
	}

	private function setDatabase() {
		$db = $this->createDatabase();
		self::setStorageEngine($db);

		$sql = "ALTER DATABASE " . '`' . $db->getDbname() . '`' . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
		$db->execute($sql);
	}

	private function writeDbInfo() {
		$settingFile = new Gpf_File_Settings();
		try {
			$settingFile->getAll();
		} catch (Exception $e) {
		}
		$settingFile->setSetting(Gpf_Settings_Gpf::DB_HOSTNAME, $this->hostname, false);
		$settingFile->setSetting(Gpf_Settings_Gpf::DB_USERNAME, $this->username, false);
		$settingFile->setSetting(Gpf_Settings_Gpf::DB_PASSWORD, $this->password, false);
		$settingFile->setSetting(Gpf_Settings_Gpf::DB_DATABASE, $this->dbname, false);
		$settingFile->saveAll();
	}

    private function isNoAutoValueOnZeroDisabled() {
        $select = new Gpf_SqlBuilder_SelectBuilder;
        $select->select->add("@@GLOBAL.sql_mode");
        try {
            $result = $select->getOneRow();
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception($this->_('Could not check database sql mode. Message: %s', $e->getMessage()));
        }
        if ($result->get('@@GLOBAL.sql_mode') == 'NO_AUTO_VALUE_ON_ZERO') {
            throw new Gpf_Exception($this->_('Database sql mode is "NO_AUTO_VALUE_ON_ZERO", remove it from mysql configuration file.'));
        }
        return true;
    }

	protected function isDBSettingsValid() {
		return $this->hostname == null || $this->dbname == null || $this->username == null ? false : true;
	}

	private function checkDatabaseConnectable() {
		$this->createDatabase()->connect();
	}

	/**
	 * @param $application
	 * @return Gpf_Install_SqlFile
	 */
	private function getSqlFile($application = Gpf::CODE) {
		$version = Gpf_Application::getInstance()->getVersion();
		if($application == Gpf::CODE) {
			$version = Gpf::GPF_VERSION;
		}
		return new Gpf_Install_SqlFile('create.sql',  $version, $application);
	}

	private function writeApplicationVersion($application = Gpf::CODE) {
		try {
			$this->getSqlFile($application)->insertVersion($application == Gpf::CODE ? true : false);
		} catch (Gpf_DbEngine_Driver_Mysql_SqlException $e) {
			throw new Gpf_Exception($this->_('Could not write application version. %s', $e->getMessage()));
		}
	}
}
?>
