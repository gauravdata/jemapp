<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MultiRequest.class.php 32716 2011-05-23 07:48:38Z jsimon $
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
class Gpf_Rpc_MultiRequest extends Gpf_Object {
    private $url = '';
    private $useNewStyleRequestsEncoding;
    /**
     *
     * @var Gpf_Rpc_Array
     */
    private $requests;
    /**
     * @var Gpf_Rpc_Json
     */
    private $json;
    protected $serverClassName = 'Gpf_Rpc_Server';

    private $sessionId = null;

    private $debugRequests = false;

    /**
     * @var Gpf_Rpc_MultiRequest
     */
    private static $instance;

    public function __construct() {
        $this->json = new Gpf_Rpc_Json();
        $this->requests = new Gpf_Rpc_Array();
    }

    public function useNewStyleRequestsEncoding($useNewStyle) {
        $this->useNewStyleRequestsEncoding = $useNewStyle;
    }

    /**
     * @return Gpf_Rpc_MultiRequest
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new Gpf_Rpc_MultiRequest();
        }
        return self::$instance;
    }

    public static function setInstance(Gpf_Rpc_MultiRequest $instance) {
        self::$instance = $instance;
    }

    public function add(Gpf_Rpc_Request $request) {
        $this->requests->add($request);
    }

    protected function sendRequest($requestBody) {
        $request = new Gpf_Net_Http_Request();

        $request->setMethod('POST');
        $request->setBody(Gpf_Rpc_Server::BODY_DATA_NAME . '=' . urlencode($requestBody));
        $request->setUrl($this->url);

        $client = new Gpf_Net_Http_Client();
        $response = $client->execute($request);
        return $response->getBody();
    }

    public function setSessionId($sessionId) {
        $this->sessionId = $sessionId;
    }

    public function setDebugRequests($debug) {
        $this->debugRequests = $debug;
    }

    public function send() {
        $request = new Gpf_Rpc_Request($this->serverClassName, Gpf_Rpc_Server::RUN_METHOD);
        if ($this->useNewStyleRequestsEncoding) {
            $request->addParam(Gpf_Rpc_Server::REQUESTS_SHORT, $this->requests);
        } else {
            $request->addParam(Gpf_Rpc_Server::REQUESTS, $this->requests);
        }
        if($this->sessionId != null) {
            $request->addParam("S", $this->sessionId);
        }
        $requestBody = $this->json->encodeResponse($request);
        $responseText = $this->sendRequest($requestBody);
        if($this->debugRequests) {
            echo "REQUEST: ".$requestBody."<br/>";
            echo "RESPONSE: ".$responseText."<br/><br/>";
        }
        $responseArray = $this->json->decode($responseText);

        if (!is_array($responseArray)) {
            throw new Gpf_Exception("Response decoding failed: not array. Received text: $responseText");
        }

        if (count($responseArray) != $this->requests->getCount()) {
            throw new Gpf_Exception("Response decoding failed: Number of responses is not same as number of requests");
        }

        $exception = false;
        foreach ($responseArray as $index => $response) {
            if (is_object($response) && isset($response->e)) {
                $exception = true;
                $this->requests->get($index)->setResponseError($response->e);
            } else {
                $this->requests->get($index)->setResponse($response);
            }
        }
        if($exception) {
            $messages = '';
            foreach ($this->requests as $request) {
                $messages .= $request->getResponseError() . "|";
            }
        }
        $this->requests = new Gpf_Rpc_Array();
        if($exception) {
            throw new Gpf_Rpc_ExecutionException($messages);
        }
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getUrl() {
        return $this->url;
    }

    private function getCookies() {
        $cookiesString = '';
        foreach ($_COOKIE as $name => $value) {
            $cookiesString .= "$name=$value;";
        }
        return $cookiesString;
    }
}

?>
