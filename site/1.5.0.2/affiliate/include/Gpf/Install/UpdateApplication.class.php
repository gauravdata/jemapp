<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
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
class Gpf_Install_UpdateApplication extends Gpf_Install_Step {
    
    public function __construct() {
        parent::__construct();
        $this->code = 'Update-Version';
        $this->name = $this->_('Update Version'); 
    }
    
    /**
     *
     * @service
     * @anonym
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Data
     */
    public function getHtml(Gpf_Rpc_Params $params) {
        $smarty = new Gpf_Templates_Template("update_version.stpl");
        try {
            $smarty->assign('installedVersion', Gpf_Application::getInstance()->getInstalledVersion());
            $smarty->assign('gpfInstalledVersion', Gpf_Application::getInstance()->getInstalledVersion(true));
        } catch (Gpf_Exception $e) {
        	$smarty->assign('installedVersion', $this->_('Not availible'));
        	$smarty->assign('gpfInstalledVersion', $this->_('Not availible'));
        }        
        $smarty->assign('newVersion', Gpf_Application::getInstance()->getVersion());
        $smarty->assign('gpfNewVersion', Gpf::GPF_VERSION);
        $smarty->assign('applicationName', Gpf_Application::getInstance()->getName());
        return $smarty->getDataResponse();
    }
    
    /**
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
    }
    
    protected function execute(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        $updateFile = $this->getNextUpdateFile();
        if($updateFile !== null) {
            try {
                Gpf_Install_CreateDatabaseTask::setStorageEngine($this->createDatabase());
                $updateFile->execute();
            } catch (Exception $e) {
                $form->setErrorMessage($e->getMessage());
                return $form;
            }
            $this->setResponseType($form, self::PART_DONE_TYPE);
            $form->setInfoMessage($this->_('Updated to version %s-%s', $updateFile->getApplication(), $updateFile->getVersion()));
            return $form;
        }
        
        $task = $this->getUpdateAccountTask();

        try {
            $task->run();
        } catch (Gpf_Tasks_LongTaskInterrupt $e) {
            $this->setResponseType($form, self::PART_DONE_TYPE);
            $form->setInfoMessage($e->getMessage());
            return $form;
        } catch (Exception $e) {
            $form->setErrorMessage($this->_('Error during application update (%s)', $e->getMessage()));
            return $form;
        }
        $this->setNextStep($form);
        return $form;
    }
    
    /**
     *
     * @return Gpf_Install_UpdateAccountTask
     */
    private function getUpdateAccountTask() {
        $account = Gpf_Application::getInstance()->createAccount();
        $account->setDefaultId();
        return $account->getUpdateTask();
    }
    
    public function updateAccount() {
        $this->getUpdateAccountTask()->run(Gpf_Tasks_LongTask::NO_INTERRUPT);
    }
    
    public function updateVersion() {
        while(null !== ($updateFile = $this->getNextUpdateFile())) {
            $updateFile->execute();
        }
    }
    
    /**
     * @return Gpf_Install_DbFile
     */
    private function getNextUpdateFile() {
        foreach (new Gpf_Install_UpdateFiles() as $version) {
            return $version;
        }
    
        foreach(new Gpf_Install_UpdateFiles(Gpf_Application::getInstance()->getCode()) as $version) {
            return $version;
        }
        return null;
    }
}
?>
