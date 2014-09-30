<?php

class WSC_MageJam_Model_Config_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Constants for config XML path
     */
    const XML_PATH_CURRENCY_BASE = 'currency/options/base';
    const XML_PATH_CURRENCY_DEFAULT = 'currency/options/default';
    const XML_PATH_CURRENCY_ALLOW = 'currency/options/allow';

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
        return $result;
    }

    /**
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
}