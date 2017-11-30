<?php
class AW_Storecredit_Model_Mysql4_History_Additional extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('aw_storecredit/history_additional', 'link_id');
    }
}