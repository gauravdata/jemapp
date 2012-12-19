<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: PermissionDeniedException.class.php 20130 2008-08-25 13:46:08Z mbebjak $
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
class Gpf_Rpc_SessionExpiredException extends Gpf_Exception implements Gpf_Rpc_Serializable {
     
    function __construct() {
        parent::__construct("PHP session expired");
    }
    
    public function toObject() {
        $ret = new stdClass();
        $ret->type = 'session';
        return $ret;
    }

    public function toText() {
        return $this->getMessage();
    }
}
?>
