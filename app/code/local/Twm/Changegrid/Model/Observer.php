<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 22-2-2017
 * Time: 20:31
 */

class Twm_Changegrid_Model_Observer {
    public function beforeBlockToHtml(Varien_Event_Observer $observer) {
        $grid = $observer->getBlock();
        /**
         * Mage_Adminhtml_Block_Customer_Grid
         */
        if ($grid instanceof Mage_Adminhtml_Block_Sales_Order_Grid) {
            $grid->addColumnAfter(
                'city',
                array(
                    'header' => Mage::helper('customer')->__('City'),
                    'index'  => 'city',
                ),
                'shipping_name'
            );
        } elseif ($grid instanceof Mage_Adminhtml_Block_Customer_Grid) {
            $grid->addColumnAfter(
                'city',
                array(
                    'header' => Mage::helper('customer')->__('City'),
                    'index'  => 'billing_city'
                ),
                'billing_postcode'
            );
        }
    }

}
