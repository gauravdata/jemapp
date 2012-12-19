<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Cookie.class.php 18000 2008-05-13 16:00:48Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

require_once 'HTTP.php';

/**
 * @package GwtPhpFramework
 */
class Gpf_Net_Server_Http_Cookie extends Gpf_Object {
    private $name;
    private $value;
    private $expire;
    private $path;
    private $domain;
    private $secure = false;

    public function __construct($name, $value) {
        $this->name = $name;
        $this->value = $value;
    }

    public function toString() {
        $out = $this->name . '=' . urldecode($this->value);
        if($this->expire !== null) {
            $out .= '; expires=' . HTTP::Date($this->expire);
        }
        if($this->path !== null) {
            $out .= '; path=' . $this->path;
        }
        if($this->domain !== null) {
            $out .= '; domain=' . urlencode($this->domain);
        }
        if($this->secure === true) {
            $out .= '; secure';
        }
        return $out;
    }
    
    public function setExpire($expire) {
        $this->expire = $expire;
    }
    
    public function setDomain($domain) {
        $this->domain = $domain;
    }
    
    public function setPath($path) {
        $this->path = $path;
    }
    
    public function setSecure($secure) {
        $this->secure = $secure;
    }
}
?>
