<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: FormHandler.class.php 21318 2008-09-29 14:23:07Z mjancovic $
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
class Gpf_Rpc_WindowNameHandler extends Gpf_Rpc_Json {

    /**
     * @return string  string representation of input var or an error if a problem occurs
     */
    public function encodeResponse(Gpf_Rpc_Serializable $response) {
        return '<html><script type="text/javascript">
        window.name="' 
      .  addcslashes(parent::encodeResponse($response), '"\\') . 
        '";</script></html>';
    }

    /**
     * @param array $requestArray
     * @return StdClass
     */
    function decode($requestArray) {
        throw new Gpf_Exception('Not implemented yet.');
    }
}
?>
