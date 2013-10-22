<?php
/**
 * Api calss for createRequest (creating payment order)
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */ 
class Comaxx_Docdata_Model_Magento implements Comaxx_Docdata_Model_System {

	/* @var Comaxx_Docdata_Helper_Config */
	private $_config;
	
	/* @var Mage_Sales_Model_Order */
	private $_order;

	/* @var Comaxx_Docdata_Model_Api_Abstract */
	private $_call_api;

	/**
	 * Constructor 
	 *
	 * @return Comaxx_Docdata_Model_Magento Class used for interfacing between Magento and Docdata API
	 */
	public function __construct() {
		$this->_config = Mage::helper('docdata/config');
	}
	
	/**
	 * Checks to see if the last call received error
	 *
	 * @return boolean True if last call has error, otherwise False
	 */
	public function hasError() {
		return $this->_call_api !== null && $this->_call_api->hasError();
	}
	
	/**
	 * Gets the last error message if any
	 *
	 * @return string Returns error message if any, otherwise returns null.
	 */
	public function getErrorMessage() {
		if ($this->_call_api !== null) {
			return $this->_call_api->getErrorMessage();
		}
		return null;
	}
	
	/**
	 * Log message into docdata log
	 *
	 * @param mixed $message message to log
	 * @param string $severity severity level
	 * 
	 * @return void
	 */
	public function log($message, $severity = Comaxx_Docdata_Model_Api_Abstract::SEVERITY_INFO) {
		
		//debug is fallback severity
		$zend_severity = Zend_Log::DEBUG;
		
		//map severity to zend severity
		switch($severity) {
			case Comaxx_Docdata_Model_Api_Abstract::SEVERITY_INFO:
				$zend_severity = Zend_Log::INFO;
				break;
			case Comaxx_Docdata_Model_Api_Abstract::SEVERITY_WARN:
				$zend_severity = Zend_Log::WARN;
				break;
			case Comaxx_Docdata_Model_Api_Abstract::SEVERITY_ERROR:
				$zend_severity = Zend_Log::ERR;
				break;
		}
		
		Mage::helper('docdata')->log($message, $zend_severity);
	}
	
	/**
	 * Translate a string using the Magento helper class
	 *
	 * @param string $string The string to translate
	 *
	 * @return string Translated string
	 */
	public function translate($string) {
		return Mage::helper('docdata')->__($string);
	}
	
	/**
	 * Uses the order data to create a payment order request
	 *
	 * @param Mage_Sales_Model_Order $order Order to perform the create call with
	 * @param array $additional_params Additional parameters to use in the create call
	 *
	 * @return Comaxx_Docdata_Model_Magento instance of the class Comaxx_Docdata_Model_Magento
	 */
	public function createCall(Mage_Sales_Model_Order $order, array $additional_params) {
		// Register given order on object
		$this->_order = $order;
		$helper = Mage::helper('docdata/api_create');
		
		//add elements for the create call
		$call_elements = array();
		$call_elements['version'] = $helper->getApiVersion();
		$call_elements['merchant'] = $helper->getMerchantDetails($order->getStoreId());
		$call_elements['merchantOrderReference'] = $order->getRealOrderId();
		$call_elements['paymentPreferences'] = $helper->getPaymentPreferences();
		$menu_pref = $helper->getMenuPreference();
		if ($menu_pref !== null) {
			$call_elements['menuPreferences'] = $menu_pref;
		}
		$call_elements['shopper'] = $helper->getShopper($order);
		$call_elements['totalGrossAmount'] = $helper->getTotalGrossAmount($order);
		$call_elements['billTo'] = $helper->getBillTo($order);
		$call_elements['invoice'] = $helper->getInvoiceData($order);
		
		// $call_elements['description'] = ''; # Max 50 chars
		
		//call payment methods for additional actions
		$payment = $order->getPayment();
		$method = $payment->getMethodInstance()->getCode();
		//get model belonging to payment method and use it to update the $call_elements
		$model_ref = Mage::helper('docdata/config')->getPaymentMethodItem($method, 'model');
		if ($model_ref !== null) {
			$call_elements = Mage::getModel($model_ref)->updateCreateCall($call_elements, $order, $additional_params);
		}
		
		$response_object = Mage::getModel('docdata/api_response');
		$this->_call_api = Mage::getModel('docdata/api_create')->call($this, $response_object, $call_elements);
		
		return $this;
	}

	/**
	 * Requires the order to cancel and collects the required information for a cancel call from the system and then passes it to the call API
	 *
	 * @param Mage_Sales_Model_Order $order Order object used to peform actions on
	 *
	 * @return Comaxx_Docdata_Model_Magento This instance of the class so additional information may be asked about the result
	 */
	public function cancelCall(Mage_Sales_Model_Order $order) {
		// Register given order on object
		$this->_order = $order;

		// For the cancel call we only need basic info, for which functions are already defined in abstract.
		$helper = Mage::helper('docdata/api_abstract');

		// Collect required data for elements in an array
		$call_elements = array(
			'version' => $helper->getApiVersion(),
			'merchant' => $helper->getMerchantDetails($order->getStoreId()),
			'paymentOrderKey' => $order->getDocdataPaymentOrderKey()
		);

		$response_object = Mage::getModel('docdata/api_response');
		// Create call API object, pass self and the call elements
		$this->_call_api = Mage::getModel('docdata/api_cancel')->call($this, $response_object, $call_elements);

		// Return current instance which will fill the communication between the call API and the system in question
		return $this;
	}

	/**
	 * Requires the order to refund
	 *
	 * @param Mage_Sales_Model_Order $order Order to call a refund on
	 * @param array $additional_params Extra parameters to be used in the call
	 *
	 * @return Comaxx_Docdata_Model_Magento False or instance of the class so additional information may be asked about the result of the failing request
	 */
	public function refundCall(Mage_Sales_Model_Order $order, array $additional_params) {
		//register given order on object
		$this->_order = $order;
		$helper = Mage::helper('docdata/api_abstract');
		$currency = $order->getOrderCurrencyCode();
		
		//check if there is an amount to refund
		if (!empty($additional_params['amount']) && ($additional_params['amount'] <= 0)) {
			return false;
		}
		$refund_amount = Mage::helper('docdata')->getAmountInMinorUnit(
			$additional_params['amount'],
			$currency
		);
		
		//build basic section of refund request call
		$call_elements = array(
			'version' => $helper->getApiVersion(),
			'merchant' => $helper->getMerchantDetails($order->getStoreId())
		);

		//all the optional nodes for the refundRequest
		$optional = array(
			'reference' => 'merchantRefundReference',
			'description' => 'description',
			'item' => 'itemCode'
		);
		
		foreach ($optional as $key => $element_name) {
			if (!empty($additional_params[$key])) {
				$call_elements[$element_name] = $additional_params[$key];
			}
		}
		
		//determine payments for current order (each payment has its own id and amount that is open for refunds)
		$refundable_payments = $this->getRefundablePayments($order);
		$response_object = Mage::getModel('docdata/api_response');
		
		foreach ($refundable_payments as $payment) {
			
			//check if refunding is still needed
			if ($refund_amount <= 0) {
				break;
			}
			
			//determine how much of current payment is to be refunded
			$current_payment_refund;
			if ($refund_amount >= $payment['amount']) {
				$current_payment_refund = $payment['amount'];
			} else {
				//$refund_amount is smaller then the amount that can be refunded on this payment
				//refund only the remaining amount
				$current_payment_refund = $refund_amount;
			}
			
			//amount needs an attribute currency, denoting the currency of the amount, which is defined in the order
			$call_elements['amount'] = array(
				'_' => $current_payment_refund,
				'currency' => $currency
			);
			//set payment id
			$call_elements['paymentId'] = $payment['id'];
			
			//create call API object, pass self and the call elements
			$this->_call_api = Mage::getModel('docdata/api_refund')->call($this, $response_object, $call_elements);
			
			if ($this->_call_api->hasError()) {
				break;
			}
			
			//update outstanding refund request amount
			$refund_amount -= $current_payment_refund;
		}

		//check if last request was successfull and $refund_amount is all requested
		if (!$this->_call_api->hasError() && $refund_amount > 0) {
			Mage::helper('docdata')->log('Refund amount ('.$additional_params['amount'].') was greater then the amount that can be refunded on order '.$order->getRealOrderId());
			return false;
		}
		
		return $this;
	}
	
	/**
	 * Requires the order to refund
	 *
	 * @param Mage_Sales_Model_Order $order Order to get refundable payments for
	 *
	 * @return array List of payments that can be refunded (includes amount and id per payment)
	 */
	public function getRefundablePayments(Mage_Sales_Model_Order $order) {
		
		$result = array();
		//use statuscall to get information about order payments
		$response = $this->statusCall($order, array('QueryOnly' => true));
		
		if ($response->hasError()) {
			//error during status call, cant extract information
			return $result;
		}
		
		//extract payments information
		$payments = $this->_call_api->getNode('report/payment');
		if ($payments !== null && is_array($payments)) {
			foreach ($payments as $payment) {
				$refunded_amount = 0;
				$captured_amount = 0;
				
				//determine how much of a payment is already refunded or requested as refund
				$refunds = $payment->xpath('authorization/refund');
				if ($refunds !== null && is_array($refunds)) {
					foreach ($refunds as $refund) {
						//simply take amount, not relevant if it is already refunded or only requested
						$refunded_amount += (int) $refund->amount;
					}
				}
				
				//determine capture amount
				$captures = $payment->xpath('authorization/capture');
				if ($captures !== null && is_array($captures)) {
					foreach ($captures as $capture) {
						//only accept payments that have captured amounts
						if (strcasecmp((string) $capture->status, 'captured') === 0) {
							$captured_amount += (int) $capture->amount;
						}
					}
				}
				
				//get amount that can still be refunded, and add it to results
				$refundable = $captured_amount - $refunded_amount;
				if ($refundable > 0) {
					$result[] = array(
						'id' => (string) $payment->id,
						'amount' => $refundable
					);
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Peforms a status update call with the given order. Any actions required on the order will also be executed with the same instance of this class
	 *
	 * @param Mage_Sales_Model_Order $order Order to call the status update on
	 * @param array $additional_params Extra parameters to be used in the call
	 *
	 * @return Comaxx_Docdata_Model_Magento This instance of the class so additional information may be asked about the result
	 */
	public function statusCall(Mage_Sales_Model_Order $order, array $additional_params) {
		// Register given order on object
		$this->_order = $order;

		// For the status call we only need basic info, for which functions are already defined in abstract.
		$helper = Mage::helper('docdata/api_abstract');

		// Collect required data for elements in an array
		$call_elements = array(
			'version' => $helper->getApiVersion(),
			'merchant' => $helper->getMerchantDetails($order->getStoreId()),
			'paymentOrderKey' => $order->getDocdataPaymentOrderKey()
		);

		// Create call API object, pass self and the call elements
		$response_object = Mage::getModel('docdata/api_response');
		$status_api = Mage::getModel('docdata/api_status');
		if (isset($additional_params['QueryOnly']) && $additional_params['QueryOnly'] === true) {
			$status_api->setQueryOnly(true);
		}
		$this->_call_api = $status_api->call($this, $response_object, $call_elements);

		// Return current instance which will fill the communication between the call API and the system in question
		return $this;
	}
	
	/**
	 * Sets the given data on the order, a type may be specified if additional filtering is needed.
	 *
	 * @param mixed $data The data to be processed and saved
	 * @param string $type Anything other than generic fields need additional processing.
	 *
	 * @return void
	 */
	public function setOrderData($data, $type = self::DATA_GENERIC) {
		$order = $this->_order;
		
		switch ($type) {
			case self::DATA_PAYMENT:
				$payment = $order->getPayment();
	
				// Add payment id to be inserted
				$order->setDocdataPaymentId($data['id']);
	
				// Update method itself, if changed
				$payment->setMethod(
					Mage::helper('docdata/config')->getPaymentCodeByCommand(
						$data['paymentMethod']
					)
				);
				break;
			case self::DATA_GENERIC:
				foreach ($data as $field => $value) {
					$order->$field = $value;
				}
				break;
		}

		$order->save();
	}

	/**
	 * Enabled the api classes to suggest a range of statusses, which this function needs to process,
	 * check which state we currently have and wether we can advance it to one of the suggested states.
	 *
	 * @param array $statusses A list of statusses/events as defined in the interface of this class
	 * @param int $captured Captured amount in minor unit if applicable
	 * @param int $refunded Refunded amount in minor unit if applicable
	 *
	 * @return void
	 */
	public function setOrderStatus(array $statusses, $captured = null, $refunded = null) {
		$order = $this->_order;

		// 1. Determine which status to use
		// This function takes an array of statusses since in theory an order may become several states at once
		// Usually that wont happen but in a case where something was paid, then cancelled, the cancel should take precedence
		$final_status = null;
		foreach ($statusses as $status => $_msg) {
			// Make sure the first iteration always sets the status
			if ($final_status === null) {
				$final_status = $status;
				$msg = $_msg;
			}

			// Cancelled status takes precedence over all.
			// An order should not be able to receive cancelled state if for some reason a payment is completed later on,
			// because that payment would not be cancelled, and thus the status would not be added.
			// After cancelled we do not want to change stuff if an order actually looks to be paid already
			if ($status === self::STATUS_CLOSED_CANCELED) {
				$final_status = $status;
				$msg = $_msg;
				break;
			} elseif ($status === self::STATUS_CLOSED_PAID) {
				$final_status = $status;
				$msg = $_msg;
			}
		}

		// If there were multiple statusses give, then log that, because that should be weird
		if (count($statusses) > 1) {
			$this->log("Multiple statusses detected. Selected $final_status for use and going on. Other statusses: ".implode(', ', array_keys($statusses)));
		}
		
		$order_id = $order->getRealOrderId();
		
		//do not change an order that is already set to complete state
		if ($order->getState() === Mage_Sales_Model_Order::STATE_COMPLETE) {
			$this->log("Order [$order_id] received update that was ignored (order is already on state 'complete'). ", Zend_Log::INFO);
			return;
		}
		
		// Get possible self specified statusses
		$config = $this->_config;
		$status = $config->getItem($final_status, $config::GROUP_STATUS);
		
		$helper = Mage::helper('docdata');
		
		// Compare with minor unit for better compatibility
		$order_currency_code = $order->getOrderCurrencyCode();
		$online_refunded = $helper->getAmountInMinorUnit($order->getBaseTotalOnlineRefunded(), $order_currency_code);
		$update_refunded = $refunded > $online_refunded;
		$refunded_major = $helper->getAmountInMajorUnit($refunded, $order_currency_code);
		
		// Check if completely refunded
		$refunded_offline = $order->getBaseTotalOfflineRefunded();
		//in case refunded offline is 0/null then refund was triggered in docdata and partial/full refund is determined on order amount
		if ($refunded_major > 0 && !$refunded_offline) {
			//refund status is needed, check if partial or full
			if ($captured > $refunded_major) {
				$status = $config->getItem(self::STATUS_PARTIAL_REFUNDED, $config::GROUP_STATUS);
			} else {
				$status = $config->getItem(self::STATUS_CLOSED_REFUNDED, $config::GROUP_STATUS);
			}
		}
		elseif ($refunded_major > 0 && $refunded_major >= $refunded_offline) {
			// If completely refunded we modify the final_status back to refunded / payment review
			$status = $config->getItem(self::STATUS_CLOSED_REFUNDED, $config::GROUP_STATUS);
		}
		
		// Check if the final status is the same as the current status, which should happen too often
		if ($status === $order->getStatus() && !$update_refunded) {
			$this->log("Ordernr [$order_id] trying to set the order to $final_status status which it is already on.");
			return;
		}
		
		$state = null;
		// 2. Determine what state belongs to that status and what message to add
		switch ($final_status) {
			case self::STATUS_NEW:
				$state = $order::STATE_NEW;
				break;
			case self::STATUS_STARTED:
			case self::STATUS_PARTIAL_PAID:
				//if status of order currently is cancele
				$order = $this->ensureActiveOrder($order);
				$state = $order::STATE_PROCESSING;
				break;
			case self::STATUS_CLOSED_REFUNDED:
				$state = $order::STATE_PAYMENT_REVIEW;
			case self::STATUS_PARTIAL_REFUNDED:
				if ($update_refunded) {
					// Set online refunded in mayor unit
					$order->setBaseTotalOnlineRefunded($refunded_major);
					$order->setTotalOnlineRefunded($refunded_major);
					$order->setBaseTotalRefunded($refunded_major);
					$order->setTotalRefunded($refunded_major);
					$translated = Mage::helper('docdata')->__("Online total refunded amount updated to %s.", $order->getBaseCurrency()->formatTxt($refunded_major));
					$msg = $msg ? $msg : $translated;
				}
				
				// If not yet fully refunded we'll get the right state and be done with it
				$state = $state ? $state : $order::STATE_PENDING_PAYMENT;
				break;
			case self::STATUS_CLOSED_PAID:
				//if status of order currently is cancele
				$order = $this->ensureActiveOrder($order);
				$due = $order->getBaseTotalDue();
				$ordered = $helper->getAmountInMinorUnit($due, $order_currency_code);
				if ($due == 0) {
					$this->log("Order [$order_id] due amount is already 0 ($due), thus it does not need to be registered.", Zend_Log::INFO);
				} elseif ($captured >= $ordered) {
					// Capture amount if captured amount matches or is higher but not when due is already 0
					$order->getPayment()->registerCaptureNotification($due);
					// only change state if order is actually changed
					$state = $order::STATE_PROCESSING;
				} else {
					$this->log("Order [$order_id] set to status PAID but couldn't match due ($ordered) amount with captured ($captured) amount.", Zend_Log::ERR);
				}
				break;
			case self::STATUS_CHARGEBACK:
				//fully refunded is a closed state
				$state = $order::STATE_CLOSED;
				break;
			case self::STATUS_CLOSED_CANCELED:
				$state = $order::STATE_CANCELED;
				break;
			case self::STATUS_ON_HOLD:
				$state = $order::STATE_HOLDED;
				break;
		}

		if ($state !== null) {
			// 3. Set the state, status, and the message, save order
			$order->setState($state, $status, $msg)->save();
		} else {
			$this->log("Ordernr [$order_id] no status change needed", Zend_Log::INFO);
		}

		if ($state === null && $status !== null) {
			$this->log("Ordernr [$order_id] tried setting order to $status but couldn't find a matching state.", Zend_Log::ERR);
		}
	}

	/**
	 * Updates a canceled order so that it is active again
	 * 
	 * @param Mage_Sales_Model_Order $order Canceled order to be made active again
	 *
	 * @return Mage_Sales_Model_Order Active order
	 */
	public function ensureActiveOrder($order) {
		if ($order->getState() === $order::STATE_CANCELED) {
			//first reset order items canceled qty to 0
			foreach ($order->getItemsCollection() as $item) {
				if ($item->getQtyCanceled() > 0) {
					$item->setQtyCanceled(0);
				}
			}
			
			//update order itself by resetting all canceled amounts
			$order->setBaseDiscountCanceled(0)
				->setBaseShippingCanceled(0)
				->setBaseSubtotalCanceled(0)
				->setBaseTaxCanceled(0)
				->setBaseTotalCanceled(0)
				->setDiscountCanceled(0)
				->setShippingCanceled(0)
				->setSubtotalCanceled(0)
				->setTaxCanceled(0)
				->setTotalCanceled(0);
		}
		
		return $order;
	}
	
	/**
	 * Sets the Docdata reference (payment order key) in the current order
	 *
	 * @param string $payment_order_key Docdata payment order key for the current order.
	 *
	 * @return void
	 */
	public function setDocdataPaymentOrderKey($payment_order_key) {
		if ($this->_order !== null) {
			//set key and save order
			$this->_order->setDocdataPaymentOrderKey($payment_order_key)->save();
		}
	}

	/**
	 * Retrieves the WSDL of the API
	 *
	 * @return string WSDL uri of the API
	 */
	public function getWsdlUri() {
		return $this->_config->getWsdlUri();
	}
	
	/**
	 * Checks if debug mode is enabled
	 *
	 * @return boolean True if debug mode is enabled, otherwise False
	 */
	public function isDebugMode() {
		return !$this->_config->isProduction();
	}
	
	/**
	 * Checks confidence level
	 *
	 * @return string of Confidence level
	 */
	public function getConfidenceLevel() {
		return $this->_config->getItem('confidence_level', Comaxx_Docdata_Helper_Config::GROUP_GENERAL);
	}
}