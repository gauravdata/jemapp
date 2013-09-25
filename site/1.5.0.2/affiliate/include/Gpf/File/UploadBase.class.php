<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric, Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UploadToDirectory.class.php 18010 2008-05-14 07:36:56Z vzeman $
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
abstract class Gpf_File_UploadBase extends Gpf_Object {
    const UPLOAD_NAME = 'uploadWidget';
    protected $name;
    protected $tmpName;
    protected $size;
    protected $type;
    private $allowedFileExtensions = array();
    private $relativeUploadPath = '';
    private $filePermissions = 0777;
    
    public function __construct() {
    }

    /**
     * Upload file to server
     *
     * @param Gpf_Rpc_Params $params
     * @service db_file write
     * @return Gpf_Rpc_Form
     */
    public function upload(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        try {
            $this->uploadFile();    
            $uploadedFile = $this->saveUploadedFile();
            $form->setField("fileurl", $uploadedFile);
            $form->setInfoMessage($this->_("File was succesfuly uploaded"));
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
        }
        return $form;
    }
    
    public function getName() {
    	return $this->name;
    }
    
    protected function setRelativeUploadPath($path) {
        $this->relativeUploadPath = $path;
    }
    
    /**
     * @param $extensions (Array with strings)
     */
    protected function setAllowedFileExtensions(array $extensions) {
    	foreach ($extensions as $key => $extension) {
    		$extensions[$key] = strtolower($extension);
    	}
        $this->allowedFileExtensions = $extensions;    
    }
    
    protected function setFilePermissions($filePermissions) {
    	$this->filePermissions = $filePermissions;
    }
    
    protected function uploadFile() {
        if (!isset($_FILES[self::UPLOAD_NAME]['name'])) {
            throw new Gpf_Exception("No file submitted");
        }

        if (isset($_FILES[self::UPLOAD_NAME]['error'])
        && $_FILES[self::UPLOAD_NAME]['error'] != UPLOAD_ERR_OK) {
            throw new Gpf_Exception("Upload error: " . $_FILES[self::UPLOAD_NAME]['error']);
        }
        
        $fileData = $_FILES[self::UPLOAD_NAME];
        $this->name = basename($fileData['name']);
        $this->tmpName = $fileData['tmp_name'];
        if (strlen($_REQUEST['max_file_size']) && $fileData['size'] > $_REQUEST['max_file_size'] && $_REQUEST['max_file_size'] > 0) {
            throw new Gpf_Exception($this->_('It is not allowed to upload file bigger as %s bytes. Your file has size %s bytes.', $_REQUEST['max_file_size'], $fileData['size']));
        }
        $this->size = $fileData['size'];
        $this->type = $fileData['type'];

        $this->checkFile();
    }
    
    protected function saveUploadedFile() {
        $file = $this->getRealFileName();
        if (!@move_uploaded_file($this->tmpName, $file)) {
            throw new Gpf_Exception("Cannot copy uploaded file to the destination directory");
        }
        if (!@chmod($file, $this->filePermissions)) {
        	throw new Gpf_Exception("Cannot set permissions to uploaded file");
        }
        return $file;
    }
    
    private function checkFile() {
        if(!is_uploaded_file($this->tmpName) || ($this->size == 0)) {
            throw new Gpf_Exception("File was not uploaded");
        }
        if (!$this->isExtensionAllowed()) {
            throw new Gpf_Exception("Upload error: file of this type (extension) is not allowed!");
        }
    }
    
    private function getFileExtension() {
        $pos = strrpos($this->name, ".");
        if ($pos === false) { 
            return '';
        }
        $extension = substr($this->name, $pos + 1);
        return $extension;
    }
    
    protected function isExtensionAllowed() {
        return in_array(strtolower($this->getFileExtension()), $this->allowedFileExtensions);
    }
    
    protected function getRealFileName() {
        $count = 0;
        while(true) {
            $relativeName = $this->relativeUploadPath . $this->generateFileName($count);
            $file = new Gpf_Io_File(Gpf_Paths::getInstance()->getTopPath() . $relativeName);
            if (!$file->isExists()) {
                return '../' . $relativeName;
            }
            if ($count > 100) {
                throw new Gpf_Exception("Cannot generate unique file name");
            }
            $count++;
        }
    }
    
    protected function generateFileName($count) {
        if($count == '' || $count == 0) {
            return $this->name;
        }
        
        $pos = strrpos($this->name, ".");
        if($pos === false) {
            $extension = '';
        } else {
            $extension = substr($this->name, $pos);
        }
        
        $filename = substr($this->name, 0, $pos);
        return $filename . "-" . $count . $extension;
    }
}
?>
