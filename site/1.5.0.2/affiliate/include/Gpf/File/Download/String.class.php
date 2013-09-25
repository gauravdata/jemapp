<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
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
class Gpf_File_Download_String extends Gpf_File_DownloadDriver {
    
    private $fileName;
    private $content;
    
    public function __construct($fileName, $content) {
        $this->fileName = $fileName;
        $this->content = $content;
    }
    
    protected function getSize() {
        return strlen($this->content);
    }
    
    protected function getType() {
        if (($pos = strrpos($this->fileName, '.')) === false) {
            return '';
        }
        return substr($this->fileName, $pos);    
    }
    
    protected function getFileName() {
        return $this->fileName;
    }
    
    protected function getContent() {
        echo $this->content;
    }
}

?>
