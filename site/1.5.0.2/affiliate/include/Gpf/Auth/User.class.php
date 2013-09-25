<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: User.class.php 27074 2010-02-04 08:56:03Z mjancovic $
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
abstract class Gpf_Auth_User extends Gpf_Object implements Gpf_Templates_HasAttributes {
    protected $accountUserId;
    protected $authId;
    protected $firstname;
    protected $lastname;
    protected $theme;
    protected $language;
    protected $authtoken;
    protected $username;
    protected $accountid;
    protected $roletypeid;
    protected $roleid;
    protected $ip;
    protected $privileges = array();

    const THEME_ATTRIBUTE_NAME = 'theme';
    const LANGUAGE_ATTRIBUTE_NAME = 'language';

    const REQUEST_AUTH_TOKEN = 'authToken';

    public function __construct() {
    }

    public function getTheme() {
        return $this->theme;
    }

    public function setTheme($theme) {
        if (!Gpf_Session::getModule()->isThemeValid($theme)) {
            $theme = Gpf_Session::getModule()->getDefaultTheme();
        }
        $this->theme = $theme;
        Gpf_Db_Table_UserAttributes::getInstance()->setSetting(self::THEME_ATTRIBUTE_NAME, $theme, $this->getAccountUserId());
    }

    public function setLanguage($language) {
        $this->language = $language;
    }

    public function createAnonym() {
        return new Gpf_Auth_Anonym();
    }

    public function getLanguage() {
        return $this->language;
    }

    /**
     * Init language attribute
     * If not set, load default value
     * If set url parameter l, overwite existing setting
     *
     */
    private function initLanguage() {
        if (!strlen($this->language)) {
            $this->language = Gpf_Lang_Dictionary::getDefaultLanguage();
        } else if (isset($_REQUEST[Gpf_Lang_Dictionary::LANGUAGE_REQUEST_PARAMETER])
        && Gpf_Lang_Dictionary::isLanguageSupported($_REQUEST[Gpf_Lang_Dictionary::LANGUAGE_REQUEST_PARAMETER]) ) {
            //overwrite existing language setting, because it is defined in URL parameter, which is stronger as other settings
            $this->language = $_REQUEST[Gpf_Lang_Dictionary::LANGUAGE_REQUEST_PARAMETER];
        }
    }

    public function init() {
        $this->initLanguage();

        try {
            $this->setLanguage(Gpf_Lang_Dictionary::getInstance()->load($this->getLanguage()));
        } catch (Exception $e) {
        }
        $this->loadTheme();
        if (!Gpf_Session::getModule()->isThemeValid($this->theme)) {
            $this->setTheme(Gpf_Session::getModule()->getDefaultTheme());
        }
    }

    protected function loadTheme() {
        try {
            $attributes = Gpf_Db_Table_UserAttributes::getInstance();
            $attributes->loadAttributes($this->accountUserId);
            $this->theme = $attributes->getAttribute(self::THEME_ATTRIBUTE_NAME);
        } catch (Exception $e) {
        }
    }

    public function __wakeup() {
    }

    public function getAccountUserId() {
        return $this->accountUserId;
    }

    public function setAccountUserId($accountUserId) {
        $this->accountUserId = $accountUserId;
    }

    public function getAttributes() {
        $ret = array();
        $ret['isLogged'] = $this->isLogged();
        if ($this->isLogged()) {
            $ret['username'] = $this->getUsername();
            $ret['firstname'] = $this->getFirstName();
            $ret['lastname'] = $this->getLastName();
            $ret['ip'] = $this->getIp();
        }
        return $ret;
    }

    public function getAccountId() {
        return $this->accountid;
    }

    public function setAccountId($accountId) {
        $this->accountid = $accountId;
    }

    public function isLogged() {
        return true;
    }
    
    public function isExists() {
    	$select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('au.'.Gpf_Db_Table_AuthUsers::ID, Gpf_Db_Table_AuthUsers::ID);
        $select->from->add(Gpf_Db_Table_Users::getName(), 'u');
        $select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au',
            'u.'.Gpf_Db_Table_Users::AUTHID.'=au.'.Gpf_Db_Table_AuthUsers::ID);
        $select->from->addInnerJoin(Gpf_Db_Table_Roles::getName(), 'r',
            'u.'.Gpf_Db_Table_Users::ROLEID.'=r.'.Gpf_Db_Table_Roles::ID);
        $select->where->add('au.'.Gpf_Db_Table_AuthUsers::ID, '=', $this->authId);
        $select->where->add('u.'.Gpf_Db_Table_Roles::ID, '=', $this->roleid);
        $select->where->add('u.'.Gpf_Db_Table_Users::ACCOUNTID, '=', $this->accountid);
        try {
        	$select->getOneRow();
        	return true;
        } catch (Gpf_Exception $e) {
        }
        return false;
    }

    protected function getRememberMeCookieName() {
        return Gpf_Session::getRoleType() . "_auth";
    }

    public function getRemeberMeToken() {
        if (!array_key_exists($this->getRememberMeCookieName(), $_COOKIE)) {
            throw new Gpf_Exception('Auth token is empty');
        }
        $authToken = $_COOKIE[$this->getRememberMeCookieName()];
        if (strlen($authToken) != Gpf_Db_AuthUser::AUTH_TOKEN_LENGTH) {
            throw new Gpf_Exception('Wrong Auth token');
        }
        return $authToken;
    }

    public function saveRememberMeCookie() {
        Gpf_Http::setCookie($this->getRememberMeCookieName(), $this->authtoken, time()+60*60*24*356, '/');
    }

    public function clearRememberMeCookie() {
        Gpf_Http::setCookie($this->getRememberMeCookieName(), false, time() - 72000, '/');
    }

    /**
     * @param Gpf_Auth_Info $authInfo
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    protected function createAuthSelect(Gpf_Auth_Info $authInfo) {
        $select = new Gpf_SqlBuilder_SelectBuilder();

        $select->select->add('u.accountuserid', 'accountuserid');
        $select->select->add('r.roletype', 'roletypeid');
        $select->select->add('r.roleid', 'roleid');
        $select->select->add('au.authid', 'authid');
        $select->select->add('au.authtoken', 'authtoken');
        $select->select->add('au.username', 'username');
        $select->select->add('au.firstname', 'firstname');
        $select->select->add('au.lastname', 'lastname');
        $select->select->add('au.ip', 'ip');
        $select->select->add('u.accountid', 'accountid');
        $select->select->add('u.rstatus', 'rstatus');

        $select->from->add(Gpf_Db_Table_Users::getName(), 'u');
        $select->from->addInnerJoin('qu_g_authusers', 'au', 'u.authid=au.authid');
        $select->from->addInnerJoin('qu_g_roles', 'r', 'u.roleid=r.roleid');

        $authInfo->addWhere($select);

        return $select;
    }

    protected function loadAuthData(Gpf_Data_Record $data) {
        $this->username = $data->get("username");
        $this->accountUserId = $data->get("accountuserid");
        $this->authtoken = $data->get("authtoken");
        $this->accountid = $data->get("accountid");
        $this->roletypeid = $data->get("roletypeid");
        $this->roleid = $data->get('roleid');
        $this->authId = $data->get('authid');
        $this->firstname = $data->get('firstname');
        $this->lastname = $data->get('lastname');
        $this->ip = $data->get('ip');

        $attributes = Gpf_Db_Table_UserAttributes::getInstance();
        $attributes->loadAttributes($this->accountUserId);
        $this->setLanguage($attributes->getAttributeWithDefaultValue(self::LANGUAGE_ATTRIBUTE_NAME,
        Gpf_Lang_Dictionary::getDefaultLanguage()));
    }

    /**
     * @param string $object
     * @param string $privilege
     * @return boolean
     */
    public function hasPrivilege($object, $privilege) {
        if (array_key_exists($object, $this->privileges)) {
            $objectPrivileges = $this->privileges[$object];
            if (array_key_exists($privilege, $objectPrivileges) ||
            array_key_exists(Gpf_Privileges::P_ALL, $objectPrivileges)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @return Gpf_Data_RecordSet
     */
    public function getPrivileges() {
        $privileges = new Gpf_Data_RecordSet();
        $privileges->setHeader(array('name'));
        foreach ($this->privileges as $object => $objectPrivileges) {
            foreach ($objectPrivileges as $privilege => $value) {
                $privileges->add(array($object.'|'.$privilege));
            }
        }
        return $privileges;
    }

    /**
     * @param Gpf_Auth_Info $authInfo
     * @return Gpf_Auth_User
     */
    public function load(Gpf_Auth_Info $authInfo) {
        Gpf_Session::getAuthUser()->setAccountId($authInfo->getAccountId());
        $authData = $this->createAuthSelect($authInfo)->getOneRow();
        if ($authData->get('rstatus') != Gpf_Db_User::APPROVED) {
            throw new Gpf_Auth_Exception($this->_("User account not approved yet"));
        }
        $authUser = $this->createUser($authData);
        $authUser->loadAfterAuthentication($authData);
        return $authUser;
    }

    final public function loadAfterAuthentication(Gpf_Data_Record $authData) {
        $this->loadAuthData($authData);
        $this->privileges = Gpf_Privileges::loadPrivileges($this->roleid);
        $this->init();
    }

    /**
     *
     * @param Gpf_Auth_Info $authInfo
     * @return Gpf_Auth_User
     */
    protected function createUser(Gpf_Data_Record $authInfo) {
        return $this;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getFirstName() {
        return $this->firstname;
    }

    public function getLastName() {
        return $this->lastname;
    }

    public function getIp() {
        return $this->ip;
    }

    /**
     * Update last known IP address of logged in user
     *
     * @param $ip New ip address
     */
    public function updateIp($ip) {
        $this->ip = $ip;
        try {
            //update last known ip address of user in auth user row
            $dbAuthUser = new Gpf_Db_AuthUser();
            $dbAuthUser->setId($this->getAuthUserId());
            $dbAuthUser->updateIp($ip);
        } catch (Gpf_Exception $e) {
        }
    }

    public function getUserId() {
        return $this->accountUserId;
    }

    public function getAuthUserId() {
        return $this->authId;
    }

    public function getRoleTypeId() {
        return $this->roletypeid;
    }

    public function getAuthToken() {
        return $this->authtoken;
    }

    public function logout() {
        Gpf_Log::info($this->_sys('Logged out user %s', $this->getUsername()));
        Gpf_Db_LoginHistory::logLogout();

        $this->clearRememberMeCookie();
        Gpf_Session::getInstance()->destroy();
    }
}
?>
