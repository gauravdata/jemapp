<?php
/**
 * Created by PhpStorm.
 * User: mathijs
 * Date: 11-12-17
 * Time: 10:27
 */ 
class Twm_ExtendAwStoreCredit_Helper_Storecredit_Data extends AW_Storecredit_Helper_Data
{
    public function prepareMessage($additionInfo)
    {
        $messageType = $additionInfo['message_type'];
        $messageData = $additionInfo['message_data'];
        $messages = array();

        $messageLabel = Mage::getModel('aw_storecredit/source_storecredit_history_action')
            ->getMessageLabelByType($messageType)
        ;
        if (!is_array($messageData)) {
            $messages[] = $messageData;
            return sprintf($this->__($messageLabel), ...$messages);
        }
        if (array_key_exists('order_increment_id', $messageData)) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($messageData['order_increment_id']);
            $url = Mage::getUrl(self::ADMIN_ORDER_VIEW_ROUTE, array('order_id' => $order->getId()));
            $messages[] = "<a href='". $url ."'>#".$messageData['order_increment_id']."</a>";
        }
        if (array_key_exists('creditmemo_increment_id', $messageData)) {
            $creditmemo = Mage::getModel('sales/order_creditmemo')->load($messageData['creditmemo_increment_id'], 'increment_id');
            $url = Mage::getUrl(self::ADMIN_CREDITMEMO_VIEW_ROUTE, array('creditmemo_id' => $creditmemo->getId()));
            $messages[] = "<a href='". $url . "'>#" . $messageData['creditmemo_increment_id'] . "</a>";
        }
        return sprintf($this->__($messageLabel), ...$messages);
    }

    public function prepareFrontendMessage($additionInfo)
    {
        $messageType = $additionInfo['message_type'];
        $messageData = $additionInfo['message_data'];
        $messages = array();

        $messageLabel = Mage::getModel('aw_storecredit/source_storecredit_history_action')
            ->getMessageLabelByType($messageType)
        ;
        if (!is_array($messageData)) {
            $messages[] = $messageData;
            return sprintf($this->__($messageLabel), ...$messages);
        }
        if (array_key_exists('order_increment_id', $messageData)) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($messageData['order_increment_id']);
            $url = Mage::getUrl(self::FRONTEND_ORDER_VIEW_ROUTE, array('order_id' => $order->getId()));
            $messages[] = "<a href='". $url ."'>#".$messageData['order_increment_id']."</a>";
        }
        if (array_key_exists('creditmemo_increment_id', $messageData) && array_key_exists('order_increment_id', $messageData)) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($messageData['order_increment_id']);
            $url = Mage::getUrl(self::FRONTEND_CREDITMEMO_VIEW_ROUTE, array('order_id' => $order->getId()));
            $messages[] = "<a href='". $url . "'>#" . $messageData['creditmemo_increment_id'] . "</a>";
        }
        return sprintf($this->__($messageLabel), ...$messages);
    }

}