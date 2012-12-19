<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro plugins
 */
class PremiumWebCart_Tracker extends Pap_Tracking_CallbackTracker {

    private $xml;

    /**
     * @return PremiumWebCart_Tracker
     */
    public function getInstance() {
        $tracker = new PremiumWebCart_Tracker();
        $tracker->setTrackerName("PremiumWebCart");
        return $tracker;
    }

    protected function registerCommission() {
        $this->debug("Start registering sales");
        if (Gpf_Settings::get(PremiumWebCart_Config::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION) == Gpf::YES) {
            $this->processWholeCartAsOneTransaction();
        } else {
            $this->processEachItemInCartSeparatly();
        }
    }

    private function processEachItemInCartSeparatly() {
        $order = $this->xml->order[0];
        $saleTracker = new Pap_Tracking_ActionTracker();

        foreach ($order->products[0] as $item) {
            if($item->getName() != 'product') {
                continue;
            }
            $sale = $saleTracker->createSale();
            $sale->setProductId((string)$item->productrowid);
            $sale->setTotalCost((string)$item->totalprice);
            $sale->setData2((string)$item->quantity);
            $sale->setData1((string)$this->xml->order[0]->customer[0]->email);
            $sale->setData3((string)$this->xml->order[0]->customer[0]->firstname . ' ' . (string)$this->xml->order[0]->customer[0]->lastname);
            $sale->setData4((string)$this->xml->order[0]->customer[0]->customerid);
            $sale->setOrderId((string)$order->orderuniqueid);

        }
        $this->registerAllSales($saleTracker);
    }

    private function processWholeCartAsOneTransaction() {
        $this->debug('PremiumWebCart - Process whole cart as one transaction');

        $order = $this->xml->order[0];
        $product = $order->products[0]->product[0];

        $saleTracker = new Pap_Tracking_ActionTracker();
        $sale = $saleTracker->createSale();

        $totalCost = 0;
        $productId = '';
        foreach ($order->products[0] as $item) {
            $totalCost += $item->totalprice;
            $productId .= (string)$item->name . ', ';
        }
        $sale->setOrderId((string)$order->orderuniqueid);
        $sale->setTotalCost($totalCost);
        $sale->setProductId(substr($productId, 0, -2));
        $this->registerAllSales($saleTracker);
    }


    public function checkStatus() {

        if ($this->getPaymentStatus() == "Accepted") {
            return true;
        }

        return false;
    }

    protected function readXmlData() {

        $request = new Gpf_Net_Http_Request();
        $request->setMethod('POST');
        $request->setUrl('https://www.secureinfossl.com/api/getOrderInfo.html');
        $request->setBody('merchantid=' . urlencode(Gpf_Settings::get(PremiumWebCart_Config::MERCHANT_ID))
        . '&signature=' . urlencode(Gpf_Settings::get(PremiumWebCart_Config::API_SIGNATURE))
        . '&orderid='.$_GET['order_unique_id']);

        $client = new Gpf_Net_Http_Client();
        $input = $client->execute($request)->getBody();
        $this->debug("Input get: " . $input);
        //echo $input;
        try {
            $xml = new SimpleXMLElement($input);
        } catch (Exception $e) {
            $this->setPaymentStatus("Failed");
            $this->debug('Wrong XML format.');
        }

        $this->xml = $xml;
    }

    public function readRequestVariables() {
        $this->setCookie($_GET['visitorId']);
        $this->readXmlData();
        $this->setPaymentStatus($this->xml->order[0]->orderstatus);
    }


}
?>
