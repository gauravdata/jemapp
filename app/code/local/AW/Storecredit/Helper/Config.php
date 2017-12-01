<?php
class AW_Storecredit_Helper_Config extends Mage_Core_Helper_Abstract
{
    const GENERAL_MODULE_ENABLED = 'aw_storecredit/general/enabled';
    const GENERAL_REFUND_AUTOMATICALLY = 'aw_storecredit/general/refund_automatically';
    const GENERAL_CMS_PAGE_IN_CUSTOMER_AREA = 'aw_storecredit/general/cms_page';
    const GENERAL_ADD_LINK_IN_TOPLINKS = 'aw_storecredit/general/display_toplink';
    const GENERAL_DISPLAY_BALANCE_IN_TOPLINKS = 'aw_storecredit/general/display_customer_balance';

    const EMAIL_SENDER = 'aw_storecredit/email/sender';
    const EMAIL_LANDING_PAGE = 'aw_storecredit/email/landing_page';
    const EMAIL_CUSTOMER_AUTO_SUBSCRIBED = 'aw_storecredit/email/subscribe_automatically';
    const EMAIL_CUSTOMER_AUTO_LOGIN = 'aw_storecredit/email/autologin_enabled';
    const EMAIL_TEMPLATE = 'aw_storecredit/email/template';

    public function isModuleEnabled($store = null)
    {
        return Mage::getStoreConfig(self::GENERAL_MODULE_ENABLED, $store);
    }

    public function isAutomaticallyStoreCreditRefund($store = null)
    {
        return Mage::getStoreConfig(self::GENERAL_REFUND_AUTOMATICALLY, $store);
    }

    public function getCmsPageInCustomerArea($store = null)
    {
        return Mage::getStoreConfig(self::GENERAL_CMS_PAGE_IN_CUSTOMER_AREA, $store);
    }

    public function isDisplayInToplinks($store = null)
    {
        return Mage::getStoreConfig(self::GENERAL_ADD_LINK_IN_TOPLINKS, $store);
    }

    public function isDisplayBalanceInToplinks($store = null)
    {
        return Mage::getStoreConfig(self::GENERAL_DISPLAY_BALANCE_IN_TOPLINKS, $store);
    }

    public function getEmailSender($store = null)
    {
        return Mage::getStoreConfig(self::EMAIL_SENDER, $store);
    }

    public function getEmailTemplate($store = null)
    {
        return Mage::getStoreConfig(self::EMAIL_TEMPLATE, $store);
    }

    public function getLandingPageUrl($store = null) {
        return Mage::app()->getStore($store)->getBaseUrl() . Mage::getStoreConfig(self::EMAIL_LANDING_PAGE, $store);
    }

    public function isAutoSubscribedCustomers($store = null)
    {
        return Mage::getStoreConfig(self::EMAIL_CUSTOMER_AUTO_SUBSCRIBED, $store);
    }

    public function isAutologinEnabled($store = null)
    {
        return Mage::getStoreConfig(self::EMAIL_CUSTOMER_AUTO_LOGIN, $store);
    }
}