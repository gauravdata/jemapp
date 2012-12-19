<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: ActionParser.class.php 16620 2008-03-21 09:21:07Z aharsani $
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
class AutoResponsePlus_Mail extends Gpf_Mail_Template {

    const AFF_NAME = 'aff_name';
    const AFF_REFID = 'aff_refid';
    const AFF_EMAIL = 'aff_email';
    const AFF_IP = 'aff_ip';
    
    /**
     * @var Pap_Common_User
     */
    private $user;
    
    public function __construct() {
        parent::__construct();
        $this->isHtmlMail = false;
    }
    
    protected function loadTemplate() {
        $this->mailTemplate = new Gpf_Db_MailTemplate();
        $this->mailTemplate->setSubject($this->getSubjectFormat());
        $this->mailTemplate->setBodyText(' ');
    }

    protected function initTemplateVariables() {
        $this->addVariable(self::AFF_NAME, self::AFF_NAME);
        $this->addVariable(self::AFF_REFID, self::AFF_REFID);
        $this->addVariable(self::AFF_EMAIL, self::AFF_EMAIL);
        $this->addVariable(self::AFF_IP, self::AFF_IP);
        
        $this->addVariable(AutoResponsePlus_Config::NAME, AutoResponsePlus_Config::NAME);
        $this->addVariable(AutoResponsePlus_Config::PASSWORD, AutoResponsePlus_Config::PASSWORD);
        $this->addVariable(AutoResponsePlus_Config::AUTORESPONDER_ADDRESS, AutoResponsePlus_Config::AUTORESPONDER_ADDRESS);
        $this->addVariable(AutoResponsePlus_Config::HTML, AutoResponsePlus_Config::HTML);
        $this->addVariable(AutoResponsePlus_Config::TRACKING_TAB, AutoResponsePlus_Config::TRACKING_TAB);
        $this->addVariable(AutoResponsePlus_Config::DROP_RULES, AutoResponsePlus_Config::DROP_RULES);
    }

    protected function setVariableValues() {
        $this->setVariable(self::AFF_NAME, trim($this->user->getFirstName()) . ' ' . trim($this->user->getLastName()));
        $this->setVariable(self::AFF_REFID, trim($this->user->getRefId()));
        $this->setVariable(self::AFF_EMAIL, trim($this->user->getEmail()));
        $this->setVariable(self::AFF_IP, $_SERVER['REMOTE_ADDR']);
        
        $this->setVariable(AutoResponsePlus_Config::NAME, $this->getSettingForSubject(AutoResponsePlus_Config::NAME));
        $this->setVariable(AutoResponsePlus_Config::PASSWORD, $this->getSettingForSubject(AutoResponsePlus_Config::PASSWORD));
        $this->setVariable(AutoResponsePlus_Config::AUTORESPONDER_ADDRESS, $this->getSettingForSubject(AutoResponsePlus_Config::AUTORESPONDER_ADDRESS));
        $this->setVariable(AutoResponsePlus_Config::HTML, (Gpf_Settings::get(AutoResponsePlus_Config::HTML) == Gpf::YES ? 'html' : 'plain'));
        $this->setVariable(AutoResponsePlus_Config::TRACKING_TAB, $this->getSettingForSubject(AutoResponsePlus_Config::TRACKING_TAB));
        $this->setVariable(AutoResponsePlus_Config::DROP_RULES, (Gpf_Settings::get(AutoResponsePlus_Config::DROP_RULES) == Gpf::YES ? 'yes' : 'no'));
    }

    public function setUser(Pap_Common_User $user) {
        $this->user = $user;
    }
    
    private function getSubjectFormat() {
        return 'owner={$'.AutoResponsePlus_Config::NAME.'}'.
        '+password={$'.AutoResponsePlus_Config::PASSWORD.'}'.
        '+action=subscribe'.
        '+name={$'.self::AFF_NAME.'}'.
        '+email={$'.self::AFF_EMAIL.'}'.
        '+autoresponder={$'.AutoResponsePlus_Config::AUTORESPONDER_ADDRESS.'}+{$'.AutoResponsePlus_Config::HTML.'}+TRA{$'.AutoResponsePlus_Config::TRACKING_TAB.'}'.
        '+ip={$'.self::AFF_IP.'}'.
        '+drop={$'.AutoResponsePlus_Config::DROP_RULES.'}';
    }
    
    /**
     * @param String $settingName
	 * @return String
     */
    private function getSettingForSubject($settingName) {
    	return trim(Gpf_Settings::get($settingName));
    }
}
