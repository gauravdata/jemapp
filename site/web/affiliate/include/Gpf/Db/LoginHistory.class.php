<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Currency.class.php 18659 2008-06-19 15:29:56Z aharsani $
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
class Gpf_Db_LoginHistory extends Gpf_DbEngine_Row {

    const WRITE_DELAY = 30;

    function __construct(){
        parent::__construct();
    }

    public function init() {
        $this->setTable(Gpf_Db_Table_LoginsHistory::getInstance());
        parent::init();
    }

    public function setId($id) {
        $this->set(Gpf_Db_Table_LoginsHistory::ID, $id);
    }

    public function setLastRequestTime($time) {
        $this->set(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, $time);
    }

    public function setLoginTime($time) {
        $this->set(Gpf_Db_Table_LoginsHistory::LOGIN, $time);
    }

    public function setLogoutTime($time) {
        $this->set(Gpf_Db_Table_LoginsHistory::LOGOUT, $time);
    }

    public function setIp($ip) {
        $this->set(Gpf_Db_Table_LoginsHistory::IP, $ip);
    }

    public function setAccountUserId($accountUserId) {
        $this->set(Gpf_Db_Table_Users::ID, $accountUserId);
    }

    public function getId() {
        return $this->get(Gpf_Db_Table_LoginsHistory::ID);
    }

    public static function logRequest() {
        try {
            if (!Gpf_Session::getInstance()->getAuthUser()->isLogged()) {
                //user is not logged in, don't monitor his session
                return;
            }
        } catch (Exception $e) {
            return;
        }

        $log = new Gpf_Db_LoginHistory();
        if ($loginId = Gpf_Session::getInstance()->getVar(Gpf_Db_Table_LoginsHistory::ID)) {
            if ((time() - Gpf_Session::getInstance()->getVar(Gpf_Db_Table_LoginsHistory::LAST_REQUEST)) > self::WRITE_DELAY) {
                //login id already defined, update last request time
                $log->setId($loginId);
                $log->setLastRequestTime($log->createDatabase()->getDateString());
                $log->update();
                Gpf_Session::getInstance()->setVar(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, time());
            }
        }
    }

    public static function logLogout() {
        try {
            if (!Gpf_Session::getInstance()->getAuthUser()->isLogged()) {
                //user is not logged in, don't monitor his session
                return;
            }
        } catch (Exception $e) {
            return;
        }

        if ($loginId = Gpf_Session::getInstance()->getVar(Gpf_Db_Table_LoginsHistory::ID)) {
            $log = new Gpf_Db_LoginHistory();
            $log->setId($loginId);
            $log->setLogoutTime($log->createDatabase()->getDateString());
            $log->update();
            Gpf_Session::getInstance()->setVar(Gpf_Db_Table_LoginsHistory::ID, false);
        }
    }
}

?>
