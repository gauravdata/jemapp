<?php
class Dealer4Dealer_Exactonline_Model_Mysql4_Category extends Mage_Core_Model_Mysql4_Abstract{
    protected function _construct()
    {
        $this->_init('exactonline/category', 'category_id');
    }   
}