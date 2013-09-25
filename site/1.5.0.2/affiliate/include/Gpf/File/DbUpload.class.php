<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman, Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: DbUpload.class.php 20176 2008-08-26 13:55:39Z mbebjak $
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
class Gpf_File_DbUpload extends Gpf_File_UploadBase {

    
    /**
     * Upload file to server
     *
     * @service db_file write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function upload(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        try {
            $this->uploadFile();
            $file = $this->saveUploadedFile();
            
            $form->setField("fileid", $file->get('fileid'));
            $form->setField("filename", $file->get('filename'));
            $form->setField("filetype", $file->get('filetype'));
            $form->setField("filesize", $file->get('filesize'));
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
        }
        return $form;
    }
    
    /**
     *
     * @return Gpf_Db_File
     */
    protected function saveUploadedFile() {
        $file = new Gpf_Db_File();
        $file->set('filename', $this->name);
        $file->set('filesize', $this->size);
        $file->set('filetype', $this->type);
        $file->save();
        $this->uploadContent($this->tmpName, $file);
        return $file;
    }
    
    protected function isExtensionAllowed() {
        return true;
    }
    
    /**
     * Save content of uploaded file to database
     *
     * @param string $filename
     * @param Gpf_Db_File $file
     */
    private function uploadContent($filename, Gpf_Db_File $file) {
        $contentId = 1;

        $tmpFile = new Gpf_Io_File(get_cfg_var('upload_tmp_dir') . $filename);
        if(!$tmpFile->isExists()) {
            $tmpFile->setFileName($filename);
        }
        if (!$tmpFile->isExists()) {
            throw new Gpf_Exception("File not found " . $tmpFile->getFileName());
        }

        $tmpFile->open();
        while ($data = $tmpFile->read(500000)) {
            $fileContent = new Gpf_Db_FileContent();
            $fileContent->set('fileid', $file->get('fileid'));
            $fileContent->set('contentid', $contentId++);
            $fileContent->set('content', $data);
            $fileContent->save();
        }
    }
}
?>
