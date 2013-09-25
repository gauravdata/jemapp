<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: Merchant.class.php 18071 2008-05-16 08:02:18Z aharsani $
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

class Gpf_Install_LicenseManager extends Gpf_Object {
    const LICENSE_ID_NAME = 'LicenseNumber';
    const LICENSE_CODE_MEMBERS_NAME = 'license_code';
    const LICENSE_MEMBERS_NAME = 'license';
    const MIN_LICENSE_LENGTH = 100;

    const MEMBERS_URL = 'http://members.qualityunit.com/scripts/server.php';


    /**
     * @param $licenseCode
     * @param $installUrl
     * @return Gpf_Install_LicenseInfo
     */
    public function getLicense($licenseCode, $installUrl = '', $hostedAccountId = null) {
        $licenseInfo = $this->getLicenseInfo($licenseCode, $installUrl, $hostedAccountId);
        $this->save($licenseInfo);
        return $licenseInfo;
    }

    protected function getLicenseFromLocalSettings() {
        return Gpf_Settings::get(Gpf_Settings_Gpf::LICENSE);
    }

    /**
     *
     * @return Gpf_Rpc_Data
     */
    private function sendRequestAndGetResponse(Gpf_Rpc_DataRequest $request) {
        $request->sendNow();
        return $request->getData();
    }

    /**
     * @param $licenseCode
     * @param $installUrl
     * @return Gpf_Install_LicenseInfo
     */
    private function getLicenseInfo($licenseCode, $installUrl = '', $hostedAccountId = null) {
        if(strlen($licenseCode) > self::MIN_LICENSE_LENGTH) {
            $licenseInfo = new Gpf_Install_LicenseInfo();
            $licenseInfo->setLicense($licenseCode);
            return $licenseInfo;
        }

        $response = $this->sendRequestAndGetResponse($this->createValidateLicenseDatarequest($licenseCode, $installUrl, $hostedAccountId));
        $license = $response->getValue(self::LICENSE_MEMBERS_NAME);

        if($license == '') {
            Gpf_Log::error(sprintf('Response from members recieved, but contains system error: %s', $response->getValue('message')));
            throw new Gpf_Exception("Invalid license");
        }
        $licenseInfo = new Gpf_Install_LicenseInfo();
        $licenseInfo->setLicense($license);

        $variationName = $response->getValue('variation_name');
        if(null !== $variationName) {
            $licenseInfo->setProductVariationName($variationName);
        }
        $licenseInfo->setApplicationCode($response->getValue('app_code'));
        return $licenseInfo;
    }

    private function removeLicense() {
        $emptyLicenseInfo = new Gpf_Install_LicenseInfo();
        $this->save($emptyLicenseInfo);
    }

    protected function save(Gpf_Install_LicenseInfo $licenseInfo) {
        Gpf_Settings::set(Gpf_Settings_Gpf::LICENSE, $licenseInfo->getLicense());

        //NOTE: during installation database table does not exist
        try {
            Gpf_Settings::set(Gpf_Settings_Gpf::VARIATION, $licenseInfo->getProductVariationName());
        } catch (Exception $e) {
        }
    }

    /**
     * @return Gpf_Rpc_DataRequest
     */
    protected function createValidateLicenseDatarequest($licenseCode, $installUrl, $hostedAccountId) {
        $request = new Gpf_Rpc_DataRequest('Dp_License_Generator', 'validateLicense');
        $request->setField(self::LICENSE_CODE_MEMBERS_NAME, $licenseCode);

        $url = new Gpf_Install_Url($installUrl);
        $request->setField('url', $url->toString());
        $request->setField('version', Gpf_Application::getInstance()->getVersion());
        $request->setUrl(self::MEMBERS_URL);
        return $request;
    }

    public function isHostingLicense() {
        try {
            $accountId = Gpf_Paths::getInstance()->getServerHostedAccountId();
        } catch (Gpf_Exception $e) {
            return false;
        }
        return class_exists('Pap_Features_Hosting_Main');
    }

    /**
     * @anonym
     * @service
     *
     * @param Gpf_Rpc_Params $params
     */
    public function updateLicense(Gpf_Rpc_Params $params) {
        $response = new Gpf_Rpc_Data();
        try {
            $info = $this->getLicense($params->get(self::LICENSE_ID_NAME));
            $response->setValue("license", $info->getLicense());
            $response->setValue("variationName", $info->getProductVariationName());
        } catch (Exception $e) {
            $response->setValue("license", $this->getLicenseFromLocalSettings());
            try {
                $response->setValue("variationName", Gpf_Settings::get(Gpf_Settings_Gpf::VARIATION));
            } catch (Exception $e) {
                $response->setValue("variationName", "");
            }
        }
        return $response;
    }

    /**
     * @anonym
     * @service
     *
     * @param Gpf_Rpc_Params $params
     */
    public function clearLicence(Gpf_Rpc_Params $params) {
        $response = new Gpf_Rpc_Action($params);
        $this->removeLicense();
        return $response;
    }
}
?>
