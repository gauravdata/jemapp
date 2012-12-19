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
class Gpf_File_Download extends Gpf_Object {
    /**
     * @var Gpf_Db_File
     */
    protected $file;
    protected $fileIdName = 'fileid';
    
    public function __construct() {
        $this->file = new Gpf_Db_File();
    }

    /**
     * Download file from server
     *
     * @param Gpf_Rpc_Params $params
     * @service uploaded_file read
     */
    public function download(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        try {
            $this->file->setFileId($form->getFieldValue($this->fileIdName));
        } catch (Exception $e) {
            throw new Gpf_Exception("No fileid specified");
        }        
        $driver = $this->getFileDriver();
        try {
            if($form->getFieldValue('attachment') == Gpf::YES) {
                $driver->setAttachment(true);
            }
        } catch (Exception $e) {
        }
        return $driver;
    }
    
    public function getContentDisposition() {
        return $this->contentDisposition;
    }
    
    protected function check() {
        $this->file->setAccountUserId(Gpf_Session::getAuthUser()->getAccountUserId());
    }
    
    /**
     * @return Gpf_File_DownloadDriver
     */
    protected function getFileDriver() {
        try {
            $this->check();
            $this->file->load();
            return $this->file->createDriver();
        } catch (Gpf_DbEngine_NoRow $e) {
            throw new Exception($this->_("File does not exist"));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Load information about file
     *
     * @service uploaded_file read
     * @param Gpf_Rpc_Params $parmas
     */
    public function loadFileInfo(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $dbRow = new Gpf_Db_File();
        $dbRow->setFileId($form->getFieldValue('fileid'));
        try {
            $dbRow->load();
            $form->load($dbRow);
        } catch (Gpf_Exception $e) {
            $form->setErrorMessage($this->_("Failed to load file info"));
        }
        return $form;
        
    }
    
    /**
     * Delete uploaded file from database
     *
     * @service uploaded_file delete
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Action
     */
    public function deleteFile(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        
        $dbRow = new Gpf_Db_File();
        $dbRow->setFileId($action->getParam('fileid'));
        try {
            $dbRow->load();
        } catch (Gpf_DbEngine_NoRow $e) {
            throw new Exception($this->_("Failed to delete file. File doesn't exist in database."));
        }        
        
        try {
            $dbRow->delete();
        } catch (Gpf_Exception $e) {
            $action->addError();
            $action->setErrorMessage($this->_('Failed to delete file from database.'));
            return $action;
        }
        
        $action->addOk();
        return $action;
    }
}

?>
