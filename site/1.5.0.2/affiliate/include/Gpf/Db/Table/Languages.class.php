<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Files.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_Table_Languages extends Gpf_DbEngine_Table {
    const ID = 'languageid';
    const CODE = 'code';
    const NAME = 'name';
    const ENGLISH_NAME = 'eng_name';
    const ACTIVE = 'active';
    const AUTHOR = 'author';
    const VERSION = 'version';
    const IMPORTED = 'imported';
    const DATE_FORMAT = 'dateformat';
    const TIME_FORMAT = 'timeformat';
    const THOUSANDS_SEPARATOR = 'thousandsseparator';
    const DECIMAL_SEPARATOR = 'decimalseparator';
    const TRANSLATED_PERCENTAGE = 'translated';
    const IS_DEFAULT = 'is_default';
    const ACCOUNTID = 'accountid';

    private static $instance;

    /**
     * Default language object
     *
     * @var Gpf_Db_Language
     */
    private $defaultLanguage = null;

    /**
     * @return Gpf_Db_Table_Languages
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('g_languages');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 40, false);
        $this->createColumn(self::CODE, 'char', 5);
        $this->createColumn(self::NAME, 'varchar', 64);
        $this->createColumn(self::ENGLISH_NAME, 'varchar', 64);
        $this->createColumn(self::ACTIVE, 'char', 1);
        $this->createColumn(self::AUTHOR, 'varchar', 255);
        $this->createColumn(self::VERSION, 'varchar', 40);
        $this->createColumn(self::IMPORTED, 'datetime');
        $this->createColumn(self::ACCOUNTID, 'char', 8);
        $this->createColumn(self::DATE_FORMAT, 'varchar', 64);
        $this->createColumn(self::TIME_FORMAT, 'varchar', 64);
        $this->createColumn(self::THOUSANDS_SEPARATOR, 'varchar', 1);
        $this->createColumn(self::DECIMAL_SEPARATOR, 'varchar', 1);
        $this->createColumn(self::TRANSLATED_PERCENTAGE, 'int');
        $this->createColumn(self::IS_DEFAULT, 'char', 1);
    }

    /**
     * Unset on all languages status default language
     */
    public function unsetDefaultLanguage($defaultLanguageId) {
        $sql = new Gpf_SqlBuilder_UpdateBuilder();
        $sql->from->add(self::getName());
        $sql->where->add(Gpf_Db_Table_Accounts::ID, '=', Gpf_Application::getInstance()->getAccountId());
        $sql->where->add(Gpf_Db_Table_Languages::ID, '<>', $defaultLanguageId);
        $sql->set->add(self::IS_DEFAULT, Gpf::NO);
        $sql->execute();
    }

    /**
     * Load default language for this account
     *
     * @return Gpf_Db_Language
     */
    public function getDefaultLanguage() {
        if ($this->defaultLanguage == null) {
            $this->defaultLanguage = new Gpf_Db_Language();
            $this->defaultLanguage->setIsDefault(true);
            $this->defaultLanguage->setAccountId(Gpf_Session::getInstance()->getAuthUser()->getAccountId());
            $this->defaultLanguage->loadFromData(array(Gpf_Db_Table_Accounts::ID, self::IS_DEFAULT));
        }
        return $this->defaultLanguage;
    }

    public function recomputeTranslationPercentage($languageId) {
        $lang = new Gpf_Db_Language();
        $lang->setId($languageId);
        $lang->load();

        $fileName = Gpf_Lang_CsvLanguage::getAccountCsvFileName($language->getCode());
        $csvLanguage = new Gpf_Lang_CsvLanguage();
        $csvLanguage->loadFromCsvFile(new Gpf_Io_Csv_Reader($fileName));

        $lang->setTranslatedPercentage($csvLanguage->getTranslationPercentage());
        $lang->update(array(self::TRANSLATED_PERCENTAGE));
    }
}
?>
