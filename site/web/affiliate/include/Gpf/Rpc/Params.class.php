<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Params.class.php 22623 2008-12-02 15:39:31Z mbebjak $
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
class Gpf_Rpc_Params extends Gpf_Object implements Gpf_Rpc_Serializable {
    private $params;
    const CLASS_NAME = 'C';
    const METHOD_NAME = 'M';
    const SESSION_ID = 'S';
    const ACCOUNT_ID = 'aid';

    function __construct($params = null) {
        if($params === null) {
            $this->params = new stdClass();
            return;
        }
        $this->params = $params;
    }

    public static function createGetRequest($className, $methodName = 'execute', $formRequest = false, $formResponse = false) {
        $requestData = array();
        $requestData[self::CLASS_NAME] = $className;
        $requestData[self::METHOD_NAME] = $methodName;
        $requestData[Gpf_Rpc_Server::FORM_REQUEST] = $formRequest ? Gpf::YES : '';
        $requestData[Gpf_Rpc_Server::FORM_RESPONSE] = $formResponse ? Gpf::YES : '';
        return $requestData;
    }

    /**
     *
     * @param unknown_type $className
     * @param unknown_type $methodName
     * @param unknown_type $formRequest
     * @param unknown_type $formResponse
     * @return Gpf_Rpc_Params
     */
    public static function create($className, $methodName = 'execute', $formRequest = false, $formResponse = false) {
        $params = new Gpf_Rpc_Params();
        $obj = new stdClass();
        foreach (self::createGetRequest($className, $methodName, $formRequest, $formResponse) as $name => $value) {
            $params->add($name,$value);
        }
        return $params;
    }

    public function setArrayParams(array $params) {
        foreach ($params as $name => $value) {
            $this->add($name, $value);
        }
    }

    public function exists($name) {
        if(!is_object($this->params) || !array_key_exists($name, $this->params)) {
            return false;
        }
        return true;
    }

    /**
     *
     * @param unknown_type $name
     * @return mixed Return null if $name does not exist.
     */
    public function get($name) {
        if(!$this->exists($name)) {
            return null;
        }
        return $this->params->{$name};
    }

    public function set($name, $value) {
        if(!$this->exists($name)) {
            return;
        }
        $this->params->{$name} = $value;
    }

    public function add($name, $value) {
        $this->params->{$name} = $value;
    }

    public function getClass() {
        return $this->get(self::CLASS_NAME);
    }

    public function getMethod() {
        return $this->get(self::METHOD_NAME);
    }

    public function getSessionId() {
        $sessionId = $this->get(self::SESSION_ID);
        if ($sessionId === null || strlen(trim($sessionId)) == 0) {
            Gpf_Session::create(new Gpf_ApiModule());
        }
        return $sessionId;
    }
    
    public function clearSessionId() {
        $this->set(self::SESSION_ID, null);
    }

    public function getAccountId() {
        return $this->get(self::ACCOUNT_ID);
    }

    public function toObject() {
        return $this->params;
    }

    public function toText() {
        throw new Gpf_Exception("Unimplemented");
    }
}

?>
