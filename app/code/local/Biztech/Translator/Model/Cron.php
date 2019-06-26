<?php

class Biztech_Translator_Model_Cron extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('translator/cron');
    }

}