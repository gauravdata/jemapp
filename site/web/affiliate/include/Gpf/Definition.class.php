<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Fric
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

class Gpf_Definition extends Gpf_Plugins_Definition  {
    public function __construct() {
        $this->codeName = 'SysCore';
        $this->name = 'System Core Plugin';
        $this->description = 'Core functionality.';
        $this->version = Gpf_Application::getInstance()->getVersion();
        $this->pluginType = self::PLUGIN_TYPE_SYSTEM;

        $this->initDefines();
    }

    protected function initDefines() {
        $this->addDefine('Core.initJsResources', 'Gpf_Contexts_Module');
        $this->addDefine('Core.assignModuleAttributes', 'Gpf_Templates_Template');
        $this->addDefine('Core.defineSettings', 'Gpf_Settings_Gpf');
        $this->addDefine('Core.loadSetting', 'Gpf_ApplicationSettings');
        $this->addDefine('Core.initPrivileges', 'Gpf_Privileges');
        $this->addDefine('Core.initSmarty', 'Gpf_Templates_Smarty');        
        $this->addDefine('AuthUsers.initConstraints', 'Gpf_Db_Table_AuthUsers');
        $this->addDefine('Accounts.initConstraints', 'Gpf_Db_Table_Accounts');
        $this->addDefine('Gpf_Report_OnlineUsers.buildWhere', 'Gpf_SqlBuilder_WhereClause');
        $this->addDefine('Gpf_Report_OnlineUsersGadget.getOnlineRolesCount', 'Gpf_SqlBuilder_WhereClause');
        $this->addDefine('Gpf_Report_LoginsHistory.buildWhere', 'Gpf_SqlBuilder_WhereClause');
        $this->addDefine('Gpf_Common_UserRichListBox.createSelectBuilder', 'Gpf_SqlBuilder_WhereClause');
        $this->addDefine('Gpf_Role_RoleUsersGrid.buildWhere', 'Gpf_Common_SelectBuilderCompoundRecord');
        $this->addDefine('Gpf_Auth_UserForm.save', 'Gpf_DbEngine_Row');
        $this->addDefine('Pap_Db_Table_Campaigns.getCampaignsSelect', 'Gpf_SqlBuilder_SelectBuilder');
        $this->addDefine('Gpf_Db_Mail.scheduleNow', 'Gpf_Plugins_ValueContext');
        $this->addDefine('GpfModuleBase.initCachedData', 'Gpf_ModuleBase');
        $this->addDefine('Gpf_AuthUser.onUpdate', 'Gpf_Db_AuthUser');
    }
}
?>
