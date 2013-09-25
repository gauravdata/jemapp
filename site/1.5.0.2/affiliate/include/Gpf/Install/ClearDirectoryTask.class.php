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
class Gpf_Install_ClearDirectoryTask extends Gpf_Tasks_LongTask {

    public function __construct($directory = null) {
        if ($directory != null) {
            $this->setParams($directory);
        }
    }

    public function getName() {
        return $this->_('Delete directory %s', $this->getParams());
    }
    
    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }


    protected function execute() {
        $directory = $this->getParams();
        if ($this->isPending('deleteFiles', $this->_('Files deleted'))) {
            $this->deleteFiles($directory);
            $this->setDone();
        }

        if ($this->isPending('deleteDirectories', $this->_('Directories deleted'))) {
            $this->deleteDirectories($directory);
            $this->setDone();
        }
    }

    private function deleteFiles($directory) {
        foreach (new Gpf_Io_DirectoryIterator($directory, '', true, false) as $fullFileName => $fileName) {
            $file = new Gpf_Io_File($fullFileName);
            $file->delete();
            $this->checkInterruption();
        }
    }

    private function deleteDirectories($directory) {
        foreach (new Gpf_Io_DirectoryIterator($directory, '', false, true) as $fullFileName => $fileName) {
            $this->checkInterruption();
            $this->deleteDirectories($fullFileName);
            $file = new Gpf_Io_File($fullFileName);
            $file->rmdir();
        }
    }
}
?>
