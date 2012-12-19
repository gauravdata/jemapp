<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Setting.class.php 22866 2008-12-16 15:37:26Z mbebjak $
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
class Gpf_Db_Setting extends Gpf_DbEngine_Row {

    function init() {
        $this->setTable(Gpf_Db_Table_Settings::getInstance());
        parent::init();
    }

    public function getSetting($name, $accountId = null) {
    	try {
    		$this->setName($name);
    		if ($accountId != null) {
    		  $this->setAccountId($accountId);
    		} else {
    		    $this->setNull(Gpf_Db_Table_Settings::ACCOUNTID);
    		}
        	$this->loadFromData();
        	return $this->getValue();
    	} catch(Gpf_Exception $e) {
    		throw new Gpf_Settings_UnknownSettingException($name);
    	}
    }

    public function save() {
        $setting = new Gpf_Db_Setting();
        try {
            $setting->getSetting($this->getName(), $this->getAccountId());
            $this->setPrimaryKeyValue($setting->getPrimaryKeyValue());
            $this->update();
        } catch (Gpf_DbEngine_NoRowException $e) {
            $this->insert(); 
        } catch (Gpf_Settings_UnknownSettingException $e) {
            $this->insert(); 
        }
    }
    
    public function setName($name) {
        $this->set(Gpf_Db_Table_Settings::NAME, $name);
    }
    
    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_Settings::ACCOUNTID, $accountId);
    }
    
    public function getName() {
        return $this->get(Gpf_Db_Table_Settings::NAME);
    }
    
    public function getAccountId() {
        return $this->get(Gpf_Db_Table_Settings::ACCOUNTID);
    }
    
    public function getValue() {
        return $this->get(Gpf_Db_Table_Settings::VALUE);
    }
    
    public function setValue($value) {
        $this->set(Gpf_Db_Table_Settings::VALUE, $value);
    }
}

?>
