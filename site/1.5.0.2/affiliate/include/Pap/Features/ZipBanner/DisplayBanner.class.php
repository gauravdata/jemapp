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

class Pap_Features_ZipBanner_DisplayBanner extends Gpf_Object {
    
    private $userFields;
    
	public function __construct(){
        $this->userFields = new Pap_Features_ZipBanner_UserFields();
    }
    
	private function getZipFolderUrl() {
        return Gpf_Paths::getInstance()->getAccountDirectoryPath(). Pap_Features_ZipBanner_Unziper::ZIP_DIR;
    }
    
    private function getCahcheFolderUrl() {
        return Gpf_Paths::getInstance()->getCacheAccountDirectory().'zip/';
    }
    
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
    
    private function copyFilesToCacheZipFolder($unpackedZipFolderPath, $cacheZipFolderPath) {
        $unpackedZipFolder = new Gpf_Io_File($unpackedZipFolderPath);
        $cacheZipFolder =  new Gpf_Io_File($cacheZipFolderPath);

        if ($cacheZipFolder->isExists()) {
            $cacheZipFolder->emptyFiles(true);
            $cacheZipFolder->rmdir();
        }
        $cacheZipFolder->mkdir();
        
        $unpackedZipFolder->recursiveCopy($cacheZipFolder);
    }
    
    /**
     *
     * @param $affId
     * @return Pap_Common_User
     */
    private function loadAffiliate($affId) {
        $affUser = new Pap_Common_User();
        $affUser->setId($affId);
        $affUser->load();
        return $affUser;
    }
    
    /**
    *
    *@param $bannerId
    *@return Pap_Common_Banner
    */
    private function loadBanner($bannerId) {
        $banner = new Pap_Common_Banner();
        $banner->setId($bannerId);
        $banner->load();
        return $banner;
    }
    
   	   	
    private function replaceVariables($affiliate, Pap_Common_Banner $banner, $channelcode, $content) {
        $this->userFields->setUser($affiliate);
        $content = $this->userFields->replaceUserConstantsInText($content);
        if ($channelcode == null) {
           $channelcode = '';
        }
        $content = Pap_Common_UserFields::replaceCustomConstantInText(Pap_Features_ZipBanner_UserFields::CHANNELID,$channelcode,$content);
     
        $content = $banner->replaceBannerConstants($content, $affiliate);
        $content = $banner->replaceUserConstants($content, $affiliate);
        $content = $banner->replaceUrlConstants($content, $affiliate, null, $banner->getDestinationUrl());

        return $content;
    }
    
    private function getFileTypesArray($filetypes){
        return explode(',', $filetypes);
    }
    
    private function replaceTemplatesInDirectory($cacheZipFolderPath, $filetypes, Pap_Common_User $affiliate, Pap_Common_Banner $banner, $channelcode){
        $cacheZipFolder = new Gpf_Io_DirectoryIterator($cacheZipFolderPath, '', true);
        
        $typesArray = $this->getFileTypesArray($filetypes);
        foreach ($cacheZipFolder as $fullFileName => $fileName) {
            $file = new Gpf_Io_File($fullFileName);
            if ($file->matchPatterns($typesArray)){
                $content = $this->replaceVariables($affiliate, $banner, $channelcode, $file->getContents());
                $file->putContents($content);
            }
        }
    }
    
    private function packBanner($cacheFolderPath, $cacheZipFolderPath, $affiliate, $fileName) {
        $archiveName = $cacheZipFolderPath . '/' . $fileName;
        $archive = new PclZip($archiveName);
        $archive->create($cacheZipFolderPath, PCLZIP_OPT_REMOVE_PATH, $cacheZipFolderPath);
        $dirToBeDeleted = new Gpf_Io_File($cacheZipFolderPath);
        $dirToBeDeleted->emptyFiles(true, array($fileName));
        return $archiveName;
    }
    
    private function getFileName($fileId) {
		$file = new Gpf_Db_File();
		$file->setFileId($fileId);
		$file->load();
		return $file->getFileName();
    }
        
    /**
     *
     * @service banner read
     * @param $fields
     */
    public function download(Gpf_Rpc_Params $params){
        $form = new Gpf_Rpc_Form($params);
        $fileid = $form->getFieldValue('fileid');
        $affiliateid = $form->getFieldValue('affiliateid');
        $bannerid = $form->getFieldValue('bannerid');
        $filetypes = $form->getFieldValue('filetypes');
       
        $affiliate = $this->loadAffiliate($affiliateid);
        $banner = $this->loadBanner($bannerid);
        try {
            $channelcode = $form->getFieldValue('channelcode');
            $banner->setChannel(Pap_Db_Channel::loadFromId($channelcode, $affiliate->getId()));
        } catch (Gpf_Exception $e) {
            $channelcode = null;
        }
        
        $unpackedZipFolderPath = $this->getZipFolderUrl() . $fileid;
        $cacheZipFolderPath = $this->getCahcheFolderUrl() . $bannerid . '_' . $affiliateid . '_' . $fileid;
        
        $this->copyFilesToCacheZipFolder($unpackedZipFolderPath, $cacheZipFolderPath);
        
        $this->replaceTemplatesInDirectory($cacheZipFolderPath, $filetypes, $affiliate, $banner, $channelcode);
        
        $archiveName = $this->packBanner($this->getCahcheFolderUrl(),$cacheZipFolderPath , $affiliate, $this->getFileName($fileid));
        
        $download = new Gpf_File_Download_FileSystem($archiveName);
        $download->setAttachment(true);
        return $download;
    }
}

?>
