<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Object.class.php 19712 2008-08-07 12:51:57Z vzeman $
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
class Gpf_Rpc_Object extends Gpf_Object implements Gpf_Rpc_Serializable {
    
    private $object;
    
    public function __construct($object = null) {
        $this->object = $object;
    }
    
    public function toObject() {
        if ($this->object != null) {
            return $this->object;
        }
        return $this;
    }
    
    public function toText() {
        return var_dump($this);
    }
}

?>
