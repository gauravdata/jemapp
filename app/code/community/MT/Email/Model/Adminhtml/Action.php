<?php

class MT_Email_Model_Adminhtml_Action
{

    private $__systemConfigMap = array(
        array(
            'path' => Mage_Sales_Model_Order::XML_PATH_EMAIL_GUEST_TEMPLATE,
            'template_code' => 'mtemail_default_sales_order_new_guest'
        ),

        array(
            'path' => Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_sales_order_new'
        ),

        array(
            'path' => Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE,
            'template_code' => 'mtemail_default_sales_order_update_guest'
        ),

        array(
            'path' => Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_sales_order_update'
        ),

        array(
            'path' =>  Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_sales_invoice_new'
        ),

        array(
            'path' => Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_GUEST_TEMPLATE,
            'template_code' => 'mtemail_default_sales_invoice_new_guest'
        ),

        array(
            'path' => Mage_Sales_Model_Order_Invoice::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE,
            'template_code' => 'mtemail_default_sales_invoice_update_guest'
        ),

        array(
            'path' => Mage_Sales_Model_Order_Invoice::XML_PATH_UPDATE_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_sales_invoice_update'
        ),

        array(
            'path' =>  Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_sales_creditmemo_new'
        ),

        array(
            'path' => Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_GUEST_TEMPLATE,
            'template_code' => 'mtemail_default_sales_creditmemo_new_guest'
        ),

        array(
            'path' => Mage_Sales_Model_Order_Creditmemo::XML_PATH_UPDATE_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_sales_creditmemo_update'
        ),

        array(
            'path' => Mage_Sales_Model_Order_Creditmemo::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE,
            'template_code' => 'mtemail_default_sales_creditmemo_update_guest'
        ),

        array(
            'path' =>  Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_sales_shipment_new'
        ),

        array(
            'path' => Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_GUEST_TEMPLATE,
            'template_code' => 'mtemail_default_sales_shipment_new_guest'
        ),

        array(
            'path' => Mage_Sales_Model_Order_Shipment::XML_PATH_UPDATE_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_sales_shipment_update'
        ),

        array(
            'path' => Mage_Sales_Model_Order_Shipment::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE,
            'template_code' => 'mtemail_default_sales_shipment_update_guest'
        ),

        array(
            'path' => Mage_Customer_Model_Customer::XML_PATH_REGISTER_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_account_new'
        ),

        array(
            'path' => Mage_Customer_Model_Customer::XML_PATH_CONFIRM_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_account_new_confirmation'
        ),

        array(
            'path' => Mage_Customer_Model_Customer::XML_PATH_CONFIRMED_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_account_new_confirmed'
        ),

        array(
            'path' => Mage_Customer_Model_Customer::XML_PATH_FORGOT_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_account_password_reset_confirmation'
        ),

        array(
            'path' => Mage_Newsletter_Model_Subscriber::XML_PATH_CONFIRM_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_newsletter_subscription_confirm'
        ),

        array(
            'path' => Mage_Newsletter_Model_Subscriber::XML_PATH_SUCCESS_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_newsletter_subscr_success'
        ),

        array(
            'path' => Mage_Newsletter_Model_Subscriber::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_newsletter_unsubscr_success'
        ),

        array(
            'path' =>  Mage_Customer_Model_Customer::XML_PATH_REMIND_EMAIL_TEMPLATE,
            'template_code' => 'mtemail_default_customer_password_remind'
        ),

        array(
            'path' => 'wishlist/email/email_template',
            'template_code' => 'mtemail_default_wishlist_share'
        ),

        array(
            'path' => 'sendfriend/email/template',
            'template_code' => 'mtemail_default_product_share'
        )
    );

    public function install($params)
    {
        $store = $this->getStore($params);
        $storeId = $store->getId();
        $locale = strtolower(Mage::getStoreConfig('general/locale/code', $storeId));
        $templateList = Mage::getConfig()->getNode('global/template/email')->asArray();
        $demoVars = Mage::helper('mtemail')->getDemoVars($storeId);

        foreach ($templateList as $code => $template) {
            if (isset($template['@']['module']) && $template['@']['module'] == 'mtemail') {
                $templateCollection = Mage::getModel('core/email_template')->getCollection()
                    ->addFieldToFilter('orig_template_code', $code)
                    ->addFieldToFilter('store_id', $storeId);
                $template = $templateCollection->getFirstItem();
                if (!$template->getData('template_id')) {
                    $template->loadDefault($code);

                    $content = $template->getTemplateText();
                    $modelTemplate = Mage::getModel('mtemail/template');
                    $content = $modelTemplate->removePlainText($content);
                    $newContent = $modelTemplate->assignUniqueBlockId($content);

                    $data = $template->getData();
                    unset($data['template_id']);
                    $data['template_text'] = $newContent;

                    $templateName =  str_replace('Admin', 'Default', ucfirst($store->getCode())).' > '.$code;

                    $newTemplate = Mage::getModel('core/email_template');
                    $newTemplate->setData($data);
                    $newTemplate->setTemplateCode($templateName);
                    $newTemplate->setOrigTemplateCode($code);
                    $newTemplate->setIsMtemail(1);
                    $newTemplate->setStoreId($storeId);
                    $newTemplate->setLocale($locale);
                    $newTemplate->save();

                    if ($newTemplate->getTemplateId()) {
                        $blockList = Mage::helper('mtemail')->parseBlockList($newTemplate);
                        $modelTemplate->updateDefaultPrivateVars($newTemplate, $blockList, true);
                        $modelTemplate->updateDefaultPublicVars($newTemplate, $blockList);
                        $modelTemplate->updatePublicVars($newTemplate, $blockList);

                        $newTemplate->getProcessedTemplate($demoVars);
                    } else {
                        throw new Exception('Can not crate template');
                    }
                }
            }
        }
    }


    public function delete($params)
    {
        $store = $this->getStore($params);
        $storeId = $store->getId();

        $templateCollection = Mage::getModel('core/email_template')->getCollection()
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('is_mtemail', 1);

        if ($templateCollection->count() > 0) {
            foreach ($templateCollection as $template) {
                $templateId = $template->getId();
                $this->deleteVars($templateId);
                $template->delete();
            }
        }
    }

    public function deleteVars($templateId)
    {
        $collection = Mage::getModel('mtemail/var')->getCollection()
            ->addFieldToFilter('template_id', $templateId);
        if ($collection->count() > 0) {
            foreach ($collection as $var) {
                $var->delete();
            }
        }

        return true;
    }

    public function replace($params)
    {
        $store = $this->getStore($params);
        $storeCode = $store->getCode();
        $storeId = $store->getId();

        $configModel = new Mage_Core_Model_Config();

        if (Mage::getStoreConfig('mtemail/general/is_active', $storeId)) {
            foreach ($this->__systemConfigMap as $config) {
                $templateCollection = Mage::getModel('core/email_template')->getCollection()
                    ->addFieldToFilter('store_id', $storeId)
                    ->addFieldToFilter('orig_template_code', $config['template_code']);

                $template = $templateCollection->getFirstItem();

                if (!$template->getData('template_id')) {
                    continue;
                }

                $defaultValue = Mage::getStoreConfig($config['path'], $storeId);
                $scope = 'default';
                if ($storeId != 0) {
                    $scope = 'stores';
                }

                $configModel->saveConfig($config['path'], $template->getTemplateId(), $scope, $storeId);

                if (substr_count($defaultValue, 'mtemail_') == 0) {
                    $configModel->saveConfig('mtemail/default_value/'.$config['path'], $defaultValue,  $scope, $storeId);
                }
            }
        }

    }

    public function revertConfig($params)
    {
        $store = $this->getStore($params);
        $storeId = $store->getId();
        $storeCode = $store->getCode();

        $configModel = new Mage_Core_Model_Config();
        foreach ($this->__systemConfigMap as $config) {
            $defaultValue = Mage::getStoreConfig('mtemail/default_value/'.$config['path'], $storeId);


            if ($defaultValue != '') {
                $scope = 'default';
                if ($storeId != 0) {
                    $scope = 'stores';
                }

                $configModel->saveConfig($config['path'], $defaultValue, $scope, $storeId);
            }
        }
    }

    /**
     * Returns current store
     *
     * @param $params
     * @return Mage_Core_Model_Store
     */
    public function getStore($params)
    {
        if (isset($params['store'])) {
            $store = Mage::app()->getStore($params['store']);
        } else {
            $store = Mage::app()->getStore(0);
        }
        return $store;
    }
}