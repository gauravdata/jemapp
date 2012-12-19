<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Request.class.php 25075 2009-07-28 12:40:05Z mjancovic $
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


/*
 * TODO: 1.Any response message which "MUST NOT" include a message-body
 *         (such as the 1xx, 204, and 304 responses and any response to a
 *         HEAD request) is always terminated by the first empty line after
 *         the header fields, regardless of the entity-header fields present
 *         in the message.
 */

class Gpf_Net_Server_Http_Request extends Gpf_Net_Server_Http_Message {
    const USER_AGENT = 'User-Agent';
    const REFERER = 'Referer';
    const ACCEPT_LANGUAGE = 'Accept-Language';
    const ACCEPT_ENCODING = 'Accept-Encoding';
    const ACCEPT_CHARSET = 'Accept-Charset';
    const ACCEPT = 'Accept';
    const COOKIE = 'Cookie';
    const HOST = 'Host';
    const PROXY_AUTH = 'Proxy-Authorization';

    private $method;
    private $uri;

    private $path = '';
    private $query = '';
    private $scheme = '';
    private $host = '';
    private $port = '';
    private $cookies = null;
    private static $httpVars = null;

    private $proxyServer;
    private $proxyPort;
    private $proxyUser;
    private $proxyPassword;


    public function __construct($method, $uri, $version) {
        $this->method = $method;
        $this->uri = $uri;
        $this->version = $version;
        $this->parseUri($this->uri);
    }

    public function loadBody($body) {
        $this->body = $body;
    }

    /**
     * @return Gpf_Net_Server_Http_Request
     */
    public static function parseHeader($requestString) {
        $lines = explode ("\n", $requestString);
        $firstLine = 0;

        //HTTP1.1 servers SHOULD ignore any empty line(s) received where a Request-Line is expected
        while($firstLine < count($lines) && strlen($lines[$firstLine++]) == 0);

        $regs = array();
        if (!preg_match("'^([^ ]+)[ ]+([^ ]+)[ ]+HTTP/([^ ]+)$'", $lines[--$firstLine], $regs)) {
            $response = Gpf_Net_Server_Http_Response::createBadRequest(
                "Bad request");
            throw new Gpf_Net_Server_Http_RequestException($response);
        }

        $request = new Gpf_Net_Server_Http_Request($regs[1], $regs[2], $regs[3]);

        for ($i = $firstLine + 1; $i < count($lines); $i++) {
            $line = rtrim($lines[$i]);

            //HTTP1.1 Header fields can be extended over multiple lines by preceding
            //         each extra line with at least one SP or HT
            while($i < (count($lines) - 1) && $lines[$i+1] != ltrim($lines[$i+1])) {
                $line .= ' ' . trim($lines[$i+1]);
                $i++;
            }
            $regs = array();
            if (!preg_match("'^([^: ]+): (.+)$'", $line, $regs)) {
                $response = Gpf_Net_Server_Http_Response::createBadRequest(
                    "Request header filed is missing : separator");
                throw new Gpf_Net_Server_Http_RequestException($response);
            }
            $request->addHeader($regs[1], $regs[2]);
        }
        $request->exportPhpVars();
        return $request;
    }


    public function isKeepConnection() {
        if($this->version == '1.0') {
            if($this->headerContain(self::CONNECTION, 'keep-alive')) {
                return true;
            }
            return false;
        }

        if($this->version = '1.1' && $this->headerContain('Connection', 'close')) {
            return false;
        }
        return true;
    }

    /**
     * get the request method
     *
     * @access   public
     * @return   string
     */
    public function getMethod() {
        return strtoupper($this->method);
    }

    public function getPath() {
        return $this->path;
    }

    public function getUri() {
        return $this->uri;
    }

    public function setUri($uri) {
        $this->uri = $uri;
        $this->parseUri($this->uri);
    }

    public function getQueryString() {
        return $this->query;
    }

    public function setServerVars(Gpf_Net_Server_Http $server) {
        $_SERVER['REMOTE_IP'] = $server->getDriver()->getRemoteIp();
        $_SERVER['REMOTE_HOST'] = $server->getDriver()->getRemoteHost();
    }

    /**
     * parse a request uri
     *
     * @access    public
     * @param    string    $path    uri to parse
     * @return    array    $path    path data
     */
    private function parseUri($uri) {
        $parsed = @parse_url($uri);

        if(isset($parsed['path'])) {
            $this->path = $parsed['path'];
        }
        if(isset($parsed['query'])) {
            $this->query = $parsed['query'];
        }
        if(isset($parsed['scheme'])) {
            $this->scheme = $parsed['scheme'];
        }
        if(isset($parsed['host'])) {
            $this->host = $parsed['host'];
        }
        if(isset($parsed['port'])) {
            $this->port = $parsed['port'];
        }
    }

    public function toString() {
        $out = sprintf('%s %s HTTP/%s' . self::CRLF, $this->method, $this->uri,
        $this->version);
        $out .= $this->getHeadersAsString();
        $out .= self::CRLF . $this->getBody();
        return $out;
    }

    public function setHost($host, $port) {
        if($port == 80 || $port == null || $port == '') {
            $this->setHeader(self::HOST, $host);
            return;
        }
        $this->setHeader(self::HOST, $host . ':' . $port);
    }

    public function getHost() {
        if ($this->isUsedProxyServer()) {
            return $this->proxyServer;
        }

        if (strpos($this->getHeader(self::HOST), ':') === false) {
            return $this->getHeader(self::HOST);
        } else {
            $host = explode(':', $this->getHeader(self::HOST));
            return $host[0];
        }
    }

    public function getPort() {
        if ($this->isUsedProxyServer()) {
            return $this->proxyPort;
        }

        if (strpos($this->getHeader(self::HOST), ':') === false) {
            return 80;
        } else {
            $host = explode(':', $this->getHeader(self::HOST));
            return $host[1];
        }
    }

    private static function getHttpVars() {
        if(self::$httpVars === null) {
            self::$httpVars = array(self::ACCEPT_ENCODING,
            self::ACCEPT_LANGUAGE, self::USER_AGENT, self::REFERER,
            self::CONNECTION, self::ACCEPT, self::ACCEPT_CHARSET);
        }
        return self::$httpVars;
    }

    /**
     *   Exports server variables based on request data
     *   like _GET, _SERVER[HTTP_*] and so
     *   The function can be used to make your own
     *   HTTP server act more than a "real" one (like apache)
     *
     *   @access public
     */
    private function exportPhpVars() {
        $this->loadGetParams();

        foreach (self::getHttpVars() as $var) {
            $value = $this->getHeader($var);
            $name = 'HTTP_' . str_replace('-', '_', strtoupper($var));
            if($value !== null) {
                $_SERVER[$name] = $value;
            } else {
                unset($_SERVER[$name]);
            }
        }

        $host = $this->getHeader(self::HOST);
        $nPos = strpos($host, ':');
        if ($nPos !== false) {
            $_SERVER['HTTP_HOST']   = substr($host, 0, $nPos);
            $_SERVER['SERVER_PORT'] = substr($host, $nPos);
        } else {
            $_SERVER['HTTP_HOST']   = $host;
            $_SERVER['SERVER_PORT'] = 80;
        }

        $_SERVER['QUERY_STRING']    = $this->query;
        $_SERVER['REQUEST_METHOD']  = $this->method;
        $_SERVER['REQUEST_URI']     = $this->uri;

        $this->loadCookies();

        //TODO: POST, REQUEST, FILES
    }

    public function existsGetParam($name) {
        return array_key_exists($name, $_GET);
    }

    public function getGetParam($name) {
        if(!$this->existsGetParam($name)) {
            return null;
        }
        return $_GET[$name];
    }

    private function loadGetParams() {
        $_GET = array();
        $_REQUEST = array();
        if(strlen($this->getQueryString()) <= 0) {
            return;
        }
        $params = explode('&', $this->getQueryString());


        foreach($params as $param) {
            $param = preg_split('/=/', rtrim($param, "\r"));
            if (isset($param[1])) {
                $_GET[urldecode($param[0])] = urldecode($param[1]);
                $_REQUEST[urldecode($param[0])] = urldecode($param[1]);
            } else {
                $_GET[urldecode($param[0])] = '';
                $_REQUEST[urldecode($param[0])] = '';
            }
        }
    }

    private function loadCookies() {
        $cookies = explode(';', $this->getHeader(self::COOKIE));
        $_COOKIE = array();

        foreach ($cookies as $cookie) {
            $cookie = explode('=', ltrim($cookie));
            if(isset($cookie[1])) {
                $_COOKIE[urldecode($cookie[0])] = urldecode($cookie[1]);
            } else {
                $_COOKIE[urldecode($cookie[0])] = '';
            }
        }
    }


    /**
     * In case request should be redirected through proxy server, set proxy server settings
     * This function should be called after function setHost !!!
     *
     * @param string $server
     * @param string $port
     * @param string $user
     * @param string $password
     */
    public function setProxyServer($server, $port, $user, $password) {
        $this->proxyServer = $server;
        $this->proxyPort = $port;
        $this->proxyUser = $user;
        $this->proxyPassword = $password;
        
        $this->setHost($this->proxyServer, $this->proxyPort);
        if (strlen($this->proxyUser)) {
            $this->setHeader(self::PROXY_AUTH, 
            'Basic ' . base64_encode ($this->proxyUser . ':' . $this->proxyPassword));
        }
    }

    private function isUsedProxyServer() {
        return strlen($this->proxyServer) > 0;
    }
}
?>
