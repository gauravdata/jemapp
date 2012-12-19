<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
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
class Gpf_Install_SelectLanguage extends Gpf_Install_Step {
    const RELOAD = 'reload';
    const SELECT_LANGUAGE = 'SelectLanguage';
    
    public function __construct() {
        parent::__construct();
        $this->code = 'Select-Language';
        $this->name = $this->_('Select Language'); 
    }
    
    /**
     *
     * @param Gpf_Rpc_Params $params
     * @service
     * @anonym
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        $currentLanguage = Gpf_Session::getAuthUser()->getLanguage();
        
        $form->setField(self::SELECT_LANGUAGE, $currentLanguage, Gpf_Lang_Languages::getInstance()->getActiveLanguagesNoRpc()->toObject());
        return $form;
    }
    
    protected function execute(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        $authUser = Gpf_Session::getAuthUser();
        $languageCode = $form->getFieldValue(self::SELECT_LANGUAGE);

        $this->setNextStep($form);
        if($languageCode != $authUser->getLanguage()) {
            $authUser->setLanguage($languageCode);
            $this->setResponseType($form, self::RELOAD);
            return $form;
        }
        return $form;
    }
    
    /**
     *
     * @return Gpf_Data_RecordSet
     */
    private function getAvailableLanguages() {
        $languages = new Gpf_Data_RecordSet();
        $languages->setHeader(array('id', 'name'));
        foreach (new Gpf_Lang_InstallLanguages() as $language) {
            $languages->add(array($language->getCode(), $language->getExtendedName()));
        }
        return $languages;
    }
}
?>
