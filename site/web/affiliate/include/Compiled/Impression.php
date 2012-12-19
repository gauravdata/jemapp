<?php
/** *   @copyright Copyright (c) 2008-2009 Quality Unit s.r.o. *   @author Quality Unit *   @package Core classes *   @since Version 1.0.0 *    *   Licensed under the Quality Unit, s.r.o. Dual License Agreement, *   Version 1.0 (the "License"); you may not use this file except in compliance *   with the License. You may obtain a copy of the License at *   http://www.qualityunit.com/licenses/gpf *    */

if (!class_exists('Pap_Tracking_ModuleBase', false)) {
  class Pap_Tracking_ModuleBase extends Gpf_ModuleBase {
      
      public function __construct() {
          parent::__construct('', 'install', 'T');
      }
      
      protected function getTitle() {
          return "";
      }
      
      protected function initCachedData() {
      }
      
      protected function initStyleSheets() {
      }
  }

} //end Pap_Tracking_ModuleBase

if (!class_exists('Pap_AuthUser', false)) {
  class Pap_AuthUser extends Gpf_Auth_User {
  
      /**
       * @var Gpf_Data_Record
       */
      private $userData;
      protected $userId;
      protected $type;
  
      public function getAttributes() {
          $ret = parent::getAttributes();
          if ($this->isLogged()) {
              $user = new Pap_Common_User();
              $user->setId($this->getPapUserId());
              $user->load();
              for ($i=1; $i<=25; $i++) {
                  $ret['data'.$i] = $user->get('data'.$i);
              }
          }
          return $ret;
      }
  
      /**
       * @param Gpf_Auth_Info $authInfo
       * @return Gpf_SqlBuilder_SelectBuilder
       */
      protected function createAuthSelect(Gpf_Auth_Info $authInfo) {
          $select = parent::createAuthSelect($authInfo);
          $select->select->add('pu.'.Pap_Db_Table_Users::REFID, 'refid');
          $select->select->add('pu.'.Pap_Db_Table_Users::NUMBERUSERID, 'numberuserid');
          $select->select->add('pu.'.Pap_Db_Table_Users::PHOTO, 'photo');
          for ($i=1; $i<=25; $i++) {
              $select->select->add('pu.'.Pap_Db_Table_Users::getDataColumnName($i), 'data'.$i);
          }
          $select->select->add('pu.'.Pap_Db_Table_Users::ID, 'userid');
          $select->select->add('pu.'.Pap_Db_Table_Users::TYPE, 'rtype');
          $select->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'pu',
              'pu.accountuserid=u.accountuserid');
          $select->where->add('pu.'.Pap_Db_Table_Users::TYPE, '=', $authInfo->getRoleType());
          return $select;
      }
  
      protected function loadAuthData(Gpf_Data_Record $data) {
          parent::loadAuthData($data);
          $this->userId = $data->get("userid");
          $this->type = $data->get("rtype");
          $this->userData = $data;        
      }
  
      public function getUserData() {
          return $this->userData;
      }
  
      public function isMerchant() {
          return $this->type == Pap_Application::ROLETYPE_MERCHANT;
      }
  
      public function isAffiliate() {
          return $this->type == Pap_Application::ROLETYPE_AFFILIATE;
      }
  
      public function getPapUserId() {
          return $this->userId;
      }
  
      public function createAnonym() {
          return new Pap_AnonymUser();
      }
      
      public function isMasterMerchant() {
          return $this->isDefaultAccount() && $this->isMerchant();
      }
      
      public function isDefaultAccount() {
          return $this->getAccountId() === Gpf_Db_Account::DEFAULT_ACCOUNT_ID;
      }
      
      public function isNetworkMerchant() {
          return !$this->isDefaultAccount() && $this->isMerchant();
      }
  }

} //end Pap_AuthUser

if (!class_exists('Gpf_Auth_Anonym', false)) {
  class Gpf_Auth_Anonym extends Gpf_Auth_User {
  
      public function isLogged() {
          return false;
      }
  
      public function getAccountId() {
          if ($this->accountid === null) {
              throw new Gpf_Exception("No accountId defined for Anonym user");
          }
          return parent::getAccountId();
      }
  
      public function init() {
          $this->theme = '';
  
          parent::init();
      }
  
      public function setTheme($themeId) {
          $this->theme = $themeId;
      }
  
      public function getUserId() {
          throw new Gpf_Exception("No userId defined for Anonymous user");
      }
      
      public function isExists() {
      	return true;
      }
  }
  

} //end Gpf_Auth_Anonym

if (!class_exists('Pap_AnonymUser', false)) {
  class Pap_AnonymUser extends Gpf_Auth_Anonym {
  
      function __construct() {
          $this->accountid = $this->resolveAccountId();
      }
  
      public function isLogged() {
          return false;
      }
  
      private function resolveAccountId() {
          return Gpf_Db_Account::DEFAULT_ACCOUNT_ID;
      }
  
      public function getPapUserId() {
          throw new Gpf_Exception("No userId defined for Anonymous user");
      }
      
      public function isMerchant() {
          return false;
      }
  
      public function isAffiliate() {
          return false;
      }
  
      public function isMasterMerchant() {
          return false;
      }
      
      public function isDefaultAccount() {
          return $this->getAccountId() === Gpf_Db_Account::DEFAULT_ACCOUNT_ID;
      }
      
      public function isNetworkMerchant() {
          return false;
      }
  }

} //end Pap_AnonymUser

if (!class_exists('Gpf_Auth_Service', false)) {
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

} //end Gpf_Auth_Service

if (!class_exists('Gpf_Data_RecordSetNoRowException', false)) {
  class Gpf_Data_RecordSetNoRowException extends Gpf_Exception {
      public function __construct($keyValue) {
          parent::__construct("'Row $keyValue does not exist");
      }
      
      protected function logException() {
      }
  }

} //end Gpf_Data_RecordSetNoRowException

if (!class_exists('Gpf_Desktop_ThemeManager', false)) {
  class Gpf_Desktop_ThemeManager extends Gpf_Object {
  
  
      /**
       * @service theme read
       *
       * @return Gpf_Data_RecordSet
       */
      public function getThemes(Gpf_Rpc_Params $params) {
          if ($params->exists('panelName')) {
              return $this->getThemesNoRpc($params->get('panelName'));
          } else {
              return $this->getThemesNoRpc(Gpf_Session::getModule()->getPanelName(),
              $params->get('filterDisabled'));
          }
      }
  
      /**
       * @return Gpf_Data_RecordSet
       */
      public function getThemesNoRpc($panelName, $filterDisabled = false) {
          $response = new Gpf_Data_RecordSet();
          $response->addColumn(Gpf_Desktop_Theme::ID);
          $response->addColumn(Gpf_Desktop_Theme::NAME);
          $response->addColumn(Gpf_Desktop_Theme::AUTHOR);
          $response->addColumn(Gpf_Desktop_Theme::URL);
          $response->addColumn(Gpf_Desktop_Theme::DESCRIPTION);
          $response->addColumn(Gpf_Desktop_Theme::THUMBNAIL);
          $response->addColumn(Gpf_Desktop_Theme::DESKTOP_MODE);
          $response->addColumn(Gpf_Desktop_Theme::ENABLED);
          $response->addColumn(Gpf_Desktop_Theme::BUILT_IN);
  
          $iterator = new Gpf_Io_DirectoryIterator(Gpf_Paths::getInstance()->getTopTemplatePath() . $panelName, '', false, true);
          foreach ($iterator as $fullName => $themeId) {
              if ($themeId == rtrim(Gpf_Paths::DEFAULT_THEME, '/')) {
                  continue;
              }
              try {
                  $theme = new Gpf_Desktop_Theme($themeId, $panelName);
                  if($filterDisabled && !$theme->isEnabled()){
                      continue;
                  }
                  $response->addRecord($theme->toRecord($response));
              } catch (Gpf_Exception $e) {
                   Gpf_Log::error($e->getMessage());
              }
          }
  
          return $response;
      }
  
      public function getFirstTheme($panelName) {
          $iterator = new Gpf_Io_DirectoryIterator(Gpf_Paths::getInstance()->getTopTemplatePath() . $panelName, '', false, true);
          $themeIds = array();
  
          foreach ($iterator as $fullName => $themeId) {
              try {
                  $theme = new Gpf_Desktop_Theme($themeId, $panelName);
                  if (strlen($themeId) > 0 && $themeId[0] != '_' && $theme->isEnabled()) {
                      $themeIds[] = $themeId;
                  }
              } catch (Gpf_Exception $e) {
                  Gpf_Log::debug('Exception during reading theme directory: ' .$e->getMessage());
              }
          }
  
          if (count($themeIds) > 0) {
              sort($themeIds, SORT_STRING);
              return $themeIds[0];
          }
  
          throw new Gpf_Exception($this->_("No available theme") . ': ' . Gpf_Paths::getInstance()->getTopTemplatePath() . $panelName);
      }
  
      /**
       * @service theme write
       *
       * @return Gpf_Rpc_Action
       */
      public function toggleThemeEnabled(Gpf_Rpc_Params $params) {
          $action = new Gpf_Rpc_Action($params);
          try {
              $panel = $action->getParam('panelName');
              $themeId = $action->getParam('themeId');
              $theme = new Gpf_Desktop_Theme($themeId, $panel);
              if($theme->isEnabled()&& !$this->canDisableTheme($panel)){
                  $action->setInfoMessage($this->_('One theme should be enabled'));
              }else{
                  $theme->setEnabled(!$theme->isEnabled());
              }
              $action->addOk();
          } catch (Exception $e) {
              $action->addError();
              $action->setErrorMessage($e->getMessage());
          }
          return $action;
      }
  
      private function canDisableTheme($panelName){
          $iterator = new Gpf_Io_DirectoryIterator(Gpf_Paths::getInstance()->getTopTemplatePath() . $panelName, '', false, true);
          $enabledCount = 0;
          foreach ($iterator as $fullName => $themeId) {
              if ($themeId == rtrim(Gpf_Paths::DEFAULT_THEME, '/')) {
                  continue;
              }
              try {
                  $theme = new Gpf_Desktop_Theme($themeId, $panelName);
                  if($theme->isEnabled()){
                      $enabledCount++;
                      if($enabledCount == 2){
                          return true;
                      }
                  }
              } catch (Gpf_Exception $e) {
                  Gpf_Log::error($e->getMessage());
              }
          }
          return false;
      }
  
      /**
       * @service theme write
       *
       * @return Gpf_Rpc_Action
       */
      public function setTheme(Gpf_Rpc_Params $params) {
          $action = new Gpf_Rpc_Action($params);
          $action->setErrorMessage($this->_("Error changing theme"));
          $action->setInfoMessage($this->_("Theme changed"));
  
          try {
              $themeId = $action->getParam('themeId');
              Gpf_Session::getAuthUser()->setTheme($themeId);
              $action->addOk();
          } catch (Exception $e) {
              $action->addError();
          }
  
          return $action;
      }
  }
} //end Gpf_Desktop_ThemeManager

if (!class_exists('Gpf_Io_DirectoryIterator', false)) {
  class Gpf_Io_DirectoryIterator extends Gpf_Object implements Iterator {
      private $directory;
      private $recursive;
      private $onlyDirectories;
      private $files;
      private $pos = -1;
      private $extension;
      private $iterator = null;
      private $ignoreDirectories = array();
      private $ignoreAbsoluteDirectories = array();
  
      public function __construct($directory, $extension = '', $recursive = false, $onlyDirectories = false) {
          $this->directory = $this->normalizeDirectory($directory);
          $this->recursive = $recursive;
          $this->extension = $extension;
          $this->onlyDirectories = $onlyDirectories;
      }
  
      private function normalizeDirectory($dir) {
          if(strlen($dir) <= 0) {
              return false;
          }
          $dir = str_replace('\\', '/', $dir);
          if(substr($dir, -1) != '/') {
              $dir .= '/';
          }
          return $dir;
      }
  
      public function current() {
          return $this->iterator->current();
      }
  
      public function key() {
          return $this->iterator->key();
      }
  
      public function next() {
          if($this->iterator != null && $this->iterator->valid()) {
              $this->iterator->next();
              if(!$this->iterator->valid()){
                  $this->iterator = null;
                  $this->next();
              }
          }
          while($this->iterator == null || !$this->iterator->valid()) {
              $this->pos++;
              if(!$this->valid()) {
                  return;
              }
              $this->iterator = $this->files[$this->pos];
              $this->iterator->rewind();
          }
      }
  
      public function rewind() {
          $this->files = array();
  
          if (!($handle = @opendir($this->directory))) {
              $this->next();
              return;
          }
          while (false !== ($file = readdir($handle))) {
              if ($file == "." || $file == "..") {
                  continue;
              }
              $filename = $this->directory . $file;
              if ($this->onlyDirectories && @is_dir($filename . '/')) {
                  $this->files[$file] = new Gpf_Io_FileIterator($filename, $file);
                  continue; 
              }
              if(@is_dir($filename . '/')) {
                  if($this->recursive && !in_array($filename . '/', $this->ignoreAbsoluteDirectories) &&
                  !in_array($file, $this->ignoreDirectories)
                  ) {
                      $this->files[$file] = $this->create($filename . '/');
                  }
              } else if (!$this->onlyDirectories) {
                  if($this->hasExtension($file)) {
                      $this->files[$file] = new Gpf_Io_FileIterator($filename, $file);
                  }
              }
          }
          ksort($this->files);
          $this->files = array_values($this->files);
          closedir($handle);
          $this->next();
      }
  
      private function create($directory) {
          $iterator = new Gpf_Io_DirectoryIterator($directory, $this->extension, $this->recursive);
          $iterator->setIgnoreDirectories($this->ignoreDirectories);
          $iterator->setIgnoreAbsoluteDirectories($this->ignoreAbsoluteDirectories);
          return $iterator;
      }
  
      private function hasExtension($file) {
          if($this->extension != '') {
              if(false === strrpos($file, $this->extension) 
                  || strrpos($file, $this->extension) != strlen($file) - strlen($this->extension)) {
                  return false;
              }
          }
          return true;
      }
  
      public function valid() {
          return $this->pos < count($this->files);
      }
  
      public function addIgnoreDirectory($dir) {
          $this->ignoreDirectories[] = $dir;
      }
  
      public function addIgnoreAbsoluteDirectory($dir) {
          $this->ignoreAbsoluteDirectories[] = $this->normalizeDirectory($dir);
      }
      
      public function setIgnoreDirectories(array $dirs) {
          $this->ignoreDirectories = $dirs;
      }
  
      public function setIgnoreAbsoluteDirectories($dirs) {
          $this->ignoreAbsoluteDirectories = $dirs;
      }
  }
  
  class Gpf_Io_FileIterator extends Gpf_Object implements Iterator {
      private $file;
      private $fullFileName;
      private $valid = true;
      
      public function __construct($fullFileName, $file) {
          $this->file = $file;
          $this->fullFileName = $fullFileName;
      }
  
      public function current() {
          return $this->file;
      }
  
      public function key() {
          return $this->fullFileName;
      }
  
      public function next() {
          $this->valid = false;
      }
  
      public function rewind() {
      }
  
      public function valid() {
          return $this->valid;
      }
  }
} //end Gpf_Io_DirectoryIterator

if (!class_exists('Pap_Db_RawImpression', false)) {
  class Pap_Db_RawImpression extends Gpf_DbEngine_Row {
  
      private $index;
  
      public function __construct($index){
          $this->index = $index;
          parent::__construct();
      }
  
      protected function init() {
          $this->setTable(Pap_Db_Table_RawImpressions::getInstance($this->index));
          parent::init();
      }
      
      public function getDate() {
          return $this->get(Pap_Db_Table_RawImpressions::DATE);
      }
      
      public function setUserId($id) {
          $this->set(Pap_Db_Table_RawImpressions::USERID, $id);
      }
      
      public function getUserId() {
          return $this->get(Pap_Db_Table_RawImpressions::USERID);
      }
      
      public function setBannerId($id) {
          $this->set(Pap_Db_Table_RawImpressions::BANNERID, $id);
      }
      
      public function getBannerId() {
          return $this->get(Pap_Db_Table_RawImpressions::BANNERID);
      }
      
      public function getParentBannerId() {
          return $this->get(Pap_Db_Table_RawImpressions::PARENTBANNERID);
      }
      
      public function setChannel($id) {
          $this->set(Pap_Db_Table_RawImpressions::CHANNEL, $id);
      }
      
      public function getChannel() {
          return $this->get(Pap_Db_Table_RawImpressions::CHANNEL);
      }
      
      public function getIp() {
          return $this->get(Pap_Db_Table_RawImpressions::IP);
      }
      
      public function getData1() {
          return $this->get(Pap_Db_Table_RawImpressions::DATA1);
      }
      
      public function getData2() {
          return $this->get(Pap_Db_Table_RawImpressions::DATA2);
      }
      
      public function isUnique() {
          return $this->get(Pap_Db_Table_RawImpressions::RTYPE) == 'U';
      }
  }
  
  

} //end Pap_Db_RawImpression

if (!class_exists('Pap_Db_Table_RawImpressions', false)) {
  class Pap_Db_Table_RawImpressions extends Gpf_DbEngine_Table {
      const ID = 'impressionid';
      const DATE = 'date';
      const RTYPE = 'rtype';
      const USERID = 'userid';
      const BANNERID = 'bannerid';
      const PARENTBANNERID = 'parentbannerid';
      const CHANNEL = 'channel';
      const IP = 'ip';
      const DATA1 = 'data1';
      const DATA2 = 'data2';
  
      private static $instance;
      
      private $index;
  
      public static function getInstance($index) {
          if(@self::$instance[$index] === null) {
              self::$instance[$index] = new self;
              self::$instance[$index]->index = $index;
          }
          return self::$instance[$index];
      }
      
      public function name() {
          return parent::name() . $this->index;
      }
  
      protected function initName() {
          $this->setName('pap_impressions');
      }
  
      public static function getName($index) {
          return self::getInstance($index)->name();
      }
  
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, 'int');
          $this->createColumn(self::DATE, 'datetime');
          $this->createColumn(self::RTYPE, 'char', 1);
          $this->createColumn(self::USERID, 'char', 8);
          $this->createColumn(self::BANNERID, 'char', 8);
          $this->createColumn(self::PARENTBANNERID, 'char', 8);
          $this->createColumn(self::CHANNEL, 'char', 10);
          $this->createColumn(self::IP, 'char', 39);
          $this->createColumn(self::DATA1, 'char', 255);
          $this->createColumn(self::DATA2, 'char', 255);
      }
  
  }

} //end Pap_Db_Table_RawImpressions

if (!class_exists('Gpf_Tasks_LongTask', false)) {
  abstract class Gpf_Tasks_LongTask extends Gpf_Object {
      const DEFAULT_MAX_WORKERS_COUNT = 1;
  
      const NO_INTERRUPT = -1;
      const MEMORY_SAFE_OFFSET = 2097152;
  
      /**
       * @var Gpf_Tasks_Task
       */
      protected $task;
  
      private $resumed = false;
      private $params;
      private $workingAreaFrom = 0;
      private $workingAreaTo = 0;
      private $startTime;
      private $skipping = true;
      private $progress;
      protected $doneProgress;
      private $progressMessage;
      private $doneMessage;
      protected $maxRunTime;
      protected $memoryLimit = null;
  
      protected function createWorker($workingRangeFrom, $workingRangeTo) {
      }
  
      protected function getMaxWorkersCount() {
          return self::DEFAULT_MAX_WORKERS_COUNT;
      }
  
      /**
       * @var Gpf_Settings_AccountSettings
       */
      protected $accountSettings;
  
      protected function getMemoryLimit() {
          if ($this->memoryLimit === null) {
              $this->memoryLimit  = Gpf_Install_Requirements::getMemoryLimit();
          }
          return $this->memoryLimit;
      }
  
      protected function checkIfMemoryIsFull ($memory) {
          if (!defined('TASK_MEMORY_SAFE_OFFSET')) {
              $offset = self::MEMORY_SAFE_OFFSET;
          } else {
              $offset = TASK_MEMORY_SAFE_OFFSET;
          }
          if ($memory + $offset < $this->getMemoryLimit()) {
              return false;
          }
          return true;
      }
  
      protected function setWorkingArea($from, $to) {
          $this->workingAreaFrom = $from;
          $this->workingAreaTo = $to;
      }
  
      protected function setParams($params) {
          $this->params = $params;
      }
  
      protected function getParams() {
          return $this->params;
      }
  
      protected function getProgress() {
          return $this->doneProgress;
      }
  
      protected function setProgress($progress) {
          $this->doneProgress = $progress;
      }
  
      protected function getStartTime() {
          return $this->startTime;
      }
  
      public function resume() {
          if ($this->task != null && !$this->task->isFinished()) {
              $this->loadFromTask();
              return true;
          }
          $this->task = $this->createTask();
          try {
              $this->loadTask();
              $this->loadFromTask();
              return true;
          } catch (Gpf_Exception $e) {
              return false;
          }
      }
  
      protected function loadTask() {
          $this->task->loadTask(get_class($this), $this->params);
      }
  
      protected function loadFromTask() {
          $this->params = $this->task->getParams();
          $this->doneProgress = $this->task->getProgress();
      }
  
      protected function isDone($code, $message = '') {
          return !$this->isPending($code, $message);
      }
  
      protected function isPending($code, $message = '') {
          if($this->maxRunTime < 0) {
              return true;
          }
  
          if($this->isProcessed($code)) {
              return false;
          }
          $this->changeProgress($code, $message);
          $this->checkInterruption();
          return true;
      }
  
      protected function changeProgress($code, $message) {
          $this->progressMessage = $message;
          $this->progress = $code;
      }
  
      private function isProcessed($code) {
          if(!$this->resumed) {
              return false;
          }
          if(!$this->skipping) {
              return false;
          }
          if($code == $this->task->getProgress()) {
              $this->skipping = false;
          }
          return true;
  
      }
  
      protected function checkInterruption() {
          if($this->isTimeToInterrupt()) {
              $this->interrupt(0);
          }
      }
  
      protected function isTimeToInterrupt() {
          return (time() - $this->startTime > $this->maxRunTime);
      }
  
      protected function setDone() {
          $this->doneProgress = $this->progress;
          $this->doneMessage = $this->progressMessage;
      }
  
      protected function setDoneAndInterrupt() {
          $this->doneProgress = $this->progress;
          $this->doneMessage = $this->progressMessage;
          $this->interrupt(0);
      }
  
      /**
       * Interupt task
       * @param $sleepSeconds Define minimum how many seconds should task wait until can be again executed
       */
      protected function interrupt($sleepSeconds = 0) {
          if($this->maxRunTime < 0) {
              return;
          }
          $this->updateTask($sleepSeconds);
          throw new Gpf_Tasks_LongTaskInterrupt($this->getProgressMessage());
      }
  
      protected function getProgressMessage() {
          if ($this->progress == $this->doneProgress) {
              return $this->doneMessage . '...' . $this->_('DONE');
          }
          return $this->progressMessage . '...' . $this->_('IN PROGRESS');
      }
  
      protected function init() {
      }
  
      abstract protected function execute();
  
      abstract public function getName();
  
      private function runWithoutInterrupt() {
          $this->init();
          $this->lock();
          $this->execute();
          $this->unlock();
      }
  
      public function setTask($task) {
          $this->task = $task;
          if ($this->task != null) {
              $this->loadFromTask();
          }
      }
  
      public function forceFinishTask() {
          $this->task->finishTask();
      }
  
      protected function imMasterWorker() {
          if ($this->task->getWorkingAreaFrom() == 0) {
              return true;
          }
          return false;
      }
  
      protected function canBeSplit() {
          if ($this->getActualWorkersCount() < $this->getAvaliableWorkersCount()) {
              return true;
          }
          return false;
      }
  
      protected function getClassName() {
          return '';
      }
  
      /**
       * @return Gpf_Db_Task
       */
      private function getMaxFreeWorker() {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->select->addAll(Gpf_Db_Table_Tasks::getInstance());
          $select->from->add(Gpf_Db_Table_Tasks::getName());
          $select->where->add(Gpf_Db_Table_Tasks::CLASSNAME, '=', $this->getClassName());
          $select->where->add(Gpf_Db_Table_Tasks::DATEFINISHED, '=', null);
          $select->orderBy->add(Gpf_Db_Table_Tasks::WORKING_AREA_TO.'-'.Gpf_Db_Table_Tasks::WORKING_AREA_FROM, false);
          $select->limit->set(0, 1);
  
          $workerId = $select->getOneRow()->get(Gpf_Db_Table_Tasks::ID);
          $task = new Gpf_Db_Task();
          $task->setId($workerId);
          $task->load();
          return $task;
      }
      
      protected function slaveExist($workingAreaFrom, $workingAreaTo) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->where->add(Gpf_Db_Table_Tasks::CLASSNAME, '=', $this->getClassName());
          $select->where->add(Gpf_Db_Table_Tasks::DATEFINISHED, '=', null);
          $select->where->add(Gpf_Db_Table_Tasks::WORKING_AREA_FROM, '=', $workingAreaFrom);
          $select->where->add(Gpf_Db_Table_Tasks::WORKING_AREA_TO, '=', $workingAreaTo);
          $count = $this->getTableRowsCount($select, Gpf_Db_Table_Tasks::getName());
          $this->debug('There are ' . $count . ' num of slaves for ' . $workingAreaFrom . ' index');
          return $count > 0;
      }
  
      protected function getTableRowsCount(Gpf_SqlBuilder_SelectBuilder $select, $tableName) {
          $select->from->add($tableName);
          $select->select->add('count(*)', 'cnt');
          $record = $select->getOneRow();
          return $record->get('cnt');
      }
  
      protected function getActualWorkersCount() {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->where->add(Gpf_Db_Table_Tasks::CLASSNAME, '=', $this->getClassName());
          $select->where->add(Gpf_Db_Table_Tasks::DATEFINISHED, '=', null);
          return $this->getTableRowsCount($select, Gpf_Db_Table_Tasks::getName());
      }
  
      protected function getAvaliableWorkersCount() { 
          $select = new Gpf_SqlBuilder_SelectBuilder();
          return $this->getTableRowsCount($select, Gpf_Db_Table_JobsRuns::getName());
      }
  
      private function imBiggestWorker() {
          $biggestTask = $this->getMaxFreeWorker();
  
          if (($this->task->getWorkingAreaTo() - $this->task->getWorkingAreaFrom()) >= ($biggestTask->getWorkingAreaTo() - $biggestTask->getWorkingAreaFrom())) {
              return true;
          }
          return false;
      }
  
      protected function splitMe() {
          $this->debug("May I split?");
          if (($this->imBiggestWorker() || ($this->imMasterWorker()) ) && ($this->task->getWorkingAreaTo() - $this->task->getWorkingAreaFrom()) >= 1) {
              $this->splitTask($this->task);
          }
      }
  
      protected function resetMyWorkingArea() {
          $this->debug("Im resetting my working area");
          $this->task->setWorkingAreaFrom(0);
          $this->task->setWorkingAreaTo($this->getMaxWorkersCount()-1);
          $this->task->updateTask();
      }
  
      protected function imAlone() {
          if ($this->getActualWorkersCount()<=1) {
              return true;
          }
          return false;
      }
  
      private function doMasterWorkBeforeExecute() {
          $this->debug('Master work before execute...');
          if($this->syncPointReached() && $this->imAlone()) {
              $this->resetMyWorkingArea();
              $this->debug('Master work at syncpoint...');
              $this->doMasterWorkWhenSyncPointReached();
          }
      }
  
      private function doSlaveWorkBeforeExecute() {
          $this->debug('Slave work before execute...');
          if($this->syncPointReached()) {
              $this->debug('Slave work at syncpoint...');
              $this->doSlaveWorkWhenSyncPointReached();
          }
      }
  
      protected function doMasterWorkAfterExecute() {
      }
  
      protected function doSlaveWorkAfterExecute() {
      }
  
      protected function doMasterWorkWhenSyncPointReached() {
      }
  
      protected function doSlaveWorkWhenSyncPointReached() {
      }
  
      protected function syncPointReached() {
          return false;
      }
  
      private function doBeforeExecute() {
          $this->debug('Before execute');
          if ($this->imMasterWorker()) {
              $this->doMasterWorkBeforeExecute();
          } else {
              $this->doSlaveWorkBeforeExecute();
          }
          if ($this->getMaxWorkersCount() > 1 && $this->canBeSplit()) {
              if ($this->imAlone()) {
                  $this->debug('Im alone, planning work for slaves...');
                  $this->createSlaves();
              } else {
                  $this->debug('Im not alone, just can split my self...');
                  $this->splitMe();
              }
          }
      }
  
      protected function createSlaves() {
          $avaliableWorkersCount = $this->getAvaliableWorkersCount();
          if ($this->getActualWorkersCount() < $avaliableWorkersCount) {
              try {
                  while ($avaliableWorkersCount > $this->getActualWorkersCount()) {
                      $worker = $this->getMaxFreeWorker();
                      if (($worker->getWorkingAreaTo() - $worker->getWorkingAreaFrom()) <= 0) {
                          $this->debug('Splitting is not possible anymore... scheduling is over');
                          break;
                      }
                      $this->splitTask($worker);
                  }
              } catch (Gpf_DbEngine_NoRowException $e) {
                  $this->debug('Error during creating new slave: ' . $e->getMessage());
                  return;
              }
          }
      }
  
      protected function splitTask(Gpf_Db_Task $task) {
          $workingAreaTo = $task->getWorkingAreaTo();
          $splitNumber = intval(($task->getWorkingAreaTo() - $task->getWorkingAreaFrom())/2);
          $task->setWorkingAreaTo($task->getWorkingAreaFrom() + $splitNumber);
          $task->update();
          if ($task->get(Gpf_Db_Table_Tasks::ID) == $this->task->get(Gpf_Db_Table_Tasks::ID)) {
              $this->task = $task;
          } 
           
          $this->createWorker($task->getWorkingAreaFrom() + $splitNumber + 1, $workingAreaTo);
      }
  
      private function doAfterExecute() {
          if ($this->imMasterWorker() || $this->getActualWorkersCount() == 1) {
              if ($this->imAlone() && !$this->syncPointReached()) {
                  $this->debug('Master finished his work, but sync point was not reached. Rescheduling...');
                  $this->resetMyWorkingArea();
              }
              $this->doMasterWorkAfterExecute();
          } else if (!$this->imMasterWorker()) {
              $this->doSlaveWorkAfterExecute();
          }
          $this->setDone();
      }
  
      final public function run($maxRunTime = 24, Gpf_Tasks_Task $task = null) {
          $this->maxRunTime = $maxRunTime;
          $this->setTask($task);
          $this->initSettings();
          if($this->maxRunTime < 0) {
              $this->runWithoutInterrupt();
              return;
          }
          $this->startTime = time();
  
          $this->resumed = true;
          if(!$this->resume()) {
              $this->resumed = false;
              $this->init();
              $this->insertTask();
          }
  
          if(strlen($this->task->getProgress()) == 0) {
              $this->resumed = false;
          }
  
          try {
              $this->lock();
              $this->doBeforeExecute();
              $this->execute();
              $this->doAfterExecute();
              $this->unlock();
          } catch (Gpf_Tasks_LongTaskInterrupt $e) {
              $this->doAfterLongTaskInterrupt();
              $this->unlock();
              throw $e;
          } catch (Exception $e) {
              //in case of error, don't execute task next 30 seconds again
              Gpf_Log::error(sprintf('Task %s threw exception %s', get_class($this), $e));
              $this->updateTask(30);
              $this->unlock();
              throw $e;
          }
  
          $this->task->finishTask();
      }
      
      protected function doAfterLongTaskInterrupt() {
          $this->updateTask(15);
      }
  
      protected function initSettings() {
          if (!is_null($this->task) && !is_null($this->task->getAccountId()) && $this->task->getAccountId() !== '') {
              $this->accountSettings = Gpf_Settings::getAccountSettings($this->task->getAccountId());
              return;
          }
          $this->accountSettings = Gpf_Settings::getAccountSettings(Gpf_Application::getInstance()->getAccountId());
      }
  
      private function lock() {
          if ($this->task != null) {
              $this->task->lockTask(true);
          }
      }
  
      private function unlock() {
          if ($this->task != null) {
              $this->task->lockTask(false);
          }
      }
  
      public function insertTask() {
          $this->task = $this->createTask();
          $this->task->setClassName(get_class($this));
          $this->task->setParams($this->params);
          $this->task->setWorkingAreaFrom($this->workingAreaFrom);
          $this->task->setWorkingAreaTo($this->workingAreaTo);
          $this->task->setName($this->getName());
          $this->task->setProgressMessage($this->getProgressMessage());
          if(false !== ($pid = @getmypid())) {
              $this->task->setPid($pid);
          }
          $this->task->insertTask();
      }
  
      /**
       * @return Gpf_Tasks_Task
       */
      protected function createTask() {
          $task = new Gpf_Db_Task();
          $task->setType($this->getTaskType());
          return $task;
      }
  
      protected function getTaskType() {
          return Gpf_Db_Task::TYPE_CRON;
      }
  
      protected function updateTask($sleepSeconds = 0) {
          if($this->doneProgress !== null) {
              $this->task->setParams($this->params);
              $this->task->setSleepTime($sleepSeconds);
              $this->task->setProgress($this->doneProgress);
              $this->task->setProgressMessage($this->getProgressMessage());
              $this->updateTaskObject();
          }
      }
  
      protected function updateTaskObject() {
          $this->task->updateTask();
      }
  
      /**
       * Returns true if user can delete from user interface task.
       * System tasks, which should not be deleted should return always false
       *
       * @return boolean
       */
      public function canUserDeleteTask() {
          return false;
      }
  
      protected function debug($message) {
          Gpf_Log::debug($message);
      }
  }
  

} //end Gpf_Tasks_LongTask

if (!class_exists('Pap_Tracking_Impression_ImpressionProcessor', false)) {
  class Pap_Tracking_Impression_ImpressionProcessor extends Gpf_Tasks_LongTask {
  	
  	const ROWS_FOR_PROCESSING_LIMIT = 100000;
  	
  	/**
  	 * @var Gpf_Log_Logger
  	 */
  	private $logger;
  	/**
  	 * @var Pap_Tracking_Impression_Save
  	 */
  	private $impressionSaver;
  	/**
  	 * @var Pap_Common_Banner_Factory
  	 */
  	private $bannerFactory;
  
  	private $bannerCache = array();
  	private $affiliateCache = array();
  	private $channelCache = array();
  	private $campaignCache = array();
  	private $processedTableIndex;
  
  	public function __construct() {
  		$this->logger = Pap_Logger::create(Pap_Common_Constants::TYPE_CPM);
  		$this->impressionSaver = new Pap_Tracking_Impression_Save();
  		$this->bannerFactory = new Pap_Common_Banner_Factory();
  	}
  
  	public function getName() {
  		return $this->_('Impression processor');
  	}
  	
  	public function runOnline(Pap_Db_RawImpression $impression) {
  	    $this->importImpression($impression);
  	}
  
  	protected function execute() {
  		$this->debug('Starting impression preocessor');
  
  		$this->switchTables();
  
  		$this->processedTableIndex = Gpf_Settings::get(Pap_Settings::IMPRESSIONS_TABLE_PROCESS);
  
  		$this->processAllImpressions();
  
          $this->debug('All impressions from '.Pap_Db_Table_RawImpressions::getName($this->processedTableIndex).'. processed');
  
  		$this->interrupt(30);
  	}
  	
  	private function processAllImpressions() {
  		$subSelect = $this->createSubSelect();
  		$impressionsSelect = $this->getAllImpressions($subSelect);				
  		$this->processImpressions($impressionsSelect, $subSelect);
  	}
  	
  	protected function processImpressions(Gpf_SqlBuilder_SelectBuilder $impressionsSelect, Gpf_SqlBuilder_SelectBuilder $subSelect) {			
  		$iterator = $this->getImpressions($impressionsSelect);		
  
  		$count = 0;	
  		foreach ($iterator as $impressionRecord) {
              $impression = new Pap_Db_RawImpression($this->processedTableIndex);
              $impression->fillFromRecord($impressionRecord);
              $this->importImpression($impression, $impressionRecord->get('count'));
              $count++;
          }        
       
          if ($count > 0) {        	
          	$this->deleteImpressions($subSelect);      	
          	$this->checkInterruption();
          	$this->processImpressions($impressionsSelect, $subSelect);
          }
  	}
  	
      protected function checkInterruption() {
          if($this->isTimeToInterrupt()) {
              $this->interrupt(30);
          }
      }
  
  	/**
  	 * @param $selectBuilder
  	 * @return Gpf_SqlBuilder_SelectIterator
  	 */
  	protected function getImpressions(Gpf_SqlBuilder_SelectBuilder $selectBuilder) {					
  		return $selectBuilder->getAllRowsIterator();
  	}
  
  	protected function deleteImpressions(Gpf_SqlBuilder_SelectBuilder $subSelect) {
  		$delete = new Gpf_SqlBuilder_DeleteBuilder();
  		$delete->delete->add('ri');
  		$delete->from->add(Pap_Db_Table_RawImpressions::getName($this->processedTableIndex), 'ri');
  		$delete->from->addInnerJoin('(' . $subSelect->toString() . ')', 'sri', 
  		'ri.'.Pap_Db_Table_RawImpressions::ID.'=sri.'.Pap_Db_Table_RawImpressions::ID);
  		$delete->delete();
  	}	
  
  	/**
  	 * switching tables for writing and processing impressions
  	 * table states: I - impressions are written to this table
  	 *               W - table is waiting to be processed
  	 *               P - table should be processed
  	 *
  	 * this method switches: I -> W, W -> P, P -> I
  	 */
  	private function switchTables() {
  		Gpf_Settings::set(Pap_Settings::IMPRESSIONS_TABLE_INPUT,
  		  (Gpf_Settings::get(Pap_Settings::IMPRESSIONS_TABLE_INPUT)+2) % 3);
          Gpf_Settings::set(Pap_Settings::IMPRESSIONS_TABLE_PROCESS,
            (Gpf_Settings::get(Pap_Settings::IMPRESSIONS_TABLE_PROCESS)+2) % 3);
  	}
  
  	/**
  	 * @return Gpf_SqlBuilder_SelectBuilder
  	 */
  	private function getAllImpressions(Gpf_SqlBuilder_SelectBuilder $subSelect) {
  		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();		
  		$selectBuilder->select->add('ri.date', 'date');
  		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::RTYPE, Pap_Db_Table_RawImpressions::RTYPE);
  		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::USERID, Pap_Db_Table_RawImpressions::USERID);
  		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::BANNERID, Pap_Db_Table_RawImpressions::BANNERID);
  		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::PARENTBANNERID, Pap_Db_Table_RawImpressions::PARENTBANNERID);
  		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::CHANNEL, Pap_Db_Table_RawImpressions::CHANNEL);
  		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::IP, Pap_Db_Table_RawImpressions::IP);
  		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::DATA1, Pap_Db_Table_RawImpressions::DATA1);
  		$selectBuilder->select->add('ri.'.Pap_Db_Table_RawImpressions::DATA2, Pap_Db_Table_RawImpressions::DATA2);		
  		$selectBuilder->select->add('count(ri.'.Pap_Db_Table_RawImpressions::ID.')', 'count');
  		
  		$selectBuilder->from->addSubselect($subSelect, 'ri');
  		
  		$selectBuilder->groupBy->add('DATE_FORMAT('.Pap_Db_Table_RawImpressions::DATE.', "%Y-%m-%d %H:00:00")', 'date');
  		$selectBuilder->groupBy->add(Pap_Db_Table_RawImpressions::RTYPE);
  		$selectBuilder->groupBy->add(Pap_Db_Table_RawImpressions::USERID);
  		$selectBuilder->groupBy->add(Pap_Db_Table_RawImpressions::BANNERID);
  		$selectBuilder->groupBy->add(Pap_Db_Table_RawImpressions::CHANNEL);
  		$selectBuilder->groupBy->add(Pap_Db_Table_RawImpressions::DATA1);
  		$selectBuilder->groupBy->add(Pap_Db_Table_RawImpressions::DATA2);
  
  		Gpf_Plugins_Engine::extensionPoint('Tracker.ImpressionProcessor.getAllImpressions', $selectBuilder);
  		
  		return $selectBuilder;
  	}
  
  	/**	 
  	 * @return Gpf_SqlBuilder_SelectBuilder
  	 */
  	private function createSubSelect() {
  		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
  		$selectBuilder->from->add(Pap_Db_Table_RawImpressions::getName($this->processedTableIndex));		
  		$dateColumn = 'DATE_FORMAT('.Pap_Db_Table_RawImpressions::DATE.', "%Y-%m-%d %H:00:00")';
  		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::ID);
  		$selectBuilder->select->add($dateColumn, 'date');
  		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::RTYPE);
  		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::USERID);
  		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::BANNERID);
  		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::PARENTBANNERID);
  		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::CHANNEL);
  		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::IP);
  		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::DATA1);
  		$selectBuilder->select->add(Pap_Db_Table_RawImpressions::DATA2);
  		$selectBuilder->limit->set(0, self::ROWS_FOR_PROCESSING_LIMIT);
  		return $selectBuilder;
  	}
  	
  	/**
  	 * @return Pap_Affiliates_User
  	 * @throws Gpf_Exception
  	 */
  	protected function getUser($affiliateId) {
  		if (!isset($this->affiliateCache[$affiliateId])) {
  			$this->affiliateCache[$affiliateId] = Pap_Affiliates_User::loadFromId($affiliateId);
  		}
  
  		return $this->affiliateCache[$affiliateId];
  	}
  
  	/**
  	 * @throws Gpf_Exception
  	 * @return Pap_Common_Banner
  	 * 
  	 * do not change public to protected because of compatibility reasons with PAP3(sb.php) 
  	 */
  	public function getBanner($bannerId) {
  		if (!isset($this->bannerCache[$bannerId])) {
  			$this->bannerCache[$bannerId] = $this->bannerFactory->getBanner($bannerId);
  		}
  		return $this->bannerCache[$bannerId];
  	}
  	
      /**
       * @throws Gpf_Exception
       * @return Pap_Common_Campaign
       */
      protected function getCampaign($campaignId) {
          if (!isset($this->campaignCache[$campaignId])) {
              $campaign = new Pap_Common_Campaign();
              $campaign->setId($campaignId);
              $campaign->load();
              $this->campaignCache[$campaignId] = $campaign;
          }
          return $this->campaignCache[$campaignId];
      }
  
  	/**
  	 * @return Pap_Db_Channel
  	 * @throws Gpf_Exception
  	 */
  	protected function getChannel($channelId, $impressionContext) {
  		if (!isset($this->channelCache[$channelId])) {
  			$recognizeChannel = new Pap_Tracking_Impression_RecognizeChannel();
  			$this->channelCache[$channelId] = $recognizeChannel->getChannelById($impressionContext, $channelId);
  		}
  		return $this->channelCache[$channelId];
  	}
  
  	protected function importImpression(Pap_Db_RawImpression $impression, $count = 1) {
  		$impressionContext = new Pap_Contexts_Impression($impression);
  	    
  	    try {
  			$impressionContext->setUserObject($this->getUser($impression->getUserId()));
  		} catch (Gpf_Exception $e) {
  			$this->debug('Invalid user '.$impression->getUserId().'. Skipping');
  			return;
  		}
  
  		try {
  			$impressionContext->setBannerObject($this->getBanner($impression->getBannerId()));
  		} catch (Gpf_Exception $e) {
  			$this->debug('Invalid banner '.$impression->getBannerId().'. Skipping');
  			return;
  		}
  		
  		try {
  		    $campaign = $this->getCampaign($impressionContext->getBannerObject()->getCampaignId());
  		    $impressionContext->setAccountId($campaign->getAccountID(), Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_FROM_CAMPAIGN);
  		} catch (Gpf_Exception $e) {
  		    $this->debug('Invalid campaign '.$impressionContext->getBannerObject()->getCampaignId().'. Skipping');
  		    return;
  		}
  
  		try {
  			$impressionContext->setChannelObject($this->getChannel($impression->getChannel(), $impressionContext));
  		} catch (Gpf_Exception $e) {
  		}
  		
  		$impressionContext->setCount($count);
  		$this->saveImpression($impressionContext);		
  	}
  	
  	protected function saveImpression(Pap_Contexts_Impression $impressionContext) {
  		$this->impressionSaver->save($impressionContext);
  	}
  
  	protected function loadTask() {
  		$this->task->setClassName(get_class($this));
  		$this->task->setNull(Gpf_Db_Table_Tasks::DATEFINISHED);
  		$this->task->loadFromData();
  	}
  }

} //end Pap_Tracking_Impression_ImpressionProcessor

if (!class_exists('Pap_Common_Constants', false)) {
  class Pap_Common_Constants  {
  	/**
  	 * status constants
  	 * for transaction, affiliate
  	 *
  	 */
      const STATUS_APPROVED = 'A';
      const STATUS_PENDING = 'P';
      const STATUS_DECLINED = 'D';
  
  	/**
  	 * enable status constants
  	 *
  	 */
      const ESTATUS_ENABLED = 'E';
      const ESTATUS_DISABLED = 'D';
      
     	/**
  	 * payout status constants
  	 *
  	 */
      const PSTATUS_PAID = 'P';
      const PSTATUS_UNPAID = 'U';
          
      /**
       * returns text name for this status
       */
      public static function getStatusAsText($status) {
      	switch($status) {
      		case self::STATUS_APPROVED: return Gpf_Lang::_('approved');
      		case self::STATUS_PENDING: return Gpf_Lang::_('pending');
      		case self::STATUS_DECLINED: return Gpf_Lang::_('declined');
      	}
      	return Gpf_Lang::_('unknown');
      }
  
      /**
       * returns text name for this payout status
       */
      public static function getPayoutStatusAsText($payoutStatus) {
      	switch($payoutStatus) {
      		case self::PSTATUS_PAID: return Gpf_Lang::_('paid');
      		case self::PSTATUS_UNPAID: return Gpf_Lang::_('unpaid');
      	}
      	return Gpf_Lang::_('unknown');
      }
      
      /**
       * type constants for transactions types
       */
      const TYPE_CPM = 'I';
      const TYPE_CLICK = 'C';
      const TYPE_SALE = 'S';
      const TYPE_LEAD = 'L';
      const TYPE_ACTION = 'A';
      const TYPE_SIGNUP = 'B';
      const TYPE_RECURRING = 'U';
      const TYPE_REFERRAL = 'F';
      const TYPE_REFUND = 'R';
      const TYPE_CHARGEBACK = 'H';
      const TYPE_EXTRABONUS = 'E';
      
      /**
       * returns text name for this type
       */
      public static function getTypeAsText($type) {
      	switch($type) {
      		case Pap_Common_Constants::TYPE_ACTION: return Gpf_Lang::_("action");
      		case Pap_Common_Constants::TYPE_CLICK: return Gpf_Lang::_("click");
      		case Pap_Common_Constants::TYPE_CPM: return Gpf_Lang::_("cpm");
      		case Pap_Common_Constants::TYPE_SALE: return Gpf_Lang::_("sale");
      		case Pap_Common_Constants::TYPE_LEAD: return Gpf_Lang::_("lead");
      		case Pap_Common_Constants::TYPE_SIGNUP: return Gpf_Lang::_("signup");
      		case Pap_Common_Constants::TYPE_RECURRING: return Gpf_Lang::_("recurring");
      		case Pap_Common_Constants::TYPE_REFERRAL: return Gpf_Lang::_("referral");
      		case Pap_Common_Constants::TYPE_REFUND: return Gpf_Lang::_("refund");
      		case Pap_Common_Constants::TYPE_CHARGEBACK: return Gpf_Lang::_("chargeback");
      		case Pap_Common_Constants::TYPE_EXTRABONUS: return Gpf_Lang::_("extra bonus");
      	}
      	
      	return Gpf_Lang::_("unknown");
      }
      
      /**
       * returns text name for campaign type
       */
      public static function getCampaignTypeAsText($type) {
      	switch($type) {
      		case Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION: return Gpf_Lang::_("private");
      		case Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC_MANUAL: return Gpf_Lang::_("public with approval");
      		case Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC: return Gpf_Lang::_("public");
      	}
      	
      	return $obj->_("unknown");
      }
      
      
      /**
       * fieldgroup constants
       *
       */
      const FIELDGROUP_TYPE_PAYOUTOPTION = 'P';
      const FIELDGROUP_TYPE_SIGNUPACTION = 'A';
      const FIELDGROUP_TYPE_AFTERSALEACTION = 'S';
      
      
      const SMARTY_SYNTAX_URL = 'troubleshooting/invalid-smarty-syntax.html';
  }

} //end Pap_Common_Constants

if (!class_exists('Pap_Tracking_Request', false)) {
  class Pap_Tracking_Request extends Gpf_Object {
      const PARAM_CAMPAIGN_ID_SETTING_NAME = 'campaignId';
  
      /* other action parameters */
      const PARAM_ACTION_DEBUG = 'PDebug';
      const PARAM_CALL_FROM_JAVASCRIPT = 'cjs';
  
      /* Constant param names */
      const PARAM_LINK_STYLE = 'ls';
      const PARAM_REFERRERURL_NAME = 'refe';
  
      /* Param setting names */
      const PARAM_DESTINATION_URL_SETTING_NAME = 'param_name_extra_data3';
      const PARAM_CHANNEL_DEFAULT = 'chan';
      const PARAM_CURRENCY = 'cur';
  
      /* Forced parameter names */
      const PARAM_FORCED_AFFILIATE_ID = 'AffiliateID';
      const PARAM_FORCED_BANNER_ID = 'BannerID';
      const PARAM_FORCED_CAMPAIGN_ID = 'CampaignID';
      const PARAM_FORCED_CHANNEL_ID = 'Channel';
      const PARAM_FORCED_IP = 'Ip';
  
      private $countryCode;
  
      protected $request;
  
      /**
       * @var Gpf_Log_Logger
       */
      protected $logger;
  
      function __construct() {
          $this->request = $_REQUEST;
      }
  
      public function parseUrl($url) {
          $this->request = array();
          if ($url === null || $url == '') {
              return;
          }
          $parsedUrl = @parse_url('?'.ltrim($url, '?'));
          if ($parsedUrl === false || !array_key_exists('query', $parsedUrl)) {
              return;
          }
          $args = explode('&', @$parsedUrl['query']);
          foreach ($args as $arg) {
              $parts = explode('=', $arg, 2);
              if (count($parts) == 2) {
                  $this->request[$parts[0]] = $parts[1];
              }
          }
      }
  
      public function getAffiliateId() {
          return $this->getRequestParameter(self::getAffiliateClickParamName());
      }
  
      public function getForcedAffiliateId() {
          return $this->getRequestParameter(self::getForcedAffiliateParamName());
      }
  
      public function getBannerId() {
          return $this->getRequestParameter(self::getBannerClickParamName());
      }
  
      public function getForcedBannerId() {
          return $this->getRequestParameter(self::getForcedBannerParamName());
      }
  
      /**
       * @return Pap_Common_User
       */
      public function getUser() {
          try {
              return Pap_Affiliates_User::loadFromId($this->getRequestParameter($this->getAffiliateClickParamName()));
          } catch (Gpf_Exception $e) {
              return null;
          }
      }
  
      /**
       * @param string $id
       * @return string
       */
      public function getRawExtraData($i) {
          $extraDataParamName = $this->getExtraDataParamName($i);
          if (!isset($this->request[$extraDataParamName])) {
              return '';
          }
          $str = preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;",urldecode($this->request[$extraDataParamName]));
          return html_entity_decode($str,null,'UTF-8');
      }
  
      public function setRawExtraData($i, $value) {
          $extraDataParamName = $this->getExtraDataParamName($i);
          $this->request[$extraDataParamName] = $value;
      }
  
      /**
       * returns custom click link parameter data1
       * It first checks for forced parameter Data1 given as parameter to JS tracking code
       *
       * @return string
       */
      public function getClickData1() {
          $value = $this->getRequestParameter('pd1');
          if($value != '') {
              return $value;
          }
  
          $paramName = $this->getClickData1ParamName();
          if (!isset($this->request[$paramName])) {
              return '';
          }
          return $this->request[$paramName];
      }
  
      /**
       * returns custom click link parameter data2
       * It first checks for forcet parameter Data2 given as parameter to JS tracking code
       *
       * @return string
       */
      public function getClickData2() {
          $value = $this->getRequestParameter('pd2');
          if($value != '') {
              return $value;
          }
  
          $paramName = $this->getClickData2ParamName();
          if (!isset($this->request[$paramName])) {
              return '';
          }
          return $this->request[$paramName];
      }
  
      public function getClickData1ParamName() {
          return Gpf_Settings::get(Pap_Settings::PARAM_NAME_EXTRA_DATA.'1');
      }
  
      public function getClickData2ParamName() {
          return Gpf_Settings::get(Pap_Settings::PARAM_NAME_EXTRA_DATA.'2');
      }
  
      public function getRefererUrl() {
          if (isset($this->request[self::PARAM_REFERRERURL_NAME]) && $this->request[self::PARAM_REFERRERURL_NAME] != '') {
              return self::decodeRefererUrl($this->request[self::PARAM_REFERRERURL_NAME]);
          }
          if (isset($_SERVER['HTTP_REFERER'])) {
              return self::decodeRefererUrl($_SERVER['HTTP_REFERER']);
          }
          return '';
      }
  
      public function getIP() {
          if ($this->getForcedIp() !== '') {
              return $this->getForcedIp();
          }
          return Gpf_Http::getRemoteIp();
      }
  
      public function getCountryCode() {
          if ($this->countryCode === null) {
              $context = new Gpf_Data_Record(
              array(Pap_Db_Table_RawImpressions::IP, Pap_Db_Table_Impressions::COUNTRYCODE), array($this->getIP(), ''));
              Gpf_Plugins_Engine::extensionPoint('Tracker.request.getCountryCode', $context);
              $this->countryCode = $context->get(Pap_Db_Table_Impressions::COUNTRYCODE);
          }
          return $this->countryCode;
      }
  
      public function getBrowser() {
          if (!isset($_SERVER['HTTP_USER_AGENT'])) {
              return '';
          }
          return substr(md5($_SERVER['HTTP_USER_AGENT']), 0, 6);
      }
  
      public function getLinkStyle() {
          if (!isset($this->request[self::PARAM_LINK_STYLE]) || $this->request[self::PARAM_LINK_STYLE] != '1') {
              return Pap_Tracking_ClickTracker::LINKMETHOD_REDIRECT;
          }
          return Pap_Tracking_ClickTracker::LINKMETHOD_URLPARAMETERS;
      }
  
      /**
       * set logger
       *
       * @param Gpf_Log_Logger $logger
       */
      public function setLogger($logger) {
          $this->logger = $logger;
      }
  
      protected function debug($msg) {
          if($this->logger != null) {
              $this->logger->debug($msg);
          }
      }
  
      public function getRequestParameter($paramName) {
          if (!isset($this->request[$paramName])) {
              return '';
          }
          return $this->request[$paramName];
      }
  
      public function setRequestParameter($paramName, $value) {
          $this->request[$paramName] = $value;
      }
  
      static public function getRotatorBannerParamName() {
          return Gpf_Settings::get(Pap_Settings::PARAM_NAME_ROTATOR_ID);
      }
  
      static public function getSpecialDestinationUrlParamName() {
          return Gpf_Settings::get(Pap_Settings::PARAM_NAME_DESTINATION_URL);
      }
  
      public function getRotatorBannerId() {
          return $this->getRequestParameter(self::getRotatorBannerParamName());
      }
  
      public function getExtraDataParamName($i) {
          return Gpf_Settings::get(Pap_Settings::PARAM_NAME_EXTRA_DATA).$i;
      }
  
      public function getDebug() {
          if(isset($_GET[self::PARAM_ACTION_DEBUG])) {
              return strtoupper($_GET[self::PARAM_ACTION_DEBUG]);
          }
          return '';
      }
  
      public function toString() {
          $params = array();
          foreach($this->request as $key => $value) {
              $params .= ($params != '' ? ", " : '')."$key=$value";
          }
          return $params;
      }
  
      public function getRecognizedClickParameters() {
          $params = 'Debug='.$this->getDebug();
          $params .= ',Data1='.$this->getClickData1();
          $params .= ',Data2='.$this->getClickData2();
  
          return $params;
      }
  
      static public function getAffiliateClickParamName() {
          return Gpf_Settings::get(Pap_Settings::PARAM_NAME_USER_ID);
      }
  
      static public function getBannerClickParamName() {
          $parameterName = trim(Gpf_Settings::get(Pap_Settings::PARAM_NAME_BANNER_ID));
          if($parameterName == '') {
              $mesage = Gpf_Lang::_('Banner ID parameter name is empty. Review URL parameter name settings');
              Gpf_Log::critical($mesage);
              throw new Gpf_Exception($mesage);
          }
          return $parameterName;
      }
  
      static public function getChannelParamName() {
          return Pap_Tracking_Request::PARAM_CHANNEL_DEFAULT;
      }
  
      public function getChannelId() {
          return $this->getRequestParameter(self::getChannelParamName());
      }
  
      static public function getForcedAffiliateParamName() {
          return Pap_Tracking_Request::PARAM_FORCED_AFFILIATE_ID;
      }
  
      static public function getForcedBannerParamName() {
          return Pap_Tracking_Request::PARAM_FORCED_BANNER_ID;
      }
  
      public function getForcedCampaignId() {
          return $this->getRequestParameter(self::getForcedCampaignParamName());
      }
  
      static public function getForcedCampaignParamName() {
          return Pap_Tracking_Request::PARAM_FORCED_CAMPAIGN_ID;
      }
  
      public function getForcedChannelId() {
          return $this->getRequestParameter(Pap_Tracking_Request::PARAM_FORCED_CHANNEL_ID);
      }
  
      public function getCampaignId() {
          return $this->getRequestParameter(self::getCampaignParamName());
      }
  
      static public function getCampaignParamName() {
          $parameterName = trim(Gpf_Settings::get(Pap_Settings::PARAM_NAME_CAMPAIGN_ID));
          if($parameterName == '') {
              $mesage = Gpf_Lang::_('Campaign ID parameter name is empty. Review URL parameter name settings');
              Gpf_Log::critical($mesage);
              throw new Gpf_Exception($mesage);
          }
          return $parameterName;
      }
  
      public function getCurrency() {
          return $this->getRequestParameter(self::PARAM_CURRENCY);
      }
  
      /**
       * @deprecated used in CallBackTracker plugins only. should be moved to callback tracker
       */
      public function getPostParam($name) {
          if (!isset($_POST[$name])) {
              return '';
          }
          return $_POST[$name];
      }
  
      /**
       * This function does escape http:// and https:// in url as mod_rewrite disables requests with ://
       *
       * @param $url
       * @return encoded url
       */
      public static function encodeRefererUrl($url) {
          $url = str_replace('http://', 'H_', $url);
          $url = str_replace('https://', 'S_', $url);
          return $url;
      }
  
      /**
       * This function does decoded encoded url
       *
       * @param encoded $url
       * @return $url
       */
      public static function decodeRefererUrl($url) {
          if (substr($url, 0, 2) == 'H_') {
              return 'http://' . substr($url, 2);
          }
          if (substr($url, 0, 2) == 'S_') {
              return 'https://' . substr($url, 2);
          }
          return $url;
      }
  
      private function getForcedIp() {
          return $this->getRequestParameter(self::PARAM_FORCED_IP);
      }
  }

} //end Pap_Tracking_Request

if (!class_exists('Gpf_Common_String', false)) {
  class Gpf_Common_String extends Gpf_Object {
  
      const NUMERIC_CHARS = '1234567890';
      const SPECIAL_CHARS = '!@#$%^&*|';
      const LOWERCASE_CHARS = 'abcdefghijklmnopqrstuvwzxy';
      const UPPERCASE_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  
      public static function generateId($length = 8) {
          return substr(md5(uniqid(rand(), true)), 0, $length);
      }
  
      public static function generatePassword($length) {
  
          //check min length
          if ($length < Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MIN_LENGTH)) {
              $length = Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MIN_LENGTH);
          }
          //check max length
          if ($length > Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MAX_LENGTH)) {
              $length = Gpf_Settings::get(Gpf_Settings_Gpf::PASSWORD_MAX_LENGTH);
          }
  
          $chars = str_shuffle(self::NUMERIC_CHARS . self::LOWERCASE_CHARS .
                   self::UPPERCASE_CHARS);
          $result = '';
          for ($i = 0; $i < $length; $i++) {
              $result .= $chars[rand(0,strlen($chars)-1)];
          }
  
          $result = self::normalizePassword(Gpf_Settings_Gpf::PASSWORD_SPECIAL, self::SPECIAL_CHARS, $result);
          $result = self::normalizePassword(Gpf_Settings_Gpf::PASSWORD_DIGITS, self::NUMERIC_CHARS, $result);
          return self::normalizePassword(Gpf_Settings_Gpf::PASSWORD_LETTERS, self::UPPERCASE_CHARS . self::LOWERCASE_CHARS, $result);
      }
  
      /**
       * Add minimum one character into password from string includeCharacters
       * if it doesn't contain already such character
       *
       * @param $settingName
       * @param $includeCharacters
       * @param $password
       * @return unknown_type
       */
      private static function normalizePassword($settingName, $includeCharacters, $password) {
          if (Gpf_Settings::get($settingName) == Gpf::YES &&
              !preg_match('/[' . preg_quote($includeCharacters) . ']/', $password)) {
              $password[rand(0,strlen($password)-1)] = $includeCharacters[rand(0, strlen($includeCharacters)-1)];
          }
          return $password;
      }
  
      /**
       * Convert text on input to be suitable in any URL
       *
       * @param string $text
       * @return string
       */
      public static function textToUrl($text) {
          return preg_replace('/[^a-zA-Z0-9\-]/', '',str_replace(' ', '-', $text));
      }
  
      /**
       * Convert simple text to html
       *
       * @param string $text Simple text
       * @return string input string in html format
       */
      public function text2Html($text) {
          return str_replace("\n", '<br>', htmlspecialchars($text));
      }
  }
  

} //end Gpf_Common_String

if (!class_exists('Pap_Tracking_Impression_Save', false)) {
  class Pap_Tracking_Impression_Save extends Gpf_Object {
  
      public function save(Pap_Contexts_Impression $context) {
          $context->debug('  Saving impression started');
  
          $impression = $this->createRowImpression();
          $impression->setAccountId($context->getAccountId());
          $impression->setUserId($context->getUserObject()->getId());
          $impression->setBannerId($context->getBannerId());
          $impression->setParentBannerId($context->getParentBannerId());
          $impression->setCampaignId($context->getCampaignId());
          $impression->setChannel($context->getChannelId());
          $impression->setCountryCode($context->getCountryCode());
          $impression->setData1($context->getClickData1());
          $impression->setData2($context->getClickData2());
          $time = new Gpf_DateTime($context->getDate());
          $impression->setTime($time->getHourStart()->toDateTime());
  
          try {
          	$this->saveAndIncrementImpressionCount($context, $impression);
          } catch (Gpf_Exception $e) {
          	$context->debug($this->_('Saving impression interrupted: %s', $e->getMessage()));
          }
           
          $context->debug('  Saving impression ended');
      }
  
      protected function createRowImpression() {
          return new Pap_Db_Impression();
      }
  
      private function saveAndIncrementImpressionCount(Pap_Contexts_Impression $context, Pap_Db_Impression $impression) {
          $impression = $this->getImpression($impression);
  
          if ($context->isUnique()) {
              $context->debug('    Impression is unique');
              $impression->addUniqueCount($context->getCount());
          } else {
              $context->debug('    Impression is not unique');
          }
  
          $impression->addRawCount($context->getCount());
           
          $impression->save();
  
          Gpf_Plugins_Engine::extensionPoint('Tracker.impression.afterSave', $context);
      }
  
      /**
       * @return Pap_Db_Impression
       */
      private function getImpression(Pap_Db_Impression $impression) {
          // we have to explicitly set all columns, otherwise it does not behave correctly,
          // because it is ommitting columns with empty or null value
          $impressionsCollection = $impression->loadCollection(array(Pap_Db_Table_ClicksImpressions::USERID,
          Pap_Db_Table_ClicksImpressions::ACCOUNTID,
          Pap_Db_Table_ClicksImpressions::BANNERID,
          Pap_Db_Table_ClicksImpressions::PARENTBANNERID,
          Pap_Db_Table_ClicksImpressions::CAMPAIGNID,
          Pap_Db_Table_ClicksImpressions::COUNTRYCODE,
          Pap_Db_Table_ClicksImpressions::CDATA1,
          Pap_Db_Table_ClicksImpressions::CDATA2,
          Pap_Db_Table_ClicksImpressions::CHANNEL,
          Pap_Db_Table_ClicksImpressions::DATEINSERTED));
  
          if ($impressionsCollection->getSize() == 0) {
              return $impression;
          }
  
          if ($impressionsCollection->getSize() == 1) {
              return $impressionsCollection->get(0);
          }
          
          $firstImpression = $impressionsCollection->get(0);
          for ($i=1; $i<$impressionsCollection->getSize(); $i++) {
              $impression = $impressionsCollection->get($i);
              $firstImpression->addRawCount($impression->getRaw());
              $firstImpression->addUniqueCount($impression->getUnique());
              $impression->delete();
          }
          
          return $firstImpression;
      }
  }
  

} //end Pap_Tracking_Impression_Save

if (!class_exists('Pap_Common_Banner_Factory', false)) {
  class Pap_Common_Banner_Factory extends Gpf_Object {
      const BannerTypeText 		= 'T';
      const BannerTypeImage 		= 'I';
      const BannerTypeHtml 		= 'H';
      const BannerTypeFlash       = 'F';
      const BannerTypePopup 		= 'P';
      const BannerTypePopunder	= 'U';
  
      const BannerTypeLandingPage = 'L';
      const BannerTypeOffline     = 'O';
      const BannerTypePdf 		= 'V';
      const BannerTypePromoEmail  = 'E';
      const BannerTypeLink	= 'A';
  
  
      /**
       * Returns banner object
       *
       * @throws Gpf_DbEngine_NoRowException
       * @param string $bannerId banner ID
       * @return Pap_Common_Banner
       */
      public function getBanner($bannerId) {
          if ($bannerId == '') {
              throw new Pap_Common_Banner_NotFound($bannerId);
          }
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->from->add(Pap_Db_Table_Banners::getName());
          $select->select->addAll(Pap_Db_Table_Banners::getInstance());
          $select->where->add(Pap_Db_Table_Banners::ID, '=', $bannerId);
          return $this->getBannerFromRecord($select->getOneRow());
      }
  
      /**
       * Returns banner object for given banner record
       *
       * @param string $record banner record loaded from other code
       * @return Pap_Common_Banner
       */
      public function getBannerFromRecord(Gpf_Data_Record $record) {
          $banner = $this->getBannerObjectFromType($record->get('rtype'));
          if($banner == null) {
              throw new Pap_Common_Banner_NotFound($record->get('id'));
          }
  
          $banner->fillFromRecord($record);
          return $banner;
      }
  
      /**
       * @param string $bannerId
       * @param string $bannerType
       * @return Pap_Common_Banner
       */
      public function getBannerObject($bannerId, $bannerType) {
          $obj = $this->getBannerObjectFromType($bannerType);
          if($obj == null) {
              throw new Pap_Common_Banner_NotFound($bannerId);
          }
          $obj->setId($bannerId);
          $obj->load();
  
          return $obj;
      }
  
      /**
       * @param string $bannerType
       * @return Pap_Common_Banner
       */
      public static function getBannerObjectFromType($bannerType) {
          switch ($bannerType) {
              case self::BannerTypeText:
                  return new Pap_Common_Banner_Text();
              case self::BannerTypeImage:
                  return new Pap_Common_Banner_Image();
              case self::BannerTypeFlash:
                  return new Pap_Common_Banner_Flash();
              case self::BannerTypeHtml:
                  return new Pap_Common_Banner_Html();
              case self::BannerTypePromoEmail:
                  return new Pap_Common_Banner_PromoEmail();
              case self::BannerTypePdf:
                  return new Pap_Common_Banner_PDF();
              case self::BannerTypeLink:
              	return new Pap_Common_Banner_Link();
          }
          $bannerTypeRequest  = new Pap_Common_Banner_BannerRequest($bannerType);
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.BannerFactory.getBannerObjectFromType',$bannerTypeRequest);
          return $bannerTypeRequest->getBanner();
      }
  }
  

} //end Pap_Common_Banner_Factory

if (!class_exists('Gpf_Plugins_Context', false)) {
  class Gpf_Plugins_Context {
  	
  	/**
  	 * array of context parameters
  	 *
  	 * @var unknown_type
  	 */
  	private $_parameters = array();
      
      /**
       * @var Gpf_Log_Logger
       */
      private $_logger = null;
  
      protected function __construct() {
      }
  
      /**
       * @return Gpf_Log_Logger
       */
      public function getLogger() {
          return $this->_logger;
      }
      
      public function getLoggerGroupId() {
          if ($this->_logger != null) {
              return $this->_logger->getGroup();
          }
          return null;
      }
  
      public function setLogger($logger) {
          $this->_logger = $logger;
      }
  
      /**
       * returns array with all keys (parameter names) that are currently
       * stored in the context
       *
       * @return unknown
       */
      public function getAllKeys() {
          $keys = array();
          foreach($this->_parameters as $key) {
              $keys[] = $key;
          }
  
          return $keys;
      }
  
      public function set($key, $value) {
          $this->_parameters[$key] = $value;
      }
  
      public function get($key) {
          if(!isset($this->_parameters[$key])) {
              return null;
          }
          return $this->_parameters[$key];
      }
  
      public function log($logLevel, $message, $logGroup = null) {
          if($this->_logger !== null) {
              $this->_logger->log($message, $logLevel, $logGroup);
          }
      }
  
      public function critical($msg) {
          $this->log(Gpf_Log::CRITICAL, $msg);
      }
  
      public function debug($msg) {
          $this->log(Gpf_Log::DEBUG, $msg);
      }
  
      public function info($msg) {
          $this->log(Gpf_Log::INFO, $msg);
      }
  
      public function error($msg) {
          $this->log(Gpf_Log::ERROR, $msg);
      }
  
      public function warning($msg) {
          $this->log(Gpf_Log::WARNING, $msg);
      }
  }

} //end Gpf_Plugins_Context

if (!class_exists('Pap_Contexts_Tracking', false)) {
  abstract class Pap_Contexts_Tracking extends Gpf_Plugins_Context {
  
      const ACCOUNT_RECOGNIZED_FROM_CAMPAIGN = 'C';
      const ACCOUNT_RECOGNIZED_FROM_FORCED_PARAMETER = 'F';
      const ACCOUNT_RECOGNIZED_DEFAULT = 'D';
      
      /**
       * @var Pap_Tracking_Request
       * @deprecated
       */
      private $requestObject;
  
      /**
       * @var Pap_Tracking_Response
       * @deprecated
       */
      private $responseObject;
  
      /**
       * @var Pap_Db_Visit
       */
      protected $visit;
  
      /**
       * @var Gpf_Log_Logger
       */
      private $_logger = null;
  
      /**
       * @var instance
       */
      static protected $instance = null;
  
      /**
       * @var Pap_Db_VisitorAffiliate
       */
      private $visitorAffiliate;
  
      /**
       * @var boolean
       */
      private $containsRequiredParameters, $doTrackerSave, $doCommissionsSave;
  
      private $countryCode = null;
      
      private $accountRecognizeMethod = null;
      
      private $manualAddMode = false;
  
      /**
       * constructs context instance
       * It creates debug logger if there are parameters for it
       *
       */
      protected function __construct() {
          Gpf_Session::getAuthUser()->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
  
          $this->setActionType($this->getActionTypeConstant());
  
          $this->initDebugLogger();
  
          $this->setRequestObject( new Pap_Tracking_Request() );
          $this->setResponseObject( new Pap_Tracking_Response() );
  
          $cookieObj = new Pap_Tracking_Cookie();
          $cookieObj->setLogger($this->getLogger());
          $this->setCookieObject( $cookieObj );
      }
  
      /**
       * @param $rowWithIp Gpf_DbEngine_Row
       * @return String or null
       */
      protected function initCountryCode($rowWithIp) {
          if (is_null($this->countryCode) && !is_null($rowWithIp)) {
              $context = new Gpf_Data_Record(array(Pap_Db_Table_RawImpressions::IP, Pap_Db_Table_Impressions::COUNTRYCODE),
              array($rowWithIp->get('ip'), ''));
              Gpf_Plugins_Engine::extensionPoint('Tracker.request.getCountryCode', $context);
              $this->countryCode = $context->get(Pap_Db_Table_Impressions::COUNTRYCODE);
          }
          return $this->countryCode;
      }
  
      public function getReferrerUrl() {
          if ($this->visit == null) {
              return null;
          }
  
          return $this->visit->getReferrerUrl();
      }
  
      /**
       * @return Pap_Db_Visit
       */
      public function getVisit() {
          return $this->visit;
      }
  
      public function setVisit(Pap_Db_Visit $value) {
          $this->visit = $value;
      }
      
      private function setAccountRecognizeMethod($method) {
          $this->accountRecognizeMethod = $method;
      }
      
      public function getAccountRecognizeMethod() {
          return $this->accountRecognizeMethod;
      }
  
      public function getRealTotalCost() {
          return 0;
      }
  
      public function getFixedCost() {
          return 0;
      }
  
      public function isManualAddMode() {
          return $this->manualAddMode;
      }
  
      public function setManualAddMode($isManualAddMode) {
          $this->manualAddMode = $isManualAddMode;
      }
  
      /**
       * override this function and return the correct
       * Pap_Common_Constants::TYPE_XXXX type of your transaction to enable logging
       */
      protected function getActionTypeConstant() {
          return '';
      }
  
      protected function initDebugLogger() {
          $logger = Pap_Logger::create($this->getActionTypeConstant());
          if($logger != null) {
              $this->setLogger($logger);
          }
      }
  
      /**
       * @throws Gpf_Exception
       * @return Pap_Db_VisitorAffiliate
       */
      public function getVisitorAffiliate() {
          if ($this->isVisitorAffiliateRecognized()) {
              return $this->visitorAffiliate;
          }
          throw new Gpf_Exception('Visitor affiliate not recognized');
      }
  
      public function setVisitorAffiliate(Pap_Db_VisitorAffiliate $value) {
          $this->visitorAffiliate = $value;
      }
  
      public function isVisitorAffiliateRecognized() {
          return $this->visitorAffiliate != null;
      }
  
  
      /**
       * returns true if request contains all required parameters
       * @return boolean
       */
      public function getContainsRequiredParameters() {
          return $this->containsRequiredParameters;
      }
  
      /**
       * sets if request contains all required parameters
       * @param boolean $value
       */
      public function setContainsRequiredParameters($value) {
          $this->containsRequiredParameters = $value;
      }
  
      /**
       * returns true if transaction should be saved by Tracker
       * @return boolean
       */
      public function getDoTrackerSave() {
          return $this->doTrackerSave;
      }
  
      /**
       * sets if transaction should be saved by Tracker
       * @param boolean $value
       */
      public function setDoTrackerSave($value) {
          $this->doTrackerSave = $value;
      }
  
      /**
       * returns true if commission should be saved by Tracker
       * @return boolean
       */
      public function getDoCommissionsSave() {
          return $this->doCommissionsSave;
      }
  
      /**
       * sets if commission should be saved by Tracker
       * @param boolean $value
       */
      public function setDoCommissionsSave($value) {
          $this->doCommissionsSave = $value;
      }
  
      /**
       * gets request object (instance of Pap_Tracking_Request)
       * @return Pap_Tracking_Request
       * @deprecated
       */
      public function getRequestObject() {
          return $this->requestObject;
      }
  
      /**
       * sets request object (instance of Pap_Tracking_Request)
       */
      public function setRequestObject(Pap_Tracking_Request $value) {
          $this->requestObject = $value;
      }
  
      /**
       * gets response object (instance of Pap_Tracking_Response)
       * @return Pap_Tracking_Response
       */
      public function getResponseObject() {
          return $this->responseObject;
      }
  
      /**
       * sets response object (instance of Pap_Tracking_Response)
       */
      public function setResponseObject(Pap_Tracking_Response $value) {
          $this->responseObject = $value;
      }
  
      /**
       * gets user object (instance of Pap_Common_User)
       * @return Pap_Common_User
       */
      public function getUserObject() {
          return $this->get("userObject");
      }
  
      /**
       * sets user object (instance of Pap_Common_User)
       */
      public function setUserObject(Pap_Common_User $value = null) {
          $this->set("userObject", $value);
      }
  
      /**
       * @return Pap_Common_Banner
       */
      public function getBannerObject() {
          return $this->get("bannerObject");
      }
  
      /**
       * sets banner object (instance of Pap_Common_Banner)
       */
      public function setBannerObject($value) {
          $this->set("bannerObject", $value);
      }
  
      /**
       * gets cookie object (instance of Pap_Tracking_Cookie)
       * @return Pap_Tracking_Cookie
       */
      public function getCookieObject() {
          return $this->get("cookieObject");
      }
  
      /**
       * sets cookie object (instance of Pap_Tracking_Cookie)
       */
      public function setCookieObject(Pap_Tracking_Cookie $value) {
          $this->set("cookieObject", $value);
      }
  
      /**
       * gets campaign object (instance of Pap_Common_Campaign)
       * @return Pap_Common_Campaign
       */
      public function getCampaignObject() {
          return $this->get("campaignObject");
      }
      /**
       * gets commission group object (instance of Pap_Db_CommissionGroup)
       * @return Pap_Db_CommissionGroup
       */
      public function getCommissionGroup() {
          return $this->get('commissionGroup');
      }
  
      /**
       * sets campaign object (instance of Pap_Common_Campaign)
       */
      public function setCampaignObject(Pap_Common_Campaign $value = null) {
          $this->set("campaignObject", $value);
      }
  
      /**
       * sets commission group object (instance of Pap_Db_CommissionGroup)
       */
      public function setCommissionGroup($commGroup) {
          $this->set('commissionGroup', $commGroup);
      }
  
      /**
       * gets channel object (instance of Pap_Db_Channel)
       * @return Pap_Db_Channel
       */
      public function getChannelObject() {
          return $this->get("channelObject");
      }
  
      /**
       * sets channel object (instance of Pap_Db_Channel)
       */
      public function setChannelObject(Pap_Db_Channel $value = null) {
          $this->set("channelObject", $value);
      }
  
      /**
       * gets commission type object (instance of Pap_Db_CommissionType)
       * @return Pap_Db_CommissionType
       */
      public function getCommissionTypeObject() {
          return $this->get("commissionTypeObject");
      }
  
      /**
       * sets commission type object (instance of Pap_Db_CommissionType)
       */
      public function setCommissionTypeObject(Pap_Db_CommissionType $value) {
          $this->set("commissionTypeObject", $value);
      }
  
      /**
       * gets currency object (instance of Gpf_Db_Currency)
       * @return Gpf_Db_Currency
       */
      public function getDefaultCurrencyObject() {
          return $this->get("defaultCurrencyObject");
      }
  
      /**
       * gets currency object (instance of Gpf_Db_Currency)
       * @return Pap_Db_CommissionType
       */
      public function setDefaultCurrencyObject(Gpf_Db_Currency $value) {
          $this->set("defaultCurrencyObject", $value);
      }
  
      /**
       * @var array<Pap_Common_Transaction>
       */
      protected $transactions = array();
  
      /**
       * @return Pap_Common_Transaction
       */
      public function getTransaction($tier = 1) {
          return $this->getTransactionObject($tier);
      }
  
      /**
       * @return Pap_Common_Transaction
       */
      public function getTransactionObject($tier = 1) {
          if (array_key_exists($tier, $this->transactions)) {
              return $this->transactions[$tier];
          }
          return null;
      }
  
      public function setTransactionObject(Pap_Common_Transaction $transaction, $tier = 1) {
          $this->transactions[$tier] = $transaction;
      }
  
      /**
       * @var array <Pap_Tracking_Common_Commission>
       * first index: commission subtype
       * second index: commission tier
       */
      private $commissions = array();
  
      /**
       * add commission
       */
      public function addCommission(Pap_Tracking_Common_Commission $commission) {
          $this->commissions[$commission->getSubType()][$commission->getTier()] = $commission;
      }
  
      /**
       * remove commission
       */
      public function removeCommission($tier, $subtype = Pap_Db_Table_Commissions::SUBTYPE_NORMAL) {
          if (array_key_exists($subtype, $this->commissions) &&
          array_key_exists($tier, $this->commissions[$subtype])) {
              unset($this->commissions[$subtype][$tier]);
          }
      }
  
      /**
       * gets commissions for given tier
       *
       * @param int $tier
       * @return Pap_Tracking_Common_Commission
       */
      public function getCommission($tier, $subtype = Pap_Db_Table_Commissions::SUBTYPE_NORMAL) {
          if (array_key_exists($subtype, $this->commissions) &&
          array_key_exists($tier, $this->commissions[$subtype])) {
              return $this->commissions[$subtype][$tier];
          }
          return null;
      }
  
      public function setStatusForAllCommissions($status) {
          foreach ($this->commissions as $subTypeCommissions) {
              foreach ($subTypeCommissions as $commission) {
                  $commission->setStatus($status);
              }
          }
      }
  
      /**
       * gets action type
       * @return string
       */
      public function getActionType() {
          return $this->get("actionType");
      }
  
      /**
       * sets action type
       */
      public function setActionType($value) {
          $this->set("actionType", $value);
      }
  
      public function getVisitorId() {
          return $this->get("visitorId");
      }
  
      public function setVisitorId($value) {
          $this->set("visitorId", $value);
      }
  
      public function getDateCreated() {
          return $this->get("dateCreated");
      }
  
      public function setDateCreated($value) {
          $this->set("dateCreated", $value);
      }
  
      /**
       * @return string datetime in standard format
       */
      public function getVisitDateTime() {
          if (($visit = $this->getVisit()) != null) {
              return $visit->getDateVisit();
          }
          if (!is_null($this->getDateCreated()) && $this->getDateCreated() !== '') {
              return $this->getDateCreated();
          }
          return Gpf_Common_DateUtils::now();
      }
  
      public function getCountryCode() {
      	if ($this->getVisit() == null) {
      		return '';
      	}
          return ($this->getVisit()->getCountryCode());
      }
  
      public function getAccountId() {
          return $this->get("accountId");
      }
  
      public function setAccountId($value, $method) {
          $this->set("accountId", $value);
          $this->accountRecognizeMethod = $method;
      }
  }

} //end Pap_Contexts_Tracking

if (!class_exists('Pap_Contexts_Impression', false)) {
  class Pap_Contexts_Impression extends Pap_Contexts_Tracking {
      
      private $count = 1;
      
      /**
       * @var Pap_Db_RawImpression
       */
      private $rawImpression;
  
      public function __construct(Pap_Db_RawImpression $rawImpression) {
      	parent::__construct();
      	$this->rawImpression = $rawImpression;
      }
      
      public function setCount($count) {
      	$this->count = $count;
      }
  
      public function getCount() {
      	return $this->count;
      }
  
      protected function getActionTypeConstant() {
      	return Pap_Common_Constants::TYPE_CPM;
      }
  
      public function getCountryCode() {
          return $this->initCountryCode($this->rawImpression);
      }
  
      public function getClickData1() {
          return $this->rawImpression->getData1();
      }
  
      public function getClickData2() {
          return $this->rawImpression->getData2();
      }
  
      public function getDate() {
          return $this->rawImpression->getDate();
      }
  
      public function isUnique() {
          return $this->rawImpression->isUnique();
      }
      
      public function getBannerId() {
          $bannerObj = $this->getBannerObject();
          if($bannerObj != null) {
              return $bannerObj->getId();
          }
          return null;
      }
  
      public function getCampaignId() {
          $bannerObj = $this->getBannerObject();
          if($bannerObj != null) {
              return $bannerObj->getCampaignId();
          }
          return null;
      }
      
      public function getParentBannerId() {
          return $this->rawImpression->getParentBannerId();
      }
  
      public function getChannelId() {
          $channelObj = $this->getChannelObject();
          if($channelObj != null) {
              return $channelObj->getId();
          }
          return null;
      }
      
  }

} //end Pap_Contexts_Impression

if (!class_exists('Pap_Tracking_Response', false)) {
  class Pap_Tracking_Response extends Gpf_Object {
      
      public function finishTracking() { 
          echo "_tracker.trackNext();";
      }
      
      public function outputEmptyImage() {
  		Gpf_Http::setHeader('Content-Type', 'image/gif');
  		$pixFile = new Gpf_Io_File('pix.gif');
  		$pixFile->output();
      }
      
      public function redirectTo($url) {
          Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::LOCATION, $url, 301);
          //echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$url\">";
      }
  }

} //end Pap_Tracking_Response

if (!class_exists('Gpf_DbEngine_RowComposite', false)) {
  abstract class Gpf_DbEngine_RowComposite extends Gpf_DbEngine_RowBase implements IteratorAggregate  {
      protected $rowObjects;
      private $mainRow;
  
      protected function __construct(Gpf_DbEngine_RowBase $mainRow, $alias = '') {
          $this->mainRow = $mainRow;
          $this->addRowObject($mainRow, $alias);
      }
  
      protected function addRowObject(Gpf_DbEngine_RowBase $row, $alias = '') {
          if ($alias != '') {
              $this->rowObjects[$alias . '_'] = $row;
          } else {
              $this->rowObjects[] = $row;
          }
      }
  
      /**
       * Fills Db_Row from a record
       * Fields that are not part of the Db_Row are ignored
       *
       * @param Gpf_Data_Record $record
       */
      public function fillFromRecord(Gpf_Data_Record $record) {
          foreach ($this->rowObjects as $alias => $rowObject) {
              $rowObject->fillFromRecord($record);
          }
      }
  
      public function toArray() {
          $array = array();
          foreach ($this->rowObjects as $alias => $rowObject) {
              $array = array_merge($array, $rowObject->toArray());
          }
          return $array;
      }
  
      public function fillFromSelect(Gpf_SqlBuilder_SelectBuilder $select) {
          $collection = array();
          $this->prepareSelectClause($select);
          foreach ($select->getAllRowsIterator() as $rowRecord) {
              $row = new $this;
              $row->fillFromRecord($rowRecord);
              $row->setPersistent(true);
              $collection[] = $row;
          }
          return $collection;
      }
  
      public function prepareSelectClause(Gpf_SqlBuilder_SelectBuilder $select, $aliasPrefix = '') {
          foreach ($this->rowObjects as $alias => $rowObject) {
              $rowObject->prepareSelectClause($select, $alias);
          }
      }
  
      /**
       * @return Gpf_DbEngine_Row
       */
      protected function getRowObject($alias) {
          return $this->rowObjects[$alias];
      }
  
      public function get($name) {
          foreach ($this->rowObjects as $row) {
              try {
                  return $row->get($name);
              } catch (Gpf_Exception $e) {
              }
          }
          throw new Gpf_Exception("Column '$name' is not valid in row composite");
      }
  
      public function set($name, $value) {
          $success = false;
          foreach ($this->rowObjects as $row) {
              try {
                  $row->set($name, $value);
                  $success = true;
              } catch (Gpf_Exception $e) {
              }
          }
          if (!$success) {
              throw new Gpf_Exception("Column '$name' is not valid in row composite");
          }
      }
  
      public function getAttributes() {
          $attributes = array();
          foreach ($this->rowObjects as $alias => $rowObject) {
              $attributes = array_merge($attributes, $rowObject->getAttributes());
          }
          return $attributes;
      }
  
      public function getIterator() {
          $columns = array();
          foreach ($this->rowObjects as $row) {
              foreach ($row as $columnName => $columnValue) {
                  if (isset($columns[$columnName])) {
                      continue;
                  }
                  $columns[$columnName] = $columnValue;
              }
          }
          return new ArrayIterator($columns);
      }
  
      /**
       * Performs explicit check on Db_Row
       *
       * @throws Gpf_DbEngine_Row_CheckException if there is some error
       */
      public function check() {
          $constraintExceptions = array();
          foreach ($this->rowObjects as $rowObject) {
              try {
                  $rowObject->check();
              } catch (Gpf_DbEngine_Row_CheckException $e) {
                  foreach ($e as $constraintException) {
                      $constraintExceptions[] = $constraintException;
                  }
              }
          }
          if (count($constraintExceptions) > 0) {
              throw new Gpf_DbEngine_Row_CheckException($constraintExceptions);
          }
      }
  }
  

} //end Gpf_DbEngine_RowComposite

if (!class_exists('Pap_Common_User', false)) {
  class Pap_Common_User extends Gpf_DbEngine_RowComposite  {
  
      /**
       * @var Pap_Db_User
       */
      protected $user;
  
      /**
       * @var Gpf_Db_User
       */
      protected $accountUser;
  
      /**
       * @var Gpf_Db_AuthUser
       */
      protected $authUser;
  
      private $inserting = false;
  
      private $sendNotification = true;
  
      public function __construct() {
          $this->user = new Pap_Db_User();
          parent::__construct($this->user);
  
          $this->accountUser = new Gpf_Db_User();
          $this->addRowObject($this->accountUser);
  
          $this->authUser = new Pap_Common_AuthUser();
          $this->addRowObject($this->authUser);
  
          $this->setDeleted(false);
      }
  
      /**
       * @return Gpf_Db_AuthUser
       */
      public function getAuthUser() {
          return $this->authUser;
      }
  
      public function delete() {
          $this->user->delete();
          $this->accountUser->delete();
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.User.afterDelete', $this);
      }
  
      public function changeStatus($userid, $status) {
          $this->setPrimaryKeyValue($userid);
          $this->load();
          $this->setStatus($status);
          $this->save();
      }
  
      public function getStatus() {
          return $this->get(Gpf_Db_Table_Users::STATUS);
      }
  
      public function setStatus($status) {
          $this->set(Gpf_Db_Table_Users::STATUS, $status);
      }
  
      public function setAccountUserId($accountUserId) {
          $this->set(Pap_Db_Table_Users::ACCOUNTUSERID, $accountUserId);
      }
  
      public function setSendNotification($status) {
          $this->sendNotification = $status;
      }
  
      public function getSendNotification() {
          return $this->sendNotification;
      }
  
      public function setPayoutOptionId($payoutOptionId) {
          $this->set(Pap_Db_Table_Users::PAYOUTOPTION_ID, $payoutOptionId);
      }
  
      public function insert() {
          return $this->save();
      }
  
      public function update($updateColumns = array()) {
          return $this->save();
      }
  
      public function save() {
          if ($this->isFirstChangeStatus()) {
              $this->setDateApproved(Gpf_Common_DateUtils::now());
          }
          try {
              $authUser = new Gpf_Db_AuthUser();
              $authUser->setPrimaryKeyValue($this->authUser->getPrimaryKeyValue());
              $authUser->load();
              $this->accountUser->setAuthId($authUser->getId());
          } catch (Gpf_Exception $e) {
              try {
                  $this->authUser->loadFromUsername();
                  $this->accountUser->setAuthId($this->authUser->getId());
              } catch (Exception $e) {
              }
          }
  
          $this->inserting = !$this->user->rowExists();
  
          $this->checkConstraints();
  
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.User.beforeSave', $this);
  
          $this->authUser->save();
          $this->accountUser->setAuthId($this->authUser->getId());
  
          try {
              $this->accountUser->save();
          } catch (Gpf_Exception $e) {
              $this->authUser->delete();
              throw new Gpf_Exception($e->getMessage());
          }
  
          $this->user->set('accountuserid', $this->accountUser->get('accountuserid'));
          $this->initRefid($this->accountUser->getId());
          $this->initMinimupPayout();
  
          try {
              $this->user->save();
          } catch (Gpf_Exception $e) {
              $this->authUser->delete();
              $this->accountUser->delete();
              throw new Gpf_Exception($e->getMessage());
          }
  
          if($this->inserting) {
              $this->afterInsert();
          } else {
              Pap_Db_Table_CachedBanners::deleteCachedBannersForUser($this->user->getId(), $this->user->getRefId());
              Gpf_Plugins_Engine::extensionPoint('PostAffiliate.User.afterSave', $this);
          }
      }
  
      protected function setDefaultTheme($theme) {
          Gpf_Db_Table_UserAttributes::setSetting(Gpf_Auth_User::THEME_ATTRIBUTE_NAME, $theme, $this->getAccountUserId());
      }
  
      protected function setQuickLaunchSetting($setting) {
          Gpf_Db_Table_UserAttributes::setSetting('quickLaunchSetting', $setting, $this->getAccountUserId());
      }
  
  
      protected function initRefid($refid) {
      }
  
      protected function afterInsert() {
          $this->setupNewUser();
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.User.afterInsert', $this);
      }
  
      protected function setupNewUser() {
      }
  
      public function load() {
          $this->user->load();
          $this->accountUser->set('accountuserid', $this->user->get('accountuserid'));
          $this->accountUser->load();
          $this->authUser->set('authid', $this->accountUser->get('authid'));
          $this->authUser->load();
      }
  
      public function loadFromData(array $loadColumns = array()) {
          $this->user->loadFromData($loadColumns);
          $this->accountUser->set('accountuserid', $this->user->get('accountuserid'));
          $this->accountUser->load();
          $this->authUser->set('authid', $this->accountUser->get('authid'));
          $this->authUser->load();
      }
  
      /**
       * changes status of user(s)
       *
       * @service affiliate write
       * @param ids - array of IDs
       * @param status - new status
       * @return Gpf_Rpc_Action
       */
      public function changeStatusUsers(Gpf_Rpc_Params $params) {
          $action = new Gpf_Rpc_Action($params);
          $action->setErrorMessage($this->_('Failed to change status for %s user(s)'));
          $action->setInfoMessage($this->_('Status successfully changed for %s user(s)'));
  
          $status = $action->getParam("status");
  
          if (!in_array($status, array(Gpf_Db_User::APPROVED, Gpf_Db_User::PENDING, Gpf_Db_User::DECLINED))) {
              throw new Exception($this->_("Status does not have allowed value"));
          }
  
  
          foreach ($action->getIds() as $userid) {
              try {
                  $result = $this->changeStatus($userid, $status);
                  $action->addOk();
              } catch (Exception $e) {
                  $action->addError();
              }
          }
          return $action;
      }
  
      /**
       * For compatibility with FormService
       *
       * @param string $id
       */
      public function setPrimaryKeyValue($id) {
          $this->setId($id);
      }
  
      /**
       * For compatibility with FormService
       */
      public function getPrimaryKeyValue() {
          return $this->getId();
      }
  
      /**
       * @return string PAP user id
       */
      public function getId() {
          return $this->user->getId();
      }
  
      public function getParentUserId() {
          return $this->user->getParentUserId();
      }
  
      public function setParentUserId($userid) {
          $this->user->setParentUserId($userid);
      }
  
      public function setOriginalParentUserId($userid) {
          $this->user->setOriginalParentUserId($userid);
      }
  
      public function setId($id) {
          return $this->user->setId($id);
      }
  
      public function setPassword($password) {
          $this->set(Gpf_Db_Table_AuthUsers::PASSWORD, $password);
      }
  
      public function setUserName($username) {
          $this->set(Gpf_Db_Table_AuthUsers::USERNAME, $username);
      }
  
      public function setFirstName($firstName) {
          $this->set(Gpf_Db_Table_AuthUsers::FIRSTNAME, $firstName);
      }
  
      public function setLastName($lastName) {
          $this->set(Gpf_Db_Table_AuthUsers::LASTNAME, $lastName);
      }
  
      public function setDateInserted($value) {
          $this->set(Pap_Db_Table_Users::DATEINSERTED, $value);
      }
  
      public function setDateApproved($value) {
          $this->set(Pap_Db_Table_Users::DATEAPPROVED, $value);
      }
  
      public function getDateApproved() {
          return $this->get(Pap_Db_Table_Users::DATEAPPROVED);
      }
  
      public function setAccountId($accountId) {
          $this->accountUser->setAccountId($accountId);
      }
  
      public function setRoleId($roleId) {
          $this->accountUser->setRoleId($roleId);
      }
  
      public function getRoleId() {
          return $this->accountUser->getRoleId();
      }
  
      public function setRefId($refId) {
          $this->set('refid', $refId);
      }
  
      public function setNote($value) {
          $this->user->set(Pap_Db_Table_Users::NOTE, $value);
      }
  
      public function setPhoto($value) {
          $this->user->set(Pap_Db_Table_Users::PHOTO, $value);
      }
  
      public function setData($i, $value) {
          $this->user->set('data'.$i, $value);
      }
  
      public function getData($i) {
          return $this->user->get('data'.$i);
      }
  
      public function getRefId() {
          $refId = $this->get('refid');
          if($refId != null && $refId != '') {
              return $refId;
          }
          return $this->getId();
      }
  
      public function getName() {
          return $this->get('firstname')." ".$this->get('lastname');
      }
  
      public function setIp($ip) {
          return $this->authUser->setIp($ip);
      }
  
      public function getIp() {
          return $this->authUser->getIp();
      }
  
      public function getUserName() {
          return $this->get('username');
      }
  
      public function getFirstName() {
          return $this->get(Gpf_Db_Table_AuthUsers::FIRSTNAME);
      }
  
      public function getLastName() {
          return $this->get(Gpf_Db_Table_AuthUsers::LASTNAME);
      }
  
      public function getEmail() {
          return $this->authUser->getEmail();
      }
  
      public function setEmail($email) {
          $this->authUser->setEmail($email);
      }
  
      public function getType() {
          return $this->get(Pap_Db_Table_Users::TYPE);
      }
  
      public function getPassword() {
          return $this->get(Gpf_Db_Table_AuthUsers::PASSWORD);
      }
  
      public function getAccountUserId() {
          return $this->accountUser->getPrimaryKeyValue();
      }
  
      public function getAccountId() {
          return $this->accountUser->getAccountId();
      }
  
      public function getPayoutOptionId() {
          return $this->user->getPayoutOptionId();
      }
  
      public function getMinimumPayout() {
          return $this->user->getMinimumPayout();
      }
  
      public function setType($type) {
          $this->set(Pap_Db_Table_Users::TYPE, $type);
      }
  
      /**
       * @param boolean $deleted
       */
      public function setDeleted($deleted) {
          if ($deleted == false) {
              $this->user->setDeleted(Gpf::NO);
          } else {
              $this->user->setDeleted(Gpf::YES);
          }
      }
  
      public function getDeleted(){
          return $this->user->getDeleted();
      }
  
      public function setMinimumPayout($minimumPayout) {
          $this->user->setMinimumPayout($minimumPayout);
      }
  
      /**
       * @return Pap_Common_User or null
       */
      public function getParentUser() {
          $parentUserId = $this->getParentUserId();
          if($parentUserId == '') {
              return null;
          }
  
          $objUser = new Pap_Common_User();
          $objUser->setPrimaryKeyValue($parentUserId);
          try {
              $objUser->load();
              return $objUser;
          } catch (Gpf_DbEngine_NoRowException $e) {
              return null;
          }
      }
  
      public static function getMerchantEmail() {
          return Gpf_Settings::get(Pap_Settings::MERCHANT_NOTIFICATION_EMAIL);
      }
  
      /**
       * @throws Gpf_DbEngine_NoRowException
       * @param $userId
       * @return Pap_Common_User
       */
      public static function getUserById($userId) {
          $user = new Pap_Common_User();
          $user->setId($userId);
          $user->load();
          return $user;
      }
  
      protected function addGadget($type, $name, $url, $posType, $top, $left, $width, $height) {
          $gadgetManager = new Gpf_GadgetManager();
          $gadget = $gadgetManager->addGadgetNoRpc($name, $url, $posType, $this->getAccountUserId());
          $gadget->setType($type);
          $gadget->setPositionTop($top);
          $gadget->setPositionLeft($left);
          $gadget->setWidth($width);
          $gadget->setHeight($height);
          $gadget->save();
      }
  
      private function initMinimupPayout() {
          if ($this->inserting) {
              $this->user->setMinimumPayout(Gpf_Settings::get(Pap_Settings::PAYOUTS_MINIMUM_PAYOUT_SETTING_NAME));
          }
      }
  
      public function getNumberOfUsersFromSameIP($ip, $periodInSeconds) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
  
          $select->select->add("count(au.authid)", "count");
          $select->from->add(Pap_Db_Table_Users::getName(),'pu');
          $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(),'qu','pu.'.Pap_Db_Table_Users::ACCOUNTUSERID.'=qu.'.Gpf_Db_Table_Users::ID);
          $select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(),'au','au.'.Gpf_Db_Table_AuthUsers::ID.'=qu.'.Gpf_Db_Table_Users::AUTHID. ' and qu.'.Gpf_Db_Table_Users::ROLEID."='".Pap_Application::DEFAULT_ROLE_AFFILIATE."'");
          $select->where->add('au.'.Gpf_Db_Table_AuthUsers::IP, "=", $ip);
          $dateFrom = new Gpf_DateTime();
          $dateFrom->addSecond(-1*$periodInSeconds);
          $select->where->add(Pap_Db_Table_Users::DATEINSERTED, ">", $dateFrom->toDateTime());
  
          $recordSet = new Gpf_Data_RecordSet();
          $recordSet->load($select);
  
          foreach($recordSet as $record) {
              return $record->get("count");
          }
          return 0;
      }
  
      /**
       * @return boolean
       */
      public function isFirstChangeStatus() {
          $firstChange = false;
          if ($this->getStatus() !== Pap_Common_Constants::STATUS_PENDING &&
          ($this->getDateApproved() === null || $this->getDateApproved() === "")) {
              $firstChange = true;
          }
          return $firstChange;
      }
  
      public function getOriginalParentUserId() {
          return $this->user->getOriginalParentUserId();
      }
  
      private function checkConstraints() {
          try{
              $this->check();
          } catch (Gpf_DbEngine_Row_CheckException $e) {
              $exceptions = array();
              foreach ($e as $constraintExeption) {
                  if($constraintExeption instanceof Gpf_DbEngine_Row_PasswordConstraintException && $this->accountUser->getRoleId() == Pap_Application::DEFAULT_ROLE_MERCHANT) {
                      continue;
                  }
                  $exceptions[] = $constraintExeption;
              }
              if(count($exceptions) > 0) {
                  throw new Gpf_DbEngine_Row_CheckException($exceptions);
              }
          }
      }
  }
  

} //end Pap_Common_User

if (!class_exists('Pap_Affiliates_User', false)) {
  class Pap_Affiliates_User extends Pap_Common_User  {
  
      public function __construct() {
          parent::__construct();
          $this->setType(Pap_Application::ROLETYPE_AFFILIATE);
          $this->setRoleId(Pap_Application::DEFAULT_ROLE_AFFILIATE);
      }
      
      public function set($name, $value) {
          if($name == Gpf_Db_Table_Users::STATUS && ($this->getStatus() != $value)) {
              Gpf_Plugins_Engine::extensionPoint('PostAffiliate.affiliate.userStatusChanged', new Gpf_Plugins_ValueContext(array($this, $value)));
          }
          
          parent::set($name, $value);
      }
  
      protected function setupNewUser() {
          $this->setDefaultTheme(Gpf_Settings::get(Pap_Settings::DEFAULT_AFFILIATE_PANEL_THEME));
          $this->setQuickLaunchSetting('showDesktop,Home,Campaigns-List-Wide,Promo-Materials,Trends-Report');
          $this->addDefaultGadgets();
      }
  
      private function addDefaultGadgets() {
          $this->addGadget('C', Gpf_Lang::_runtime('Payout'), 'content://PayoutGadget',
          Gpf_Db_Table_Gadgets::POSITION_TYPE_SIDEBAR, 63, 1000, 333, 92);
      }
  
      private function sendUserEmails() {
          if ($this->getStatus() == Pap_Common_Constants::STATUS_APPROVED) {
              $this->sendNewUserSignupApprovedMail();
          } else if ($this->getStatus() == Pap_Common_Constants::STATUS_DECLINED) {
              $this->sendNewUserSignupDeclinedMail();
          }
      }
  
      protected function sendNewUserSignupApprovedMail() {
          $signupEmail = new Pap_Signup_SendEmailToUser();
          $signupEmail->sendNewUserSignupApprovedMail($this, $this->getEmail());
      }
  
      protected function sendNewUserSignupDeclinedMail() {
          $signupEmail = new Pap_Signup_SendEmailToUser();
          $signupEmail->sendNewUserSignupDeclinedMail($this, $this->getEmail());
      }
  
      public function check() {
          if (Gpf_Application::isDemo() && Gpf_Application::isDemoEntryId($this->getId())) {
              if ($this->getUserName() != Pap_Branding::DEMO_AFFILIATE_USERNAME ||
              $this->getPassword() != Pap_Branding::DEMO_PASSWORD ||
              $this->getStatus() != "A") {
                  throw new Gpf_Exception("Demo affiliate username, password and status can not be modified");
              }
          }
          return parent::check();
      }
  
      public function save() {
          $firstChange = $this->isFirstChangeStatus();
  
          parent::save();
          
          if ($firstChange && $this->getSendNotification()) {
              $this->sendUserEmails();
          }
          if ($firstChange && $this->getStatus() == Gpf_Db_User::APPROVED) {
              $this->updateStatusSignupAndReferral();
              Gpf_Plugins_Engine::extensionPoint('PostAffiliate.affiliate.firsttimeApproved', $this);
          }
          
      }
  
      protected function updateStatusSignupAndReferral() {
          $update = new Gpf_SqlBuilder_UpdateBuilder();
          $update->from->add(Pap_Db_Table_Transactions::getName());
          $update->set->add(Pap_Db_Table_Transactions::R_STATUS, Pap_Common_Constants::STATUS_APPROVED);
          $update->where->add(Pap_Db_Table_Transactions::DATA5, '=', $this->getId());
          $update->where->add(Pap_Db_Table_Transactions::R_STATUS, '=', Pap_Common_Constants::STATUS_PENDING);
          $typeWhere = new Gpf_SqlBuilder_CompoundWhereCondition();
          $typeWhere->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Db_Transaction::TYPE_SIGNUP_BONUS, 'OR');
          $typeWhere->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Db_Transaction::TYPE_REFERRAL, 'OR');
          $update->where->addCondition($typeWhere);
          $update->execute();
      }
  
      public function delete($moveChildAffiliates = false) {
          if (Gpf_Application::isDemo() && Gpf_Application::isDemoEntryId($this->getId())) {
              throw new Gpf_Exception("Demo affiliate can not be deleted");
          }
          $this->load();
          if ($moveChildAffiliates) {
              $this->moveChildAffiliatesTo($this->getParentUserId());
          } else {
              $this->clearParentAffiliate();
          }
          return parent::delete();
      }
  
      private function moveChildAffiliatesTo($toAffiliateId) {
          $updateBuilder = new Gpf_SqlBuilder_UpdateBuilder();
          $updateBuilder->from->add(Pap_Db_Table_Users::getName());
          $updateBuilder->set->add(Pap_Db_Table_Users::PARENTUSERID, $toAffiliateId);
          $updateBuilder->where->add(Pap_Db_Table_Users::PARENTUSERID, "=", $this->getId());
          $updateBuilder->execute();
      }
  
      private function clearParentAffiliate() {
          $updateBuilder = new Gpf_SqlBuilder_UpdateBuilder();
          $updateBuilder->from->add(Pap_Db_Table_Users::getName());
          $updateBuilder->set->add(Pap_Db_Table_Users::PARENTUSERID, "");
          $updateBuilder->where->add(Pap_Db_Table_Users::PARENTUSERID, "=", $this->getId());
          $updateBuilder->execute();
      }
  
      /**
       * gets user by refid or userid
       * @param $id
       * @return Pap_Affiliates_User
       * @throws Gpf_Exception
       */
      public static function loadFromId($id) {
          try {
              return self::loadFromRefid($id);
          } catch (Gpf_Exception $e) {
              return self::loadFromUserid($id);
          }
      }
  
      /**
       * @param $userid
       * @return Pap_Affiliates_User
       * @throws Gpf_Exception
       */
      private static function loadFromUserid($userid) {
          $user = new Pap_Affiliates_User();
          $user->setPrimaryKeyValue($userid);
          $user->load();
          return $user;
      }
  
      /**
       * @param $refid
       * @return Pap_Affiliates_User
       * @throws Gpf_Exception
       */
      private static function loadFromRefid($refid) {
          $user = new Pap_Affiliates_User();
          $user->setRefId($refid);
          $user->loadFromData(array(Pap_Db_Table_Users::REFID));
          return $user;
      }
  }
  

} //end Pap_Affiliates_User

if (!class_exists('Pap_Db_User', false)) {
  class Pap_Db_User extends Gpf_DbEngine_Row {
  
      function __construct(){
          parent::__construct();
      }
  
      function init() {
          $this->setTable(Pap_Db_Table_Users::getInstance());
          parent::init();
      }
  
      public function getType() {
          return $this->get(Pap_Db_Table_Users::TYPE);
      }
  
      public function getId() {
          return $this->get(Pap_Db_Table_Users::ID);
      }
  
      public function getParentUserId() {
          return $this->get(Pap_Db_Table_Users::PARENTUSERID);
      }
      
      public function getOriginalParentUserId() {
          return $this->get(Pap_Db_Table_Users::ORIGINAL_PARENT_USERID);
      }
  
      public function setParentUserId($userid) {
          $this->set(Pap_Db_Table_Users::PARENTUSERID, $userid);
      }
      
      public function setType($type) {
          $this->set(Pap_Db_Table_Users::TYPE, $type);
      }
  
      public function setOriginalParentUserId($userid) {
          $this->set(Pap_Db_Table_Users::ORIGINAL_PARENT_USERID, $userid);
      }
  
      public function setId($userid) {
          $this->set(Pap_Db_Table_Users::ID, $userid);
      }
  
      public function insert() {
          parent::insert();
      }
      
      protected function generatePrimaryKey() {
          parent::generatePrimaryKey();
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.User.generatePrimaryKey', $this);
      }
  
      public function update($updateColumns = array()) {
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.User.onUpdate', $this);
          parent::update($updateColumns);
      }
  
      public function setDeleted($deleted) {
          $this->set(Pap_Db_Table_Users::DELETED, $deleted);
      }
  
      public function setMinimumPayout($minimupPayout) {
          $this->set(Pap_Db_Table_Users::MINIMUM_PAYOUT, $minimupPayout);
      }
  
      public function getRefId() {
          return $this->get(Pap_Db_Table_Users::REFID);
      }
  
      public function setRefId($refid) {
          $this->set(Pap_Db_Table_Users::REFID, $refid);
      }
  
      public function getAccountUserId() {
          return $this->get(Pap_Db_Table_Users::ACCOUNTUSERID);
      }
      
      public function setAccountUserId($accountUserId) {
          $this->set(Pap_Db_Table_Users::ACCOUNTUSERID, $accountUserId);
      }
  
      public function getPayoutOptionId() {
          return $this->get(Pap_Db_Table_Users::PAYOUTOPTION_ID);
      }
  
      public function getMinimumPayout() {
          return $this->get(Pap_Db_Table_Users::MINIMUM_PAYOUT);
      }
      
      public function getDeleted(){
          return $this->get(Pap_Db_Table_Users::DELETED);
      }
  }
  

} //end Pap_Db_User

if (!class_exists('Pap_Db_Table_Users', false)) {
  class Pap_Db_Table_Users extends Gpf_DbEngine_Table {
      const ID = 'userid';
      const REFID = 'refid';
      const NUMBERUSERID = 'numberuserid';
      const TYPE = 'rtype';
      const DATEINSERTED = 'dateinserted';
      const DATEAPPROVED = 'dateapproved';
      const DELETED = 'deleted';
      const ACCOUNTUSERID = 'accountuserid';
      const PARENTUSERID = 'parentuserid';
      const PAYOUTOPTION_ID = "payoutoptionid";
      const MINIMUM_PAYOUT = "minimumpayout";
      const NOTE = "note";
      const PHOTO = "photo";
      const ORIGINAL_PARENT_USERID = 'originalparentuserid';
  
      private static $instance;
  
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
  
      protected function initName() {
          $this->setName('pap_users');
      }
  
      public static function getName() {
          return self::getInstance()->name();
      }
  
      public static function getDataColumnName($i) {
          return 'data'.$i;
      }
  
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, 'char', 8, true);
          $this->createColumn(self::REFID, 'char', 128, true);
          $this->createColumn(self::NUMBERUSERID, self::INT);
          $this->createColumn(self::TYPE, 'char', 1);
          $this->createColumn(self::DATEINSERTED, 'datetime', 0);
          $this->createColumn(self::DATEAPPROVED, 'datetime', 0);
          $this->createColumn(self::DELETED, 'char', 1);
          $this->createColumn(self::ACCOUNTUSERID, 'char', 20);
          $this->createColumn(self::PARENTUSERID, 'char', 20);
          $this->createColumn(self::MINIMUM_PAYOUT, 'char', 20);
          $this->createColumn(self::PAYOUTOPTION_ID, 'char', 8);
          $this->createColumn(self::NOTE, 'char');
          $this->createColumn(self::PHOTO, 'char', 255);
          $this->createColumn(self::ORIGINAL_PARENT_USERID, self::CHAR, 20);
          for ($i = 1; $i <= 25; $i++) {
              $this->createColumn(self::getDataColumnName($i), self::CHAR, 255);
          }
      }
  
      public static function getAffiliateCount() {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->select->add('COUNT(*)', 'count');
          $select->from->add(self::getName(), 'pu');
          $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'u', 'u.accountuserid=pu.accountuserid');
          $select->where->add('pu.' . self::TYPE, '=', Pap_Application::ROLETYPE_AFFILIATE);
          $select->where->add('u.'.Gpf_Db_Table_Users::STATUS, '=', Gpf_Db_User::APPROVED);
          $select->where->add('pu.'.Pap_Db_Table_Users::DELETED, '=', Gpf::NO);
          return $select->getOneRow()->get('count');
      }
  
      /**
       * Pap alert application handle, do not modifi this source!
       *
       * @return Gpf_Data_Record
       */
      public static function getAffiliatesCount($date) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->select->add("SUM(IF(gu.".Gpf_Db_Table_Users::STATUS."= 'A', 1, 0))", 'affiliates_approved');
          $select->select->add("SUM(IF(gu.".Gpf_Db_Table_Users::STATUS."= 'P', 1, 0))", 'affiliates_pending');
          $select->select->add("SUM(IF(gu.".Gpf_Db_Table_Users::STATUS."= 'D', 1, 0))", 'affiliates_declined');
          $select->from->add(Gpf_Db_Table_Users::getName(), 'gu');
          $select->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'pu',
              'gu.'.Gpf_Db_Table_Users::ID.'=pu.'.Pap_Db_Table_Users::ACCOUNTUSERID);
          $select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au',
              'gu.'.Gpf_Db_Table_Users::AUTHID.'=au.'.Gpf_Db_Table_AuthUsers::ID);
          $select->where->add('gu.'.Gpf_Db_Table_Users::ROLEID, "=", Pap_Application::DEFAULT_ROLE_AFFILIATE);
          $select->where->add('pu.'.Pap_Db_Table_Users::DATEINSERTED, ">=", $date);
          $row = $select->getOneRow();
  
          return $row;
      }
  
      protected function initConstraints() {
          $this->addConstraint(new Gpf_DbEngine_Row_RegExpConstraint(self::REFID,
                                      "/^[a-zA-Z0-9_\-]*$/",
          $this->_('Referral ID can contain only [a-zA-Z0-9_-] characters. %s given')));
  
          $this->addConstraint(new Gpf_DbEngine_Row_ColumnsNotEqualConstraint(self::REFID, array(self::ID, self::REFID),
          $this->_("Referral ID is already used")));
  
          $this->addConstraint(new Gpf_DbEngine_Row_RelationConstraint(
          array(self::PARENTUSERID => self::ID),
          new Pap_Db_User_SpecialInit($this),
          false,
          $this->_('Selected parent affiliate does not exist')));
  
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CachedBanners::USERID, new Pap_Db_CachedBanner());
          $this->addCascadeDeleteConstraint(self::REFID, Pap_Db_Table_CachedBanners::USERID, new Pap_Db_CachedBanner());
  
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_RawClicks::USERID, new Pap_Db_RawClick());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Clicks::USERID, new Pap_Db_Click());
  
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Impressions::USERID, new Pap_Db_Impression());
  
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CpmCommissions::USERID, new Pap_Db_CpmCommission());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Channels::USER_ID, new Pap_Db_Channel());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_DirectLinkUrls::USER_ID, new Pap_Db_DirectLinkUrl());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_LifetimeCommissions::USER_ID, new Pap_Db_LifetimeCommission());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Payouts::USER_ID, new Pap_Db_Payout());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Transactions::USER_ID, new Pap_Db_Transaction());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_UserInCommissionGroup::USER_ID, new Pap_Db_UserInCommissionGroup());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_UserPayoutOptions::USERID, new Pap_Db_UserPayoutOption());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_VisitorAffiliates::USERID, new Pap_Db_VisitorAffiliate());
  
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.UsersTable.constraints', $this);
      }
  }
  
  class Pap_Db_User_SpecialInit extends Pap_Db_User {
  
      private $table;
  
      function __construct(Gpf_DbEngine_Table $table){
          $this->table = $table;
          parent::__construct();
      }
  
      function init() {
          $this->setTable($this->table);
          Gpf_DbEngine_Row::init();
      }
  }
  

} //end Pap_Db_Table_Users

if (!class_exists('Gpf_Db_User', false)) {
  class Gpf_Db_User extends Gpf_DbEngine_Row {
      const APPROVED = 'A';
      const PENDING  = 'P';
      const DECLINED = 'D';
  
      function __construct(){
          parent::__construct();
      }
  
      function init() {
          $this->setTable(Gpf_Db_Table_Users::getInstance());
          parent::init();
      }
      
      public function getId() {
          return $this->get(Gpf_Db_Table_Users::ID);
      }
      
      public function getAuthId() {
          return $this->get(Gpf_Db_Table_Users::AUTHID);
      }
      
      public function setAccountId($accountId) {
          $this->set(Gpf_Db_Table_Users::ACCOUNTID, $accountId);
      }
      
      public function getAccountId() {
          return $this->get(Gpf_Db_Table_Accounts::ID);
      }
      
      public function setId($id) {
          $this->set(Gpf_Db_Table_Users::ID, $id);
      }
      
      public function setRoleId($roleId) {
          $this->set(Gpf_Db_Table_Users::ROLEID, $roleId);
      }
      
      public function getRoleId() {
          return $this->get(Gpf_Db_Table_Users::ROLEID);
      }
      
      public function setStatus($newStatus) {
          $this->set(Gpf_Db_Table_Users::STATUS, $newStatus);
      }
      
      public function getStatus() {
          return $this->get(Gpf_Db_Table_Users::STATUS);
      }
      
      public function setAuthId($authId) {
          $this->set(Gpf_Db_Table_Users::AUTHID, $authId);
      }
      
      public function loadByRoleType($roleType, $application) {
          $query = new Gpf_SqlBuilder_SelectBuilder();
          $query->select->addAll(Gpf_Db_Table_Users::getInstance(), 'u');
          $query->from->add(Gpf_Db_Table_Users::getName(), "u");
          $query->from->addInnerJoin(Gpf_Db_Table_Roles::getName(), "r", "r.roleid = u.roleid");
          $query->from->addInnerJoin(Gpf_Db_Table_Accounts::getName(), "a", "u.accountid = a.accountid");
          $query->where->add('u.authid', '=', $this->getAuthId());
          $query->where->add('u.accountid', '=', $this->getAccountId());
          $query->where->add('a.application', '=', $application);
          $query->where->add('r.roletype', '=', $roleType);
          $record = $query->getOneRow();
          $this->fillFromRecord($record);
      }
      
      public function isStatusValid() {
          $status = $this->getStatus();
          return in_array($status, array(self::APPROVED, self::PENDING, self::DECLINED));
      }
      
      protected function beforeSaveCheck() {
      	parent::beforeSaveCheck();
          if(!$this->isStatusValid()) {
              throw new Gpf_Exception('User status is invalid.');
          }
      }
  }
  

} //end Gpf_Db_User

if (!class_exists('Gpf_DbEngine_DeleteConstraint', false)) {
  abstract class Gpf_DbEngine_DeleteConstraint extends Gpf_Object {
  
      /**
       * @var array of string
       */
      protected $selfColumns;
      /**
       * @var array of string
       */
      protected $foreignColumns;
      /**
       * @var Gpf_DbEngine_Row
       */
      protected $foreignDbRow;
      
      function __construct($selfColumns, $foreignColumns, Gpf_DbEngine_Row $foreignDbRow) {
          if (is_array($selfColumns)) {
              $this->selfColumns = $selfColumns;            
          } else {
              $this->selfColumns = array($selfColumns);
          }
          if (is_array($foreignColumns)) {
              $this->foreignColumns = $foreignColumns;            
          } else {
              $this->foreignColumns = array($foreignColumns);
          }
          if (count($this->selfColumns) != count($this->foreignColumns)) {
              throw new Gpf_Exception("selfColumns count and foreignColumnsCount must be equal when creating DeleteConstraint");
          }
          $this->foreignDbRow = $foreignDbRow;
      }
      
      abstract public function execute(Gpf_DbEngine_Row $dbRow);
      
  }
} //end Gpf_DbEngine_DeleteConstraint

if (!class_exists('Gpf_DbEngine_CascadeDeleteConstraint', false)) {
  class Gpf_DbEngine_CascadeDeleteConstraint extends Gpf_DbEngine_DeleteConstraint {
     
      public function execute(Gpf_DbEngine_Row $dbRow) {
          if (count($this->foreignDbRow->getTable()->getDeleteConstraints()) == 0) {
              $deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
              $deleteBuilder->from->add($this->foreignDbRow->getTable()->name());
              for ($i=0; $i<count($this->selfColumns); $i++) {
                  $deleteBuilder->where->add($this->foreignColumns[$i], "=",
                                             $dbRow->get($this->selfColumns[$i]));
              }
              $deleteBuilder->execute(); 
              return;
          }
          for ($i=0; $i<count($this->selfColumns); $i++) {
              $this->foreignDbRow->set($this->foreignColumns[$i],
                                       $dbRow->get($this->selfColumns[$i]));
          }
          $rowCollection = $this->foreignDbRow->loadCollection($this->foreignColumns);
          foreach ($rowCollection as $row) {
              $row->delete();
          }
      }
      
  }

} //end Gpf_DbEngine_CascadeDeleteConstraint

if (!class_exists('Gpf_Db_Table_Users', false)) {
  class Gpf_Db_Table_Users extends Gpf_DbEngine_Table {
  
      /**
       * @deprecated use const ID instead
       */
      public static $ID = 'accountuserid';
      /**
       * @deprecated use const ACCOUNTID instead
       */
      public static $ACCOUNTID = 'accountid';
      
      const ID = 'accountuserid';
      const AUTHID = 'authid';
      const ACCOUNTID = 'accountid';
      const ROLEID = 'roleid';
      const STATUS = 'rstatus';
      
      private static $instance;
          
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
          
      protected function initName() {
          $this->setName('g_users');
      }
      
      public static function getName() {
          return self::getInstance()->name();
      }
      
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, 'char', 8, true);
          $this->createColumn(self::AUTHID, 'char', 100);
          $this->createColumn(self::ACCOUNTID, 'char', 20);
          $this->createColumn(self::ROLEID, 'char', 20);
          $this->createColumn(self::STATUS, 'char', 1);
      }
      
      protected function initConstraints() {
         $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID, new Gpf_Db_UserAttribute());
         $this->addDeleteConstraint(new Gpf_Db_Table_Users_AuthUsersDeleteConstraint(self::AUTHID, Gpf_Db_Table_AuthUsers::ID, new Gpf_Db_AuthUser()));
         
         $this->addConstraint(new Gpf_Db_Table_Constraints_UsersUniqueConstraint());
      }
  }
  
  class Gpf_Db_Table_Users_AuthUsersDeleteConstraint extends Gpf_DbEngine_CascadeDeleteConstraint {
     
      public function execute(Gpf_DbEngine_Row $dbRow) {
          if (!$this->isLastUserWithAuthID($dbRow->get(Gpf_Db_Table_Users::AUTHID))) {
              return;
          }
          parent::execute($dbRow);
      } 
         
      /**
       * @param $authId
       * @return boolean
       */
      private function isLastUserWithAuthID($authId) {
          $guser = new Gpf_Db_User();
          $guser->setAuthId($authId);
          try {
              $guser->loadFromData(array(Gpf_Db_Table_Users::AUTHID));
          } catch (Gpf_Exception $e) {
              return false;
          } 
          return true;
      }
  }

} //end Gpf_Db_Table_Users

if (!class_exists('Gpf_Db_AuthUser', false)) {
  class Gpf_Db_AuthUser extends Gpf_DbEngine_Row {
      const AUTH_TOKEN_LENGTH = 32;
  
      function __construct() {
          parent::__construct();
      }
  
      function init() {
          $this->setTable(Gpf_Db_Table_AuthUsers::getInstance());
          parent::init();
      }
  
      public function loadFromUsername() {
          $this->loadFromData(array('username'));
      }
  
      public function insert() {
          $password = $this->get(Gpf_Db_Table_AuthUsers::PASSWORD);
          if(strlen(trim($password)) == 0) {
              $this->set(Gpf_Db_Table_AuthUsers::PASSWORD, Gpf_Common_String::generatePassword(8));
          }
          $this->generateAuthToken();
          $this->setIp(Gpf_Http::getRemoteIp());
  
          parent::insert();
      }
  
      public function update($updateColumns = array()) {
          Gpf_Plugins_Engine::extensionPoint('Gpf_AuthUser.onUpdate', $this);
          $this->generateAuthToken();
          parent::update($updateColumns);
      }
  
      private function generateAuthToken() {
          $authToken = substr(md5($this->getUsername() . $this->getPassword() . time()).uniqid('', true), 0, self::AUTH_TOKEN_LENGTH);
          $this->set('authtoken', $authToken);
      }
  
      public function getId() {
          return $this->get(Gpf_Db_Table_AuthUsers::ID);
      }
  
      public function setId($id) {
          $this->set(Gpf_Db_Table_AuthUsers::ID, $id);
      }
  
      public function setUsername($username) {
          $this->set(Gpf_Db_Table_AuthUsers::USERNAME, $username);
      }
  
      public function setNotificationEmail($email) {
          $this->set(Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL, $email);
      }
  
      public function getUsername() {
          return $this->get(Gpf_Db_Table_AuthUsers::USERNAME);
      }
  
      public function getPassword() {
          return $this->get(Gpf_Db_Table_AuthUsers::PASSWORD);
      }
  
      public function setPassword($password) {
          $this->set(Gpf_Db_Table_AuthUsers::PASSWORD, $password);
      }
  
      public function setFirstName($firstName) {
          $this->set(Gpf_Db_Table_AuthUsers::FIRSTNAME, $firstName);
      }
  
      public function getFirstName() {
          return $this->get(Gpf_Db_Table_AuthUsers::FIRSTNAME);
      }
  
      public function setLastName($lastName) {
          $this->set(Gpf_Db_Table_AuthUsers::LASTNAME, $lastName);
      }
  
      public function getLastName() {
          return $this->get(Gpf_Db_Table_AuthUsers::LASTNAME);
      }
  
      public function setIp($ip) {
          $this->set(Gpf_Db_Table_AuthUsers::IP, $ip);
      }
  
      public function getIp() {
          return $this->get(Gpf_Db_Table_AuthUsers::IP);
      }
  
      public function updateIp($ip) {
          $this->setIp($ip);
          return parent::update(array(Gpf_Db_Table_AuthUsers::IP));
      }
  
      public function getEmail() {
          if ($this->getNotificationEmail() !== null && $this->getNotificationEmail() !== '') {
              return $this->getNotificationEmail();
          }
          return $this->getUsername();
      }
  
      public function setEmail($email) {
          if ($this->getNotificationEmail() !== null && $this->getNotificationEmail() !== '') {
              $this->setNotificationEmail($email);
          }
          $this->setUsername($email);
      }
  
      /**
       * @return boolean
       */
      public function isUserNameUnique() {
      	try {
      		$authUser = $this->createUserWithUserName();
      		$authUser->loadFromUsername();
      	} catch (Gpf_DbEngine_NoRowException $e) {
      		return true;
      	} catch (Gpf_DbEngine_TooManyRowsException $e) {
      	}
      	return false;
      }
  
      /**
       * @return Gpf_Db_AuthUser
       */
      protected function createUserWithUserName() {
      	$authUser = new Gpf_Db_AuthUser();
      	$authUser->setUsername($this->getUsername());
      	return $authUser;
      }
  
      public function getNotificationEmail() {
          return $this->get(Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL);
      }
  }

} //end Gpf_Db_AuthUser

if (!class_exists('Gpf_Db_Table_AuthUsers', false)) {
  class Gpf_Db_Table_AuthUsers extends Gpf_DbEngine_Table {
  
      const ID = 'authid';
      const USERNAME = 'username';
      const PASSWORD = 'rpassword';
      const FIRSTNAME = 'firstname';
      const LASTNAME = 'lastname';
      const AUTHTOKEN = 'authtoken';
      const NOTIFICATION_EMAIL = 'notificationemail';
      const IP = 'ip';
  
      private static $instance;
  
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
  
      protected function initName() {
          $this->setName('g_authusers');
      }
  
      public static function getName() {
          return self::getInstance()->name();
      }
  
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
          $this->createColumn(self::USERNAME, self::CHAR, 60);
          $this->createColumn(self::PASSWORD, self::CHAR, 60);
          $this->createColumn(self::FIRSTNAME, self::CHAR, 100);
          $this->createColumn(self::LASTNAME, self::CHAR, 100);
          $this->createColumn(self::AUTHTOKEN, self::CHAR, 100);
          $this->createColumn(self::NOTIFICATION_EMAIL, self::CHAR, 80);
          $this->createColumn(self::IP, self::CHAR, 40);
      }
  
      protected function initConstraints() {
          $this->addCascadeDeleteConstraint(self::ID, Gpf_Db_Table_PasswordRequests::AUTHUSERID, new Gpf_Db_PasswordRequest());
          Gpf_Plugins_Engine::extensionPoint('AuthUsers.initConstraints', $this);
          $this->addConstraint(new Gpf_DbEngine_Row_PasswordConstraint(self::PASSWORD));        
          $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::USERNAME)));
      }
  }
  

} //end Gpf_Db_Table_AuthUsers

if (!class_exists('Gpf_DbEngine_Row_MissingFieldException', false)) {
  class Gpf_DbEngine_Row_MissingFieldException extends Gpf_Exception {
      public function __construct($fieldCode, $class) {
          parent::__construct("Invalid field (column) ".$fieldCode." in class ".$class);
      }
      
      protected function logException() {
      }
  }
  

} //end Gpf_DbEngine_Row_MissingFieldException

if (!class_exists('Pap_Db_Table_Banners', false)) {
  class Pap_Db_Table_Banners extends Gpf_DbEngine_Table {
      const ID = 'bannerid';
      const ACCOUNT_ID = 'accountid';
      const CAMPAIGN_ID = 'campaignid';
      const WRAPPER_ID = 'wrapperid';
      const TYPE = 'rtype';
      const STATUS = 'rstatus';
      const NAME = 'name';
      const DESTINATION_URL = 'destinationurl';
      const DATEINSERTED = 'dateinserted';
      const SIZE = 'size';
      const DATA1 = 'data1';
      const DATA2 = 'data2';
      const DATA3 = 'data3';
      const DATA4 = 'data4';
      const DATA5 = 'data5';
      const DATA = 'data';
      const ORDER = 'rorder';
      const DESCRIPTION = 'description';
      const SEOSTRING = 'seostring';
  
      private static $instance;
  
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
  
      protected function initName() {
          $this->setName('pap_banners');
      }
  
      public static function getName() {
          return self::getInstance()->name();
      }
  
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, 'char', 8, true);
          $this->createColumn(self::ACCOUNT_ID, 'char', 8);
          $this->createColumn(self::CAMPAIGN_ID, 'char', 8);
          $this->createColumn(self::WRAPPER_ID, 'char', 8);
          $this->createColumn(self::TYPE, 'char', 1);
          $this->createColumn(self::STATUS, 'char', 1);
          $this->createColumn(self::NAME, 'char', 100);
          $this->createColumn(self::DESTINATION_URL, 'char', 1000);
          $this->createColumn(self::DATEINSERTED, 'datetime', 0);
          $this->createColumn(self::SIZE, 'char', 50);
          $this->createColumn(self::DATA.'1', 'text');
          $this->createColumn(self::DATA.'2', 'text');
          $this->createColumn(self::DATA.'3', 'text');
          $this->createColumn(self::DATA.'4', 'text');
          $this->createColumn(self::DATA.'5', 'text');
          $this->createColumn(self::DATA.'6', 'text');
          $this->createColumn(self::DATA.'7', 'text');
          $this->createColumn(self::DATA.'8', 'text');
          $this->createColumn(self::DATA.'9', 'text');
          $this->createColumn(self::ORDER, 'int', 0);
          $this->createColumn(self::DESCRIPTION, 'text');
          $this->createColumn(self::SEOSTRING, 'text');
      }
  
      protected function initConstraints() {
  		$this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_DirectLinkUrls::BANNER_ID, new Pap_Db_DirectLinkUrl());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CpmCommissions::BANNERID, new Pap_Db_CpmCommission());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID, new Pap_Db_BannerInRotator());
           
          $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_RawClicks::BANNERID, new Pap_Db_RawClick());
          $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_Clicks::BANNERID, new Pap_Db_Click());
           
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Impressions::BANNERID, new Pap_Db_Impression());
           
          $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_DirectLinkUrls::BANNER_ID, new Pap_Db_DirectLinkUrl());
          $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_Transactions::BANNER_ID, new Pap_Db_Transaction());
           
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID, new Pap_Db_BannerInRotator());
          
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CachedBanners::BANNERID, new Pap_Db_CachedBanner());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CachedBanners::PARENTBANNERID, new Pap_Db_CachedBanner());
      }
  
  
      /**
       * checks if banner name is unique
       *
       * @return unknown
       */
      public function checkUniqueName($name, $bannerId, $accountId) {
          $result = new Gpf_Data_RecordSet('id');
  
          $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
          $selectBuilder->select->add(self::ID, 'bannerid');
          $selectBuilder->select->add('name', 'name');
          $selectBuilder->from->add(self::getName());
          $selectBuilder->where->add('name', '=', $name);
          $selectBuilder->where->add('accountid', '=', $accountId);
          if($bannerId != '') {
              $selectBuilder->where->add('bannerid', '<>', $bannerId);
          }
           
          $result->load($selectBuilder);
          return $result;
      }
  }

} //end Pap_Db_Table_Banners

if (!class_exists('Pap_Db_Banner', false)) {
  class Pap_Db_Banner extends Gpf_DbEngine_Row {
      const SIZE_NOTAPPLICABLE = 'U';
      const SIZE_OWN = 'O';
      const SIZE_PREDEFINED = 'P';
  
      const STATUS_ACTIVE = 'A';
      const STATUS_HIDDEN = 'H';
  
      protected $width;
      protected $height;
  
      protected function init() {
          $this->setTable(Pap_Db_Table_Banners::getInstance());
          parent::init();
      }
  
      public function setDateInserted($dateInserted) {
          $this->set(Pap_Db_Table_Banners::DATEINSERTED, $dateInserted);
      }
      
      public function setAccountId($value) {
          $this->set(Pap_Db_Table_Banners::ACCOUNT_ID, $value);
      }
      
      public function getAccountId() {
          return $this->get(Pap_Db_Table_Banners::ACCOUNT_ID);
      }
      
      public function getDestinationUrl() {
          return $this->get(Pap_Db_Table_Banners::DESTINATION_URL);
      }
  
      public function getId() {
          return $this->get(Pap_Db_Table_Banners::ID);
      }
  
      public function setId($id) {
          $this->set(Pap_Db_Table_Banners::ID, $id);
      }
  
      public function getCampaignId() {
          return $this->get(Pap_Db_Table_Banners::CAMPAIGN_ID);
      }
  
      public function setName($name) {
          $this->set(Pap_Db_Table_Banners::NAME, $name);
      }
  
      public function getName() {
          return $this->get(Pap_Db_Table_Banners::NAME);
      }
  
      public function getBannerType() {
          return $this->get(Pap_Db_Table_Banners::TYPE);
      }
  
      public function getSeoString() {
          return $this->get(Pap_Db_Table_Banners::SEOSTRING);
      }
      
      public function setBannerType($type) {
          $this->set(Pap_Db_Table_Banners::TYPE, $type);
      }
  
      public function setCampaignId($value) {
          $this->set(Pap_Db_Table_Banners::CAMPAIGN_ID, $value);
      }
  
      public function setStatus($value) {
          $this->set(Pap_Db_Table_Banners::STATUS, $value);
      }
  
      public function setDestinationUrl($value) {
          $this->set(Pap_Db_Table_Banners::DESTINATION_URL, $value);
      }
  
      public function setSize($value) {
          $this->set(Pap_Db_Table_Banners::SIZE, $value);
      }
  
      public function setData1($value) {
          $this->set(Pap_Db_Table_Banners::DATA1, $value);
      }
  
      public function setData2($value) {
          $this->set(Pap_Db_Table_Banners::DATA2, $value);
      }
  
      public function setData3($value) {
          $this->set(Pap_Db_Table_Banners::DATA3, $value);
      }
  
      public function setData4($value) {
          $this->set(Pap_Db_Table_Banners::DATA4, $value);
      }
  
      public function setData5($value) {
          $this->set(Pap_Db_Table_Banners::DATA5, $value);
      }
  
      public function setData($num, $value) {
          $this->set(Pap_Db_Table_Banners::DATA.$num, $value);
      }
  
      public function getStatus() {
          return $this->get(Pap_Db_Table_Banners::STATUS);
      }
  
      public function getData1() {
          return $this->get(Pap_Db_Table_Banners::DATA1);
      }
  
      public function getData2() {
          return $this->get(Pap_Db_Table_Banners::DATA2);
      }
  
      public function getData3() {
          return $this->get(Pap_Db_Table_Banners::DATA3);
      }
  
      public function getData4() {
          return $this->get(Pap_Db_Table_Banners::DATA4);
      }
  
      public function getData5() {
          return $this->get(Pap_Db_Table_Banners::DATA5);
      }
  
      public function getData($num){
          return $this->get(Pap_Db_Table_Banners::DATA.$num);
      }
  
      public function setWrapperId($id){
          $this->set(Pap_Db_Table_Banners::WRAPPER_ID, $id);
      }
      
      public function getWrapperId(){
          return $this->get(Pap_Db_Table_Banners::WRAPPER_ID);
      }
  
      /**
       * @param Pap_Common_User $user
       * @return string
       */
      protected function getDescription(Pap_Common_User $user) {
          $description = $this->get(Pap_Db_Table_Banners::DATA2);
  
          $userFields = Pap_Common_UserFields::getInstance();
          $userFields->setUser($user);
          $description = $userFields->replaceUserConstantsInText($description);
  
          return $description;
      }
  
      public function getSizeType($sizeColumnName) {
          $sizeField = $this->get($sizeColumnName);
          if ($sizeField == '') {
              return self::SIZE_NOTAPPLICABLE;
          } else {
              return substr($sizeField, 0, 1);
          }
      }
  
      public function getWidth() {
          $this->decodeWidthAndHeight();
          return $this->width;
      }
  
      public function getHeight() {
          $this->decodeWidthAndHeight();
          return $this->height;
      }
  
      private function decodeWidthAndHeight() {
          if($this->width !== null){
              return;
          }
          if($this->isSizeDefined()) {
              $sizeField = $this->get(Pap_Db_Table_Banners::SIZE);
              $sizeArray = explode('x', substr($sizeField, 1));
              if(count($sizeArray) == 2) {
                  $this->width = $sizeArray[0];
                  $this->height = $sizeArray[1];
              }
          } else {
              $this->setUndefinedSize();
          }
      }
  
      public function isSizeDefined(){
          return $this->getSizeType(Pap_Db_Table_Banners::SIZE) !== self::SIZE_NOTAPPLICABLE;
      }
  
      protected function setUndefinedSize(){
          $this->width = '';
          $this->height = '';
      }
  
      public function delete() {
          if (Gpf_Application::isDemo() && Gpf_Application::isDemoEntryId($this->getId())) {
              throw new Gpf_Exception("Demo banner can not be deleted");
          }
          return parent::delete();
      }
      
      public function update($updateColumns = array()) {
          parent::update($updateColumns);
          Pap_Db_Table_CachedBanners::deleteCachedBannersForBanner($this->getId());
      }
      
      protected function beforeSaveAction() {
          parent::beforeSaveAction();
          if ($this->getCampaignId() != '') {
              $this->setAccountId($this->resolveAccountId($this->getCampaignId()));
          }
      }
  
      /**
       * @throws Gpf_Exception
       */
      protected function resolveAccountId($campaignId) {
          $campaign = new Pap_Db_Campaign();
          $campaign->setId($campaignId);
          try {
              $campaign->load();
              return $campaign->getAccountId();
          } catch (Gpf_DbEngine_NoRowException $e) {
              throw new Gpf_Exception("Can not resolve accountId for campaign '$campaignId' in Pap_Db_Banner::resolveAccountId()");
          }
      }
  }
  

} //end Pap_Db_Banner

if (!class_exists('Pap_Common_Banner', false)) {
  class Pap_Common_Banner extends Pap_Db_Banner {
      const BANNER_PREVIEW_HEIGHT = '50';
      const BANNER_PREVIEW_HEIGHT_ACTUAL_SIZE = '100%';
      const HTML_AMP = '&amp;';
      const FLAG_MERCHANT_PREVIEW = 1;
      const FLAG_AFFILIATE_PREVIEW = 4;
      const FLAG_DIRECTLINK = 2;
      const FLAG_RAW_CODE = 8;
  
      /**
       * @var Pap_Common_Banner_Rotator
       */
      private $rotator = null;
      /**
       * @var Pap_Db_Channel
       */
      protected $channel = null;
  
      private $dynamicLink = null;
  
      private $parentBannerId = null;
  
      /**
       * @var Pap_Common_Banner
       */
      private $parentBanner = null;
      protected $viewInActualSize;
  
      function __construct() {
          parent::__construct();
      }
  
      public function setChannel(Pap_Db_Channel $channel) {
          $this->channel = $channel;
      }
  
      public function fillForm(Gpf_Rpc_Form $form) {
          $form->load($this);
      }
  
      /**
       * stores width x height to the size field.
       *
       * @param Gpf_Rpc_Form $form
       * @param String $sizeFieldName
       */
      public function encodeSize(Gpf_Rpc_Form $form, $sizeFieldName) {
          if($form->getFieldValue($sizeFieldName) == Pap_Db_Banner::SIZE_PREDEFINED) {
              $form->setField($sizeFieldName, $form->getFieldValue($sizeFieldName).$form->getFieldValue('size_predefined'));
          }
          if($form->getFieldValue($sizeFieldName) == Pap_Db_Banner::SIZE_OWN) {
              $form->setField($sizeFieldName, $form->getFieldValue($sizeFieldName).$form->getFieldValue('size_width').'x'.$form->getFieldValue('size_height'));
          }
      }
  
      protected function getBannerCode(Pap_Common_User $user, $flags) {
      }
  
      /**
       * @return String
       */
      public function getParentBannerId() {
          return $this->parentBannerId;
      }
  
      /**
       * @throws Gpf_DbEngine_NoRowException
       * @return Pap_Common_Banner
       */
      public function getParentBanner() {
          if($this->parentBanner == null && $this->parentBannerId != null){
              $factory  = new Pap_Common_Banner_Factory();
              $this->parentBanner = $factory->getBanner($this->parentBannerId);
          }
          return $this->parentBanner;
      }
  
      /*
       *@param String $bannerId
       */
      function setParentBannerId($bannerId){
          $this->parentBannerId = $bannerId;
      }
       
      /**
       * Used by hower banner to display banner or in affiliate panel to get banner code
       *
       * @param Pap_Common_User $user
       * @return string
       */
      public function getCode(Pap_Common_User $user, $flags = '') {
          return $this->getCompleteCode($user, $flags);
      }
  
      public function getDynamicLinkCode(Pap_Common_User $user, $dynamicLink) {
          $this->setDynamicLink($dynamicLink);
          return $this->getCompleteCode($user, Pap_Tracking_ClickTracker::LINKMETHOD_REDIRECT);
      }
  
      public function getPreview(Pap_Common_User $user) {
          $flag = self::FLAG_MERCHANT_PREVIEW;
          if(Gpf_Session::getAuthUser()->isAffiliate()) {
              $flag = self::FLAG_AFFILIATE_PREVIEW;
          }
          return $this->getBannerCode($user, $flag);
      }
  
      public function getDisplayCode(Pap_Common_User $user) {
          $flags = '';
          if($this->getDynamicLink() != '') {
              $flags = Pap_Tracking_ClickTracker::LINKMETHOD_REDIRECT;
          }
          return $this->getBannerCode($user, $flags);
      }
  
      public function fillCachedBanner(Pap_Db_CachedBanner $cachedBanner, Pap_Common_User $user) {
          $cachedBanner->setHeaders('');
          $cachedBanner->setDynamicLink($this->getDynamicLink());
          $cachedBanner->setCode($this->getDisplayCode($user, ''));
      }
  
      public function getDirectLinkCode(Pap_Common_User $user) {
          return $this->getCompleteCode($user, self::FLAG_DIRECTLINK);
      }
  
      public function getCompleteCode(Pap_Common_User $user, $flags){
          $code = $this->getBannerCode($user, $flags);
          $id = $this->getWrapperId();
          if($this->getWrapperId() !== null && $this->getWrapperId() !== ''){
              $wrapperservice = new Pap_Merchants_Config_BannerWrapperService();
              $code = $wrapperservice->getBannerInWrapper($code, $this, $user);
          }
          return $code;
      }
  
      public function initValidators(Gpf_Rpc_Form $form) {
          $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::NAME, $this->_('name'));
          $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::STATUS, $this->_('status'));
          $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::SIZE, $this->_('size'));
          $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::DESTINATION_URL, $this->_('destination url'));
      }
  
      /**
       * Replaces width and height in banner format
       *
       * @param string $format
       * @param boolean $isPreview
       * @return string
       */
      protected function replaceWidthHeightConstants($format, $flags) {
          
          if($this->viewInActualSize == Gpf::YES) {
              $format = Pap_Common_UserFields::replaceCustomConstantInText('height', Pap_Common_Banner::BANNER_PREVIEW_HEIGHT_ACTUAL_SIZE, $format);
              $format = Pap_Common_UserFields::replaceCustomConstantInText('width', '', $format);
              return self::cleanIncompleteCode($format);
          }
          
          if($this->getWidth() > 0 && $this->getHeight() > 0) {
              if(($flags & self::FLAG_MERCHANT_PREVIEW)&&($this->getHeight() > Pap_Common_Banner::BANNER_PREVIEW_HEIGHT)) {
                  $ratio = $this->getWidth()/$this->getHeight();
                  $newHeight = Pap_Common_Banner::BANNER_PREVIEW_HEIGHT;
                  $newWidth = $ratio*$newHeight;
              } else {
                  $newHeight = $this->getHeight();
                  $newWidth = $this->getWidth();
              }
              $format = Pap_Common_UserFields::replaceCustomConstantInText('width', $newWidth, $format);
              $format = Pap_Common_UserFields::replaceCustomConstantInText('height', $newHeight, $format);
          } else {
              if ($flags & self::FLAG_MERCHANT_PREVIEW) {
                  $format = Pap_Common_UserFields::replaceCustomConstantInText('height', Pap_Common_Banner::BANNER_PREVIEW_HEIGHT, $format);
              } else {
                  $format = Pap_Common_UserFields::replaceCustomConstantInText('height', '', $format);
              }
              $format = Pap_Common_UserFields::replaceCustomConstantInText('width', '', $format);
          }
          return self::cleanIncompleteCode($format);
      }
  
      public static function cleanIncompleteCode($code){
          $code = str_replace(array('width=""', "width=''", 'height=""', "height=''"), '', $code);
          return $code;
      }
  
      public function replaceBannerConstants($text, Pap_Common_User $user) {
          $text = str_replace('{$bannerid}', $this->getId(), $text);
          $valueContext = new Gpf_Plugins_ValueContext($text);
          $valueContext->setArray(array('bannerType' => $this->getBannerType(), 'user' => $user));
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Banner.replaceBannerConstants', $valueContext);
          return $valueContext->get();
      }
  
      /**
       * Replaces Url constants: targeturl, targeturl_encoded, impression_track
       *
       * @return string
       */
      public function replaceUrlConstants($text, Pap_Common_User $user = null, $flags, $destinationUrl) {
          $clickUrl = $this->getClickUrl($user, $destinationUrl, $flags);
          $impressionTrack = $this->getImpressionTrackingCode($user, $flags);
  
          $clickUrlEncoded = $this->urlEncodeClickUrl($clickUrl);
  
          $text = Pap_Common_UserFields::replaceCustomConstantInText('targeturl', $clickUrl, $text);
          $text = Pap_Common_UserFields::replaceCustomConstantInText('targeturl_encoded', $clickUrlEncoded, $text);
          $text = Pap_Common_UserFields::replaceCustomConstantInText('impression_track', $impressionTrack, $text);
  
          $context = new Pap_Common_BannerReplaceVariablesContext($text, $this, $user);
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Banner.replaceUrlConstants', $context);
          $text = $context->getText();
  
          return $text;
      }
  
      private function urlEncodeClickUrl($clickUrl) {
          return urlencode(str_replace('&amp;', '&', $clickUrl));
      }
  
      /**
       * Replaces user constants like username, firstname, ... data25
       *
       * @return string
       */
      public function replaceUserConstants($text, $user, $mainFields = null) {
          $userFields = Pap_Common_UserFields::getInstance();
          $userFields->setUser($user);
  
          $text = $userFields->replaceUserConstantsInText($text, $mainFields);
          $text = Pap_Common_UserFields::removeCommentsInText($text);
  
          return $text;
      }
  
      /**
       * Removes user constants like username, firstname, ... data25
       *
       * @return string
       */
      public function removeUserConstants($text, $mainFields = null) {
          $userFields = Pap_Common_UserFields::getInstance();
  
          $text = $userFields->removeUserConstantsInText($text, $mainFields);
          $text = Pap_Common_UserFields::removeCommentsInText($text);
  
          return $text;
      }
  
      /**
       * Replaces user constants like username, firstname, ... data25
       *
       * @return string
       */
      public function replaceClickConstants($text, $clickFieldsValues) {
          foreach($clickFieldsValues as $code => $value) {
              $text = Pap_Common_UserFields::replaceCustomConstantInText($code, $value, $text);
          }
          $text = Pap_Common_UserFields::removeCommentsInText($text);
          return $text;
      }
  
      /**
       * @param Pap_Common_User $user
       * @param string $specialDesturl
       * @return String click URL
       */
      public function getClickUrl(Pap_Common_User $user, $specialDesturl = '', $flags = '') {
          if($flags & Pap_Common_Banner::FLAG_MERCHANT_PREVIEW) {
              if($specialDesturl == '') {
                  return $this->getDestinationUrl($user);
              }
              return $specialDesturl;
          }
  
          return Pap_Tracking_ClickTracker::getInstance()->getClickUrl($this, $user, $specialDesturl, $flags, $this->channel);
      }
  
      /**
       * @param Pap_Common_User $user
       * @return String impression tracking code
       */
      public function getImpressionTrackingCode(Pap_Common_User $user, $flags) {
          if($flags & Pap_Common_Banner::FLAG_MERCHANT_PREVIEW || $flags & Pap_Common_Banner::FLAG_AFFILIATE_PREVIEW) {
              return '';
          }
          return Pap_Tracking_ImpressionTracker::getInstance()->getImpressionTrackingCode($this, $user, $this->channel);
      }
  
      public function setDynamicLink($dynamicLink = null) {
          $this->dynamicLink = $dynamicLink;
      }
  
      public function getDynamicLink() {
          return $this->dynamicLink;
      }
  
      public function getBannerScriptUrl(Pap_Common_User $user) {
          return Pap_Tracking_BannerViewer::getBannerScriptUrl($user->getRefId(), $this->getId(), $this->getChannelId(), $this->getParentBannerId());
      }
  
      protected function getChannelId(){
          if($this->channel != null){
              return $this->channel->getValue();
          }
          return null;
      }
  
      /**
       * @return Pap_Db_Channel
       */
      public function getChannel() {
          return $this->channel;
      }
  
      public function getDestinationUrl($user = null) {
          if ($user === null) {
              return parent::getDestinationUrl();
          }
          return $this->replaceUserConstants(parent::getDestinationUrl(), $user);
      }
      
      public function setViewInActualSize($actualSize) {
          $this->viewInActualSize = $actualSize;
      }
  }

} //end Pap_Common_Banner

if (!class_exists('Pap_Common_Banner_Image', false)) {
  class Pap_Common_Banner_Image extends Pap_Common_Banner {
  
      protected function getBannerCode(Pap_Common_User $user, $flags) {
          $imageUrl = $this->getImageUrl();
          if((@$_SERVER['HTTPS'] == 'on') && (strpos($imageUrl, 'https') === false)){
              $imageUrl = str_ireplace('http', 'https', $imageUrl);
          }
          $description = $this->getDescription($user);
          $format = $this->getBannerFormat();
  
          $format = Pap_Common_UserFields::replaceCustomConstantInText('image_src', $imageUrl, $format);
          $format = Pap_Common_UserFields::replaceCustomConstantInText('image_name', basename($imageUrl), $format);
          $format = Pap_Common_UserFields::replaceCustomConstantInText('alt', $description, $format);
          $format = Pap_Common_UserFields::replaceCustomConstantInText(Pap_Db_Table_Banners::SEOSTRING, $this->getSeoString(), $format);
  
          $format = $this->replaceUrlConstants($format, $user, $flags, '');
          $format = $this->replaceUserConstants($format, $user);
          $format = $this->replaceWidthHeightConstants($format, $flags);
  
          return $format;
      }
  
      public function getImageUrl() {
          return $this->getData(1);
      }
  
      public static function getBannerFormat() {
          return Gpf_Settings::get(Pap_Settings::IMAGE_BANNER_FORMAT_SETTING_NAME);
      }
  
      protected function setDetectedSize($size){
          $this->setData(4, $size);
      }
  
      protected function getDetectedSize(){
          return $this->getData(4);
      }
  
      protected function beforeSaveAction() {
          $this->detectImageSize();
      }
  
      private function detectImageSize(){
          $image = $this->encodeImageUrlForGetImageSize($this->getImageUrl());
          if (($size = @getimagesize($image)) !== false) {
              $this->setDetectedSize($size[0].'x'.$size[1]);
          } else {
              $this->setDetectedSize(Gpf_DbEngine_Row::NULL);
          }
      }
      
      /**
       * @return String
       */
      protected function encodeImageUrlForGetImageSize($url) {
      	$url = urldecode($url);
          $url = str_replace(' ', '%20', $url);
          return $url;
      }
  
      protected function setUndefinedSize(){
          if($this->getDetectedSize() != null){
              $size = explode('x',$this->getDetectedSize());
              $this->width = $size[0];
              $this->height = $size[1];
          }
      }
  }
  

} //end Pap_Common_Banner_Image

if (!interface_exists('Pap_Tracking_Common_Recognizer', false)) {
  interface Pap_Tracking_Common_Recognizer {
  	public function recognize(Pap_Contexts_Tracking $context);
  }
  

} //end Pap_Tracking_Common_Recognizer

if (!class_exists('Pap_Tracking_Common_RecognizeChannel', false)) {
  abstract class Pap_Tracking_Common_RecognizeChannel extends Gpf_Object implements Pap_Tracking_Common_Recognizer {
  
      private $channelsCache = array();
  
      public function __construct() {
      }
  
      public final function recognize(Pap_Contexts_Tracking $context) {
          $context->debug('Recognizing channel started');
  
          $channel = $this->recognizeChannels($context);
  
          if($channel != null) {
              $context->setChannelObject($channel);
          } else {
              $context->debug('No channel recognized!');
          }
           
          $context->debug('Recognizing channel ended');
      }
  
      /**
       * @return Pap_Db_Channel
       */
      protected abstract function recognizeChannels(Pap_Contexts_Tracking $context);
  
      /**
       * gets channel by channel id
       * @param $channelId
       * @return Pap_Db_Channel
       * @throws Gpf_Exception
       */
      public function getChannelById(Pap_Contexts_Tracking $context, $channelId) {
          if($channelId == '') {
              $this->logAndThrow($context, 'Channel id is empty');
          }
          $user = $context->getUserObject();
          if ($user == null) {
              $this->logAndThrow($context, 'User is not recognized. Channel can not be found');
          }
  
          if (isset($this->channelsCache[$channelId])) {
              return $this->channelsCache[$channelId];
          }
  
          $channel = new Pap_Db_Channel();
          $channel->setPrimaryKeyValue($channelId);
          $channel->setPapUserId($user->getId());
          try {
              $channel->loadFromData(array(Pap_Db_Table_Channels::ID, Pap_Db_Table_Channels::USER_ID));
              $context->debug('Channel found: '.$channel->getName());
              $this->channelsCache[$channelId] = $channel;
              return $channel;
          } catch (Gpf_DbEngine_NoRowException $e) {
              $channel->setValue($channelId);
              $channel->loadFromData(array(Pap_Db_Table_Channels::VALUE, Pap_Db_Table_Channels::USER_ID));
              $this->channelsCache[$channelId] = $channel;
              return $channel;
          }
      }
  
      /**
       * @param $message
       * @throws Pap_Tracking_Exception
       */
      protected function logAndThrow(Pap_Contexts_Tracking $context, $message) {
          $context->debug($message);
          throw new Pap_Tracking_Exception($message);
      }
  }
  

} //end Pap_Tracking_Common_RecognizeChannel

if (!class_exists('Pap_Tracking_Impression_RecognizeChannel', false)) {
  class Pap_Tracking_Impression_RecognizeChannel extends Pap_Tracking_Common_RecognizeChannel implements Pap_Tracking_Common_Recognizer {
  
      /**
       * @return Pap_Db_Channel
       */
      protected function recognizeChannels(Pap_Contexts_Tracking $context) {
          try {
              return $this->getChannelFromParameter($context);
          } catch (Gpf_Exception $e) {
          }
      }
  
      /**
       * @return Pap_Db_Channel
       * @throws Gpf_Exception
       */
      private function getChannelFromParameter(Pap_Contexts_Impression $context) {
          $context->debug('Trying to get channel from parameter');
          return $this->getChannelById($context, $context->getRequestObject()->getChannelId());
      }
  }
  

} //end Pap_Tracking_Impression_RecognizeChannel

if (!class_exists('Pap_Tracking_Exception', false)) {
  class Pap_Tracking_Exception extends Gpf_Exception {
  
  	function __construct($message) {
  		parent::__construct($message);
  	}
  }
  

} //end Pap_Tracking_Exception

if (!class_exists('Pap_Db_ClickImpression', false)) {
  abstract class Pap_Db_ClickImpression extends Gpf_DbEngine_Row {
  	
  	const STATUS_RAW = 'R';
  	const STATUS_UNIQUE = 'U';
  	const STATUS_DECLINED = 'D';
  	
      protected function init() {
          $this->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
          $this->setBannerId('');
          $this->setCampaignId('');
          $this->setUserId('');
          $this->setParentBannerId('');
          $this->setCountryCode('');
          $this->setChannel('');
          $this->setData1('');
          $this->setData2('');
          parent::init();
      }
      
      public function setRaw($value) {
          $this->set(Pap_Db_Table_ClicksImpressions::RAW, $value);
      }
  
      public function setUnique($value) {
          $this->set(Pap_Db_Table_ClicksImpressions::UNIQUE, $value);
      }
      
      public function getRaw() {
          return $this->get(Pap_Db_Table_ClicksImpressions::RAW);
      }
  
      public function getUnique() {
          return $this->get(Pap_Db_Table_ClicksImpressions::UNIQUE);
      }
  
      public function setTime($time) {
          $this->set(Pap_Stats_Table::DATEINSERTED, $time);
      }
          
      public function getTime() {
          return $this->get(Pap_Stats_Table::DATEINSERTED);
      }
  
      public function setAccountId($id) {
          $this->set(Pap_Stats_Table::ACCOUNTID, $id);
      }
  
      public function getAccountId() {
          return $this->get(Pap_Stats_Table::ACCOUNTID);
      }
  
      public function getUserId() {
          return $this->get(Pap_Stats_Table::USERID);
      }
      
      public function setUserId($id) {
          $this->set(Pap_Stats_Table::USERID, $id);
      }
      
      public function getCampaignId() {
          return $this->get(Pap_Stats_Table::CAMPAIGNID);
      }
  
      public function setCampaignId($id) {
          $this->set(Pap_Stats_Table::CAMPAIGNID, $id);
      }
      
      public function getBannerId() {
          return $this->get(Pap_Stats_Table::BANNERID);
      }
  
      public function setBannerId($id) {
          $this->set(Pap_Stats_Table::BANNERID, $id);
      }
  
      public function setParentBannerId($id) {
          $this->set(Pap_Stats_Table::PARENTBANNERID, $id);
      }
  
      public function setCountryCode($code) {
          $this->set(Pap_Stats_Table::COUNTRYCODE, $code);
      }
      
      public function getCountryCode() {
          return $this->get(Pap_Stats_Table::COUNTRYCODE);
      }
  
      public function setData1($value) {
          $this->set(Pap_Stats_Table::CDATA1, $value);
      }
  
      public function setData2($value) {
          $this->set(Pap_Stats_Table::CDATA2, $value);
      }
      
      public function setChannel($value) {
          $this->set(Pap_Stats_Table::CHANNEL, $value);
      }
  
      public function addRaw() {
          $this->addRawCount(1);
      }
  
      public function addRawCount($count) {
          $this->setRaw($this->getRaw()+$count);
      }
          
      public function addUnique() {
          $this->addUniqueCount(1);
      }
      
      public function addUniqueCount($count) {
          $this->setUnique($this->getUnique()+$count);
      }
      
      public function mergeWith(Pap_Db_ClickImpression $clickImpression) {
          $this->addRawCount($clickImpression->getRaw());
          $this->addUniqueCount($clickImpression->getUnique());
      }
  }

} //end Pap_Db_ClickImpression

if (!class_exists('Pap_Db_Impression', false)) {
  class Pap_Db_Impression extends Pap_Db_ClickImpression {
  
      protected function init() {
          $this->setTable(Pap_Db_Table_Impressions::getInstance());
          parent::init();
      }
  }
  

} //end Pap_Db_Impression

if (!interface_exists('Pap_Stats_Table', false)) {
  interface Pap_Stats_Table {
      const ACCOUNTID = 'accountid';
      const USERID = 'userid';
      const CAMPAIGNID = 'campaignid';
      const BANNERID = 'bannerid';
      const PARENTBANNERID = 'parentbannerid';
      const COUNTRYCODE = 'countrycode';
      const CDATA1 = 'cdata1';
      const CDATA2 = 'cdata2';
      const CHANNEL = 'channel';
      const DATEINSERTED = 'dateinserted';
      const DATEAPPROVED = 'dateapproved';
      const ORDERID = 'orderid';
      
      public function name();
      
      /**
       * @return Gpf_SqlBuilder_SelectBuilder
       */
      public function getStatsSelect(Pap_Stats_Params $statParams, $groupColumn, $groupColumnAlias);
  }

} //end Pap_Stats_Table

if (!class_exists('Pap_Db_Table_ClicksImpressions', false)) {
  abstract class Pap_Db_Table_ClicksImpressions extends Gpf_DbEngine_Table implements Pap_Stats_Table {
      const RAW = "raw";
      const UNIQUE = "uniq";
  
      protected function initColumns() {
          $this->createColumn(Pap_Stats_Table::ACCOUNTID, self::CHAR, 8);
          $this->createColumn(Pap_Stats_Table::USERID, self::CHAR, 8);
          $this->createColumn(Pap_Stats_Table::CAMPAIGNID, self::CHAR, 8);
          $this->createColumn(Pap_Stats_Table::BANNERID, self::CHAR, 8);
          $this->createColumn(Pap_Stats_Table::PARENTBANNERID, self::CHAR, 8);
          $this->createColumn(Pap_Stats_Table::COUNTRYCODE, self::CHAR, 2);
          $this->createColumn(Pap_Stats_Table::CDATA1, self::CHAR, 255);
          $this->createColumn(Pap_Stats_Table::CDATA2, self::CHAR, 255);
          $this->createColumn(Pap_Stats_Table::CHANNEL, self::CHAR, 10);
          $this->createColumn(Pap_Stats_Table::DATEINSERTED, self::DATETIME);
          $this->createColumn(self::RAW, self::INT);
          $this->createColumn(self::UNIQUE, self::INT);
      }
      
      /**
       * @return Gpf_SqlBuilder_UnionBuilder
       */
      public function getStatsSelect(Pap_Stats_Params $statParams, $groupColumn, $groupColumnAlias) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->select->add($groupColumn, $groupColumnAlias);
          $this->initStatsSelect($select->select);
          $select->from->add($this->name());
          $statParams->addTo($select);
          $select->groupBy->add($groupColumn);
  
          $unionBuilder = new Gpf_SqlBuilder_UnionBuilder();
          $unionBuilder->addSelect($select);
          $statsSelectContext = new Pap_Stats_StatsSelectContext($unionBuilder, $select, $groupColumn, $groupColumnAlias);
  
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Db_Table_ClicksImpressions.getStatsSelect', $statsSelectContext);
          return $unionBuilder;
      }
      
      protected function initStatsSelect(Gpf_SqlBuilder_SelectClause  $select) {
          $select->add('sum('.self::RAW.')', self::RAW);
          $select->add('sum('.self::UNIQUE.')', self::UNIQUE);
      }
  }
  

} //end Pap_Db_Table_ClicksImpressions

if (!class_exists('Pap_Db_Table_Impressions', false)) {
  class Pap_Db_Table_Impressions extends Pap_Db_Table_ClicksImpressions {
  	const ID = "impressionid";
  	
      private static $instance;
          
      /**
       * @return Pap_Db_Table_Impressions
       */
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
          
      protected function initName() {
          $this->setName('pap_impressions');
      }
      
      public static function getName() {
          return self::getInstance()->name();
      }
      
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, self::INT);
          parent::initColumns();
      }
  }
  

} //end Pap_Db_Table_Impressions

if (!class_exists('Gpf_DateTime', false)) {
  class Gpf_DateTime extends Gpf_Object {
      
      // 1.1.1970 00:00:00
      const MIN_TIMESTAMP = 0;
      // 31.12.2030 14:59:59
      const MAX_TIMESTAMP = 1924988399;
      
      private $timestamp;
      /**
       * @var boolean
       */
      private $serverTime;
  
      /**
       * Creates date object
       *
       * @param $time null, timestamp or datetime in text format. if no parameter is specified current time is used
       */
      public function __construct($time = null, $serverTime = true) {
           $this->init($time);
           $this->serverTime = $serverTime;
      }
      
      public static function daysToSeconds($days) {
          return $days*24*60*60;
      }
      
      public static function hoursToSeconds($hours) {
          return $hours*60*60;
      }
      
      public static function minutesToSeconds($minutes) {
          return $minutes*60;
      }
      
      public static function weeksToSeconds($weeks) {
          return $weeks*7*24*60*60;
      }
      
      public static function monthsToSeconds($months) {
          return $months*30*24*60*60;
      }
      
      public static function yearsToSeconds($years) {
          return $years*365*24*60*60;
      }
  
      public static function secondsToHours($seconds) {
          return $seconds/60/60;
      }
      
      /**
       * @return Gpf_DateTime
       */
      public function makeClone() {
          return new Gpf_DateTime($this->timestamp, $this->serverTime);
      }
  
      /**
       * @return Gpf_DateTime
       */
      public function getServerTime() {
          if ($this->serverTime) {
              return $this;
          }
          return new Gpf_DateTime(Gpf_Common_DateUtils::getServerTime($this->timestamp), true);
      }
          
      /**
       * @return Gpf_DateTime
       */
      public function getClientTime() {
          if ($this->serverTime) {
              return new Gpf_DateTime(Gpf_Common_DateUtils::getClientTime($this->timestamp), false);
          }
          return $this;
      }
      
      public function toTimeStamp() {
          return $this->timestamp;
      }
      
      /**
       * @return date string in system format
       */
      public function toDate() {
          return Gpf_Common_DateUtils::getOnlyDatePart($this->toDateTime());
      }
      
      /**
       * @return date in locale format
       */
      public function toLocaleDate() {
          return Gpf_Common_DateUtils::getDateInLocaleFormat($this->timestamp);
      }
      
      public function format($format) {
          return date($format, $this->timestamp);
      }
      
      /**
       * @return datetime string in system format
       */
      public function toDateTime() {
          return Gpf_Common_DateUtils::getDateTime($this->timestamp);
      }
  
      /**
       * @return Gpf_DateTime start of the month
       */
      public function getMonthStart() {
          return new Gpf_DateTime(mktime(0, 0, 0, $this->getMonth(), 1, $this->getYear()), $this->serverTime);
      }
      
      /**
       * @return Gpf_DateTime end of the month
       */
      public function getMonthEnd() {
          return new Gpf_DateTime(mktime(23, 59, 59,
                                     $this->getMonth(),
                                     Gpf_Common_DateUtils::getDaysInMonth($this->getMonth(), $this->getYear()),
                                     $this->getYear()), $this->serverTime);
      }
      
      /**
       * @return Gpf_DateTime start of the week
       */
      public function getWeekStart() {
          return new Gpf_DateTime(mktime(0, 0, 0,
                                  $this->getMonth(),
                                  $this->getDay() - date('w', $this->timestamp),
                                  $this->getYear()), $this->serverTime);
      }
      
      /**
       * @return Gpf_DateTime end of the week
       */
      public function getWeekEnd() {
          return new Gpf_DateTime(mktime(23, 59, 59,
                                  $this->getMonth(),
                                  $this->getDay() - date('w', $this->timestamp) + 6,
                                  $this->getYear()), $this->serverTime);
      }
      
      /**
       * @return Gpf_DateTime start of the day
       */
      public function getDayStart() {
          return new Gpf_DateTime(mktime(0, 0, 0,
                                  $this->getMonth(),
                                  $this->getDay(),
                                  $this->getYear()), $this->serverTime);
      }
      
      /**
       * @return Gpf_DateTime start of the day
       */
      public function getHourStart() {
          return new Gpf_DateTime(mktime($this->getHour(), 0, 0,
                                  $this->getMonth(),
                                  $this->getDay(),
                                  $this->getYear()), $this->serverTime);
      }
      
      /**
       * @return Gpf_DateTime end of the day
       */
      public function getDayEnd() {
          return new Gpf_DateTime(mktime(23, 59, 59,
                                  $this->getMonth(),
                                  $this->getDay(),
                                  $this->getYear()), $this->serverTime);
      }
      
      /**
       * @param $date
       * @return 0 if times are equal, -1 if $this is before $date, 1 if $this is after $date
       */
      public function compare(Gpf_DateTime $date) {
          if ($this->timestamp == $date->timestamp) {
              return 0;
          }
          if ($this->timestamp < $date->timestamp) {
              return -1;
          }
          return 1;
      }
      
      /**
       * @return year (numeric 4 digits)
       */
      public function getYear() {
          return date('Y', $this->timestamp);
      }
      
      /**
       * @return month (numeric with leading zeros)
       */
      public function getMonth() {
          return date('m', $this->timestamp);
      }
      
      /**
       * @return week (numeric with leading zeros)
       */
      public function getWeek() {
          return date('W', $this->timestamp);
      }
      
      /**
       * @return day (numeric with leading zeros)
       */
      public function getDay() {
          return date('d', $this->timestamp);
      }
      
      /**
       * @return hour (numeric with leading zeros)
       */
      public function getHour() {
          return date('H', $this->timestamp);
      }
      
      /**
       * @return minute (numeric with leading zeros)
       */
      public function getMinute() {
          return date('i', $this->timestamp);
      }
      
      /**
       * @return second (numeric with leading zeros)
       */
      public function getSecond() {
          return date('s', $this->timestamp);
      }
      
      public function checkTimestamp($timestamp) {
          if ($timestamp < self::MIN_TIMESTAMP) {
              $this->timestamp = self::MIN_TIMESTAMP;
              return false;
          }
          
          if ($timestamp > self::MAX_TIMESTAMP) {
              $this->timestamp = self::MAX_TIMESTAMP;
              return false;
          }
          return true;
      }
      
      public function addDay($count = 1) {
          if (!$this->checkTimestamp($this->toTimeStamp() + self::daysToSeconds($count))) {
              return;
          }
          $this->timestamp = $this->maketime($this->getMonth(), $this->getDay()+$count, $this->getYear());
      }
      
      public function addSecond($count = 1) {
          if (!$this->checkTimestamp($this->toTimeStamp() + $count)) {
              return;
          }
          $this->timestamp = mktime($this->getHour(), $this->getMinute(), $this->getSecond()+$count, $this->getMonth(), $this->getDay(), $this->getYear());
      }
      
      public function addMinute($count = 1) {
          if (!$this->checkTimestamp($this->toTimeStamp() + self::minutesToSeconds($count))) {
              return;
          }
          $this->timestamp = mktime($this->getHour(), $this->getMinute()+$count, $this->getSecond(), $this->getMonth(), $this->getDay(), $this->getYear());
      }
      
      public function addHour($count = 1) {
          if (!$this->checkTimestamp($this->toTimeStamp() + self::hoursToSeconds($count))) {
              return;
          }
          $this->timestamp = mktime($this->getHour()+$count, $this->getMinute(), $this->getSecond(), $this->getMonth(), $this->getDay(), $this->getYear());
      }
      
      public function addWeek($count = 1) {
          if (!$this->checkTimestamp($this->toTimeStamp() + self::weeksToSeconds($count))) {
              return;
          }
          $this->timestamp = $this->maketime($this->getMonth(), $this->getDay()+$count*7, $this->getYear());
      }
      
      public function addMonth($count = 1) {
          if (!$this->checkTimestamp($this->toTimeStamp() + self::monthsToSeconds($count))) {
              return;
          }
          $month = $this->getMonth()+$count;
          $day = Gpf_Common_DateUtils::daysInMonth($month, $this->getDay(), $this->getYear());
          $this->timestamp = $this->maketime($month, $day, $this->getYear());
      }
      
      /**
       *
       * @return Gpf_DateTime
       */
      public static function min() {
          return new Gpf_DateTime(self::MIN_TIMESTAMP);
      }
      
      private function maketime($month, $day, $year) {
          return mktime($this->getHour(), $this->getMinute(), $this->getSecond(), $month, $day, $year);
      }
      
      private function init($time = null) {
          if ($time === null) {
              $this->timestamp = time();
              return;
          }
          if (is_int($time)) {
              $this->timestamp = $time;
              return;
          }
          $this->timestamp = Gpf_Common_DateUtils::getTimestamp($time);
      }
  }

} //end Gpf_DateTime
/*
VERSION
65183fd602e36062c0c827e08545721c
*/
?>
