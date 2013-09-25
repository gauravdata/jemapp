<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Response.class.php 25412 2009-09-16 12:53:21Z jsimon $
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

require_once 'HTTP.php';

class Gpf_Net_Server_Http_Response extends Gpf_Net_Server_Http_Message {
    const SET_COOKIE = 'Set-Cookie';
    const CONTENT_TYPE = 'Content-Type';
    const CONTENT_DISPOSITION = 'Content-Disposition';
    const LOCATION = 'Location';

    private $code = 200;

    /**
     * list of HTTP status codes
     * @var array $_statusCodes
     */
    private static $statusCodes = array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    102 => 'Processing',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoriative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    207 => 'Multi-Status',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Granted',
    403 => 'Forbidden',
    404 => 'File Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Time-out',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Large',
    415 => 'Unsupported Media Type',
    416 => 'Requested range not satisfiable',
    417 => 'Expectation Failed',
    422 => 'Unprocessable Entity',
    423 => 'Locked',
    424 => 'Failed Dependency',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Overloaded',
    503 => 'Gateway Timeout',
    505 => 'HTTP Version not supported',
    507 => 'Insufficient Storage'
    );

    /**
     *
     * @param unknown_type $text
     * @return Gpf_Net_Server_Http_Response
     */
    public static function createBadRequest($text) {
        $response = new Gpf_Net_Server_Http_Response(400);
        $response->setConnection("close");
        $response->setBody($text);
        return $response;
    }

    /**
     *
     * @param unknown_type $text
     * @return Gpf_Net_Server_Http_Response
     */
    public static function createUnimplemented($text) {
        $response = new Gpf_Net_Server_Http_Response(501);
        $response->setConnection("close");
        $response->setBody($text);
        return $response;
    }

    public function __construct($code) {
        $this->code = $code;
        $this->setServerName('quw');
    }

    public function setCode($code) {
        $this->code = $code;
    }
    
    public function getCode() {
        return $this->code;
    }
    
    public function setCookies(array $cookies) {
        foreach ($cookies as $cookie) {
            $this->addMultiHeader(self::SET_COOKIE, $cookie->toString());
        }
    }

    public function getHeadersAsString() {
        $text = sprintf("HTTP/%s %s %s" . self::CRLF, $this->version, $this->code,
        self::resolveStatusCode($this->code));

        $this->setHeader(self::DATE, HTTP::Date(time()));

        if($this->body && !$this->existsHeader('Content-Type')) {
            $this->setContentType('text/html;charset=UTF-8');
        }
        
        $text .= parent::getHeadersAsString() . self::CRLF;
        
        return $text;
    }

    public function toString() {
        $text = $this->getHeadersAsString();
        $text .= $this->getBody();
        return $text;
    }

    private function checkMessageBody() {
        if($this->code >= 100 && $this->code < 200 || $this->code ==204 || $this->code == 304) {
            //remove body
            $this->setBody('');
            return;
        }
        $this->setBody($this->getBody());
    }

    public function setServerName($value) {
        $this->setHeader('Server', $value);
    }

    public function setContentType($value) {
        $this->setHeader('Content-Type', $value);
    }

    /**
     * resolve a status code
     *
     * @access private
     * @param integer $code status code
     * @return string $status http status
     */
    protected static function resolveStatusCode($code) {
        if (!isset(self::$statusCodes[$code])) {
            return false;
        }
        return self::$statusCodes[$code];
    }
}
?>
