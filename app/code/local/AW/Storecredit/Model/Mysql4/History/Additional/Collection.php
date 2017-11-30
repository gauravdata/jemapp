<?php
class AW_Storecredit_Model_Mysql4_History_Additional_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('aw_storecredit/history_additional');
    }
}