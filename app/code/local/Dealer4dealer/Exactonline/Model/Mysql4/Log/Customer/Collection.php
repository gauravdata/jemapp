<?php
class Dealer4Dealer_Exactonline_Model_Mysql4_Log_Customer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected function _construct()
    {
            $this->_init('exactonline/log_customer');
    }
}