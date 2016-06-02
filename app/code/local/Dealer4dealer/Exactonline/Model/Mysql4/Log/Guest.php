<?php
/**
 * Created by PhpStorm.
 * User: Michiel
 * Date: 03-Jul-15
 * Time: 10:47
 */ 
class Dealer4dealer_Exactonline_Model_Mysql4_Log_Guest extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('exactonline/log_guest', 'guest_id');
    }

}