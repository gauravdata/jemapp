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


class AW_Productupdates_Helper_Notifications extends Mage_Core_Helper_Abstract
{

    const ADMIN_EMAIL = 'administrator_email';
    const NOTIFICATIONS_ROOT = 'productupdates/notifications/';
 
    /* email notifications constants */
    const NOTIF_PRICE_EMAILS = 'productupdates/notifications/price_changed_enable';
    const NOTIF_STOCK_EMAILS = 'productupdates/notifications/stock_changed_enable';

    public function send($template, $data)
    {       
        Mage::getSingleton('core/translate')->setTranslateInline(false);
        
        $product = $data['queue']->getCatalogProduct();   
        
        /* back compatibility with PUN! */
        $product->setPrice($product->getFormatedPrice());
        $this->_addImageVars($product);
        /* */       
        Mage::getModel('productupdates/email_template')
            ->setDesignConfig(array('area' => 'frontend', 'store' => $data['queue']->getStoreId()))
            ->setQueue($data['queue'])
            ->sendTransactional(
                Mage::getStoreConfig(self::NOTIFICATIONS_ROOT . $template, $data['queue']->getProductupdatesSchedule()->getData('store_ids')),
                $this->_getSenderName($data['queue']->getStoreId()),
                $data['queue']->getProductupdatesSubscribers()->getEmail(),
                $data['queue']->getProductupdatesSubscribers()->getFullname(),
                array(
                    'queue' => $data['queue'],
                    'customer' => $data['queue']->getCustomerCustomer(),
                    'product' => $data['queue']->getCatalogProduct(),
                    'subscribers' => $data['queue']->getProductupdatesSubscribers(),
                    'subscriber'  => $data['queue']->getProductupdatesSubscribers()->getFullname(),
                    'schedule' => $data['queue']->getProductupdatesSchedule()
                ) + $this->_unsubscribe($data['queue']),
                $data['queue']->getStoreId()
            )
        ;
    }

    public function config($path, $store, $addRoot = false)
    {
        if ($addRoot) {
            $path = self::NOTIFICATIONS_ROOT . $path;
        }
        return Mage::getStoreConfig($path, $store);
    }

    public function processEmails($sendType, $params)
    {
        $types = Mage::getSingleton('productupdates/source_subscriptionTypes')->getAllowedTypes();
        foreach ($types as $template => $type) {
            if ($type == $sendType) {
                var_dump();
                $this->send($template, $params);
                return true;
            }
        }
        return false;
    }

    private function _getSenderName($storeId = 0)
    {
        $senderEmailIdentity = $this->config('productupdates/configuration/sender_email_identity', $storeId);
        return array(
            'name' => $this->config("trans_email/ident_{$senderEmailIdentity}/name", $storeId),
            'email' => $this->config("trans_email/ident_{$senderEmailIdentity}/email", $storeId)
        );
    }
    
    private function _addImageVars($product)
    {
        if ($product->getAwFpEnabled()) {
            $image = Mage::helper('catalog/image')->init($product, 'image', $product->getAwFpImage());
        } else {
            $image = Mage::helper('catalog/image')->init($product, 'image');
        }
        $product->setImage("<img src='{$image}' />");
    }
    
    private function _unsubscribe($queue)
    {
        $unsubscribe = Mage::helper('productupdates')->encrypt(
            array(
                'key' => $queue->getProductupdatesSubscribers()->getId(),
                '_store' => $queue->getStoreId(),
                'store' => $queue->getStoreId(),
                'type' => $queue->getSendType(),
                'prod' => $queue->getProductId(),
                'catalog_prod' => $queue->getCatalogProduct()->getId(),
            )
        );
        $unsubscribeAll = Mage::helper('productupdates')->encrypt(
            array(
                'key' => $queue->getProductupdatesSubscribers()->getId(),
                'prod' => $queue->getProductId(),
                '_store' => $queue->getStoreId(),
                'catalog_prod' => $queue->getCatalogProduct()->getId(),
            )
        );
        return array(
            'unsubscribe_link' => Mage::getUrl("productupdates/index/unsubscribe", $unsubscribe),
            'unsubscribe_all_link' => Mage::getUrl("productupdates/index/unsubscribeall", $unsubscribeAll),
        );
    }
}