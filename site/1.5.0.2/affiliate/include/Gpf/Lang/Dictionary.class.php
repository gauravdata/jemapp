<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Dictionary.class.php 32373 2011-05-04 09:24:46Z dmilonova $
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
class Gpf_Lang_Dictionary extends Gpf_Object {
    const LANGUAGE_DIRECTORY = 'lang/';
    const LANGUAGE_REQUEST_PARAMETER = 'l';

    /**
     * Array of language dictonary instances. For each language code can be here own instance.
     *
     * @var array
     */
    protected static $instances = array();

    /**
     * @var Gpf_Lang_Language
     */
    private $language;

    protected function __construct() {
    }

    /**
     * @param string $langCode language code for which you need instance
     * @return Gpf_Lang_Dictionary
     */
    public static function getInstance($langCode = '') {
        if(!array_key_exists($langCode, self::$instances)) {
            self::$instances[$langCode] = new Gpf_Lang_Dictionary();
            if ($langCode != '') {
                try {
                    self::$instances[$langCode]->load($langCode);
                } catch (Exception $e) {
                }
            }
            setlocale(LC_ALL, 'en_US.UTF-8');
        }
        return self::$instances[$langCode];
    }

    /**
     * Compute default language in following order:
     * 1. try if language parameter is not set in request
     * 2. try if cookie doesn't contain language selection from the past
     * 3. try load language settings from browser preferences
     * 4. load default system language
     *
     * @return string Default language code
     */
    public static function getDefaultLanguage() {
        //try if language was not defined by language parameter in request
        if (isset($_REQUEST[self::LANGUAGE_REQUEST_PARAMETER]) && self::isLanguageSupported($_REQUEST[self::LANGUAGE_REQUEST_PARAMETER]) ) {
            return $_REQUEST[self::LANGUAGE_REQUEST_PARAMETER];
        }

        //try if language was not defined in cookie parameter
        if (isset($_COOKIE[Gpf_Auth_Service::COOKIE_LANGUAGE]) &&
        self::isLanguageSupported($_COOKIE[Gpf_Auth_Service::COOKIE_LANGUAGE])) {
            return $_COOKIE[Gpf_Auth_Service::COOKIE_LANGUAGE];
        }

        //try load language from browser
        if (($acceptLang = Gpf_Lang_Dictionary::getBrowserLanguage()) !== false) {
            return $acceptLang;
        }

        //use default system language
        return self::getDefaultSystemLanguage();
    }

    public static function getDefaultSystemLanguage() {
        try {
            $defaultLanguage = Gpf_Db_Table_Languages::getInstance()->getDefaultLanguage();
            $langCode = $defaultLanguage->getCode();
            if ($langCode != null) {
                return $langCode;
            }
        } catch (Exception $e) {
        }
        return Gpf_Application::getInstance()->getDefaultLanguage();
    }

    public static function isLanguageSupported($langCode) {
        static $languages;
        if ($languages == null) {
            try {
                $languagesObj = Gpf_Lang_Languages::getInstance();
                $languages = $languagesObj->getActiveLanguagesNoRpc();
            } catch (Exception $e) {
                return false;
            }
        }
        return $languages->existsRecord($langCode);
    }

    /**
     * Get first supported language browser
     *
     * @return string If none supported language was found, return false
     */
    private static function getBrowserLanguage() {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $languages = explode(',', str_replace(' ', '', $_SERVER['HTTP_ACCEPT_LANGUAGE']));
            foreach($languages as $language) {
                $arrLang = explode(';', $language);
                $langCode = self::decodeLanguageCode($arrLang[0]);
                if (self::isLanguageSupported($langCode)) {
                    return $langCode;
                }
            }
        }
        return false;
    }

    /**
     * @param String $browserLangCode
     * @return String
     */
    public static function decodeLanguageCode($browserLangCode) {
        $langCode = strtolower($browserLangCode);
        if (strlen($browserLangCode) > 2) {
            $langCode = substr($langCode, 0, 2) . strtoupper(substr($browserLangCode, 2));
        }
        return $langCode;
    }

    protected function isSupportedLanguage($languageCode) {
        return self::isLanguageSupported($languageCode);
    }

    public function load($languageCode) {
        if (!$this->isSupportedLanguage($languageCode)) {
            $languageCode = self::getDefaultLanguage();
        }
        $language = new Gpf_Lang_Language($languageCode);
        $language->load();
        $this->language = $language;
        self::$instances[$languageCode] = $this;
        return $languageCode;
    }

    public function getEncodedClientMessages() {
        if ($this->getLanguage() != null) {
            $langCode = $this->getLanguage()->getCode();
        } else {
            $langCode = $this->getDefaultSystemLanguage();
        }
        $file = new Gpf_Io_File(Gpf_Paths::getInstance()->getLanguageCacheDirectory()
        . Gpf_Application::getInstance()->getCode() . '_' .
        $langCode . '.c.php');
        return $file->getContents();
    }

    /**
     *
     * @return Gpf_Data_RecordSet
     */
    public function getClientMessages() {
        if($this->language === null) {
            $this->load(Gpf_Session::getAuthUser()->getLanguage());
        }
        $recordSet = new Gpf_Data_RecordSet();
        $recordSet->setHeader(array('source', 'translation'));

        foreach ($this->language->getClientMessages() as $source => $translation) {
            $recordSet->add(array($source, $translation));
        }
        return $recordSet;
    }

    public function get($message) {
        if($this->language === null) {
            return $message;
        }
        return $this->language->localize($message);
    }

    /**
     * return language definition
     *
     * @return Gpf_Lang_Language
     */
    public function getLanguage() {
        return $this->language;
    }
}
?>
