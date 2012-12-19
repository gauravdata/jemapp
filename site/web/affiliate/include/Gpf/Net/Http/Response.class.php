<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Request.class.php 19101 2008-07-11 12:47:55Z vzeman $
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
class Gpf_Net_Http_Response extends Gpf_Object {

    private $responseText = '';
    private $header = '';
    private $body = '';

    public function setResponseText($responseText) {
        $this->responseText = $responseText;
        $this->parse();
    }

    public function getHeadersText() {
        return $this->header;
    }

    private function getHeaderPosition($pos) {
        return strpos($this->responseText, "\r\n\r\nHTTP", $pos);
    }

    public function getBody() {
        return $this->body;
    }

    private function parse() {
        $offset = 0;
        while ($this->getHeaderPosition($offset)) {
            $offset = $this->getHeaderPosition($offset) + 4;
        }
        if (($pos = strpos($this->responseText, "\r\n\r\n", $offset)) > 0) {
            $this->body = substr($this->responseText, $pos + 4);
            $this->header = substr($this->responseText, $offset, $pos - $offset);
            return;
        }
        $this->body = '';
        $this->header = '';
    }



    public function getResponseCode() {
        $headers = $this->getHeaders();
        preg_match('/.*?\s([0-9]*?)\s.*/', $headers['status'], $match);
        return $match[1];
    }

    public function getHeaders() {
        return $this->httpParseHeaders($this->header);
    }

    private function httpParseHeaders($headers=false){
        if($headers === false){
            return false;
        }
        $headers = str_replace("\r","",$headers);
        $headers = explode("\n",$headers);
        foreach($headers as $value){
            $header = explode(": ",$value);
            if($header[0] && !isset($header[1])){
                $headerdata['status'] = $header[0];
            } elseif($header[0] && isset($header[1])){
                $headerdata[$header[0]] = $header[1];
            }
        }
        return $headerdata;
    }
}
?>
