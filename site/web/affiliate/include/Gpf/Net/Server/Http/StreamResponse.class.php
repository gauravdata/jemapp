<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Response.class.php 18000 2008-05-13 16:00:48Z aharsani $
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

require_once 'HTTP.php';

class Gpf_Net_Server_Http_StreamResponse extends Gpf_Net_Server_Http_Response {
    /**
     * @var Gpf_Common_Stream
     */
    private $streamingObject;
    
    /**
     * @param unknown_type $code
     * @param Gpf_Common_Stream $streamingObject
     */
    public function __construct($code, Gpf_Common_Stream $streamingObject) {
        parent::__construct($code);
        $this->streamingObject = $streamingObject;
    }
    
    /**
     * Return streaming object
     *
     * @return Gpf_Common_Stream
     */
    public function getStream() {
        return $this->streamingObject;
    }
}
?>
