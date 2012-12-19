<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Server.class.php 30310 2010-12-07 14:06:06Z vzeman $
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
class Gpf_Rpc_Server extends Gpf_Object {
    const REQUESTS = 'requests';
    const REQUESTS_SHORT = 'R';
    const RUN_METHOD = 'run';
    const FORM_REQUEST = 'FormRequest';
    const FORM_RESPONSE = 'FormResponse';
    const BODY_DATA_NAME = 'D';


    const HANDLER_FORM = 'Y';
    const HANDLER_JASON = 'N';
    const HANDLER_WINDOW_NAME = 'W';

    /**
     * @var Gpf_Rpc_DataEncoder
     */
    private $dataEncoder;
    /**
     * @var Gpf_Rpc_DataDecoder
     */
    private $dataDecoder;

    public function __construct() {
    }

    private function initDatabaseLogger() {
        $logger = Gpf_Log_Logger::getInstance();

        if(!$logger->checkLoggerTypeExists(Gpf_Log_LoggerDatabase::TYPE)) {
            $logger->setGroup(Gpf_Common_String::generateId(10));
            $logLevel = Gpf_Settings::get(Gpf_Settings_Gpf::LOG_LEVEL_SETTING_NAME);
            $logger->add(Gpf_Log_LoggerDatabase::TYPE, $logLevel);
        }
    }

    /**
     * Return response to standard output
     */
    public function executeAndEcho($request = '') {
        $response = $this->encodeResponse($this->execute($request));
        Gpf_ModuleBase::startGzip();
        echo $response;
        Gpf_ModuleBase::flushGzip();
    }

    /**
     * @return Gpf_Rpc_Serializable
     */
    public function execute($request = '') {
        try {
            if(isset($_REQUEST[self::BODY_DATA_NAME])) {
                $request = $this->parseRequestDataFromPost($_REQUEST[self::BODY_DATA_NAME]);
            }
            if($this->isStandardRequestUsed($_REQUEST)) {
                $request = $this->setStandardRequest();
            }

            $this->setDecoder($request);
            $params = new Gpf_Rpc_Params($this->decodeRequest($request));
            $this->setEncoder($params);
            $response = $this->executeRequest($params);
        } catch (Exception $e) {
            return new Gpf_Rpc_ExceptionResponse($e);
        }
        return $response;
    }

    private function parseRequestDataFromPost($data) {
        if(get_magic_quotes_gpc()) {
            return stripslashes($data);
        }
        return $data;
    }

    /**
     *
     * @param unknown_type $requestObj
     * @return Gpf_Rpc_Serializable
     */
    private function executeRequest(Gpf_Rpc_Params $params) {
        try {
            Gpf_Db_LoginHistory::logRequest();
            return $this->callServiceMethod($params);
        } catch (Gpf_Rpc_SessionExpiredException $e) {
            return $e;
        } catch (Exception $e) {
            return new Gpf_Rpc_ExceptionResponse($e);
        }
    }

    protected function callServiceMethod(Gpf_Rpc_Params $params) {
        $method = new Gpf_Rpc_ServiceMethod($params);
        return $method->invoke($params);
    }

    /**
     * Compute correct handler type for server response
     *
     * @param array $requestData
     * @param string $type
     * @return string
     */
    private function getEncoderHandlerType($requestData) {
        if ($this->isFormHandler($requestData, self::FORM_RESPONSE, self::HANDLER_FORM)) {
            return self::HANDLER_FORM;
        }
        if ($this->isFormHandler($requestData, self::FORM_RESPONSE, self::HANDLER_WINDOW_NAME)) {
            return self::HANDLER_WINDOW_NAME;
        }
        return self::HANDLER_JASON;
    }


    private function isFormHandler($requestData, $type, $handler) {
        return (isset($_REQUEST[$type]) && $_REQUEST[$type] == $handler) ||
        (isset($requestData) && isset($requestData[$type]) && $requestData[$type] == $handler);
    }

    private function decodeRequest($requestData) {
        return $this->dataDecoder->decode($requestData);
    }

    private function isStandardRequestUsed($requestArray) {
        return is_array($requestArray) && array_key_exists(Gpf_Rpc_Params::CLASS_NAME, $requestArray);
    }

    private function setStandardRequest() {
        return array_merge($_POST, $_GET);
    }

    private function isFormRequest($request) {
        return $this->isFormHandler($request, self::FORM_REQUEST, self::HANDLER_FORM);
    }

    private function encodeResponse(Gpf_Rpc_Serializable $response) {
        return $this->dataEncoder->encodeResponse($response);
    }


    private function setDecoder($request) {
        if ($this->isFormRequest($request)) {
            $this->dataDecoder = new Gpf_Rpc_FormHandler();
        } else {
            $this->dataDecoder = new Gpf_Rpc_Json();
        }
    }

    private function setEncoder(Gpf_Rpc_Params $params) {
        switch ($params->get(self::FORM_RESPONSE)) {
            case self::HANDLER_FORM:
                $this->dataEncoder = new Gpf_Rpc_FormHandler();
                break;
            case self::HANDLER_WINDOW_NAME:
                $this->dataEncoder = new Gpf_Rpc_WindowNameHandler();
                break;
            default:
                $this->dataEncoder = new Gpf_Rpc_Json();
                break;
        }
    }

    /**
     * Executes multi request
     *
     * @service
     * @anonym
     * @return Gpf_Rpc_Serializable
     */
    public function run(Gpf_Rpc_Params $params) {
        $requestArray = $params->get(self::REQUESTS);

        if ($requestArray === null) {
            $requestArray = $params->get(self::REQUESTS_SHORT);
        }

        $response = new Gpf_Rpc_Array();
        foreach ($requestArray as $request) {
            $response->add($this->executeRequest(new Gpf_Rpc_Params($request)));
        }
        return $response;
    }

    /**
     * Set time offset between client and server and store it to session
     * Offset is computed as client time - server time
     *
     * @anonym
     * @service
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Action
     */
    public function syncTime(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        Gpf_Session::getInstance()->setTimeOffset($action->getParam('offset')/1000);
        $action->addOk();
        return $action;
    }
}
?>
