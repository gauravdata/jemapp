<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
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
class Pap_Merchants_Config_ThemesConfig extends Gpf_Object {

    /**
     * @service affiliate_panel_settings write
     *
     * @return Gpf_Rpc_Action
     */
    public function setDefaultTheme(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_("Error changing default theme"));
        $action->setInfoMessage($this->_("Default theme changed"));

        try {
            Gpf_Settings::set($action->getParam("settingName"), $action->getParam('themeId'));
            if ($action->existsParam('allAffiliates') && $action->getParam('allAffiliates') == Gpf::YES) {
                $this->updateThemeExistingAffiliates($action->getParam('themeId'));
            }
            $action->addOk();
        } catch (Exception $e) {
            $action->addError();
        }

        return $action;
    }

    /**
     * @service theme write
     *
     * @return Gpf_Rpc_Action
     */
    public function setTheme(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_("Error changing theme"));
        $action->setInfoMessage($this->_("Theme changed"));

        try {
            $themeId = $action->getParam('themeId');
            Gpf_Session::getAuthUser()->setTheme($themeId);
            Gpf_Settings::set($action->getParam('settingName'), $themeId);
            $action->addOk();
        } catch (Exception $e) {
            $action->addError();
        }

        return $action;
    }

    private function updateThemeExistingAffiliates($themeId) {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Gpf_Db_Table_UserAttributes::getName(), 'ua');
        $update->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'pu', 'ua.'.Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID.'=pu.'.Pap_Db_Table_Users::ACCOUNTUSERID);
        $update->set->add('ua.'.Gpf_Db_Table_UserAttributes::VALUE, $themeId);
        $update->where->add('ua.'.Gpf_Db_Table_UserAttributes::NAME, '=', Gpf_Auth_User::THEME_ATTRIBUTE_NAME);
        $update->where->add('pu.'.Pap_Db_Table_Users::TYPE, '=', Pap_Application::ROLETYPE_AFFILIATE);
        $update->update();
    }


}

?>
