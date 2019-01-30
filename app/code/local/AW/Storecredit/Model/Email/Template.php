<?php

class AW_Storecredit_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    const DEFAULT_EMAIL_TEMPLATE_PATH = 'aw_storecredit_email_template';

    protected $emailTemplate;

    protected function _construct()
    {
        $this->emailTemplate = Mage::getModel('core/email_template');
        parent::_construct();
    }

    public function prepareEmailAndSend(array $variables, $store)
    {
        if (!array_key_exists('customer_id', $variables)) {
            throw new Exception("Customer ID not found. Email hasn't been sent");
        }

        $customerId = $variables['customer_id'];

        $storeCredit = Mage::getModel('aw_storecredit/storecredit')->loadByCustomerId($customerId);
        if (!$storeCredit || !$storeCredit->getId()) {
            throw new Exception("Store Credit not found. Email hasn't been sent");
        }

        if ($storeCredit->getSubscribeState() != AW_Storecredit_Model_Source_Storecredit_Subscribe_State::SUBSCRIBED_VALUE) {
            return false;
        }
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $template = Mage::helper('aw_storecredit/config')->getEmailTemplate($customer->getStoreId());
        if (!$template) {
            $template = self::DEFAULT_EMAIL_TEMPLATE_PATH;
        }
        $this->emailTemplate->setDesignConfig(array('store' => $customer->getStoreId()));
        $templateData = $this->_getEmptyTemplateData();
        $templateData['store'] = $store;
        $templateData['store_name'] = $store->getName();
        $templateData['link_to_landing_page'] = Mage::helper('aw_storecredit/url')->getLandingPageUrlForEmail($customerId, $customer->getStoreId());
        $templateData['link_to_customer_storecredit_tab'] = Mage::helper('aw_storecredit/url')->getStorecreditAccountUrlForEmail($customerId, $customer->getStoreId());

        if ($customer && $customer->getId()) {
            $templateData['customer_name'] = $customer->getName();
            $templateData['customer_email'] = $customer->getEmail();
            $templateData['customer_firstname'] = $customer->getFirstname();
        }

        $templateData['store_credit_balance_formatted'] = Mage::helper('core')->currency($storeCredit->getBalance(), true, false);
        if ($storeCredit->getBalance() > 0) {
            $templateData['store_credit_balance'] = true;
        }
        if (array_key_exists('store_credit_redeemed_from_giftcard', $variables)) {
            $templateData['store_credit_redeemed_from_giftcard'] = $variables['store_credit_redeemed_from_giftcard'];
        }

        if (array_key_exists('gift_card_code', $variables)) {
            $templateData['gift_card_code'] = $variables['gift_card_code'];
        }

        if (array_key_exists('store_credit_product_bought', $variables)) {
            $templateData['store_credit_product_bought'] = $variables['store_credit_product_bought'];
        }

        if (array_key_exists('credit_spent', $variables)) {
            $templateData['credit_spent'] = $variables['credit_spent'];
        }

        if (array_key_exists('order_increment_id', $variables)) {
            $templateData['order_increment_id'] = $variables['order_increment_id'];
        }

        if (array_key_exists('order_url', $variables)) {
            $templateData['order_url'] = $variables['order_url'];
        }

        if (array_key_exists('store_credit_added_by_admin', $variables)) {
            $templateData['store_credit_added_by_admin'] = $variables['store_credit_added_by_admin'];
        }

        if (array_key_exists('store_credit_delta_balance_formatted', $variables)) {
            $templateData['store_credit_delta_balance_formatted'] = $variables['store_credit_delta_balance_formatted'];
        }

        if (array_key_exists('store_credit_admin_comment', $variables)) {
            $templateData['store_credit_admin_comment'] = $variables['store_credit_admin_comment'];
        }

        if (array_key_exists('store_credit_has_creditmemo', $variables)) {
            $templateData['store_credit_has_creditmemo'] = $variables['store_credit_has_creditmemo'];
        }
        $subject = Mage::helper('aw_storecredit')->__('Store Credit balance update - %s', $templateData['store_name']);
        $this->emailTemplate->setTemplateSubject($subject);
        return $this->emailTemplate->sendTransactional(
            $template,
            Mage::helper('aw_storecredit/config')->getEmailSender($store),
            $templateData['customer_email'],
            $templateData['customer_name'],
            $templateData,
            $customer->getStoreId()
        );
    }

    protected function _getEmptyTemplateData()
    {
        return $templateData = array(
            'customer_name'    => '',
            'customer_email'   => '',
            'customer_firstname' => '',
            'store_credit_balance_formatted' => '',
            'store_credit_added_by_admin' => '',
            'store_credit_delta_balance_formatted' => '',
            'store_credit_admin_comment' => ''
        );
    }
}