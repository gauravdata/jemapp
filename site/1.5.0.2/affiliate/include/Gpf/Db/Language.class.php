<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: File.class.php 19471 2008-07-30 08:58:19Z mbebjak $
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
class Gpf_Db_Language extends Gpf_DbEngine_Row {

    public function __construct(){
        parent::__construct();
    }

    protected function init() {
        $this->setTable(Gpf_Db_Table_Languages::getInstance());
        parent::init();
    }

    public function insert() {
        $this->set('imported', Gpf_DbEngine_Database::getDateString());
        $this->setId($this->generateId());
        $this->checkIsDefaultStatus();
        return parent::insert();
    }

    public function delete() {
        $this->load();
        if ($this->isDefault()) {
            throw new Gpf_Exception($this->_("Default language can't be deleted"));
        }

        $returnValue = parent::delete();

        $this->deleteLanguageFilesFromAccount();

        return $returnValue;
    }

    /**
     * Delete csv file from account directory
     */
    private function deleteLanguageFilesFromAccount() {
        //delete csv file from account
        $fileName = Gpf_Lang_CsvLanguage::getAccountCsvFileName($this->getCode());
        $file = new Gpf_Io_File($fileName);
        if ($file->isExists()) {
            $file->delete();
        }

        //TODO delete also cache language files from account
    }

    private function checkIsDefaultStatus() {
        if (!$this->isActive() && $this->isDefault()) {
            throw new Gpf_Exception($this->_('Default language has to be active !'));
        }

        try {
            $defLang = Gpf_Db_Table_Languages::getInstance()->getDefaultLanguage();
            if (($this->getCode() == $defLang->getCode() || !strlen($defLang->getCode())) && $this->isDefault() === false) {
                $this->setIsDefault(true);
            }
        } catch (Gpf_DbEngine_NoRowException $e) {
            $this->setIsDefault(true);
        }

        if ($this->isDefault()) {
            Gpf_Db_Table_Languages::getInstance()->unsetDefaultLanguage($this->getId());
        }
    }

    public function update($updateColumns = array()) {
        $this->checkIsDefaultStatus();
        parent::update($updateColumns);
    }

    public function generateId() {
        return $this->getAccountId() . '_' . $this->getCode();
    }

    public function getId() {
        return $this->get(Gpf_Db_Table_Languages::ID);
    }

    public function setId($id) {
        $this->set(Gpf_Db_Table_Languages::ID, $id);
    }

    public function getCode() {
        return $this->get(Gpf_Db_Table_Languages::CODE);
    }

    public function setCode($code) {
        $this->set(Gpf_Db_Table_Languages::CODE, $code);
    }

    public function getName() {
        return $this->get(Gpf_Db_Table_Languages::NAME);
    }

    public function setName($name) {
        $this->set(Gpf_Db_Table_Languages::NAME, $name);
    }

    public function getEnglishName() {
        return $this->get(Gpf_Db_Table_Languages::ENGLISH_NAME);
    }

    public function setEnglishName($name) {
        $this->set(Gpf_Db_Table_Languages::ENGLISH_NAME, $name);
    }

    public function isActive() {
        return $this->get(Gpf_Db_Table_Languages::ACTIVE) == Gpf::YES;
    }

    public function setActive($isActive) {
        if ($isActive == Gpf::YES || $isActive === true) {
            $this->set(Gpf_Db_Table_Languages::ACTIVE, Gpf::YES);
        } else {
            $this->set(Gpf_Db_Table_Languages::ACTIVE, Gpf::NO);
        }
    }

    public function getAuthor() {
        return $this->get(Gpf_Db_Table_Languages::AUTHOR);
    }

    public function setAuthor($author) {
        $this->set(Gpf_Db_Table_Languages::AUTHOR, $author);
    }

    public function getVersion() {
        return $this->get(Gpf_Db_Table_Languages::VERSION);
    }

    public function setVersion($version) {
        $this->set(Gpf_Db_Table_Languages::VERSION, $version);
    }

    public function getImported() {
        return $this->get(Gpf_Db_Table_Languages::IMPORTED);
    }

    public function setImported($imported) {
        $this->set(Gpf_Db_Table_Languages::IMPORTED, $imported);
    }

    public function getAccountId() {
        return $this->get(Gpf_Db_Table_Accounts::ID);
    }

    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_Accounts::ID, $accountId);
    }

    public function getDateFormat() {
        return $this->get(Gpf_Db_Table_Languages::DATE_FORMAT);
    }

    public function setDateFormat($format) {
        $this->set(Gpf_Db_Table_Languages::DATE_FORMAT, $format);
    }

    public function getTimeFormat() {
        return $this->get(Gpf_Db_Table_Languages::TIME_FORMAT);
    }

    public function setTimeFormat($format) {
        $this->set(Gpf_Db_Table_Languages::TIME_FORMAT, $format);
    }
    
	public function getThousandsSeparator() {
        return $this->get(Gpf_Db_Table_Languages::THOUSANDS_SEPARATOR);
    }
    
	public function setThousandsSeparator($separator) {
        $this->set(Gpf_Db_Table_Languages::THOUSANDS_SEPARATOR, $separator);
    }
    
	public function getDecimalSeparator() {
        return $this->get(Gpf_Db_Table_Languages::DECIMAL_SEPARATOR);
    }

	public function setDecimalSeparator($separator) {
        $this->set(Gpf_Db_Table_Languages::DECIMAL_SEPARATOR, $separator);
    }
    
    public function getTranslatedPercentage() {
        return $this->get(Gpf_Db_Table_Languages::TRANSLATED_PERCENTAGE);
    }

    public function setTranslatedPercentage($percent) {
        $this->set(Gpf_Db_Table_Languages::TRANSLATED_PERCENTAGE, $percent);
    }

    public function isDefault() {
        return $this->get(Gpf_Db_Table_Languages::IS_DEFAULT) == Gpf::YES;
    }

    public function setIsDefault($isDefault) {
        if ($isDefault == Gpf::YES || $isDefault === true) {
            $this->set(Gpf_Db_Table_Languages::IS_DEFAULT, Gpf::YES);
        } else {
            $this->set(Gpf_Db_Table_Languages::IS_DEFAULT, Gpf::NO);
        }
    }
}
