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
class GoogleCheckout_Tracker extends Pap_Tracking_CallbackTracker {

    const NEW_ORDER_NOTIFICATION = 'new-order-notification';
    const CHARGE_AMOUNT_NOTIFICATION = 'charge-amount-notification';
    const REFUND_AMOUNT_NOTIFICATION = 'refund-amount-notification';
    const ORDER_STATE_CHANGE_NOTIFICATION = 'order-state-change-notification';
    const CANCELLED = 'CANCELLED';

    const RESPONSE_HANDLER_ERROR_LOG_FILE = 'googleerror.log';
    const RESPONSE_HANDLER_LOG_FILE = 'googlemessage.log';


    private $cartItems;
    private $merchantPrivateData;
    private $couponAmount;

    private function setMerchantPrivateData($data) {
        $this->merchantPrivateData = $data;
    }

    private function getMerchantPrivateData() {
        return $this->merchantPrivateData;
    }
    
    private function setCouponAmount($amount) {
        $this->couponAmount = $amount;
    }

    private function getCouponAmount() {
        return $this->couponAmount;
    }

    private function setCartItems($items) {
        $this->cartItems = $items;
    }

    private function getCartItems() {
        return $this->cartItems;
    }

    /**
     *
     * @return string
     */
    private function getMerchantId() {
        return Gpf_Settings::get(GoogleCheckout_Config::MERCHANT_ID);
    }

    /**
     *
     * @return string
     */
    private function getMerchantKey() {
        return Gpf_Settings::get(GoogleCheckout_Config::MERCHANT_KEY);
    }

    /**
     * @return GoogleCheckout_Tracker
     */
    public function getInstance() {
        $tracker = new GoogleCheckout_Tracker();
        $tracker->setTrackerName("GoogleCheckout");
        return $tracker;
    }

    protected function updateTransactionStatus($orderId, $status) {
        $this->debug('Updating transactions with orderid='.$orderId.' to status='.$status.' started...');
        $transactions = $this->getAllTransactionIdsWithOrderId($this->getOrderID(), true);
        foreach ($transactions as $transaction) {
            $transaction->setStatus($status);
            $transaction->save();
        }
        $this->debug('Updating transactions ended.');
    }
     
    protected function registerCommission() {
        $this->debug("Start registering sales");
        if (($this->getCartItems() != null) && ($this->getType() == self::NEW_ORDER_NOTIFICATION)) {
            if (Gpf_Settings::get(GoogleCheckout_Config::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION) == Gpf::YES) {
                $this->processWholeCartAsOneTransaction();
            } else {
                $this->processEachItemInCartSeparatly();
            }
        }
    }
    
    private function processEachItemInCartSeparatly() {
        $totalCost = 0;
        foreach ($this->getCartItems() as $item) {
            $totalCost += (float)$this->getXmlElementByName('unit-price',$item)*(int)$this->getXmlElementByName('quantity',$item);
        }
    	foreach ($this->getCartItems() as $item) {
            $saleTracker = new Pap_Tracking_ActionTracker();
            $sale = $saleTracker->createSale();
            $sale->setProductId((string)$this->getXmlElementByName('item-name',$item));
            $itemCost = (float)$this->getXmlElementByName('unit-price',$item)*(int)$this->getXmlElementByName('quantity',$item);
            if ($totalCost > 0) {
                $itemCouponAmount = $itemCost * $this->getCouponAmount()/$totalCost;
            } else {
                $itemCouponAmount = 0;
            }
            $sale->setTotalCost($itemCost - $itemCouponAmount);
            $sale->setData1((string)$this->getXmlElementByName('quantity',$item));
            $sale->setOrderId($this->getOrderID());

            $privateItemData = (string)$this->getXmlElementByName('merchant-private-item-data',$item);
            if ($privateItemData == '') {
                $privateItemData = $this->getMerchantPrivateData();
            }
            $cookie = $this->parseCookie(stripslashes($privateItemData));
            $this->setVisitorAndAccount($saleTracker, $this->getAffiliateID(), $this->getCampaignID(), $cookie);
            $this->registerAllSales($saleTracker);
        }
    }
    
    private function processWholeCartAsOneTransaction() {
        $saleTracker = new Pap_Tracking_ActionTracker();
        $sale = $saleTracker->createSale();
        
        $totalCost = 0;
        $productId = '';
        foreach ($this->getCartItems() as $item) {
            $totalCost += (float)$this->getXmlElementByName('unit-price',$item)*(int)$this->getXmlElementByName('quantity',$item);
            $productId .= (string)$this->getXmlElementByName('item-name',$item) . ', ';
        }
        $sale->setOrderId($this->getOrderID());
        $sale->setTotalCost($totalCost-$this->getCouponAmount());
        $sale->setProductId(substr($productId, 0, -2));
        $cookie = $this->parseCookie(stripslashes($this->getMerchantPrivateData()));
        $this->setVisitorAndAccount($saleTracker, $this->getAffiliateID(), $this->getCampaignID(), $cookie);
        $this->registerAllSales($saleTracker);
    }

    /**
     *
     * @param $orderId
     * @param $includeRefund
     * @return Gpf_DbEngine_Row_Collection<Pap_Common_Transaction>
     */
    private function getAllTransactionIdsWithOrderId($orderId, $includeRefund){
        $status = array (Pap_Common_Constants::STATUS_APPROVED, Pap_Common_Constants::STATUS_PENDING);
        $types = array(Pap_Common_Constants::TYPE_SALE, Pap_Common_Constants::TYPE_ACTION, Pap_Common_Constants::TYPE_LEAD);
        if ($includeRefund == true) {
            $types[] = Pap_Common_Constants::TYPE_REFUND;
        }

        $select = new Gpf_SqlBuilder_SelectBuilder();
         
        $select->select->addAll(Pap_Db_Table_Transactions::getInstance());
        $select->from->add(Pap_Db_Table_Transactions::getName());
        $select->where->add(Pap_Db_Table_Transactions::ORDER_ID, "=", $orderId);

        $select->where->add(Pap_Db_Table_Transactions::R_TYPE, "IN", $types);
        $select->where->add(Pap_Db_Table_Transactions::R_STATUS, "IN", $status);

        $transaction = new Pap_Common_Transaction();
        return $transaction->loadCollectionFromRecordset($select->getAllRows());
    }

    protected function refundChargeback() {
        $this->debug('Starting refund');
        $transactions = $this->getAllTransactionIdsWithOrderId($this->getOrderID(), false);
        foreach ($transactions as $parentTransaction) {
            $this->debug('Refunding transaction with id=' . $parentTransaction->getId());
            $parentTransaction->refundChargeback(Pap_Db_Transaction::TYPE_REFUND, '', $this->getOrderID());
        }
        $this->debug('Refund finished');
    }

    protected function verifyMerchant($Gresponse) {
        $Gresponse->SetLogFiles(self::RESPONSE_HANDLER_ERROR_LOG_FILE, self::RESPONSE_HANDLER_LOG_FILE, GoogleCheckout_GoogleLog::L_ALL);

        $Gresponse->SetMerchantAuthentication($this->getMerchantId(), $this->getMerchantKey());

        $status = $Gresponse->HttpAuthentication();
        if(! $status) {
            $this->debug('authentication failed');
            return false;
        }
        return true;
    }

    public function checkStatus() {
        $Gresponse = new GoogleCheckout_GoogleResponse($this->getMerchantId(), $this->getMerchantKey());

        if ($this->verifyMerchant($Gresponse) === false) {
            return false;
        }

        $this->sendBackAck($Gresponse);
        if (($this->getType() == self::NEW_ORDER_NOTIFICATION)) {
            $this->setStatus(Pap_Common_Constants::STATUS_PENDING);
            $this->debug('New order notification, continue to register order...');
            return true;
        }
        if (($this->getType() == self::CHARGE_AMOUNT_NOTIFICATION)) {
            $this->setStatus(Pap_Common_Constants::STATUS_APPROVED);
            $this->debug('Charge order, continue to change status of transactions...');
            $this->updateTransactionStatus($this->getOrderID(), $this->getStatus());
            return false;
        }
        if (($this->getType() == self::ORDER_STATE_CHANGE_NOTIFICATION) && ($this->getStatus()==Pap_Common_Constants::STATUS_DECLINED)) {
            $this->debug('Decline order, continue to change status of transactions...');
            $this->updateTransactionStatus($this->getOrderID(), $this->getStatus());
            return false;
        }
        if (($this->getType() == self::REFUND_AMOUNT_NOTIFICATION)) {
            $this->refundChargeback();
            return false;
        }

        return false;
    }

    protected function sendBackAck(GoogleCheckout_GoogleResponse $Gresponse) {
        $this->debug("  Sending back ack status");
        $Gresponse->SendAck(false);
    }

    protected function readXmlData() {
        $post_data = file_get_contents('php://input');
        if (get_magic_quotes_gpc()) {
            $post_data = stripslashes($post_data);
        }
        return $post_data;
    }

    protected function getXmlElementByName($name, $elements) {
        foreach ($elements as $element) {
            if ($element->getName() == $name) {
                return $element;
            }
        }
        return false;
    }

    public function readRequestVariables() {
        $input = $this->readXmlData();

        $this->debug("Input get: " . $input);
        try {
            $xml = new SimpleXMLElement($input);
        } catch (Exception $e) {
            $this->setPaymentStatus("Failed");
            $this->debug('Wrong XML format.');
        }

        $this->setType((string)$xml->getName());
        $this->setTransactionID((string)$this->getXmlElementByName('serial-number',$xml->attributes()));
        $this->setCartItems(null);
        if ($this->getType() == self::NEW_ORDER_NOTIFICATION) {
            $cart = $this->getXmlElementByName('shopping-cart',$xml);
            $this->setCartItems($this->getXmlElementByName('items',$cart));
            $this->setMerchantPrivateData((string)$this->getXmlElementByName('merchant-private-data',$cart));
            $this->setCouponAmount($this->getCouponAmountFromXml($xml));
        }
        if ($this->getType() == self::REFUND_AMOUNT_NOTIFICATION) {
            $this->setTotalCost((float)$this->getXmlElementByName('total-refund-amount',$xml));
        }
        if ($this->getType() == self::ORDER_STATE_CHANGE_NOTIFICATION) {
            if ((string)$this->getXmlElementByName('new-financial-order-state',$xml) == self::CANCELLED) {
                $this->debug("Cancel this order" . $this->getOrderID());
                $this->setStatus(Pap_Common_Constants::STATUS_DECLINED);
            }
        }
    }

    /**
     * @param SimpleXMLElement
     * @return float
     */
    private function getCouponAmountFromXml(SimpleXMLElement $xml) {
        if(!$orderAdjustment = $this->getXmlElementByName('order-adjustment', $xml)) {
            return 0;
        }
        if (!$merchantCodes = $this->getXmlElementByName('merchant-codes', $orderAdjustment)) {
            return 0;
        }
        if (!$couponAdjustment = $this->getXmlElementByName('coupon-adjustment', $merchantCodes)) {
            return 0;
        }
        if (!$couponAmount = $this->getXmlElementByName('applied-amount', $couponAdjustment)) {
            return 0;
        }
        return (float)$couponAmount;
    }

    public function getOrderID() {
        $sernum = explode('-',$this->getTransactionID());
        return $sernum[0];
    }
}
?>
