<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Michal Bebjak
*   @since Version 1.0.0
*   $Id: LoggingForm.class.php 18882 2008-06-27 12:15:52Z mfric $
*
*   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
*   Version 1.0 (the "License"); you may not use this file except in compliance
*   with the License. You may obtain a copy of the License at
*   http://www.qualityunit.com/licenses/license
*
*/

/**
 * @package PostAffiliatePro
 */
class Gpf_Plugins_Features extends Gpf_Object {
    
    /**
     * @service feature read
     * @param $fields
     */
    public function buyFeature(Gpf_Rpc_Params $params) {
        $request = new Gpf_Rpc_DataRequest("Dp_QualityUnit_AddonPurchase", "getPurchaseLink");
        $request->setUrl(Gpf_Install_LicenseManager::MEMBERS_URL);
        $request->setParams($params);
        $request->sendNow();
        return $request->getResponseObject();
    }

    /**
     * @service feature read
     * @param $fields
     */
    public function getAddonsList(Gpf_Rpc_Params $params) {
        $request = new Gpf_Rpc_RecordSetRequest("Dp_QualityUnit_AddonPurchase", "getAddonsList");
        $request->setUrl(Gpf_Install_LicenseManager::MEMBERS_URL);
        $request->setParams($params);
        $request->sendNow();
        return $request->getResponseObject();
    }
}

?>
