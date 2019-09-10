<?php
class MT_Email_Model_Resource_Var_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('mtemail/var');
    }
}