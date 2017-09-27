w<?php
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


class AW_Productupdates_Model_Cron
{

    const XML_PATH_EMAIL_SENDER = 'productupdates/configuration/sender_email_identity';
    const NOTIFICATIONS_PER_LAUNCH = 5;

    public function prepareQueue()
    {
        // lock ready to run schedule        
        $schedule = $this->_lockSchedule();

        if (!$schedule->getId()) {
            return;
        }
        // get all notifications of particular type related to specific product  
        $notifications = Mage::getModel('productupdates/productupdates')
                ->getCollection()
                ->joinSubscribers()
                ->addSubscrStoreIdFilter(explode(',', $schedule->getStoreIds()))
                ->addProductFilter($schedule->getProductId());

        if ($schedule->getSendType() == AW_Productupdates_Model_Source_SubscriptionTypes::GENERAL_SUBSCRIPTION_TYPE) {
            $notifications->groupByGeneral();
        } else {
            $notifications->addTypeFilter($schedule->getSendType());
        }
        
        foreach ($notifications as $notification) {
            if ((string)$schedule->getSendType() != '1'){
                try {
                    Mage::getModel('productupdates/queue')
                        ->setProductId($schedule->getProductId())
                        ->setCustomerId($notification->getRegId())
                        ->setSubscriberId($notification->getSubscriberId())
                        ->setScheduleId($schedule->getId())
                        ->setStoreId($notification->getSubscrStoreId())
                        ->setSendType($schedule->getSendType())
                        ->setStatus(AW_Productupdates_Model_Schedule::READY)
                        ->setCreatedAt(gmdate('Y-m-d H:i:s'))
                        ->save();
                } catch (Exception $e) {
                    $this->_log($e);
                }
            }
        }

        $this->_success($schedule);
    }

    protected function _addProductTemplateData($queue)
    {
        $generalType = AW_Productupdates_Model_Source_SubscriptionTypes::GENERAL_SUBSCRIPTION_TYPE;
        if ($queue->getProductupdatesSchedule()->getSendType() == $generalType) {
            $productData = Zend_Json::decode($queue->getProductupdatesSchedule()->getAdditional());
            if (!is_array($productData)) {
                return $this;
            }
            $queue->getCatalogProduct()->setData('product_object_request', new Varien_Object($productData));
        }
        return $this;
    }

    public function sendNotifications()
    {
        for ($i = 0; $i < self::NOTIFICATIONS_PER_LAUNCH; $i++) {
            $queue = $this->_lockQueue();

            if (!$this->_validateQueue($queue)) {
                $this->_fail($queue);
                continue;
            }

            $this->_addProductTemplateData($queue);
            try {
                $this->_helper()->processEmails($queue->getSendType(), array('queue' => $queue));
                $this->_prepareForSave($queue, true);
            } catch (Exception $e) {
                return $this->_log($e)->_fail($queue);
            }

            $queue->save();
        }
        return $this;
    }

    protected function _log($e)
    {
        $this->_helper('productupdates')->log($e);
        return $this;
    }

    protected function _fail($object)
    {
        try {
            if ($object->getId()) {
                $this->_prepareForSave($object, false)->save();
            }
        } catch (Exception $e) {
            $this->_log($e);
        }
        return $this;
    }

    protected function _success($object)
    {
        try {
            if ($object->getId()) {
                $this->_prepareForSave($object, true)->save();
            }
        } catch (Exception $e) {
            $this->_log($e);
        }
        return $this;
    }

    protected function _prepareForSave($object, $success)
    {
        if ($success === true) {
            $status = AW_Productupdates_Model_Schedule::PROCESSED;
        } else if ($success === false) {
            $status = AW_Productupdates_Model_Schedule::FAILED;
        } else {
            $status = $success;
        }

        return $object->setStatus($status)->setProcessedAt(Mage::getModel('core/date')->gmtDate());
    }

    protected function _helper($type = 'productupdates/notifications')
    {
        return Mage::helper($type);
    }

    protected function _resource($type = 'productupdates/catalogrule')
    {
        return Mage::getResourceModel($type);
    }

    public function reindexAll()
    {
        $this->_resource()->updateCatalogrule()->updatePriceIndex()->updateInventoryStock();
    }

    public function reindexPrices()
    {
        $this->_resource()->updateCatalogrule()->updatePriceIndex();
    }

    public function reindexInventory()
    {
        $this->_resource()->updateInventoryStock();
    }

    public function notificationsEnabled($queue)
    {
        $allowedTypes = $this->_helper('productupdates')->getTypes(true);

        foreach ($allowedTypes as $key => $allowedType) {
            if ($queue->getSendType() == $allowedType) {
                return $this->_helper()->config("{$key}_enable", $queue->getStoreId(), true);
            }
        }

        return false;
    }
    
    protected function _lockQueue()
    {
        return Mage::getModel('productupdates/queue')->lockFirstAvailable();
    }
    
    protected function _lockSchedule()
    {
        return Mage::getModel('productupdates/schedule')->lockFirstAvailable();
    }
    
    /**
     * Varlidate queue:
     * 1. Queue exists   
     * 2. Notifications enabled
     * 3. Product is salable and visible in catalog
     * 4. Customer group is appropriate
     * @param AW_Productupdates_Model_Queue $queue
     * @return boolean
     */
    protected function _validateQueue($queue)
    {
        if (!$queue->getId()) {
            return false;
        }        
        if (!$this->notificationsEnabled($queue)) {
            return false;
        }
        if (!$queue->addAllObjects()) {
            return false;
        }
        if (!$this->_getIsSalable($queue->getCatalogProduct())) {
            return false;
        }

        $allowedVisibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );
        if (!in_array($queue->getCatalogProduct()->getVisibility(), $allowedVisibility)) {
            return false;
        }
        
        $schedule = $queue->getProductupdatesSchedule();

        if ($schedule->getSendType() == AW_Productupdates_Model_Source_SubscriptionTypes::WAITING_PRICE_CHANGE) {
            $customer = $queue->getCustomerCustomer();
            $customerGroups = explode(',', $schedule->getCustomerGroupIds());
            if ($customer) {
                if (!in_array($customer->getGroupId(), $customerGroups)) {
                    return false;
                }
            } else {
                if (!in_array(0, $customerGroups)) {
                    return false;
                }
            }
        }
 
        return true;
    }

    protected function _getIsSalable($product)
    {
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $typeInstance = $product->getTypeInstance(true);
            $salable = $typeInstance->getProduct($product)->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
            if ($salable && $typeInstance->getProduct($product)->hasData('is_salable')) {
                $salable = $typeInstance->getProduct($product)->getData('is_salable');
            }
            elseif ($salable && $typeInstance->isComposite()) {
                $salable = null;
            }

            if ($salable !== false) {
                $salable = false;
                if (!is_null($product)) {
                    $typeInstance->setStoreFilter($product->getStoreId(), $product);
                }
                $collection = $typeInstance->getUsedProductCollection($product);
                $collection->setStoreId($product->getStoreId());
                foreach ($collection as $child) {
                    if ($child->isSalable()) {
                        $salable = true;
                        break;
                    }
                }
            }
            return $salable;
        } else {
            return $product->getIsSalable();
        }
    }
}
