<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *`
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro plugins
 */
class Ejunkie_Tracker extends Pap_Tracking_CallbackTracker {

    protected $reasonCode;
    protected $parentTransId;

    protected function getParentTransId() {
        return $this->parentTransId;
    }

    protected function setParentTransId($value) {
        $this->parentTransId = $value;
    }

    private function getTransactionIdFromOrderId($orderId){
        $transaction = new Pap_Common_Transaction();
        $output = $transaction->getFirstRecordWith(Pap_Db_Table_Transactions::ORDER_ID, $orderId, array(Pap_Common_Constants::STATUS_APPROVED, Pap_Common_Constants::STATUS_PENDING));
        return $output->getId();
    }

    /**
     * @return Ejunkie_Tracker
     */
    public function getInstance() {
        $tracker = new Ejunkie_Tracker();
        $tracker->setTrackerName("Ejunkie");
        return $tracker;
    }

    protected function refundChargeback() {
        $transaction = new Pap_Common_Transaction();
        $transaction->processRefundChargeback($this->getTransactionIdFromOrderId($this->getParentTransId()), Pap_Db_Transaction::TYPE_REFUND, '', 
            $this->getOrderID(), 0, true);
    }

    public function checkStatus() {
        return true;
    }


    protected function discountFromTotalcost ($totalcost, $value) {
        if (($value != '') && (is_numeric($value))) {
            return $totalcost - $value;
        }
        return $totalcost;
    }
    
    protected function computeTotalCost(Pap_Tracking_Request $request) {
        if ($request->getPostParam('mc_gross') != '') {
            return $this->adjustTotalCost($request->getPostParam('mc_gross'), $request);
        }
        $totalCost = 0;
        $counter = 1;
        $amount = $this->adjustTotalCost($request->getPostParam('mc_gross_' . $counter), $request, $counter);
        while ($amount != '') {
            $totalCost += $amount;
            $counter ++;
            $amount = $this->adjustTotalCost($request->getPostParam('mc_gross_' . $counter), $request, $counter);
        }
        return $totalCost;
    }
    
    /**
     *  @return Pap_Tracking_Requestw
     */
    protected function getRequestObject() {
        return Pap_Contexts_Action::getContextInstance()->getRequestObject();
    }

    public function readRequestVariables() {
        $request = $this->getRequestObject();
        $cookieValue = stripslashes($request->getPostParam('custom'));
        try {
            $customSeparator = Gpf_Settings::get(Ejunkie_Config::CUSTOM_SEPARATOR);
            if ($customSeparator != '') {
                $explodedCookieValue = explode($customSeparator, $cookieValue, 2);
                if (count($explodedCookieValue) == 2) {
                    $cookieValue = $explodedCookieValue[1];
                }
            }
        } catch (Gpf_Exception $e) {
        }
        $this->setCookie($cookieValue);
        $this->setTotalCost($this->computeTotalCost($request));
        $this->setTransactionID($request->getPostParam('txn_id'));
        $this->setSubscriptionID($request->getRequestParameter('subscr_id'));
        $this->setProductID($request->getPostParam('item_number'));
        $this->setType($request->getPostParam('txn_type'));
        $this->setPaymentStatus($request->getPostParam('payment_status'));
        $this->setEmail($request->getPostParam('payer_email'));
        $this->setParentTransId($request->getPostParam('parent_txn_id'));
        $this->setCurrency($request->getPostParam('mc_currency'));
    }

    public function isRecurring() {
        if($this->getType() == 'subscr_payment') {
            return true;
        }

        return false;
    }
    
 	protected function allowUseRecurringCommissionSettings() {
    	return (Gpf_Settings::get(Ejunkie_Config::USE_RECURRING_COMMISSION_SETTINGS) == Gpf::YES);
    }

    public function getOrderID() {
        if($this->isRecurring()) {
            return $this->getSubscriptionID();
        } else {
            return $this->getTransactionID();
        }
    }

    protected function prepareSales(Pap_Tracking_ActionTracker $saleTracker) {
        if ($this->getRequestObject()->getPostParam('num_cart_items') > 0
        && Gpf_Settings::get(Ejunkie_Config::PROCESS_WHOLE_CART_AS_ONE_TRANSACTION) == GPF::NO) {
            $this->prepareSeparateCartItems($saleTracker);
        } else {
            parent::prepareSales($saleTracker);
        }
    }

    private function prepareSeparateCartItems(Pap_Tracking_ActionTracker $saleTracker) {
        $request = $this->getRequestObject();
        $numItems = $request->getPostParam('num_cart_items');

        for ($i=1; $i<=$numItems; $i++) {
            $sale = $saleTracker->createSale();
            $sale->setTotalCost($this->adjustTotalCost($request->getPostParam('mc_gross_'.$i), $request, $i));
            $sale->setOrderID($this->getOrderID());
            $sale->setProductID($request->getPostParam('item_number'.$i));
            $sale->setData1($this->getData1());
            $sale->setData2($this->getData2());
            $sale->setData3($this->getData3());
            $sale->setData4($this->getData4());
            $sale->setData5($this->getData5());
            $sale->setCoupon($this->getCouponCode());
            $sale->setCurrency($this->getCurrency());
            if ($this->getStatus()!='') {
                $sale->setStatus($this->getStatus());
            }
            if($this->getAffiliateID() != '' && $this->getCampaignID() != '') {
                $sale->setAffiliateID($this->getAffiliateID());
                $sale->setCampaignID($this->getCampaignID());
            }

            $this->setVisitorAndAccount($saleTracker, $this->getAffiliateID(), $this->getCampaignID(), $this->getCookie());
        }
    }

    private function adjustTotalCost($originalTotalCost, Pap_Tracking_Request $request, $index = '') {
        $totalCost = $originalTotalCost;
        $this->debug('Original totalcost: '.$totalCost);
        if (Gpf_Settings::get(Ejunkie_Config::DISCOUNT_FEE)==Gpf::YES) {
            $totalCost = $this->discountFromTotalcost($totalCost, $request->getPostParam('mc_fee'.$index));
            $this->debug('Discounting fee ('.$request->getPostParam('mc_fee'.$index).') from totalcost.');
        }
        if (Gpf_Settings::get(Ejunkie_Config::DISCOUNT_TAX)==Gpf::YES) {
            $totalCost = $this->discountFromTotalcost($totalCost, $request->getPostParam('tax'.$index));
            $this->debug('Discounting tax ('.$request->getPostParam('tax'.$index).') from totalcost.');
        }
        if (Gpf_Settings::get(Ejunkie_Config::DISCOUNT_HANDLING)==Gpf::YES) {
            $totalCost = $this->discountFromTotalcost($totalCost, $request->getPostParam('mc_handling'.$index));
            $this->debug('Discounting handling ('.$request->getPostParam('mc_handling'.$index).') from totalcost.');
        }
        if (Gpf_Settings::get(Ejunkie_Config::DISCOUNT_SHIPPING)==Gpf::YES) {
            if ($index == '' && $request->getPostParam('mc_shipping') == '') {
                $totalCost = $this->discountFromTotalcost($totalCost, $request->getPostParam('shipping'));
                $this->debug('Discounting shipping ('.$request->getPostParam('shipping').') from totalcost.');
            } else {
                $totalCost = $this->discountFromTotalcost($totalCost, $request->getPostParam('mc_shipping'.$index));
                $this->debug('Discounting shipping ('.$request->getPostParam('mc_shipping'.$index).') from totalcost.');
            }
        }
        $this->debug('Totalcost after discounts: '.$totalCost);
        return $totalCost;
    }
}
?>
