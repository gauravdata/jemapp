<?php
/**
 * @method int getId()
 * @method Dealer4dealer_Exactonline_Model_Log_Order setId(int $value)
 * @method string getLastSync()
 * @method Dealer4dealer_Exactonline_Model_Log_Order setLastSync(string $value)
 * @method int getSyncedDelivery()
 * @method Dealer4dealer_Exactonline_Model_Log_Order setSyncedDelivery(int $value)
 * @method string getStatusMessage()
 * @method Dealer4dealer_Exactonline_Model_Log_Order setStatusMessage(string $value)
 * @method int getState()
 * @method Dealer4dealer_Exactonline_Model_Log_Order setState(int $value)
 * @method string getDeliveryDate()
 * @method Dealer4dealer_Exactonline_Model_Log_Order setDeliveryDate(string $value)
 * @method string getDeliveryStatusMessage()
 * @method Dealer4dealer_Exactonline_Model_Log_Order setDeliveryStatusMessage(string $value)
 * @method string getExactId()
 * @method Dealer4dealer_Exactonline_Model_Log_Order setExactId(string $value)
 * @method int getOrderId()
 * @method Dealer4dealer_Exactonline_Model_Log_Order setOrderId(int $value)
 * @method int getDeliveryState()
 * @method Dealer4dealer_Exactonline_Model_Log_Order setDeliveryState(int $value)
 * @method string getRawXmlResponse()
 * @method Dealer4dealer_Exactonline_Model_Log_Order setRawXmlResponse(string $value)
 */
class Dealer4dealer_Exactonline_Model_Log_Order extends Mage_Core_Model_Abstract
{
    protected function _construct(){
        $this->_init('exactonline/log_order');
    }
}