<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Versions.class.php 18552 2008-06-17 12:59:40Z aharsani $
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
class Gpf_Install_CheckModSecRpcCaller extends Gpf_Object {
    
    /**
     * @param Gpf_Rpc_Params $params
     * @service
     * @anonym
     * @return Gpf_Rpc_Data
     */
    public function check(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);
        $text = $data->getParam('teststring');
        
        $dataOut = new Gpf_Rpc_Data();
        $dataOut->setValue('status', 'OK');
        $dataOut->setValue('recieved', $text);
        return $dataOut;
    }
}
