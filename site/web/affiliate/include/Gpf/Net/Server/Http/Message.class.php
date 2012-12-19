<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Message.class.php 18094 2008-05-17 00:27:35Z aharsani $
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

abstract class Gpf_Net_Server_Http_Message extends Gpf_Object {
    const BODY_SEPARATOR = "\r\n\r\n";
    const CRLF = "\r\n";

    const CONTENT_LENGTH = 'Content-Length';
    const CONNECTION = 'Connection';
    const ETAG = 'Etag';
    const DATE = 'Date';
    const IFNONEMATCH = 'If-None-Match';
    
    /**
     * headers
     *
     * @access   public
     * @var      array
     */
    protected $headers = array();
    protected $version = '1.1';
    protected $body = '';


    public function setHeader($name, $value) {
        $this->headers[strtolower($name)] = array($value, $name);
    }

    public function addMultiHeader($name, $value) {
        if(!$this->existsHeader($name)) {
            $this->setHeader($name, $value);
            return;
        }
        $valueArr = $this->headers[strtolower($name)];
        if(!is_array($valueArr[0])) {
            $this->headers[strtolower($name)][0] = array($valueArr[0]);
        }
        $this->headers[strtolower($name)][0][] = $value;
    }

    public function addHeader($name, $value) {
        if(!$this->existsHeader($name)) {
            $this->setHeader($name, $value);
        } else {
            $this->setHeader($name, $this->getHeader($name) . ',' . $value);
        }
    }

    public function existsHeader($name) {
        return array_key_exists(strtolower($name), $this->headers);
    }

    public function headerContain($name, $value) {
        $myValue = $this->getHeader($name);
        if($myValue === null) {
            return false;
        }
        return strtolower($myValue) == strtolower($value);
    }

    public function setVersion($version) {
        $this->version = $version;
    }

    public function getVersion() {
        return $this->version;
    }

    public function getBody() {
        if (strlen($this->body)) {
            return $this->body;
        }
        return '';
    }

    public function clearHeader($name) {
        unset($this->headers[strtolower($name)]);
    }

    public function getHeader($name) {
        if(!$this->existsHeader($name)) {
            return null;
        }
        $h = $this->headers[strtolower($name)];
        return $h[0];
    }

    public function getContentLength() {
        if(!$this->existsHeader(self::CONTENT_LENGTH)) {
            return 0;
        }
        return (int)$this->getHeader(self::CONTENT_LENGTH);
    }

    public function setContentLength($len) {
        $this->setHeader('Content-Length', $len);
    }

    public function setConnection($value) {
        $this->setHeader(self::CONNECTION, $value);
    }

    public function setBody($body) {
        $this->setContentLength(strlen($body));
        $this->body = $body;
    }
    
    /**
     * Set file ETag header
     *
     * @param string $value
     */
    public function setETag($value) {
        $this->setHeader(self::ETAG, $value);
    }
    
    /**
     * Return If-None-Match header value
     *
     * @return string
     */
    public function getIfNoneMatch() {
        return $this->getHeader(self::IFNONEMATCH);
    }
    
    /**
     * Return true if If-None-Match tag contains given ETag or contains *
     *
     * @param string $etag
     * @return boolean
     */
    public function ifNoneMatch($etag) {
        return $this->headerContain(self::IFNONEMATCH, $etag) || $this->getIfNoneMatch() === '*';
    }

    protected function getHeadersAsString() {
        $text = '';
        foreach($this->headers as $name => $value) {
            if(is_array($value[0])) {
                foreach ($value[0] as $val) {
                    $text .= $value[1] . ': ' . $val . self::CRLF;
                }
            } else {
                $text .= $value[1] . ': ' . $value[0] . self::CRLF;
            }
        }
        return $text;
    }
}
?>
