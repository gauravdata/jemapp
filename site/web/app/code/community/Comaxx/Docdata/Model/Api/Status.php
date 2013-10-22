<?php
/**
 * Api class for statusRequest (status update for payment order)
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Model_Api_Status extends Comaxx_Docdata_Model_Api_Abstract {

	const CONFIDENCE_AUTH 	= 'authorization',
		CONFIDENCE_CAPTURE	= 'capture';

	/**
	 * @var Comaxx_Docdata_Model_Magento Parent Api class
	 */
	private $_api;
	/**
	 * @var boolean to check if call is for query purposes only
	 */
	private $_query_only = false;
	
	
	/**
	 * Sets the queryonly property. When set to true the statuscall will not modify the order.
	 *
	 * @param boolean $value Value to set the queryonly property on
	 *
	 * @return void
	 */
	public function setQueryOnly($value) {
		$this->_query_only = $value;
	}
	
	/**
	 * Docdata API method. Send a status request to Docdata
	 *
	 * @param Comaxx_Docdata_Model_System $api System class which provides an interface to the eCommerce system we're in
	 * @param Comaxx_Docdata_Model_Api_Response $response_object Object to access the response of the call
	 * @param array $elements Data for the call
	 *
	 * @return Comaxx_Docdata_Model_Api_Response_Status
	 */
	public function call(Comaxx_Docdata_Model_System $api, Comaxx_Docdata_Model_Api_Response $response_object, array $elements) {
		$this->_api = $api;
		$result = $response_object;
		$confidenceLevel = $this->_api->getConfidenceLevel();
		
		try {
			$this->_api->log('API call Status: ', self::SEVERITY_DEBUG);
			$this->_api->log($elements, self::SEVERITY_DEBUG);

			//perform create call (wrap elements in array as rootelement)
			$client = $this->getConnection($api);
			$client->__soapCall('status', array($elements));
			$result->setResponse(
				$client->__getLastResponse()
			);

		} catch(Exception $ex) {
			$result->setErrorResponse($ex->getMessage());
		}
		
		$this->_api->log('API status call return: ', self::SEVERITY_DEBUG);
		$this->_api->log($result, self::SEVERITY_DEBUG);

		if ($result->hasError()) {
			//log error
			$this->_api->log($result->getErrorMessage(), self::SEVERITY_ERROR);
			return $result;
		}
		
		//if call is done for query purpose only: do not update order
		if($this->_query_only === true) {
			return $result;
		}
		
		$suggests = array();
		
		$totals = $result->getNode('report/approximateTotals');
		$totals = $totals[0];
		
		if(empty($totals)) {
			$this->_api->log(
				'While apearantly no errors occured (this was checked earlier), the totals node was missing, which shouldn\'t happen when all is well.',
				self::SEVERITY_ERROR
			);
		} else {
			// Check the totals for the conditions needed for certain events
			$suggests = $this->_checkTotalsEvents($totals, $confidenceLevel);
		}
		
		$payments = $result->getNode('report/payment');
		if ($payments !== null && is_array($payments)) {
			// Process payments to check if they are all canceled and update the last payment id with a not cancelled state
			$suggests = $suggests + $this->_checkPaymentsEvents($payments);
		}
		
		$fields = array('totalCaptured', 'totalRefunded', 'totalAcquirerApproved');
		foreach ($fields as $field) $$field = (int) $totals->$field;
		//in case of authorization confidence level, check if amount needs to be captured
		if ($confidenceLevel === self::CONFIDENCE_AUTH && $totalCaptured === 0 && $totalAcquirerApproved > 0) {
			$totalCaptured = $totalAcquirerApproved;
		}
		
		// Process all the suggestions taking into account precedence, current status and importance
		$api->setOrderStatus($suggests, $totalCaptured, $totalRefunded);

		return $result;
	}

	/**
	 * Checks the payment of an order and adds suggestions based on it, and triggers the update of the order with the last payment ID
	 *
	 * @param array $payments Array with all the payments which were started for the current order
	 *
	 * @return void
	 */
	private function _checkPaymentsEvents(array $payments) {
		$suggests = array();
		$api = $this->_api;

		$last_non_cancelled = null;
		$last_cancelled = null;
		$refund_candidate = false;
		
		$started_states = array('STARTED', 'NEW');
		$canceled_states = array('FAILED', 'CANCELED');
		
		foreach ($payments as $payment) {
			$captures = (array)$payment->xpath('authorization/capture');
			foreach($captures as $capture) {
				$capture_status = (string)$capture->status;
				
				if (!in_array($capture_status, $canceled_states)
					&& ($last_non_cancelled === null || (int)$payment->id > (int)$last_non_cancelled->id)
				) {
					$last_non_cancelled = $payment;
				}

				if (in_array($capture_status, $canceled_states)
					&& ($last_cancelled === null || (int)$payment->id > (int)$last_cancelled->id)
				) {
					$last_cancelled = $payment;
				}
				
				if (in_array($capture_status, $started_states)) {
					$suggests[$api::STATUS_STARTED] = $api->translate("New pending capture found in Docdata open payments.");
				}
			}
			
			$refunds = (array)$payment->xpath('authorization/refund');
			foreach ($refunds as $refund) {
				if(in_array((string)$refund->status, $started_states)) {
					$refund_candidate = true;
				}
			}
		}
		
		if ($refund_candidate) {
			$suggests[$api::STATUS_PARTIAL_REFUNDED] = $api->translate("New pending refund found in the open payments. Assuming manual refund done in Docdata backoffice.");
		}

		$last = null;
		if ($last_non_cancelled === null && $last_cancelled !== null) {
			$suggests[$api::STATUS_CLOSED_CANCELED] = $api->translate("Canceled because no non-canceled payments could be found.");
			$last = $last_cancelled;
		}

		if ($last_non_cancelled !== null) {
			$last = $last_non_cancelled;
		}

		if ($last !== null) {
			$data = array(
				'id' => (string)$last->id,
				'paymentMethod' => (string)$last->paymentMethod
			);
			// Let the API class figure out how to set that data on the order
			$api->setOrderData($data, $api::DATA_PAYMENT);
		}

		return $suggests;
	}

	/**
	 * Suggests a range of actions for an order based on the totals given. These nodes should be present in the response, according to the xsd.
	 *
	 * @param array $totals The totals nodes in the response of the call.
	 * @param string $confidenceLevel Indicates desired confidenceLevel.
	 *
	 * @return array An array with suggestions which may be empty if no events were found.
	 */
	private function _checkTotalsEvents(SimpleXMLElement $totals, $confidenceLevel) {
		$api = $this->_api;
		$suggests = array();
		// report/
		//     approximateTotals/
		//         totalRegistered			# Amount requested
		//         totalShopperPending		# not relevant or absolute enough, what still needs to be payed.
		//         totalAcquirerPending		# not relevant or absolute enough, what the financial institution is trying to approve
		//         totalAcquirerApproved	# not relevant or absolute enough, what the financial institution says will be payed
		//         totalCaptured			# Total obtained from financial institution
		//         totalRefunded			# Total returned to account of origin at financial institution
		//         exchangedTo				# Currency
		//         exchangeRateDate			# Currency exchange date
		// 
		// All these nodes are all in their minor currency

		// Make variables of everything in the $fields array, for easy use
		$fields = array('totalRegistered', 'totalCaptured', 'totalRefunded', 'totalAcquirerApproved');
		foreach ($fields as $field) $$field = (int) $totals->$field;
		//in case of authorization confidence level, check if amount needs to be captured
		if ($confidenceLevel === self::CONFIDENCE_AUTH && $totalCaptured === 0 && $totalAcquirerApproved > 0) {
			$totalCaptured = $totalAcquirerApproved;
		}

		if($totalRefunded === 0) {
			// If refunded is 0 we will always try to achieve a paid status
			if ($totalCaptured >= $totalRegistered) {
				// The amount captured was greater or equal to the amount requested = closed, paid
				$suggests[$api::STATUS_CLOSED_PAID] = $api->translate("Fully paid according to Docdata approximate totals.");
			} else if ($totalCaptured > 0) {
				// The captured amount did not meet the registered amound but was not 0, which means partially paid
				$suggests[$api::STATUS_PARTIAL_PAID] = $api->translate("Partially paid according to Docdata approximate totals.");
			}
		} else {
			// If refunded is not 0 we will always try to achieve a refunded status
			if ($totalCaptured <= $totalRefunded) {
				// An equal or greater amount was refunded = closed, refunded
				$suggests[$api::STATUS_CLOSED_REFUNDED] = $api->translate("Fully refunded according to Docdata approximate totals.");
			} else {
				// Any other case, refunded was not 0, but the refunded amount did not meet the total captured
				$suggests[$api::STATUS_PARTIAL_REFUNDED] = $api->translate("Partially refunded according to Docdata approximate totals.");
			}
		}

		return $suggests;
	}
}