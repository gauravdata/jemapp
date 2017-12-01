<?php
class AW_Storecredit_Model_Mysql4_Quote_Storecredit_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_storecredit/quote_storecredit');
    }


    public function setFilterByQuoteId($quoteId)
    {
        $this->getSelect()->where('quote_entity_id = ?', $quoteId);
        return $this;
    }

    public function setFilterByStorecreditId($storecreditId)
    {
        $this->getSelect()->where('storecredit_id = ?', $storecreditId);
        return $this;
    }

    public function joinStoreCreditTable()
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