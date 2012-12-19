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
class Gpf_Lang_InstallLanguages extends Gpf_Lang_Languages {

    /**
     * @return Gpf_Lang_Language
     */
    private function loadLanguage($languageCode) {
        $language = new Gpf_Lang_Language($languageCode);
        $language->load();
        return $language;
    }
    
    /**
     * Compute active languages recordset from install directory language files
     *
     * @return Gpf_Data_IndexedRecordSet
     */
    public function getActiveLanguagesNoRpc() {
        $result = new Gpf_Data_IndexedRecordSet('code');
        $result->setHeader(array(Gpf_Db_Table_Languages::CODE, 
        Gpf_Db_Table_Languages::NAME,
        Gpf_Db_Table_Languages::ENGLISH_NAME,
        Gpf_Db_Table_Languages::IS_DEFAULT));
        $langCacheDirectory = Gpf_Paths::getInstance()->getInstallDirectoryPath() . 
            Gpf_Paths::CACHE_DIRECTORY . Gpf_Lang_Dictionary::LANGUAGE_DIRECTORY;    
        
        foreach (new Gpf_Io_DirectoryIterator($langCacheDirectory) as $file) {
            if(preg_match('!^' . Gpf_Application::getInstance()->getCode() . '_(.+)\.s\.php$!', $file, $matches)) {
                $language = $this->loadLanguage($matches[1]);
                $result->add(array($matches[1], $language->getName(), $language->getEnglishName(), Gpf::NO));
            }
        }
        return $result;
    }
}
?>
