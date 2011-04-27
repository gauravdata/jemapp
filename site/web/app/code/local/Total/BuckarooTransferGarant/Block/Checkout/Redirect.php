<?php
class Total_BuckarooTransferGarant_Block_Checkout_Redirect extends Mage_Core_Block_Abstract
{
	public $payment_method='transfergarant';
	public $_code='buckarootransfergarant';
    
    protected function _toHtml()
    {
    	
		list($ResponseStatus, $StatusDescription, $AdditionalMessage, $order_id)=Mage::getSingleton('Total_BuckarooTransferGarant_Model_PaymentMethod')->request();

		$order=Mage::getModel('sales/order')->loadByIncrementId($order_id);
		$buckaroo_response = Mage::getSingleton('Total_Buckaroo_Model_PaymentMethod')->process_responsecodes($ResponseStatus, $order);
		
		$message=Mage::helper('buckaroo')->__($buckaroo_response['omschrijving']);

		
		// NEW kind of order processing
            
            list($setState, $setStatus)=Total_Buckaroo_Model_PaymentMethod::getStateAndStatusByCode($buckaroo_response['code']);
            $currentStateAndStatus=array($order->getState(), $order->getStatus());
            
            $debug_info.="\nStates: ".$order->getState().' '.var_export($setState, true)."\n";
            $debug_info.="\nStatuses: ".$order->getStatus().' '.var_export($setStatus, true)."\n";
            
            // getting GrandTotal
            $amount=round($order->getGrandTotal()*100, 0);
            
            if($buckaroo_response['code']==Total_Buckaroo_Model_PaymentMethod::BUCKAROO_FAILED)
            {
                if(Mage::getStoreConfig('payment/buckaroo/cancel_on_failed', Mage::app()->getStore()->getStoreId()))
                {
                    $order->cancel()->save();
                }
                else
                {
                    if($currentStateAndStatus!=array($setState, $setStatus))
                    {
                        $order->setState($setState);
                        $order->setStatus($setStatus);
                        $order->save();
                    }
                }
            }
            else if($buckaroo_response['code']==Total_Buckaroo_Model_PaymentMethod::BUCKAROO_SUCCESS)
            {
                if($currentStateAndStatus!=array($setState, $setStatus))
                {
                    $order->setState($setState);
                    $order->setStatus($setStatus);
                    $order->save();
                }
            }
            else
            {
                $method=$order->getPayment()->getMethod();
                
                $setState=$order->getState();
                $setStatus=Mage::getStoreConfig('payment/'.$method.'/order_status', Mage::app()->getStore()->getStoreId());
                
                if($currentStateAndStatus!=array($setState, $setStatus))
                {
                    $order->setState($setState);
                    $order->setStatus($setStatus);
                    $order->save();                    
                }
            }
            
            try
            {
              if($buckaroo_response['code']!=Total_Buckaroo_Model_PaymentMethod::BUCKAROO_FAILED)
              {
                if(!$order->getEmailSent())
                {
                    $order->sendNewOrderEmail();
                }
              }
            } catch (Exception $ex) {  }
            
            if(($buckaroo_response['code']==Total_Buckaroo_Model_PaymentMethod::BUCKAROO_SUCCESS)
                && Mage::getStoreConfig('payment/buckaroo/auto_invoice', Mage::app()->getStore()->getStoreId())
                && $order->getStatus()!='buckaroo_too_less')
            {
                Total_Buckaroo_Model_PaymentMethod::saveInvoice($order);
            }
            
            $debug_info .= "Order updated: state ".$setState."\n";
            $debug_info .= "Order updated: status ".$setStatus."\n\n";
            $debug_info .= "Order updated: description ".$buckaroo_response["omschrijving"]."\n";
        // 
		
		$url=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
			
		$form = new Varien_Data_Form();
		$form->setAction($url.'buckaroo/checkout/success')
             ->setId('redirect')
             ->setName('redirect')
             ->setMethod('POST')
             ->setUseContainer(true);
		
        $form->addField('bpe_invoice', 'hidden', array('name'=>'bpe_invoice', 'value'=>$order_id));	     
		$form->addField('bpe_result', 'hidden', array('name'=>'bpe_result', 'value'=>$ResponseStatus));        
		
		
        $html = '<html><body>';
        $html.= $this->__('Your order is approved, you will be redirected to the store...');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("redirect").submit();</script>';   
        $html.= '</body></html>';
	        
        return $html;
		
    }
}