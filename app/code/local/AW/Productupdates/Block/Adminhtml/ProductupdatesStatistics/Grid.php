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


class AW_Productupdates_Block_Adminhtml_ProductupdatesStatistics_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productupdatesStats');
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $subscriptions = Mage::getModel('productupdates/productupdates')->getCollection();
        foreach ($this->getAllowedTypes() as $key => $type) {
            $subscriptions->getSelect()
                ->columns(
                    array($key => new Zend_Db_Expr("COUNT(IF(subscription_type = {$type}, subscriber_id, NULL))"))
                )
            ;
        }

        $attributeId = Mage::getModel('eav/entity')->setType('catalog_product')->getAttribute('name')->getAttributeId();
        $tableName = Mage::getModel('catalog/product')->getResource()->getTable('catalog_product_entity_varchar');
        $subscriptions->getSelect()->group('main_table.product_id')
            ->joinLeft(
                array('a' => $tableName),
                'a.entity_id = main_table.product_id and a.attribute_id =' . (int)$attributeId,
                array('product_name' => 'value')
            )
        ;

        $this->setCollection($subscriptions);
        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'product_id',
            array(
                'header' => $this->__('Product Id'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'product_id',
                'type' => 'number'
            )
        );

        $this->addColumn(
            'product_name',
            array(
                'header' => $this->__('Name'),
                'align' => 'left',
                'index' => 'product_name',
                'type' => 'action',
                'renderer' => new AW_Productupdates_Block_Adminhtml_Render_Productlink(),
                'filter_condition_callback'=> array($this, '_filterStoreCondition'),
            )
        );

        foreach ($this->getAllowedTypes() as $key => $type) {
            $title = ucwords(str_replace("_", " ", $key));
            $this->addColumn(
                $key, array(
                    'header' => $this->__("Subscribed to  %s", $title),
                    'align' => 'left',
                    'width' => '50px',
                    'index' => $key,
                    'filter' => false,
                    'type' => 'number'
                )
            );
        }
        return parent::_prepareColumns();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addFieldToFilter('a.value', array('like' => '%' . $value . '%'));
    }

    public function getAllowedTypes()
    {
        return Mage::helper('productupdates')->getTypes();
    }

    public function addColumn($columnId, $column)
    {
        if (!is_array($column)) {
            throw new Exception(Mage::helper('adminhtml')->__('Wrong column format.'));
        }
        $this->_columns[$columnId] = $this->getLayout()->createBlock('adminhtml/widget_grid_column')
            ->setData($column)
            ->setGrid($this)
            ->setId($columnId)
        ;
        $this->_lastColumnId = $columnId;
        return $this;
    }

    protected function filterNumber($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if (isset($value['from']) && isset($value['to'])) {
            $where = "{$column->getIndex()} >= {$value['from']} AND {$column->getIndex()} <= {$value['to']}";
        } elseif (isset($value['from']) && !isset($value['to'])) {
            $where = "{$column->getIndex()} >= {$value['from']}";
        } else {
            $where = "{$column->getIndex()} <= {$value['to']}";
        }
        $collection->getSelect()->having($where);
    }
}