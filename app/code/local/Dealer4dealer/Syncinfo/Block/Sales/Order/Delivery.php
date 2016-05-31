<?php
class Dealer4dealer_Syncinfo_Block_Sales_Order_Delivery extends Dealer4dealer_Syncinfo_Block_Grid_Abstract
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }

    protected function _getValue(Varien_Object $row)
    {
        // Get orderIds
        $realOrderId = $row->getIncrementId();
        $orderId = $row->getEntityId();


        $log = Mage::getModel('exactonline/log_order')->load($orderId,'order_id');
        $date  = Mage::helper('core')->formatTime($log->getDeliveryDate(), Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, true);

        if($log->getId() && $log->getSyncedDelivery()) {
            if((bool)$log->getDeliveryState()) {
                return '<img src="'.$this->getMediaUrl('synced.png').'" title="'.$date.' - '.$log->getDeliveryStatusMessage().'" alt="'.$log->getDeliveryStatusMessage().'"  />';
            }else {
                return '<img src="'.$this->getMediaUrl('error.png').'" title="'.$date.' - '.$log->getDeliveryStatusMessage().'" alt="'.$log->getDeliveryStatusMessage().'"  />';
            }
        }else {
            $statusMessage = Mage::helper('core')->__('Not yet delivered');
            return '<img src="'.$this->getMediaUrl('not-synced.png').'" title="'.$statusMessage.'" alt="'.$statusMessage.'" />';
        }
    }
}