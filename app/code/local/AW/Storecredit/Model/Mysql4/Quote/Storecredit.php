<?php
class AW_Storecredit_Model_Mysql4_Quote_Storecredit extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('aw_storecredit/quote_storecredit', 'link_id');
    }
}