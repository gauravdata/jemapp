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



/**
 * @method Mirasvit_Rma_Model_Resource_Offline_Order_Collection|Mirasvit_Rma_Model_Offline_Order[] getCollection()
 * @method Mirasvit_Rma_Model_Offline_Order load(int $id)
 * @method string getReceiptNumber()
 * @method $this setReceiptNumber(string $param)
 */
class Mirasvit_Rma_Model_Offline_Order extends Mage_Core_Model_Abstract
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('rma/offline_order');
    }

    /**
     * @var Mirasvit_Rma_Model_Offline_Item[]|Mirasvit_Rma_Model_Resource_Offline_Item_Collection
     */
    protected $itemCollection;

    /**
     * @return Mirasvit_Rma_Model_Offline_Item[]|Mirasvit_Rma_Model_Resource_Offline_Item_Collection
     */
    public function getItemCollection()
    {
        if (!$this->itemCollection) {
            $this->itemCollection = Mage::getModel('rma/offline_item')->getCollection()
                ->addFieldToFilter('offline_order_id', $this->getId());
        }

        return $this->itemCollection;
    }
}