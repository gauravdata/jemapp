<?php
class AW_Storecredit_Model_Order_Refunded_Storecredit extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_storecredit/order_refunded_storecredit');
    }
}