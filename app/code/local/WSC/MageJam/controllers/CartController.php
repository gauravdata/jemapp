<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart controller
 */
include_once('Mage/Checkout/controllers/CartController.php');
class WSC_MageJam_CartController extends Mage_Checkout_CartController
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
     * @return Mage_Checkout_CartController
     */
    public function preDispatch()
    {
	    $this->_authenticate();
        return parent::preDispatch();      
    }

    /**
     * Shopping cart display action
     */
    public function indexAction()
    {
    	$session = Mage::getSingleton('checkout/session');
        $session->unsetData('success');
        $session->getMessages(true);
        
    	$quoteId = $this->getRequest()->getParam('quote_id');
    	 if (!$quoteId) {
            $this->_fault('Requires quote_id as param');
            return;
        }
		$quote = $this->_getCurrentQuote($quoteId);
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
		
		Mage::getSingleton('customer/session')->setCustomer($quote->getCustomer());
		parent::_getCart()->setQuote($quote);
		
        $this->_redirectUrl(Mage::getUrl('checkout/cart'));
        
	//	$this->_redirectUrl(Mage::helper('checkout/url')->getCheckoutUrl());
	//	return parent::indexAction();
    }
    
    /**
     * Loads quote
     *
     * @param $quoteId
     * @return Mage_Sales_Model_Quote
     */
    protected function _getCurrentQuote($quoteId)
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
        $this->_redirect('magejam/cart/result', array('_secure' => true));
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
            $indexUrl = Mage::getUrl('magejam/cart/index', array('_secure' => true));
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

    /**
     * Set back redirect url to response
     *
     * @return Mage_Checkout_CartController
     * @throws Mage_Exception
     */
    protected function _goBack()
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {

            if (!$this->_isUrlInternal($returnUrl)) {
                throw new Mage_Exception('External urls redirect to "' . $returnUrl . '" denied!');
            }

            $this->_getSession()->getMessages(true);
            $this->getResponse()->setRedirect('magejam/cart/index/quote_id/'.$this->_getQuote()->getId());
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('magejam/cart/index/quote_id/'.$this->_getQuote()->getId());
        }
        return $this;
    }
}
