<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Server.class.php 18000 2008-05-13 16:00:48Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Net_Http_ProxyServerSettings extends Gpf_Object {
    public function __construct() {
    }

    /**
     * @service proxy_setting add
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return $this->save($params);
    }

    /**
     * Save Proxy Server settings
     *
     * @service proxy_setting write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        try {
            Gpf_Settings::set(Gpf_Settings_Gpf::PROXY_SERVER_SETTING_NAME, $form->getFieldValue('server'));
            Gpf_Settings::set(Gpf_Settings_Gpf::PROXY_PORT_SETTING_NAME, $form->getFieldValue('port'));
            Gpf_Settings::set(Gpf_Settings_Gpf::PROXY_USER_SETTING_NAME, $form->getFieldValue('user'));
            Gpf_Settings::set(Gpf_Settings_Gpf::PROXY_PASSWORD_SETTING_NAME, $form->getFieldValue('password'));
        } catch (Gpf_Exception $e) {
            $form->setErrorMessage($this->_('Failed to save settings with error %s', $e->getMessage()));
            return $form;
        }

        $form->setInfoMessage($this->_('Proxy server settings saved.'));
        return $form;
    }


    /**
     *
     * @service proxy_setting read
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        try {
            $form->setField('server', Gpf_Settings::get(Gpf_Settings_Gpf::PROXY_SERVER_SETTING_NAME));
            $form->setField('port', Gpf_Settings::get(Gpf_Settings_Gpf::PROXY_PORT_SETTING_NAME));
            $form->setField('user', Gpf_Settings::get(Gpf_Settings_Gpf::PROXY_USER_SETTING_NAME));
            $form->setField('password', Gpf_Settings::get(Gpf_Settings_Gpf::PROXY_PASSWORD_SETTING_NAME));
        } catch (Gpf_Exception $e) {
            $form->setErrorMessage($this->_('Failed to load proxy server settings.'));
        }
        return $form;
    }

}
?>
