<?php
class Icreators_Emalo_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
            $this->loadLayout();
            $this->renderLayout();
    }

	public function twmPushOrdersAction() {
		exit('nee');
		echo '<pre>';
		$orders = Mage::getModel('sales/order')->getCollection()
			->addfieldtofilter('created_at',array(array('gteq' => '2013-12-13 00:00:00')));
		foreach ($orders as $order) {
			$state = $order->getState();
			if ($state == Mage_Sales_Model_Order::STATE_PROCESSING)
			{
				if ($order->hasInvoices())
				{
					$invoicesPaid = true;
					foreach ($order->getInvoiceCollection() as $invoice)
					{
						if ((int)$invoice->getState() !== Mage_Sales_Model_Order_Invoice::STATE_PAID)
						{
							// $invoiceId = $invoice->getId();
							// Mage::log("Invoice #{$invoiceId} not paid", Zend_Log::DEBUG, 'debug.log');
							$invoicesPaid = false;
						}
					}
					if ($invoicesPaid) {
						$this->pushOrder($order->getId());
						var_dump($order->getId());					
					}
					else {
						var_dump('not paid');
					}
				}
			}
		}
		exit;	
	}	

	public function pushOrder($id)
    	{
//        	$incrementId = $id;


	        try
        	{
            	$order = new Mage_Sales_Model_Order();
	        $order->load($id);
//		$order->loadByIncrementId($incrementId);

            	if (!$order->getId()) {
                	Mage::throwException('Invalid order number');
            	}
		
		$observer = new Icreators_Emalo_Model_Observer();
            	$xml = $observer->generateXml($order);

            	$icUrl = Mage::getStoreConfig('emalo_options/export/emalourl');
            	$icAccessArea = Mage::getStoreConfig('emalo_options/export/emaloAccessArea');
            	$icCustomerNumber = Mage::getStoreConfig('emalo_options/export/emaloCustomerNumber');
            	$icPassword = Mage::getStoreConfig('emalo_options/export/emaloPassword');
            	$result = '';
            	$params = array(
                	"sAccessArea"         => $icAccessArea,
	                'sCustomerNumber'     => $icCustomerNumber,
        	        'sPassword'         => $icPassword,
                	'sMethod'             => "mfnlWebshopIn",
	                'sParams'             => $xml,
        	        'sResult'             => $result
            	);

	        ini_set('default_socket_timeout', 60);
         	$client = new SoapClient($icUrl);
	        $result = $client->gbCallCustomerBusinessLinkMethod($params);
        	}
	        catch(Exception $e)
        	{
            		Mage::log('Exception for event order ' . $e->getMessage(), null, 'emalo.log');
	        }
	}
}
