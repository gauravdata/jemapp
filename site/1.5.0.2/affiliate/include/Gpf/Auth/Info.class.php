<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Info.class.php 26951 2010-01-27 08:30:44Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 *   @package GwtPhpFramework
 */
abstract class Gpf_Auth_Info extends Gpf_Object {
    private $accountid;
    private $roleType;
    
    public function __construct($accountid, $roleType = '') {
        $this->accountid = $accountid;
        $this->roleType = $roleType;
    }
    
    /**
     * @return Gpf_Auth_Info
     */
    public static function create($username, $password, $accountid, $roleType = '', $authToken = '') {
        if ($username != '' && $password != '') {
            return new Gpf_Auth_InfoUsernamePassword($username, $password, $accountid, $roleType);
        }
        return new Gpf_Auth_InfoAuthToken($accountid, $authToken, $roleType);
    }
    
    public function addWhere(Gpf_SqlBuilder_SelectBuilder $builder) {
        if ($this->accountid != '') {
            $builder->where->add('u.accountid', '=', $this->accountid);
        }
        $builder->where->add('r.roletype', '=', $this->getRoleType());
    }
    
    public function getRoleType() {
        if ($this->roleType == '') {
            return Gpf_Session::getModule()->getRoleType();
        }
        return $this->roleType;
    }
    
    public function hasAccount() {
        return $this->accountid != '';
    }
    
    public function setAccount($accountid) {
        $this->accountid = $accountid;
    }
    
    public function getAccountId() {
        return $this->accountid;
    }
}
?>
