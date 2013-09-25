<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Common_AuthUser extends Gpf_Db_AuthUser {
    protected function beforeSaveCheck() {
        try {
            parent::beforeSaveCheck();
        } catch (Gpf_DbEngine_Row_PasswordConstraintException $e) {
            if(!$this->isMasterMerchant($this->get(Gpf_Db_Table_AuthUsers::ID))) {
                throw $e;
            }
        }
    }
    
    private function isMasterMerchant($authId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Gpf_Db_Table_Users::ROLEID);
        $select->from->add(Gpf_Db_Table_AuthUsers::getName(), 'au');
        $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'u', 'au.' . Gpf_Db_Table_AuthUsers::ID . '=u.' . Gpf_Db_Table_Users::AUTHID);
        $select->where->add('au.' . Gpf_Db_Table_AuthUsers::ID, '=', $authId);
        return $select->getOneRow()->get(Gpf_Db_Table_Users::ROLEID) == Pap_Application::DEFAULT_ROLE_MERCHANT;
    }
}

?>
