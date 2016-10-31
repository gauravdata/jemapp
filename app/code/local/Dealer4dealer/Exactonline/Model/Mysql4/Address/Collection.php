<?php
/**
 * Created by PhpStorm.
 * User: Michiel
 * Date: 21-Jan-15
 * Time: 16:46
 */ 
class Dealer4dealer_Exactonline_Model_Mysql4_Address_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('exactonline/address');
    }

}