<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DbDownload.class.php 18512 2008-06-13 15:18:51Z aharsani $
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
class Gpf_File_Download_FileSystem extends Gpf_File_DownloadDriver {
    /**
     * @var Gpf_Io_File
     */
    private $file;
    
    public function __construct($filename) {
        $this->file = new Gpf_Io_File($filename);
    }
    
    protected function check() { 
        try {
            $this->file->open('rb');
            return true;
        } catch (Exception $e) {
        }
        return false;
    }
    
    protected function getSize() {
        return $this->file->getSize();
    }
    
    protected function getType() {
        return $this->file->getExtension();    
    }
    
    protected function getFileName() {
        return basename($this->file->getFileName());
    }
    
    protected function getContent() {
        fpassthru($this->file->getFileHandler());
        flush();
    }
}

?>
