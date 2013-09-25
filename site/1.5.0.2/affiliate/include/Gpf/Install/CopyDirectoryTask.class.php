<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
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
class Gpf_Install_CopyDirectoryTask extends Gpf_Tasks_LongTask {
    /**
     *
     * @var Gpf_Io_File
     */
    protected $source;
    /**
     *
     * @var Gpf_Io_File
     */
    protected $target;
    protected $mode;

    public function __construct(Gpf_Io_File $source, Gpf_Io_File $target, $mode = null) {
        $this->source = $source;
        $this->target = $target;
        $this->mode = $mode;
        $this->setParams($source->getFileName() . ' -> ' . $target->getFileName());
    }

    public function getName() {
        return $this->_('Copy directory %s', $this->getParams());
    }

    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }

    protected function execute() {
        $this->dircopy($this->source, $this->target);
    }

    private function dircopy(Gpf_Io_File $source, Gpf_Io_File $target) {
        if (!$source->isDirectory()) {
            $this->copyFile($source, $target);
            return;
        }

        if(!$target->isExists()) {
            $target->mkdir(true, $this->mode);
        }

        $dir = dir($source->getFileName());

        $entries = array();
        while (false !== ($entry = $dir->read())) {
            $entries[] = $entry;
        }
        $dir->close();
        sort($entries);

        foreach ($entries as $entry) {
            if ($entry == '.' || $entry == '..' || $entry == '.svn') {
                continue;
            }
            $newSource = new Gpf_Io_File(rtrim($source->getFileName(), '/') . '/' . $entry);
            $newTarget = new Gpf_Io_File(rtrim($target->getFileName(), '/') . '/' . $entry);
            if($newSource->isDirectory()) {
                $this->dircopy($newSource, $newTarget);
                continue;
            }
            $this->copyFile($newSource, $newTarget);
        }
    }

    private function copyFile(Gpf_Io_File $source, Gpf_Io_File $target) {
        if($this->isPending(md5($source->getFileName()), $this->_('Copying file'))) {
            $this->copy($source, $target);
            $this->setDone();
        }
    }

    protected function copy(Gpf_Io_File $source, Gpf_Io_File $target) {
        Gpf_Io_File::copy($source, $target, $this->mode);
    }
}
?>
