<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
abstract class Pap_Stats_Data_Object extends Gpf_Object {

    private $valueNames;
    
    public function __construct() {
        $this->valueNames = $this->getValueNames();
    }
    
    protected abstract function getValueNames();
    
    public function __get($name) {
        if (in_array($name, $this->valueNames)) {
            $method = "get" . strtoupper($name[0]) . substr($name, 1);
            return $this->$method();
        }
        return 'Undefined';
    }
    
    public function __toString() {
        $string = "";
        foreach ($this->valueNames as $valueName) {
            $string = $string . $valueName . " = " . $this->__get($valueName) . ", ";
        }
        return rtrim($string, ', ');
    }
}
?>
