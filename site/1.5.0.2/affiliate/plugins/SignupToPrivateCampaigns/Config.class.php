<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
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
class SignupToPrivateCampaigns_Config extends Gpf_Plugins_Config {
    const CAMPAIGNS_IDS = 'SignupToPrivateCampaignsIds';
    
    protected function initFields() {
        $this->addTextBox($this->_("private campaigns (divided by comma)"), self::CAMPAIGNS_IDS, $this->_("Private campaigns' IDs (divided by comma) into which will be affiliate sign up after his signup."));        
    }
    
    /**
     * @anonym
     * @service custom_separator write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        if (SignupToPrivateCampaigns_Main::getHandlerInstance()->getCampaignsIdsFromString($form->getFieldValue(self::CAMPAIGNS_IDS)) === false) {
            $form->setErrorMessage('Campaigns IDs must be 8 characters long and be divided by comma!');
            return $form;
        }
        
        Gpf_Settings::set(self::CAMPAIGNS_IDS, str_replace(' ', '',$form->getFieldValue(self::CAMPAIGNS_IDS)));
        $form->setInfoMessage($this->_('Signup To Private Campaigns plugin configuration saved'));
        return $form;
    }


    
    /**
     * @anonym
     * @service custom_separator read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $form->addField(self::CAMPAIGNS_IDS, Gpf_Settings::get(self::CAMPAIGNS_IDS));
        return $form;
    }
}

?>
