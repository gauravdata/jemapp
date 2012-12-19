<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Sale.class.php 20226 2008-08-27 09:18:01Z mfric $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 *
 * all public properties in JsonObject are encoded
 */
class Gpf_Rpc_JsonObject extends Gpf_Object {
    
    public function __construct($object = null) {
        if ($object != null) {
            $this->initFrom($object);
        }
    }
    
    public function decode($string) {
        if ($string == null || $string == "") {
            throw new Gpf_Exception("Invalid format (".get_class($this).")");
        }
        $string = stripslashes($string);
        $json = new Gpf_Rpc_Json();
        $object = $json->decode($string);
        if (!is_object($object)) {
            throw new Gpf_Exception("Invalid format (".get_class($this).")");
        }
        $this->initFrom($object);
    }
    
    private function initFrom($object) {
        $object_vars = get_object_vars($object);
        foreach ($object_vars as $name => $value) {
            if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }
    }
    
    public function encode() {
        $json = new Gpf_Rpc_Json();
        return $json->encode($this);
    }
    
    public function __toString() {
        return $this->encode();
    }
}
?>
