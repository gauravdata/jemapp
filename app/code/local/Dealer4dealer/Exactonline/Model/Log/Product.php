<?php
/**
 * @method int getId()
 * @method Dealer4dealer_Exactonline_Model_Log_Product setId(int $value)
 * @method string getLastSync()
 * @method Dealer4dealer_Exactonline_Model_Log_Product setLastSync(string $value)
 * @method int getProductId()
 * @method Dealer4dealer_Exactonline_Model_Log_Product setProductId(int $value)
 * @method string getStatusMessage()
 * @method Dealer4dealer_Exactonline_Model_Log_Product setStatusMessage(string $value)
 * @method int getState()
 * @method Dealer4dealer_Exactonline_Model_Log_Product setState(int $value)
 * @method string getRawXmlResponse()
 * @method Dealer4dealer_Exactonline_Model_Log_Product setRawXmlResponse(string $value)
 */
class Dealer4dealer_Exactonline_Model_Log_Product extends Mage_Core_Model_Abstract
{
    protected function _construct(){
        $this->_init('exactonline/log_product');
    }
}