<?php

class Biztech_Translator_Model_Mysql4_Logcron_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('translator/logcron');
    }
}