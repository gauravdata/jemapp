<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: WindowManager.class.php 25676 2009-10-13 09:17:04Z mjancovic $
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
class Gpf_Desktop_WindowManager extends Gpf_Object {
    
    const SCREEN_CODE = "screenCode";
    
    const SIDEBAR_WIDTH_SETTING_NAME = "sideBarWidth";
    const SIDEBAR_HIDDEN_SETTING_NAME = "sideBarHidden";
    const SIDEBAR_ONTOP_SETTING_NAME = "sideBarOnTop";
    
    /**
     * @service window write
     * @return Gpf_Rpc_Action
     */
    public function saveWindows(Gpf_Rpc_Params $params) {
        $windows = new Gpf_Data_RecordSet();
        $windows->loadFromArray($params->get('windows'));
        
        //Gpf_Db_Table_Windows::setAllWindowClosed(Gpf_Session::getAuthUser()->getAccountUserId());
        
        foreach ($windows as $windowRecord) {
            $window = new Gpf_Db_Window();
            $window->set('accountuserid', Gpf_Session::getAuthUser()->getAccountUserId());
            $window->fillFromRecord($windowRecord);    
            try {
            	$window->insert();
            } catch (Gpf_DbEngine_DuplicateEntryException $e) {
            	$window->update();
            }
        }
        $action = new Gpf_Rpc_Action(new Gpf_Rpc_Params());
        $action->setInfoMessage($this->_('Windows saved'));
        $action->addOk(); 
        return $action;
    }
    
    /**
     * @service window write
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Action
     */
    public function saveAutoRefresh(Gpf_Rpc_Params $params) {
        $window = new Gpf_Db_Window();
        $window->set('autorefreshtime', $params->get('autorefreshtime'));
        $window->set('content', $params->get('content'));
        $window->set('accountuserid', Gpf_Session::getAuthUser()->getAccountUserId());

        try {
            $window->insert();
        } catch (Gpf_DbEngine_DuplicateEntryException $e) {
            $window->update();
        }

        $action = new Gpf_Rpc_Action(new Gpf_Rpc_Params());
        $action->setInfoMessage($this->_('AutoRefresh saved'));
        $action->addOk(); 
        return $action;
    }

    /**
     * @service window read
     */
    public function getWindows(Gpf_Rpc_Params $params) {
        return $this->getWindowsNoRpc();
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    public function getWindowsNoRpc() {
        $windowsTable = Gpf_Db_Table_Windows::getInstance();
        return $windowsTable->getWindows(Gpf_Session::getAuthUser()->getAccountUserId());
    }
    
    /**
     * @service quicklaunch write
     * @return Gpf_Rpc_Action
     */
    public function saveQuickLaunch(Gpf_Rpc_Params $params) {
        $items = new Gpf_Data_RecordSet();
        $items->loadFromArray($params->get('items'));
        
        $quickLaunchSetting = "";
        foreach ($items as $item) {
            $quickLaunchSetting .= $item->get(self::SCREEN_CODE) . ",";
        }
        $quickLaunchSetting = rtrim($quickLaunchSetting, ",");
        
        Gpf_Db_Table_UserAttributes::setSetting(Gpf_Settings_Gpf::QUICK_LAUNCH_SETTING_NAME, $quickLaunchSetting);
        
        $action = new Gpf_Rpc_Action(new Gpf_Rpc_Params());
        $action->setInfoMessage($this->_('Quick Launch saved'));
        $action->addOk(); 
        return $action;
    }
    
    /**
     * @service quicklaunch read
     */
    public function getQuickLaunch(Gpf_Rpc_Params $params) {
        return $this->getQuickLaunchNoRpc();
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    public function getQuickLaunchNoRpc() {
        try {
            $quickLaunchSetting = Gpf_Db_Table_UserAttributes::getSetting(Gpf_Settings_Gpf::QUICK_LAUNCH_SETTING_NAME);
        } catch (Gpf_DbEngine_NoRowException $e) {
            $quickLaunchSetting = Gpf_Settings::get(Gpf_Settings_Gpf::QUICK_LAUNCH_SETTING_NAME); 
        }
        $items = explode(",", $quickLaunchSetting);
        $result = new Gpf_Data_RecordSet();
        $result->addColumn(self::SCREEN_CODE);
        if (is_array($items)) {
            foreach ($items as $item) {
                $result->add(array(trim($item)));
            }
        }
        return $result; 
    }
    
   /**
     * @service sidebar write
     * 
     * @return Gpf_Rpc_Action
     */
    public function saveSideBar(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage($this->_("Side bar saved"));
        
        Gpf_Db_Table_UserAttributes::setSetting(
            self::SIDEBAR_WIDTH_SETTING_NAME,
            $action->getParam("width"));
            
        Gpf_Db_Table_UserAttributes::setSetting(
            self::SIDEBAR_HIDDEN_SETTING_NAME,
            $action->getParam("hidden"));
            
        Gpf_Db_Table_UserAttributes::setSetting(
            self::SIDEBAR_ONTOP_SETTING_NAME,
            $action->getParam("onTop"));    
            
        $action->addOk();
        return $action; 
    }
    
    /**
     * @service sidebar read
     * 
     * @return Gpf_Rpc_Form
     */
    public function loadSideBar(Gpf_Rpc_Params $params) {
        return $this->loadSideBarNoRpc();
    }   
       
    /**
     * @return Gpf_Rpc_Form
     */
    public function loadSideBarNoRpc() {
        $response = new Gpf_Data_RecordSet();
        $response->addColumn("name");
        $response->addColumn("value");
        
        $record = $response->createRecord();
        $record->set("name", "width");
        $sideBarWidthValue = $this->getUserAttributeWithDefaultValue(
            self::SIDEBAR_WIDTH_SETTING_NAME, "200");
        if ($sideBarWidthValue < 0) {
            $sideBarWidthValue = 200;
        }
        $record->set("value", $sideBarWidthValue);
        $response->add($record);
        
        $record = $response->createRecord();
        $record->set("name", "hidden");
        $record->set("value", $this->getUserAttributeWithDefaultValue(
            self::SIDEBAR_HIDDEN_SETTING_NAME, "N"));
            
        $response->add($record);
        $record = $response->createRecord();
        $record->set("name", "onTop");
        $record->set("value", $this->getUserAttributeWithDefaultValue(
            self::SIDEBAR_ONTOP_SETTING_NAME, "N"));
        $response->add($record);
        
        return $response; 
    }
    
    public function getUserAttributeWithDefaultValue($attributeName, $defaultValue) {
        try {
            $value = Gpf_Db_Table_UserAttributes::getSetting($attributeName);
        } catch (Gpf_DbEngine_NoRowException $e) {
            $value = $defaultValue;
            Gpf_Db_Table_UserAttributes::setSetting($attributeName, $defaultValue);
        }
        return $value;
    }
}
