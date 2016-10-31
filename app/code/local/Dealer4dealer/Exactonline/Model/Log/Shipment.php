<?php
/**
 * @method int getId()
 * @method Dealer4dealer_Exactonline_Model_Log_Shipment setId(int $value)
 * @method string getLastSync()
 * @method Dealer4dealer_Exactonline_Model_Log_Shipment setLastSync(string $value)
 * @method string getStatusMessage()
 * @method Dealer4dealer_Exactonline_Model_Log_Shipment setStatusMessage(string $value)
 * @method int getState()
 * @method Dealer4dealer_Exactonline_Model_Log_Shipment setState(int $value)
 * @method int getShipmentId()
 * @method Dealer4dealer_Exactonline_Model_Log_Shipment setShipmentId(int $value)
 * @method string getExactId()
 * @method Dealer4dealer_Exactonline_Model_Log_Shipment setExactId(string $value)
 * @method string getRawXmlResponse()
 * @method Dealer4dealer_Exactonline_Model_Log_Shipment setRawXmlResponse(string $value)
 */
class Dealer4dealer_Exactonline_Model_Log_Shipment extends Mage_Core_Model_Abstract
{
    protected function _construct(){
        $this->_init('exactonline/log_shipment');
    }
}