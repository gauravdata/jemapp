<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Rene Dohanisko
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: BannerUpload.class.php 18513 2008-06-13 15:19:18Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Features_RebrandPdfBanner_Upload extends Gpf_File_UploadBase {

    const TMP_EXT = '.tmp';

    /**
     * @var Gpf_Io_File
     */
    private $file;
    private $fileSize;
    private $originalFile;

    public function __construct() {
        parent::__construct();
        $this->setRelativeUploadPath(Gpf_Paths::getInstance()->getAccountDirectoryRelativePath() .
        Pap_Merchants_Banner_BannerUpload::BANNERS_DIR);
    }

    protected function isExtensionAllowed() {
        return true;
    }

    public function upload(Gpf_Rpc_Params $params){
        $form = parent::upload($params);
        if($form->isSuccessful()){
            $this->file = new Gpf_Io_File($form->getFieldValue("fileurl"));
            $fileSizeArray = explode('.', $this->file->getSize());
            $this->fileSize = $fileSizeArray[0];
            $fileData = $_FILES[self::UPLOAD_NAME];
            $this->originalFile =  basename($fileData['name']);
        }
        return $form;
    }

    public function getOriginalFile(){
        return $this->originalFile;
    }

    public function getFileSize(){
        return $this->fileSize;
    }

    public function getFile(){
        return $this->file->getFileName();
    }

    protected function generateFileName($count){
        return parent::generateFileName($count). self::TMP_EXT;
    }
}
?>
