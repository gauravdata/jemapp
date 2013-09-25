<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @author Matej Kendera
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id:
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
class Gpf_Api_DownloadAPI {
	
	const API_BASE_PATH = '../api/';

    /**
     * Download API file from server
     * @service
     * @anonym
     * @param Gpf_Rpc_Params $params
     */
    public function download(Gpf_Rpc_Params $params) {
        $file = new Gpf_File_Download_FileSystem(self::API_BASE_PATH . Gpf_Application::getInstance()->getApiFileName());
        $file->setAttachment(true);
        return $file;
    }
}

?>
