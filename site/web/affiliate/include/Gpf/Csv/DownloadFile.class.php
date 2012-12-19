<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework 
 *   @author Matej Kendera
 *   @since Version 1.0.0
 *   $Id: UploadFile.class.php 17234 2008-04-11 14:23:06Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Csv_DownloadFile extends Gpf_File_Download {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     *
     * @service uploaded_file delete
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Action
     */
    public function deleteFile(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->addOk();
        return $action;
    }
}
