<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DbFile.class.php 23715 2009-03-10 11:51:51Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 *   @package GwtPhpFramework
 */
abstract class Gpf_Install_DbFile extends Gpf_Object {
    const GPF = 'gpf';
    protected $version;
    protected $application;
    /**
     * @var Gpf_Io_File
     */
    protected $file;

    public function __construct($fileName, $version, $application) {
        $this->version = $version;
        $this->application = $application;
        $this->file = new Gpf_Io_File(self::getDbDirPath($this->application) . $fileName);
    }

    final public function execute($logDone = true) {
        $this->executeFile();
        $this->insertVersion($logDone);
    }
    
    public function insertVersion($logDone) {
    	$version = new Gpf_Db_Version();
        $version->setApplication($this->application);
        $version->setVersion($this->version);
        if($logDone) {
            $version->setDone();
        }
        $version->insert();
    }

    protected abstract function executeFile();

    public function getVersion() {
        return $this->version;
    }

    public function getApplication() {
        return $this->application;
    }

    public static function getDbDirPath($application) {
        $basePath = Gpf_Paths::getInstance()->getTopPath();
        if($application == self::GPF) {
            $basePath = Gpf_Paths::getInstance()->getFrameworkPath();
        }
        return $basePath . Gpf_Paths::INSTALL_DIR .'db/' . $application . '/';
    }
}
