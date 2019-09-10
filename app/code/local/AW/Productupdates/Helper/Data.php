<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Productupdates
 * @version    2.1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Productupdates_Helper_Data extends Mage_Core_Helper_Abstract
{
    const ROOT_CONFIG = 'productupdates';

    public static $store = null;

    public function encrypt(array $data)
    {
        foreach ($data as $key => &$value) {
            if (substr($key, 0, 1) == '_') {
                continue;
            }
            $value = $this->urlEncode(Mage::helper('core')->encrypt($value));
        }
        return $data;
    }

    public function decrypt(array $data)
    {
        foreach ($data as $key => &$value) {
            if (substr($key, 0, 1) == '_') {
                continue;
            }
            $value = Mage::helper('core')->decrypt($this->urlDecode($value));
        }
        return $data;
    }

    public function log($e)
    {
        if (is_object($e)) {
            Mage::log($e->getMessage(), null, 'AW_ProductupdatesNotifications', true);
        } elseif (is_array($e)) {
            foreach ($e as $msg) {
                Mage::log($msg, null, 'AW_ProductupdatesNotifications', true);
            }
        } else {
            Mage::log($e, null, 'AW_ProductupdatesNotifications', true);
        }
    }

    public function getTypes($general = false)
    {
        $types = Mage::getSingleton('productupdates/source_subscriptionTypes')->getAllowedTypes();
        if (!$general) {
            foreach ($types as $key => $type) {
                if ($type == AW_Productupdates_Model_Source_SubscriptionTypes::GENERAL_SUBSCRIPTION_TYPE) {
                    unset($types[$key]);
                }
            }
        }
        return $types;
    }

    public function extensionEnabled($extensionName)
    {
        $modules = (array) Mage::getConfig()->getNode('modules')->children();
        if (!isset($modules[$extensionName])
                || $modules[$extensionName]->descend('active')->asArray() == 'false'
                || Mage::getStoreConfig('advanced/modules_disable_output/' . $extensionName)
        ) {
            return false;
        }
        return true;
    }

    public function config($path, $store)
    {
        return Mage::getStoreConfig(self::ROOT_CONFIG . "/{$path}", $store);
    }

    public function getStore()
    {
        if (self::$store) {
            return self::$store;
        }
        return Mage::app()->getStore()->getId();
    }

    /**
     * Get subscription type:
     * 1. From request
     * 2. From registry
     * @param boolean $link = false
     * @return int
     */
    public function getSubscriptionType($link = false)
    {
        if ($link && $link instanceof AW_Productupdates_Block_Subscribelink) {
            if ($link->getProduct()) {
                return $link->getProduct()->getIsSalable();
            }
        }

        $type = (int) Mage::app()->getRequest()->getParam('type', false);
        $allowedTypes = Mage::getSingleton('productupdates/source_subscriptionTypes')->getAllowedTypes();
        if ($type && in_array($type, $allowedTypes)) {
            return $type;
        }

        if (!$this->_product() || !$this->_product()->getIsSalable()) {
            return AW_Productupdates_Model_Source_SubscriptionTypes::WAITING_STOCK_CHANGE;
        }
        return AW_Productupdates_Model_Source_SubscriptionTypes::WAITING_PRICE_CHANGE;
    }

    public function getCustomer()
    {
        $session = Mage::getSingleton('customer/session');
        if ($session->isLoggedIn()) {
            return $session->getCustomer();
        }
        return null;
    }

    public function getCustomerIdentity()
    {
        if ($this->getCustomer()) {
            return $this->getCustomer()->getId();
        }
        return null;
    }

    public function wrapRequestParams()
    {
        return new Varien_Object(Mage::app()->getRequest()->getParams());
    }

    public function wrapRequestPost()
    {
        return new Varien_Object(Mage::app()->getRequest()->getPost());
    }

    protected function _product()
    {
        return Mage::registry('current_product');
    }

}