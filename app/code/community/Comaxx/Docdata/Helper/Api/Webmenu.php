<?php
/**
 * Helper for webmenu communication
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Helper_Api_Webmenu {
	
	const RETURN_URL_SUCCESS = 'docdata/payment/success/',
		  RETURN_URL_CANCELED = 'docdata/payment/cancel/',
		  RETURN_URL_ERROR = 'docdata/payment/error/',
		  RETURN_URL_PENDING = 'docdata/payment/pending/';
	
	/**
	 * Extracts parameters needed for the Webmenu
	 *
	 * @param Mage_Sales_Model_Order $order Order to handle in the Webmenu
	 * @param string $pm_code Payment method code
	 * @param string $payment_order_key Reference for the payment order to be used in the Webmenu
	 * @param array $extra_params Additional parameters to be used in the Webmenu
	 *
	 * @return array Array of parameters
	 */
	public function getParams(Mage_Sales_Model_Order $order, $pm_code, $payment_order_key, array $extra_params)
	{
		$helper = Mage::helper('docdata/config');
		// get values to send
		$lang = explode('_', Mage::app()->getLocale()->getLocaleCode());
		$merchant = $helper->getMerchant();
		//convert pm_code to command
		$pm_command = $helper->getPaymentMethodItem($pm_code, 'command');
		
		$result = array(
			'payment_cluster_key' => $payment_order_key,
			'merchant_name' => $merchant['username'],
			'client_language' => $lang[0],
			'default_pm' => $pm_command
		);
		
		//send urls to allow return urls per store (docdata backend currently only supports 1 return url for a shop)
		$result['return_url_success'] = Mage::getUrl(self::RETURN_URL_SUCCESS, array('_secure'=>true));
		$result['return_url_canceled'] = Mage::getUrl(self::RETURN_URL_CANCELED, array('_secure'=>true));
		$result['return_url_error'] = Mage::getUrl(self::RETURN_URL_ERROR, array('_secure'=>true));
		$result['return_url_pending'] = Mage::getUrl(self::RETURN_URL_PENDING, array('_secure'=>true));
		
		return array_merge($result, $extra_params);
	}
}