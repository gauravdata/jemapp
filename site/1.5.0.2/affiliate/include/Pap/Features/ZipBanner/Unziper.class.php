<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

require_once 'pclzip.lib.php';

/**
 * @package PostAffiliatePro
 */
class Pap_Features_ZipBanner_Unziper extends Gpf_File_UploadBase {
    const ZIP_DIR = 'banners/zip/';

    public function __construct() {
        parent::__construct();
        $this->setAllowedFileExtensions(array('zip'));
    }
    
    private function getZipFolderUrl() {
        return Gpf_Paths::getInstance()->getAccountDirectoryPath(). self::ZIP_DIR ;
    }
    
    
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

    protected function saveUploadedFile() {
        
        $file = new Gpf_Db_File();
        $file->set('filename', $this->name);
        $file->set('filesize', $this->size);
        $file->set('filetype', $this->type);
        $file->save();
             
        $dir = new Gpf_Io_File($this->getZipFolderUrl().$file->getFileId().'/');
        if ($dir->isExists()) {
            $dir->delete();
        }
        $dir->mkdir();
        $tmpZip = new Gpf_Io_File($this->getZipFolderUrl().$file->getFileId().'/'.$file->getFileId().".zip");
        $dir->copy(new Gpf_Io_File($this->tmpName),$tmpZip);
        
        $archive = new PclZip($this->getZipFolderUrl().$file->getFileId().'/'.$file->getFileId().".zip");
        $err = $archive->extract($this->getZipFolderUrl().$file->getFileId().'/');
        if ($err <= 0) {
            throw new Gpf_Exception("code: ".$err);
        }

        $tmpZip->delete();
 
        return $file;
    }
}
