<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   RMA
 * @version   2.4.0
 * @build     1607
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Rma_Helper_OfflineOrder
{
    /**
     * @param int $customerId
     * @param string $receiptNumber
     * @return Mirasvit_Rma_Model_Offline_Order
     */
    public function getOfflineOrder($customerId, $receiptNumber)
    {
        $collection = Mage::getModel('rma/offline_order')->getCollection()
            ->addFieldToFilter('receipt_number', $receiptNumber)
            ->addFieldToFilter('customer_id', $customerId);
        if ($collection->count()) {
            return $collection->getFirstItem();
        }
        $order = Mage::getModel('rma/offline_order');
        $order->setCustomerId($customerId);
        $order->setReceiptNumber($receiptNumber);
        $order->save();
        return $order;
    }

}