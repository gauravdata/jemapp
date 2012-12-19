<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ExceptionResponse.class.php 26218 2009-11-24 11:44:41Z mbebjak $
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
class Gpf_Rpc_ExceptionResponse extends Gpf_Object implements Gpf_Rpc_Serializable {
    /**
     * @var Exception
     */
    private $e;
    
    public function __construct(Exception $e) {
        $this->e = $e;
    }
    
    public function toObject() {
        $obj = new stdClass();
        $obj->e = $this->toText();
        return $obj;
    }
    
    public function toText() {
        return $this->e->getMessage();// . '('.$this->e->getTraceAsString().')';
    }
}

?>
