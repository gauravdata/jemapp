<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Database.class.php 33536 2011-06-29 12:18:13Z jsimon $
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
abstract class Gpf_DbEngine_Database extends Gpf_Object {
    const MYSQL = 'Mysql';

    protected $connected;

    protected $host;
    protected $username;
    protected $password;
    protected $dbname;
    protected $newLink = false;
    
    private static $database;

    /**
     * @return Gpf_DbEngine_Database
     */
    public static function getDatabase() {
        if(self::$database === null) {
            self::create(Gpf_Settings::get(Gpf_Settings_Gpf::DB_HOSTNAME),
                         Gpf_Settings::get(Gpf_Settings_Gpf::DB_USERNAME),
                         Gpf_Settings::get(Gpf_Settings_Gpf::DB_PASSWORD),
                         Gpf_Settings::get(Gpf_Settings_Gpf::DB_DATABASE));
        }
        return self::$database;
    }

    abstract public function connect();

    /**
     * @return Gpf_DbEngine_Database
     */
    public static function create($hostname, $username, $password, $dbname) {
        self::$database = self::createDriver(self::MYSQL);
        self::$database->init($hostname, $username, $password, $dbname);
        return self::$database;
    }

    /**
     * @param string $type
     * @return Gpf_DbEngine_Database
     */
    public static function createDriver($type) {
        $class = 'Gpf_DbEngine_Driver_' . $type . '_Database';
        return new $class;
    }

    public function isConnected() {
        return $this->connected;
    }

    public function getHostname() {
        return $this->host;
    }
    public function getUsername() {
        return $this->username;
    }
    public function getPassword() {
        return $this->password;
    }
    public function getDbname() {
        return $this->dbname;
    }

    public function init($host, $username, $password, $database, $newLink = false) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $database;
        $this->newLink = $newLink;
    }

    public function disconnect() {
        $this->connected = false;
    }

    public function createUniqueId($length = 8) {
        return substr(md5(uniqid(rand(), true)), 0, $length);
    }

    // TODO: move this method to Gpf_Common_Date class
    public static function getDateString($time = '') {
        if($time === '') {
            $time = time();
        }
        return strftime("%Y-%m-%d %H:%M:%S", $time);
    }

    public abstract function escapeString($str);

    /**
     *
     * @return Gpf_DbEngine_Driver_Mysql_Statement
     */
    public abstract function execute($sqlString);
}

?>
