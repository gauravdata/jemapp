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
class Gpf_Lang_CachedLanguageFile extends Gpf_Object {
    private $directory;
    private $languageCode;

    public function __construct($directory, $languageCode) {
        $this->directory = $directory;
        $this->languageCode = $languageCode;
    }

    private function getFilename($server = true) {
        $ext = 's';
        if(!$server) {
            $ext = 'c';
        }
        $application = Gpf_Application::getInstance()->getCode();
        return $this->directory . $application . '_' . $this->languageCode . ".$ext.php";
    }

    public function regenerateLanguageCacheFiles() {
        //load langage
        $dbLang = new Gpf_Db_Language();
        $dbLang->setAccountId(Gpf_Session::getInstance()->getAuthUser()->getAccountId());
        $dbLang->setCode($this->languageCode);
        $dbLang->setId($dbLang->generateId());
        $dbLang->load();
        $lang = new Gpf_Lang_CsvLanguage();
        $lang->loadFromCsvFile(new Gpf_Io_Csv_Reader(Gpf_Lang_CsvLanguage::getAccountCsvFileName($this->languageCode)));
        $lang->exportAccountCache();
    }

    public function loadClientMessages() {
        $file = new Gpf_Io_File($this->getFilename(false));
        try {
            $file->open('r');
        } catch (Exception $e) {
            try {
                $this->regenerateLanguageCacheFiles();
                $file->open('r');
            } catch (Exception $e2) {
                throw new Gpf_Exception($this->_('Could not open language file %s', $e2->getMessage()));
            }
        }

        @include($file->getFileName());
        return $_dict;
    }

    public function load(Gpf_Lang_Language $language) {
        $file = new Gpf_Io_File($this->getFilename());
        try {
            $file->open('r');
        } catch (Exception $e) {
            try {
                $this->regenerateLanguageCacheFiles();
                $file->open('r');
            } catch (Exception $e2) {
                throw new Gpf_Exception($this->_('Could not open language file %s', $e2->getMessage()));
            }
        }

        $_name = '';
        $_engName = '';
        $_author = '';
        $_version = '';
        $_dict = '';
        $_dateFormat = '';
        $_timeFormat = '';
        $_thousandsSeparator = '';
        $_decimalSeparator = '';

        if (@eval(str_replace(array('<?php', '?>'), '',$file->getContents())) === false) {
            throw new Gpf_Exception($this->_('Corrupted language file %s', $file->getFileName()));
        }

        @$language->setName($_name);
        @$language->setEnglishName($_engName);
        @$language->setAuthor($_author);
        @$language->setVersion($_version);
        @$language->setDictionary($_dict);
        @$language->setDateFormat($_dateFormat);
        @$language->setTimeFormat($_timeFormat);
        @$language->setThousandsSeparator($_thousandsSeparator);
        @$language->setDecimalSeparator($_decimalSeparator);
    }
}
?>
