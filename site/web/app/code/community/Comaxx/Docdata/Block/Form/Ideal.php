<?php
/**
 * Ideal issuers block
 *
 * @category Comaxx
 * @package  Comaxx_Docdata
 * @author   Development <development@comaxx.nl>
 */
class Comaxx_Docdata_Block_Form_Ideal extends Mage_Payment_Block_Form {
	protected function _construct() {
		parent::_construct();
		//set template with issuers to be displayed
		$issuers = Mage::helper('docdata/config')->getPaymentMethodItem('docdata_idl', 'issuers');
		$this->setTemplate('comaxx_docdata/form/ideal.phtml')
			 ->setIssuers($issuers);
	}
}