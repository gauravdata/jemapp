<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Header.class.php 18000 2008-05-13 16:00:48Z aharsani $
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

class Gpf_Net_Server_Http_Header extends Gpf_Object {
    private $name;
    private $value;
    private $replace = true;
    private $httpResponseCode = null;
    
    public function __construct($name, $value, $replace = true, $httpResponseCode = null) {
        $this->name = $name;
        $this->value = $value;
        $this->replace = $replace;
        $this->httpResponseCode = $httpResponseCode;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function getResponseCode() {
        return $this->httpResponseCode;
    }
    
    public function isReplace() {
        return $this->replace !== false;
    }
}
?>
