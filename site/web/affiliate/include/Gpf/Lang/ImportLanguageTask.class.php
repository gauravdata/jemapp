<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani, Viktor Zeman
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
class Gpf_Lang_ImportLanguageTask extends Gpf_Tasks_LongTask {
    /**
     * Name of file to be imported into db
     *
     * @var string
     */
    private $fileName;

    private $overwrite = true;

    private $langcode;

    private $saveMetadata = true;

    /**
     * @var Gpf_Lang_CsvLanguage
     */
    private $language;

    public function __construct($fileName, $languageCode, $overwrite = true, $saveMetadata = true) {
        $this->fileName = $fileName;
        $this->overwrite = $overwrite;
        $this->langcode = $languageCode;
        $this->saveMetadata = $saveMetadata;
        $this->setParams(serialize(array('filename' => $this->fileName, 'code' => $languageCode)));
    }

    protected function getTaskType() {
        return Gpf_Db_Task::TYPE_USER;
    }

    /**
     *
     * @return Gpf_Db_Language
     */
    private function loadLanguage() {
        $lang = new Gpf_Db_Language();
        $lang->setCode($this->langcode);
        $lang->setAccountId(Gpf_Application::getInstance()->getAccountId());
        $lang->setId($lang->generateId());
        try {
            $lang->load();
        } catch (Exception $e) {
        }
        return $lang;
    }

    public function saveLanguageMetadata(Gpf_Db_Language $lang) {
        $lang->setName($this->language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_NAME));
        $lang->setEnglishName($this->language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_ENG_NAME));
        $lang->setActive(true);
        $lang->setAuthor($this->language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_AUTHOR));
        $lang->setVersion($this->language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_VERSION));
        $lang->setImported(Gpf_DbEngine_Database::getDateString());
        $lang->setDateFormat($this->language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_DATE_FORMAT));
        $lang->setTimeFormat($this->language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_TIME_FORMAT));
        $lang->setThousandsSeparator($this->language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_THOUSANDS_SEPARATOR));
        $lang->setDecimalSeparator($this->language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_DECIMAL_SEPARATOR));
        $lang->setTranslatedPercentage($this->language->getTranslationPercentage());
        $lang->setIsDefault(false);
        $lang->save();
    }

    /**
     * If exists language with this code in account directory, load custom translations
     *
     * @param $language
     */
    private function loadCustomTranslations(Gpf_Lang_CsvLanguage $language) {
        $origFileName =  Gpf_Lang_CsvLanguage::getAccountCsvFileName($language->getCode());

        $file = new Gpf_Io_File($origFileName);

        if ($file->isExists()) {
            $file = new Gpf_Io_Csv_Reader($origFileName);
            foreach ($file as $record) {
                switch ($record->get('type')) {
                    case 'M': //Metadata - no processing required
                        break;
                    case '':
                        //empty row - no processing required
                        break;
                    default:
                        try {
                            $translation = new Gpf_Lang_Parser_Translation();
                            $translation->loadFromRecord($record);

                            if (!$language->existTranslation($translation) && $translation->isCustomerSpecific() == Gpf::YES) {
                                //add missing customer translation from current account language
                                $language->addTranslation($translation);
                            } else {
                                //replace custom translation with own text even if it was already in file
                                $existingTranslation = $language->getTranslation($translation);
                                if ($translation->isCustomerSpecific() == Gpf::YES) {
                                    //keep custom translation from existing language file !
                                    $existingTranslation->setDestinationMessage($translation->getDestinationMessage());
                                    $existingTranslation->setCustomerSpecific($translation->isCustomerSpecific());
                                }
                            }
                        } catch (Exception $e) {

                        }
                        break;
                }
            }
            $file->close();
        }
    }

    /**
     * If exists language with this code in account directory already, backup translation file
     *
     * @param $language
     */
    private function backupOriginalTranslation(Gpf_Lang_CsvLanguage $language) {
        $origFileName =  Gpf_Lang_CsvLanguage::getAccountCsvFileName($language->getCode());

        $file = new Gpf_Io_File($origFileName);
        if ($file->isExists()) {
            Gpf_Io_File::copy($file,
            new Gpf_Io_File($origFileName . '.v' .
            str_replace('.', '', Gpf_Application::getInstance()->getVersion()) . '_' . date("YmdHis") ));
        }
    }

    public function getName() {
        return $this->_('Import language file %s', $this->fileName);
    }

    protected function execute() {
        $lang = $this->loadLanguage();

        $file = new Gpf_Io_Csv_Reader($this->fileName, ';', '"',
        array('source','translation','type','module','status','customer'));
        $this->language = new Gpf_Lang_CsvLanguage();
        $this->language->loadFromCsvFile($file);

        $pendingMessage = $this->_('Importing language dictionary %s (%s)',
        $this->language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_NAME),
        $this->language->getMetaValue(Gpf_Lang_CsvLanguage::LANG_ENG_NAME));

        if ($this->saveMetadata) {
            if($this->isPending('saveMetadata', $pendingMessage)) {
                $this->saveLanguageMetadata($lang);
                $this->setDone();
            }
        }

        if($this->isPending('loadCustomTranslations', $pendingMessage)) {
            $this->loadCustomTranslations($this->language);
            $this->setDone();
        }

        if($this->isPending('backupOriginalTranslation', $pendingMessage)) {
            $this->backupOriginalTranslation($this->language);
            $this->setDone();
        }

        if($this->isPending('exportCache', $this->_('Exporting dictionary cache.'))) {
            $this->language->exportAccountCache();
            $this->setDone();
        }
    }
}
?>
