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
            Mage::exception('Empty order');
        }

        $order = new Mage_Sales_Model_Order();
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

            ini_set('default_socket_timeout', 60);
            $client = new SoapClient($icUrl);
            $result = $client->gbCallCustomerBusinessLinkMethod($params);
        }
        catch(Exception $e)
        {
            Mage::log('Exception for event order ' . $e->getMessage(), null, 'emalo.log');
        }
    }

    protected function generateXml(Mage_Sales_Model_Order $order)
    {
        $orderId  = $order->getIncrementId();
        Mage::log('Regenerate xml for order #' . $orderId, null, 'emalo.log');

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
				<NAME1>".substr($billingAddress->getName(),0,50)."</NAME1>
				<STREET>".$billingAddress->getStreetFull()."</STREET>
				<POSTALCODE>".$billingAddress->getPostcode()."</POSTALCODE>
				<CITY>".$billingAddress->getCity()."</CITY>
				<COUNTRY>".$billingAddress->getCountryId()."</COUNTRY>
				<LANGUAGE>NL</LANGUAGE>
				<FIRSTNAME>".$billingAddress->getFirstname()."</FIRSTNAME>
				<LASTNAME>".$billingAddress->getLastname()."</LASTNAME>
			</PA>

			<SA>
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
				<PHONENR>".$billingAddress->getTelephone()."</PHONENR>
				<MOBILEPHONE></MOBILEPHONE>
				<EMAIL>".$order->getCustomerEmail()."</EMAIL>
			</SA>

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

}