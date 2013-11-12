<?php
/**
 * Controller used to handle Docdata callbacks
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_PaymentController extends Mage_Core_Controller_Front_Action {
	
	/* @var Mage_Sales_Model_Order */
	protected $order;
	
	/**
	 * Retrieve the order
	 *
	 * @param boolean $forceReload Used to force a reload to get the latest order data
	 * 
	 * @return Mage_Sales_Model_Order
	 */
	protected function getOrder($forceReload = false, $request = null) {
		//reload order if needed
		if ($forceReload || $this->order === null) {
			$orderId = $this->getOrderId($request);
			if($orderId != null) {
				$this->order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
			}
		}

		return $this->order;
	}
	
	/**
	 * Get current order id
	 *
	 * @return null|string
	 */
	protected function getOrderId($request = null) {
		$orderId = null;
		if ($this->order !== null) {
			return $this->order->getIncrementId();
		}
		
		//first check id in request params
		if ($request !== null) {
			$orderId = $request->getParam('id');
			
			//support old plugin id
			if ($orderId === null) {
				$orderId = $request->getParam('mtid');
			}
		}
		//if there is stil no id get it from session
		if (empty($orderId)) {
			$orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		}
		
		return $orderId;
	}

	/**
	 * Cancels the last order for the current user.
	 *
	 * @param string $cancelMsg Message to show the user after he has been redirected to the cart
	 *
	 * @return void
	 */
	protected function cancelOrder($cancelMsg, $request = null, $error = null, $cancelDocdata = false, $restoreCart = true) {
		
		$orderId = $this->getOrderId($request);
		
		if ($orderId != null) {
			if ($error) {
				Mage::helper('docdata')->log('Cancellation of order ' . $orderId . ', reason: ' .$error);
			} else {
				Mage::helper('docdata')->log('Cancellation of order ' . $orderId . ', message: ' . $cancelMsg);
			}
			
			//acquire lock
			//not checking result: if locked failed still continue since customer is waiting on this action
			$lock = $this->getOrderLock($orderId);
			
			//get the latest version of the order is retrieved after lock is acquired
			$order = $this->getOrder(true, $request);
	
			//in case order requires docdata cancel first the order in magento might not need cancel (if docdata rejects)
			if($cancelDocdata) {
				$helper = Mage::helper('docdata');
				$response = Mage::getModel('docdata/magento')->cancelCall($order);
				
				//regardless of outcome continue to cancel in magento
				if ($response->hasError()) {
					$helper->log('Cancel action of an order failed: '.$order->getRealOrderId(), Zend_Log::WARN);
				} else {
					$helper->log('Cancel of the order '.$order->getRealOrderId(). ' succeeded');
				} 
			}
			
			/* Remove the order from the session */
			Mage::getSingleton('customer/session')->setLastOrderId(null);
	
			/* Cancel the order so it isn't open anymore */
			$order->cancel()->save();
			
			if ($restoreCart) {
				/* Restore the last qoute (cart) */
				Mage::helper('docdata')->restoreLastQoute();
			}
			
			/* Set the given message to be shown on the next page load */
			Mage::getSingleton('checkout/session')->addNotice($cancelMsg);
			
			$this->releaseOrderLock($lock);
		}
		
		/* Redirect to the shopping cart*/
		$this->_redirect('checkout/cart');
	}
	
	/**
	 * Action executed to redirect the customer to the Docdata Webmenu page
	 * 
	 * @throws Mage_Payment_Model_Info_Exception
	 *
	 * @return void
	 */
	public function redirectAction() {
		/* retrieve the order */
		$order = $this->getOrder();
		$extra_params = array();
		$pm_code = $this->getRequest()->getParam('pm_code');
		
		Mage::helper('docdata')->log('Redirect Action for the order '.$order->getRealOrderId());
		
		//make sure order still needs to be placed with Docdata
		if ($order->getDocdataPaymentOrderKey() === null) {

			// retrieve parameters sent 
			$extra_params = $this->getRequest()->getParams();
			unset($extra_params['pm_code']);
	
			// add extra parameters for the creation of the payment order 
			$extra_paramsCO = Mage::helper('docdata')->removePrefix(
				Comaxx_Docdata_Model_Method_Abstract::PREFIX_CREATE,
				$extra_params
			);
			
			$errorMsg = false;
			try {
				// creation of the payment order
				$result = Mage::getModel('docdata/magento')->createCall($order, $extra_paramsCO);
			} catch (Comaxx_Docdata_Model_System_Exception $exception) {
				$errorMsg = $exception->getMessage();
			}

			if ($result->hasError()) {
				if (!$errorMsg) {
					$errorMsg = __('We\'re sorry but an error occured trying to create your order. We restored your shopping cart, and you may try again or come back later. We will keep your shopping cart saved if you\'re logged in.');
				}
				
				//error has been seen, cancel order and restore cart.
				$this->cancelOrder(
					$errorMsg,
					null,
					$result->getErrorMessage()
				);
				return;
			}
		}
		
		//order is present prepare values to define the display of the Webmenu page
		$payment_order_key = $order->getDocdataPaymentOrderKey();
		$extra_paramsSO = Mage::helper('docdata')->removePrefix(
			Comaxx_Docdata_Model_Method_Abstract::PREFIX_SHOW,
			$extra_params
		);

		$params = Mage::helper('docdata/api_webmenu')->getParams($order, $pm_code, $payment_order_key, $extra_paramsSO);
		$webmenu_url = Mage::helper('docdata/config')->getWebmenuUri();
	
		// creation of the block that will do the redirection with POST values
		$this->getResponse()->setBody(
			$this->getLayout()
				->createBlock('docdata/webmenu')
				->setWebmenuUrl($webmenu_url)
				->setParams($params)
				->setPaymentOrderExists(false)
				->toHtml()
		);
	}
	
	/**
	 * Action executed at successfull creation of an order
	 * 
	 * @return void
	 */
	public function successAction() {
		$request = Mage::app()->getRequest();
		// retrieve the order 
		$order = $this->getOrder(false, $request);
		
		if ($order != null) {
			Mage::helper('docdata')->log('Success Action for the order '.$order->getRealOrderId());
			//handle new order
			$this->_newOrder($order, $request);
		} else {
			//no order found in session or via URL
			//redirect customer to success page
			$this->_redirect('checkout/onepage/success');
		}
	}
	
	/**
	 * Action executed at successfull creation of an order into the pending state
	 * 
	 * @return void
	 */
	public function pendingAction() {
		$request = Mage::app()->getRequest();
		// retrieve the order 
		$order = $this->getOrder(false, $request);
		
		if ($order != null) {
			Mage::helper('docdata')->log('Pending Action for the order '.$order->getRealOrderId());
			//handle new order
			$this->_newOrder($order, $request);
		} else {
			//no order found in session or via URL
			//redirect customer to success page
			$this->_redirect('checkout/onepage/success');
		}
	}
	
	/**
	 * Action executed when error occurs during handling of an order in the Docdata webmenu
	 * 
	 * @return void
	 */
	public function errorAction() {
		$request = Mage::app()->getRequest();
		// retrieve the order 
		$order = $this->getOrder(false, $request);
		
		if ($order != null) {
			Mage::helper('docdata')->log('Error Action for the order '.$order->getRealOrderId(), Zend_Log::ERR);
		}
		
		$this->cancelOrder(
			$this->__('An error occured during the payment process. We restored your shopping cart, and you may try again or come back later. We will keep your shopping cart saved if you\'re logged in.'),
			$request
		);
	}
	
	/**
	 * Handles the new order actions
	 * 
	 * @param Mage_Sales_Model_Order $order Order that is recently created
	 *
	 * @return void
	 */
	private function _newOrder(Mage_Sales_Model_Order $order, $request = null) {
		
		//acquire lock
		//not checking result: if locked failed still continue since customer is waiting on this action
		$lock = $this->getOrderLock($this->getOrderId($request));
		
		//get the latest version of the order is retrieved after lock is acquired
		$order = $this->getOrder(true, $request);
		
		//update order with latest data
		Mage::getModel('docdata/magento')->statusCall($order, array());
		
		//send email if not done yet
		if (!Mage::getStoreConfig('docdata/general/mail_confirmation_on_paid') && !$order->getEmailSent()) {
			$order->sendNewOrderEmail();
		}
		
		//save order to ensure all changes are kept
		$order->save();
		
		$this->releaseOrderLock($lock);
		
		//redirect customer to success page
		$this->_redirect('checkout/onepage/success');
	}
	
	/**
	 * Action executed when Docdata notifies Magento that there has been an update on an order
	 *
	 * @return void
	 */
	public function updateAction() {
		$error = false;
		
		$request = Mage::app()->getRequest();
		$helper = Mage::helper('docdata');

		// Url should look somewhat like this: domain.tld/docdata/payment/update/id/1234
		$reference = $request->getParam('id');
		
		//support old plugin id
		if($reference === null) {
			$reference = $request->getParam('mtid');
		}
		
		if ($reference === null) {
			// Error, id parameter not found, but was expected
			$helper->log('No id given in the current url', Zend_Log::ERR);
			$error = true;
		}

		$position = strpos($reference, '_');
		$reference = ($position !== false ? substr($reference, 0, $position) : $reference);
		if (strlen($reference) === 0) {
			// Error, could not find underscore
			$helper->log(
				'Reference evaluated to nothing usable, anything after an underscore is stripped, maybe that caused this?',
				Zend_Log::WARN
			);
			$error = true;
		}
		
		//acquire lock for reference
		$lock = $this->getOrderLock($reference);
		
		//retrieve the order to update
		$order = Mage::getModel("sales/order")->loadByIncrementId($reference);
		if ($order->getId() === null || $order === null) {
			// Error, order by the given reference has not been found
			$helper->log(
				'Order not found by given reference, cannot proceed',
				Zend_Log::ERR
			);
			$error = true;
		}
		
		if ($order->getDocdataPaymentOrderKey() === null) {
			// Error, no payment order key found for the order, which is strange, because why would docdata notify us of an order which has never been made with docdata according to our system
			$helper->log(
				'Order does not have an order key, which means Docdata asked us to update something which isn\'t in their system according to our system...',
				Zend_Log::ERR
			);
			$error = true;
		}

		if (!$error) {
			$result = Mage::getModel('docdata/magento')->statusCall($order, array());
		}
		
		$this->releaseOrderLock($lock);
		
		if (isset($result) && $result->hasError()) {
			// Error, error during status update
			$helper->log(
				'An error occured during the update of the order, Docdata backend didn\'t response or data couldn\'t be parsed. Manual action might be required.',
				Zend_Log::ERR
			);
			$error = true;
		} else {
			//send email if not done yet
			if (!Mage::getStoreConfig('docdata/general/mail_confirmation_on_paid') && !$order->getEmailSent()) {
				$order->sendNewOrderEmail();
			}
		
			//save order to ensure all changes are kept
			$order->save();
		}
		
		if ($error) {
			$response = $this->getResponse();
			$response->setBody('<h1>Server Error</h1><p>Required parameters could not be parsed.</p>');
			$response->setHttpResponseCode(500);
		}
	}

	/**
	 * Executed when a user returns from Docdata after canceling the payment themselves
	 *
	 * @return void
	 */
	public function cancelAction() {
		//log external trigger of the cancel action, details are logged in cancelOrder function
		Mage::helper('docdata')->log('Cancel Action');
		
		$cancelMsg = $this->__('Your payment was cancelled upon your request. You can still place your order again later.');
		
		$this->cancelOrder($cancelMsg, Mage::app()->getRequest(), null, true);
	}
	
	/**
	 * Executed when a user returns from Docdata after canceling the payment themselves
	 *
	 * @return void
	 */
	public function abortAction() {
		//log external trigger of the abort action, details are logged in cancelOrder function
		Mage::helper('docdata')->log('Abort Action');
		
		$cancelMsg = $this->__('Your payment was cancelled upon your request.');
		
		$this->cancelOrder($cancelMsg, Mage::app()->getRequest(), null, true, false);
	}
	
	/**
	 * Create a lock object, and try to get lock.
	 * This function blocks until the lock is aquired.
	 *
	 * @param string $orderNr the order increment id
	 *
	 * @return Comaxx_Docdata_Model_Locking|False Returns locking object if lock is acquired otherwise False
	 */
	protected function getOrderLock($orderNr) {
		$lockName	= 'order_'.$orderNr;
		$orderInfo	= 'ordernr ['.$orderNr.'] pid ['.getmypid().'] lock ['.$lockName.']';
		$helper 	= Mage::helper('docdata');
		
		$helper->log(get_class().': Creating lock object. '.$orderInfo);
		$locking = Mage::getModel('docdata/locking', $lockName);
		
		//check if locking model successfully initialized
		if ($locking
			&& $locking instanceof Comaxx_Docdata_Model_Locking
			&& $locking->initCheck()
		) {
			$helper->log(get_class().': Trying to lock object. '.$orderInfo);
			// Get the actual lock, this is blocking until lock is available.
			if (!$locking->lock()) {
				//cant get lock within timeout range
				$helper->log(get_class().': Acquiring lock unsuccessful. '.$orderInfo, Zend_Log::ERR);
				$locking = false;
			} else {
				//lock acquired
				$helper->log(get_class().': lock acquired. '.$orderInfo);
			}
		} else {
			$helper->log(get_class().': Creating lock object failed. '.$orderInfo, Zend_Log::WARN);
			$locking = false;
		}
		return $locking;
	}
	
	/**
	 * Release a lock object.
	 *
	 * @param Comaxx_Docdata_Model_Locking $lock Locking object
	 *
	 * @return void
	 */
	protected function releaseOrderLock(Comaxx_Docdata_Model_Locking $lock) {
		$helper = Mage::helper('docdata');
		
		// Release lock
		if ( $lock
			and $lock instanceof Comaxx_Docdata_Model_Locking
			and $lock->initCheck()
		) {
			$orderInfo = 'pid ['.getmypid().'] lock ['.$lock->getLockCode().']';
			$helper->log(get_class().': Trying to unlock object. '.$orderInfo);
			$lock->unlock();
			$helper->log(get_class().': Unlock performed. '.$orderInfo);
		} else {
			$orderInfo = 'pid ['.getmypid().']';
			$helper->log(get_class().': No lock, so no unlocking needed. '.$orderInfo);
		}
	}
}