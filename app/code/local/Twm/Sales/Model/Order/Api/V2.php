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
        /** @var Twm_Sales_Model_Order $order */
        $order = $this->_initOrder($orderIncrementId);

        try {
            $order->receiveRma();
            $order->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        }

        return true;

    }




}