<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Viktor Zeman
 *   @since Version 1.0.0
 *   $Id: Google.class.php 18112 2008-05-20 07:17:10Z mbebjak $
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
class Gpf_Gadget_Library extends Gpf_Object  {

    /**
     * Proxy request from server to GwtPHP server and return list of Gadgets
     * @service gadget read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Data_RecordSet
     */
    public function getWidgetsList(Gpf_Rpc_Params $params) {
        $proxyRequest = new Gpf_Rpc_Request('Aw_GadgetLibrary', 'getRows');
        $proxyRequest->setUrl('http://addons.qualityunit.com/scripts/server.php');
        $proxyRequest->setParams($params);
        $proxyRequest->sendNow();
        return $proxyRequest->getResponseObject();
    }

}
?>
