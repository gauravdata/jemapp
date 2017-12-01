<?php
class AW_Storecredit_Model_Mysql4_Order_Creditmemo_Storecredit extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('aw_storecredit/order_creditmemo_storecredit', 'link_id');
    }
}