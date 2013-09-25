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
class Pap_Affiliates_Profile_EmailNotificationsForm extends Gpf_Object {
    /**
     * @service affiliate_email_notification read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $this->attribute = Gpf_Db_Table_UserAttributes::getInstance();
        $this->attribute->loadAttributes(Gpf_Session::getAuthUser()->getAccountUserId());

        $form->setField("aff_notification_on_new_sale", $this->attribute->getAttributeWithDefaultValue("aff_notification_on_new_sale", "N"));
        $form->setField("aff_notification_on_change_comm_status", $this->attribute->getAttributeWithDefaultValue("aff_notification_on_change_comm_status", "N"));
        $form->setField("aff_notification_on_subaff_signup", $this->attribute->getAttributeWithDefaultValue("aff_notification_on_subaff_signup", "N"));
        $form->setField("aff_notification_on_subaff_sale", $this->attribute->getAttributeWithDefaultValue("aff_notification_on_subaff_sale", "N"));
        $form->setField("aff_notification_on_direct_link_enabled", $this->attribute->getAttributeWithDefaultValue("aff_notification_on_direct_link_enabled", "N"));
        $form->setField("aff_notification_daily_report", $this->attribute->getAttributeWithDefaultValue("aff_notification_daily_report", "N"));
        $form->setField("aff_notification_weekly_report", $this->attribute->getAttributeWithDefaultValue("aff_notification_weekly_report", "N"));
        $form->setField("aff_notification_monthly_report", $this->attribute->getAttributeWithDefaultValue("aff_notification_monthly_report", "N"));


       	return $form;
    }

    /**
     * @service affiliate_email_notification write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

       	Gpf_Db_Table_UserAttributes::setSetting("aff_notification_on_new_sale", $this->getFieldValue($form, "aff_notification_on_new_sale"), Gpf_Session::getAuthUser()->getAccountUserId());
       	Gpf_Db_Table_UserAttributes::setSetting("aff_notification_on_change_comm_status", $this->getFieldValue($form, "aff_notification_on_change_comm_status"), Gpf_Session::getAuthUser()->getAccountUserId());
       	Gpf_Db_Table_UserAttributes::setSetting("aff_notification_on_subaff_signup", $this->getFieldValue($form, "aff_notification_on_subaff_signup"), Gpf_Session::getAuthUser()->getAccountUserId());
       	Gpf_Db_Table_UserAttributes::setSetting("aff_notification_on_subaff_sale", $this->getFieldValue($form, "aff_notification_on_subaff_sale"), Gpf_Session::getAuthUser()->getAccountUserId());
       	Gpf_Db_Table_UserAttributes::setSetting("aff_notification_on_direct_link_enabled", $this->getFieldValue($form, "aff_notification_on_direct_link_enabled"), Gpf_Session::getAuthUser()->getAccountUserId());
       	Gpf_Db_Table_UserAttributes::setSetting("aff_notification_daily_report", $this->getFieldValue($form, "aff_notification_daily_report"), Gpf_Session::getAuthUser()->getAccountUserId());
       	Gpf_Db_Table_UserAttributes::setSetting("aff_notification_weekly_report", $this->getFieldValue($form, "aff_notification_weekly_report"), Gpf_Session::getAuthUser()->getAccountUserId());
       	Gpf_Db_Table_UserAttributes::setSetting("aff_notification_monthly_report", $this->getFieldValue($form, "aff_notification_monthly_report"), Gpf_Session::getAuthUser()->getAccountUserId());

        $form->setInfoMessage($this->_("Email notifications saved"));
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
