<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: FraudprotectionForm.class.php 16669 2008-03-25 16:13:22Z mjancovic $
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
class Pap_Merchants_Config_FraudprotectionSalesForm extends Gpf_Object {


    /**
     * @service fraud_protection read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $form->setField(Pap_Settings::BANNEDIPS_LIST_SALES,
        Gpf_Settings::get(Pap_Settings::BANNEDIPS_LIST_SALES));

        $form->setField(Pap_Settings::BANNEDIPS_SALES,
        Gpf_Settings::get(Pap_Settings::BANNEDIPS_SALES));

        $form->setField(Pap_Settings::BANNEDIPS_SALES_ACTION,
        Gpf_Settings::get(Pap_Settings::BANNEDIPS_SALES_ACTION));

        $form->setField(Pap_Settings::BANNEDIPS_SALES_MESSAGE,
        Gpf_Settings::get(Pap_Settings::BANNEDIPS_SALES_MESSAGE));

        $form->setField(Pap_Settings::DUPLICATE_ORDERS_IP_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_SETTING_NAME));

        $form->setField(Pap_Settings::DUPLICATE_ORDERS_IP_SECONDS_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_SECONDS_SETTING_NAME));

        $form->setField(Pap_Settings::DUPLICATE_ORDERS_IP_ACTION_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_ACTION_SETTING_NAME));

        $form->setField(Pap_Settings::DUPLICATE_ORDERS_IP_MESSAGE_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_MESSAGE_SETTING_NAME));

        $form->setField(Pap_Settings::DUPLICATE_ORDERS_IP_SAMECAMPAIGN_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_SAMECAMPAIGN_SETTING_NAME));

        $form->setField(Pap_Settings::DUPLICATE_ORDERS_IP_SAMEORDERID_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_SAMEORDERID_SETTING_NAME));

        $form->setField(Pap_Settings::DUPLICATE_ORDERS_ID_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_ID_SETTING_NAME));

        $form->setField(Pap_Settings::DUPLICATE_ORDER_ID_HOURS_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDER_ID_HOURS_SETTING_NAME));

        $form->setField(Pap_Settings::APPLY_TO_EMPTY_ID_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::APPLY_TO_EMPTY_ID_SETTING_NAME));

        $form->setField(Pap_Settings::DUPLICATE_ORDERS_ID_ACTION_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_ID_ACTION_SETTING_NAME));

        $form->setField(Pap_Settings::DUPLICATE_ORDERS_ID_MESSAGE_SETTING_NAME,
        Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_ID_MESSAGE_SETTING_NAME));

        Gpf_Plugins_Engine::extensionPoint('FraudProtectionSalesForm.load', $form);

        return $form;
    }

    /**
     * @service fraud_protection write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        Gpf_Settings::set(Pap_Settings::BANNEDIPS_LIST_SALES,
        $form->getFieldValue(Pap_Settings::BANNEDIPS_LIST_SALES));

        Gpf_Settings::set(Pap_Settings::BANNEDIPS_SALES,
        $form->getFieldValue(Pap_Settings::BANNEDIPS_SALES));

        Gpf_Settings::set(Pap_Settings::BANNEDIPS_SALES_ACTION,
        $form->getFieldValue(Pap_Settings::BANNEDIPS_SALES_ACTION));

        Gpf_Settings::set(Pap_Settings::BANNEDIPS_SALES_MESSAGE,
        $form->getFieldValue(Pap_Settings::BANNEDIPS_SALES_MESSAGE));

        Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_IP_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::DUPLICATE_ORDERS_IP_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_IP_SECONDS_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::DUPLICATE_ORDERS_IP_SECONDS_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_IP_ACTION_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::DUPLICATE_ORDERS_IP_ACTION_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_IP_MESSAGE_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::DUPLICATE_ORDERS_IP_MESSAGE_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_IP_SAMECAMPAIGN_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::DUPLICATE_ORDERS_IP_SAMECAMPAIGN_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_IP_SAMEORDERID_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::DUPLICATE_ORDERS_IP_SAMEORDERID_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_ID_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::DUPLICATE_ORDERS_ID_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDER_ID_HOURS_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::DUPLICATE_ORDER_ID_HOURS_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::APPLY_TO_EMPTY_ID_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::APPLY_TO_EMPTY_ID_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_ID_ACTION_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::DUPLICATE_ORDERS_ID_ACTION_SETTING_NAME));

        Gpf_Settings::set(Pap_Settings::DUPLICATE_ORDERS_ID_MESSAGE_SETTING_NAME,
        $form->getFieldValue(Pap_Settings::DUPLICATE_ORDERS_ID_MESSAGE_SETTING_NAME));

        Gpf_Plugins_Engine::extensionPoint('FraudProtectionSalesForm.save', $form);

        $form->setInfoMessage($this->_("Fraud protections saved"));
        return $form;
    }
}
?>
