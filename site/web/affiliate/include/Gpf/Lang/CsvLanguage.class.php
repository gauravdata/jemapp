<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
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
class Gpf_Lang_CsvLanguage extends Gpf_Object {
    const LANG_CODE = 'lang_code';
    const LANG_NAME = 'lang_name';
    const LANG_ENG_NAME = 'lang_english_name';
    const LANG_AUTHOR = 'lang_author';
    const LANG_VERSION = 'lang_version';
    const LANG_TRANSLATION_PERCENTAGE = 'lang_transaltion_percentage';
    const LANG_DATE_FORMAT = 'lang_date_format';
    const LANG_TIME_FORMAT = 'lang_time_format';
    const LANG_THOUSANDS_SEPARATOR = 'lang_thousands_separator';
    const LANG_DECIMAL_SEPARATOR = 'lang_decimal_separator';

    var $metaData = array();
    var $translations = array();
    var $translated = 0;
    var $depreceated = 0;

    private static function getLangCacheDirectory() {
        return Gpf_Paths::getInstance()->getAccountPath() .
        Gpf_Paths::CACHE_DIRECTORY . Gpf_Lang_Dictionary::LANGUAGE_DIRECTORY;
    }

    public function addTranslation(Gpf_Lang_Parser_Translation $translation) {
        $this->translations[$translation->getId()] = $translation;
        if ($translation->getStatus() == Gpf_Lang_Parser_Translation::STATUS_TRANSLATED) {
            $this->translated ++;
        } else if ($translation->getStatus() == Gpf_Lang_Parser_Translation::STATUS_DEPRECATED) {
            $this->depreceated ++;
        }
    }

    /**
     * Set language metadata value
     *
     * @param string $id
     * @param string $value
     */
    public function setMetaData($id, $value) {
        $this->metaData[$id] = $value;
    }

    public function getMetaValue($id) {
        if (!array_key_exists($id, $this->metaData)) {
            throw new Gpf_Exception($this->_('Language metadata not defined %s', $id));
        }
        return $this->metaData[$id];
    }

    public function getMetaData() {
        return $this->metaData;
    }

    /**
     * Get all translations from language
     *
     * @return array of Gpf_Lang_Parser_Translation
     */
    public function getTranslations() {
        return $this->translations;
    }

    /**
     * Return true if translation is already in array of translations
     *
     * @param Gpf_Lang_Parser_Translation $translation
     * @return boolean
     */

    public function existTranslation(Gpf_Lang_Parser_Translation $translation) {
        return array_key_exists($translation->getId(), $this->translations);
    }

    /**
     * Get translation  from this language for input translation
     *
     * @param Gpf_Lang_Parser_Translation $translation
     * @param boolean $strict If set to true, just exact match will be found. If false, also strings without spaces (trimmed) will be searched.
     * @return Gpf_Lang_Parser_Translation
     */
    public function getTranslation(Gpf_Lang_Parser_Translation $translation, $strict = true) {
        if ($this->existTranslation($translation)) {
            return $this->translations[$translation->getId()];
        }

        if (!$strict) {
            //check similar translation
            foreach ($this->translations as $id => $translationItem) {
                if (trim($translation->getSourceMessage()) == trim($translationItem->getSourceMessage())) {
                    $newTranslation = clone $this->translations[$id];
                    $newTranslation->setSourceMessage($translation->getSourceMessage());
                    $newTranslation->setDestinationMessage(trim($translationItem->getDestinationMessage()));
                    $this->translations[$newTranslation->getId()] = $newTranslation;
                    return $newTranslation;
                }
            }

        }

        throw new Gpf_Exception('Translation does not exist');
    }

    /**
     * Save language to csv file
     *
     * @param string $fileName
     * @param $cleanDeprecated If set to true, don't export deprecated translations
     */
    public function saveToCsv($fileName, $cleanDeprecated = false) {
        $file = new Gpf_Io_Csv_Writer($fileName,
        array('source', 'translation', 'type', 'module', 'status', 'customer'));

        //write metadata
        foreach ($this->metaData as $source => $dest) {
            $row = array();
            $row['source'] = $source;
            $row['translation'] = $dest;
            $row['type'] = Gpf_Lang_Parser_Translation::TYPE_METADATA;
            $row['module'] = '';
            $row['status'] = Gpf_Lang_Parser_Translation::STATUS_TRANSLATED;
            $row['customer'] = Gpf::NO;
            $file->writeLine($row);
        }

        //write translations
        foreach ($this->translations as $translation) {
            $row = array();
            $row['source'] = $translation->getSourceMessage();
            $row['translation'] = $translation->getDestinationMessage();
            $row['type'] = $translation->getType();
            $row['module'] = $translation->getModule();
            $row['status'] = $translation->getStatus();
            $row['customer'] = $translation->isCustomerSpecific();
            if (!($cleanDeprecated && $row['status'] == Gpf_Lang_Parser_Translation::STATUS_DEPRECATED)) {
                $file->writeRawLine($row, array('source', 'translation'));
            }
        }
    }

    /**
     * Load language from csv file
     *
     * @param Gpf_Io_Csv_Reader $file
     */
    public function loadFromCsvFile(Gpf_Io_Csv_Reader $file, $metaOnly = false) {
        foreach ($file as $record) {
            switch ($record->get('type')) {
                case 'M': //Metadata
                    $this->setMetaData($record->get('source'), $record->get('translation'));
                    break;
                case '':
                    //empty row
                    break;
                default:
                    if ($metaOnly) {
                        return;
                    }
                    try {
                        $translation = new Gpf_Lang_Parser_Translation();
                        $translation->loadFromRecord($record);
                        $this->addTranslation($translation);
                    } catch (Exception $e) {

                    }
                    break;
            }
        }
    }

    public function getTranslationPercentage() {
        if (count($this->translations) > 0) {
            return round($this->translated/count($this->translations) * 100);
        }
        return 0;
    }

    public function getCode() {
        return $this->getMetaValue(self::LANG_CODE);
    }

    /**
     * Create language cache files in account folder
     */
    public function exportAccountCache() {
        $this->exportLanguageCache(self::getLangCacheDirectory());
        $this->saveToCsv(Gpf_Lang_CsvLanguage::getAccountCsvFileName($this->getCode()));
    }

    /**
     * Export cache language file and store it to input directory
     *
     * @param string $dirName directory, where should be stored cache file
     */
    public function exportLanguageCache($dirName) {
        $this->exportServerCacheFile($dirName);
        $this->exportClientCacheFile($dirName);
    }

    public function getCacheFileName($dirName, $isServer) {
        return $dirName . Gpf_Application::getInstance()->getCode() . '_' .
        $this->getCode() . '.' . ($isServer ? 's' : 'c') . '.php';
    }

    private function exportClientCacheFile($dirName) {
        $file = new Gpf_Io_File($this->getCacheFileName($dirName, false));
        $file->setFileMode('w');

        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->setHeader(array('source', 'translation'));

        foreach ($this->translations as $translation) {
            if ($translation->getType() == Gpf_Lang_Parser_Translation::TYPE_CLIENT || $translation->getType() == Gpf_Lang_Parser_Translation::TYPE_BOTH) {
                if ($translation->getStatus() != 'D') {
                    $recordSet->add(array($translation->getSourceMessage(), $translation->getDestinationMessage()));
                }
            }
        }
        $recordSet->add(array("_dateFormat", $this->getMetaValue(self::LANG_DATE_FORMAT)));
        $recordSet->add(array("_timeFormat", $this->getMetaValue(self::LANG_TIME_FORMAT)));

        $recordSet->add(array("_thousandsSeparator", $this->getMetaValue(self::LANG_THOUSANDS_SEPARATOR)));
        $recordSet->add(array("_decimalSeparator", $this->getMetaValue(self::LANG_DECIMAL_SEPARATOR)));

        $encoder = new Gpf_Rpc_Json();

        $file->write(addcslashes($encoder->encodeResponse($recordSet), '"\\'));
        $file->close();
    }

    private function exportServerCacheFile($dirName) {
        $file = new Gpf_Io_File($this->getCacheFileName($dirName, true));
        $file->setFileMode('w');
        $file->write("<?php\n");
        $file->write("// DON'T CHANGE THIS FILE !!!\n
\$_code='" . $this->getCode() . "';
\$_name='" . $this->getMetaValue(self::LANG_NAME) . "';
\$_engName='" . $this->getMetaValue(self::LANG_ENG_NAME) . "';
\$_author='" . $this->getMetaValue(self::LANG_AUTHOR) . "';
\$_version='" . $this->getMetaValue(self::LANG_VERSION) . "';
\$_dateFormat='" . $this->getMetaValue(self::LANG_DATE_FORMAT) . "';
\$_timeFormat='" . $this->getMetaValue(self::LANG_TIME_FORMAT) . "';
\$_thousandsSeparator='" . $this->getMetaValue(self::LANG_THOUSANDS_SEPARATOR) . "';
\$_decimalSeparator='" . $this->getMetaValue(self::LANG_DECIMAL_SEPARATOR) . "';
");
        $file->write("\$_dict=array(\n");

        foreach ($this->translations as $translation) {
            if (($translation->getType() == Gpf_Lang_Parser_Translation::TYPE_SERVER || $translation->getType() == Gpf_Lang_Parser_Translation::TYPE_BOTH) ) {
                if ($translation->getStatus() != 'D') {
                    $file->write('\'' . addcslashes($translation->getSourceMessage(), "'") . '\'=>\'' . addcslashes($translation->getDestinationMessage(), "'") . "',\n");
                }
            }
        }

        $file->write("'_dateFormat'=>'" .$this->getMetaValue(self::LANG_DATE_FORMAT) .
        "',\n'_timeFormat'=>'" .$this->getMetaValue(self::LANG_TIME_FORMAT) .
        "',\n'_thousandsSeparator'=>'" .$this->getMetaValue(self::LANG_THOUSANDS_SEPARATOR) .
        "',\n'_decimalSeparator'=>'" .$this->getMetaValue(self::LANG_DECIMAL_SEPARATOR) . "');\n");

        $file->write("?>");
        $file->close();
    }


    /**
     * Copy metadata from database object representing language in db
     *
     * @param Gpf_Db_Language $lang
     */
    public function copyMetadataFromDbLanguage(Gpf_Db_Language $lang) {
        $this->setMetaData(self::LANG_AUTHOR, $lang->getAuthor());
        $this->setMetaData(self::LANG_CODE, $lang->getCode());
        $this->setMetaData(self::LANG_DATE_FORMAT, $lang->getDateFormat());
        $this->setMetaData(self::LANG_ENG_NAME, $lang->getEnglishName());
        $this->setMetaData(self::LANG_NAME, $lang->getName());
        $this->setMetaData(self::LANG_TIME_FORMAT, $lang->getTimeFormat());
        $this->setMetaData(self::LANG_THOUSANDS_SEPARATOR, $lang->getThousandsSeparator());
        $this->setMetaData(self::LANG_DECIMAL_SEPARATOR, $lang->getDecimalSeparator());
    }

    /**
     * Rebuild cache for language defined by id
     *
     * @param $languageId
     */
    public static function rebuildLanguageCache($languageId) {
        $lang = new Gpf_Db_Language();
        $lang->setId($languageId);
        $lang->load();

        $fileName =  Gpf_Lang_CsvLanguage::getAccountCsvFileName($lang->getCode());

        $csvLang = new Gpf_Lang_CsvLanguage();
        $csvLang->loadFromCsvFile(new Gpf_Io_Csv_Reader($fileName));
        $csvLang->copyMetadataFromDbLanguage($lang);

        $csvLang->exportAccountCache();
    }

    public static function getAccountCsvFileName($languageCode) {
        return Gpf_Paths::getInstance()->getLanguageAccountDirectory() .
        Gpf_Application::getInstance()->getCode() . '_' . $languageCode . '.csv';
    }

    public function incrementTranslatedCount() {
        $this->translated++;
    }

}
?>
