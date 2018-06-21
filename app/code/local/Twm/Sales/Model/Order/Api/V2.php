<?php
/**
 * Created by PhpStorm.
 * User: mirjana
 * Date: 21-6-2018
 * Time: 09:51
 */

class Twm_Sales_Model_Order_Api_V2 extends Mage_Sales_Model_Order_Api_V2
{


    /**
     * Rma received
     *
     * @param string $orderIncrementId
     * @return boolean
     */
    public function receiveRma($orderIncrementId)
    {

        $order = $this->_initOrder($orderIncrementId);

        try {
            $this->execute($orderIncrementId);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        }

        return true;

    }


    protected function execute($orderIncrementId){
        // TODO
        Mage::log("called receiveRma voor de order $orderIncrementId", Zend_Log::INFO, 'receiveRma.log');
    }


}