<?php

class Kiyoh_Customerreview_Adminhtml_Model_Observer  
{
    public function salesOrderShipmentSaveAfter(Varien_Event_Observer $observer)
    {
        //error_log("My observer called ....",0);
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();  
		$email = $order->getCustomerEmail();
		$storeId = $order->getStoreId();
		
		$kiyoh_status = Mage::getStoreConfig('customconfig/review_group/custom_enable',$storeId);
		$kiyoh_eventval = Mage::getStoreConfig('customconfig/review_group/custom_event',$storeId);
		$kiyoh_connector = Mage::getStoreConfig('customconfig/review_group/custom_connector',$storeId);
		$kiyoh_action = Mage::getStoreConfig('customconfig/review_group/custom_action',$storeId);
		$kiyoh_user = Mage::getStoreConfig('customconfig/review_group/custom_user',$storeId);
		$kiyoh_delay = Mage::getStoreConfig('customconfig/review_group/custom_delay',$storeId);
		
		if($kiyoh_eventval == 'Shipping' &&  $kiyoh_status =='1')
		{
		
			$url = 'https://www.kiyoh.nl/set.php?user='.$kiyoh_user.'&connector='.$kiyoh_connector.'&action='.$kiyoh_action.'&targetMail='.$email.'&delay='.$kiyoh_delay;
	
			// create a new cURL resource
			$curl = curl_init();
	
			// set URL and other appropriate options
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			// grab URL and pass it to the browser
			$response = curl_exec($curl);
			if (curl_errno($curl)) 
			{
				Mage::log(curl_error($curl).'---Url---'.$url, null, 'kiyoh.log');
				curl_close($curl);
				exit;
			}
			
			Mage::log($response.'---Url---'.$url, null, 'kiyoh.log');
			curl_close($curl);
		}     
    }
}