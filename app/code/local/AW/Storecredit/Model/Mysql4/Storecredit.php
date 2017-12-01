<?php
class AW_Storecredit_Model_Mysql4_Storecredit extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('aw_storecredit/storecredit', 'entity_id');
    }

    public function removeTotals(AW_Storecredit_Model_Storecredit $storecreditModel)
    {
        $write = $this->_getWriteAdapter();
        $write->query(
            "DELETE FROM {$this->getTable('aw_storecredit/history')} "
            . "WHERE storecredit_id = {$storecreditModel->getId()}"
        );
        $write->query(
            "DELETE FROM {$this->getTable('aw_storecredit/quote_storecredit')} "
            . "WHERE storecredit_id = {$storecreditModel->getId()}"
        );
        $write->query(
            "DELETE FROM {$this->getTable('aw_storecredit/order_invoice_storecredit')} "
            . "WHERE storecredit_id = {$storecreditModel->getId()}"
        );
        $write->query(
            "DELETE FROM {$this->getTable('aw_storecredit/order_creditmemo_storecredit')} "
            . "WHERE storecredit_id = {$storecreditModel->getId()}"
        );
        return $this;
    }
}