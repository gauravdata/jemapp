<?php

class WSC_MageJam_Helper_Auth extends Mage_Core_Helper_Abstract
{
    /**
     * Constants for XML path config settings
     */
    const XML_PATH_USE_AUTH = 'magejam/settings/auth';
    const XML_PATH_USE_API_LOGIN = 'magejam/settings/use_api_login';
    const XML_PATH_CUSTOM_LOGIN = 'magejam/settings/custom_login';
    const XML_PATH_CUSTOM_PASSWORD = 'magejam/settings/custom_password';

    /**
     * User authorization according to config settings
     *
     * @param $login
     * @param $password
     * @return bool
     */
    public function auth($login, $password)
    {
        if(!Mage::getStoreConfig(self::XML_PATH_USE_AUTH)) {
            return true;
        }

        if(Mage::getStoreConfig(self::XML_PATH_USE_API_LOGIN)) {
            /* @var $user Mage_Api_Model_User */
            $user = Mage::getModel('api/user');
            if($user->authenticate($login, $password)) {
                return true;
            }
            return false;
        }

        if($login == Mage::getStoreConfig(self::XML_PATH_CUSTOM_LOGIN) && $password == Mage::getStoreConfig(self::XML_PATH_CUSTOM_PASSWORD)) {
            return true;
        }
        return false;
    }
}