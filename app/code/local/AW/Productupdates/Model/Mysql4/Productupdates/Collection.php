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


class AW_Productupdates_Model_Mysql4_Productupdates_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productupdates/productupdates');
    }

    public function getSubscribersList()
    {        
        $eavEntity = Mage::getModel('eav/entity')->setType('catalog_product');        
        $attribute = $eavEntity->getAttribute('name');
        $this->getSelect()
            ->from(
                '',
                array(
                    'subscriberid' => 'main_table.subscriber_id',
                    'fullname',
                    'email',
                    'reg_id',
                    'productsstr' => new Zend_Db_Expr('GROUP_CONCAT(value SEPARATOR "\<br /\>")')
                )
            )
            ->joinInner(array('b' => $this->getTable('subscribers')), 'main_table.subscriber_id = b.subscriber_id')
            ->joinInner(array('c' => $attribute->getBackend()->getTable()), 'main_table.product_id = c.entity_id')
            ->where('c.attribute_id = ?', $attribute->getAttributeId())
            ->where('c.entity_type_id = ?', $eavEntity->getTypeId())
            ->where('store_id = 0')
            ->group('email');
        return $this;
    }
    
    public function getNotificationByQueue($queue)
    {
       return $this->addProductFilter($queue->getProductId())
             ->addTypeFilter($queue->getSendType())
             ->addSubscrStoreIdFilter($queue->getStoreId())
             ->addSubscriberFilter($queue->getSubscriberId())
             ->getFirstItem()
       ;
    }
    
    public function joinSubscribers()
    {
        $this->getSelect()
            ->join(
                array('sub_table' => $this->getTable('productupdates/subscribers')),
                'main_table.subscriber_id = sub_table.subscriber_id',
                array('*')
            )
        ;
        return $this;
    }
    
    public function addProductFilter($products)
    {
        if (!is_array($products)) {
            $products = array($products);
        }
        $this->getSelect()->where('main_table.product_id IN (?) OR main_table.parent IN (?)', $products);
        return $this;
    }
    
    public function addSubscriberFilter($subscribers)
    {
        if (!is_array($subscribers)) {
            $subscribers = array($subscribers);
        }
        $this->getSelect()->where('main_table.subscriber_id IN (?)', $subscribers);
        return $this;
    }
    
    public function groupByGeneral()
    {
        $this->getSelect()->group(
            array('main_table.product_id', 'main_table.subscriber_id', 'main_table.subscr_store_id')
        );
        return $this;
    }
    
    public function addTypeFilter($type)
    {
        if (!is_array($type)) {
            $type = array($type);
        }
        $this->getSelect()->where('main_table.subscription_type IN (?)', $type);
        return $this;
    }
    
    public function addSubscrStoreIdFilter($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }
        $this->getSelect()->where('main_table.subscr_store_id IN (?)', $store);
        return $this;
    }
 
    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            $sql = $this->getSelectCountSql();
            $this->_totalRecords = count($this->getConnection()->fetchAll($sql, $this->_bindParams));
        }
        return intval($this->_totalRecords);
    }

    public function getSelectCountSql()
    {
        /* Covers original bug in Varien_Data_Collection_Db */
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        //$countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);

        $countSelect->from('', 'COUNT(*)');
        return $countSelect;
    }
    
    /**
     * @param Varien_Object
     *
     * @return AW_Productupdates_Model_Productupdates
     */    
    public function getActiveSubscription(Varien_Object $model)
    {
        $this->getSelect()
            ->where('main_table.product_id = ?', $model->getProductId())
            ->where('main_table.subscriber_id = ?', $model->getSubscriberId())
            ->where('main_table.subscr_store_id IN(?)', array($model->getWebsiteStores()))
        ;
        return $this->getFirstItem();
    }

    public function addProductstrFilter($value)
    {
        $this->getSelect()->having("`productsstr` LIKE '%{$value}%'");
        return $this;
    }

    public function addSubsIdFilter($fromTo)
    {
        if (isset($fromTo['from'])) {
            $this->getSelect()->where("main_table.subscriber_id >= '{$fromTo['from']}'");
        }
        if (isset($fromTo['to'])) {
            $this->getSelect()->where("main_table.subscriber_id <= '{$fromTo['to']}'");
        }
        return $this;
    }
}