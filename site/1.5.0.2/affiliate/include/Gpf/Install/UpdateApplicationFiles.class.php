<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
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
class Gpf_Install_UpdateApplicationFiles extends Gpf_Install_Step {
    
    const FTP_USERNAME  = 'Username';
    const FTP_PASSWORD  = 'Password';
    const FTP_HOSTNAME  = 'Hostname';
    const FTP_DIRECTORY = 'Directory';
    
    const FILE_EXTENSION = '.zip';
    
    private $distributionFileName;
    
    public function __construct() {
        parent::__construct();
        $this->code = 'Update-Files';
        $this->name = $this->_('Update files'); 
        $this->distributionFileName = $this->getNewDistributionFileName();
    }
    
    public function isAvailable() {
        return $this->distributionFileName != null;
    }
    
    private function getNewDistributionFileName() {
        $filePreffix = Gpf_Application::getInstance()->getCode() . '_';
        $directory = new Gpf_Io_DirectoryIterator(Gpf_Paths::getInstance()->getTopPath().Gpf_paths::INSTALL_DIR, self::FILE_EXTENSION);
        $maxVersion = '0';
        foreach ($directory as $file) {
            if (strpos($file, $filePreffix) !== 0) {
                continue;
            }
            $version = substr($file, strlen($filePreffix), strlen($file) - strlen(self::FILE_EXTENSION) - strlen($filePreffix));
            if (version_compare($maxVersion, $version) == -1) {
                $maxVersion = $version;
            }
            
        }
        if (version_compare($maxVersion, Gpf_Application::getInstance()->getVersion()) <=0) {
            return null;
        }
        return $filePreffix . $maxVersion. self::FILE_EXTENSION;
    }
        
    /**
     * @anonym 
     * @param Gpf_Rpc_Params $params
     * @service
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        $username = Gpf_Settings::get(Gpf_Settings_Gpf::FTP_USERNAME);
        $password = Gpf_Settings::get(Gpf_Settings_Gpf::FTP_PASSWORD);
        $hostname = Gpf_Settings::get(Gpf_Settings_Gpf::FTP_HOSTNAME);
        $directory = Gpf_Settings::get(Gpf_Settings_Gpf::FTP_DIRECTORY);
        
        if($this->checkFtpConnectionSetAndOk($hostname, $directory, $username, $password) === true) {
            $form->setField(self::FTP_USERNAME,  $username);
            $form->setField(self::FTP_PASSWORD,  '*****');
            $form->setField(self::FTP_HOSTNAME,  $hostname);
            $form->setField(self::FTP_DIRECTORY, $directory);
        }
        return $form;
    }
    
    protected function execute(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $hostname = $form->getFieldValue(self::FTP_HOSTNAME);
        if (substr($hostname, 0, 6) == 'ftp://') {
            $hostname = substr($hostname, 6);
        }
        $username = $form->getFieldValue(self::FTP_USERNAME);
        $password = $form->getFieldValue(self::FTP_PASSWORD);
        if($password == '*****') {
            $password = Gpf_Settings::get(Gpf_Settings_Gpf::FTP_PASSWORD);
        }
        $directory = $form->getFieldValue(self::FTP_DIRECTORY);
        
        $checkResult = $this->checkFtpConnectionSetAndOk($hostname, $directory, $username, $password);
        if($checkResult !== true) {
            $form->setErrorMessage($checkResult);
            return $form;
        }
        
        $this->writeFtpInfo($hostname, $directory, $username, $password);

        try {
            $task = new Gpf_Install_UpdateApplicationFilesTask($this->distributionFileName);
            $task->run();
        } catch (Gpf_Tasks_LongTaskInterrupt $e) {
            $this->setResponseType($form, self::PART_DONE_TYPE);
            $form->setInfoMessage($e->getMessage());
            return $form;
        } catch (Exception $e) {
            $form->setErrorMessage($this->_('Error during files update (%s)', $e->getMessage()));
            return $form;
        }
        $form->setInfoMessage($this->_('Files updated'));
        $this->setNextStep($form);
        return $form;
    }
    
    private function writeFtpInfo($hostname, $directory, $username, $password) {
        $settingFile = new Gpf_File_Settings();
        try {
            $settingFile->getAll();
        } catch (Exception $e) {
        }
        $settingFile->setSetting(Gpf_Settings_Gpf::FTP_USERNAME,  $username, false);
        $settingFile->setSetting(Gpf_Settings_Gpf::FTP_PASSWORD,  $password, false);
        $settingFile->setSetting(Gpf_Settings_Gpf::FTP_HOSTNAME,  $hostname, false);
        $settingFile->setSetting(Gpf_Settings_Gpf::FTP_DIRECTORY, $directory, false);
        $settingFile->saveAll();
    }
    
    private function checkFtpConnectionSetAndOk($hostname, $directory, $username, $password) {
        $ftp = new Gpf_Io_Ftp();
        $ftp->setParams($hostname, $directory, $username, $password);
        try {
            $ftp->connect();
        } catch (Gpf_Exception $e) {
            $ftp->close();
            return $e->getMessage();
        }
        try {
            $fileList = $ftp->getFileList(Gpf_Paths::INSTALL_DIR);
        } catch (Gpf_Exception $e) {
            $ftp->close();
            return $this->_('Invalid main directory');
        }
        if (array_key_exists($this->distributionFileName, array_values($fileList))) {
            $ftp->close();
            return $this->_('Invalid main directory');
        }
        $ftp->close();
        return true;
    }
}
?>
