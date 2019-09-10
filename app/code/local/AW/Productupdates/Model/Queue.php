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


class AW_Productupdates_Model_Queue extends AW_Productupdates_Model_Schedule
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('productupdates/queue');
    }

    public function addAllObjects()
    {       
        $customerId = $this->getCustomerId();
        if (!$customerId) {
            $customerId = AW_Productupdates_Model_Schedule::SKIP_FLAG;
        }
        $result = $this->_loadRelated(
            array(
                'productupdates/subscribers' => $this->getSubscriberId(),
                'productupdates/schedule' => $this->getScheduleId(),
                'customer/customer' => $customerId,
                'catalog/product' => $this->getProductId()
            )
        );

        if ($result) {
            $schedule = $this->getProductupdatesSchedule();
            if ($schedule->getAdditional()) {
                $itemData = Zend_Json::decode($schedule->getAdditional());                
                if ($itemData && is_array($itemData)) {
                    if (isset($itemData['parent_product'])) {
                        $parentId = $itemData['parent_product'];
                    } else {
                        $item = array_shift($itemData);
                        if (is_array($item) && isset($item['parent']) && $item['parent']) {
                            $parentId = $item['parent'];
                        }
                    }
                    if (isset($parentId)) {
                        $currentPrice = $this->getCatalogProduct()->getAwPreparedPrice();
                        $this->setCatalogProduct(
                            Mage::getModel('catalog/product')->setStoreId($this->getStoreId())->load($parentId)
                        );
                        if (isset($item['entity_id']) && $item['entity_id'] == $parentId) {
                            $schedule->_calculateProductFinalPrice($this->getCatalogProduct());
                        } else {
                            $this->getCatalogProduct()->setAwPreparedPrice($currentPrice);
                        }
                        $this->_addConfigurationToProduct();
                    }
                }
            }
        } 
      
        return $this;
    }

    protected function _addConfigurationToProduct()
    {
        $notification = Mage::getModel('productupdates/productupdates')
            ->getCollection()
            ->getNotificationByQueue($this)
        ;
        if (!$notification->getId()) {
            return $this;
        }
        try {
            $additional = Zend_Json::decode($notification->getAdditional());
        } catch (Exception $e) {
            return Mage::helper('productupdates')->log($e);
        }

        $eavEntity = $eavAttribute = Mage::getModel('eav/entity')->setType('catalog_product');
        if (!isset($additional['super_attribute'])) {
            return $this;
        }

        $conf = null;
        foreach ($additional['super_attribute'] as $key => $value) {
            $attribute = $eavEntity->getAttribute($key);
            if (!$attribute->getId()) {
                continue;
            }
            $conf .= "{$attribute->getStoreLabel($this->getStoreId())} ";
            $conf .= "- {$attribute->getSource()->getOptionText($value)} ";
        }
        $this->getCatalogProduct()->setData('additional_configuration', $conf);
        return $this;
    }

}