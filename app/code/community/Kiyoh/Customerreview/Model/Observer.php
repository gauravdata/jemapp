<?php

class Kiyoh_Customerreview_Model_Observer
{
    public function salesOrderShipmentSaveAfter(Varien_Event_Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $storeId = $order->getStoreId();

        $kiyoh_status = Mage::getStoreConfig('customconfig/review_group/custom_enable',$storeId);
        $kiyoh_eventval = Mage::getStoreConfig('customconfig/review_group/custom_event',$storeId);

        if($kiyoh_eventval === 'Shipping' &&  $kiyoh_status =='1')
        {
                $this->sendRequest($order);
        }
    }
    public function salesOrderSaveAfter($observer){
        $order = $observer->getOrder();
        $storeId = $order->getStoreId();
        $kiyoh_status = Mage::getStoreConfig('customconfig/review_group/custom_enable',$storeId);
        $kiyoh_eventval = Mage::getStoreConfig('customconfig/review_group/custom_event',$storeId);
        $kiyoh_orderstatus = explode(',',Mage::getStoreConfig('customconfig/review_group/custom_event_order_status',$storeId));

        if($kiyoh_eventval === 'Orderstatus' &&  $kiyoh_status =='1' && in_array($observer->getOrder()->getStatus(), $kiyoh_orderstatus))
        {
                $this->sendRequest($observer->getOrder());
        }
    }
    protected function sendRequest($order){
        $email = $order->getCustomerEmail();
	$storeId = $order->getStoreId();
        $kiyoh_server = Mage::getStoreConfig('customconfig/review_group/custom_server',$storeId);
        $kiyoh_user = Mage::getStoreConfig('customconfig/review_group/custom_user',$storeId);
        $kiyoh_connector = Mage::getStoreConfig('customconfig/review_group/custom_connector',$storeId);
        $kiyoh_action = Mage::getStoreConfig('customconfig/review_group/custom_action',$storeId);

        $kiyoh_delay = Mage::getStoreConfig('customconfig/review_group/custom_delay',$storeId);
        $url = 'https://www.'.$kiyoh_server.'/set.php?user='.$kiyoh_user.'&connector='.$kiyoh_connector.'&action='.$kiyoh_action.'&targetMail='.$email.'&delay='.$kiyoh_delay;

        // create a new cURL resource
        $curl = curl_init();

        // set URL and other appropriate options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($curl, CURLOPT_TIMEOUT, 4);
        // grab URL and pass it to the browser
        $response = curl_exec($curl);
        if (curl_errno($curl))
        {
                Mage::log(curl_error($curl).'---Url---'.$url, null, 'kiyoh.log');
                curl_close($curl);
                return;
        }
        if(Mage::getStoreConfig('customconfig/review_group/debug_enable',$storeId)){
            Mage::log($response.'---Url---'.$url, null, 'kiyoh.log');
        }
        curl_close($curl);
    }
}