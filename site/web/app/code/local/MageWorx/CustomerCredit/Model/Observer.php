<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Customer Credit extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @author     MageWorx Dev Team
 */

class MageWorx_CustomerCredit_Model_Observer
{
    public function saveCodeAfter(Varien_Event_Observer $observer) {
        $code = $observer->getEvent()->getCode();
        $code->getLogModel()
            ->setCodeModel($code)
            ->save();
    }

    public function saveCreditAfter(Varien_Event_Observer $observer) {
        $credit = $observer->getEvent()->getCredit();
        $credit->getLogModel()
            ->setCreditModel($credit)
            ->save();
    }

    public function prepareCustomerSave(Varien_Event_Observer $observer) {
        $customer = $observer->getEvent()->getCustomer();
        $request  = $observer->getEvent()->getRequest();
        if ($data = $request->getPost('customercredit'))
        {
            $customer->setCustomerCreditData($data);
        }
    }

    public function saveCustomerAfter(Varien_Event_Observer $observer) {
        if (!Mage::helper('customercredit')->isEnabled()) return false;                
        $customer = $observer->getEvent()->getCustomer();
        $customerCredit = Mage::getModel('customercredit/credit');
        if (($data = $customer->getCustomerCreditData()) && !empty($data['value_change'])) {
            // no minus
            if ((floatval($data['credit_value']) + floatval($data['value_change'])) < 0 ) $data['value_change'] = floatval($data['credit_value'])*-1;
            
            $customerCredit->setData($data)->setCustomer($customer)->save();
            
            // if send email
            if (Mage::helper('customercredit')->isSendNotificationBalanceChanged()) {                
                Mage::helper('customercredit')->sendNotificationBalanceChangedEmail($customer);
            }
            
        }
    }

    public function collectQuoteTotalsBefore(Varien_Event_Observer $observer) {
        $quote = $observer->getEvent()->getQuote();
     //   echo get_class($quote); exit;
        $quote->setCustomerCreditTotalsCollected(false);
    }

    public function placeOrderBefore(Varien_Event_Observer $observer) {
        
        if (!Mage::helper('customercredit')->isEnabled()) return;

        $order = $observer->getEvent()->getOrder();
        /* @var $order Mage_Sales_Model_Order */
        if ($order->getBaseCustomerCreditAmount() > 0) {
            
            $credit = Mage::helper('customercredit')->getCreditValue($order->getCustomerId(), Mage::app()->getStore($order->getStoreId())->getWebsiteId());            
            
            if (($order->getBaseCustomerCreditAmount() - $credit) >= 0.0001) {
                Mage::getSingleton('checkout/type_onepage')
                    ->getCheckout()
                    ->setUpdateSection('payment-method')
                    ->setGotoSection('payment');

                Mage::throwException(Mage::helper('customercredit')->__('Not enough Credit Amount to complete this Order.'));
            }
        }
    }

    public function reduceCustomerCreditValue(Varien_Event_Observer $observer) {
        if (!Mage::helper('customercredit')->isEnabled()) return false;
        $order = $observer->getEvent()->getOrder();
        /* @var $order Mage_Sales_Model_Order */
        if ($order->getBaseCustomerCreditAmount() > 0) {
            //reduce credit value
            Mage::getModel('customercredit/credit')->useCredit($order);
            return true;            
        }
        return false;
    }

    public function saveInvoiceAfter(Varien_Event_Observer $observer) {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();

        if ($invoice->getBaseCustomerCreditAmount()) {
            $order->setBaseCustomerCreditInvoiced($order->getBaseCustomerCreditInvoiced() + $invoice->getBaseCustomerCreditAmount());
            $order->setCustomerCreditInvoiced($order->getCustomerCreditInvoiced() + $invoice->getCustomerCreditAmount());
        }
    }

    public function loadOrderAfter(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getOrder();

        if ($order->canUnhold()) {
            return $this;
        }

        if ($order->getState() === Mage_Sales_Model_Order::STATE_CANCELED ||
            $order->getState() === Mage_Sales_Model_Order::STATE_CLOSED ) {
            return $this;
        }


        if (abs($order->getCustomerCreditInvoiced() - $order->getCustomerCreditRefunded())<.0001) {
            return $this;
        }
        $order->setForcedCanCreditmemo(true);

        return $this;
    }
           
    public function refundCreditmemo(Varien_Event_Observer $observer) {                
        
        $creditmemo = $observer->getEvent()->getCreditmemo();
        Mage::register('cc_order_refund', true, true);
        $order = $creditmemo->getOrder();        
        
        // get real total
        $baseTotal = $creditmemo->getBaseGrandTotal();
        //if ($order->getBaseCustomerCreditAmount()>$order->getBaseCustomerCreditRefunded()) $baseTotal += $order->getBaseCustomerCreditAmount() - $order->getBaseCustomerCreditRefunded();
        if ($order->getBaseCustomerCreditAmount()>$order->getBaseCustomerCreditRefunded()) $baseTotal += $creditmemo->getBaseCustomerCreditAmount();
        
        $baseTotal = floatval($baseTotal);
        
        // add message Returned credit amount..
        $post = Mage::app()->getRequest()->getParam('creditmemo');                        
        if (isset($post['credit_return'])) {
            $baseCreditAmountReturn = floatval($post['credit_return']);
            // validation
            if ($baseCreditAmountReturn>$baseTotal) $baseCreditAmountReturn = $baseTotal;
        } else {
            //$baseCreditAmountReturn = $creditmemo->getBaseCustomerCreditAmount() - $order->getBaseCustomerCreditRefunded();
            //if ($baseCreditAmountReturn<0) $baseCreditAmountReturn = $order->getBaseCustomerCreditAmount() - $order->getBaseCustomerCreditRefunded();
            $baseCreditAmountReturn = $creditmemo->getBaseCustomerCreditAmount();
        }
        
        if ($baseCreditAmountReturn>0) {
            // set CustomerCreditRefunded
            $order->setBaseCustomerCreditRefunded($order->getBaseCustomerCreditRefunded() + $baseCreditAmountReturn);            
            $creditAmountReturn = $creditmemo->getStore()->convertPrice($baseCreditAmountReturn, false, false);
            $order->setCustomerCreditRefunded($order->getCustomerCreditRefunded() + $creditAmountReturn);                                  
            
            // if payment is not 100% credit
            if ($order->getBaseGrandTotal()!=0) {
                // set [base_]total_refunded 
                $order->setBaseTotalRefunded(($order->getBaseTotalRefunded() - $creditmemo->getBaseGrandTotal()) + ($baseTotal - $baseCreditAmountReturn));
                $total = $creditmemo->getStore()->convertPrice($baseTotal, false, false);
                $order->setTotalRefunded(($order->getTotalRefunded() - $creditmemo->getGrandTotal()) + ($total - $creditAmountReturn));
            }
            
            if (abs($order->getCustomerCreditInvoiced() - $order->getCustomerCreditRefunded())<.0001) {
                $order->setForcedCanCreditmemo(false);
            }
            
            // set message
            $payment = $order->getPayment();
            

            if ($order->getBaseGrandTotal()!=0) {
                if ($creditmemo->getDoTransaction() && $creditmemo->getInvoice()) {
                    // online
                    $message = Mage::helper('sales')->__('Refunded amount of %s online.', $payment->getOrder()->getBaseCurrency()->formatTxt($baseTotal - $baseCreditAmountReturn))."<br/>";
                } else {
                    // offline
                    $message = Mage::helper('sales')->__('Refunded amount of %s offline.', $payment->getOrder()->getBaseCurrency()->formatTxt($baseTotal - $baseCreditAmountReturn))."<br/>";
                }
            } else {
                $message = '';
            }
            $message .= Mage::helper('customercredit')->__('Returned credit amount: %s.', $payment->getOrder()->getBaseCurrency()->formatTxt($baseCreditAmountReturn));
            $historyRefund = $payment->getOrder()->getStatusHistoryCollection()->getLastItem();
            $historyRefund->setComment($message);
        }
        return $this;
    }

    public function paypalCart($observer) {
        $model = $observer->getEvent()->getPaypalCart();

        if (Mage::app()->getStore()->isAdmin()) {
            $allItems = Mage::getSingleton('adminhtml/sales_order_create')->getQuote()->getAllItems();
            $productIds = array();
            foreach ($allItems as $item) {
                $productIds[] = $item->getProductId();
            }
        } else {
            $productIds = Mage::getSingleton('checkout/cart')->getProductIds();            
        }

        if (count($productIds)==0) return $this;

        $address = $model->getSalesEntity()->getBillingAddress();
        foreach ($productIds as $productId) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $productTypeId = $product->getTypeId();
            if ($productTypeId!='downloadable' && !$product->isVirtual()) {
                $address = $model->getSalesEntity()->getShippingAddress();
                break;
            }
        }
        
        $credit = $address->getCustomerCreditAmount(); 
        if($credit == NULL)
        {
            $credit = 0;
            $credit = $model->getSalesEntity()->getCustomerCreditAmount();
        }
         $model->updateTotal(Mage_Paypal_Model_Cart::TOTAL_DISCOUNT,$credit);
    }
    
    # Paypal method for f... GoMage Checkout
//    public function paypalCart($observer)
//    {
//        $model = $observer->getEvent()->getPaypalCart();
//
//        if (Mage::app()->getStore()->isAdmin()) {
//            $allItems = Mage::getSingleton('adminhtml/sales_order_create')->getQuote()->getAllItems();
//            $productIds = array();
//            foreach ($allItems as $item) {
//                $productIds[] = $item->getProductId();
//            }
//        } else {
//				$quoteId = $model->getSalesEntity()->getQuoteId();
//				$quote = Mage::getSingleton('gomage_checkout/type_onestep')->getQuote();
//				$quote = $quote->load($quoteId);
//			 foreach ($quote->getAllVisibleItems() as $item)
//			{
//			    $productIds[] = $item->getProduct()->getId();
//			}
//        }
//
//        if (count($productIds)==0) return $this;
//
//        $address = $model->getSalesEntity()->getBillingAddress();
//        foreach ($productIds as $productId) {
//            $product = Mage::getModel('catalog/product')->load($productId);
//            $productTypeId = $product->getTypeId();
//            if ($productTypeId!='downloadable' && !$product->isVirtual()) {
//                $address = $model->getSalesEntity()->getShippingAddress();
//                break;
//            }
//        }
//        
//        $credit = $address->getCustomerCreditAmount(); 
//        if($credit == NULL)
//        {
//            $credit = 0;
//            $credit = $model->getSalesEntity()->getCustomerCreditAmount();
//        }
//         $model->updateTotal(Mage_Paypal_Model_Cart::TOTAL_DISCOUNT,$credit);
//    }

    public function saveCreditmemoAfter(Varien_Event_Observer $observer) {
        Mage::getModel('customercredit/credit')->refund($observer->getEvent()->getCreditmemo(), Mage::app()->getRequest()->getParam('creditmemo'));        
        return $this;
    }

    public function customercreditRule(Varien_Event_Observer $observer){    	       
        $order = $observer->getEvent()->getOrder();
        if(Mage::registry('cc_order_refund'))
        {
            return true;
        }
        if ($customerId = $order->getCustomerId()) {
            $store = $order->getStore();
            $customer = Mage::getModel('customer/customer')->setStore($store)->load($customerId);
            $customerGroupId = $customer->getGroupId();
	    $websiteId = $store->getWebsiteId();
	    $ruleModel = Mage::getResourceModel('customercredit/rules_collection');                                    
            $orderQty  = 0;//$order->getTotalQtyOrdered();
	    $ruleModel->setValidationFilter($websiteId, $customerGroupId);
	    foreach ($ruleModel->getData() as $rule) {                                
	    	$conditions = unserialize($rule['conditions_serialized']);                
                foreach ($conditions['conditions'] as $key => $condition) {

                    $success[$key] = true;

                    if ($condition['attribute'] == 'registration'){

                            $regArr = explode(' ', $customer['created_at'], 2);
                            $regDate = explode('-', $regArr[0], 3);
                            $regTimestamp = mktime(0, 0, 0, $regDate[1], $regDate[2], $regDate[0]);

                            $ruleRegDate = explode('-', $condition['value'], 3);
                            $ruleRegTimestamp = mktime(0, 0, 0, $ruleRegDate[1], $ruleRegDate[2], $ruleRegDate[0]);

                            if (!version_compare($regTimestamp, $ruleRegTimestamp, $condition['operator'])){
                                    $success[$key] = false;
                            }


                    } elseif ($condition['attribute'] == 'total_amount'){

                            $orders = Mage::getResourceModel('sales/order_collection');
                            $orders->getSelect()
                                            ->reset(Zend_Db_Select::WHERE)
                                            ->columns(array('grand_subtotal' => 'SUM(subtotal)'))
                                            ->where('customer_id='.$customerId)
                                            ->group('customer_id');

                            $data = $orders->getData();

                            if (count($data) != 1){
                                    $success[$key] = false;
                            }
                            if (!version_compare($data[0]['grand_subtotal'], $condition['value'], $condition['operator'])){
                                    $success[$key] = false;
                            }

                    } else {                        
                        // product atributes:
                        $success[$key] = false;                        
                        $products = $order->getAllItems();
                        $conditionProductModel = Mage::getModel($condition['type'])->loadArray($condition);                                                                                                
                        foreach($products as $item) {
                            $product = Mage::getModel('catalog/product')->load($item->getProductId());  
                            if ($conditionProductModel->validate($product)) {  
                                $success[$key] = true;
                                $orderQty += $item->getQtyOrdered() - $item->getQtyRefunded() - $item->getQtyCanceled();
                            //    break;
                            }                                    
                        }                                           
                    }                    
                    
                }

	    	$result = true;
                switch ($conditions['aggregator']){
                    case 'any':
                        switch ($conditions['value']){
                            case '1':
                                if(!in_array(true, $success)){
                                        $result = false;
                                }
                                break;
                            case '0':
                                if (!in_array(false, $success)){
                                        $result = false;
                                }
                                break;
                        }
                        break;
                    case 'all':
                        switch ($conditions['value']){
                            case 1:
                                if (in_array(false, $success)){
                                        $result = false;
                                }
                                break;
                            case 0:
                                if (in_array(true, $success)){
                                        $result = false;
                                }
                                break;
                        }
                        break;
                }

                if (!$result) continue;
                
                // if qty dependent
                if (isset($rule['qty_dependent']) && ($rule['qty_dependent']==1)) {
                    $rule['credit'] = $rule['credit'] * $orderQty;
                }
                
                // if onetime
                if (isset($rule['is_onetime'])) $isOnetime = $rule['is_onetime']; else $isOnetime = 1;
                
                $rulesCustomer = Mage::getModel('customercredit/rules_customer')->loadByRuleAndCustomer($rule['rule_id'], $customerId);
                                                
                if (!$rulesCustomer || !$rulesCustomer->getId()) {
                    $rulesCustomer = Mage::getModel('customercredit/rules_customer')->setRuleId($rule['rule_id'])->setCustomerId($customerId)->save();                    
                } else {
                    if ($isOnetime) continue;
                }                
                $creditLog = Mage::getModel('customercredit/credit_log')->loadByOrderAndAction($order->getId(), 3, $rulesCustomer->getId());                    
                if (!$creditLog || !$creditLog->getId()) {
                    Mage::getModel('customercredit/credit')
                            ->setCustomerId($customerId)
                            ->setWebsiteId($websiteId)
                            ->setOrder($order)
                            ->setRuleName($rule['name'])
                            ->setRulesCustomerId($rulesCustomer->getId())                            
                            ->setValueChange($rule['credit'])
                            ->setActionType(MageWorx_CustomerCredit_Model_Credit_Log::ACTION_TYPE_CREDITRULE)
                            ->save();
                }    

	    }

    	}

    }
    
    public function returnCredit(Varien_Event_Observer $observer) {                                                                            
        Mage::getModel('customercredit/credit')->cancel($observer->getEvent()->getOrder());                
        return $this;        
    }
    
    public function placeOrderAfter(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        if ($this->reduceCustomerCreditValue($observer)) {
            // if payment of credit is fully -> invoice
            if (Mage::helper('customercredit')->isEnabledInvoiceOrder() && $order->getBaseTotalDue()==0 && $order->canInvoice()) {                
                $savedQtys = array();
                foreach ($order->getAllItems() as $orderItem) {
                    $savedQtys[$orderItem->getId()] = $orderItem->getQtyToInvoice();
                }
                
                $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
                if (!$invoice->getTotalQty()) return $this;                
                $invoice->register();
                $invoice->getOrder()->setIsInProcess(true);
                
                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $transactionSave->save();                
            }
        }
        $this->placeOrderCustomer($order);
        //$this->customercreditRule($observer);        
        return $this;  
        
        
    }       
    
    public function checkCompleteStatusOrder(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        if ($order->getStatus()=='complete') {            
            $creditProductSku = Mage::helper('customercredit')->getCreditProductSku();
            $creditQty = 0;
            if ($creditProductSku) {
                $allItems = $order->getAllItems();
                foreach ($allItems as $item) {
                    if ($item->getSku()==$creditProductSku) {
                        $creditQty = intval($item->getQtyInvoiced());
                    }
                }
                if ($creditQty>0) {                    
                    $creditLog = Mage::getModel('customercredit/credit_log')->loadByOrderAndAction($order->getId(), 5);                    
                    if (!$creditLog || !$creditLog->getId()) {                    
                        Mage::getModel('customercredit/credit')
                            ->setCustomerId($order->getCustomerId())
                            ->setWebsiteId($order->getStore()->getWebsiteId())
                            ->setOrder($order)
                            ->setValueChange($creditQty)
                            ->setActionType(MageWorx_CustomerCredit_Model_Credit_Log::ACTION_TYPE_CREDIT_PRODUCT)
                            ->save();
                    }    
                }
            }
            $this->customercreditRule($observer);
            
        }    
        
        return $this;        
    }
    
    public function toHtmlBlockBefore(Varien_Event_Observer $observer) {
        $block = $observer->getEvent()->getBlock();
        $blockName = $block->getNameInLayout();
        if ($blockName == 'customer_account_navigation') {
            if (Mage::helper('customercredit')->isShowCustomerCredit()) $block->addLink('customercredit', 'customercredit', Mage::helper('customercredit')->__('My Credit'));
        } 
    }
    
    public function changeGroup($observer) {
        $customer = $observer->getEvent()->getCustomer();
        if($customer->hasDataChanges()) {
       //     $newValues = array_diff($customer->getData(), $customer->getOrigData());
            $websiteId = $customer->getWebsiteId();
            if($customer->getData('group_id')!=$customer->getOrigData('group_id')) {
                $customerGroup = $customer->getData('group_id');
                $time = Mage::getStoreConfig('mageworx_customers/customercredit_expiration/default_expiration_period');
                if(Mage::getStoreConfig('mageworx_customers/customercredit_expiration/default_expiration_period_'.$customerGroup)) {
                    $time = Mage::getStoreConfig('mageworx_customers/customercredit_expiration/default_expiration_period_'.$customerGroup);
                }
                $credit = Mage::getModel('customercredit/credit')
                    ->setCustomerId($customer->getId())
                    ->setWebsiteId($websiteId)
                    ->loadCredit();
                if(!$time) {
                    $credit->setData('expiration_time',"0000-00-00");
                } else {
                    $credit->setData('expiration_time',date('Y-m-d',time()+3600*24*$time));
                }
                
                $credit->setIsCron(true)->save();
            }
        }
    }
    
    public function subscribeCustomer($observer) {
        $customer = $observer->getEvent()->getCustomer();
        if (Mage::app()->getRequest()->getParam('is_subscribed') && ($customer->getIdSubscribed()==1))
        {
            return true;
        }
        $customerId = $customer->getId();
        Mage::log($observer->getEvent()->getName());
        if(!$customerId OR Mage::app()->getRequest()->getParam('is_subscribed') == 0)
        {
            return true;
        }
            $customerGroupId = $customer->getGroupId();
            
            $store = Mage::app()->getStore();
	    $websiteId = $store->getWebsiteId();
            
            $model = Mage::getModel('customercredit/rules_customer_action');
            $actionTag = MageWorx_Customercredit_Model_Rules_Customer_Action::MAGEWORX_CUSTOMER_ACTION_SUBSCRIBE;
	    $log = Mage::getModel('customercredit/rules_customer_log');
            $logCollectionModel = $log->getCollection()->setActionTag($actionTag);
            
            $ruleModel = Mage::getResourceModel('customercredit/rules_collection');                                    
            $ruleModel->setValidationFilter($websiteId, $customerGroupId);
	    foreach ($ruleModel->getData() as $rule) {                                
	    	$conditions = unserialize($rule['conditions_serialized']); 
               
                foreach ($conditions['conditions'] as $key => $condition) {
                    $skipUrl = false;
                    if ($condition['attribute'] == 'newsletter_signup'){
                        $logCollection = $logCollectionModel->loadByRuleAndCustomer($rule['rule_id'], $customerId);
                        
                        if($logCollection->getSize()) {
                                $success[$key] = false;
                                $skipUrl = true;
                                continue;
                        }
                        
                        $success[$key] = true;
                    }    
                    else {
                         $success[$key] = false;
                    }
                }

	    	$result = true;
                switch ($conditions['aggregator']){
                    case 'any':
                        switch ($conditions['value']){
                            case '1':
                                if(!in_array(true, $success)){
                                        $result = false;
                                }
                                break;
                            case '0':
                                if (!in_array(false, $success)){
                                        $result = false;
                                }
                                break;
                        }
                        break;
                    case 'all':
                        switch ($conditions['value']){
                            case 1:
                                if (in_array(false, $success)){
                                        $result = false;
                                }
                                break;
                            case 0:
                                if (in_array(true, $success)){
                                        $result = false;
                                }
                                break;
                        }
                        break;
                }

                if (!$result) continue;
                
                if(!$skipUrl) {
                    $log->setId(null)
                      ->setRuleId($rule['rule_id'])
                      ->setCustomerId($customerId)
                      ->setActionTag($actionTag)
                      ->setValue($customerId)
                      ->save();
                }
                else {
                    continue;
                }
                    
                // if onetime
                if (isset($rule['is_onetime'])) $isOnetime = $rule['is_onetime']; else $isOnetime = 1;
                $rulesCustomer = Mage::getModel('customercredit/rules_customer')->loadByRuleAndCustomer($rule['rule_id'], $customerId);
                                                
                if (!$rulesCustomer || !$rulesCustomer->getId()) {
                    $rulesCustomer = Mage::getModel('customercredit/rules_customer')->setRuleId($rule['rule_id'])->setCustomerId($customerId)->save();                    
                } else {
                    if ($isOnetime) continue;
                }                
                $creditLog = Mage::getModel('customercredit/credit_log');                   
                Mage::getModel('customercredit/credit')
                        ->setCustomerId($customerId)
                        ->setWebsiteId($websiteId)
                        ->setRuleName($rule['name'])
                        ->setRuleId($rule['rule_id'])
                        ->setRulesCustomerId($rulesCustomer->getId())                            
                        ->setValueChange($rule['credit'])
                        ->setActionType(MageWorx_CustomerCredit_Model_Credit_Log::ACTION_TYPE_CREDIT_ACTION)
                        ->save();
 	    }
        return $this;
    }
    
    public function checkCustomerTagRule($observer) {
        $object = $observer->getObject();
        $customerId = $object->getData('first_customer_id');
        $actionName = $observer->getEvent()->getName();
        if(!$customerId || ($object->getStatus()!=1))
        {
            return true;
        }
       
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $customerGroupId = $customer->getGroupId();

        $store = Mage::app()->getStore();
        $websiteId = $customer->getWebsiteId();
        
        $ruleModel = Mage::getResourceModel('customercredit/rules_collection');                                    
        $ruleModel->setValidationByCustomerGroup($customerGroupId);
        
        $model = Mage::getModel('customercredit/rules_customer_action');
        $collection = $model->getCollection();
        
        $actionTag = MageWorx_Customercredit_Model_Rules_Customer_Action::MAGEWORX_CUSTOMER_ACTION_TAG;
       
        $log = Mage::getModel('customercredit/rules_customer_log');
        $logCollectionModel = $log->getCollection()->setActionTag($actionTag);
        $tagProductCollection = Mage::getResourceModel('tag/product_collection');
        $tagProductCollection->getSelect()->where('t.tag_id=?',$object->getTagId());
        foreach ($ruleModel->getData() as $rule) {   
            $customerActions = array();
            $conditions = unserialize($rule['conditions_serialized']);  
            foreach ($conditions['conditions'] as $key => $condition) {
                $coff = 1;
                $rest = 0;
                $skipUrl = false;
                if($actionTag)
                {
                    if ($condition['attribute']=='tag_product') {
                        $skipUrl = false;
                        $logCollection = $logCollectionModel->loadByRuleAndCustomer($rule['rule_id'], $customerId);
                        foreach ($logCollection as $item)
                        {
                            if($item->getValue() == $object->getTagId()) {
                                $success[$key] = false;
                                $skipUrl = true;
                            }
                        }
                        
                        
                        $action = $collection->loadByRuleAndCustomer($rule['rule_id'], $customerId)->getFirstItem();
                        if ($action->getId()) {
                            $currentValue = $action->getValue();
                        } else {
                            $currentValue = 0;
                        }
                        $nextValue = $currentValue+1;
                        if($nextValue >= $condition['value'])
                        {
                           $coff = $nextValue / $condition['value'];
                           $coff = (int) $coff;
                           $rest = $nextValue - $condition['value']*$coff;
                           $success[$key] = true;
                        }
                        else {
                            $rest = $nextValue;
                            $success[$key] = false;
                        }
                        $customerActions[] = array('rule'=>$rule,'rule_id'=>$rule['rule_id'],'customerId'=>$customerId,'actionTag'=>$actionTag,'rest'=>$rest,'coff'=>$coff,'nextValue'=>$nextValue); 
                    }
                    else {
                        $success[$key] = false;  
                        $products = $tagProductCollection->getItems();
                        $conditionProductModel = Mage::getModel($condition['type'])->loadArray($condition);                                                                                                
                        foreach($products as $item) {
                            $product = Mage::getModel('catalog/product')->load($item->getProductId());
                            if ($conditionProductModel->validateProduct($product)) {                            
                                $success[$key] = true;
                                break;
                            }                                    
                        } 
                    }
                }
            }

            $result = true;
            switch ($conditions['aggregator']){
                case 'any':
                    switch ($conditions['value']){
                        case '1':
                            if(!in_array(true, $success)){
                                    $result = false;
                            }
                            break;
                        case '0':
                            if (!in_array(false, $success)){
                                    $result = false;
                            }
                            break;
                    }
                    break;
                case 'all':
                    switch ($conditions['value']){
                        case 1:
                            if (in_array(false, $success)){
                                    $result = false;
                            }
                            break;
                        case 0:
                            if (in_array(true, $success)){
                                    $result = false;
                            }
                            break;
                    }
                    break;
            }
            if(count($customerActions)) {
                foreach ($customerActions as $actionValue) {
                    if(!$skipUrl) {
                        $log->setId(null)
                          ->setRuleId($actionValue['rule_id'])
                          ->setCustomerId($customerId)
                          ->setActionTag($actionTag)
                          ->setValue($object->getTagId())
                          ->save();
                    }
                    
                    $action->setRuleId($actionValue['rule_id'])
                        ->setCustomerId($actionValue['customerId'])
                        ->setActionTag($actionValue['actionTag']);
                    if($result) {
                        $action->setValue($actionValue['rest']);
                    } else {   
                        $action->setValue($actionValue['nextValue']);
                    }
                    $action->save();
                  
                
               
                if (!$result) continue;

                // if onetime
                if (isset($rule['is_onetime'])) $isOnetime = $rule['is_onetime']; else $isOnetime = 1;
                $rulesCustomer = Mage::getModel('customercredit/rules_customer')->loadByRuleAndCustomer($rule['rule_id'], $customerId);

                if (!$rulesCustomer || !$rulesCustomer->getId()) {
                    $rulesCustomer = Mage::getModel('customercredit/rules_customer')->setRuleId($rule['rule_id'])->setCustomerId($customerId)->save();                    
                } else {
                    if ($isOnetime) continue;
                }

                
                $creditLog = Mage::getModel('customercredit/credit_log');                   
                Mage::getModel('customercredit/credit')
                        ->setCustomerId($customerId)
                        ->setWebsiteId($websiteId)
                        ->setRuleId($actionValue['rule']['rule_id'])
                        ->setRuleName($actionValue['rule']['name'])
                        ->setRulesCustomerId($rulesCustomer->getId())                            
                        ->setValueChange($actionValue['rule']['credit']*$coff)
                        ->setActionType(MageWorx_CustomerCredit_Model_Credit_Log::ACTION_TYPE_CREDIT_ACTION)
                        ->save();
                }
            }
        }
       
        return $this;
    }
    
    public function checkCustomerReviewRule($observer) {
        $object = $observer->getObject();
        $customerId = $object->getCustomerId();
        $actionName = $observer->getEvent()->getName();
        
        if(!$customerId || ($object->getStatusId()!=1))
        {
            return true;
        }
        
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $customerGroupId = $customer->getGroupId();

        $store = Mage::app()->getStore();
        $websiteId = $customer->getWebsiteId();
        
        $ruleModel = Mage::getResourceModel('customercredit/rules_collection');                                    
        $ruleModel->setValidationByCustomerGroup($customerGroupId);
        
        $model = Mage::getModel('customercredit/rules_customer_action');
        $collection = $model->getCollection();
        
        $actionTag = MageWorx_Customercredit_Model_Rules_Customer_Action::MAGEWORX_CUSTOMER_ACTION_REVIEW;
        $log = Mage::getModel('customercredit/rules_customer_log');
        $logCollectionModel = $log->getCollection()->setActionTag($actionTag);
        
        $reviewProductCollection = Mage::getResourceModel('review/review_product_collection');

        $reviewProductCollection->getSelect()->where('rt.review_id=?',$object->getReviewId());
        foreach ($ruleModel->getData() as $rule) {   
            $customerActions = array();
            $conditions = unserialize($rule['conditions_serialized']);  
            foreach ($conditions['conditions'] as $key => $condition) {
                $coff = 1;
                $rest = 0;
                $skipUrl = false;
                if($actionTag)
                {
                    if ($condition['attribute']=='review_product') {
                        $skipUrl = false;
                        $logCollection = $logCollectionModel->loadByRuleAndCustomer($rule['rule_id'], $customerId);
                        foreach ($logCollection as $item)
                        {
                            if($item->getValue() == $object->getReviewId()) {
                                $success[$key] = false;
                                $skipUrl = true;
                            }
                        }
                        
                        
                        $action = $collection->loadByRuleAndCustomer($rule['rule_id'], $customerId)->getFirstItem();
                        if ($action->getId()) {
                            $currentValue = $action->getValue();
                        } else {
                            $currentValue = 0;
                        }
                        $nextValue = $currentValue+1;
                        if($nextValue >= $condition['value'])
                        {
                           $coff = $nextValue / $condition['value'];
                           $coff = (int) $coff;
                           $rest = $nextValue - $condition['value']*$coff;
                           $success[$key] = true;
                        }
                        else {
                            $rest = $nextValue;
                            $success[$key] = false;
                        }
                        $customerActions[] = array('rule'=>$rule,'rule_id'=>$rule['rule_id'],'customerId'=>$customerId,'actionTag'=>$actionTag,'rest'=>$rest,'coff'=>$coff,'nextValue'=>$nextValue); 
                    }
                    else {
                        $success[$key] = false;  
                        $products = $reviewProductCollection->getItems();
                        $conditionProductModel = Mage::getModel($condition['type'])->loadArray($condition);                                                                                                
                        foreach($products as $item) {
                           // print_r($item->getData()); exit;
                            $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$item->getSku());
                            if ($conditionProductModel->validateProduct($product)) {                            
                                $success[$key] = true;
                                break;
                            }                                    
                        } 
                    }
                }
            }

            $result = true;
            switch ($conditions['aggregator']){
                case 'any':
                    switch ($conditions['value']){
                        case '1':
                            if(!in_array(true, $success)){
                                    $result = false;
                            }
                            break;
                        case '0':
                            if (!in_array(false, $success)){
                                    $result = false;
                            }
                            break;
                    }
                    break;
                case 'all':
                    switch ($conditions['value']){
                        case 1:
                            if (in_array(false, $success)){
                                    $result = false;
                            }
                            break;
                        case 0:
                            if (in_array(true, $success)){
                                    $result = false;
                            }
                            break;
                    }
                    break;
            }
            if(count($customerActions)) {
                foreach ($customerActions as $actionValue) {
                    if(!$skipUrl) {
                        $log->setId(null)
                          ->setRuleId($actionValue['rule_id'])
                          ->setCustomerId($customerId)
                          ->setActionTag($actionTag)
                          ->setValue($object->getReviewId())
                          ->save();
                    }
                    
                    $action->setRuleId($actionValue['rule_id'])
                        ->setCustomerId($actionValue['customerId'])
                        ->setActionTag($actionValue['actionTag']);
                    if($result) {
                        $action->setValue($actionValue['rest']);
                    } else {   
                        $action->setValue($actionValue['nextValue']);
                    }
                    $action->save();
                  
                
               
                if (!$result) continue;

                // if onetime
                if (isset($rule['is_onetime'])) $isOnetime = $rule['is_onetime']; else $isOnetime = 1;
                $rulesCustomer = Mage::getModel('customercredit/rules_customer')->loadByRuleAndCustomer($rule['rule_id'], $customerId);

                if (!$rulesCustomer || !$rulesCustomer->getId()) {
                    $rulesCustomer = Mage::getModel('customercredit/rules_customer')->setRuleId($rule['rule_id'])->setCustomerId($customerId)->save();                    
                } else {
                    if ($isOnetime) continue;
                }

                
                $creditLog = Mage::getModel('customercredit/credit_log');                   
                Mage::getModel('customercredit/credit')
                        ->setCustomerId($customerId)
                        ->setWebsiteId($websiteId)
                        ->setRuleId($actionValue['rule']['rule_id'])
                        ->setRuleName($actionValue['rule']['name'])
                        ->setRulesCustomerId($rulesCustomer->getId())                            
                        ->setValueChange($actionValue['rule']['credit']*$coff)
                        ->setActionType(MageWorx_CustomerCredit_Model_Credit_Log::ACTION_TYPE_CREDIT_ACTION)
                        ->save();
                }
            }
        }
       
        return $this;
    }
    
    public function customerRegisterSuccess($observer) {
        $success = array();
        
        $customer = $observer->getEvent()->getCustomer();
        $customerId = $customer->getId();
        
        $customerGroupId = $customer->getGroupId();
        
        $store = Mage::app()->getStore();
        $websiteId = $store->getWebsiteId();
        $ruleModel = Mage::getResourceModel('customercredit/rules_collection');                                    

        $ruleModel->setValidationFilter($websiteId, $customerGroupId);
        foreach ($ruleModel->getData() as $rule) {                                
            $conditions = unserialize($rule['conditions_serialized']);                
            if(isset($conditions['conditions'])) {
                foreach ($conditions['conditions'] as $key => $condition) {

                    if ($condition['attribute'] == 'registration'){
                        list($date,$time) = explode(' ', $customer->getCreatedAt());
                        switch ($condition['operator']) {
                            case '>=':
                                if($date >= $condition['value']) {
                                    $success[$key] = true;
                                } 
                                break;
                            case '==':

                                if($date == $condition['value']) {
                                    $success[$key] = true;
                                }
                                break;
                            case '=<':
                                if($date <= $condition['value']) {
                                    $success[$key] = true;
                                }
                                break;
                        }
                    } 
                    else {
                        $success[$key] = false;
                    }
                }
            }
            $result = true;
            switch ($conditions['aggregator']){
                case 'any':
                    switch ($conditions['value']){
                        case '1':
                            if(!in_array(true, $success)){
                                    $result = false;
                            }
                            break;
                        case '0':
                            if (!in_array(false, $success)){
                                    $result = false;
                            }
                            break;
                    }
                    break;
                case 'all':
                    switch ($conditions['value']){
                        case 1:
                            if (in_array(false, $success)){
                                    $result = false;
                            }
                            break;
                        case 0:
                            if (in_array(true, $success)){
                                    $result = false;
                            }
                            break;
                    }
                    break;
            }

            if (!$result) continue;

            // if onetime
            if (isset($rule['is_onetime'])) $isOnetime = $rule['is_onetime']; else $isOnetime = 1;
            $rulesCustomer = Mage::getModel('customercredit/rules_customer')->loadByRuleAndCustomer($rule['rule_id'], $customerId);

            if (!$rulesCustomer || !$rulesCustomer->getId()) {
                $rulesCustomer = Mage::getModel('customercredit/rules_customer')->setRuleId($rule['rule_id'])->setCustomerId($customerId)->save();                    
            } else {
                if ($isOnetime) continue;
            }                
            $creditLog = Mage::getModel('customercredit/credit_log');                   
            Mage::getModel('customercredit/credit')
                    ->setCustomerId($customerId)
                    ->setWebsiteId($websiteId)
                    ->setRuleName($rule['name'])
                    ->setRuleId($rule['rule_id'])
                    ->setRulesCustomerId($rulesCustomer->getId())                            
                    ->setValueChange($rule['credit'])
                    ->setActionType(MageWorx_CustomerCredit_Model_Credit_Log::ACTION_TYPE_CREDIT_ACTION)
                    ->save();
        }
        return $this;
    }
    
    public function dobCustomerCron() {
        $collection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('dob',date('Y-m-d', time()));
        foreach($collection->getItems()as $customer)
        {
            try {
                $this->dobCustomer($customer);
            } catch (Exception $e) {
                Mage::log($e->getMessage());
            }
        }
        return true;
    }
    
    public function dobCustomer($customer) {
        $customerId = $customer->getId();
        $customerGroupId = $customer->getGroupId();

        $store = Mage::app()->getStore($customer->getStoreId());
        $websiteId = $store->getWebsiteId();

        $model = Mage::getModel('customercredit/rules_customer_action');
        $actionTag = MageWorx_Customercredit_Model_Rules_Customer_Action::MAGEWORX_CUSTOMER_ACTION_DOB;
        $log = Mage::getModel('customercredit/rules_customer_log');
        $logCollectionModel = $log->getCollection()->setActionTag($actionTag);

        $ruleModel = Mage::getResourceModel('customercredit/rules_collection');                                    
        $ruleModel->setValidationFilter($websiteId, $customerGroupId);
        foreach ($ruleModel->getData() as $rule) {                                
            $conditions = unserialize($rule['conditions_serialized']); 

            foreach ($conditions['conditions'] as $key => $condition) {
                $skipUrl = false;
                if ($condition['attribute'] == 'birthday'){
                    $logCollection = $logCollectionModel->loadByRuleAndCustomer($rule['rule_id'], $customerId);
                    $logCollection->getSelect()->order('id ASC');
                    if($logCollection->getSize()) {
                        $lastItem = $logCollection->getLastItem();
                        if(time() - $lastItem->getValue() < 31104000) { // one year
                            $success[$key] = false;
                            $skipUrl = true;
                            continue;
                        }
                    }

                    $success[$key] = true;
                }    
                else {
                     $success[$key] = false;
                }
            }

            $result = true;
            switch ($conditions['aggregator']){
                case 'any':
                    switch ($conditions['value']){
                        case '1':
                            if(!in_array(true, $success)){
                                    $result = false;
                            }
                            break;
                        case '0':
                            if (!in_array(false, $success)){
                                    $result = false;
                            }
                            break;
                    }
                    break;
                case 'all':
                    switch ($conditions['value']){
                        case 1:
                            if (in_array(false, $success)){
                                    $result = false;
                            }
                            break;
                        case 0:
                            if (in_array(true, $success)){
                                    $result = false;
                            }
                            break;
                    }
                    break;
            }

            if (!$result) continue;

            if(!$skipUrl) {
                $log->setId(null)
                  ->setRuleId($rule['rule_id'])
                  ->setCustomerId($customerId)
                  ->setActionTag($actionTag)
                  ->setValue(time())
                  ->save();
            }
            else {
                continue;
            }

            // if onetime
            if (isset($rule['is_onetime'])) $isOnetime = $rule['is_onetime']; else $isOnetime = 1;
            $rulesCustomer = Mage::getModel('customercredit/rules_customer')->loadByRuleAndCustomer($rule['rule_id'], $customerId);

            if (!$rulesCustomer || !$rulesCustomer->getId()) {
                $rulesCustomer = Mage::getModel('customercredit/rules_customer')->setRuleId($rule['rule_id'])->setCustomerId($customerId)->save();                    
            } else {
                if ($isOnetime) continue;
            }                
            $creditLog = Mage::getModel('customercredit/credit_log');                   
            Mage::getModel('customercredit/credit')
                    ->setCustomerId($customerId)
                    ->setWebsiteId($websiteId)
                    ->setRuleName($rule['name'])
                    ->setRuleId($rule['rule_id'])
                    ->setRulesCustomerId($rulesCustomer->getId())                            
                    ->setValueChange($rule['credit'])
                    ->setActionType(MageWorx_CustomerCredit_Model_Credit_Log::ACTION_TYPE_CREDIT_ACTION)
                    ->save();
        }
        return $this;
    }
    
    public function placeOrderCustomer($order) {
    //    echo "<pre>"; print_r($order->getQuote()->getCustomer()->getData()); exit;
        $customer = $order->getQuote()->getCustomer();
        $customerId = $customer->getId();
        $customerGroupId = $customer->getGroupId();

        $store = Mage::app()->getStore($customer->getStoreId());
        $websiteId = $store->getWebsiteId();

        $model = Mage::getModel('customercredit/rules_customer_action');
        $actionTag = MageWorx_Customercredit_Model_Rules_Customer_Action::MAGEWORX_CUSTOMER_ACTION_PLACEORDER;
        $log = Mage::getModel('customercredit/rules_customer_log');
        $logCollectionModel = $log->getCollection()->setActionTag($actionTag);

        $ruleModel = Mage::getResourceModel('customercredit/rules_collection');                                    
        $ruleModel->setValidationFilter($websiteId, $customerGroupId);
        foreach ($ruleModel->getData() as $rule) {       
            $conditions = unserialize($rule['conditions_serialized']); 
            if (isset($rule['is_onetime'])) $isOnetime = $rule['is_onetime']; else $isOnetime = 1;
            foreach ($conditions['conditions'] as $key => $condition) {
                $skipUrl = false;
                if ($condition['attribute'] == 'place_order' && $condition['value']==1){
                    $logCollection = $logCollectionModel->loadByRuleAndCustomer($rule['rule_id'], $customerId);
                    $logCollection->getSelect()->order('id ASC');
                    if($logCollection->getSize() && $isOnetime) {
                        $lastItem = $logCollection->getLastItem();
                        $success[$key] = false;
                        $skipUrl = true;
                        continue;
                    }
                    $success[$key] = true;
                }    
                else {
                     $success[$key] = false;
                }
            }

            $result = true;
            switch ($conditions['aggregator']){
                case 'any':
                    switch ($conditions['value']){
                        case '1':
                            if(!in_array(true, $success)){
                                    $result = false;
                            }
                            break;
                        case '0':
                            if (!in_array(false, $success)){
                                    $result = false;
                            }
                            break;
                    }
                    break;
                case 'all':
                    switch ($conditions['value']){
                        case 1:
                            if (in_array(false, $success)){
                                    $result = false;
                            }
                            break;
                        case 0:
                            if (in_array(true, $success)){
                                    $result = false;
                            }
                            break;
                    }
                    break;
            }

            if (!$result) continue;

            if(!$skipUrl) {
                $log->setId(null)
                  ->setRuleId($rule['rule_id'])
                  ->setCustomerId($customerId)
                  ->setActionTag($actionTag)
                  ->setValue(time())
                  ->save();
            }
            else {
                continue;
            }

            // if onetime
            
            $rulesCustomer = Mage::getModel('customercredit/rules_customer')->loadByRuleAndCustomer($rule['rule_id'], $customerId);

            if (!$rulesCustomer || !$rulesCustomer->getId()) {
                $rulesCustomer = Mage::getModel('customercredit/rules_customer')->setRuleId($rule['rule_id'])->setCustomerId($customerId)->save();                    
            } else {
                if ($isOnetime) continue;
            }                
            $creditLog = Mage::getModel('customercredit/credit_log');    
            
            $rule_credit = $rule['credit'];
            if(strpos($rule_credit,'%')!==false) {
                $rule_credit = (int) str_replace('%', '', $rule_credit);
                $rule_credit = round($order->getGrandTotal()*$rule_credit/100,2);
            }
         //   echo $rule_credit; exit;
            Mage::getModel('customercredit/credit')
                    ->setCustomerId($customerId)
                    ->setWebsiteId($websiteId)
                    ->setRuleName($rule['name'])
                    ->setRuleId($rule['rule_id'])
                    ->setRulesCustomerId($rulesCustomer->getId())                            
                    ->setValueChange($rule_credit)
                    ->setActionType(MageWorx_CustomerCredit_Model_Credit_Log::ACTION_TYPE_CREDIT_ACTION)
                    ->save();
        }
        return $this;
    }
    
    public function customerGroupSaveAfter($observer) {
        $days = Mage::app()->getRequest()->getParam('customercredit_expiration_in');
        Mage::getModel('core/config')->saveConfig('mageworx_customers/customercredit_expiration/default_expiration_period_'.Mage::app()->getRequest()->getParam('id'),$days);
        $this->expirationDateRefreshCron();
    }
    
    public function customerGroupLoadAfter($observer) {
        $groupModel = $observer->getEvent()->getObject();
        $days = Mage::getStoreConfig('mageworx_customers/customercredit_expiration/default_expiration_period_'.$groupModel->getCustomerGroupId());
        $groupModel->setData('customercredit_expiration_in',$days);
        //      echo "<pre>"; print_r($groupModel); exit;
        return $groupModel;
    }
    
    public function customerGroupPrepareLayoutAfter($observer) {
        $block = $observer->getBlock();
        if($block->getType()!='adminhtml/customer_group_edit_form') return ;
        $customerGroup = Mage::registry('current_group');

        $form = $block->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $element = $fieldset->addField('customercredit_expiration_in', 'text',
            array(
                'name'  => 'customercredit_expiration_in',
                'label' => Mage::helper('customer')->__('Customer Credit Expiration In'),
                'title' => Mage::helper('customer')->__('Customer Credit Expiration In'),
                'note'  => Mage::helper('customer')->__('day(s)'),
            )
        );
        $element->setValue($customerGroup->getData($element->getId()));

        $block->setForm($form);
    }

    public function expirationDateCron() {
        $today = strtotime(date("Y-m-d"));
        $model=Mage::getModel('customercredit/credit');
        $collection = $model->getCollection()->joinCustomerTable();
        $sendCustomerNotificationPeriod = Mage::getStoreConfig('mageworx_customers/customercredit_expiration/notify_expiration_date_left');
        foreach ($collection as $item) {
            if($item->getExpirationTime()!='0000-00-00') {
                $date = strtotime($item->getExpirationTime());
                $hash = ($date-$today)/(3600*24);
                if($hash==$sendCustomerNotificationPeriod) {
                    Mage::helper('customercredit')->sendNotificationExpiration($item->getCustomerId(),$sendCustomerNotificationPeriod);
                }
                if($hash==0) {
                    $item->setValueChange(0-$item->getValue());
                    $item->getLogModel()->setActionType(MageWorx_CustomerCredit_Model_Credit_Log::ACTION_TYPE_EXPIRED);     
                    $item->save();
                }
            }
        }
    }
    
    public function expirationDateRefreshCron() {
        $model=Mage::getModel('customercredit/credit');
        $collection = $model->getCollection()->joinCustomerTable();
        $isRefreshAll = Mage::getStoreConfig('mageworx_customers/customercredit_expiration/update_expiration_date');
        foreach ($collection as $item) {
            $customerGroup =$item->getGroupId();
            $time = Mage::getStoreConfig('mageworx_customers/customercredit_expiration/default_expiration_period');
            if(Mage::getStoreConfig('mageworx_customers/customercredit_expiration/default_expiration_period_'.$customerGroup)) {
                $time = Mage::getStoreConfig('mageworx_customers/customercredit_expiration/default_expiration_period_'.$customerGroup);
            }
            
            if(($item->getExpirationTime()=='0000-00-00') || $isRefreshAll) {
                if(!$time) {
                    $item->setData('expiration_time',"0000-00-00");
                } else {
                    $item->setData('expiration_time',date('Y-m-d',time()+3600*24*$time));
                }
            }
        
            $item->setIsCron(true)->save();
        }
    }
}