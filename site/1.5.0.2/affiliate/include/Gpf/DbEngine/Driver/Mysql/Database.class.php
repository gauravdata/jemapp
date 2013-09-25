<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Database.class.php 33536 2011-06-29 12:18:13Z jsimon $
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
class Gpf_DbEngine_Driver_Mysql_Database extends Gpf_DbEngine_Database  {
    const BENCHMARK_CONNECT = 'db_connect';
    const BENCHMARK_EXECUTE = 'db_execute';
    const CR_SERVER_GONE_ERROR = 2006;
    const MAX_FAILED_CONNECTION_COUNT = 5;

    private $handle;
    private $failedConnections = 0;

    public function connect() {
        Gpf_Log_Benchmark::start(self::BENCHMARK_CONNECT);

        $handle = @mysql_connect($this->host, $this->username, $this->password, $this->newLink);
        if(!$handle) {
            Gpf_Log_Benchmark::end(self::BENCHMARK_CONNECT, "Unable to connect to database: " . mysql_error());
            throw new Gpf_DbEngine_Exception("Unable to connect to database: " . mysql_error());
        }
        if(!mysql_select_db($this->dbname, $handle)) {
            Gpf_Log_Benchmark::end(self::BENCHMARK_CONNECT, "Unable to select database ");
            throw new Gpf_DbEngine_Exception("Unable to select database " . $this->dbname . ' Reason:' . mysql_error());
        }
        mysql_query("SET NAMES utf8");
        mysql_query("SET CHARACTER_SET utf8");
        $this->handle = $handle;
        $this->connected = true;

        Gpf_Log_Benchmark::end(self::BENCHMARK_CONNECT, "Connected succesfuly");
        return true;
    }

    public function prepare($sqlString) {
        $sth = new Gpf_DbEngine_Driver_Mysql_Statement();
        $sth->init($sqlString, $this->handle);
        return $sth;
    }

    /**
     * @return Gpf_DbEngine_Driver_Mysql_Statement
     */
    public function execute($sqlString, $getAutoincrementId = false) {
        Gpf_Log_Benchmark::start(self::BENCHMARK_EXECUTE);

        if(!$this->isConnected()) {
            $this->connect();
        }

        $sth = $this->prepare($sqlString);
        $retval = $sth->execute();
        Gpf_Log_Benchmark::end(self::BENCHMARK_EXECUTE, "SQL [returned $retval]: " . $sqlString);
        if($retval !== false) {
            if($getAutoincrementId) {
                $sth->loadAutoIncrementId();
            }
            $this->resetFailedConnectionsCount();
            return $sth;
        }
        
        try {
            $this->handleError($sth);
        } catch (Gpf_DbEngine_ConnectionGoneException $e) {
            if ($this->maxConnectionsCountRaised()) {
                throw new Gpf_DbEngine_Exception($this->_sys('Maximum failed connection count %s reached. Giving up.', self::MAX_FAILED_CONNECTION_COUNT));
            }
            $this->reconnect();
            return $this->execute($sqlString, $getAutoincrementId);
        }
    }


    private function handleError($sth) {
        if($sth->getErrorCode() == self::CR_SERVER_GONE_ERROR) {
            Gpf_Log::disableType(Gpf_Log_LoggerDatabase::TYPE);
            Gpf_Log::info($this->_sys('MySql server has gone away: Reconnecting...'));
            Gpf_Log::enableAllTypes();
            throw new Gpf_DbEngine_ConnectionGoneException($this->_sys('MySql server has gone away.'));
        }
        
        Gpf_Log_Benchmark::end(self::BENCHMARK_EXECUTE, "SQL ERROR: ".$sth->getStatement());
        
        $this->resetFailedConnectionsCount();
        throw new Gpf_DbEngine_Driver_Mysql_SqlException($sth->getStatement(), $sth->getErrorMessage(), $sth->getErrorCode());
    }


    protected function resetFailedConnectionsCount() {
        $this->failedConnections = 0;
    }

    private function incFailedConnectionsCount() {
        $this->failedConnections++;
    }

    private function maxConnectionsCountRaised() {
        if ($this->failedConnections == self::MAX_FAILED_CONNECTION_COUNT) {
            return true;
        }
        return false;
    }

    private function reconnect() {
        $this->disconnect();
        $this->connect();
        $this->incFailedConnectionsCount();
    }

    public function disconnect() {
        @mysql_close($this->handle);
        $this->handle = null;
        parent::disconnect();
    }

    function escapeString($str) {
        if(!$this->isConnected()) {
            $this->connect();
        }
        return mysql_real_escape_string($str, $this->handle);
    }

    function getVersion() {
        return mysql_get_server_info($this->handle);
    }
}

?>
