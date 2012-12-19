<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Dictionary.class.php 19083 2008-07-10 16:32:14Z aharsani $
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
class Gpf_Lang_CsvHandler extends Gpf_Object {

    /**
     * Translations parsed from source code
     *
     * @var array
     */
    private $sourceTranslations = array();

    /**
     * Translations computed from default language
     *
     * @var Gpf_Lang_CsvLanguage
     */
    private $referenceLanguage = array();

    const DEFAULT_LANGUAGE = 'en-US';

    private $languages = array();

    public function __construct() {
        $this->sourceTranslations = $this->getSourceTranslations();
        $this->referenceLanguage = $this->computeLanguage(self::DEFAULT_LANGUAGE);
        $this->languages[self::DEFAULT_LANGUAGE] = $this->referenceLanguage;
    }

    public function generateNewTranslationFiles($cleanDeprecated = false) {
        $this->computeNewTranslations();
        $this->exportLanguages($cleanDeprecated);
    }

    /**
     * Compute new translations for all languages
     *
     */
    private function computeNewTranslations() {
        $languagesIterator = new Gpf_Io_DirectoryIterator(Gpf_Paths::getInstance()->getLanguageInstallDirectory(), 'csv', false);
        foreach ($languagesIterator as $fullName => $file) {
            if(preg_match('/^'.Gpf_Application::getInstance()->getCode().'_(.+)\.csv$/', $file, $matches)) {
                if ($matches[1] != self::DEFAULT_LANGUAGE) {
                    $this->languages[$matches[1]] = $this->computeLanguage($matches[1]);
                }
            }
        }
    }

    /**
     * Parse all sources and return all translations
     *
     * @return array
     */
    private function getSourceTranslations() {
        $importer = new Gpf_Lang_Parser_Sources($this);
        $importer->import();
        return $importer->getSourceTranslations();
    }

    /**
     * Each application can rewrite this function and
     * compute module name depending on different conditions
     *
     * @return string
     */
    public function getModule(Gpf_Io_File $file) {
        return '';
    }

    /**
     * Compute translation array for given language code
     *
     * @param string $langCode
     * @return Gpf_Lang_CsvLanguage
     */
    protected function computeLanguage($langCode) {
        $file = new Gpf_Io_Csv_Reader(
        Gpf_Paths::getInstance()->getLanguageInstallDirectory() .
        Gpf_Application::getInstance()->getCode() .
                '_' . $langCode . '.csv', ';', '"',
        array('source','translation','type','module','status','customer'));

        $language = new Gpf_Lang_CsvLanguage();
        $language->loadFromCsvFile($file);

        //check if translation is not depreceated
        foreach ($language->getTranslations() as $translation) {
            if (!isset($this->sourceTranslations[$translation->getId()])) {
                $translation->setStatus(Gpf_Lang_Parser_Translation::STATUS_DEPRECATED);
            }
        }

        //add new translations, set translation modules
        foreach ($this->sourceTranslations as $sourceTranslation) {

            try {
                //try if it is existing translation
                $translation = $language->getTranslation($sourceTranslation, false);
                $translation->setModules($sourceTranslation->getModules());
                $translation->setType($sourceTranslation->getType());
                if ($translation->getStatus() == Gpf_Lang_Parser_Translation::STATUS_DEPRECATED) {
                    $translation->setStatus(Gpf_Lang_Parser_Translation::STATUS_NOT_TRANSLATED);
                }

                if ($translation->getStatus() == Gpf_Lang_Parser_Translation::STATUS_NOT_TRANSLATED) {
                    try {
                        //check if in reference language is this translation same or not - maybe it is already translated
                        if (is_object($this->referenceLanguage)) {
                            $refTranslation = $this->referenceLanguage->getTranslation($translation);
                            if ($translation->getDestinationMessage() != $translation->getSourceMessage() &&
                            $translation->getDestinationMessage() != $refTranslation->getDestinationMessage()) {
                                //destination message is unique, set it as translated
                                $translation->setStatus(Gpf_Lang_Parser_Translation::STATUS_TRANSLATED);
                            }
                        }
                    } catch (Exception $e) {
                    }
                }
            } catch (Exception $e) {
                //this is new translation
                $translation = clone $sourceTranslation;
                $translation->setStatus(Gpf_Lang_Parser_Translation::STATUS_NOT_TRANSLATED);
                try {
                    //load translation from reference translation if exist
                    if (is_object($this->referenceLanguage)) {
                        $refTranslation = $this->referenceLanguage->getTranslation($translation);
                        $translation->setDestinationMessage($refTranslation->getDestinationMessage());
                    } else {
                        throw new Gpf_Exception('Reference language not defined yet');
                    }
                } catch (Gpf_Exception $e) {
                    $translation->setDestinationMessage($translation->getSourceMessage());
                }
                $language->addTranslation($translation);
            }

        }
        return $language;
    }

    private function exportLanguages($cleanDeprecated = false) {
        foreach ($this->languages as $langCode => $language) {
            $fileName = Gpf_Paths::getInstance()->getLanguageInstallDirectory() .
            Gpf_Application::getInstance()->getCode() .
                '_' . $langCode . '.csv';
            $this->backupExistingFile($fileName);

            //update version information
            $language->setMetaData(Gpf_Lang_CsvLanguage::LANG_VERSION, Gpf_Application::getInstance()->getVersion());
            $language->setMetaData(Gpf_Lang_CsvLanguage::LANG_TRANSLATION_PERCENTAGE, $language->getTranslationPercentage());

            //export to csv
            $language->saveToCsv($fileName, $cleanDeprecated);
            $language->exportLanguageCache(Gpf_Paths::getInstance()->getLanguageCacheInstallDirectory());
        }
    }

    private function backupExistingFile($fileName) {
        $backup = $fileName . '.old-' . date('Y-m-d-H-i-s');
        if(file_exists($backup)) {
            @unlink($backup);
        }
        if(file_exists($fileName)) {
            @rename($fileName, $backup);
        }
    }
}
?>
