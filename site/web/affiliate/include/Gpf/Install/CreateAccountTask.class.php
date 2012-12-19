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
class Gpf_Install_CreateAccountTask extends Gpf_Tasks_LongTask {
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
    
    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }

    protected function init() {
        if($this->isAccountCreated()) {
            throw new Gpf_Exception($this->_('Account is already created.'));
        }
    }

    protected function isAccountCreated() {
        return $this->getAccountDirectory()->isDirectory();
    }

    public function getName() {
        return $this->_('Create account');
    }

    protected function execute() {
        if ($this->isPending('createAccountDirectory', $this->_('Creating Account Directory'))) {
            $this->createAccountDirectory();
            $this->setDoneAndInterrupt();
        }

        if ($this->isPending('copyTemplates', $this->_('Installing Templates'))) {
            $this->installTemplates();
            $this->setDoneAndInterrupt();
        }

        if ($this->isPending('importLanguage', $this->_('Importing Language'))) {
            $this->importLanguage();
            $this->setDone();
        }

        if ($this->isPending('insertAccount', $this->_('Creating Account'))) {
            $this->insertAccount();
            $this->setDone();
        }

        if ($this->isPending('initAccount', $this->_('Initializing Account'))) {
            $this->initializeAccount();
            $this->setDoneAndInterrupt();
        }

        self::logInstallationDone();
    }

    private static function logInstallationDone() {
        $version = new Gpf_Db_Version();
        $version->setVersion(Gpf_Application::getInstance()->getVersion());
        $version->setApplication(Gpf_Application::getInstance()->getCode());
        $version->loadFromData(array(Gpf_Db_Table_Versions::NAME, Gpf_Db_Table_Versions::APPLICATION));
        $version->setDone();
        $version->update();
    }

    private function insertAccount() {
        $this->account->setStatus(Gpf_Db_Account::APPROVED);
        try {
            $this->account->insert();
        } catch (Exception $e) {
            throw new Gpf_Exception($this->_('Could not create account. (%s)', $e->getMessage()));
        }
    }

    protected function initializeAccount() {
        $this->scheduleOutBoxRunner();
    }

    protected function createMailTemplates() {
        $this->setupTemplate(new Gpf_Mail_EmailAccountTestMail());
        $this->setupTemplate(new Gpf_Auth_RequestNewPasswordMail());
    }

    protected function setupTemplate(Gpf_Mail_Template $template) {
        $template->setup($this->account->getId());
    }

    protected function createDefaultMailAccount() {
        $mailAccount = new Gpf_Db_MailAccount();
        $mailAccount->setAsDefault(true);
        $mailAccount->setAccountName('Default Mail Account');
        $mailAccount->setAccountEmail('noreply@postaffiliatepro.com');
        $mailAccount->setFromName(Gpf_Application::getInstance()->getName());
        $mailAccount->setUseSmtp(false);
        $mailAccount->setAccountId($this->account->getId());
        $mailAccount->insert();
    }

    /**
     * Import selected language into database and
     * create language cache file in account directory
     */
    protected function importLanguage() {
        $langCode = Gpf_Session::getInstance()->getAuthUser()->getLanguage();
        $fileName = Gpf_Paths::getInstance()->getLanguageInstallDirectory() .
        Gpf_Application::getInstance()->getCode() .
                '_' . $langCode . '.csv';

        $importer = new Gpf_Lang_ImportLanguageTask($fileName, $langCode);
        $importer->run($this->maxRunTime);
    }

    protected function createAccountDirectory() {
        $accountDirectory = $this->getAccountDirectory();

        $this->createDirectory($accountDirectory);

        $cacheDirectory = new Gpf_Io_File($accountDirectory->getFileName()
        . Gpf_Paths::CACHE_DIRECTORY);
        $this->createDirectory($cacheDirectory);

        $compiledTemplates = new Gpf_Io_File($accountDirectory->getFileName() .
        Gpf_Paths::CACHE_DIRECTORY . Gpf_Templates_Smarty::COMPILED_TEMPLATES_DIR);
        $this->createDirectory($compiledTemplates);

        $files = new Gpf_Io_File($accountDirectory->getFileName() . Gpf_Paths::FILES_DIRECTORY);
        $this->createDirectory($files);

        $logs = new Gpf_Io_File($accountDirectory->getFileName() . Gpf_Log_Logs::LOGS_DIR);
        $this->createDirectory($logs);

        $exportDirectory = new Gpf_Io_File($accountDirectory->getFileName() .
        Gpf_Csv_ImportExportService::EXPORT_DIRECTORY);
        $this->createDirectory($exportDirectory);

        $configDirectory = new Gpf_Io_File($accountDirectory->getFileName() .
        Gpf_Paths::CONFIG_DIR);
        $this->createDirectory($configDirectory);

        $langCacheDirectory = new Gpf_Io_File($accountDirectory->getFileName() .
        Gpf_Paths::CACHE_DIRECTORY . Gpf_Lang_Dictionary::LANGUAGE_DIRECTORY);
        $this->createDirectory($langCacheDirectory);

        $langDirectory = new Gpf_Io_File($accountDirectory->getFileName() .
        Gpf_Lang_Dictionary::LANGUAGE_DIRECTORY);
        $this->createDirectory($langDirectory);

        $exportCache = new Gpf_Io_File($accountDirectory->getFileName() .
        Gpf_Paths::CACHE_DIRECTORY . Gpf_Csv_ImportExportService::EXPORT_DIRECTORY);
        $this->createDirectory($exportCache);

        $this->createSettingsFile();
    }

    /**
     * @return Gpf_Io_File
     */
    protected function getAccountDirectory() {
        $accountDirectory = new Gpf_Io_File(Gpf_Paths::getInstance()->getAccountsPath()
        . $this->account->getId() . '/');
        return $accountDirectory;
    }

    protected function createDirectory(Gpf_Io_File $directory) {
        if($directory->isExists() && !$directory->isWritable()) {
            throw new Gpf_Exception($this->_('Directory %s is not writable by web server user. Please remove it or set write permissions.',
            $directory->getFileName()));
        }
        if(!$directory->isExists()) {
            $directory->mkdir(true);
        }
    }

    private function copy($name) {
        $source = new Gpf_Io_File(Gpf_Paths::getInstance()->getInstallDirectoryPath() . $name);
        $target = new Gpf_Io_File(Gpf_Paths::getInstance()->getAccountsPath() . $this->account->getId() . '/' . $name);
        try {
            $task = new Gpf_Install_CopyDirectoryTask($source, $target, 0777);
            $task->run($this->maxRunTime);
        } catch (Gpf_Tasks_LongTaskInterrupt  $e) {
            throw $e;
        } catch (Exception $e) {
            throw new Gpf_Exception($this->_('Error during creating account directory %s (%s). Please delete it and try again.',
            $target->getFileName(), $e->getMessage()));
        }
    }

    protected function installTemplates() {
        $name = Gpf_Paths::TEMPLATES_DIR;
        $source = new Gpf_Io_File(Gpf_Paths::getInstance()->getInstallDirectoryPath() . $name);
        $target = new Gpf_Io_File(Gpf_Paths::getInstance()->getAccountsPath() . $this->account->getId() . '/' . $name);
        try {
            $task = new Gpf_Install_InstallTemplatesTask($source, $target);
            $task->run($this->maxRunTime);
        } catch (Gpf_Tasks_LongTaskInterrupt  $e) {
            throw $e;
        } catch (Exception $e) {
            throw new Gpf_Exception($this->_('Error during installing templates (%s)', $e->getMessage()));
        }
    }

    private function createSettingsFile() {
        $setting = new Gpf_File_Settings($this->account->getId());
        $setting->saveAll();
        @chmod($setting->getSettingFileName(), 0777);
    }

    /**
     * @param string $id
     * @param string $name
     * @param string $type
     * @param int $period period in seconds
     * @param int $frequency
     *
     * @return Gpf_Recurrence_Preset
     */
    public function addRecurrencePreset($id, $name, $type = '', $period = '', $frequency = '') {
        $preset = new Gpf_Recurrence_Preset();
        $preset->setId($id);
        $preset->setName($name);
        $preset->setType(Gpf_Db_RecurrencePreset::SYSTEM_PRESET);
        $preset->insert();

        if ($type != '') {
            $presetSetting = new Gpf_Db_RecurrenceSetting();
            $presetSetting->setRecurrencePresetId($preset->getId());
            $presetSetting->setType($type);
            $presetSetting->setPeriod($period);
            $presetSetting->setFrequency($frequency);
            $presetSetting->insert();
        }

        return $preset;
    }


    private function scheduleOutBoxRunner() {
        $outboxRunner = new Gpf_Mail_OutboxRunner();
        $outboxRunner->initParams();
        $outboxRunner->insertTask();
    }
}
?>
