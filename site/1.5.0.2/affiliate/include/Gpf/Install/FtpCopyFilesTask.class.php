<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
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

require_once 'pclzip.lib.php';

/**
 * @package GwtPhpFramework
 */
class Gpf_Install_FtpCopyFilesTask extends Gpf_Tasks_LongTask {

    const DELIMITER = '|->|';


    private $sourceDirectory;

    private $destinationDirectory;

    private $excludeDirectory;

    /**
     * @var Gpf_Io_Ftp
     */
    private $ftp;

    public function __construct($sourceDirectory = null, $destinationDirectory = '', $excludeDir = '') {
        if ($sourceDirectory != null) {
            $this->setParams($sourceDirectory . self::DELIMITER .
                             $destinationDirectory . self::DELIMITER .
                             $excludeDir);
        }
    }

    private function loadParams() {
        $params = explode(self::DELIMITER, $this->getParams(), 3);
        if (!is_array($params) || count($params) != 3) {
            throw new Gpf_Exception('Invalid task parameters for Gpf_Install_UnzipFileTask');
        }
        $this->sourceDirectory = $params[0];
        $this->destinationDirectory = $params[1];
        $this->excludeDirectory = $params[2];

        $this->ftp = new Gpf_Io_Ftp();
        $this->ftp->connect();
    }

    public function getName() {
        return $this->_('FTP - Copy files');
    }

    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }
    
    
    protected function execute() {
        $this->loadParams();

        if ($this->isPending('makeDirectories', $this->_('Directories created'))) {
            $this->copyDirectories($this->sourceDirectory, $this->excludeDirectory);
            $this->setDone();
        }

        if ($this->isPending('copyFiles', $this->_('Files copied'))) {
            $this->copyFiles();
            $this->setDone();
        }
    }

    private function getRelativeFileName($fileName) {
        if ($this->destinationDirectory != '') {
            return $this->destinationDirectory.'/'.substr($fileName, strlen($this->sourceDirectory));
        } else {
            return substr($fileName, strlen($this->sourceDirectory));
        }
    }

    /**
     * @return Gpf_Io_DirectoryIterator
     */
    private function getDirectoryIteratorIterator($directory, $recursive, $onlyDirectories, $excludeDir = '') {
        $iterator = new Gpf_Io_DirectoryIterator($directory, '', $recursive, $onlyDirectories);
        if ($excludeDir != '') {
            $iterator->addIgnoreDirectory($excludeDir);
        }
        return $iterator;
    }

    private function copyFiles() {
        foreach ($this->getDirectoryIteratorIterator($this->sourceDirectory, true, false) as $fullFileName => $fileName) {
            if ($this->isDone($fullFileName)) {
                continue;
            }
            $this->ftp->delete($this->getRelativeFileName($fullFileName));
            $this->ftp->rename(substr($fullFileName, 3), $this->getRelativeFileName($fullFileName));
            $this->setDone($fullFileName);
        }
    }

    private function copyDirectories($directory, $excludeDir = '') {
        foreach ($this->getDirectoryIteratorIterator($directory, false, true, $excludeDir) as $fullFileName => $fileName) {
            if ($this->isDone($fullFileName) || $fileName == $this->excludeDirectory) {
                continue;
            }
            $this->copyDirectories($fullFileName);
            $this->ftp->mkdir($this->getRelativeFileName($fullFileName));
            $this->setDone($fullFileName);
        }
    }

}
?>
