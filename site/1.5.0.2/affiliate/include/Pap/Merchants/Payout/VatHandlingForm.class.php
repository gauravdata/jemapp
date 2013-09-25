<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Merchants_Payout_VatHandlingForm extends Pap_Merchants_Payout_InvoiceFormatForm {
    const VAT_COMPUTATION_DEDUCT_FROM_COMMISSION = 'D';
    const VAT_COMPUTATION_ADD_TO_COMMISSION = 'A';

	/**
	 *
	 * @service vat_setting read
	 * @param $fields
	 */
	public function load(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);

        $form->setField(Pap_Settings::SUPPORT_VAT_SETTING_NAME,
            Gpf_Settings::get(Pap_Settings::SUPPORT_VAT_SETTING_NAME));

        $form->setField(Pap_Settings::VAT_PERCENTAGE_SETTING_NAME,
            Gpf_Settings::get(Pap_Settings::VAT_PERCENTAGE_SETTING_NAME));

        $form->setField(Pap_Settings::VAT_COMPUTATION_SETTING_NAME,
            Gpf_Settings::get(Pap_Settings::VAT_COMPUTATION_SETTING_NAME));

        $form->setField(Pap_Settings::PAYOUT_INVOICE_WITH_VAT_SETTING_NAME,
        	Gpf_Settings::get(Pap_Settings::PAYOUT_INVOICE_WITH_VAT_SETTING_NAME));

		return $form;
	}

	/**
	 * @service vat_setting write
	 * @param $fields
	 */
	public function save(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);

		Gpf_Settings::set(Pap_Settings::SUPPORT_VAT_SETTING_NAME,
		  $form->getFieldValue(Pap_Settings::SUPPORT_VAT_SETTING_NAME));
		Gpf_Settings::set(Pap_Settings::VAT_PERCENTAGE_SETTING_NAME,
		  $form->getFieldValue(Pap_Settings::VAT_PERCENTAGE_SETTING_NAME));
		Gpf_Settings::set(Pap_Settings::VAT_COMPUTATION_SETTING_NAME,
		  $form->getFieldValue(Pap_Settings::VAT_COMPUTATION_SETTING_NAME));

        $this->checkAndSavePayoutInvoice($form, $form->getFieldValue(Pap_Settings::PAYOUT_INVOICE_WITH_VAT_SETTING_NAME),
            Pap_Settings::PAYOUT_INVOICE_WITH_VAT_SETTING_NAME);

		return $form;
	}

    protected function getInfoMessage() {
        return $this->_('VAT handling saved');
    }
}

?>
