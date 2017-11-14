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
 * @package    AW_Points
 * @version    1.9.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Points_Block_Adminhtml_Customersummary_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customersummaryGrid');
        $this->setDefaultSort('entity_id');
    }

    protected function _prepareCollection()
    {
        $this->setCollection($this->_getPreparedCollection());
        return parent::_prepareCollection();
    }

    protected function _getPreparedCollection()
    {
        $customerCollection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('website_id')
        ;

        $customerCollection = $this->addPointsSummaryInfo($customerCollection);

        return $customerCollection;
    }

    protected function addPointsSummaryInfo($collection)
    {
        $collection->joinTable(
            array('points_summary_table' => Mage::getSingleton('core/resource')->getTableName('points/summary')),
            "customer_id = entity_id",
            array(
                'points' => 'points',
                'balance_update_notification' => 'balance_update_notification',
                'points_expiration_notification' => 'points_expiration_notification'
            ),
            null,
            'left'
        );

        return $collection;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'name',
            array(
                'header'    => Mage::helper('points')->__('Customer Name'),
                'index'     => 'name',
            )
        );
        $this->addColumn(
            'email',
            array(
                'header'    => Mage::helper('points')->__('Customer Email'),
                'index'     => 'email',
            )
        );

        $this->addColumn(
            'points',
            array(
                'header'    => Mage::helper('points')->__('Current customer balance'),
                'index'     => 'points',
                'type'      => 'number',
                'default'   => '0',
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'website_id',
                array(
                    'header'  => Mage::helper('points')->__('Website'),
                    'align'   => 'center',
                    'width'   => '80px',
                    'type'    => 'options',
                    'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                    'index'   => 'website_id',
                )
            );
        }

        $this->addColumn(
            'balance_update_notification',
            array(
                'header'    => Mage::helper('points')->__('Balance Update Notifications'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('points/source_subscribestatus')->toOptionArray(),
                'index'     => 'balance_update_notification',
                'renderer'  => 'points/adminhtml_customersummary_grid_column_renderer_subscribestatus',
            )
        );

        $this->addColumn(
            'points_expiration_notification',
            array(
                'header'    => Mage::helper('points')->__('Balance Expiration Notifications'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('points/source_subscribestatus')->toOptionArray(),
                'index'     => 'points_expiration_notification',
                'renderer'  => 'points/adminhtml_customersummary_grid_column_renderer_subscribestatus',
            )
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('points')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('points')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', array('id' => $row->getId()));
    }
}