<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic    (created by Rick Braddy / WinningWare.com for PostAffiliatePro)
 *   @package PostAffiliatePro
 *   @since Version 1.0.1
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
class AWeber_Mail extends Gpf_Mail_Template {

    const AFF_NAME = 'aff_name';
    const AFF_EMAIL = 'aff_email';
    
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
        $this->mailTemplate->setSubject('premiumwebcart');  // uses the built-in AWeber "Premium Web Cart" email parser, which must be enabled for target autoresponder
        $this->mailTemplate->setBodyText($this->getBody());
    }

    protected function initTemplateVariables() {
        $this->addVariable(self::AFF_NAME, self::AFF_NAME);
        $this->addVariable(self::AFF_EMAIL, self::AFF_EMAIL);        
    }

    protected function setVariableValues() {
        $this->setVariable(self::AFF_NAME, $this->user->getFirstName());
        $this->setVariable(self::AFF_EMAIL, $this->user->getEmail());
    }

    public function setUser(Pap_Common_User $user) {
        $this->user = $user;
    }
    
    private function getBody() {
        return 'Registering new affiliate ' . "\n\n" .
		'Name: ' . '{$'.self::AFF_NAME.'}' . "\n" .
        'Email: ' . '{$'.self::AFF_EMAIL.'}' . "\n\n";
    }
}
