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


class AW_Productupdates_Block_Adminhtml_Productupdates_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('subscribers');
        $this->setDefaultSort('subscriberid');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $subscribers = Mage::getModel('productupdates/productupdates')->getCollection()
            ->getSubscribersList()
        ;
        $this->setCollection($subscribers);
        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'subscriber_id',
            array(
                'header' => $this->__('Subscriber ID'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'subscriberid',
                'type' => 'number',
                'filter_condition_callback' => array($this, 'filter_id_callback'),
            )
        );

        $this->addColumn(
            'fullname',
            array(
                'header' => $this->__('Full name'),
                'align' => 'left',
                'index' => 'fullname',
            )
        );

        $this->addColumn(
            'e-mail',
            array(
                'header' => $this->__('Email'),
                'align' => 'left',
                'index' => 'email',
            )
        );

        $this->addColumn(
            'productsstr',
            array(
                'header' => $this->__('Products'),
                'align' => 'left',
                'index' => 'productsstr',
                'type' => 'text',
                'filter_condition_callback' => array($this, 'filter_productsstr_callback'),
            )
        );

        $this->addColumn(
            'subscription_date',
            array(
                'header' => $this->__('Subscription date'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'subscription_date',
            )
        );

        $this->addColumn(
            'action',
            array(
                'header' => $this->__('Action'),
                'width' => '140',
                'type' => 'action',
                'getter' => 'getSubscriberId',
                'actions' => array(
                    array(
                        'caption' => $this->__('Unsubscribe from all'),
                        'url' => array('base' => '*/*/delete'),
                        'field' => 'id',
                        'confirm' => $this->__('Are you sure?'),
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
            )
        );
        return parent::_prepareColumns();
    }

    protected function filter_productsstr_callback($collection, $column)
    {
        $val = $column->getFilter()->getValue();
        if (!$val) {
            return;
        }
        $collection->addProductstrFilter($val);
    }

    protected function filter_id_callback($collection, $column)
    {
        $fromTo = $column->getFilter()->getValue();
        if (!isset($fromTo['from']) && !isset($fromTo['to'])) {
            return;
        }
        if (
            isset($fromTo['from']) && !is_numeric($fromTo['from'])
            || isset($fromTo['to']) && !is_numeric($fromTo['to'])
        ) {
             return;
        }
        $collection->addSubsIdFilter($fromTo);
    }

}