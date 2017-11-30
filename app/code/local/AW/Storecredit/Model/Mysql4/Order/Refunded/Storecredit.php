<?php
class AW_Storecredit_Model_Mysql4_Order_Refunded_Storecredit extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('aw_storecredit/order_refunded_storecredit', 'link_id');
    }
}