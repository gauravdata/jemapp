<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Matej Kendera
 *   @since Version 1.0.0
 *   $Id: EmailSettingsForms.class.php 25470 2009-09-25 10:05:33Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Settings_RegionalSettingsForms extends Gpf_Object {

    /**
     * @service regional_settings read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->setField(Gpf_Settings_Gpf::REGIONAL_SETTINGS_IS_DEFAULT,
        Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_IS_DEFAULT));
        $form->setField(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DATE_FORMAT,
        Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DATE_FORMAT));
        $form->setField(Gpf_Settings_Gpf::REGIONAL_SETTINGS_TIME_FORMAT,
        Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_TIME_FORMAT));
        $form->setField(Gpf_Settings_Gpf::REGIONAL_SETTINGS_THOUSANDS_SEPARATOR,
        Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_THOUSANDS_SEPARATOR));
        $form->setField(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DECIMAL_SEPARATOR,
        Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DECIMAL_SEPARATOR));

        return $form;
    }

    /**
     * @service regional_settings write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        Gpf_Settings::set(Gpf_Settings_Gpf::REGIONAL_SETTINGS_IS_DEFAULT, $form->getFieldValue(Gpf_Settings_Gpf::REGIONAL_SETTINGS_IS_DEFAULT));

        if (Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_IS_DEFAULT) != Gpf::YES) {
            Gpf_Settings::set(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DATE_FORMAT, $form->getFieldValue(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DATE_FORMAT));
            Gpf_Settings::set(Gpf_Settings_Gpf::REGIONAL_SETTINGS_TIME_FORMAT, $form->getFieldValue(Gpf_Settings_Gpf::REGIONAL_SETTINGS_TIME_FORMAT));
            Gpf_Settings::set(Gpf_Settings_Gpf::REGIONAL_SETTINGS_THOUSANDS_SEPARATOR, $form->getFieldValue(Gpf_Settings_Gpf::REGIONAL_SETTINGS_THOUSANDS_SEPARATOR));
            Gpf_Settings::set(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DECIMAL_SEPARATOR, $form->getFieldValue(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DECIMAL_SEPARATOR));
        }

        $form->setInfoMessage($this->_("Regional settings saved"));
        return $form;
    }
}
?>
