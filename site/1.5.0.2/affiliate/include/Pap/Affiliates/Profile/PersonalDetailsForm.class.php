<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: AffiliateForm.class.php 18857 2008-06-26 14:57:36Z mbebjak $
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
class Pap_Affiliates_Profile_PersonalDetailsForm extends Pap_Merchants_User_AffiliateForm {

	protected function getId(Gpf_Rpc_Form $form) {
		return Gpf_Session::getAuthUser()->getPapUserId();
	}

	protected function setMandatoryFields(Gpf_Data_RecordSet $formFields, array $mandatoryFields) {
		foreach ($formFields as $field) {
			if ($field->get("code") != "parentuserid") {
				$mandatoryFields[$field->get("code")] = $this->_localize($field->get("name"));
			}
		}
	}
}
?>
