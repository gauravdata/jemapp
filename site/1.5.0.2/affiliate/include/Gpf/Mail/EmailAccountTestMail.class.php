<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package LiveAgentPro
 *   @since Version 1.0.0
 *   $Id: NewUserRegistration.class.php 18095 2008-05-18 07:32:45Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package LiveAgentPro
 */
class Gpf_Mail_EmailAccountTestMail extends Gpf_Mail_Template {

    public function __construct() {
        parent::__construct();
        $this->mailTemplateFile = 'email_account_test_mail.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Email account test mail');
        $this->subject = $this->templateName;
    }

    protected function initTemplateVariables() {
        $this->addVariable('account_name', $this->_('Mail Account Name'));
        $this->addVariable('from_name', $this->_('From Name'));
        $this->addVariable('account_email', $this->_('From Email'));
        $this->addVariable('use_smtp', $this->_('Use SMTP protocol'));
        $this->addVariable('smtp_server', $this->_('SMTP Server'));
        $this->addVariable('smtp_port', $this->_('SMTP Port'));
        $this->addVariable('smtp_auth', $this->_('SMTP Authentication'));
        $this->addVariable('smtp_ssl', $this->_('Use SSL connection'));
        $this->addVariable('smtp_username', $this->_('SMTP Username'));
        $this->addVariable('is_default', $this->_('Is default mail account'));
    }

    protected function setVariableValues() {
        $this->setVariable('account_name', $this->mailAccount->getAccountName());
        $this->setVariable('from_name', $this->mailAccount->getFromName());
        $this->setVariable('account_email', $this->mailAccount->getAccountEmail());
        if ($this->mailAccount->useSmtp()) {
            $this->setVariable('use_smtp', $this->_('Yes'));
        } else {
            $this->setVariable('use_smtp', $this->_('No'));
        }

        $this->setVariable('smtp_server', $this->mailAccount->getSmtpServer());
        $this->setVariable('smtp_port', $this->mailAccount->getSmtpPort());
        
        if ($this->mailAccount->useSmtpAuth()) {
            $this->setVariable('smtp_auth', $this->_('Yes'));
        } else {
            $this->setVariable('smtp_auth', $this->_('No'));
        }
        if ($this->mailAccount->useSmtpSsl()) {
            $this->setVariable('smtp_ssl', $this->_('Yes'));
        } else {
            $this->setVariable('smtp_ssl', $this->_('No'));
        }
        $this->setVariable('smtp_username', $this->mailAccount->getSmtpUser());
        if ($this->mailAccount->isDefault()) {
            $this->setVariable('is_default', $this->_('Yes'));
        } else {
            $this->setVariable('is_default', $this->_('No'));
        }
    }
}
