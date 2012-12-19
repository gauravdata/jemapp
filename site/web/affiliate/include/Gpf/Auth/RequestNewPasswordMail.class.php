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
class Gpf_Auth_RequestNewPasswordMail extends Gpf_Mail_Template {
    /**
     * @var Gpf_Db_AuthUser
     */
    var $user;

    var $url;
    var $fullUrl;

    public function __construct($url = null, Gpf_Db_AuthUser $user = null) {
        parent::__construct();
        $this->mailTemplateFile = 'email_request_new_password.stpl';
        $this->isHtmlMail = true;
        $this->templateName = Gpf_Lang::_runtime('Request new password');
        $this->subject = $this->templateName;
    }

    private function getPasswordRequest() {
        Gpf_Db_Table_PasswordRequests::invalidateOtherRequest($this->user->getId());
        $passwordRequest = new Gpf_Db_PasswordRequest();
        $passwordRequest->setAuthUser($this->user->getId());
        $passwordRequest->insert();
        return $passwordRequest;
    }
    
    protected function initTemplateVariables() {
        $this->addVariable('username', $this->_('Username'));
        $this->addVariable('firstname', $this->_('First Name'));
        $this->addVariable('lastname', $this->_('Last Name'));
        $this->addVariable('newPasswordUrl', $this->_('New password URL'));
        $this->addVariable('newPasswordLink', $this->_('New password link'));
        $this->addVariable('validUntil', $this->_('Request valid until'));
    }

    protected function setVariableValues() {
        $this->setVariable('username', $this->user->getUsername());
        $this->setVariable('firstname', $this->user->getFirstName());
        $this->setVariable('lastname', $this->user->getLastName());
        $this->setVariable('newPasswordUrl', $this->url);
        $this->setVariable('newPasswordLink', '<a href="'.$this->fullUrl.'">'.$this->_('To reset your password click here').'</a>');
        $this->addRecipient($this->user->getEmail());
    }

    protected function setTimeVariableValues($timeOffset = 0) {
        parent::setTimeVariableValues($timeOffset);
        $this->setVariable('validUntil', Gpf_DbEngine_Database::getDateString(time()
            + Gpf_Db_Table_PasswordRequests::VALID_SECONDS + $timeOffset));
    }

    public function setUser(Gpf_Db_AuthUser $user) {
        $this->user = $user;
    }
    
    public function setUrl($requestPasswordUrl) {
    	if ($this->user == null) {
    		throw new Gpf_Exception('You need to setUser() before calling setUrl()');
    	}
    	if (!strlen($requestPasswordUrl)) {
            throw new Gpf_Exception("Received empty URL");
        }
        if ($parsedUrl = parse_url($requestPasswordUrl)) {
            $this->url = $parsedUrl['host'] . $parsedUrl['path'] .
            '?requestid=' . urlencode($this->getPasswordRequest()->getId()) . '#setpasswd';
            $this->fullUrl = $parsedUrl['scheme'] . '://' . $this->url;
        } else {
            throw new Gpf_Exception('Invalid URL: ' . $requestPasswordUrl);
        }
    }
}
