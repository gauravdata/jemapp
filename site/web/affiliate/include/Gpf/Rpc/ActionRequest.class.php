<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ActionRequest.class.php 24612 2009-06-11 13:28:02Z aharsani $
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
class Gpf_Rpc_ActionRequest extends Gpf_Rpc_Request {
    
    /**
     * @return Gpf_Rpc_Action
     */
    public function getAction() {
        $action = new Gpf_Rpc_Action(new Gpf_Rpc_Params());
        $action->loadFromObject($this->getStdResponse());
        return $action;        
    }
}

?>
