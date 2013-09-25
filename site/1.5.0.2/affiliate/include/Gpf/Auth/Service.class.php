<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Service.class.php 33844 2011-07-18 12:21:22Z mkendera $
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
class Gpf_Auth_Service extends Gpf_Object {
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const AUTHTOKEN = 'authToken';
    const ACCOUNTID = 'accountid';
    const REMEMBER_ME = 'rememberMe';
    const USER_ID = 'userid';
    const LANGUAGE = 'language';
    const ROLETYPE = 'roleType';
    const COOKIE_LANGUAGE = 'gpf_language';
    const TIME_OFFSET = 'time_offset';

    /**
     *
     * @service
     * @anonym
     * @param $username
     * @param $password
     * @param $accountId
     * @param $rememberMe
     * @param $language
     * @return Gpf_Rpc_Serializable
     */
    public function authenticate(Gpf_Rpc_Params $params) {
        $loginForm = new Gpf_Rpc_Form($params);

        $username = $this->getFieldValue($loginForm, self::USERNAME);
        $password = $this->getFieldValue($loginForm, self::PASSWORD);
        $authToken = $this->getFieldValue($loginForm, self::AUTHTOKEN);

        $accountId = $this->getFieldValue($loginForm, self::ACCOUNTID);
        $rememberMe = $this->getFieldValue($loginForm, self::REMEMBER_ME, Gpf::NO);
        $language = $this->getFieldValue($loginForm, self::LANGUAGE, Gpf_Lang_Dictionary::getDefaultLanguage());
        $roleType = $this->getFieldValue($loginForm, self::ROLETYPE);

        $result = $this->authenticateNoRpc($username, $password, $accountId, $rememberMe, $language, $roleType, $authToken);
        $this->registerLogin($loginForm);
        return $result;
    }

    public function registerLogin(Gpf_Rpc_Form $loginForm = null) {
        $isFromApi = Gpf::NO;
        if($loginForm != null){
            try{
                $isFromApi = $loginForm->getFieldValue('isFromApi');
            } catch(Gpf_Exception $e) {
            }
        }
        if($isFromApi == Gpf::YES) {
            return;
        }
        $log = new Gpf_Db_LoginHistory();
        $loginDbTime = $log->createDatabase()->getDateString();

        $log->setLoginTime($loginDbTime);
        $log->setIp(Gpf_Http::getRemoteIp());
        $log->setLastRequestTime($loginDbTime);
        $log->setAccountUserId(Gpf_Session::getInstance()->getAuthUser()->getAccountUserId());
        $log->insert();

        Gpf_Session::getInstance()->setVar(Gpf_Db_Table_LoginsHistory::ID, $log->getId());
        Gpf_Session::getInstance()->setVar(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, time());
    }

    private function getFieldValue(Gpf_Rpc_Form $form, $name, $defaultValue = '') {
        try {
            return $form->getFieldValue($name);
        } catch (Gpf_Exception $e) {
            return $defaultValue;
        }
    }

    /**
     *
     * @param $username
     * @param $password
     * @param $accountId
     * @param $rememberMe
     * @return Gpf_Rpc_Form
     */
    public function authenticateNoRpc($username = '', $password = '', $accountId = '', $rememberMe = Gpf::NO, $language = '', $roleType = '', $authToken = '') {
        if ($language == '') {
            $language = Gpf_Lang_Dictionary::getDefaultLanguage();
        }
        $loginForm = $this->createResponseForm();
        $loginForm->setField(self::USERNAME, $username);
        $loginForm->setField(self::PASSWORD, $password);
        $loginForm->setField(self::ACCOUNTID, $accountId);
        $loginForm->setField(self::REMEMBER_ME, $rememberMe);
        $loginForm->setField(self::LANGUAGE, $language);

        try {
            $authInfo = Gpf_Auth_Info::create($loginForm->getFieldValue(self::USERNAME),
                                              $loginForm->getFieldValue(self::PASSWORD),
                                              $accountId,
                                              $roleType,
                                              $authToken);
            if ($authInfo->hasAccount()) {
                return $this->authenticateUser($loginForm, $authInfo);
            }

            $accounts = Gpf_Db_Table_Accounts::getAccounts($authInfo);
            if ($accounts->getSize() == 0) {
                Gpf_Log::info($this->_sys("Wrong username/password (Username: %s)", $username));
                $loginForm->setErrorMessage($this->_("Wrong username/password"));
            } else if ($accounts->getSize() == 1) {
                $authInfo->setAccount($accounts->getRecord(0)->get('accountid'));
                return $this->authenticateUser($loginForm, $authInfo);
            } else if ($accounts->getSize() > 1) {
                $loginForm->setField(self::ACCOUNTID, "select_account", $accounts->toObject());
                $loginForm->setInfoMessage($this->_("Select account"));
            }
        } catch (Gpf_Auth_Exception $e) {
            $loginForm->setErrorMessage($e->getMessage());
        } catch (Gpf_DbEngine_NoRowException $e) {
            Gpf_Log::info($this->_sys("Wrong username/password (Username: %s)", $username));
            $loginForm->setErrorMessage($this->_("Wrong username/password"));
        } catch (Exception $e) {
            if (strlen($username)) {
                Gpf_Log::info($this->_sys("Authentication failed for user %s", $username));
            }
            $loginForm->setErrorMessage($this->_("Authentication failed"));
        }
        return $loginForm;
    }

    /**
     * @return Gpf_Rpc_Form
     */
    protected function createResponseForm() {
        return new Gpf_Rpc_Form();
    }

    /**
     *
     * @param Gpf_Rpc_Form $loginForm
     * @param Gpf_Auth_Info $authInfo
     * @return Gpf_Rpc_Serializable
     */
    protected function authenticateUser(Gpf_Rpc_Form $loginForm, Gpf_Auth_Info $authInfo) {
        $authUser = $this->loadUser($authInfo);
        Gpf_Session::getInstance()->save($authUser);
        $this->setAuthenticationSucesful($loginForm, $authUser);
        Gpf_Session::getInstance()->getAuthUser()->updateIp(Gpf_Http::getRemoteIp());
        return $loginForm;
    }

    /**
     *
     * @return Gpf_Auth_User
     */
    protected function loadUser(Gpf_Auth_Info $authInfo) {
        $authUser = Gpf::newObj(Gpf_Application::getInstance()->getAuthClass());
        return $authUser->load($authInfo);
    }

    protected function setAuthenticationSucesful(Gpf_Rpc_Form $loginForm, Gpf_Auth_User $authUser) {
        try {
            if($loginForm->getFieldValue(self::REMEMBER_ME) == Gpf::YES) {
                $authUser->saveRememberMeCookie();
            }
        } catch (Gpf_Exception $e) {
        }

        $authUser->setLanguage($loginForm->getFieldValue(self::LANGUAGE));
        Gpf_Db_UserAttribute::saveAttribute(self::LANGUAGE, $loginForm->getFieldValue(self::LANGUAGE), $authUser->getAccountUserId());
        Gpf_Db_UserAttribute::saveAttribute(self::TIME_OFFSET, Gpf_Session::getInstance()->getTimeOffset(), $authUser->getAccountUserId());

        Gpf_Http::setCookie(self::COOKIE_LANGUAGE,
        $authUser->getLanguage(), time() + 20000000, '/');

        $loginForm->addField("S", Gpf_Session::getInstance()->getId());

        $loginForm->setInfoMessage($this->_("User authenticated. Logging in."));
        Gpf_Log::info($this->_sys("User %s authenticated. Logging in.", $authUser->getUsername()));
    }

    private function getCookiePrefix() {
        return Gpf_Application::getInstance()->getCode() . '_' .
        Gpf_Session::getRoleType() . '_';
    }

    /**
     * Load default username and password in login form
     *
     * @service
     * @anonym
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        return $this->loadNoRpc();
    }

    /**
     * Load default username and password in login form
     *
     * @return Gpf_Rpc_Form
     */
    public function loadNoRpc() {
        $form = new Gpf_Rpc_Form(new Gpf_Rpc_Params());
        $form->setField(self::REMEMBER_ME, Gpf::YES);

        if (Gpf_Application::isDemo()) {
            $form->setField(self::USERNAME, Gpf_Session::getInstance()->getModule()->getDemoUsername());
            $form->setField(self::PASSWORD, Gpf_Session::getInstance()->getModule()->getDemoPassword());
        }

        $langage = Gpf_Http::getCookie(self::COOKIE_LANGUAGE);

        $form->setField(self::LANGUAGE, $langage, $this->setDefaultLanguage(Gpf_Lang_Languages::getInstance()->getActiveLanguagesNoRpc())->toObject());
        return $form;
    }
    
    /**
     * @param Gpf_Data_IndexedRecordSet $languageRecordSet
     * @return Gpf_Data_IndexedRecordSet
     */
    private function setDefaultLanguage(Gpf_Data_IndexedRecordSet $languageRecordSet) {
        $defaultLanguageCode = Gpf_Lang_Dictionary::getInstance()->getLanguage()->getCode();
        
        foreach ($languageRecordSet as $languageRecord) {
            if ($languageRecord->get(Gpf_Db_Table_Languages::CODE) == $defaultLanguageCode) {
                $languageRecord->set(Gpf_Db_Table_Languages::IS_DEFAULT, Gpf::YES);
            } else {
                $languageRecord->set(Gpf_Db_Table_Languages::IS_DEFAULT, Gpf::NO);
            }
        }
        return $languageRecordSet;
    }

    /**
     *
     * @service authentication logout
     * @return Gpf_Rpc_Action
     */
    public function logout(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        try {
            Gpf_Session::getAuthUser()->logout();
            $action->setInfoMessage($this->_('Logout successful'));
        } catch (Exception $e) {
            $action->setErrorMessage($this->_('Logout error'));
        }
        return $action;
    }

    /**
     *
     * @service authentication logout
     * @return Gpf_Rpc_Action
     */
    public function logoutByURL(Gpf_Rpc_Params $params) {
        try {
            $panelName = Gpf_Session::getModule()->getPanelName();
            Gpf_Session::getAuthUser()->logout();
            Gpf_Http::setHeader('Location', Gpf_Paths::getInstance()->getTopPath() . $panelName . '/login.php');

        } catch (Exception $e) {
            echo $this->_("Logout was not successfull");
        }
    }

    /**
     * Request new password for specified username
     * @service
     * @anonym
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function requestNewPassword(Gpf_Rpc_Params $params) {
        $response = new Gpf_Rpc_Form($params);
        if (!Gpf_Captcha::isValid('lost_pw_captcha', $response->getFieldValue('lost_pw_captcha'))) {
            $response->setFieldError('lost_pw_captcha', $this->_("You have entered invalid security code"));
            return $response;
        }
        try {
            $user = new Gpf_Db_AuthUser();
            $user->setUsername($response->getFieldValue('username'));
            $user->loadFromData(array(Gpf_Db_Table_AuthUsers::USERNAME));
        } catch (Gpf_Exception $e) {
            $response->setFieldError('username', $this->_("You entered invalid username"));
            return $response;
        }

        $mail = new Gpf_Auth_RequestNewPasswordMail();
        $mail->setUser($user);
        $mail->setUrl($response->getFieldValue('applicationUrl'));
        $mail->addRecipient($user->getEmail());
        $mail->sendNow();

        $response->setInfoMessage($this->_("Within a few minutes you will receive instruction on how to reset you password."));
        return $response;
    }

    /**
     * Load username from PasswordRequest
     *
     * @service
     * @anonym
     * @param Gfp_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function loadAuthUserName(Gpf_Rpc_Params $params) {
        $response = new Gpf_Rpc_Form($params);

        $user = $this->getAuthUserFromRequest($params);

        if(is_null($user)) {
            $response->setErrorMessage($this->_('Load auth user from PasswordRequest failed.'));
            return $response;
        }

        $response->setField('username', $user->getUsername());
        return $response;
    }

    private function getAuthUserFromRequest($params) {
        if ($params->exists('requestid') && $params->get('requestid') != null && $params->get('requestid') != '') {
            $requestid = $params->get('requestid');
        } else {
            return null;
        }
        
        $errorMessage = $this->getInvalidPasswordRequestErrorMessage();

        $passwordRequest = new Gpf_Db_PasswordRequest();
        $passwordRequest->setId($requestid);
        try{
            $passwordRequest->load();
        } catch (Gpf_Exception $e) {
            $response->setErrorMessage($errorMessage);
            return null;
        }

        $user = new Gpf_Db_AuthUser();
        $user->setId($passwordRequest->getAuthUser());
        try {
            $user->load();
        } catch (Gpf_Exception $e) {
            $response->setErrorMessage($errorMessage);
            return null;
        }
        return $user;
    }

    /**
     * Set new password for user, which requested new password
     *
     * @service
     * @anonym
     * @param Gfp_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function setNewPassword(Gpf_Rpc_Params $params) {
        $response = new Gpf_Rpc_Form($params);
        if (!Gpf_Captcha::isValid('set_pw_captcha', $response->getFieldValue('set_pw_captcha'))) {
            $response->setFieldError('set_pw_captcha', $this->_("You entered invalid security code"));
            return $response;
        }

        Gpf_Db_Table_PasswordRequests::expireOldRequest();

        $errorMessageInvalidUsername = $this->_('You entered invalid username');

        $user = new Gpf_Db_AuthUser();
        $user->setUsername($response->getFieldValue('username'));
        try {
            $user->loadFromData(array(Gpf_Db_Table_AuthUsers::USERNAME));
        } catch (Gpf_Exception $e) {
            $response->setFieldError('username', $errorMessageInvalidUsername);
            return $response;
        }

        $errorMessage = $this->getInvalidPasswordRequestErrorMessage();

        $passwordRequest = new Gpf_Db_PasswordRequest();
        $passwordRequest->setId($response->getFieldValue('requestid'));
        try{
            $passwordRequest->load();
        } catch (Gpf_Exception $e) {
            $response->setErrorMessage($errorMessage);
            return $response;
        }

        if ($user->getId() != $passwordRequest->getAuthUser()) {
            $response->setFieldError('username', $errorMessageInvalidUsername);
            return $response;
        }

        if ($passwordRequest->getStatus() != Gpf_Db_Table_PasswordRequests::STATUS_PENDING ||
        $user->getUsername() != $response->getFieldValue('username')) {
            $response->setErrorMessage($errorMessage);
            return $response;
        }

        $user->setPassword($response->getFieldValue('password'));
        try {
            $user->update(array(Gpf_Db_Table_AuthUsers::PASSWORD));
        } catch (Gpf_DbEngine_Row_ConstraintException $e) {
            $response->setErrorMessage($e->getMessage());
            return $response;
        }

        $passwordRequest->setStatus(Gpf_Db_Table_PasswordRequests::STATUS_APPLIED);
        $passwordRequest->update(array(Gpf_Db_Table_PasswordRequests::STATUS));

        $response->setInfoMessage($this->_("Your password was changed. Go back to login dialog and login."));
        return $response;
    }

    private function getInvalidPasswordRequestErrorMessage() {
        return $this->_('Invalid password request. You have to request again new password before trying to set password.');
    }
}
?>
