<?php
class Total_Buckaroo_NotifyController extends Mage_Core_Controller_Front_Action {

    public function errorAction()
    {
       if(empty($_POST))
        {
            echo "Only Buckaroo can call this page properly."; exit;
        }
        
        $order_id=$_POST['bpe_invoice'];
        $order=Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $order->cancel()->save();
            
        Mage::getSingleton('core/session')->addError($this->__('The payment has been canceled'));
        
        return $this->_redirect(trim(Mage::getStoreConfig('payment/buckaroo/failure_redirect', Mage::app()->getStore()->getStoreId())));
    }
    
    public function testAction()
    {
//      echo Mage::getSingleton('core/resource')->getTableName('sales/order_address'); exit;
//      $order_id=100000055;
//      $order=Mage::getModel('sales/order')->loadByIncrementId($order_id);
//      echo $order->getState()."<br/>";
//      echo $order->getStatus()."<br/>";
//      $order->save();
//      
//      exit;

//      var_dump(Mage::getSingleton('sales/order_config')->getStateStatuses('complete', true));
    }
    

    public function indexAction()
    {
//        $QUERY="";
//        
//        $_POST=array();
//        foreach(explode("\n", $QUERY) as $line)
//        {
//            $line=trim($line);
//            list($key, $value)=explode("=", $line);
//            
//            $_POST[$key]=$value;
//        }
        
        $debug_email=Mage::getStoreConfig('payment/buckaroo/debug_email', Mage::app()->getStore()->getStoreId());

        ob_start();
            echo "POST:\n";
            print_r($_POST);
            echo "\n\n";
            echo "SERVER:\n";
            print_r($_SERVER);
            echo "\n\n";
            $debug_info = ob_get_contents();
        ob_clean();
        
        if($debug_email!='')
        {
           mail($debug_email, 'Initial variables', $debug_info);
        }
                            
        if(empty($_POST))
        {
            echo "Only Buckaroo can call this page properly."; exit;
        }
    
        $language = preg_replace("/[^a-zA-Z]/","",$_POST["bpe_reference"]); 
        
        $signature =  md5(Mage::getStoreConfig('payment/buckaroo/key', Mage::app()->getStore()->getStoreId()).$_POST["bpe_invoice"].$_POST["bpe_trx"].Mage::getStoreConfig('payment/buckaroo/digital_signature', Mage::app()->getStore()->getStoreId()));
        $signature2 = md5($_POST["bpe_trx"].$_POST["bpe_timestamp"].Mage::getStoreConfig('payment/buckaroo/key', Mage::app()->getStore()->getStoreId()).$_POST["bpe_invoice"].$_POST["bpe_reference"].$_POST["bpe_currency"].$_POST["bpe_amount"].$_POST["bpe_result"].$_POST["bpe_mode"].Mage::getStoreConfig('payment/buckaroo/digital_signature', Mage::app()->getStore()->getStoreId()));
        
        $debug_info .= "Signature:".$_POST["bpe_signature2"]." - Signature2:".$signature2." = ";
        
        // Get successfull state and status
        $successStateAndStatus=Total_Buckaroo_Model_PaymentMethod::getStateAndStatusByCode(Total_Buckaroo_Model_PaymentMethod::BUCKAROO_SUCCESS);
        
        // check signature
        //----------------------------------------------------------------------------------------------
        if($_POST["bpe_signature2"]==$signature2)
        {
            $debug_info .= "Signature passed\n\n";
            $debug_info .= "---------------------------------\n";

            $order_id=$_POST['bpe_invoice'];
            
            $order=Mage::getModel('sales/order')->loadByIncrementId($order_id);         
        
            // Get payment method model
            $method=$order->getPayment()->getMethod();
            $paymentMethod=Mage::getModel($method.'/PaymentMethod');
            
            // if there is a custom method for processing the buckaroo response
            if(method_exists($paymentMethod, 'processReponseCode'))
            {
                $debug_info .= "\nCustom workflow by model $method/PaymentMethod\n";
                
                list($setState, $setStatus)=$paymentMethod->processReponseCode($_POST["bpe_result"], $order);
                
                $currentStateAndStatus=array($order->getState(), $order->getStatus());
                               
                if($successStateAndStatus==$currentStateAndStatus)
                {
                    $debug_info .= "\nOrder already has the success state and status, any further changes are prohibited.\n";
                }
                else
                {
                    if($currentStateAndStatus!=array($setState, $setStatus))
                    {
                        $order->setState($setState);
                        $order->setStatus($setStatus);
                        $order->save();
                    }
                    
                    $debug_info .= "Order updated: state ".$setState."\n";
                    $debug_info .= "Order updated: status ".$setStatus."\n\n";
                }
            }
            // process by default
            else
            {
                $debug_info .= "\nDefault workflow\n";
                
                $buckaroo_response = Mage::getModel('Total_Buckaroo_Model_PaymentMethod')->process_responsecodes($_POST["bpe_result"], $order);
                        
                $debug_info.="\n".var_export($buckaroo_response,true)."\n";
                list($setState, $setStatus)=Total_Buckaroo_Model_PaymentMethod::getStateAndStatusByCode($buckaroo_response['code']);
                 
                $currentStateAndStatus=array($order->getState(), $order->getStatus());
                
                // if successful state and status are already set
                if($successStateAndStatus==$currentStateAndStatus)
                {
                    $debug_info .= "\nOrder already has the success state and status, any further changes are prohibited.\n";
                }
                else
                {
                        $debug_info.="\nStates: ".$order->getState().' '.var_export($setState, true)."\n";
                        $debug_info.="Statuses: ".$order->getStatus().' '.var_export($setStatus, true)."\n\n";
                        
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
                                // if payment is equal as grandtotal
                                if($amount==$_POST['bpe_amount'])
                                {
                                    
                                }
                                // if payment is less then grandtotal
                                elseif($_POST['bpe_amount']<$amount)
                                {
                                    $setState='processing';
                                    $setStatus='buckaroo_too_less';
                                }
                                // if payment is greater then grandtotal
                                elseif($_POST['bpe_amount']>$amount)
                                {
                                    $setState='processing';
                                    $setStatus='buckaroo_too_much';
                                }
                                
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
                            // if payment is equal as grandtotal
                            if($amount==$_POST['bpe_amount'])
                            {
                                
                            }
                            // if payment is less then grandtotal
                            elseif($_POST['bpe_amount']<$amount)
                            {
                                $setState='processing';
                                $setStatus='buckaroo_too_less';
                            }
                            // if payment is greater then grandtotal
                            elseif($_POST['bpe_amount']>$amount)
                            {
                                $setState='processing';
                                $setStatus='buckaroo_too_much';
                            }
                            
                            
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
                            $payment = Total_Buckaroo_Model_PaymentMethod::saveInvoice($order, round($_POST['bpe_amount'] / 100, 2));
                            $order->setState($setState);
                            $order->setStatus($setStatus);
                            $order->save();
                        }
                    
                        $debug_info .= "Order updated: state ".$setState."\n";
                        $debug_info .= "Order updated: status ".$setStatus."\n\n";
                        
                        $debug_info .= "Order updated: description ".$buckaroo_response["omschrijving"]."\n";
                }
            }
        }
        else 
        {
            $debug_info .= "Signature failed.\n\n";
            $debug_info .= "Process stopped.\n";
        }
        
        $debug_info .= "Language: ".$language."\n";
        
        if($debug_email!='')
        {
            mail($debug_email,"Buckaroo Payment service, debug e-mail",$debug_info);
        }
        
        echo "PUSH was processed properly.";
    } 

}