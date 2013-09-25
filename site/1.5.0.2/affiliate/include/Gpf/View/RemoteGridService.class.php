<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: GridService.class.php 27783 2010-04-15 10:00:59Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 *   @package GwtPhpFramework
 */
abstract class Gpf_View_RemoteGridService extends Gpf_Object {
   
   protected abstract function getClassName();
   
   protected abstract function getUrl();
   
   public function getRows(Gpf_Rpc_Params $params) {
        $request = new Gpf_Rpc_GridRequest($this->getClassName(), 'getRows');
        $request->setUrl($this->getUrl());
        $request->setParams($params);
               
        $request->sendNow();
                        
        return $request->getResponseObject();
   }
   
   public function getCustomFilterFields(Gpf_Rpc_Params $params) {
        $request = new Gpf_Rpc_RecordSetRequest($this->getClassName(), 'getCustomFilterFields');
        $request->setParams($params);
        $request->setUrl($this->getUrl());
        $request->sendNow();
        
        return $request->getRecordSet();
   }
   
   public function getDefaultViewColumns() {
        $request = new Gpf_Rpc_RecordSetRequest($this->getClassName(), 'getDefaultViewColumns');
        $request->setUrl($this->getUrl());
        $request->sendNow();
        
        return $request->getRecordSet();
   }
   
    public function getAllViewColumns() {
        $request = new Gpf_Rpc_RecordSetRequest($this->getClassName(), 'getAllViewColumns');
        $request->setUrl($this->getUrl());
        $request->sendNow();
        
        return $request->getIndexedRecordSet('id');
   }
}

?>
