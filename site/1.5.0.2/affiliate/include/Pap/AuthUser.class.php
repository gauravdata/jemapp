<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AuthUser.class.php 30197 2010-11-30 11:29:37Z mjancovic $
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
class Pap_AuthUser extends Gpf_Auth_User {

    /**
     * @var Gpf_Data_Record
     */
    private $userData;
    protected $userId;
    protected $type;

    public function getAttributes() {
        $ret = parent::getAttributes();
        if ($this->isLogged()) {
            $user = new Pap_Common_User();
            $user->setId($this->getPapUserId());
            $user->load();
            for ($i=1; $i<=25; $i++) {
                $ret['data'.$i] = $user->get('data'.$i);
            }
        }
        return $ret;
    }

    /**
     * @param Gpf_Auth_Info $authInfo
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createAuthSelect(Gpf_Auth_Info $authInfo) {
        $select = parent::createAuthSelect($authInfo);
        $select->select->add('pu.'.Pap_Db_Table_Users::REFID, 'refid');
        $select->select->add('pu.'.Pap_Db_Table_Users::NUMBERUSERID, 'numberuserid');
        $select->select->add('pu.'.Pap_Db_Table_Users::PHOTO, 'photo');
        for ($i=1; $i<=25; $i++) {
            $select->select->add('pu.'.Pap_Db_Table_Users::getDataColumnName($i), 'data'.$i);
        }
        $select->select->add('pu.'.Pap_Db_Table_Users::ID, 'userid');
        $select->select->add('pu.'.Pap_Db_Table_Users::TYPE, 'rtype');
        $select->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'pu',
            'pu.accountuserid=u.accountuserid');
        $select->where->add('pu.'.Pap_Db_Table_Users::TYPE, '=', $authInfo->getRoleType());
        return $select;
    }

    protected function loadAuthData(Gpf_Data_Record $data) {
        parent::loadAuthData($data);
        $this->userId = $data->get("userid");
        $this->type = $data->get("rtype");
        $this->userData = $data;        
    }

    public function getUserData() {
        return $this->userData;
    }

    public function isMerchant() {
        return $this->type == Pap_Application::ROLETYPE_MERCHANT;
    }

    public function isAffiliate() {
        return $this->type == Pap_Application::ROLETYPE_AFFILIATE;
    }

    public function getPapUserId() {
        return $this->userId;
    }

    public function createAnonym() {
        return new Pap_AnonymUser();
    }
    
    public function isMasterMerchant() {
        return $this->isDefaultAccount() && $this->isMerchant();
    }
    
    public function isDefaultAccount() {
        return $this->getAccountId() === Gpf_Db_Account::DEFAULT_ACCOUNT_ID;
    }
    
    public function isNetworkMerchant() {
        return !$this->isDefaultAccount() && $this->isMerchant();
    }
}
?>
