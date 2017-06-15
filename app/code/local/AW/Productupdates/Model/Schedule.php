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


class AW_Productupdates_Model_Schedule extends Mage_Core_Model_Abstract
{

    const READY = 1;
    const SUSPENDED = 2;
    const PROGRESS = 3;
    const PROCESSED = 4;
    const FAILED = 5;
    
    const SKIP_FLAG = 'skip';

    public function _construct()
    {
        parent::_construct();
        $this->_init('productupdates/schedule');
    }
   
    /**
     * Lock record during cron launch consider multipale instances
     * @return obj
     */
    public function lockFirstAvailable()
    {
        $unique = time() . mt_rand();
        // use direct pdo query as with resource it's impossible to spesify limit on update statements
        $this->getResource()->preparedQuery(
            "UPDATE {$this->getResource()->getMainTable()} set locked_by = ?, `status` = ?  "
            . "WHERE locked_by IS NULL ORDER BY `schedule_id` ASC LIMIT 1",
            array($unique, AW_Productupdates_Model_Schedule::READY)
        );
        return $this->load($unique, 'locked_by');
    } 

    protected function _loadRelated($types)
    {
        if (!is_array($types)) {
            $types = array($types);
        }
        foreach ($types as $modelKey => $id) {
            if ($id == self::SKIP_FLAG) {
                continue;
            }

            $model = Mage::getModel($modelKey);
            if (!$model) {
                return null;
            }

            $model->setStoreId($this->getStoreId())->load($id);
            if (!$model->getId()) {
                return null;
            }
            $this->setData(str_replace("/", "_", $modelKey), $model);
        }
        
        $customer = $this->getCustomerCustomer();
        $product = $this->getCatalogProduct();
        $product->setCustomerGroupId($customer ? $customer->getGroupId() : 0);
        $this->_calculateProductFinalPrice($product);
        return true;
    }
    
    protected function _calculateProductFinalPrice($product)
    {
        switch ($product->getTypeId()) {
            case 'giftcard':
                $product->setPrice($product->getPriceModel()->getMinAmount($product));
                break;
            case 'bundle':
                $this->_getMinimalPrice($product);
                break;
            default:
                break;
        }

        $product->setAwPreparedPrice(
            Mage::app()->getStore($this->getStoreId())->convertPrice($product->getFinalPrice(), true)
        );
    }
    
    private function _getMinimalPrice($product)
    {
        $schedule = $this->getProductupdatesSchedule();
        if ($schedule->getAdditional()) {
            try {
                $itemData = Zend_Json::decode($schedule->getAdditional());
            } catch(Exception $e) {
                Mage::helper('productupdates')->log($e);
                return $this;
            }
            if ($itemData && is_array($itemData)) {
                foreach ($itemData as $item) {
                    if (isset($item['customer_group_id'])
                        && ($item['customer_group_id'] == $product->getCustomerGroupId())) {
                        $product->setFinalPrice(max($item['final_price'], $item['min_price']));
                    }
                }
            }
        }
        return $this;
    }

}