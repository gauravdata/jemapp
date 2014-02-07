<?php

/**
 * Payment method class for iDEAL
 */
class Comaxx_Docdata_Model_Method_Ideal extends Comaxx_Docdata_Model_Method_Abstract {
	protected $_code = 'docdata_idl';
	protected $_formBlockType = 'docdata/form_ideal';
	
	/**
	 * Assign form data as additional information (called by checkout steps to record data)
	 * @see Mage_Payment_Model_Method_Abstract::assignData()
	 */
	public function assignData($data) {
    	if (!($data instanceof Varien_Object))
			$data = new Varien_Object($data);

		//set data to instance so IDEAL data (issuer ID) can be retrieved in following checkout actions
		$info = $this->getInfoInstance();
		$info->setAdditionalInformation($data->getData());
		return $this;
	}
	
	/**
	 * Return the iDEAL issuer selected by the shopper
	 * 
	 * return int IDEAL issuer ID
	 */
	protected function getIssuer() {
		$data =  $this->getInfoInstance()->getAdditionalInformation();
		return $data['issuer'];
	}
	
	/**
	 * Return parameters specific to the payment method used for the redirection
	 * These additional parameters are used to for the Docdata webmenu in order to display correct payment screen 
	 * 
	 * @return array
	 */
	public function getAdditionalParametersShowOrder() {
		return array(
			'ideal_issuer_id' => $this->getIssuer(),
			'default_act' => 'yes'
		);
	}
}