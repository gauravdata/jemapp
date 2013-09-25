<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework 
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: UploadFile.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
class Gpf_Csv_UploadFile extends Gpf_File_UploadBase {
    
    public function __construct() {
        parent::__construct();
        $this->setAllowedFileExtensions(array('csv'));
    }
    
	/**
     * Upload file to server
     *
     * @param Gpf_Rpc_Params $params
     * @service db_file write
     * @return Gpf_Rpc_Form
     */
    public function upload(Gpf_Rpc_Params $params) {
        $cacheExportDirectory = Gpf_Paths::CACHE_DIRECTORY . Gpf_Csv_ImportExportService::EXPORT_DIRECTORY;
            
        $this->clearTempDirectory(Gpf_Paths::getInstance()->getAccountDirectoryPath()
            . $cacheExportDirectory);
    	
    	$this->setRelativeUploadPath(Gpf_Paths::getInstance()->getAccountDirectoryRelativePath()
            . $cacheExportDirectory);
  
        $form = parent::upload($params);
        
        $form->setField("filename", Gpf_Paths::getInstance()->getFullAccountServerUrl() . 
            $cacheExportDirectory . $this->getName());
        $form->setField("fileid", Gpf_Paths::getInstance()->getAccountPath() . 
        $cacheExportDirectory . $this->getName());
        $form->setField("filesize", $this->size);
        $form->setField("filetype", $this->type);
           
        return $form;
    }
    
    private function clearTempDirectory($path) {
        foreach (new Gpf_Io_DirectoryIterator($path) as $fullFileName => $file) {
            $file = new Gpf_Io_File($fullFileName);
            $file->delete();
        }
    }
}
