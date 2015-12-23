<?php
class Icreators_Emalo_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
            $this->loadLayout();
            $this->renderLayout();
    }

	public function pushOrderAction()
    {
        $incrementId = $this->getRequest()->getParam('order_id');

        if (empty($incrementId)) {
            Mage::throwException('Empty order in function ' . __FUNCTION__);
        }

        try
        {
            $order = new Mage_Sales_Model_Order();
            $order->loadByIncrementId($incrementId);

            if (!$order->getId()) {
                Mage::throwException('Invalid order number');
            }
			
			$this->pushOrder($order->getId());
        }
        catch(Exception $e)
        {
            Mage::log('Exception manual push for event order ' . $e->getMessage(), null, 'emalo.log');
        }
    }

    public function showOrderAction()
    {
        $incrementId = $this->getRequest()->getParam('order_id');

        if (empty($incrementId)) {
            Mage::throwException('Empty order in function ' . __FUNCTION__);
        }

        try
        {
            $order = new Mage_Sales_Model_Order();
            $order->loadByIncrementId($incrementId);

            if (!$order->getId()) {
                Mage::throwException('Invalid order number');
            }

            $observer = new Icreators_Emalo_Model_Observer();
            $xml = $observer->generateXml($order);

            $this->getResponse()->setHeader('Content-typ', 'text/xml')->setBody($xml);
        }
        catch(Exception $e)
        {
            Mage::log('Exception manual push for event order ' . $e->getMessage(), null, 'emalo.log');
        }
    }

	public function twmPushOrdersAction() {
		echo '<pre>';
		$orders = Mage::getModel('sales/order')->getCollection()
//			->addfieldtofilter('created_at',array(array('gteq' => '2015-06-07 00:00:00')))
//			->addfieldtofilter('created_at',array(array('lt' => '2015-06-09 00:00:00')));
			->addfieldtofilter('increment_id',array(array('gteq' => '500057105')))
			->addfieldtofilter('increment_id',array(array('lteq' => '500057317')));
		$i = 0;
		echo "Export " . $orders->count() . "orders <br/>" . PHP_EOL;

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
						$i++;
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
        	var_dump($result);
		}
	        catch(Exception $e)
        	{
            		Mage::log('Exception for event order ' . $e->getMessage(), null, 'emalo.log');
	        }
	}
}
