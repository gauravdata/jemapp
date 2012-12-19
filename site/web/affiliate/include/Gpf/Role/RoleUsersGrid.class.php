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
class Gpf_Role_RoleUsersGrid extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn(Gpf_Db_Table_AuthUsers::USERNAME, $this->_("Username"), true);
        $this->addViewColumn(Gpf_Db_Table_AuthUsers::FIRSTNAME, $this->_("First Name"), true);
        $this->addViewColumn(Gpf_Db_Table_AuthUsers::LASTNAME, $this->_("Last Name"), true);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Gpf_Db_Table_Users::ID);
        $this->addDataColumn(Gpf_Db_Table_Roles::ID, Gpf_Db_Table_Roles::ID);
        $this->addDataColumn(Gpf_Db_Table_AuthUsers::USERNAME, Gpf_Db_Table_AuthUsers::USERNAME);
        $this->addDataColumn(Gpf_Db_Table_AuthUsers::FIRSTNAME, Gpf_Db_Table_AuthUsers::FIRSTNAME);
        $this->addDataColumn(Gpf_Db_Table_AuthUsers::LASTNAME, Gpf_Db_Table_AuthUsers::LASTNAME);
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn(Gpf_Db_Table_AuthUsers::USERNAME, '100px', 'A');
        $this->addDefaultViewColumn(Gpf_Db_Table_AuthUsers::FIRSTNAME, '100px', 'N');
        $this->addDefaultViewColumn(Gpf_Db_Table_AuthUsers::LASTNAME, '100px', 'N');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Gpf_Db_Table_Users::getName(), 'u');
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au', 'au.authid=u.authid');
    }

    protected function buildWhere() {
        parent::buildWhere();        
        Gpf_Plugins_Engine::extensionPoint('Gpf_Role_RoleUsersGrid.buildWhere', 
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder, new Gpf_Data_Record(array('columnPrefix'), array('u'))));
    }
    
    /**
     * @service role read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        return parent::getRows($params);
    }
       
    /**
     * @service role export
     * @return Gpf_Rpc_Serializable
     */
    public function getCSVFile(Gpf_Rpc_Params $params) {
        return parent::getCSVFile($params);
    }
    }
?>
