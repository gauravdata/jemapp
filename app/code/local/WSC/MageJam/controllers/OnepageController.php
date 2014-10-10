<?php

include_once('Mage/Checkout/controllers/OnepageController.php');
class WSC_MageJam_OnepageController extends Mage_Checkout_OnepageController
{

    /**
     * Authenticates user by basic access authentication
    */
    protected function _authenticate()
    {
        $login = $this->getRequest()->getServer('PHP_AUTH_USER', false);
        $pass = $this->getRequest()->getServer('PHP_AUTH_PW', false);

        /* @var $helper WSC_MageJam_Helper_Auth */
        $helper = Mage::helper('magejam/auth');
        if(!$helper->auth($login, $pass)) {
            header('WWW-Authenticate: Basic realm="magajam realm"');
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }
    }

    /**
     * Predispatch: should set layout area
     *
     * @return Mage_Checkout_OnepageController
     */
    public function preDispatch()
    {
	    $this->_authenticate();
        return parent::preDispatch();      
    }
	
	/**
     * Action for rendering payment methods without any css
     */
    public function indexAction()
    {
    //	Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::helper('core/url')->getCurrentUrl()); 
    	
        /* @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');
        $session->unsetData('success');
        $session->getMessages(true);

        $quoteId = $this->getRequest()->getParam('quote_id');
        if (!$quoteId) {
            $this->_fault('Requires quote_id as param');
            return;
        }

        $quote = $this->_getQuote($quoteId);
        if (!$quote->getId()) {
            $this->_fault('Shopping cart id is not valid');
            return;
        }
     	if (!$quote->getIsActive()){
	        try {
	            /*@var $quote Mage_Sales_Model_Quote*/
	            $quote->setIsActive(true)->save();
	        } catch (Mage_Core_Exception $e) {
	            $this->_fault('create_quote_fault'.$e->getMessage());
	        }
        		
        }
        $session->replaceQuote($quote);

/*        $methodCode = $this->getRequest()->getParam('method');
        if(!$methodCode) {
            $this->_fault('Requires payment method as param');
            return;
        }
*/
        /* @var $helper WSC_MageJam_Helper_Data */
/*        $helper = Mage::helper('magejam');
        $methodInstance = $helper->getMethod($methodCode);
        if(!$methodInstance) {
            $this->_fault('Invalid state for shopping cart');
            return;
        }
*/		
		Mage::getSingleton('customer/session')->setCustomer($quote->getCustomer());
		$checkout = Mage::getSingleton('checkout/type_onepage');
		/*
         * One page checkout consists of following steps
         * (1) Customer login method aka Checkout method
         * (2) Billing information (address)
         * (3) Shipping information (address)
         * (4) Shipping method
         * (5) Payment information
         * (6) Order review, 
         */

		//STEP(1)
        if ($quote->getCustomer()){
			$checkout->saveCheckoutMethod('login');        	
		}else{
			$checkout->saveCheckoutMethod('guest');	
		}
        
        //STEP(2)
        $checkout->saveBilling($quote->getBillingAddress(), false);

        //STEP(3)
        $checkout->saveShipping($quote->getShippingAddress(), false);

         //STEP(4)
        $checkout->saveShippingMethod($quote->getShippingAddress()->getShippingMethod());

        //STEP(5)
		//$quote->getPayment()->setMethod($methodCode)->getMethodInstance();
       //$checkout->savePayment(array('method'=>$methodCode));

		$this->loadLayout();
        $this->renderLayout();
    }
	
	    /**
     * Loads quote
     *
     * @param $quoteId
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote($quoteId)
    {
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel("sales/quote");
        $storeId = Mage::app()->getStore()->getId();
        $quote->setStoreId($storeId)
            ->load($quoteId);

        return $quote;
    }
	
	 /**
     * Used for setting error message into session and redirection to result page
     *
     * @param $message
     */
    protected function _fault($message)
    {
        /* @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');
        $session->setSuccess(false);
        $session->addError($message);
        $this->_redirect('magejam/onepage/result', array('_secure' => true));
    }
	
	/**
     * Used for displaying results in json format
     */
    public function resultAction()
    {
        /* @var $session Mage_Checkout_Model_Session */
        $session = Mage::getSingleton('checkout/session');
        $result = array();

        if(is_null($session->getSuccess())) {
            $result['success'] = false;
            $indexUrl = Mage::getUrl('magejam/onepage/index', array('_secure' => true));
            $result['errors'][] = $this->__('Session is empty, you should start payment once again from ' . $indexUrl);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }

        $result['success'] = $session->getSuccess();
        if($session->getCanceled()) {
            $result['canceled'] = $session->getCanceled();
            $session->unsetData('canceled');
        }
        if($session->getClickedBack()) {
            $result['clicked_back'] = $session->getClickedBack();
            $session->unsetData('clicked_back');
        }
        if ($result['success']) {
            $result['order_id'] = $session->getOrderId();
            $result['order_increment_id'] = $session->getLastRealOrderId();
        } else {
            foreach ($session->getMessages(true)->getErrors() as $error) {
                $result['errors'][] = $error->getText();
            }
            if($session->hasData('goto_section')) {
                $result['goto_section'] = $session->getData('goto_section');
            } else {
                $result['goto_section'] = 'cart';
            }
            if($session->hasData('update_section')) {
                $result['update_section'] = $session->getData('update_section');
            }
            if($session->hasData('fields')) {
                $result['fields'] = $session->getData('fields');
            }
        }

        $session->unsetData('magejam');
        $session->unsetData('success');
        Mage::getSingleton('customer/session')->logout();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}