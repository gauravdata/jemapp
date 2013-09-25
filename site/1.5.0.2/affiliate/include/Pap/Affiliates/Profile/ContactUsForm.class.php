<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: LoggingForm.class.php 18882 2008-06-27 12:15:52Z mfric $
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
class Pap_Affiliates_Profile_ContactUsForm extends Gpf_Object {
    /**
     * Processes contact us form and sends email to merchant
     *
     * @service contact_us write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $subject = $this->getFieldValue($form, "subject");
        $text = $this->getFieldValue($form, "text");
        Gpf_Session::getAuthUser()->getPapUserId();

        $user = new Pap_Common_User();
        $user->setPrimaryKeyValue(Gpf_Session::getAuthUser()->getPapUserId());
        try {
        	$user->load();
        } catch (Gpf_DbEngine_NoRowException $e) {
        	$form->setErrorMessage($this->_("User cannot be found"));
        	return $form;
        }

        $mailTemplate = new Pap_Mail_MerchantOnContactUs();
        $mailTemplate->setUser($user);
        $mailTemplate->setEmail($subject, $text);
        $mailTemplate->addRecipient(Pap_Common_User::getMerchantEmail());
        $mailTemplate->setFromEmail($user->getEmail());
        $mailTemplate->setFromName($user->getFirstName()." ".$user->getLastName());
        $mailTemplate->send();

        $form->setInfoMessage($this->_("Email was successfuly sent to merchant"));
        return $form;
    }

    private function getFieldValue(Gpf_Rpc_Form $form, $fieldName) {
    	if($form->existsField($fieldName)) {
    		return $form->getFieldValue($fieldName);
    	}
    	return Gpf::NO;
    }
}

?>
