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
class Gpf_Install_UnzipFileTask extends Gpf_Tasks_LongTask {

    const DELIMITER = '|->|';
    const UNZIP_STEP = 100;

    /**
     * @var Gpf_Io_File
     */
    private $zipFile;
    /**
     * @var Gpf_Io_File
     */
    private $outputDirectory;

    public function __construct(Gpf_Io_File $zipFile = null, Gpf_Io_File $outputDirectory = null) {
        if ($zipFile != null && $outputDirectory != null) {
            $this->setParams($zipFile->getFileName().self::DELIMITER.$outputDirectory->getFileName());
        }
    }

    private function loadParams() {
        $params = explode(self::DELIMITER, $this->getParams(), 2);
        if (!is_array($params) || count($params) != 2) {
            throw new Gpf_Exception('Invalid task parameters for Gpf_Install_UnzipFileTask');
        }
        $this->zipFile = new Gpf_Io_File($params[0]);
        $this->outputDirectory = new Gpf_Io_File($params[1]);
    }

    public function getName() {
        if ($this->zipFile != null) {
            return $this->_('Unzip file %s', $this->zipFile->getFileName());
        }
        return $this->_('Unzip file');
    }
    
    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }
    

    protected function execute() {
        $this->loadParams();
        $archive = new PclZip($this->zipFile->getFileName());
        $listContent = $archive->listContent();
        $allFilesCount = count($listContent);
        $processedFiles = - self::UNZIP_STEP;

        while ($processedFiles < $allFilesCount) {
            $processedFiles += self::UNZIP_STEP;
            if ($this->isDone($processedFiles, $this->_('%s%% files unzipped', round(($processedFiles/$allFilesCount)*100, 0)))) {
                continue;
            }
            $this->changePermissions($archive->extractByIndex($processedFiles.'-'.($processedFiles + self::UNZIP_STEP - 1), $this->outputDirectory->getFileName()));
            $this->setDone($processedFiles);
        }
    }
    
    /**
     * @param $fileDataArray
     */
    private function changePermissions($fileDataArray) {
    	if ($fileDataArray == null) {
    		return;
    	}
    	foreach ($fileDataArray as $fileData) {
    		@chmod($fileData['filename'], 0777);
    	}
    }
}
?>
