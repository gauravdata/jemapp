<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Versions.class.php 18552 2008-06-17 12:59:40Z aharsani $
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
class Gpf_Install_AcceptLicense extends Gpf_Install_Step {
    const LICENSE_AGREE_NAME = 'LicenseAgree';
    const LICENSE_ID_NAME = 'LicenseNumber';
    const VERSION_NAME = 'Version';
    
    /**
     * @var Gpf_Install_LicenseManager
     */
    protected $licenseManager;
    
    public function __construct() {
        parent::__construct();
        $this->code = 'Accept-License';
        $this->name = $this->_('License'); 
        $this->licenseManager = new Gpf_Install_LicenseManager();
    }
    
    /**
     * @anonym 
     * @param Gpf_Rpc_Params $params
     * @service
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->setField(self::LICENSE_AGREE_NAME, '');
        $form->setField(self::LICENSE_ID_NAME, '');
        $form->setField(self::VERSION_NAME, Gpf_Application::getInstance()->getVersion());
        return $form;
    }
    
    protected function execute(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        if(!$form->getFieldValue(self::LICENSE_AGREE_NAME)) {
            $form->setFieldError(self::LICENSE_AGREE_NAME, $this->_('You have to agree license.'));
            $form->setErrorMessage($this->_('You have to agree license.'));
            return $form;
        }            

        try {
            $setting = new Gpf_File_Settings();
            $settingsArray = $setting->getAll();
            if (is_array($settingsArray) && empty($settingsArray)) {
                $this->addDBDefaultSettings($setting);
            }
            $setting->saveAll();
            @chmod($setting->getSettingFileName(), 0777);
        } catch (Exception $e) {
            $form->setErrorMessage($this->_('Could not create settings file. Reason: %s', $e->getMessage()));
            return $form;
        }
        
        try {
            $info = $this->licenseManager->getLicense(trim($form->getFieldValue(self::LICENSE_ID_NAME)));
            if(!$info->isApplicationCodeValid()) {
                $form->setErrorMessage($this->_('Invalid license.'));
                return $form;
            }
        } catch (Exception $e) {
            $form->setErrorMessage($this->_('Could not validate license. Reason: %s', $e->getMessage()));
            return $form;
        }
        $this->setNextStep($form);
        return $form;
    }

    private function addDBDefaultSettings(Gpf_File_Settings $setting) {
        $setting->setSetting(Gpf_Settings_Gpf::DB_DATABASE, '', false);
        $setting->setSetting(Gpf_Settings_Gpf::DB_HOSTNAME, '', false);
        $setting->setSetting(Gpf_Settings_Gpf::DB_PASSWORD, '', false);
        $setting->setSetting(Gpf_Settings_Gpf::DB_USERNAME, '', false);
    }
}
?>
