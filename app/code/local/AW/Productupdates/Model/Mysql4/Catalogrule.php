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


class AW_Productupdates_Model_Mysql4_Catalogrule extends AW_Productupdates_Model_Mysql4_Schedule
{

    protected $_priceIndex;
    protected $_stockIndex;
    protected $_rulesIndex;
    protected $_priceIndexAlias;
    protected $_stockIndexAlias;
    protected $_rulesIndexAlias;
    protected $_storesByWebsite;
    protected $_notifications;
    protected $_productEntity;

    public function _construct()
    {
        $this->_init('productupdates/catalogrule', 'rule_product_price_id');
        $this->_priceIndex = $this->getTable('catalog/product_index_price');
        $this->_priceIndexAlias = $this->getTable('productupdates/priceindex');
        $this->_stockIndex = $this->getTable('cataloginventory/stock_status');
        $this->_stockIndexAlias = $this->getTable('productupdates/inventoryindex');
        $this->_rulesIndex = $this->getTable('catalogrule/rule_product_price');
        $this->_rulesIndexAlias = $this->getTable('productupdates/catalogrule');
        $this->_notifications = $this->getTable('productupdates/productupdates');
        $this->_productEntity = $this->getTable('catalog/product');
        $this->_storesByWebsite = Mage::getResourceModel('productupdates/productupdates')->getStoresByWebsite();
       
    }

    public function updateCatalogRule()
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('ri' => $this->_rulesIndex, array('*')))
            ->joinLeft(
                array('ra' => $this->_rulesIndexAlias),
                'ri.customer_group_id = ra.customer_group_id AND ri.product_id = ra.product_id '
                . 'AND ri.website_id = ra.website_id AND ri.rule_date = ra.rule_date',
                array('old_price' => 'rule_price')
            )
            ->joinLeft(
                array('pi' => $this->_priceIndex),
                'ri.product_id = pi.entity_id '
                . 'AND ri.customer_group_id = pi.customer_group_id '
                . 'AND ri.website_id = pi.website_id',
                array('final_price')
            )
            ->where('ri.rule_price != ra.rule_price OR ra.rule_product_price_id IS NULL')
            ->where(
                'ri.latest_start_date <= Curdate() '
                . 'AND (ri.earliest_end_date IS NULL '
                . 'OR ri.earliest_end_date >= ri.latest_start_date)'
            )
            ->group(array('ri.website_id', 'ri.customer_group_id', 'ri.product_id'))
            ->order(array('ri.product_id', 'ri.website_id'))
        ;
     
        $this->_joinNotifications(
            $select, 'ri.product_id', AW_Productupdates_Model_Source_SubscriptionTypes::WAITING_PRICE_CHANGE
        );
        $update = $this->_getReadAdapter()->fetchAll($select);
        if (!empty($update)) {
            $this->_updateSchedule(
                $update,
                array(
                    'type' => AW_Productupdates_Model_Source_SubscriptionTypes::WAITING_PRICE_CHANGE,
                    'source' => AW_Productupdates_Model_Source_Sourcefrom::CATALOG_PRICE_RULE
                )
            );
        }
        $this->_reindexData(array('index' => $this->_rulesIndex, 'alias' => $this->_rulesIndexAlias));
        return $this;
    }

    public function updatePriceIndex()
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('pi' => $this->_priceIndex), array('*', 'product_id' => 'entity_id'))
            ->join(
                array('entity' => $this->_productEntity), 'pi.entity_id = entity.entity_id', array('type_id')
            )
            ->joinLeft(
                array('piAlias' => $this->_priceIndexAlias),
                'pi.customer_group_id = piAlias.customer_group_id '
                . 'AND pi.entity_id = piAlias.entity_id '
                . 'AND pi.website_id = piAlias.website_id',
                array('aliasPrice' => 'piAlias.final_price')
            )
            ->join(
                array('inv' => $this->_stockIndex),
                'pi.entity_id = inv.product_id AND pi.website_id = inv.website_id AND inv.stock_status = 1',
                array()
            )
            ->joinLeft(
                array('ri' => $this->_rulesIndex),
                'pi.customer_group_id = ri.customer_group_id '
                . 'AND pi.entity_id = ri.product_id '
                . 'AND pi.website_id = ri.website_id',
                array()
            )
            ->where(
                'pi.final_price IS NOT NULL '
                . 'AND (pi.final_price != piAlias.final_price '
                . 'OR piAlias.entity_id IS NULL '
                . 'OR (pi.min_price != piAlias.min_price AND entity.type_id = \'bundle\'))'
            )
            ->where('rule_product_price_id IS NULL OR latest_start_date > Curdate()')
            ->group(array('pi.website_id', 'pi.customer_group_id', 'pi.entity_id'))
            ->order(array('pi.entity_id', 'pi.website_id'))
        ;

        $this->_joinNotifications(
            $select, 'pi.entity_id', AW_Productupdates_Model_Source_SubscriptionTypes::WAITING_PRICE_CHANGE
        );

        $update = $this->_getReadAdapter()->fetchAll($select);
        if (!empty($update)) {
            $this->_updateSchedule(
                $update,
                array(
                    'type' => AW_Productupdates_Model_Source_SubscriptionTypes::WAITING_PRICE_CHANGE,
                    'source' => AW_Productupdates_Model_Source_Sourcefrom::PRODUCT_PIRCE_INDEX
                )
            );
        }
        $this->_reindexData(array('index' => $this->_priceIndex, 'alias' => $this->_priceIndexAlias));
        return $this;
    }

    public function updateInventoryStock()
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('ii' => $this->_stockIndex, array('*')))
            ->joinLeft(
                array('ia' => $this->_stockIndexAlias),
                'ii.product_id = ia.product_id AND ii.website_id = ia.website_id AND ii.stock_id = ia.stock_id',
                array('statusAlias' => 'stock_status')
            )
            ->where('ii.stock_status = 1 AND (ia.product_id IS NULL OR ia.stock_status = 0)')
            ->order(array('ii.product_id', 'ii.website_id'))
        ;
       
        $this->_joinNotifications(
            $select, 'ii.product_id', AW_Productupdates_Model_Source_SubscriptionTypes::WAITING_STOCK_CHANGE
        );

        $update = $this->_getReadAdapter()->fetchAll($select);
        if (!empty($update)) {
            $this->_updateSchedule(
                $update,
                array(
                    'type' => AW_Productupdates_Model_Source_SubscriptionTypes::WAITING_STOCK_CHANGE,
                    'source' => AW_Productupdates_Model_Source_Sourcefrom::INVENTORY_INDEX
                )
            );
        }

        $this->_reindexData(array('index' => $this->_stockIndex, 'alias' => $this->_stockIndexAlias));
        return $this;
    }

    protected function _joinNotifications($select, $index, $type)
    {
        $select
            ->join(
                array('ni' => $this->_notifications),
                "{$index} = ni.product_id OR {$index} = ni.parent",
                array('parent' => 'ni.parent', 'main_product' => 'ni.product_id')
            )
            ->where('ni.subscription_type = ?', $type)
        ;
    }

    protected function _reindexData($table)
    {
        try {
            $this->_getWriteAdapter()->raw_query("TRUNCATE TABLE `{$table['alias']}`");
            $this->_getWriteAdapter()->raw_query(
                "REPLACE INTO `{$table['alias']}` ({$this->_getColumns($table['alias'])}) "
                . "SELECT * FROM `{$table['index']}`"
            );
        } catch (Exception $e) {
            Mage::helper('productupdates')->log($e);
        }      
    }
    
    public function reindexData(array $table)
    {
        $this->_reindexData($table);
        return $this;
    }

    private function _getColumns($table)
    {
        $describe = $this->_getReadAdapter()->describeTable($table);
        $columns = array();
        foreach ($describe as $column) {
            $columns[] = $column['COLUMN_NAME'];
        }
        return implode(',', $columns);
    }

    protected function _updateSchedule($scope, $data)
    {
        $customerGroups = array();
        $scheduleAdditional = array();

        foreach ($scope as $key => $item) {
            $scheduleAdditional[] = $item;
            if (isset($item['customer_group_id'])) {
                $customerGroups[] = $item['customer_group_id'];
            }
            if (!isset($scope[$key + 1])
                || ($scope[$key + 1]['product_id'] != $item['product_id'])
                || ($scope[$key + 1]['website_id'] != $item['website_id'])) {
                try {                    
                    Mage::getModel('productupdates/schedule')
                        ->setProductId($item['main_product'])
                        ->setWebsiteId($item['website_id'])
                        ->setCustomerGroupIds(implode(',', $customerGroups))
                        ->setStatus(AW_Productupdates_Model_Schedule::READY)
                        ->setSource($data['source'])
                        ->setStoreIds($this->_storesByWebsite[$item['website_id']])
                        ->setSendType($data['type'])
                        ->setCreatedAt(gmdate('Y-m-d H:i:s'))
                        ->setAdditional(Zend_Json::encode($scheduleAdditional))
                        ->save()
                    ;
                } catch (Exception $e) {
                    Mage::helper('productupdates')->log($e);
                }
                $customerGroups = $scheduleAdditional = array();
            }
        }
    }

    public function updateInventoryStockRow($productId, $stockStatus)
    {
        try {
            $this->_getWriteAdapter()->raw_query(
                "REPLACE INTO `{$this->_stockIndexAlias}` ({$this->_getColumns($this->_stockIndexAlias)}) "
                    . "SELECT * FROM `{$this->_stockIndex}`
                WHERE `{$this->_stockIndex}`.`product_id` = {$productId}"
            );
            $this->_getWriteAdapter()->raw_query(
                "UPDATE `{$this->_stockIndexAlias}`"
                    . "SET `{$this->_stockIndexAlias}`.`stock_status` = {$stockStatus}
                WHERE `{$this->_stockIndexAlias}`.`product_id` = {$productId}"
            );
        } catch (Exception $e) {
            Mage::helper('productupdates')->log($e);
        }
    }
}