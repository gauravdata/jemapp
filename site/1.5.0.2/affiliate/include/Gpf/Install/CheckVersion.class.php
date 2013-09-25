<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
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
class Gpf_Install_CheckVersion extends Gpf_Object {
    
    /**
     *
     * @service
     * @anonym
     * 
     * @param Gpf_Rpc_Params $params
     */
    public function getLatestVersion(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);
        
        $request = new Gpf_Rpc_DataRequest('Dp_Version_Info', 'getLatestVersion');
        $request->setUrl(Gpf_Install_LicenseManager::MEMBERS_URL);
        $request->setField('id', $data->getParam('id'));

        try {
            $request->sendNow();
            $versionInfo = $request->getData();
            if(version_compare($versionInfo->getValue('version'), 
                Gpf_Application::getInstance()->getVersion()) < 0) {
                $versionInfo->setValue('version', Gpf_Application::getInstance()->getVersion());        
            }
            return $versionInfo;
        } catch (Exception $e) {
        }
        return $data;
    }
}
?>
