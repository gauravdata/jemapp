<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Request.class.php 32716 2011-05-23 07:48:38Z jsimon $
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
class Gpf_Rpc_Request extends Gpf_Object implements Gpf_Rpc_Serializable {
    protected $className;
    protected $methodName;
    private $responseError;
    protected $response;
    protected $apiSessionObject = null;
    private $useNewStyleRequestsEncoding = false;

    /**
     * @var Gpf_Rpc_MultiRequest
     */
    private $multiRequest;

    /**
     * @var Gpf_Rpc_Params
     */
    protected $params;
    private $accountId = null;

    public function __construct($className, $methodName, Gpf_Api_Session $apiSessionObject = null) {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->params = new Gpf_Rpc_Params();
        $this->setRequiredParams($this->className, $this->methodName);
        if($apiSessionObject != null) {
            $this->apiSessionObject = $apiSessionObject;
        }
    }

    public function useNewStyleRequestsEncoding($useNewStyle) {
        $this->useNewStyleRequestsEncoding = $useNewStyle;
    }

    public function setAccountId($accountId) {
        $this->accountId = $accountId;
    }

    public function addParam($name, $value) {
        if(is_scalar($value) || is_null($value)) {
            $this->params->add($name, $value);
            return;
        }
        if($value instanceof Gpf_Rpc_Serializable) {
            $this->params->add($name, $value->toObject());
            return;
        }
        throw new Gpf_Exception("Cannot add request param: Value ($name=$value) is not scalar or Gpf_Rpc_Serializable");
    }

    /**
     *
     * @return Gpf_Rpc_MultiRequest
     */
    private function getMultiRequest() {
        if($this->multiRequest === null) {
            return Gpf_Rpc_MultiRequest::getInstance();
        }
        return $this->multiRequest;
    }

    public function setUrl($url) {
        $this->multiRequest = new Gpf_Rpc_MultiRequest();
        $this->multiRequest->setUrl($url);
    }

    public function send() {
        if($this->apiSessionObject != null) {
            $this->multiRequest = new Gpf_Rpc_MultiRequest();
            $this->multiRequest->setUrl($this->apiSessionObject->getUrl());
            $this->multiRequest->useNewStyleRequestsEncoding($this->useNewStyleRequestsEncoding);
            $this->multiRequest->setSessionId($this->apiSessionObject->getSessionId());
            $this->multiRequest->setDebugRequests($this->apiSessionObject->getDebug());
        }
         
        $multiRequest = $this->getMultiRequest();
        $multiRequest->add($this);
        $multiRequest->useNewStyleRequestsEncoding($this->useNewStyleRequestsEncoding);
    }

    public function sendNow() {
        $this->send();
        $this->getMultiRequest()->send();
    }

    public function setResponseError($message) {
        $this->responseError = $message;
    }

    public function getResponseError() {
        return $this->responseError;
    }

    public function setResponse($response) {
        $this->response = $response;
    }

    public function toObject() {
        return $this->params->toObject();
    }

    public function toText() {
        throw new Gpf_Exception("Unimplemented");
    }

    /**
     *
     * @return stdClass
     */
    final public function getStdResponse() {
        if(isset($this->responseError)) {
            throw new Gpf_Rpc_ExecutionException($this->responseError);
        }
        if($this->response === null) {
            throw new Gpf_Exception("Request not executed yet.");
        }
        return $this->response;
    }

    final public function getResponseObject() {
        return new Gpf_Rpc_Object($this->getStdResponse());
    }

    private function setRequiredParams($className, $methodName) {
        $this->addParam(Gpf_Rpc_Params::CLASS_NAME, $className);
        $this->addParam(Gpf_Rpc_Params::METHOD_NAME, $methodName);
    }

    /**
     * @param Gpf_Rpc_Params $params
     */
    public function setParams(Gpf_Rpc_Params $params) {
        $originalParams = $this->params;
        $this->params = $params;
        $this->setRequiredParams($originalParams->getClass(), $originalParams->getMethod());
    }
}

?>
