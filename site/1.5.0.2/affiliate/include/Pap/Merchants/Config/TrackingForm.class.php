<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TrackingForm.class.php 29496 2010-10-07 15:42:59Z jsimon $
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
class Pap_Merchants_Config_TrackingForm extends Pap_Merchants_Config_TaskSettingsFormBase {

    const VALIDITY_DAYS = "D";
    const VALIDITY_HOURS = "H";
    const VALIDITY_MINUTES = "M";

    /**
     * @service tracking_setting read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $form->setField(Pap_Settings::DEFAULT_AFFILIATE_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::DEFAULT_AFFILIATE_SETTING_NAME));
        $form->setField(Pap_Settings::SAVE_UNREFERED_SALE_LEAD_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::SAVE_UNREFERED_SALE_LEAD_SETTING_NAME));
        $form->setField(Pap_Settings::FORCE_CHOOSING_PRODUCTID_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::FORCE_CHOOSING_PRODUCTID_SETTING_NAME));
        $form->setField(Pap_Settings::TRACK_BY_IP_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::TRACK_BY_IP_SETTING_NAME));
        $form->setField(Pap_Settings::IP_VALIDITY_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::IP_VALIDITY_SETTING_NAME));
        $form->setField(Pap_Settings::IP_VALIDITY_FORMAT_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::IP_VALIDITY_FORMAT_SETTING_NAME));
        $form->setField(Pap_Settings::AUTO_DELETE_RAWCLICKS,
        Gpf_Settings::get(Pap_Settings::AUTO_DELETE_RAWCLICKS));
        $form->setField(Pap_Settings::ALLOW_COMPUTE_NEGATIVE_COMMISSION,
        Gpf_Settings::get(Pap_Settings::ALLOW_COMPUTE_NEGATIVE_COMMISSION));

        $form->setField(Pap_Settings::MAIN_SITE_URL,
        Gpf_Settings::get(Pap_Settings::MAIN_SITE_URL));
        $form->setField(Pap_Settings::AUTO_DELETE_EXPIRED_VISITORS,
        Gpf_Settings::get(Pap_Settings::AUTO_DELETE_EXPIRED_VISITORS));
        $form->setField(Pap_Settings::SETTING_LINKING_METHOD,
        Gpf_Settings::get(Pap_Settings::SETTING_LINKING_METHOD));
         
        $form->setField(Pap_Settings::SUPPORT_DIRECT_LINKING, Gpf_Settings::get(Pap_Settings::SUPPORT_DIRECT_LINKING));
        $form->setField(Pap_Settings::SUPPORT_SHORT_ANCHOR_LINKING, Gpf_Settings::get(Pap_Settings::SUPPORT_SHORT_ANCHOR_LINKING));

        return $form;
    }

    /**
     * @service tracking_setting write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $this->initValidators($form);
        if (!$form->validate()) {
            return $form;
        }

        Gpf_Settings::set(Pap_Settings::DEFAULT_AFFILIATE_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::DEFAULT_AFFILIATE_SETTING_NAME));
        Gpf_Settings::set(Pap_Settings::SAVE_UNREFERED_SALE_LEAD_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::SAVE_UNREFERED_SALE_LEAD_SETTING_NAME));
        Gpf_Settings::set(Pap_Settings::FORCE_CHOOSING_PRODUCTID_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::FORCE_CHOOSING_PRODUCTID_SETTING_NAME));
        Gpf_Settings::set(Pap_Settings::TRACK_BY_IP_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::TRACK_BY_IP_SETTING_NAME));
        Gpf_Settings::set(Pap_Settings::IP_VALIDITY_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::IP_VALIDITY_SETTING_NAME));
        Gpf_Settings::set(Pap_Settings::IP_VALIDITY_FORMAT_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::IP_VALIDITY_FORMAT_SETTING_NAME));
        Gpf_Settings::set(Pap_Settings::MAIN_SITE_URL, $form->getFieldValue(Pap_Settings::MAIN_SITE_URL));
                Gpf_Settings::set(Pap_Settings::ALLOW_COMPUTE_NEGATIVE_COMMISSION,
        $form->getFieldValue(Pap_Settings::ALLOW_COMPUTE_NEGATIVE_COMMISSION));
        Gpf_Settings::set(Pap_Settings::AUTO_DELETE_EXPIRED_VISITORS,$form->getFieldValue(Pap_Settings::AUTO_DELETE_EXPIRED_VISITORS));
        Gpf_Settings::set(Pap_Settings::SETTING_LINKING_METHOD,
        $form->getFieldValue(Pap_Settings::SETTING_LINKING_METHOD));
        Gpf_Settings::set(Pap_Settings::SUPPORT_DIRECT_LINKING, $form->getFieldValue(Pap_Settings::SUPPORT_DIRECT_LINKING));
        Gpf_Settings::set(Pap_Settings::SUPPORT_SHORT_ANCHOR_LINKING, $form->getFieldValue(Pap_Settings::SUPPORT_SHORT_ANCHOR_LINKING));
        $this->saveDeleteClicks($form);
        $this->insertAutoDeleteExpiredUsersTask();
        
        $form->setInfoMessage($this->_("Tracking saved"));
        return $form;
    }

    private function initValidators(Gpf_Rpc_Form $form) {
        $form->addValidator(new Pap_Merchants_Config_TrackingAutoDeleteWithIpValidityValidator($form), Pap_Settings::AUTO_DELETE_RAWCLICKS);
        $form->addValidator(new Pap_Merchants_Config_TrackingAutoDeleteWithRepeatingClicksValidator($form), Pap_Settings::AUTO_DELETE_RAWCLICKS);
    }

    private function saveDeleteClicks(Gpf_Rpc_Form $form) {
        Gpf_Settings::set(Pap_Settings::AUTO_DELETE_RAWCLICKS, $form->getFieldValue(Pap_Settings::AUTO_DELETE_RAWCLICKS));
        if (Gpf_Settings::get(Pap_Settings::AUTO_DELETE_RAWCLICKS) > 0) {
            $this->insertTask('Pap_Merchants_Config_DeleteClicksTask');
            return;
        }
        $this->removeTask('Pap_Merchants_Config_DeleteClicksTask');
    }
    
    private function insertAutoDeleteExpiredUsersTask(){
        if(Gpf_Settings::get(Pap_Settings::AUTO_DELETE_EXPIRED_VISITORS) == Gpf::YES){
            $this->insertTask('Pap_Tracking_Visit_DeleteVisitorAffiliatesTask');
        } else{
            $this->removeTask('Pap_Tracking_Visit_DeleteVisitorAffiliatesTask');
        }
    }
}
?>
