<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: GeneralSettingsForm.class.php 26315 2009-11-29 17:02:26Z vzeman $
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
class Pap_Merchants_Payout_GeneralSettingsForm extends Gpf_Object {

	/**
	 * @service general_setting read
	 * @param $fields
	 */
	public function load(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);

        $form->setField("payoutOptions",
        	Gpf_Settings::get(
        		Pap_Settings::PAYOUTS_PAYOUT_OPTIONS_SETTING_NAME));

        $form->setField("minimumPayout",
        	Gpf_Settings::get(Pap_Settings::PAYOUTS_MINIMUM_PAYOUT_SETTING_NAME));

		return $form;
	}

	/**
	 * @service general_setting write
	 * @param $fields
	 */
	public function save(Gpf_Rpc_Params $params) {
		$form = new Gpf_Rpc_Form($params);

		Gpf_Settings::set(Pap_Settings::PAYOUTS_PAYOUT_OPTIONS_SETTING_NAME, $form->getFieldValue("payoutOptions"));
		Gpf_Settings::set(Pap_Settings::PAYOUTS_MINIMUM_PAYOUT_SETTING_NAME, $form->getFieldValue("minimumPayout"));

		return $form;
	}


}

?>
