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
class Gpf_Lang_Language extends Gpf_Object {
    private $code;
    private $name;
    private $englishName;
    private $author;
    private $version;
    private $dateFormat;
    private $timeFormat;
    private $thousandsSeparator;
    private $decimalSeparator;
    private $dictionary = array();
    
    public function __construct($code) {
        $this->code = $code;
    }
    
    public function getCode() {
        return $this->code;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
   
    public function setEnglishName($name) {
        $this->englishName = $name;
    }
    
    public function getEnglishName() {
        return $this->englishName;
    }
    
    public function setVersion($version) {
        $this->version = $version;
    }
    
    public function setAuthor($author) {
        $this->author = $author;
    }
    
    public function setDictionary(array $dictionary) {
        $this->dictionary = $dictionary;
    }
    
    public function setDateFormat($dateFormat) {
        $this->dateFormat = $dateFormat;
    }
    
    public function getDateFormat() {
        return $this->dateFormat;
    }
    
    public function setTimeFormat($timeFormat) {
      $this->timeFormat = $timeFormat;
    }
    
    public function getTimeFormat() {
        return $this->timeFormat;
    }
    
    public function setThousandsSeparator($thousandsSeparator) {
      $this->thousandsSeparator = $thousandsSeparator;
    }
    
    public function getThousandsSeparator() {
        return $this->thousandsSeparator;
    }
    
    public function setDecimalSeparator($decimalSeparator) {
      $this->decimalSeparator = $decimalSeparator;
    }
    
    public function getDecimalSeparator() {
        return $this->decimalSeparator;
    }
    
    public function getClientMessages() {
        $langDirectory = Gpf_Paths::getInstance()->getAccountDirectoryPath() .
            Gpf_Paths::CACHE_DIRECTORY . Gpf_Lang_Dictionary::LANGUAGE_DIRECTORY;
        
        $file = new Gpf_Lang_CachedLanguageFile($langDirectory, $this->code);
        return $file->loadClientMessages();
    }
    
    public function load() {
        $langDirectory = Gpf_Paths::getInstance()->getAccountDirectoryPath() .
            Gpf_Paths::CACHE_DIRECTORY . Gpf_Lang_Dictionary::LANGUAGE_DIRECTORY;
        
        $file = new Gpf_Lang_CachedLanguageFile($langDirectory, $this->code);
        $file->load($this);
    }
    
    public function localize($mesage) {
        if(!isset($this->dictionary[$mesage])) {
            return $mesage;
        }
        return $this->dictionary[$mesage];
    }
}
?>
