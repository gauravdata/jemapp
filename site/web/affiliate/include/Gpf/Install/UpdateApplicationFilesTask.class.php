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

/**
 * @package GwtPhpFramework
 */
class Gpf_Install_UpdateApplicationFilesTask extends Gpf_Tasks_LongTask {

    const DISTRIBUTION_DIRECTORY = 'distrib/';

    protected $distributionFile;

    public function __construct($distributionFile = '') {
        if ($distributionFile != '') {
            $this->setParams($distributionFile);
        }
    }

    public function getName() {
        return $this->_('Update application files');
    }
    
    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }

    protected function execute() {
        if($this->isPending('prepareDistribDirectory', $this->_('Preparing distribution directory'))) {
            $this->clearDistributionDirectory();
            $this->setDone();
        }

        if ($this->isPending('unzipDistributionFile', $this->_('Unzipping distribution'))) {
            $this->unzipDistributionFile();
            $this->setDoneAndInterrupt();
        }


        if ($this->isPending('copyIncludeFiles', $this->_('Copying files'))) {
            $this->copyIncludeFiles();
            $this->setDone();
        }

        if ($this->isPending('copyFiles', $this->_('Copying files'))) {
            $this->copyFiles();
            $this->setDone();
        }

        if($this->isPending('clearDistribDirectory', $this->_('Clearing distribution directory'))) {
            $this->clearDistributionDirectory();
            $this->setDone();
        }
    }

    /**
     * @return Gpf_Io_File
     */
    private function getDistributionDirectory($subdirectory = '') {
        return new Gpf_Io_File(Gpf_Paths::getInstance()->getRealAccountDirectoryPath() .
                               Gpf_Paths::CACHE_DIRECTORY . self::DISTRIBUTION_DIRECTORY . $subdirectory);
    }

    /**
     * @return Gpf_Io_File
     */
    private function getZipFile() {
        return new Gpf_Io_File(Gpf_Paths::getInstance()->getTopPath().Gpf_Paths::INSTALL_DIR.$this->getParams());
    }

    private function clearDistributionDirectory() {
        $distDir = $this->getDistributionDirectory();
        if (!$distDir->isExists()) {
            $distDir->mkdir(true);
        }

        $clearDirectoryTask = new Gpf_Install_ClearDirectoryTask($distDir->getFileName());
        $clearDirectoryTask->run();
    }

    private function unzipDistributionFile() {
        $unzipTask = new Gpf_Install_UnzipFileTask($this->getZipFile(), $this->getDistributionDirectory());
        $unzipTask->run();
    }

    private function copyFiles() {
        $copyTask = new Gpf_Install_FtpCopyFilesTask($this->getDistributionDirectory(), '', 'include');
        $copyTask->run();
    }

    private function copyIncludeFiles() {
        $copyTask = new Gpf_Install_FtpCopyFilesTask($this->getDistributionDirectory('include/'), 'include');
        $copyTask->run();
    }

}
?>
