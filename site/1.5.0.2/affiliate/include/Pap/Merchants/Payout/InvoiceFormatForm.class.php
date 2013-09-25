<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: InvoiceFormatForm.class.php 31078 2011-02-07 13:27:56Z mkendera $
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
class Pap_Merchants_Payout_InvoiceFormatForm extends Gpf_Object {
	const DEFAULT_INVOICE_SUBJECT = "Default subject";

    protected function getInfoMessage() {
        return $this->_('Invoice settings saved');
    }

	/**
     * @service invoice_format read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $form->setField(Pap_Settings::GENERATE_INVOICES, Gpf_Settings::get(Pap_Settings::GENERATE_INVOICES));
        $form->setField(Pap_Settings::INVOICE_BCC_RECIPIENT, Gpf_Settings::get(Pap_Settings::INVOICE_BCC_RECIPIENT));

        $form->setField("payoutInvoice",
        	Gpf_Settings::get(Pap_Settings::PAYOUT_INVOICE));
        return $form;
    }

    /**
     * @service invoice_format write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        Gpf_Settings::set(Pap_Settings::GENERATE_INVOICES, $form->getFieldValue(Pap_Settings::GENERATE_INVOICES));
        Gpf_settings::set(Pap_Settings::INVOICE_BCC_RECIPIENT, $form->getFieldValue(Pap_Settings::INVOICE_BCC_RECIPIENT));
        $this->checkAndSavePayoutInvoice($form, $form->getFieldValue('payoutInvoice'), Pap_Settings::PAYOUT_INVOICE);

        return $form;
    }

    protected function checkAndSavePayoutInvoice(Gpf_Rpc_Form $form, $templateSource, $settingName) {
    	$template = new Gpf_Templates_Template($templateSource, '', Gpf_Templates_Template::FETCH_TEXT);
        if ($template->isValid()) {
        	Gpf_Settings::set($settingName, $templateSource);
            $form->setInfoMessage($this->getInfoMessage());
            return;
        }
        $form->setErrorMessage($this->_('Invalid Smarty syntax. More information: ') .
        Gpf_Application::getInstance()->getHelpUrl() . Pap_Common_Constants::SMARTY_SYNTAX_URL);
    }

    /**
     * @deprecated
     */
    public static function replaceConstantsInInvoice($params) {
        if(isset($params['applyVat']) && $params['applyVat'] == Gpf::YES) {
    		$text = $params['payoutInvoiceWithVat'];
    	}  else {
    		$text = $params['payoutInvoice'];
    	}

  		$userFields = Pap_Common_UserFields::getInstance();
  		$userFields->setUser($params['user']);
  		$text = $userFields->replaceUserConstantsInText($text);

  		$text = Pap_Common_UserFields::replaceCustomConstantInText('vat_number', $params['vat_number'], $text);
  		$text = Pap_Common_UserFields::replaceCustomConstantInText('reg_number', $params['reg_number'], $text);
  		$text = Pap_Common_UserFields::replaceCustomConstantInText(Pap_Settings::VAT_PERCENTAGE_SETTING_NAME,
  		    $params['vatPercentage'], $text);
  		$text = Pap_Common_UserFields::replaceCustomConstantInText('payoutcurrency', $params['payout_currency'], $text);
  		$text = Pap_Common_UserFields::replaceCustomConstantInText('affiliate_note', $params['affiliateNote'], $text);
  		$text = Pap_Common_UserFields::replaceCustomConstantInText('payment', $params['payout_clear'], $text);
  		$text = Pap_Common_UserFields::replaceCustomConstantInText('payoutmethod', $params['payout_method'], $text);
  		$text = Pap_Common_UserFields::replaceCustomConstantInText('payment_vat_part', $params['payment_vat_part'], $text);
  		$text = Pap_Common_UserFields::replaceCustomConstantInText('payment_incl_vat', $params['payment_incl_vat'], $text);
  		$text = Pap_Common_UserFields::replaceCustomConstantInText('payment_excl_vat', $params['payment_excl_vat'], $text);

  		$text = Pap_Common_UserFields::replaceCustomConstantInText('date', Gpf_Common_DateUtils::getDateInLocaleFormat(), $text);
  		$text = Pap_Common_UserFields::replaceCustomConstantInText('time', Gpf_Common_DateUtils::getTimeInLocaleFormat(), $text);

  		return $text;
    }
}

?>
