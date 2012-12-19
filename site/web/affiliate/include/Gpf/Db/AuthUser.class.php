<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: AuthUser.class.php 30822 2011-01-25 13:57:46Z mkendera $
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
class Gpf_Db_AuthUser extends Gpf_DbEngine_Row {
    const AUTH_TOKEN_LENGTH = 32;

    function __construct() {
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_AuthUsers::getInstance());
        parent::init();
    }

    public function loadFromUsername() {
        $this->loadFromData(array('username'));
    }

    public function insert() {
        $password = $this->get(Gpf_Db_Table_AuthUsers::PASSWORD);
        if(strlen(trim($password)) == 0) {
            $this->set(Gpf_Db_Table_AuthUsers::PASSWORD, Gpf_Common_String::generatePassword(8));
        }
        $this->generateAuthToken();
        $this->setIp(Gpf_Http::getRemoteIp());

        parent::insert();
    }

    public function update($updateColumns = array()) {
        Gpf_Plugins_Engine::extensionPoint('Gpf_AuthUser.onUpdate', $this);
        $this->generateAuthToken();
        parent::update($updateColumns);
    }

    private function generateAuthToken() {
        $authToken = substr(md5($this->getUsername() . $this->getPassword() . time()).uniqid('', true), 0, self::AUTH_TOKEN_LENGTH);
        $this->set('authtoken', $authToken);
    }

    public function getId() {
        return $this->get(Gpf_Db_Table_AuthUsers::ID);
    }

    public function setId($id) {
        $this->set(Gpf_Db_Table_AuthUsers::ID, $id);
    }

    public function setUsername($username) {
        $this->set(Gpf_Db_Table_AuthUsers::USERNAME, $username);
    }

    public function setNotificationEmail($email) {
        $this->set(Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL, $email);
    }

    public function getUsername() {
        return $this->get(Gpf_Db_Table_AuthUsers::USERNAME);
    }

    public function getPassword() {
        return $this->get(Gpf_Db_Table_AuthUsers::PASSWORD);
    }

    public function setPassword($password) {
        $this->set(Gpf_Db_Table_AuthUsers::PASSWORD, $password);
    }

    public function setFirstName($firstName) {
        $this->set(Gpf_Db_Table_AuthUsers::FIRSTNAME, $firstName);
    }

    public function getFirstName() {
        return $this->get(Gpf_Db_Table_AuthUsers::FIRSTNAME);
    }

    public function setLastName($lastName) {
        $this->set(Gpf_Db_Table_AuthUsers::LASTNAME, $lastName);
    }

    public function getLastName() {
        return $this->get(Gpf_Db_Table_AuthUsers::LASTNAME);
    }

    public function setIp($ip) {
        $this->set(Gpf_Db_Table_AuthUsers::IP, $ip);
    }

    public function getIp() {
        return $this->get(Gpf_Db_Table_AuthUsers::IP);
    }

    public function updateIp($ip) {
        $this->setIp($ip);
        return parent::update(array(Gpf_Db_Table_AuthUsers::IP));
    }

    public function getEmail() {
        if ($this->getNotificationEmail() !== null && $this->getNotificationEmail() !== '') {
            return $this->getNotificationEmail();
        }
        return $this->getUsername();
    }

    public function setEmail($email) {
        if ($this->getNotificationEmail() !== null && $this->getNotificationEmail() !== '') {
            $this->setNotificationEmail($email);
        }
        $this->setUsername($email);
    }

    /**
     * @return boolean
     */
    public function isUserNameUnique() {
    	try {
    		$authUser = $this->createUserWithUserName();
    		$authUser->loadFromUsername();
    	} catch (Gpf_DbEngine_NoRowException $e) {
    		return true;
    	} catch (Gpf_DbEngine_TooManyRowsException $e) {
    	}
    	return false;
    }

    /**
     * @return Gpf_Db_AuthUser
     */
    protected function createUserWithUserName() {
    	$authUser = new Gpf_Db_AuthUser();
    	$authUser->setUsername($this->getUsername());
    	return $authUser;
    }

    public function getNotificationEmail() {
        return $this->get(Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL);
    }
}
?>
