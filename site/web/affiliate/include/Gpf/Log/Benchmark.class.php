<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Logger.class.php 18399 2008-06-05 14:56:11Z mbebjak $
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
class Gpf_Log_Benchmark extends Gpf_Object {
    /**
     * @var Gpf_Log_Benchmark
     */
    private static $instance;
    private $isActive = null;
    private $minSqlTime = 0;
    private $startTimes = array();
    /**
     *
     * @var Gpf_Log_Logger
     */
    private static $benchmarkLogger;

    private function __construct() {
    }

    /**
     * @return Gpf_Log_Benchmark
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function start($benchmarkName) {
        self::getInstance()->startBenchmark($benchmarkName);
    }

    private function startBenchmark($benchmarkName) {
        if(!$this->isActive()) {
            return;
        }
        $this->startTimes[$benchmarkName] = Gpf_Common_DateUtils::getNowSeconds();
    }

    /**
     * Return how many seconds is running benchmark already
     *
     * @param string $benchmarkName
     * @return int number of seconds
     */
    public function getBenchmarkTime($benchmarkName) {
        if(!$this->isActive()) {
            return 0;
        }
        return Gpf_Common_DateUtils::getNowSeconds() - $this->startTimes[$benchmarkName];
    }

    public static function end($benchmarkName, $message) {
        self::getInstance()->endBenchmark($benchmarkName, $message);
    }

    protected function getLogFileName() {
        $logDir = new Gpf_Io_File(Gpf_Paths::getInstance()->getAccountsPath());
        return $logDir->getFileName() . 'benchmark.log';
    }

    private function endBenchmark($benchmarkName, $message) {
        if(!$this->isActive()) {
            return;
        }

        if(!isset($this->startTimes[$benchmarkName])) {
            return;
        }

        $time = Gpf_Common_DateUtils::getNowSeconds() - $this->startTimes[$benchmarkName];
        unset($this->startTimes[$benchmarkName]);

        if(self::$benchmarkLogger === null) {
            try {
                $this->initLogger();
            } catch (Exception $e) {
                $this->isActive = false;
                return;
            }
        }

        if ($time >= $this->minSqlTime) {
            self::$benchmarkLogger->debug($time . " secs. | " . $message);
        }
    }

    private function initLogger() {
        self::$benchmarkLogger = Gpf_Log_Logger::getInstance('benchmark');
        $fileLogger = new Gpf_Log_LoggerFile();

        $fileName = $this->getLogFileName();
        $fileLogger->setFileName($fileName);
        $this->checkResetFileSize($fileName);
        self::$benchmarkLogger->addLogger($fileLogger, Gpf_Log::DEBUG);
    }

    private function isActive() {
        if($this->isActive !== null) {
            return $this->isActive;
        }
        $this->isActive = false;
        $this->minSqlTime = 0;
        try {
            $this->isActive = Gpf_Settings::get(Gpf_Settings_Gpf::BENCHMARK_ACTIVE) == Gpf::YES;
            $this->minSqlTime = Gpf_Settings::get(Gpf_Settings_Gpf::BENCHMARK_MIN_SQL_TIME);
        } catch (Exception $e) {
        }
        return $this->isActive;
    }

    protected function checkResetFileSize($fileName) {
        $file = new Gpf_Io_File($fileName);
        $fileSize = $file->getSize();
        if ($fileSize/1024/1024 > Gpf_Settings::get(Gpf_Settings_Gpf::BENCHMARK_MAX_FILE_SIZE)) {
            $this->resetFile($file, $fileSize);
        }
    }

    private function resetFile(Gpf_Io_File $file, $fileSize) {
        $file->open('w');
        $file->write('File was truncated after exceeding size ' . $fileSize. " bytes. Now continuing...\n");
        $file->close();
    }
}
?>
