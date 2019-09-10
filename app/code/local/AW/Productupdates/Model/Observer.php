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


class AW_Productupdates_Model_Observer
{

    protected $_scheduleResource;
    protected $_productsResource;
    protected $_registry = array();
    protected $_linkInstance = null;

    public function prepareSending($observer)
    {
        $pars = $this->_getRequestParams();
        if (!$pars->getSend()) {
            return $this;
        }

        if (!$pars->getData('product/website_ids')) {
            if (Mage::app()->isSingleStoreMode()) {
                $product = $pars->getProduct();
                $product['website_ids'] = (array) Mage::app()->getDefaultStoreView()->getWebsite()->getId();
                $pars->setProduct($product);
            } else {
                return Mage::getSingleton('adminhtml/session')->addNotice(
                    $this->_helper()->__(
                        'Notifications have not been queued as product website visibility was not specified'
                    )
                );
            }
        }

        $storesByWebsite = Mage::getResourceModel('productupdates/productupdates')
            ->getStoresByWebsite($pars->getData('product/website_ids'), $pars->getStore())
        ;
      
        $this->_prepareSchedule(
            array($observer->getProduct()->getId()), $storesByWebsite, $pars
        );

        Mage::getSingleton('adminhtml/session')->addSuccess(
            $this->_helper()->__('Notifications have been queued.')
        );
        return $this;
    }

    public function updateStockIndex($observer)
    {
        $product = $observer->getProduct();
        if (!$product->getStockData('is_in_stock')) {
            Mage::getResourceModel('productupdates/catalogrule')->updateInventoryStockRow(
                $product->getId(),
                $product->getStockData('is_in_stock')
            );
        }
    }

    private function _getAttributeValue($attribute, $product, $store)
    {
        return Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect($attribute)
            ->setStore($store)
            ->addFieldToFilter('entity_id', array('eq' => $product))
            ->getFirstItem()
        ;
    }

    private function _prepareAttributesValue($data)
    {
        $useDefault = $data['transport']->getData('use_default');
        foreach ($data['type'] as $type) {
            if ($useDefault) {
                if (in_array($type, $useDefault)) {
                    $default = $this->_getAttributeValue($type, $data['product'], 0);
                    $title = trim($default->getData($type));
                } else {
                    $title = trim($data['transport']->getData("product/{$type}"));
                }
            } else {
                $default = $this->_getAttributeValue($type, $data['product'], $data['store']);
                $title = trim($default->getData($type));
            }

            if (!empty($title)) {
                $data['transport']->setData($type, $title);
                continue;
            }
            $merge = str_replace("_", "", $type);
            $data['transport']->setData($type, $this->_helper()->config("configuration/{$merge}", $data['store']));
        }
    }

    protected function _prepareSchedule(array $products, array $websites, Varien_Object $pars)
    {
        $gmt = Mage::getModel('core/date')->gmtDate();
        foreach ($products as $product) {
            foreach ($websites as $website => $stores) {
                foreach (explode(',', $stores) as $store) {

                    $transport = clone $pars;
                    /* if attribute uses default value get from request */
                    $this->_prepareAttributesValue(
                        array(
                            'transport' => $transport,
                            'product' => $product,
                            'store' => $store,
                            'type' => array('notification_title', 'notification_text')
                        )
                    );
                    Mage::getModel('productupdates/schedule')
                        ->setWebsiteId($website)
                        ->setSendType(AW_Productupdates_Model_Source_SubscriptionTypes::WAITING_STOCK_CHANGE)
                        ->setStatus(AW_Productupdates_Model_Schedule::READY)
                        ->setSource(AW_Productupdates_Model_Source_Sourcefrom::PRODUCT_CHANGE)
                        ->setCreatedAt($gmt)
                        ->setStoreIds($store)
                        ->setProductId($product)
                        ->setAdditional($transport->toJson())
                        ->save()
                    ;
                }
            }
        }
    }

    protected function _getRequestParams()
    {
        $params = $this->_helper()->wrapRequestParams();
        $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($params->getId());
        if (!empty($parentIds) && $parentIds[0]) {
            $params->setParentProduct($parentIds[0]);
        }
        $params->addData($this->_helper()->wrapRequestPost()->getData());
        return $params;
    }

    protected function _helper($type = 'productupdates')
    {
        return Mage::helper($type);
    }

    public function customerSaveAfter($event)
    {
        $customer = $event->getCustomer();
        $customerInfo = array(
            'fullname'  => $customer->getName(),
            'email'     => $customer->getEmail(),
            'regId'     => $customer->getEntityId()
        );

        Mage::getModel('productupdates/subscribers')->getCollection()
            ->updateSubscriber($customerInfo)
        ;
    }

    public function updateCatalogRules($observer)
    {
        Mage::getModel('productupdates/cron')->reindexPrices();
    }
    
   
    /**
     * Adds subscription links at the category pages
     * Skip product pages 
     */
    public function prepareRewrites()
    {
        if (!Mage::getStoreConfig('advanced/modules_disable_output/AW_Productupdates')) {
            $node = Mage::getConfig()->getNode('global/blocks/catalog/rewrite');
            $dnodes = Mage::getConfig()->getNode('global/blocks/catalog/drewrite');

            foreach ($dnodes->children() as $dnode) {
                $node->appendChild($dnode);
            }

            $adminhtml = Mage::getConfig()->getNode('global/blocks/adminhtml/rewrite');
            $dadminhtml = Mage::getConfig()->getNode('global/blocks/adminhtml/drewrite');

            foreach ($dadminhtml->children() as $dnode) {
                $adminhtml->appendChild($dnode);
            }
        }
    }

    public function blockAbstractToHtmlAfter($observer)
    {
        if (Mage::registry('current_product')
            || !$this->_helper()->config('configuration/categories', $this->_helper()->getStore())) {
            return $this;
        }
        if ($observer->getBlock() instanceof Mage_Catalog_Block_Product_Price) {
            if (!$this->_getLinkInstance()) {
                return $this;
            }
            $observer->getTransport()->setHtml(
                $observer->getTransport()->getHtml()
                . $this->_getLinkInstance()->setData('product', $observer->getBlock()->getProduct())->toHtml()
            );
        }
        return $this;
    }

    protected function _getLinkInstance()
    {
        if (null === $this->_linkInstance) {
            $this->_linkInstance = Mage::app()->getLayout()->createBlock(
                'productupdates/subscribelink', 'aw_pun_subscribe_link'
            );
        }
        return $this->_linkInstance;
    }

}