<?php
class AW_Storecredit_Model_Mysql4_Order_Invoice_Storecredit extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('aw_storecredit/order_invoice_storecredit', 'link_id');
    }
}