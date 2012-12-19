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
class Gpf_Install_UpdateTemplatesTask extends Gpf_Install_CopyAndBackupDirectoryTask {
    private $sourceOffset;
    /**
     * @var Gpf_Db_InstalledTemplate
     */
    private $installedTemplate;

    public function __construct(Gpf_Io_File $source, Gpf_Io_File $target) {
        parent::__construct($source, $target);
        $this->sourceOffset = strlen(rtrim($source->getFileName(), '/\\')) + 1;
    }

    protected function isFileChanged(Gpf_Io_File $source, Gpf_Io_File $target) {
        if(!$this->installedTemplate->isPersistent()) {
            return parent::isFileChanged($source, $target);
        }
        return $this->installedTemplate->getContentHash() != md5($target->getContents());
    }

    protected function copy(Gpf_Io_File $source, Gpf_Io_File $target) {
        if ($this->isCustomPageTemplate($source)) {
            return;
        }

        if ($this->isInstallThemeConfigFile($source, $target)) {
            Gpf_Io_File::copy($source, $target);
            return;
        }

        if ($this->isThemeConfigFile($source)) {
            $accountsThemeConfigFile = $this->getAccuontsThemeConfig($target);

            $isThemeEnabled = $this->getEnabled($accountsThemeConfigFile);
            Gpf_Io_File::copy($source, $target);

            $accountsThemeConfigFile = $this->getAccuontsThemeConfig($target);
            $accountsThemeConfigFile->setSetting(Gpf_Desktop_Theme::ENABLED, $isThemeEnabled ? 'Y' : 'N');

            return;
        }

        $this->installedTemplate = new Gpf_Db_InstalledTemplate();
        $this->installedTemplate->setName(substr($source->getFileName(), $this->sourceOffset));
        try {
            $this->installedTemplate->load();
        } catch (Exception $e) {
        }

        parent::copy($source, $target);

        $this->installedTemplate->setContentHash(md5($source->getContents()));
        $this->installedTemplate->setVersion(md5($source->getContents()));
        $this->installedTemplate->setOverwriteExisting($this->resourceOverwritten);
        $this->installedTemplate->save();
    }

    private function isThemeConfigFile(Gpf_Io_File $file) {
        return strpos($file->getFileName(),Gpf_Desktop_Theme::CONFIG_FILE_NAME);
    }

    private function isInstallThemeConfigFile(Gpf_Io_File $file, Gpf_Io_File $target) {
        return $this->isThemeConfigFile($file) && strpos($target->getFileName(),'/install/');
    }

    private function getEnabled(Gpf_File_Config $themeConfigFile) {
        if($themeConfigFile->hasSetting(Gpf_Desktop_Theme::ENABLED)) {
            return $themeConfigFile->getSetting(Gpf_Desktop_Theme::ENABLED) == 'Y';
        }
        return true;
    }

    private function getAccuontsThemeConfig(Gpf_Io_File $accountThemeConfigFile) {
        $configFile = new Gpf_File_Config($accountThemeConfigFile->getFileName());
        $configFile->getAll();
        return $configFile;
    }

    private function isCustomPageTemplate(Gpf_Io_File $templateFile) {
        if (strstr($templateFile->getFileName(), Gpf_Paths::DEFAULT_THEME . 'custom/')) {
            return true;
        }
        return false;
    }
}
?>
