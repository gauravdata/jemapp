<?php
class AW_Storecredit_Model_Mysql4_Order_Creditmemo_Storecredit_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_storecredit/order_creditmemo_storecredit');
    }

    public function setFilterByCreditmemoIds(array $invoiceIds)
    {
        $this->getSelect()->where('creditmemo_entity_id IN (?)', $invoiceIds);
        return $this;
    }

    public function setFilterByCreditmemoId($invoiceId)
    {
        $this->getSelect()->where('creditmemo_entity_id = ?', $invoiceId);
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
                     'storecredit_balance' => 'storecredit.balance'
                )
            )
        ;
        return $this;
    }
}