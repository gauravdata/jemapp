<?php
class MT_Email_Model_Resource_Var extends Mage_Core_Model_Mysql4_Abstract
{


    protected function _construct()
    {
        $this->_init('mtemail/var', 'entity_id');
    }


}