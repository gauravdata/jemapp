<?php
class AW_Storecredit_Model_Mysql4_Order_Refunded_Storecredit_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_storecredit/order_refunded_storecredit');
    }

    public function setFilterByOrderId($orderId)
    {
        $this->getSelect()->where('order_entity_id = ?', $orderId);
        return $this;
    }

    public function setFilterByStorecreditId($storecreditId)
    {
        $this->getSelect()->where('storecredit_id = ?', $storecreditId);
        return $this;
    }

    public function joinStorecreditTable()
    {
        $this->getSelect()
            ->joinLeft(
                array(
                    'storecredit' => $this->getTable('aw_storecredit/storecredit')
                ),
                'main_table.storecredit_id = storecredit.entity_id',
                array(
                    'storecredit_refunded_amount' => 'main_table.refunded_amount'
                )
            )
        ;
        return $this;
    }
}