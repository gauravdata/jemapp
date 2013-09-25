<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: NoSocket.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_Net_Server_NoSocket extends Gpf_Net_Server_Socket {
    
    public function __construct() {
    }
    
    public function setClientInfo() {
        throw new Gpf_Exception("setClientInfo:Not valid socket");    
    }
    
    public function getSocket() {
        throw new Gpf_Exception("getSocket:Not valid socket");    
    }

    public function isValid() {
        return false;
    }

    public function getClientInfo() {
        return "getClientInfo: Not valid socket";    
    }
    
    public function getLeftOverBuffer() {
        throw new Gpf_Exception("getLeftOverBuffer:Not valid socket");    
    }
    
    public function setLeftOverBuffer($data) {
        throw new Gpf_Exception("setLeftOverBuffer:Not valid socket");    
    }
    
    public function close() {
    }
}
?>
