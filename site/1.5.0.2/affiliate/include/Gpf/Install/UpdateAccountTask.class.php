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
class Gpf_Install_UpdateAccountTask extends Gpf_Tasks_LongTask {
    /**
     *
     * @var Gpf_Db_Account
     */
    protected $account;

    public function setAccount(Gpf_Db_Account $account) {
        $this->account = $account;
        $paramsString = $account->getId().$account->getName().$account->getStatus().$account->getApplication().$account->getEmail();
        $this->setParams(md5(serialize($paramsString)));
    }

    public function getName() {
        return $this->_('Update account');
    }
    
    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }

    protected function execute() {
        $this->updateAccountDirectory();
        self::logInstallationDone();
    }

    private static function logInstallationDone() {
        $version = new Gpf_Db_Version();
        $version->setVersion(Gpf_Application::getInstance()->getVersion());
        $version->setApplication(Gpf_Application::getInstance()->getCode());
        $version->setDone();
        try {
            $version->loadFromData(array(Gpf_Db_Table_Versions::NAME, Gpf_Db_Table_Versions::APPLICATION));
        } catch (Exception $e) {
        }
        $version->save();

        $version = new Gpf_Db_Version();
        $version->setVersion(Gpf::GPF_VERSION);
        $version->setApplication(Gpf::CODE);
        $version->setDone();
        try {
            $version->loadFromData(array(Gpf_Db_Table_Versions::NAME, Gpf_Db_Table_Versions::APPLICATION));
        } catch (Exception $e) {
        }
        $version->save();
    }

    protected function importLanguages() {
        $languages = Gpf_Lang_Languages::getInstance(true)->getActiveLanguagesNoRpc();
        foreach ($languages as $code => $record) {
            if($this->isPending('updatingLanguage' . $code,
                $this->_('Updating %s Language', $record->get(Gpf_Db_Table_Languages::ENGLISH_NAME)))) {
                $this->importLanguage($code);
                $this->setDoneAndInterrupt();
            }
        }
    }

    /**
     * Import selected language into database and
     * create language cache file in account directory
     */
    protected function importLanguage($code) {
        $fileName = Gpf_Paths::getInstance()->getLanguageInstallDirectory() .
            Gpf_Application::getInstance()->getCode() .
                '_' . $code . '.csv';

        if(!Gpf_Io_File::isFileExists($fileName)) {
            return;
        }

        $importer = new Gpf_Lang_ImportLanguageTask($fileName, $code, false);
        $importer->run();
    }

    private function removeTemplateCache() {
        $accountDirectory = $this->getAccountDirectory();
        $cacheTemplates = $accountDirectory->getFileName() .
            Gpf_Paths::CACHE_DIRECTORY . Gpf_Templates_Smarty::COMPILED_TEMPLATES_DIR;
        foreach (new Gpf_Io_DirectoryIterator($cacheTemplates, '', true) as $fullName => $file) {
            $this->checkInterruption();
            $file = new Gpf_Io_File($fullName);
            $file->delete();
        }
    }

    protected function updateAccountDirectory() {
        if ($this->isPending('clearTemplatesCache', $this->_('Clearing templates cache'))) {
            try {
                $this->removeTemplateCache();
            } catch (Exception $e) {
            }
            $this->setDoneAndInterrupt();
        }

        if ($this->isPending('copyTemplates', $this->_('Installing templates'))) {
            $this->copyTemplates();
            $this->setDoneAndInterrupt();
        }
        
        if ($this->isPending('updateEngineConfig', $this->_('Updating plugin engine'))) {
            Gpf_Plugins_Engine::getInstance()->refreshConfiguration();
            $this->setDone();
        }

        $this->importLanguages();
    }



    private function copyTemplates() {
        $source = new Gpf_Io_File(Gpf_Paths::getInstance()->getInstallDirectoryPath() . Gpf_Paths::TEMPLATES_DIR);
        $target = new Gpf_Io_File(Gpf_Paths::getInstance()->getAccountsPath() . $this->account->getId() . '/' . Gpf_Paths::TEMPLATES_DIR);

        $task = new Gpf_Install_UpdateTemplatesTask($source, $target);
        $task->run($this->maxRunTime);
    }

    /**
     * @return Gpf_Io_File
     */
    protected function getAccountDirectory() {
        $accountDirectory = new Gpf_Io_File(Gpf_Paths::getInstance()->getAccountsPath()
            . $this->account->getId() . '/');
            return $accountDirectory;
    }
}
?>
