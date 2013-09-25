<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ActiveView.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_InstalledTemplate extends Gpf_DbEngine_Row {
     
    protected function init() {
        $this->setTable(Gpf_Db_Table_InstalledTemplates::getInstance());
        parent::init();
    }
    
    public function setName($name) {
        $this->set(Gpf_Db_Table_InstalledTemplates::NAME, $name);
        $this->set(Gpf_Db_Table_InstalledTemplates::ID, md5($name));
    }
    
    public function setContentHash($hash) {
        $this->set(Gpf_Db_Table_InstalledTemplates::HASH, $hash);
    }

    public function setVersion() {
        $this->set(Gpf_Db_Table_InstalledTemplates::VERSION, Gpf_Application::getInstance()->getVersion());
    }
    
    public function setOverwriteExisting($isOverwriteExisting) {
        $this->set(Gpf_Db_Table_InstalledTemplates::OVERWRITE_EXISTING, $isOverwriteExisting ? Gpf::YES : Gpf::NO);
    }
    
    public function getContentHash() {
        return $this->get(Gpf_Db_Table_InstalledTemplates::HASH);
    }
    
    public function update($updateColumns = array()) {
        $this->setVersion();
        $this->set(Gpf_Db_Table_InstalledTemplates::CHANGED, Gpf_Common_DateUtils::now());
        parent::update($updateColumns);
    }
    
    public function insert() {
        $this->setVersion();
        $this->set(Gpf_Db_Table_InstalledTemplates::CHANGED, Gpf_Common_DateUtils::now());
        parent::insert();
    }
}

?>
