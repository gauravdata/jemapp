<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: TrackingForm.class.php 18081 2008-05-16 12:17:32Z mfric $
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
class Pap_Merchants_Config_CookiesForm extends Gpf_Object {
    const DEFAULT_DOMAIN_VALIDITY = "";

    /**
     * @service cookies_setting read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $form->setField(Pap_Settings::URL_TO_P3P,
            Gpf_Settings::get(
                Pap_Settings::URL_TO_P3P));
        $form->setField(Pap_Settings::P3P_POLICY_COMPACT,
            Gpf_Settings::get(Pap_Settings::P3P_POLICY_COMPACT));
        $form->setField(Pap_Settings::OVERWRITE_COOKIE,
            Gpf_Settings::get(Pap_Settings::OVERWRITE_COOKIE));
        $form->setField(Pap_Settings::DELETE_COOKIE,
            Gpf_Settings::get(Pap_Settings::DELETE_COOKIE));
        $form->setField(Pap_Settings::COOKIE_DOMAIN,
            Gpf_Settings::get(Pap_Settings::COOKIE_DOMAIN));

        return $form;
    }

    /**
     * @service cookies_setting write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        Gpf_Settings::set(Pap_Settings::URL_TO_P3P, $form->getFieldValue(Pap_Settings::URL_TO_P3P));
        Gpf_Settings::set(Pap_Settings::P3P_POLICY_COMPACT, $form->getFieldValue(Pap_Settings::P3P_POLICY_COMPACT));
        if ($form->existsField(Pap_Settings::OVERWRITE_COOKIE)) {
            Gpf_Settings::set(Pap_Settings::OVERWRITE_COOKIE, $form->getFieldValue(Pap_Settings::OVERWRITE_COOKIE));    
        }        
        Gpf_Settings::set(Pap_Settings::DELETE_COOKIE, $form->getFieldValue(Pap_Settings::DELETE_COOKIE));
        Gpf_Settings::set(Pap_Settings::COOKIE_DOMAIN, $form->getFieldValue(Pap_Settings::COOKIE_DOMAIN));

        $form->setInfoMessage($this->_("Cookies settings saved"));
        return $form;
    }
}

?>
