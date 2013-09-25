<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
 *   @since Version 1.0.0
 *   $Id: LanguageAndDate.class.php 18081 2008-05-16 12:17:32Z mfric $
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
class Pap_Features_SplitCommissions_Config extends Pap_Merchants_Config_TaskSettingsFormBase {

    private static $instance;

    /**
     * @return Pap_Features_SplitCommissions_SplitCommissions
     */
    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new Pap_Features_SplitCommissions_Config();
        }
        return self::$instance;
    }

    public function initSettingsEmailNotifications(Gpf_Settings_Gpf $context) {
        $context->addDbSetting(Pap_Features_SplitCommissions_Definition::NOTIFICATION_ON_SALE_SUMMARY, Gpf::YES, true);
        $context->addDbSetting(Pap_Features_SplitCommissions_Definition::NOTIFICATION_ON_SALE_SUMMARY_STATUS, 'A,P,D', true);
    }

    public function saveSettingsEmailNotifications(Gpf_Rpc_Form $form) {
        Gpf_Settings::set(Pap_Features_SplitCommissions_Definition::NOTIFICATION_ON_SALE_SUMMARY_STATUS,
       	$this->getFieldValue($form, Pap_Features_SplitCommissions_Definition::NOTIFICATION_ON_SALE_SUMMARY_STATUS));

       	Gpf_Settings::set(Pap_Features_SplitCommissions_Definition::NOTIFICATION_ON_SALE_SUMMARY,
       	$this->getFieldValue($form, Pap_Features_SplitCommissions_Definition::NOTIFICATION_ON_SALE_SUMMARY));
    }

    public function loadSettingsEmailNotifications(Gpf_Rpc_Form $form) {
        $form->setField(Pap_Features_SplitCommissions_Definition::NOTIFICATION_ON_SALE_SUMMARY,
        Gpf_Settings::get(Pap_Features_SplitCommissions_Definition::NOTIFICATION_ON_SALE_SUMMARY));

        $form->setField(Pap_Features_SplitCommissions_Definition::NOTIFICATION_ON_SALE_SUMMARY_STATUS,
        Gpf_Settings::get(Pap_Features_SplitCommissions_Definition::NOTIFICATION_ON_SALE_SUMMARY_STATUS));   
    }
}

?>
