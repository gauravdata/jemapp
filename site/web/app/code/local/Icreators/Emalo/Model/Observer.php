<?php
 
class Icreators_Emalo_Model_Observer
{

	public function generateXml(Mage_Sales_Model_Order $order) 
	{
		
		$orderId  = $order->getIncrementId();
		$shippingAmount = $order->getData('shipping_amount'); // in cents
		$shippingAmount = (int)($shippingAmount);
		$paymentMethod 	= $order->getPayment()->getMethod();
		$paymentType	= $order->getPayment()->getType();

		$shippingAddress = $order->getShippingAddress();
		$billingAddress = $order->getBillingAddress();

		$customerNumber = $order->getCustomerId();

		$xml="<SALESORDERS>
		<SALESORDER>
			<CUSTOMER>
				<CUSTOMERGROUP>GHI</CUSTOMERGROUP>
				<CUSTOMERNUMBER>".$customerNumber."</CUSTOMERNUMBER>
			</CUSTOMER>

			<SA>
				<NAME1>".substr($billingAddress->getName(),0,50)."</NAME1>
				<STREET>".$billingAddress->getStreetFull()."</STREET>
				<POSTALCODE>".$billingAddress->getPostcode()."</POSTALCODE>
				<CITY>".$billingAddress->getCity()."</CITY>
				<COUNTRY>".$billingAddress->getCountryId()."</COUNTRY>
				<LANGUAGE>NL</LANGUAGE>
				<FIRSTNAME>".$billingAddress->getFirstname()."</FIRSTNAME>
				<LASTNAME>".$billingAddress->getLastname()."</LASTNAME>
				<PHONENR>".$billingAddress->getTelephone()."</PHONENR>
				<MOBILEPHONE></MOBILEPHONE>
				<EMAIL>".$order->getCustomerEmail()."</EMAIL>
			</SA>

			<PA>
				<NATURALPERSON/>
				<NAME1>".substr($shippingAddress->getName(),0,50)."</NAME1>
				<STREET>".$shippingAddress->getStreetFull()."</STREET>
				<POSTALCODE>".$shippingAddress->getPostcode()."</POSTALCODE>
				<CITY>".$shippingAddress->getCity()."</CITY>
				<COUNTRY>".$shippingAddress->getCountryId()."</COUNTRY>
				<LANGUAGE>NL</LANGUAGE>
				<FIRSTNAME>".$shippingAddress->getFirstname()."</FIRSTNAME>
				<PREFIX/>
				<LASTNAME>".$shippingAddress->getLastname()."</LASTNAME>
			</PA>

			<HEADER>
				<ORDERNUMBER>".$orderId."</ORDERNUMBER>
				<ORDERTYPE>IP-</ORDERTYPE>
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
		
		return $xml;
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
						"sAccessArea" 		=> $icAccessArea, 
						'sCustomerNumber' 	=> $icCustomerNumber, 
						'sPassword' 		=> $icPassword, 
						'sMethod' 			=> "mfnlWebshopIn",
						'sParams'		 	=> $xml, 
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