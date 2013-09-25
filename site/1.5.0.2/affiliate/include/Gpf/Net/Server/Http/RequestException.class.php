<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: RequestException.class.php 18000 2008-05-13 16:00:48Z aharsani $
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

class Gpf_Net_Server_Http_RequestException extends Gpf_Exception {
    /**
     *
     * @var Gpf_Net_Server_Http_Response
     */
    private $response;
    
    public function __construct(Gpf_Net_Server_Http_Response $response) {
        parent::__construct('');
        $this->response = $response;
    }
    
    protected function logException() {
    }
    
    /**
     *
     * @return Gpf_Net_Server_Http_Response
     */
    public function getResponse() {
        return $this->response;
    }
}
?>
