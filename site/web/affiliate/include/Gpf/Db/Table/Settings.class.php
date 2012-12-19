<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Settings.class.php 22866 2008-12-16 15:37:26Z mbebjak $
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
class Gpf_Db_Table_Settings extends Gpf_DbEngine_Table {
    const ID = "settingid";
    const NAME = "name";
    const VALUE = "value";
    const ACCOUNTID = "accountid";
    
    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_settings');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    public static function getSetting($name, $accountId = null) {
        $setting = new Gpf_Db_Setting();
        return $setting->getSetting($name, $accountId);
    }
    
    public static function setSetting($name, $value, $accountId = null) {
        $setting = new Gpf_Db_Setting();
        $setting->set(self::NAME, $name);
        $setting->set(self::VALUE, $value);
        if ($accountId != null) {
            $setting->set(self::ACCOUNTID, $accountId);
        }
        $setting->save();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::NAME, 'char', 50);
        $this->createColumn(self::VALUE, 'text');
        $this->createColumn(self::ACCOUNTID, 'char', 8);
    }
    
  	/**
  	 * returns recordset with setting values for given setting names (array) and given account
  	 *
  	 * @param  settingNamesArray array
  	 * @param  accountId
  	 * @return Gpf_Data_RecordSet
  	 */
    public function getSettings($settingNames, $accountId) {
    	if(!is_array($settingNames) || count($settingNames)<=0) {
    		throw new Gpf_Exception("getSettings(): parameter settingNames is empty or not an array!");
    	}
    	
    	$result = new Gpf_Data_IndexedRecordSet('name');

    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
    	$selectBuilder->select->add('name', 'name');
    	$selectBuilder->select->add('value', 'value');
    	$selectBuilder->from->add(self::getName());
    	$selectBuilder->where->add("accountid", "=", $accountId);

    	$names = "";
    	foreach($settingNames as $name) {
    		$names .= ($names != "" ? "," : '')."'".$name."'";
    	}
   	
    	$names = "(".$names.")";

    	$selectBuilder->where->add("name", 'in', $settingNames);

    	$result->load($selectBuilder);
    	return $result;
    }
}

?>
