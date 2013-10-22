<?php
/**
 * Payment method class for Complex payment methods
 * Base class for:
 * - Klarna Account
 * - Klarna Invoice
 * - Afterpay?...
 */
class Comaxx_Docdata_Model_Method_Fee extends Comaxx_Docdata_Model_Method_Abstract {
	protected $_formBlockType = 'docdata/form_afterpay';
	protected $_pm_name; // Used for translatable fields (servicecosts)

	public function getPmName() {
		return $this->_pm_name;
	}

	/**
	 * Assign form data as additional information
	 * @see Mage_Payment_Model_Method_Abstract::assignData()
	 */
	public function assignData($data) {
		if (!($data instanceof Varien_Object)) {
			$data = new Varien_Object($data);
		}

		foreach(array(
			'billing_street',
			'billing_housenumber',
			'billing_housenumberaddition',
			'shipping_street',
			'shipping_housenumber',
			'shipping_housenumberaddition',
			'shopper_phonenumber',
		) as $field) {
			if (isset($data[$field])) {
				$data[$field] = trim($data[$field]);
			}
		}

		$info = $this->getInfoInstance();
		$info->setAdditionalInformation($data->getData());

		$quote = Mage::getModel('checkout/cart')->getQuote();
		$billing_address = $quote->getBillingAddress();
		$shipping_address = $quote->getShippingAddress();

		// Update docdata afterpay values
		$billing_address->setDocdataExtraStreet($data['billing_street'])
						->setDocdataExtraHousenumber($data['billing_housenumber'])
						->setDocdataExtraHousenumberAddition($data['billing_housenumberaddition']);

		$node = 'shipping';
		if ($shipping_address->getSameAsBilling()) {
			$node = 'billing';
		}

		$shipping_address->setDocdataExtraStreet($data[$node.'_street'])
						 ->setDocdataExtraHousenumber($data[$node.'_housenumber'])
						 ->setDocdataExtraHousenumberAddition($data[$node.'_housenumberaddition']);

		// Update phone number for docdata
		$billing_address->setDocdataExtraTelephone($data['shopper_phonenumber']);
		$shipping_address->setDocdataExtraTelephone($data['shopper_phonenumber']);


		return $this;
	}

	/**
	 * Return parameters specific to the payment method used for the redirection
	 * These additional parameters are used for the Create Payment Order call
	 *
	 * @return array
	 */
	public function getAdditionalParametersCreateOrder() {
		$info = $this->getInfoInstance();
		//extract billing data
		$billing_house_nr = $info->getAdditionalInformation('billing_housenumber');
		$billing_house_nr_add = $info->getAdditionalInformation('billing_housenumberaddition');

		//in case shipping data is empty it means billing is same as shipping
		$shipping_house_nr = $info->getAdditionalInformation('shipping_housenumber');
		if (!$shipping_house_nr) {
			$shipping_house_nr = $billing_house_nr;
		}
		$shipping_house_nr_add = $info->getAdditionalInformation('shipping_housenumberaddition');
		if (!$shipping_house_nr_add) {
			$shipping_house_nr_add = $billing_house_nr_add;
		}

		//use data to fill additional argument array
		$additional_args = array(
			'billto_houseNumber' => $billing_house_nr,
			'billto_houseNumberaddition' => $billing_house_nr_add,
			'shipto_houseNumber' => $shipping_house_nr,
			'shipto_houseNumberaddition' => $shipping_house_nr_add
		);

		//add phone number
		$phone = $info->getAdditionalInformation('shopper_phonenumber');
		if (strpos($phone, '06') === 0) {
			$additional_args['shopper_mobilePhoneNumber'] = $phone;
		} else {
			$additional_args['shopper_phoneNumber'] = $phone;
		}

		return $additional_args;
	}

	private function _getTaxPercentage($quote_id) {
		$tax_class = Mage::helper('docdata/config')->getPaymentMethodItem($this->_code, 'extra_costs_taxclass');

		//check if there is a tax class
		if (!$tax_class) {
			return 0;
		}

		//get data needed in tax calls
		$quote = $quote = Mage::getModel("sales/quote")->load($quote_id);
		$tax_calc = Mage::getSingleton('tax/calculation');

		//gather tax data to extract percentage
		$customer_tax_class = $quote->getCustomerTaxClassId();

		$request = $tax_calc->getRateRequest($quote->getShippingAddress(), $quote->getBillingAddress(), $customer_tax_class, $quote->getStore());

		return $tax_calc->getRate($request->setProductClassId($tax_class));
	}

	/**
	 * Allows payment method to update data in the create call to Docdata.
	 *
	 * @param array $call_data Data to be used in the create call
	 * @param Mage_Sales_Model_Order $order Order where the create call is made for
	 * @param array $additional_params Extra data can be provided via this array
	 *
	 * @return array Updated call data
	 */
	public function updateCreateCall(array $call_data, Mage_Sales_Model_Order $order, array $additional_params) {

		$country = $order->getShippingAddress()->getCountryId();
		$currency = $order->getOrderCurrencyCode();
		$helper = Mage::helper('docdata');

		// Determine fee/tax data
		$fee = $order->getDocdataFeeAmount();
		$tax = $order->getDocdataFeeTaxAmount();
		$tax_percent = $this->_getTaxPercentage($order->getQuoteId());
		$vat_amount = $helper->getAmountInMinorUnit($tax, $currency);
		$net_amount = $helper->getAmountInMinorUnit($fee, $currency);
		$gross_amount = $helper->getAmountInMinorUnit($fee+$tax, $currency);

		// Tax in totalvat
		$call_data['invoice']['totalVatAmount'][] = array(
			'_' => $vat_amount,
			'rate' => $tax_percent,
			'currency' => $currency
		);

		// Add servicecost item
		$call_data['invoice']['item'][] = array(
			'number' => (count($call_data)+1),
			'name' => $this->_pm_name . ' servicekosten',
			'code' => $this->_pm_name . ' servicekosten',
			'quantity' => array(
				'_' => 1,
				'unitOfMeasure' => 'PCS'
			),
			'description' => $this->_pm_name . ' servicekosten',
			'netAmount' => array(
				'_' => $net_amount,
				'currency' => $currency
			),
			'grossAmount' => array(
				'_' => $gross_amount,
				'currency' => $currency
			),
			'vat' => array(
				'rate' => $tax_percent,
				'amount' => array(
					'_' => $vat_amount,
					'currency' => $currency
				),
				'country' => array(
					'code' => $country
				),
			),
			'totalNetAmount' => array(
				'_' => $net_amount,
				'currency' => $currency
			),
			'totalGrossAmount' => array(
				'_' => $gross_amount,
				'currency' => $currency
			),
			'totalVat' => array(
				'rate' => $tax_percent,
				'amount' => array(
					'_' => $vat_amount,
					'currency' => $currency
				),
				'country' => array(
					'code' => $country
				),
			)
		);

		//set addional params address, shopper
		foreach ($additional_params as $key => $value) {
			switch ($key) {
			case 'billto_houseNumber':
				$call_data['billTo']['address']['houseNumber'] = $value;
				break;
			case 'billto_houseNumberaddition':
				$call_data['billTo']['address']['houseNumberAddition'] = $value;
				break;
			case 'shipto_houseNumber':
				$call_data['invoice']['shipTo']['address']['houseNumber'] = $value;
				break;
			case 'shipto_houseNumberaddition':
				$call_data['invoice']['shipTo']['address']['houseNumberAddition'] = $value;
				break;
			case 'shopper_mobilePhoneNumber':
				$call_data['shopper']['mobilePhoneNumber'] = $value;
				break;
			case 'shopper_phoneNumber':
				$call_data['shopper']['phoneNumber'] = $value;
				break;
			}
		}

		return $call_data;
	}
}