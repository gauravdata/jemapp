<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Context.class.php 18001 2008-05-13 16:05:33Z aharsani $
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
class Gpf_Plugins_ValueContext  {

    private $value;
    /**
     * @var array
     */
    private $array;

    public function __construct($value) {
        $this->value = $value;
    }

    public function get() {
        return $this->value;
    }

    public function set($value) {
        $this->value = $value;
    }

    public function getArray() {
        return $this->array;
    }

    public function setArray(array $array) {
        $this->array = $array;
    }
}
?>
