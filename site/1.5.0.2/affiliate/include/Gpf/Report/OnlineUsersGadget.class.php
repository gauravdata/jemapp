<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Logs.class.php 19394 2008-07-25 09:11:14Z mfric $
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
class Gpf_Report_OnlineUsersGadget extends Gpf_Object {

    /**
     * Return count of online users per role
     *
     * @service online_user read
     * @param Gpf_Rpc_Params $params
     */
    public function getOnlineRolesCount(Gpf_Rpc_Params $params) {
         $sql = new Gpf_SqlBuilder_SelectBuilder();
         $sql->from->add(Gpf_Db_Table_Roles::getName(), 'r');
         $sql->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'u', 'u.roleid=r.roleid');
         $sql->from->addInnerJoin(Gpf_Db_Table_LoginsHistory::getName(), 'l', 'l.accountuserid=u.accountuserid');
         
         $sql->select->add(Gpf_Db_Table_Roles::NAME, Gpf_Db_Table_Roles::NAME, 'r');
         $sql->select->add('count(*)', 'usersCount');
                  
         $sql->where->add(Gpf_Db_Table_LoginsHistory::LOGOUT, 'is', 'NULL', 'AND', false);
         $sql->where->add(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, '>', 
        "('" . $this->createDatabase()->getDateString() . "' - INTERVAL 1800 SECOND)", 'AND', false);
         Gpf_Plugins_Engine::extensionPoint('Gpf_Report_OnlineUsersGadget.getOnlineRolesCount', $sql->where);
         
         $sql->groupBy->add('r.' . Gpf_Db_Table_Roles::ID);
         
         return $this->translateRoleNames($sql->getAllRows());
    }
    
    /**
     * @param Gpf_Data_RecordSet $roles
     * @return Gpf_Data_RecordSet
     */
    private function translateRoleNames(Gpf_Data_RecordSet $roles) {
    	foreach ($roles as $role) {
    		$role->set(Gpf_Db_Table_Roles::NAME, $this->_($role->get(Gpf_Db_Table_Roles::NAME)));
    	}
    	return $roles;
    }
}
?>
