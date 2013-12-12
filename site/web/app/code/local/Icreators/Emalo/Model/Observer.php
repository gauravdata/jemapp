<?php
 
class Icreators_Emalo_Model_Observer
{

	public function generateXml(Mage_Sales_Model_Order $order) 
	{
		
		$orderId  = $order->getIncrementId();

		// ADDED BY M2SC START
		$orderStatus = $order->getStatus();
		$orderState = $order->getState();
		$orderTotal = $order->getGrandTotal();
		$orderValue = $order->getSubtotal();
		// $shippingMethod = $order->getAddressShippingMethod();
		$shippingMethod = $order->getShippingMethod();
		$shippingDescription = $order->getShippingDescription();
		$storeId = Mage::app()->getStore()->getId();
		$storeName = Mage::app()->getStore()->getName();
		$websiteId = Mage::app()->getStore()->getWebsiteId();
		// ADDED BY M2SC END

		$shippingAmount = $order->getData('shipping_amount'); // in cents
		$shippingAmount = (int)($shippingAmount);
		$paymentMethod = $order->getPayment()->getMethod();
		$paymentType = $order->getPayment()->getType();

		$shippingAddress = $order->getShippingAddress();
		$billingAddress = $order->getBillingAddress();

		$customerNumber = $order->getCustomerId();

		$xml="<SALESORDERS>
		<SALESORDER>
			<CUSTOMER>
				<CUSTOMERGROUP>GHI</CUSTOMERGROUP>
				<CUSTOMERNUMBER>".$customerNumber."</CUSTOMERNUMBER>
			</CUSTOMER>

			<PA>
				<NAME1>".htmlspecialchars(substr($billingAddress->getName(),0,50))."</NAME1>
				<STREET>".htmlspecialchars($billingAddress->getStreetFull())."</STREET>
				<POSTALCODE>".$billingAddress->getPostcode()."</POSTALCODE>
				<CITY>".htmlspecialchars($billingAddress->getCity())."</CITY>
				<COUNTRY>".$billingAddress->getCountryId()."</COUNTRY>
				<LANGUAGE>NL</LANGUAGE>
				<FIRSTNAME>".htmlspecialchars($billingAddress->getFirstname())."</FIRSTNAME>
				<LASTNAME>".htmlspecialchars($billingAddress->getLastname())."</LASTNAME>
			</PA>

			<SA>
				<NATURALPERSON/>
				<NAME1>".htmlspecialchars(substr($shippingAddress->getName(),0,50))."</NAME1>
				<STREET>".htmlspecialchars($shippingAddress->getStreetFull())."</STREET>
				<POSTALCODE>".$shippingAddress->getPostcode()."</POSTALCODE>
				<CITY>".htmlspecialchars($shippingAddress->getCity())."</CITY>
				<COUNTRY>".$shippingAddress->getCountryId()."</COUNTRY>
				<LANGUAGE>NL</LANGUAGE>
				<FIRSTNAME>".htmlspecialchars($shippingAddress->getFirstname())."</FIRSTNAME>
				<PREFIX/>
				<LASTNAME>".htmlspecialchars($shippingAddress->getLastname())."</LASTNAME>
				<PHONENR>".htmlspecialchars($billingAddress->getTelephone())."</PHONENR>
				<MOBILEPHONE></MOBILEPHONE>
				<EMAIL>".htmlspecialchars($order->getCustomerEmail())."</EMAIL>
			</SA>

			<HEADER>
				<ORDERNUMBER>".$orderId."</ORDERNUMBER>
				<ORDERSTATUS>".$orderStatus."</ORDERSTATUS>
				<ORDERSTATE>".$orderState."</ORDERSTATE>
				<WEBSITEID>".$websiteId."</WEBSITEID>
				<STOREID>".$storeId."</STOREID>
				<STORENAME>".htmlspecialchars($storeName)."</STORENAME>
				<ORDERTYPE>IP-</ORDERTYPE>
				<ORDERTOTAL>".$orderTotal."</ORDERTOTAL>
				<ORDERVALUE>".$orderValue."</ORDERVALUE>
				<SHIPPINGAMOUNT>".$shippingAmount."</SHIPPINGAMOUNT>
				<SHIPPINGMETHOD>".$shippingMethod."</SHIPPINGMETHOD>
				<SHIPPINGDESCRIPTION>".htmlspecialchars($shippingDescription)."</SHIPPINGDESCRIPTION>
				<PAYMENTTYPE>".$paymentType."</PAYMENTTYPE>
				<PAYMENTMETHOD>".htmlspecialchars($paymentMethod)."</PAYMENTMETHOD>
			</HEADER>";

		$orderItems = $order->getItemsCollection();
		$xml.= "<POSITIONS>";
		foreach ($orderItems as $item)
		{
			if (!$item->isDummy())
			{
				$xml .= "<POSITION>";
				$sku = $this->getItemSku($item);
				$pos = strpos($sku, '-');
				if ($pos === false)
				{
					$xml .= "<PRODUCTNUMBER>".$sku."</PRODUCTNUMBER>";
					$xml .= "<VARIANTID></VARIANTID>";
				}
				else
				{
					$xml .= "<PRODUCTNUMBER>".$sku."</PRODUCTNUMBER>";
					$xml .= "<VARIANTID></VARIANTID>";
				}
				$xml .= "<QUANTITY>".(int)$item->getQtyOrdered()."</QUANTITY>";
				$xml .= "<PRICE>".(int)($item->getPrice()*100)."</PRICE>";
				$xml .= "</POSITION>";
			}
		}
		$xml .= "</POSITIONS>
		</SALESORDER>
</SALESORDERS>";



/*  COMMENTED BY M2SC
		$dir = Mage::getBaseDir().DS.'var'.DS.'emalo'.DS.'export'.DS;
		if (!file_exists($dir))
		{
			if (!mkdir($dir,0777,true))
			{
				throw new Exception ('failed to create dir, check your permission ');
				return FALSE;
			}
		}

		$xmlFile = $dir.$orderId.'.xml';
		$fp = fopen($xmlFile, 'w');
		fputs($fp,$xml);
		fclose($fp);
*/

		return $xml;
	}


	public function onSalesOrderSaveAfter(Varien_Event_Observer $observer)
	{
		$event = $observer->getEvent();
		$order = $event->getOrder();

		$incrementId = $order->getIncrementId();

		$status = $order->getStatus();
		$state = $order->getState();

		// Mage::log("Order #{$incrementId} saved (state: {$state})", Zend_Log::DEBUG, 'debug.log');
		if ($state == Mage_Sales_Model_Order::STATE_PROCESSING)
		{
			// Mage::log("Order #{$incrementId} saved (status: {$status})", Zend_Log::DEBUG, 'debug.log');
			if ($order->hasInvoices())
			{
				$invoicesPaid = true;
				foreach ($order->getInvoiceCollection() as $invoice)
				{
					if ($invoice->getState() !== Mage_Sales_Model_Order_Invoice::STATE_PAID)
					{
						// $invoiceId = $invoice->getId();
						// Mage::log("Invoice #{$invoiceId} not paid", Zend_Log::DEBUG, 'debug.log');
						$invoicesPaid = false;
					}
				}
				if ($invoicesPaid)
				{
					// Mage::log("Order #{$incrementId} saved and invoices paid (status: {$status})", Zend_Log::DEBUG, 'debug.log');
					$xml = $this->generateXml($order);

					try
					{
						$icUrl = Mage::getStoreConfig('emalo_options/export/emalourl');
						$icAccessArea = Mage::getStoreConfig('emalo_options/export/emaloAccessArea');
						$icCustomerNumber = Mage::getStoreConfig('emalo_options/export/emaloCustomerNumber');
						$icPassword = Mage::getStoreConfig('emalo_options/export/emaloPassword');	
						$result = '';
						$params = array(
								'sAccessArea' 		=> $icAccessArea, 
								'sCustomerNumber' 	=> $icCustomerNumber, 
								'sPassword' 		=> $icPassword, 
								'sMethod' 			=> "mfnlWebshopIn", 
								'sParams' 		 	=> $xml, 
								'sResult' 			=> $result
						);

						$client = new SoapClient($icUrl);
						$result = $client->gbCallCustomerBusinessLinkMethod($params);
					}
					catch(Exception $e)
					{
						echo $e->getMessage();
					}
				}
			}
		}
	}


	public function exportOrder()
	{
		if($this->validate())
		{
			$order = new Mage_Sales_Model_Order();
			$incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
			$order->loadByIncrementId($incrementId);
			$xml = $this->generateXml($order);

			try
			{
				$icUrl = Mage::getStoreConfig('emalo_options/export/emalourl');
				$icAccessArea = Mage::getStoreConfig('emalo_options/export/emaloAccessArea');
				$icCustomerNumber = Mage::getStoreConfig('emalo_options/export/emaloCustomerNumber');
				$icPassword = Mage::getStoreConfig('emalo_options/export/emaloPassword');	
				$result = '';
				$params = array(
						'sAccessArea' 		=> $icAccessArea, 
						'sCustomerNumber' 	=> $icCustomerNumber, 
						'sPassword' 		=> $icPassword, 
						'sMethod' 			=> "mfnlWebshopIn", 
						'sParams' 		 	=> $xml, 
						'sResult' 			=> $result
				);

				$client = new SoapClient($icUrl);
				$result = $client->gbCallCustomerBusinessLinkMethod($params);
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
		}
	}
	public function getItemSku($item)
	{
		if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
		{
			return $item->getProductOptionByCode('simple_sku');
		}
		return $item->getSku();
	}
	
	public function validate()
	{
		return true;
	}

	public function validate2()
	{
		$icAccessArea = Mage::getStoreConfig('emalo_options/export/emaloAccessArea');
		$icCustomerNumber = Mage::getStoreConfig('emalo_options/export/emaloCustomerNumber');
		
		$params = implode('',
		array(
					'sAccessArea' 		=> $icAccessArea, 
					'sCustomerNumber' 	=> $icCustomerNumber,
		));
		$signMac = Zend_Crypt_Hmac::compute('cBbTxKP1wGhaBDdXjL8Lc#DdvKan%!@Z#8AzN2nE!CJKqUXPZFiU', 'sha1', $params);
		$license = base64_encode(pack('H*',$signMac));
		$backendKey = trim(Mage::getStoreConfig('emalo_options/export/key'));
		if (strcmp($backendKey, $license) === 0)
		{
			return true;
		}
		return false;
	} 
	
	public function validate_admin_license()
	{
		if ($this->validate())
		{
			Mage::getSingleton('adminhtml/session')->addSuccess('Valid Emalo License');
		}
		else
		{
			Mage::getSingleton('adminhtml/session')->addError('Invalid Emalo License, please contact  E Marketing Diensten email: license@magento-extension.nl');
		}
	} 
}