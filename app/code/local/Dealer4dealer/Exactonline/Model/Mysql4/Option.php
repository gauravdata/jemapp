<?php
class Dealer4Dealer_Exactonline_Model_Mysql4_Option extends Mage_Core_Model_Mysql4_Abstract{
    protected function _construct()
    {
        $this->_init('exactonline/option', 'option_id');
    }
}