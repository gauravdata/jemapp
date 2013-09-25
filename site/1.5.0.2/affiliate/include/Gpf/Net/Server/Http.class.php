<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Http.class.php 18094 2008-05-17 00:27:35Z aharsani $
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
abstract class Gpf_Net_Server_Http extends Gpf_Object implements Gpf_Net_Server_Handler, Gpf_HttpResponse {
    /**
     * Max. size of request header
     *
     */
    protected $requestHeaderMaxBuffer = '16000';

    /**
     * @var Gpf_Net_Server
     */
    protected $driver;

    protected static $methods = array('GET', 'HEAD', 'POST');

    /**
     *
     * @var Gpf_Net_Server_Http_Request
     */
    private static $request;

    private static $responseCookies = array();

    /**
     * Note: Revealing the specific software version of the server might
     * allow the server machine to become more vulnerable to attacks
     * against software that is known to contain security holes. Server
     * implementors are encouraged to make this field a configurable
     * option.
     *
     */
    protected $serverName = 'QualityUnitHttp 1.0';

    private $readBufferSize = 128;

    private $getHandlers = array();
    private $postHandlers = array();
    private $responseHeaders = array();
    private $documentRootUrl;

    private static function clearCookies() {
        self::$responseCookies = array();
    }

    protected function addPostHandler(Gpf_Net_Server_Http_Handler $handler, $path = '') {
        $this->postHandlers[$path] = $handler;
    }

    protected function addGetHandler(Gpf_Net_Server_Http_Handler $handler, $path = '') {
        $this->getHandlers[$path] = $handler;
    }

    public function setCookieValue($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httpOnly = null) {
        $cookie = new Gpf_Net_Server_Http_Cookie($name, $value);
        $cookie->setExpire($expire);
        $cookie->setDomain($domain);
        $cookie->setPath($path);
        $cookie->setSecure($secure);
        if(self::$responseCookies === null) {
            self::clearCookies();
        }
        self::$responseCookies[$name] = $cookie;
    }
    
    public function setHeaderValue($name, $value, $replace = true, $httpResponseCode = null) {
        $this->responseHeaders[] = new Gpf_Net_Server_Http_Header($name, $value, $replace, $httpResponseCode);
    }
    
    /**
     * constructor
     *
     * @access   public
     * @param    string      hostname
     * @param    integer     port
     * @param    string      driver, see Net_Server documentation
     */
    protected function __construct($hostname, $port = 80, $documentRootUrl = '/', Gpf_Net_Server $driver = null) {
        $this->documentRootUrl = rtrim(str_replace('\\', '/', $documentRootUrl), '/') . '/';
        if($driver === null) {
            $this->driver = new Gpf_Net_Server_Driver_Sequential($hostname, $port);
        } else {
            $this->driver = $driver;
            $this->driver->setAddress($hostname, $port);
        }
        $this->driver->setListener($this);
    }

    public function __destruct() {
        $this->driver->shutDown();
    }
    
    /**
     * start the server
     *
     * @access   public
     */
    public function start() {
        try {
            $this->driver->start();
        } catch (Exception $e) {
            die("\n" . $e->getMessage());
        }
    }

    /**
     *
     * @return Gpf_Net_Server
     */
    public function getDriver() {
        return $this->driver;    
    }
    
    public function onStart() {
        $this->info('HTTP server ' . $this->serverName . ' has been started');
        Gpf_Http::setInstance($this);
    }

    public function onIdle() {
    }

    public function onShutdown() {
        $this->info('HTTP server ' . $this->serverName . ' has been shutdown');
    }

    public function onConnect() {
    }

    public function onDisconnect() {
    }

    public function onConnectionRefused() {
    }

    public function onReceiveData() {
        $this->debug('Processing request');
        try {
            $request = $this->readRequestHeader();
            self::$request = $request;
            $this->responseHeaders = array();
            $this->serveRequest($request);
        } catch (Gpf_Net_Server_Http_RequestException $e) {
            $this->send($e->getResponse());
        } catch (Exception $e) {
            $this->driver->closeConnection();
            $this->error('Error while processing request: ' . $e->getMessage()
            . "\nTrace:\n" . $e->getTraceAsString());
        }
    }

    protected function info($message) {
        Gpf_Log::info($message, 'HTTP');
    }

    protected function error($message) {
        Gpf_Log::error($message, 'HTTP');
    }

    protected function debug($message) {
        Gpf_Log::debug($message, 'HTTP');
    }

    /**
     * handle a GET request
     * this method should return an array of the following format:
     *
     * @param  Gpf_Net_Server_Http_Request   $request
     * @return Gpf_Net_Server_Http_Response
     */
    protected function GET(Gpf_Net_Server_Http_Request $request) {
        if(array_key_exists($request->getPath(), $this->getHandlers)) {
            return $this->getHandlers[$request->getPath()]->handle($request);
        }

        if(array_key_exists('', $this->getHandlers)) {
            return $this->getHandlers['']->handle($request);
        }
        return self::get404();
    }
    
    /**
     *
     * @return Gpf_Net_Server_Http_Response
     */
    private static function get404() {
        $response = new Gpf_Net_Server_Http_Response(404);
        $response->setBody("Not found");
        return $response;
    }

    /**
     * handle a POST request
     * this method should return an array of the following format:
     *
     * @param  Gpf_Net_Server_Http_Request   $request
     * @return Gpf_Net_Server_Http_Response
     */
    protected function POST(Gpf_Net_Server_Http_Request $request) {
        if(array_key_exists($request->getPath(), $this->postHandlers)) {
            return $this->postHandlers[$request->getPath()]->handle($request);
        }

        if(array_key_exists('', $this->postHandlers)) {
            return $this->postHandlers['']->handle($request);
        }
        return self::get404();
    }

    public function readBody(Gpf_Net_Server_Http_Request $request) {
        $this->debug("Trying to read HTTP request's body");
        if($request->getContentLength() <= 0) {
            $this->debug("HTTP request's body is empty OK");
            return;
        }

        try {
            $data = $this->driver->read(null, $request->getContentLength());
            $this->debug("HTTP request's body read OK");
        } catch (Gpf_Net_Server_SocketException $e) {
            $this->driver->closeConnection();
            throw new Gpf_Net_Server_SocketException("Body: " . $e->getMessage());
        }
        $request->loadBody($data);
    }

    protected function isMethodAllowed($method) {
        return in_array($method, self::$methods);
    }

    private function send(Gpf_Net_Server_Http_Response $response) {
        $response->setCookies(self::$responseCookies);
        $this->setHeaders($response);
        self::clearCookies();
        $response->setServerName($this->serverName);
        try {
            if ($response instanceof Gpf_Net_Server_Http_StreamResponse) {
                $this->driver->send($response->getHeadersAsString() . $response->getStream()->getData());
                while ($data = $response->getStream()->getData()) {
                    $this->driver->send($data);
                }
            } else {
                $this->driver->send($response->toString());
            }
        } catch (Gpf_Net_Server_SocketException $e) {
            $this->driver->closeConnection();
        }
    }
    
    private function setHeaders(Gpf_Net_Server_Http_Response $response) {
        foreach ($this->responseHeaders as $header) {
            if($header->isReplace()) {
                $response->setHeader($header->getName(), $header->getValue());
            } else {
                $response->addMultiHeader($header->getName(), $header->getValue());
            }
            if($header->getResponseCode() !== null) {
                $response->setCode($header->getResponseCode());
            }
        }
    }

    /**
     *
     * @return Gpf_Net_Server_Http_Request
     */
    private function readRequestHeader() {
        $this->debug("Trying to read HTTP request's header");
        try {
            $header = $this->driver->read(Gpf_Net_Server_Http_Message::BODY_SEPARATOR, $this->requestHeaderMaxBuffer);
            $this->debug("HTTP request's header read OK");
        } catch (Gpf_Net_Server_SocketException $e) {
            $this->driver->closeConnection();
            throw new Gpf_Net_Server_SocketException("Header: " . $e->getMessage());
        }

        $header = substr($header, 0, -strlen(Gpf_Net_Server_Http_Message::BODY_SEPARATOR));
        $request = Gpf_Net_Server_Http_Request::parseHeader($header);
        $this->debug('URL:' . $request->getUri());
        $this->checkRequestHeader($request);
        $request->setServerVars($this);
        return $request;
    }

    private function serveRequest(Gpf_Net_Server_Http_Request $request) {
        $method = $request->getMethod();

        if(!method_exists($this, $method)) {
            // not implemented
            //TODO: refactor
            $response = new Gpf_Net_Server_Http_Response(501);
            $response->setContentType("text/html");
            $response->setConnection("close");
            throw new Gpf_Net_Server_Http_RequestException($response);
        }

        if(!$this->isMethodAllowed($method)) {
            // not allowed
            //TODO: refactor
            $response = new Gpf_Net_Server_Http_Response(405);
            $response->setContentType("text/html");
            $response->setConnection("close");
            throw new Gpf_Net_Server_Http_RequestException($response);
        }

        $response = $this->$method($request);
        if($response === null) {
            return;
        }
        $this->send($response);
    }

    private function checkRequestHeader(Gpf_Net_Server_Http_Request $request) {
        //HTTP/1.1 All Internet-based HTTP/1.1 servers MUST respond with a 400
        //         (Bad Request) status code to any HTTP/1.1 request message which
        //         lacks a Host header field.
        if($request->getVersion() == '1.1' && !$request->existsHeader('Host')) {
            $response = Gpf_Net_Server_Http_Response::createBadRequest(
                "Host header is missing");
            throw new Gpf_Net_Server_Http_RequestException($response);
        }

//        if($request->existsHeader('TE')) {
//            $response = Gpf_Net_Server_Http_Response::createUnimplemented(
//                "Transfer-Encoding not supported");
//            throw new Gpf_Net_Server_Http_RequestException($response);
//        }

        if($request->headerContain('Content-Type', 'multipart/byteranges') ) {
            $response = Gpf_Net_Server_Http_Response::createUnimplemented(
                "Content-Type multipart/byteranges not supported");
            throw new Gpf_Net_Server_Http_RequestException($response);
        }
    }
    
    public function getDocumentRootUrl() {
        return $this->documentRootUrl;
    }
}
?>
