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
abstract class Gpf_File_DownloadDriver extends Gpf_Object implements Gpf_Rpc_Serializable {
    protected $isAttachment = false;
    
    public function toObject() {
        throw new Gpf_Exception("Unsupported");
    }
    
    final public function toText() {
        if(!$this->check()) {
            return 'Download Error';
        }
        $this->printHeaders();
        $this->getContent();
        exit;
    }
    
    protected function check() {
        return true;
    }
    
    protected function isAttachment() {
        return $this->isAttachment;
    }
    
    public function setAttachment($isAttachment) {
        $this->isAttachment = $isAttachment;    
    }
    
    abstract protected function getSize();
    
    abstract protected function getType();
    
    abstract protected function getFileName();
    
    abstract protected function getContent(); 

    protected function printHeaders() {
        Gpf_Http::setHeader("Content-Type", $this->getType());
       
        if($this->isAttachment()) {
            Gpf_Http::setHeader('Cache-Control', 'private, must-revalidate');
            Gpf_Http::setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
            Gpf_Http::setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");
            Gpf_Http::setHeader("Content-Transfer-Encoding", 'binary');

            Gpf_Http::setHeader('Content-Description', 'File Transfer');
            Gpf_Http::setHeader('Content-Type', 'application/force-download');
            Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::CONTENT_DISPOSITION, 'attachment; filename="' 
                . htmlspecialchars($this->getFileName()) . '"');
        }
        Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::CONTENT_LENGTH, $this->getSize());
    }
}

?>
