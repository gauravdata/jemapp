<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro 
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: AffiliatesGrid.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Affiliates_Promo_LoginForm extends Gpf_Object  {

    public static function getLoginScriptUrl() {
        return Gpf_Paths::getInstance()->getFullBaseServerUrl() . 'affiliates/login.php';
    }
    
    /**
     * @service affiliate_login_form read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Data
     */
    public function load(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);
        
        $loginFormPanel = new Gpf_Ui_TemplatePanel("login_form_downloadable.tpl", "affiliates");    
        $loginFormPanel->add('<input type="text" name="username">', "username");   
        $loginFormPanel->add('<input type="password" name="password">', "password");
        $loginFormPanel->add('<input type="checkBox" name="rememberMe">', "rememberMe");
        $loginFormPanel->add('<input type="submit" value="' . $this->_("Login") . '">', "LoginButton");
        
        $loginFormPanelHtml = '<form action="' . self::getLoginScriptUrl() . '" method="post">' 
                               . $loginFormPanel->render()
                               . '</form>';
        
        $data->setValue("formSource", $loginFormPanelHtml);
        $data->setValue("formPreview", $loginFormPanelHtml);
        
        return $data;
    }
}
?>
