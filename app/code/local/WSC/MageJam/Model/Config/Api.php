<?php

class WSC_MageJam_Model_Config_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Constants for config XML path
     */
    const XML_PATH_CURRENCY_BASE = 'currency/options/base';
    const XML_PATH_CURRENCY_DEFAULT = 'currency/options/default';
    const XML_PATH_CURRENCY_ALLOW = 'currency/options/allow';
    const XML_PATH_DEFAULT_COUNTRY = 'general/country/default';
    const XML_PATH_COUNTRY_ALLOW = 'general/country/allow';
    const XML_PATH_OPTIONAL_POSTCODE = 'general/country/optional_zip_countries';
    const XML_PATH_STATE_REQUIRED = 'general/region/state_required';
    const XML_PATH_DISPLAY_ALL = 'general/region/display_all';
    const XML_PATH_TIMEZONE = 'general/locale/timezone';
    const XML_PATH_LOCALE = 'general/locale/code';
    const XML_PATH_GUEST_CHECKOUT = 'checkout/options/guest_checkout';

    /**
     * @param null $store
     * @return array
     */
    public function info($store = null)
    {
        /* @var $helper WSC_MageJam_Helper_Data */
        $helper = Mage::helper('magejam');
        $storeId = $helper->getStoreId($store);
        $result = array();
        $result['currency'] = $this->getCurrency($storeId);
        $result['general'] = $this->getGeneralInfo($storeId);
        return $result;
    }

    /**
     * Retrieves currency info
     *
     * @param $storeId
     * @return array
     */
    protected function getCurrency($storeId)
    {
        $currency = array();
        $currency['base'] = Mage::getStoreConfig(self::XML_PATH_CURRENCY_BASE, $storeId);
        $currency['default'] = Mage::getStoreConfig(self::XML_PATH_CURRENCY_DEFAULT, $storeId);
        $currency['allow'] = Mage::getStoreConfig(self::XML_PATH_CURRENCY_ALLOW, $storeId);
        return $currency;
    }

    /**
     * Retrieves general info
     *
     * @param $storeId
     * @return array
     */
    protected function getGeneralInfo($storeId)
    {
        $info = array();
        $info['default_country'] = Mage::getStoreConfig(self::XML_PATH_DEFAULT_COUNTRY, $storeId);
        $info['allow_countries'] = Mage::getStoreConfig(self::XML_PATH_COUNTRY_ALLOW, $storeId);
        $info['optional_zip_countries'] = Mage::getStoreConfig(self::XML_PATH_OPTIONAL_POSTCODE, $storeId);
        $info['state_required'] =  Mage::getStoreConfig(self::XML_PATH_STATE_REQUIRED, $storeId);
        $info['display_not_required_state'] = Mage::getStoreConfig(self::XML_PATH_DISPLAY_ALL, $storeId);
        $info['timezone'] = Mage::getStoreConfig(self::XML_PATH_TIMEZONE, $storeId);
        $info['locale'] = Mage::getStoreConfig(self::XML_PATH_LOCALE, $storeId);
        $info['guest_checkout'] = Mage::getStoreConfig(self::XML_PATH_GUEST_CHECKOUT, $storeId);

        return $info;
    }
}