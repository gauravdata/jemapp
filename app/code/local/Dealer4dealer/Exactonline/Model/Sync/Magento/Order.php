<?php
class Dealer4dealer_Exactonline_Model_Sync_Magento_Order extends Dealer4dealer_Exactonline_Model_Sync_Magento_Abstract
{
    /**
     * @var float
     */
    protected $_highestTaxPercentage;

    /**
     * @var string
     */
    protected $_country;

    /**
     * @var string
     */
    protected $_category = 'SalesOrders';

    /**
     *  Init all classes needed
     *
     * @TODO Fix dependencies and create DI
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Synchronize all orders based on the provided filters.
     *
     * @param array $filter
     * @param string $lastSyncDate
     * @return bool
     */
    public function synchronizeOrders($filter, $lastSyncDate)
    {
        $success = true;
        reset($filter);

        $orderCollection = $this->_getCollection($filter, $lastSyncDate);

        foreach($orderCollection as $order) {

            if (!$this->_canContinue()) {
                break;
            }

            /* @var $order Mage_Sales_Model_Order */
            $order->load($order->getId());

            $this->_log->writeLog('Starting synchronising SalesOrder '.$order->getRealOrderId().' ('.$order->getId().')',$this->_category);

            // Check if the order should be synchronised
            if($this->_shouldBeSynchronised($order)) {

                $xml = $this->getOrderBulkXml(array($order));
                $response = $this->_exactConnector->sendXML('SalesOrders',$xml);

                if($this->_xmlAnalyzer->analyseSalesOrders($response['ch'],$response['result'])) {
                    $this->_logSuccess($order,$response['ch'],$response['result']);
                }else {
                    $this->_logError($order,$response['ch'],$response['result']);
                    $success = false;
                }

                if(!$this->_isDebugMode()) {
                    // Save the sync date
                    $this->_settings->saveSetting("ordersyncdate", $order->getUpdatedAt());
                }

                // Maximum 20 requests per second to Exact Online
                usleep(1000*$this->_settings->getSetting("sleep_milliseconds"));

            }else {
                $this->_log->writeLog('SalesOrder '.$order->getRealOrderId().' ('.$order->getId().') is already synchronised',$this->_category);
            }
        }

        return $success;
    }

    /**
     * Build XML for provided order items
     *
     * @param array $ordersArray
     * @return mixed
     */
    private function getOrderBulkXml($ordersArray)
    {
        $xmlResult = $this->_xmlTool->getXmlTemplate("SalesOrderBulk");

        /** @var $order Mage_Sales_Model_Order */
        foreach($ordersArray as $order) {

            // Reset vars
            $this->_highestTaxPercentage = 0;

            if ($order->getShippingAddress()) {
                $this->_country = $order->getShippingAddress()->getCountry();
            } else {
                $this->_country = $order->getBillingAddress()->getCountry();
            }

            /** @var SimpleXMLElement $xmlOrder */
            $xmlOrder = $xmlResult->SalesOrders->addChild('SalesOrder');

            $xmlOrder->addChild('OrderDate', date('Y-m-d',strtotime($order->getCreatedAt())));
            $xmlOrder->addChild('DeliveryDate', date('Y-m-d',strtotime($order->getCreatedAt())));
            $xmlOrder->addChild('Description', $this->_getOrderDescription($order));
            $xmlOrder->addChild('YourRef', $order->getIncrementId());

            try{
                $debtorId = $this->getDebtorId($order);
            } catch(Exception $e) {
                $this->_log->writeLog('Error while getting the debtorID: '.$e->getMessage(),$this->_category);
                continue;
            }

            $xmlOrder->addChild('OrderedBy')->addAttribute('code', $debtorId);
            $xmlOrder->addChild('DeliverTo')->addAttribute('code', $debtorId);
            $xmlOrder->addChild('InvoiceTo')->addAttribute('code', $debtorId);

            // Look for the warehouse based on the store id
            $warehouseCode = $this->getWarehouseCode($order);
            if ($warehouseCode != '') {
                $xmlOrder->addChild('Warehouse')->addAttribute('code', $warehouseCode);
            }

            /**
             * ONE PLACE TO DETERMINE THE ALTERNATIVE ADDRESS
             * WHEN ADDING USER INPUT LIKE FIRSTNAME OR COMPANY, USE TRIM() ON ALL OF THEM
             */
            if($transsmartAddress = $this->checkForTransmartAddress($order)) {
                $street = $this->getStreetFromAddress($transsmartAddress);

                $addressLine1 = htmlspecialchars($street, ENT_XML1, 'UTF-8');
                $addressLine2 = htmlspecialchars($transsmartAddress->getCompany(), ENT_XML1, 'UTF-8');
                $addressLine3 = $transsmartAddress->getTranssmartServicepointId();

                $deliveryAddress = $xmlOrder->addChild('DeliveryAddress');
                $deliveryAddress->addAttribute('insert', "true");
                $deliveryAddress->addChild('AddressLine1', $addressLine1);
                $deliveryAddress->addChild('AddressLine2', $addressLine2);
                $deliveryAddress->addChild('AddressLine3', $addressLine3);
                $deliveryAddress->addChild('PostalCode', $transsmartAddress->getPostcode());
                $deliveryAddress->addChild('City', $transsmartAddress->getCity());
                $deliveryAddress->addChild('Phone', $transsmartAddress->getTelephone());
                $deliveryAddress->addChild('Country')->addAttribute('code',$transsmartAddress->getCountry());

                $shippingMethod = $this->_settings->getSetting('shipping_method_servicepoint');
                $xmlOrder->addChild('ShippingMethod')->addAttribute('code', $shippingMethod);

            } elseif($address = $order->getShippingAddress()) {

                $addressLine1 = $this->getStreetFromAddress($address);
                $addressLine2 = $this->getCustomerName($address);

                $deliveryAddress = $xmlOrder->addChild('DeliveryAddress');
                $deliveryAddress->addAttribute('insert', "true");
                $deliveryAddress->addChild('AddressLine1', $addressLine1);
                $deliveryAddress->addChild('AddressLine2', $addressLine2);
                $deliveryAddress->addChild('PostalCode', $address->getPostcode());
                $deliveryAddress->addChild('City', $address->getCity());
                $deliveryAddress->addChild('Phone', $address->getTelephone());
                $deliveryAddress->addChild('Country')->addAttribute('code',$address->getCountry());

            }

            if($this->_settings->getSetting($order->getPayment()->getMethod()) != '') {
                $paymentCondition = $this->_settings->getSetting($order->getPayment()->getMethod());
            }else {
                $paymentCondition = $this->_settings->getSetting('payment_condition');
            }

            $xmlOrder->PaymentCondition['code'] = $paymentCondition;

            if($order->getOrderCurrencyCode() != '' && $order->getOrderCurrencyCode() != 'EUR') {
                $xmlOrder->ForeignAmount->Currency['code'] = $order->getOrderCurrencyCode();
            }

            $deliveryDate = date('Y-m-d',strtotime($order->getUpdatedAt()));
            foreach($order->getAllItems() as $item) {
                $this->addOrderInvoice($item, $xmlOrder, $deliveryDate, $order);
            }

            // Add shipping costs
            $shippingAmount = $order->getBaseShippingAmount();
            if($shippingAmount > 0) {

                if($this->_taxCalculator->isIncludingAmount()) {
                    $shippingAmount += $this->_taxCalculator->getShippingBtwAmount($order);
                }

                $shippingTaxCode = $this->_settings->getSetting('shipping_tax_percent');
                if(is_null($shippingTaxCode) || strtolower($shippingTaxCode) == 'null') {
                    $shippingTaxCode = $this->_taxCalculator->getBtwCodeFromPercentage($this->_highestTaxPercentage, $this->_country);
                }

                $skuShipment = $this->getShipmentSku();

                $salesOrderLine = $xmlOrder->addChild('SalesOrderLine');
                $salesOrderLine->addChild('Item')->addAttribute('code', $skuShipment);
                $salesOrderLine->addChild('Quantity','1');
                $salesOrderLine->addChild('DeliveryDate', $deliveryDate);
                //$salesOrderLine->addChild('Note')->addCData('Verzendkosten');
                $salesOrderLineNetPrice = $salesOrderLine->addChild('NetPrice');
                $salesOrderLineNetPrice->addChild('VAT');
                $salesOrderLineNetPrice->VAT['code'] = $shippingTaxCode;
                $salesOrderLineNetPrice->addChild('Value', $shippingAmount);
                $salesOrderLineNetPrice->addChild('Currency');
                $salesOrderLineNetPrice->Currency['code'] = $this->_settings->getSetting("currency");

                if($this->_settings->getSetting('use_costcenter_costunit')=='1') {
                    $costCenterCode = $this->_getCostCenter($order);
                    $costUnitCode = $this->_getCostUnit($order);

                    $costCenter = $salesOrderLine->addChild('Costcenter');
                    $costCenter->addAttribute('code', $costCenterCode['code']);
                    $costCenter->addChild('Description', $costCenterCode['label']);

                    $unit = $salesOrderLine->addChild('Costunit');
                    $unit->addAttribute('code', $costUnitCode['code']);
                    $unit->addChild('Description', $costUnitCode['label']);
                }
            }

            // Add afterpaycosts
            $paymentfee = $order->getBaseBuckarooFee();
            if($paymentfee > 0) {


                $paymentTaxCode = $this->_settings->getSetting('vatcode_payment_fee');
                if(is_null($paymentTaxCode) || strtolower($paymentTaxCode) == 'null') {
                    $paymentTaxCode = $this->_taxCalculator->getBtwCodeFromPercentage($this->_highestTaxPercentage, $this->_country);
                }

                $salesOrderLine = $xmlOrder->addChild('SalesOrderLine');
                $salesOrderLine->addChild('Item')->addAttribute('code', $this->_settings->getSetting('sku_payment_fee'));
                $salesOrderLine->addChild('Quantity','1');
                $salesOrderLine->addChild('DeliveryDate', $deliveryDate);
                //$salesOrderLine->addChild('Note')->addCData('Verzendkosten');
                $salesOrderLineNetPrice = $salesOrderLine->addChild('NetPrice');
                $salesOrderLineNetPrice->addChild('VAT');
                $salesOrderLineNetPrice->VAT['code'] = $paymentTaxCode;
                $salesOrderLineNetPrice->addChild('Value', $paymentfee);
                $salesOrderLineNetPrice->addChild('Currency');
                $salesOrderLineNetPrice->Currency['code'] = $this->_settings->getSetting("currency");

                if($this->_settings->getSetting('use_costcenter_costunit')=='1') {
                    $costCenterCode = $this->_getCostCenter($order);
                    $costUnitCode = $this->_getCostUnit($order);

                    $costCenter = $salesOrderLine->addChild('Costcenter');
                    $costCenter->addAttribute('code', $costCenterCode['code']);
                    $costCenter->addChild('Description', $costCenterCode['label']);

                    $unit = $salesOrderLine->addChild('Costunit');
                    $unit->addAttribute('code', $costUnitCode['code']);
                    $unit->addChild('Description', $costUnitCode['label']);
                }
            }

            $awObject = current($order->getAwStorecredit());
            if($awObject && $awObject->getBaseStorecreditAmount())
            {
                $baseDiscountAmount = abs($awObject->getBaseStorecreditAmount());
            } else {
                $baseDiscountAmount = 0;
            }
            // Toevoegen totale korting o.b.v. module in Magento

            if ($baseDiscountAmount > 0) {
                $orderDiscount = $xmlOrder->addChild('EntryDiscount');
                $orderDiscount->addChild('AmountInclVAT', round($baseDiscountAmount,2));
            }
        }

        if($this->_isDebugMode()) {
            $this->_settings->saveSetting('debug_order_xml_sent',$xmlResult->asXML());
        }

        return $xmlResult->asXML();
    }

    private function getStreetFromAddress($shippingAddress)
    {
        $streetaddress = '';
        if ($shippingAddress->getStreet(1) != '') {
            $streetaddress = trim($shippingAddress->getStreet(1));
        }
        if ($shippingAddress->getStreet(2)) {
            $streetaddress = $streetaddress.' '.trim($shippingAddress->getStreet(2));
        }
        if ($shippingAddress->getStreet(3)) {
            $streetaddress = $streetaddress.' '.trim($shippingAddress->getStreet(3));
        }

        $streetaddress = str_replace("'", "", $streetaddress);

        return $streetaddress;
    }

    private function getCustomerName($shippingAddress) {
        $name = trim($shippingAddress->getFirstname());
        $name .= (trim($shippingAddress->getMiddlename()) != "") ? " " . trim($shippingAddress->getMiddlename()) : null;
        $name .= (trim($shippingAddress->getLastname()) != "") ? " " . trim($shippingAddress->getLastname()) : null;

        return $name;
    }

    private function checkForTransmartAddress($order) {
        $addresses = $order->getAddressesCollection();
        foreach($addresses as $address) {
            if($address->getAddressType() == 'transsmart_pickup') {
                return $address;
            }
        }
        return false;
    }

    /**
     * Add order line to the XML
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param SimpleXMLElement $xmlOrder
     * @param string $deliveryDate
     * @param Mage_Sales_Model_Order $order
     * @return null
     */
    private function addOrderInvoice($item, $xmlOrder, $deliveryDate, $order)
    {
        /**
         * Children of configurable products should never be added
         * to the order. They have no price and are not relevant to the
         * order.
         */
        if (!$this->_canAddItem($item)) {
            return null;
        }

        if($this->canShowPriceInfo($item)) {

            // Calculate the price
            if ($this->_taxCalculator->isIncludingAmount()) {
                $basePrice = $item->getBasePriceInclTax();
            } else {
                $basePrice = $item->getBasePrice();
            }

            $baseDiscountAmount = abs($item->getBaseDiscountAmount());

            if ($baseDiscountAmount > 0) {

                // Remove tax when amounts should be booked excluding tax.
                if (!$this->_taxCalculator->isIncludingAmount()) {
                    $baseDiscountAmount = $this->removeTax($baseDiscountAmount, $item->getTaxPercent());
                }

                // Calculate discount per item ordered and remove from base price
                $basePrice = $basePrice - ($baseDiscountAmount / $item->getQtyOrdered());
            }
        } else {
            $basePrice = 0;
        }

        // Log the highest tax percent to use for shipping.
        $this->_highestTaxPercentage = max($item->getTaxPercent(), $this->_highestTaxPercentage);

        // Get the tax code
        $taxCode = $this->_taxCalculator->getBtwCodeFromPercentage($item->getTaxPercent(), $this->_country);
        $sku = $this->_getSkuFromItem($item);

        $salesOrderLine = $xmlOrder->addChild('SalesOrderLine');
        $salesOrderLine->addChild('Item')->addAttribute('code', $sku);
        $salesOrderLine->addChild('Quantity',$item->getQtyOrdered());
        $salesOrderLine->addChild('DeliveryDate',$deliveryDate);
        $salesOrderLine->addChild('Note')->addCData($item->getProductName());
        $salesOrderLine->addChild('NetPrice');

        $salesOrderLine->NetPrice->addChild('Value', round($basePrice,2));
        $salesOrderLine->NetPrice->addChild('Currency');
        $salesOrderLine->NetPrice->Currency['code'] = $this->_settings->getSetting("currency");
        $salesOrderLine->NetPrice->addChild('VAT');
        $salesOrderLine->NetPrice->VAT['code'] = $taxCode;

        if($this->_settings->getSetting('use_costcenter_costunit')=='1') {
            $costCenterCode = $this->_getCostCenter($order);
            $costUnitCode = $this->_getCostUnit($order);

            $costCenter = $salesOrderLine->addChild('Costcenter');
            $costCenter->addAttribute('code', $costCenterCode['code']);
            $costCenter->addChild('Description', $costCenterCode['label']);

            $unit = $salesOrderLine->addChild('Costunit');
            $unit->addAttribute('code', $costUnitCode['code']);
            $unit->addChild('Description', $costUnitCode['label']);
        }
    }

    /**
     * Add successfully synchronized order to log.
     *
     * @param Mage_Sales_Model_Order $order
     * @param $ch
     * @param SimpleXMLElement $response
     */
    private function _logSuccess($order, $ch, $response)
    {
        $xml = $this->_xmlTool->strToXml($response);

        if($this->_isDebugMode()) {
            $this->_settings->saveSetting('debug_order_xml_received',$xml->asXML());
        }

        // Get the message from the XML response
        $exactResult = $this->_xmlAnalyzer->getOrderXmlResponseMessage($xml,'SalesOrder');

        /** @var Dealer4dealer_Exactonline_Model_Log_Order $orderLog */
        $orderLog = Mage::getModel('exactonline/log_order')->load($order->getId(),'order_id');
        $orderLog->setOrderId($order->getId());
        $orderLog->setStatusMessage((string)$exactResult['Description']);
        $orderLog->setLastSync(date('Y-n-j H:i:s'));
        $orderLog->setRawXmlResponse((string)$response);
        $orderLog->setState(1);
        $orderLog->setExactId((string)$exactResult['Exact_id']);
        $orderLog->save();

        $this->_log->writeLog('Order '.$order->getRealOrderId().'('.$order->getId().') successfull synchronized: '.(string)$exactResult['Description']);
    }

    /**
     * Add failed synchronized order to log.
     *
     * @param Mage_Sales_Model_Order $order
     * @param $ch
     * @param SimpleXMLElement $response
     */
    private function _logError($order,$ch,$response)
    {
        $xml = $this->_xmlTool->strToXml($response);

        if($this->_isDebugMode()) {
            $this->_settings->saveSetting('debug_order_xml_received',$xml->asXML());
        }

        // Get the message from the XML response
        $exactResult = $this->_xmlAnalyzer->getOrderXmlResponseMessage($xml,'SalesOrder');

        /** @var Dealer4dealer_Exactonline_Model_Log_Order $orderLog */
        $orderLog = Mage::getModel('exactonline/log_order')->load($order->getId(),'order_id');
        $orderLog->setOrderId($order->getId());
        $orderLog->setStatusMessage((string)$exactResult['Description']);
        $orderLog->setLastSync(date('Y-n-j H:i:s'));
        $orderLog->setRawXmlResponse((string)$response);
        $orderLog->setState(0);
        $orderLog->setExactId((string)$exactResult['Exact_id']);
        $orderLog->save();

        $this->_log->writeLog('Order '.$order->getRealOrderId().'('.$order->getId().') synchronize failed: '.(string)$exactResult['Description']);
    }

    /**
     * Check the log to make sure the provided order
     * should be synchronized to Exact Online.
     *
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    private function _shouldBeSynchronised($order)
    {
        if($this->_isDebugMode()) {
            return true;
        }

        /** @var Dealer4dealer_Exactonline_Model_Log_Order $log */
        $log = Mage::getModel('exactonline/log_order')->load($order->getId(),'order_id');

        if($log->getId()) {

            // Check if we need to synchronize failed orders
            if($this->_settings->getSetting('resync_failed_orders')=='1') {
                // Never resync successfull orders
                if((bool)$log->getState()) {
                    return false;
                }else {
                    // Allow resyncing of a failed order
                    $this->_log->writeLog('Resyncing order '.$order->getRealOrderId().'('.$order->getId().')');
                    return true;
                }
            }else {
                return false;
            }
        }
        return true;
    }

    /**
     * Is the connector in debug mode?
     *
     * @return bool
     */
    protected function _isDebugMode()
    {
        $debugMode = $this->_settings->getSetting('debug_mode');

        if($debugMode == '1') {
            return true;
        }

        return false;
    }

    /**
     * Get a collection of orders to synchronize.
     *
     * @param array $filter
     * @param string $lastSyncDate
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function _getCollection($filter, $lastSyncDate)
    {
        $orderCollection = Mage::getModel('sales/order')->getCollection();
        $orderCollection->addFieldToSelect('entity_id');
        $orderCollection->addFieldToSelect('increment_id');

        if(!$this->_isDebugMode()) {
            //filter out articles we want to synchronize
            while(list($key, $value) = each($filter)){
                $orderCollection->addAttributeToFilter($key, $value);
            }

            /**
             *  Support for orders with a different status based on the payment method.
             *  E.g. Pending orders with checkmo as payment method.
             */
            $paymentMethods = Mage::getSingleton('core/resource')->getTableName('sales/order_payment');
            $specialStatus  = explode(',', $this->_settings->getSetting('special_order_sync_status'));
            $specialPayment = explode(',', $this->_settings->getSetting('special_order_sync_paymentmethod'));

            if (count($specialStatus) && $specialStatus[0] != '' && count($specialPayment) && $specialPayment[0] != '') {
                $orderCollection
                    ->getSelect()
                    ->joinLeft(array('payment'=>$paymentMethods),'`main_table`.`entity_id` = `payment`.`parent_id`',array('method'))
                    ->orWhere('(`main_table`.`status` IN(?)',$specialStatus)
                    ->where('`main_table`.`updated_at` >=?', $lastSyncDate)
                    ->where('`payment`.`method` IN (?))',$specialPayment);
            }

            // Set the order from
            $orderCollection->setOrder('updated_at', 'asc');
            $orderCollection->setPageSize($this->_settings->getSetting('collection_limit'));
        }else {

            $orderId = $this->_settings->getSetting('debug_order_id');
            $orderCollection->addFieldToFilter('entity_id', $orderId);
        }

        return $orderCollection;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return int
     */
   /* public function getDebtorId($order)
    {
        if($order->getCustomerIsGuest()) {
            return $this->_settings->getSetting("code_verzameldebiteur_gasten");
        } else {

            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());

            if ($customer->getData(Dealer4dealer_Exactonline_Model_Sync_Magento_Customer::DEBTOR_ATTRIBUTE)) {
                return $customer->getData(Dealer4dealer_Exactonline_Model_Sync_Magento_Customer::DEBTOR_ATTRIBUTE);
            }
        }

        return ($order->getCustomerId() + $this->_settings->getSetting("klantid_range"));
    }*/

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getWarehouseCode($order)
    {
        $storeId = $order->getStoreId();

        $warehouseCode = $this->_settings->getSetting('order_warehouse_' . $storeId);

        if ($warehouseCode != '') {
            return $warehouseCode;
        }

        return $this->_settings->getSetting('default_warehouse');
    }
}