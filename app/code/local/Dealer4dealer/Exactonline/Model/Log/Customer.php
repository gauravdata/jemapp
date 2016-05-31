<?php
/**
 * @method int getId()
 * @method Dealer4dealer_Exactonline_Model_Log_Customer setId(int $value)
 * @method string getLastSync()
 * @method Dealer4dealer_Exactonline_Model_Log_Customer setLastSync(string $value)
 * @method string getStatusMessage()
 * @method Dealer4dealer_Exactonline_Model_Log_Customer setStatusMessage(string $value)
 * @method int getState()
 * @method Dealer4dealer_Exactonline_Model_Log_Customer setState(int $value)
 * @method int getCustomerId()
 * @method Dealer4dealer_Exactonline_Model_Log_Customer setCustomerId(int $value)
 * @method string getRawXmlResponse()
 * @method Dealer4dealer_Exactonline_Model_Log_Customer setRawXmlResponse(string $value)
 */
class Dealer4dealer_Exactonline_Model_Log_Customer extends Mage_Core_Model_Abstract
{
    protected function _construct(){
        $this->_init('exactonline/log_customer');
    }
}