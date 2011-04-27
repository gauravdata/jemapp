<?php
class Total_BuckarooCollect_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
    /**
    * unique internal payment method identifier
    * 
    * @var string [a-z0-9_]
    */
    protected $_code = 'buckaroocollect';

    protected $_formBlockType = 'buckaroocollect/checkout_form';
    /**
     * Here are examples of flags that will determine functionality availability
     * of this module to be used by frontend and backend.
     * 
     * @see all flags and their defaults in Mage_Payment_Model_Method_Abstract
     *
     * It is possible to have a custom dynamic logic by overloading
     * public function can* for each flag respectively
     */
     
    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;
    
    
    // process response code in a different way
    public function processReponseCode($response_code, $order)
    {
        switch($response_code)
        {
            case 600:   $setState=Mage::getStoreConfig('payment/buckaroocollect/order_state_600', Mage::app()->getStore()->getStoreId());
                        $setStatus=Mage::getStoreConfig('payment/buckaroocollect/order_status_600', Mage::app()->getStore()->getStoreId());

                        if(Mage::getStoreConfig('payment/buckaroocollect/send_mails', Mage::app()->getStore()->getStoreId()))
                        {
                            // where to send
                            $email = $order->getCustomerEmail();
                           
                            // who is receiver
                            $billing_info = $order->getBillingAddress();
                            $name = $billing_info->getFirstname().' '.$billing_info->getLastname();
    
                            $translate  = Mage::getSingleton('core/translate');
                            $mail=Mage::getModel('core/email_template')->loadByCode('buckaroocollect_600');
                            
                            // set sender
                            $storeId = Mage::app()->getStore()->getId();
                            $mail->setSenderName(Mage::getStoreConfig('trans_email/ident_sales/name', $storeId));
                            $mail->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email', $storeId));
                            
                            $mail->send($email, $name, array() /* variables */);
                            $translate->setTranslateInline(true);
                        }
                        
                        break;
                        
            case 601:   $setState=Mage::getStoreConfig('payment/buckaroocollect/order_state_601', Mage::app()->getStore()->getStoreId());
                        $setStatus=Mage::getStoreConfig('payment/buckaroocollect/order_state_601', Mage::app()->getStore()->getStoreId());
                        
                        if(!$order->getEmailSent())
                        {
                            $order->sendNewOrderEmail();
                        }
                        
                        break;

            case 602:
            case 603:   
                        $setState=Mage::getStoreConfig('payment/buckaroocollect/order_state_602_603', Mage::app()->getStore()->getStoreId());
                        $setStatus=Mage::getStoreConfig('payment/buckaroocollect/order_state_602_603', Mage::app()->getStore()->getStoreId());
                        
                        if(Mage::getStoreConfig('payment/buckaroocollect/send_mails', Mage::app()->getStore()->getStoreId()))
                        {
                             // where to send
                            $email = Mage::getStoreConfig('trans_email/ident_general/email', Mage::app()->getStore()->getStoreId());
                           
                            // who is receiver
                            $name =  Mage::getStoreConfig('trans_email/ident_general/name', Mage::app()->getStore()->getStoreId());
                            
                            $translate  = Mage::getSingleton('core/translate');
                            $mail=Mage::getModel('core/email_template')->loadByCode('buckaroocollect_602_603');
    
                            // set sender
                            $storeId = Mage::app()->getStore()->getId();
                            $mail->setSenderName(Mage::getStoreConfig('trans_email/ident_sales/name', $storeId));
                            $mail->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email', $storeId));
                            
                            $mail->send($email, $name, array() /* variables */);
                            $translate->setTranslateInline(true);
                        }
                        
                        break;
                        
            default:    $setState=Mage::getStoreConfig('payment/buckaroocollect/order_state_602_603', Mage::app()->getStore()->getStoreId());
                        $setStatus=Mage::getStoreConfig('payment/buckaroocollect/order_state_602_603', Mage::app()->getStore()->getStoreId());
                        break;
        }
        
        return array($setState, $setStatus);
    }
    
    public function getOrderPlaceRedirectUrl()
    {
    	// save quote_id
        Total_Buckaroo_Model_PaymentMethod::saveQuoteId();
        
        $session = Mage::getSingleton('core/session');
    	$session->setData('additional_fields',array('BPE_AccountNumber'=>$_POST['BPE_AccountNumber'], 'BPE_AccountName'=>$_POST['BPE_AccountName']));
    	
    	return Mage::getUrl('buckaroocollect/checkout/redirect', array('_secure' => true));
    }

}
?>