<?php
/**
 * File containing the Klarna order status model
 *
 * PHP Version 5.3
 *
 * @category  Payment
 * @package   Klarna_Module_Magento
 * @author    Merchant Integration <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0
 * @link      http://integration.klarna.com/
 */

/**
 * Class used to hide the Klarna order statuses for customers
 *
 * @category  Payment
 * @package   Klarna_Module_Magento
 * @author    Merchant Integration <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      http://integration.klarna.com/
 */
class Klarna_KlarnaPaymentModule_Model_Order_Status
    extends Mage_Sales_Model_Order_Status
{

    const ACCEPTED = "klarna_accepted";
    const PENDING = "klarna_pending";
    const DENIED = "klarna_denied";

    /**
     * Get status labels per store and hiding Klarna status for customers
     *
     * @return array Array of status labels
     */
    public function getStoreLabels()
    {
        $status = $this->getStatus();
        if ($status !== self::ACCEPTED
            && $status !== self::PENDING
            && $status !== self::DENIED
        ) {
            return parent::getStoreLabels();
        }

        $labels = array();
        $processing = Mage::getModel('sales/order_status')
            ->load(Mage_Sales_Model_Order::STATE_PROCESSING);

        foreach (Mage::app()->getStores() as $store) {
            $storeId = $store->getId();
            $labels[$storeId] = $processing->getStoreLabel($storeId);
        }

        return $labels;
    }
}
