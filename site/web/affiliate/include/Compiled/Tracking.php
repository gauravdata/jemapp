<?php
/** *   @copyright Copyright (c) 2008-2009 Quality Unit s.r.o. *   @author Quality Unit *   @package Core classes *   @since Version 1.0.0 *    *   Licensed under the Quality Unit, s.r.o. Dual License Agreement, *   Version 1.0 (the "License"); you may not use this file except in compliance *   with the License. You may obtain a copy of the License at *   http://www.qualityunit.com/licenses/gpf *    */

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

if (!class_exists('Gpf_Db_Account', false)) {
  abstract class Gpf_Db_Account extends Gpf_DbEngine_Row {
      const DEFAULT_ACCOUNT_ID = 'default1';
      const APPROVED = 'A';
      const PENDING = 'P';
      const SUSPENDED = 'S';
      const DECLINED = 'D';
  
      private $password;
      private $firstname;
      private $lastname;
  
      function __construct(){
          parent::__construct();
          $this->setApplication(Gpf_Application::getInstance()->getCode());
          $date = new Gpf_DateTime();
          $this->setDateinserted($date->toDateTime());
      }
  
      function init() {
          $this->setTable(Gpf_Db_Table_Accounts::getInstance());
          parent::init();
      }
  
      function setId($id) {
          $this->set(Gpf_Db_Table_Accounts::ID, $id);
      }
  
      public function setDefaultId() {
          $this->setId(self::DEFAULT_ACCOUNT_ID);
      }
  
      public function getId() {
          return $this->get(Gpf_Db_Table_Accounts::ID);
      }
  
      public function setDateinserted($dateInserted) {
          $this->set(Gpf_Db_Table_Accounts::DATEINSERTED, $dateInserted);
      }
  
      public function getDateinserted() {
          return $this->get(Gpf_Db_Table_Accounts::DATEINSERTED);
      }
  
      /**
       *
       * @return Gpf_Install_CreateAccountTask
       */
      public function getCreateTask() {
          $task = new Gpf_Install_CreateAccountTask();
          $task->setAccount($this);
          return $task;
      }
  
      /**
       *
       * @return Gpf_Install_UpdateAccountTask
       */
      public function getUpdateTask() {
          $task = new Gpf_Install_UpdateAccountTask();
          $task->setAccount($this);
          return $task;
      }
  
      public function createTestAccount($email, $password, $firstName, $lastName) {
          $this->setDefaultId();
          $this->setEmail($email);
          $this->setPassword($password);
          $this->setFirstname($firstName);
          $this->setLastname($lastName);
          $this->getCreateTask()->run(Gpf_Tasks_LongTask::NO_INTERRUPT);
      }
  
      public function getEmail() {
          return $this->get(Gpf_Db_Table_Accounts::EMAIL);
      }
  
      public function getPassword() {
          return $this->password;
      }
  
      public function getStatus() {
          return $this->get(Gpf_Db_Table_Accounts::STATUS);
      }
  
      public function setPassword($password) {
          $this->password = $password;
      }
  
      public function setFirstname($name) {
          $this->firstname = $name;
          $this->setName($this->firstname . ' ' . $this->lastname);
      }
  
      public function setLastname($name) {
          $this->lastname = $name;
          $this->setName($this->firstname . ' ' . $this->lastname);
      }
  
      public function getFirstname() {
          return $this->firstname;
      }
  
      public function getLastname() {
          return $this->lastname;
      }
  
      public function  setName($name) {
          $this->set(Gpf_Db_Table_Accounts::NAME, $name);
      }
  
      public function  setEmail($email) {
          $this->set(Gpf_Db_Table_Accounts::EMAIL, $email);
      }
  
      public function setStatus($newStatus) {
          $this->set(Gpf_Db_Table_Accounts::STATUS, $newStatus);
      }
  
      public function setApplication($application) {
          $this->set(Gpf_Db_Table_Accounts::APPLICATION, $application);
      }
  
      public function getApplication() {
          return $this->get(Gpf_Db_Table_Accounts::APPLICATION);
      }
  
      public function getName() {
          return $this->get(Gpf_Db_Table_Accounts::NAME);
      }
  
      public function setAccountNote($accountNote) {
          $this->set(Gpf_Db_Table_Accounts::ACCOUNT_NOTE, $accountNote);
      }
  
      public function getAccountNote() {
          return $this->get(Gpf_Db_Table_Accounts::ACCOUNT_NOTE);
      }
  
      public function setSystemNote($systemNote) {
          $this->set(Gpf_Db_Table_Accounts::SYSTEM_NOTE, $systemNote);
      }
  
      public function getStystemNote() {
          return $this->get(Gpf_Db_Table_Accounts::SYSTEM_NOTE);
      }
  }

} //end Gpf_Db_Account

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

if (!class_exists('Pap_Db_Visit', false)) {
  class Pap_Db_Visit extends Gpf_DbEngine_Row {
  
      const UNPROCESSED = 'U';
      const PROCESSED = 'P';
      const INPROCESSING = 'I';
      const INCRONPROCESSING = 'C';
      
      private $index;
  
      private $newVisitor = true;
      
      private $countryCode = "";
  
      function __construct($index = 0){
          $this->index = $index;
          parent::__construct();
      }
      
      public function setCountryCode($countryCode) {
      	$this->countryCode = $countryCode;
      }
      
      public function getCountryCode() {
      	return $this->countryCode;
      }
      
      public function setVisitorIdHash($hash) {
          $this->set(Pap_Db_Table_Visits::VISITORID_HASH, $hash);
      }
      
      public function getVisitorIdHash() {
          return $this->get(Pap_Db_Table_Visits::VISITORID_HASH);
      }
  
      public function setNewVisitor($value) {
          $this->newVisitor = $value;
      }
  
      public function isNewVisitor() {
          return $this->newVisitor;
      }
  
      function init() {
          $this->setTable(Pap_Db_Table_Visits::getInstance($this->index));
          parent::init();
      }
  
      public function setAccountId($value) {
          $this->set(Pap_Db_Table_Visits::ACCOUNTID, $value);
      }
      
      public function getVisitorId() {
          return $this->get(Pap_Db_Table_Visits::VISITORID);
      }
  
      public function setVisitorId($value) {
          $this->set(Pap_Db_Table_Visits::VISITORID, $value);
      }
  
      public function getSaleParams() {
      	return $this->get(Pap_Db_Table_Visits::SALE_PARAMS);
      }
  
      public function getGetParams() {
          return $this->get(Pap_Db_Table_Visits::GET_PARAMS);
      }
  
      public function getUrl() {
          return $this->get(Pap_Db_Table_Visits::URL);
      }
  
      public function getReferrerUrl() {
          return Pap_Tracking_Request::decodeRefererUrl($this->get(Pap_Db_Table_Visits::REFERRERURL));
      }
  
      public function getDateVisit() {
      	return $this->get(Pap_Db_Table_Visits::DATEVISIT);
      }
  
      public function getAnchor() {
          return $this->get(Pap_Db_Table_Visits::ANCHOR);
      }
  
      public function getIp() {
          return $this->get(Pap_Db_Table_Visits::IP);
      }
  
      public function getCookies() {
          return $this->get(Pap_Db_Table_Visits::COOKIES);
      }
  
      public function setCookies($value) {
          $this->set(Pap_Db_Table_Visits::COOKIES, $value);
      }
  
      public function setDateVisit($value) {
          $this->set(Pap_Db_Table_Visits::DATEVISIT, $value);
      }
  
      public function setIp($value) {
          $this->set(Pap_Db_Table_Visits::IP, $value);
      }
  
      public function setSaleParams($value) {
          $this->set(Pap_Db_Table_Visits::SALE_PARAMS, $value);
      }
  
      public function setUserAgent($value) {
          $this->set(Pap_Db_Table_Visits::USER_AGENT, $value);
      }
  
      public function setGetParams($value) {
      	$this->set(Pap_Db_Table_Visits::GET_PARAMS, $value);
      }
      
      public function setReferrerUrl($value) {
      	$this->set(Pap_Db_Table_Visits::REFERRERURL, $value);
      }
      
      public function setUrl($value) {
          $this->set(Pap_Db_Table_Visits::URL, $value);
      }
      
      public function setAnchor($value) {
          $this->set(Pap_Db_Table_Visits::ANCHOR, $value);
      }
      
      public function getUserAgent() {
      	return $this->get(Pap_Db_Table_Visits::USER_AGENT);
      }
      
      public function getTrackMethod() {
      	return $this->get(Pap_Db_Table_Visits::TRACKMETHOD);
      }
      
      public function setTrackMethod($value) {
          $this->set(Pap_Db_Table_Visits::TRACKMETHOD, $value);
      }
      
      public function getAccountId() {
      	return $this->get(Pap_Db_Table_Visits::ACCOUNTID);
      }
      
      public function setProcessed() {
          $this->set(Pap_Db_Table_Visits::RSTATUS, self::PROCESSED);
      }
  
      public function setInCronProcessing() {
          $this->set(Pap_Db_Table_Visits::RSTATUS, self::INCRONPROCESSING);
      }
  
      public function getId() {
          return $this->get(Pap_Db_Table_Visits::ID);
      }
  }
  
  

} //end Pap_Db_Visit

if (!class_exists('Pap_Db_Table_Visits', false)) {
  class Pap_Db_Table_Visits extends Gpf_DbEngine_Table {
      const ID = 'visitid';
      const ACCOUNTID = 'accountid';
      const RSTATUS = 'rstatus';
      const VISITORID = 'visitorid';
      const VISITORID_HASH = 'visitoridhash';
      const DATEVISIT = 'datevisit';
  	const TRACKMETHOD = 'trackmethod';
      const URL = 'url';
      const REFERRERURL = 'referrerurl';
      const GET_PARAMS = 'get';
      const ANCHOR = 'anchor';
      const SALE_PARAMS = 'sale';
  	const COOKIES = 'cookies';
      const IP = 'ip';
      const USER_AGENT = "useragent";
  
      private static $instance;
  
  	private $index;
  
  	public static function getInstance($index) {
  		if(@self::$instance[$index] === null) {
  			self::$instance[$index] = new self;
  			self::$instance[$index]->index = $index;
          }
  		return self::$instance[$index];
      }
  
      protected function initName() {
          $this->setName('pap_visits');
      }
      
      public function name() {
          return parent::name() . $this->index;
      }
  
      public static function getName($index) {
          return self::getInstance($index)->name();
      }
  
      protected function initColumns() {
  		$this->createPrimaryColumn(self::ID, self::INT);
  		$this->createColumn(self::ACCOUNTID, self::CHAR, 8);
  		$this->createColumn(self::RSTATUS, self::CHAR, 1);
  		$this->createColumn(self::VISITORID, self::CHAR, 36, true);
  		$this->createColumn(self::VISITORID_HASH, self::INT);
  		$this->createColumn(self::DATEVISIT, self::DATETIME);
  		$this->createColumn(self::TRACKMETHOD, self::CHAR, 1);
  		$this->createColumn(self::URL, self::CHAR);
  		$this->createColumn(self::REFERRERURL, self::CHAR);
  		$this->createColumn(self::GET_PARAMS, self::CHAR);
  		$this->createColumn(self::ANCHOR, self::CHAR);
  		$this->createColumn(self::SALE_PARAMS, self::CHAR);
  		$this->createColumn(self::COOKIES, self::CHAR);
  		$this->createColumn(self::IP, self::CHAR, 39);
  		$this->createColumn(self::USER_AGENT, self::CHAR);	
      }
  
  }

} //end Pap_Db_Table_Visits

if (!class_exists('Pap_Tracking_Visit_Processor', false)) {
  class Pap_Tracking_Visit_Processor extends Gpf_Tasks_LongTask {
  
      const PROGRESS_START = "start";
      const MAX_WORKERS_COUNT = 256;
  
      private $visitorCache = array();
  
      /**
       * @var Pap_Tracking_Visit_VisitorAffiliateCache
       */
      protected $visitorAffiliateCache;
  
      /**
       * @var array<Pap_Tracking_Common_VisitProcessor>
       */
      protected $visitProcessors;
  
      /**
       * @var Pap_Tracking_BackwardCompatibility_BackwardCompatibilityProcessor
       */
      private $backwardCompatibilityProcessor;
  
      public function __construct() {
          $this->visitorAffiliateCache = new Pap_Tracking_Visit_VisitorAffiliateCache();
          $this->visitProcessors = $this->createProcessors($this->visitorAffiliateCache);
          $this->setProgress(self::PROGRESS_START);
      }
  
      public static function getVisitorIdLength() {
          if (defined('VISITOR_ID_LENGTH')) {
              return VISITOR_ID_LENGTH;
          }
          return 32;
      }
  
      public function createWorker($workingRangeFrom, $workingRangeTo) {
          $task = new Pap_Tracking_Visit_Processor();
          $this->debug('Creating new worker Pap_Tracking_Visit_Processor for range:' . $workingRangeFrom . '-' . $workingRangeTo);
          $task->setWorkingArea($workingRangeFrom, $workingRangeTo);
          $task->insertTask();
      }
  
      protected function splitMe() {
          $this->debug('I can not split my self');
      }
  
      private function otherThanMyVisitsExists() {
          $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
          $selectBuilder->where->add(Pap_Db_Table_Visits::RSTATUS, '=', Pap_Db_Visit::UNPROCESSED);
          $selectBuilder->where->add(Pap_Db_Table_Visits::VISITORID_HASH, '>', 0);
          $count = $this->getTableRowsCount($selectBuilder, Pap_Db_Table_Visits::getName($this->computeProcessTable(Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT, true))));
          $this->debug('Visits that are for bigger hash than 0:' . $count);
          return $count > 0;
      }
  
      protected function createSlaves() {
          if (!$this->otherThanMyVisitsExists()) {
              $this->debug('No more slaves needed - skipping');
              return;
          }
          $this->task->setWorkingAreaFrom(0);
          $this->task->setWorkingAreaTo(0);
          $this->task->update();
          for ($a=1;$a<$this->getMaxWorkersCount(); $a++) {
              if (!$this->slaveExist($a,$a)) {
                  $this->createWorker($a, $a);
              }
          }
      }
  
      protected function getClassName() {
          return get_class();
      }
  
      protected function getAvaliableWorkersCount() {
          return self::MAX_WORKERS_COUNT - $this->getActualWorkersCount();
      }
  
      protected function getMaxWorkersCount() {
          return self::MAX_WORKERS_COUNT;
      }
  
      /**
       * @param $visitorAffiliateCache
       * @return array<Pap_Tracking_Common_VisitProcessor>
       */
      protected function createProcessors(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
          $visitProcessors = array();
          $visitProcessors[] = new Pap_Tracking_BackwardCompatibility_BackwardCompatibilityProcessor($visitorAffiliateCache);
          $visitProcessors[] = new Pap_Tracking_Click_ClickProcessor($visitorAffiliateCache);
          $visitProcessors[] = new Pap_Tracking_Action_ActionProcessor($visitorAffiliateCache);
          return $visitProcessors;
      }
  
      public function getName() {
          return $this->_('Visit log processor');
      }
  
      public function runOnline(Pap_Db_Visit $visit) {
          $this->processVisit($visit);
          $this->saveVisitChanges();
      }
  
      protected function interrupt($sleepSeconds = 0) {
          $this->saveVisitChanges();
          parent::interrupt($sleepSeconds);
      }
  
      protected function saveVisitChanges() {
          foreach ($this->visitProcessors as $visitHandler) {
              $visitHandler->saveChanges();
          }
          $this->visitorAffiliateCache->saveChanges();
      }
  
      protected function doMasterWorkWhenSyncPointReached() {
          $this->debug('master at sync!!!');
          if ($this->getProgress() == self::PROGRESS_START ) {
              $this->optimizeTable($this->computeProcessTable(Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT, true)));
              $this->switchTables();
          }
      }
  
      protected function doSlaveWorkAfterExecute() {
          $this->debug('Worker finished his work...');
          try {
              $this->saveVisitChanges();
          } catch (Gpf_Exception $e) {
              $this->debug('Error when saving visit changes: ' . $e->getMessage());
          }
          $this->setDone();
          $this->debug('Worker finished his work...2');
      }
  
      protected function doMasterWorkAfterExecute() {
          $this->setProgress(self::PROGRESS_START);
          $this->debug('interrupting...');
          $this->interrupt(0);
      }
  
      protected function doAfterLongTaskInterrupt() {
          //do nothing - we do not want to update our task after long task interrupt occurs
      }
  
      protected function doSlaveWorkWhenSyncPointReached() {
          $this->setDone();
          $this->forceFinishTask();
          $this->interrupt();
      }
  
      private function computeProcessTable($inputTableNumber) {
          return ($inputTableNumber + 2) % 3;
      }
  
      protected function execute() {
          $this->debug('Starting visit processor');
  
          $this->setProgress(0);
          $processedTableIndex = $this->computeProcessTable(Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT, true));
          $this->debug('Processing visits from ' . $processedTableIndex);
          $this->processAllVisits($processedTableIndex);
          $this->debug('Processing is over now');
      }
  
      protected function syncPointReached() {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->from->add(Pap_Db_Table_Visits::getName($this->computeProcessTable(Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT, true))));
          $select->select->add('count(*)', 'cnt');
          $select->where->add(Pap_Db_Table_Visits::RSTATUS, '=', Pap_Db_Visit::UNPROCESSED);
          $record = $select->getOneRow();
          if ($record->get('cnt') > 0) {
              return false;
          }
          return true;
      }
  
      protected function processAllVisits($processedTableIndex) {
          $progress = $this->getProgress();
          while (($visit = $this->getNextVisit($processedTableIndex)) !== false) {
              $this->setInCronProcessingStatus($visit);
              $this->processAndUpdateVisit($visit);
              $this->setProgress(++$progress);
              $this->checkInterruption();
          }
      }
  
      private function setInCronProcessingStatus(Pap_Db_Visit $visit) {
          $logger = Pap_Logger::create(Pap_Common_Constants::TYPE_ACTION);
          $logger->debug('Before visit processing - visitid: '.$visit->getId().' set status IN CRON PROCESSING');
          $visit->setInCronProcessing();
          $visit->update(array(Pap_Db_Table_Visits::RSTATUS));
      }
  
      protected function canBeSplit() {
          return $this->imMasterWorker(); 
      }
  
      protected function processAndUpdateVisit(Pap_Db_Visit $visit) {
          try {
              $this->processVisit($visit);
          } catch (Exception $e) {
              Gpf_Log::error("Visit processing failed ($e)");
          }
          $visit->delete();
      }
  
      /**
       * @throws Exception
       */
      protected function processVisit(Pap_Db_Visit $visit) {
          $visitorId = $visit->getVisitorId();
          if ($visit->getVisitorIdHash() >= self::MAX_WORKERS_COUNT) {
              $visit->setVisitorIdHash($visit->getVisitorIdHash() % self::MAX_WORKERS_COUNT);
          }
  
          $visit->setNewVisitor($this->isNewVisitor($visitorId));
  
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.PapTrackingVisitProcessor.processVisit', $visit);
  
          foreach ($this->visitProcessors as $visitHandler) {
              $visitHandler->process($visit);
          }
      }
  
      protected function isNewVisitor($visitorId) {
          if (isset($this->visitorCache[$visitorId])) {
              return false;
          }
          $this->visitorCache[$visitorId] = true;
  
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->from->add(Pap_Db_Table_VisitorAffiliates::getName());
          $select->select->add(Pap_Db_Table_VisitorAffiliates::VISITORID);
          $select->where->add(Pap_Db_Table_VisitorAffiliates::VISITORID, '=', $visitorId);
          $select->limit->set(0, 1);
          try {
              $select->getOneRow();
              return false;
          } catch (Gpf_DbEngine_NoRowException $e) {
              return true;
          }
      }
  
      protected function optimizeTable($processedTableIndex) {
          $this->debug('Optimizing table num. ' . $processedTableIndex);
          Pap_Db_Table_Visits::getInstance($processedTableIndex)->optimize();
      }
  
      /**
       * switching tables for writing and processing impressions
       * table states: I - visits are written to this table
       *               W - table is waiting to be processed
       *               P - table should be processed
       *
       * this method switches: I -> W, W -> P, P -> I
       */
      protected function switchTables() {
          $inputTableTo = (Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT, true)+2) % 3;
          $this->debug('Setting input from '.Gpf_Settings::get(Pap_Settings::VISITS_TABLE_INPUT, true).' to '. $inputTableTo . '.');
          Gpf_Settings::set(Pap_Settings::VISITS_TABLE_INPUT, $inputTableTo, true);
      }
  
      /**
       * @return Pap_Db_Visit
       */
      protected function getNextVisit($processedTableIndex) {
          $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
          $selectBuilder->from->add(Pap_Db_Table_Visits::getName($processedTableIndex));
          $selectBuilder->select->addAll(Pap_Db_Table_Visits::getInstance($processedTableIndex));
          $selectBuilder->where->add(Pap_Db_Table_Visits::RSTATUS, '=', Pap_Db_Visit::UNPROCESSED);
          $selectBuilder->where->add(Pap_Db_Table_Visits::VISITORID_HASH, '=', $this->task->getWorkingAreaFrom());
          $selectBuilder->limit->set(0, 1);
          $visit = new Pap_Db_Visit($processedTableIndex);
          try {
              $visit->fillFromSelect($selectBuilder);
              return $visit;
          } catch (Gpf_Exception $e) {
              return false;
          }
      }
  
      protected function loadTask() {
          $this->task->setClassName(get_class($this));
          $this->task->setNull(Gpf_Db_Table_Tasks::DATEFINISHED);
          $this->task->loadFromData();
      }
  
      protected function debug($message) {
          $message .= ' (WORKER_'.$this->task->getWorkingAreaFrom().'-'.$this->task->getWorkingAreaTo() . ')';
          parent::debug($message);
      }
  }

} //end Pap_Tracking_Visit_Processor

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

if (!class_exists('Pap_Tracking_Visit_VisitorAffiliateCache', false)) {
  class Pap_Tracking_Visit_VisitorAffiliateCache {
  	protected $visitorAffiliateCollections = array();
  
  	private $removeVisitorAffiliateIds = array();
  
  	private $accountId;
  
  	public function __construct() {
  		$this->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
  	}
  //TODO: rewrite this class to all public methods have accountid parameter and rmeove set/get account methods
  	public function setAccountId($accountId) {
  		$this->accountId = $accountId;
  		if (is_null($this->accountId) || $this->accountId === '') {
  			$this->accountId = Gpf_Db_Account::DEFAULT_ACCOUNT_ID;
  		}
  		if (!isset($this->visitorAffiliateCollections[$this->accountId])) {
  			$this->visitorAffiliateCollections[$this->accountId] = array();
  		}
  	}
  	
  	public function getAccountId() {
  		return $this->accountId;
  	}
  
  	/**
  	 * @return Pap_Tracking_Common_VisitorAffiliateCollection
  	 */
  	public static function sortVisitorAffiliatesByDateVisit(Pap_Tracking_Common_VisitorAffiliateCollection $visitorAffiliates) {
  		$visitorAffiliates->sort(array("Pap_Tracking_Visit_VisitorAffiliateCache", "compareTwoVisitorAffiliateByDateVisit"));
  	}
  
  	public static function compareTwoVisitorAffiliateByDateVisit(Pap_Db_VisitorAffiliate $a,
  	Pap_Db_VisitorAffiliate $b) {
  		if ($a->getDateVisit() == $b->getDateVisit()) {
  			return 0;
  		}
  		return ($a->getDateVisit() > $b->getDateVisit()) ? +1 : -1;
  	}
  
  	public function removeVisitorAffiliate($visitorAffiliateId) {
  		$this->removeVisitorAffiliateIds[] = $visitorAffiliateId;
  	}
  
  	public function getVisitorAffiliateCollections() {
  		return $this->visitorAffiliateCollections[$this->accountId];
  	}
  
  	/**
       * @return Pap_Tracking_Common_VisitorAffiliateCollection
       */
      public function getVisitorAffiliateAllRows($visitorId) {
          if ($visitorId == '') {
              throw new Gpf_Exception('visitorId can not be empty in Pap_Tracking_Visit_VisitorAffiliateCache::getVisitorAffiliateAllRows()');
          }
  
          if (!isset($this->visitorAffiliateCollections[$this->accountId][$visitorId])) {
              Gpf_Log::debug('VisitorAffiliate not found in cache, loading from DB');			
  			$this->visitorAffiliateCollections[$this->accountId][$visitorId] = $this->loadVisitorAffiliatesFromDb($visitorId);
  			Gpf_Log::debug('Saving collection to cache for visitorid=' . $visitorId . ', num rows=' . $this->visitorAffiliateCollections[$this->accountId][$visitorId]->getSize());
  		}		
  		return $this->visitorAffiliateCollections[$this->accountId][$visitorId];
      }
  
  	/**
  	 * @param Pap_Context_Tracking
  	 * @return Pap_Db_VisitorAffiliate
  	 */
  	public function getActualVisitorAffiliate($visitorId) {
  		foreach ($this->getVisitorAffiliateAllRows($visitorId) as $visitorAffiliate) {
  			if ($visitorAffiliate->isActual() && $visitorAffiliate->isValid()) {
  				return $visitorAffiliate;
  			}
  		}
  		return null;
  	}
  
  	/**
  	 * @param visitorId
  	 * @return Pap_Db_VisitorAffiliate
  	 */
  	public function createVisitorAffiliate($visitorId) {
  		$visitorAffiliate = new Pap_Db_VisitorAffiliate();
  		$visitorAffiliate->setVisitorId($visitorId);
  		$visitorAffiliate->setAccountId($this->accountId);
  		return $visitorAffiliate;
  	}
  
  	/**
  	 * @param $ip
  	 * @return Pap_Db_VisitorAffiliate
  	 */
  	public function getLatestVisitorAffiliateFromIp($ip, $accountId) {
  		$cacheVisitorAffiliate = $this->getLatestAffiliateFromCollectionByIp($this->getVisitorAffiliateCollections(), $ip);
  
  		$dbVisitorAffiliate = $this->getLatestVisitorAffiliateFromDbByIp($ip, $accountId);
  
  		if ($cacheVisitorAffiliate == null) {
  			return $dbVisitorAffiliate;
  		}
  
  		if ($dbVisitorAffiliate == null) {
  			return $cacheVisitorAffiliate;
  		}
  
  		if ($dbVisitorAffiliate->getDateVisit() <
  		$cacheVisitorAffiliate->getDateVisit()) {
  			return $cacheVisitorAffiliate;
  		}
  		return $dbVisitorAffiliate;
  	}
  
  	/**
  	 * @param $ip
  	 * @return Pap_Db_VisitorAffiliate
  	 */
  	protected function getLatestVisitorAffiliateFromDbByIp($ip, $accountId) {
  		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
  		$selectBuilder->from->add(Pap_Db_Table_VisitorAffiliates::getName());
  		$selectBuilder->select->addAll(Pap_Db_Table_VisitorAffiliates::getInstance());
  		$selectBuilder->where->add(Pap_Db_Table_VisitorAffiliates::IP, '=', $ip);
  		$selectBuilder->where->add(Pap_Db_Table_VisitorAffiliates::ACCOUNTID, '=', $accountId);
  		$selectBuilder->where->add(Pap_Db_Table_VisitorAffiliates::VALIDTO, '>=', Gpf_Common_DateUtils::now());
  		$selectBuilder->orderBy->add(Pap_Db_Table_VisitorAffiliates::DATEVISIT, false);
  		$selectBuilder->limit->set(0, 1);
  
  		try {
  			$visitorAffiliate = new Pap_Db_VisitorAffiliate();
  			$visitorAffiliate->fillFromRecord($selectBuilder->getOneRow());
  		} catch (Gpf_Exception $e) {
  			return null;
  		}
  		 
  		return $visitorAffiliate;
  	}
  
  
  	public function saveChanges() {
          Gpf_Log::debug('Saving visitor affiliate cache.');
  		foreach ($this->visitorAffiliateCollections as $accountVisitorAffiliates) {
  			foreach ($accountVisitorAffiliates as $visitorAffiliates) {
  				foreach ($visitorAffiliates as $visitorAffiliate) {
                      $visitorAffiliate->save();
  					Gpf_Log::debug('Saved visitor affiliate, visitorId: '. $visitorAffiliate->getVisitorId().', userId: ' . $visitorAffiliate->getUserId());
  				}
  			}
  		}
  
  		$this->deleteVisitorAffiliatesFromDb();
  	}
  
  	private function deleteVisitorAffiliatesFromDb() {
  		if (count($this->removeVisitorAffiliateIds) == 0) {
  			return;
  		}
  
  		$deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
  		$deleteBuilder->from->add(Pap_Db_Table_VisitorAffiliates::getName());
  		foreach ($this->removeVisitorAffiliateIds as $id) {
  			$deleteBuilder->where->add(Pap_Db_Table_VisitorAffiliates::ID, '=', $id, 'OR');
  		}
  		$deleteBuilder->execute();
  	}
  
  	/**
  	 * @return Pap_Tracking_Common_VisitorAffiliateCollection
  	 */
  	protected function loadVisitorAffiliatesFromDb($visitorId) {
  		$visitorAffiliates = $this->createVisitorAffiliate($visitorId);
  		return $visitorAffiliates->loadCollection();
  	}
  
  	protected function getLatestAffiliateFromCollectionByIp($collections, $ip) {
  		$latestVisitorAffiliate = null;
  
  		foreach ($collections as $visitorAffiliateCollection) {
  			foreach ($visitorAffiliateCollection as $visitorAffiliate) {
  				if ($visitorAffiliate->getIp() == $ip) {
  					if (($latestVisitorAffiliate == null ||
  					$latestVisitorAffiliate->getDateVisit() < $visitorAffiliate->getDateVisit()) && $visitorAffiliate->isValid()) {
  						$latestVisitorAffiliate = $visitorAffiliate;
  					}
  				}
  			}
  		}
  
  		return $latestVisitorAffiliate;
  	}
  }
  

} //end Pap_Tracking_Visit_VisitorAffiliateCache

if (!class_exists('Pap_Tracking_BackwardCompatibility_BackwardCompatibilityProcessor', false)) {
  class Pap_Tracking_BackwardCompatibility_BackwardCompatibilityProcessor extends Gpf_Object implements Pap_Tracking_Common_VisitProcessor {
       
      /**
       * @var Pap_Tracking_Cookie_ClickData
       */
      protected $firstClickCookie = null;
      /**
       * @var Pap_Tracking_Cookie_ClickData
       */
      protected $lastClickCookie = null;
      /**
       * @var Pap_Tracking_Cookie_Sale
       */
      protected $saleCookie = null;
  
      /**
       * @var Pap_Tracking_Visit_VisitorAffiliateCache
       */
      private $visitorAffiliateCache;
  	
      /**
       * @var Pap_Tracking_BackwardCompatibility_RecognizeAffiliate
       */
      protected $recognizeAffiliate;
  
      /**
       * @var Pap_Tracking_BackwardCompatibility_RecognizeCampaign
       */
      private $recognizeCampaign;
  
      /**
       * @var Pap_Tracking_Common_RecognizeCommGroup
       */
      private $recognizeCommGroup;
      
      /**
       * @var Pap_Contexts_BackwardCompatibility
       */
      private $context;
  
      public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
          $this->visitorAffiliateCache = $visitorAffiliateCache;
          $this->context = new Pap_Contexts_BackwardCompatibility();
          
          $this->recognizeAffiliate = new Pap_Tracking_BackwardCompatibility_RecognizeAffiliate();
          $this->recognizeCampaign = new Pap_Tracking_BackwardCompatibility_RecognizeCampaign();
          $this->recognizeCommGroup = new Pap_Tracking_Common_RecognizeCommGroup();
      }
  
      public function saveChanges() {
      }
  
      public function process(Pap_Db_Visit $visit) {
          $visitorId = $visit->getVisitorId();
          $this->logMessage('Backward compatibility processor ('.$visitorId.') - started');
  
          if (!$visit->isNewVisitor()) {
              $this->logMessage('Not new visitor ('.$visitorId.') - stopped');
              return;
          }
  
          if ($visit->getCookies() == '') {
              $this->logMessage('Not old cookie ('.$visitorId.') - stopped');
              return;
          }
          
          $this->visitorAffiliateCache->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
          $visitorAffiliates = $this->visitorAffiliateCache->getVisitorAffiliateAllRows($visitorId);
  
          $this->loadCookies($visit);
  
          if ($this->firstClickCookie !== null) {
              $this->logMessage('Processing first click cookie');
              $visitorAffiliates->add($this->createVisitorAffiliate($this->firstClickCookie, $visit));
              $visit->setNewVisitor(false);
          }
  
          if ($this->lastClickCookie !== null) {
              if ($this->firstClickCookie == null ||
              !$this->lastClickCookie->equals($this->firstClickCookie)) {
                  $this->logMessage('Processing last click cookie');
                  $visitorAffiliates->add($this->createVisitorAffiliate($this->lastClickCookie, $visit));
                  $visit->setNewVisitor(false);
              }
          }
  
          if ($this->saleCookie !== null) {
              $this->logMessage('Processing sale cookie - creating visitor affiliate');
              $saleVisitorAffiliate = $this->createVisitorAffiliateFromSale($this->saleCookie, $visit);
              $this->addSaleToVisitorAffiliates($saleVisitorAffiliate, $visitorAffiliates);
              $visit->setNewVisitor(false);
          }
  
          $this->logMessage('Backward compatibility - finished');
      }
  
      private function addSaleToVisitorAffiliates(Pap_Db_VisitorAffiliate $saleVisitorAffiliate, Gpf_DbEngine_Row_Collection $visitorAffiliates) {
          $iterator = $visitorAffiliates->getIterator();
          while ($iterator->valid()) {
              $visitorAffiliate = $iterator->current();
              if ($this->isSameVisitorAffiliates($saleVisitorAffiliate, $visitorAffiliate)) {
                  $visitorAffiliate->setActual(Pap_Db_VisitorAffiliate::TYPE_ACTUAL);
                  return;
              }
              $iterator->next();
          }
  
          if ($visitorAffiliates->getSize() == 1 && $this->firstClickCookie != null) {
              $visitorAffiliates->add($saleVisitorAffiliate);
              return;
          }
  
          $visitorAffiliates->insert($visitorAffiliates->getSize() - 1, $saleVisitorAffiliate);
      }
  
      private function isSameVisitorAffiliates(Pap_Db_VisitorAffiliate $saleVisitorAffiliate, Pap_Db_VisitorAffiliate $clickVisitorAffilaite) {
          return ($saleVisitorAffiliate->getUserId() == $clickVisitorAffilaite->getUserId() &&
          $saleVisitorAffiliate->getCampaignId() == $clickVisitorAffilaite->getCampaignId() &&
          $saleVisitorAffiliate->getChannelId() == $clickVisitorAffilaite->getChannelId());
      }
  
      private function createVisitorAffiliateFromSale(Pap_Tracking_Cookie_Sale $saleCookie, Pap_Db_Visit $visit) {
          $visitorAffiliate = $this->visitorAffiliateCache->createVisitorAffiliate($visit->getVisitorId());
          $visitorAffiliate->setUserId($saleCookie->getAffiliateId());
          $visitorAffiliate->setCampaignId($saleCookie->getCampaignId());
          $visitorAffiliate->setChannelId($saleCookie->getChannelId());
          $visitorAffiliate->setActual(Pap_Db_VisitorAffiliate::TYPE_ACTUAL);
          $visitorAffiliate->setDateVisit(Gpf_Common_DateUtils::now());
          $this->setVisitorAffiliateValidity($visitorAffiliate);
          return $visitorAffiliate;
      }
  
      protected function createVisitorAffiliate(Pap_Tracking_Cookie_ClickData $clickCookie, Pap_Db_Visit $visit) {
          $visitorAffiliate = $this->visitorAffiliateCache->createVisitorAffiliate($visit->getVisitorId());
          $visitorAffiliate->setBannerId($clickCookie->getBannerId());
          try {
              $click = $clickCookie->getClick();
              $visitorAffiliate->setUserId($click->getUserId());
              $visitorAffiliate->setCampaignId($click->getCampaignId());
          } catch (Gpf_Exception $e) {
          }
          $visitorAffiliate->setChannelId($clickCookie->getChannelId());
          $visitorAffiliate->setIp($this->getIp($clickCookie, $visit));
          $visitorAffiliate->setDateVisit($this->getDateVisit($clickCookie));
          $visitorAffiliate->setReferrerUrl($this->getReferrerUrl($clickCookie, $visit));
          $visitorAffiliate->setData1($clickCookie->getData1());
          $visitorAffiliate->setData2($clickCookie->getData2());
          $this->setVisitorAffiliateValidity($visitorAffiliate);
          return $visitorAffiliate;
      }
      
      protected function getIp(Pap_Tracking_Cookie_ClickData $clickCookie, Pap_Db_Visit $visit) {
          if ($clickCookie->getIp() !== null && $clickCookie->getIp() !== '') {
              return $clickCookie->getIp();
          }
          
          return $visit->getIp();
      }
      
      protected function getReferrerUrl(Pap_Tracking_Cookie_ClickData $clickCookie, Pap_Db_Visit $visit) {
          if ($clickCookie->getReferrerUrl() != null) {
              return $clickCookie->getReferrerUrl();
          }
          
          return $visit->getReferrerUrl();
      }
      
      protected function getDateVisit(Pap_Tracking_Cookie_ClickData $clickCookie) {
          if ($clickCookie->getTimestamp() != null) {
              return Gpf_Common_DateUtils::getDateTime($clickCookie->getTimestamp());
          }
          
          if ($this->lastClickCookie->getTimestamp() != null) {
              return Gpf_Common_DateUtils::getDateTime($this->lastClickCookie->getTimestamp());
          }
          
          return Gpf_Common_DateUtils::now();
      }
      
      private function setVisitorAffiliateValidity(Pap_Db_VisitorAffiliate $visitorAffiliate) {
          $this->context->setVisitorAffiliate($visitorAffiliate);
          $this->recognizeAffiliate->recognize($this->context);
          $this->recognizeCampaign->recognize($this->context);
          $this->recognizeCommGroup->recognize($this->context);
          $visitorAffiliate->setValidTo(Pap_Tracking_Click_SaveVisitorAffiliate::getVisitorAffiliateValidity($this->context, $visitorAffiliate));
      }
  
      protected function loadCookies(Pap_Db_Visit $visit) {
          $cookiesArray = array();
          $args = explode('||', ltrim($visit->getCookies(), '|'));;
          foreach ($args as $arg) {
              $parsedParams = explode('=', $arg);
              if (count($parsedParams)>=2) {
                  list($argName, $argValue) = $parsedParams;
                  if ($argValue != '') {
                      $cookiesArray[$argName] = urldecode($argValue);
                  }
              }
          }
  
          $cookies = new Pap_Tracking_Cookie($cookiesArray);
          try {
          	$this->firstClickCookie = $this->getClickCookie($cookies->getFirstClickCookie());
          } catch (Pap_Tracking_Exception $e) {
              $this->logMessage($e->getMessage());
          }
          try {
              $this->lastClickCookie = $this->getClickCookie($cookies->getLastClickCookie());
          } catch (Pap_Tracking_Exception $e) {
              $this->logMessage($e->getMessage());
          }
          try {
              $this->saleCookie = $this->getSaleCookie($cookies->getSaleCookie());
          } catch (Pap_Tracking_Exception $e) {
              $this->logMessage($e->getMessage());
          }
      }
          
      /**
       *
       * @param $saleCookie
       * @return Pap_Tracking_Cookie_Sale
       */
      protected function getSaleCookie(Pap_Tracking_Cookie_Sale $saleCookie = null) {
          if ($saleCookie === null) {
              return null;
          }
          $this->logMessage('Sale cookie not null, affiliateid=' . $saleCookie->getAffiliateId() . ', campaignid=' . $saleCookie->getCampaignId());
          if ($this->isClickDataValid($saleCookie->getAffiliateId(), $saleCookie->getCampaignId())) {
              $this->logMessage('Sale cookie valid, user and campaign exists.');
          	return $saleCookie;
          }
          $this->logMessage('Sale cookie not valid, user or campaign probably does not exists.');
          return null;
      }
      
      /**
       *
       * @param $clickData
       * @return Pap_Tracking_Cookie_ClickData
       */
      protected function getClickCookie(Pap_Tracking_Cookie_ClickData $clickData) {
      	try {
          	if ($this->isClickDataValid($clickData->getClick()->getUserId(), $clickData->getClick()->getCampaignId())) {
          		return $clickData;
          	}
      	} catch (Gpf_Exception $e) {
      	}
          return null;
      }
  
      private function logMessage($msg) {
          $this->context->debug($msg);
      }
      
      /**
       * @param $campaignId
       * @param $clickData
       * @return boolean
       */
      private function isClickDataValid($userId, $campaignId) {
      	return $this->isExistsUser($userId) && $this->isExistsCampaign($campaignId);
      }
      
      protected function isExistsUser($userId) {
          if (is_null($this->recognizeAffiliate->getUserById($this->context, $userId))) {
              $this->logMessage('User ' . $userId . ' not found!');
              return false;
          }
          return true;
      }
      
      protected function isExistsCampaign($campaignId) {
          try {
      		$this->recognizeCampaign->getCampaignById($this->context, $campaignId);
      	} catch (Gpf_Exception $e) {
      	    $this->logMessage('Campaign ' . $campaignId . ' not found! ' . $e->getMessage());
      		return false;
      	}
      	return true;
      }
  }

} //end Pap_Tracking_BackwardCompatibility_BackwardCompatibilityProcessor

if (!interface_exists('Pap_Tracking_Common_Recognizer', false)) {
  interface Pap_Tracking_Common_Recognizer {
  	public function recognize(Pap_Contexts_Tracking $context);
  }
  

} //end Pap_Tracking_Common_Recognizer

if (!interface_exists('Pap_Tracking_Common_Saver', false)) {
  interface Pap_Tracking_Common_Saver {
  	public function process(Pap_Contexts_Tracking $context);
  	
  	public function saveChanges();
  }
  

} //end Pap_Tracking_Common_Saver

if (!interface_exists('Pap_Tracking_Common_VisitProcessor', false)) {
  interface Pap_Tracking_Common_VisitProcessor {
  
      public function process(Pap_Db_Visit $visit);
      
      public function saveChanges();
  }
  

} //end Pap_Tracking_Common_VisitProcessor

if (!class_exists('Pap_Tracking_Common_RecognizeAffiliate', false)) {
  abstract class Pap_Tracking_Common_RecognizeAffiliate extends Gpf_Object implements Pap_Tracking_Common_Recognizer {
  
      private $usersCache = array();
  
      public function recognize(Pap_Contexts_Tracking $context) {
          $context->debug('Recognizing affiliate started');
  
          $user = $this->getUser($context);
           
          if($user == null) {
              $context->debug('    Error, no affiliate recognized! setDoSaveCommissions(false)');
              $context->setDoTrackerSave(false);
              $context->setDoCommissionsSave(false);
              return;
          }
  
          $context->setUserObject($user);
          $context->debug('Recognizing affiliate ended. Recognized affiliate id: '.$user->getId());
          $context->debug("");
      }
  
      protected abstract function getUser(Pap_Contexts_Tracking $context);
  
      protected function addUser($id,Pap_Affiliates_User $user) {
          $this->usersCache[$id] = $user;
      }
  
      /**
       * gets user by user id
       * @param $userId
       * @return Pap_Affiliates_User
       */
      public function getUserById($context, $id) {
          if($id == '') {
              return null;
          }
  
          if (isset($this->usersCache[$id])) {
              return $this->usersCache[$id];
          }
  
          try {
              $this->usersCache[$id] = $this->loadUserFromId($id);
              return $this->usersCache[$id];
          } catch (Gpf_Exception $e) {
              $context->debug("User with RefId/UserId: $id doesn't exist");
              return null;
          }
      }
  
      protected function loadUserFromId($id) {
          return Pap_Affiliates_User::loadFromId($id);
      }
  }
  

} //end Pap_Tracking_Common_RecognizeAffiliate

if (!class_exists('Pap_Tracking_Common_RecognizeBanner', false)) {
  abstract class Pap_Tracking_Common_RecognizeBanner extends Gpf_Object implements Pap_Tracking_Common_Recognizer {
  
      private $bannersCache = array();
  
      public function __construct() {
      }
  
      public final function recognize(Pap_Contexts_Tracking $context) {
          $context->debug('Recognizing banner started');
  
          $banner = $this->recognizeBanners($context);
  
          if($banner == null) {
              $context->debug('  No banner recognized!');
          }
           
          $context->debug('Recognizing banner ended');
  
          if($banner != null) {
              $this->setParentBanner($context, $banner);
              $context->setBannerObject($banner);
          }
      }
  
      /**
       * @return Pap_Common_Banner
       */
      protected abstract function recognizeBanners(Pap_Contexts_Tracking $context);
  
      /**
       *
       * @param Pap_Common_Banner $banner
       */
      private function setParentBanner(Pap_Contexts_Tracking $context, Pap_Common_Banner $banner){
          $id = $context->getRotatorBannerId();
          if($id != ''){
              $banner->setParentBannerId($id);
          }
      }
  
      /**
       * returns user object from standard parameter from request
       *
       * @return Pap_Common_Banner
       * @throws Gpf_Exception
       */
      protected function getBannerFromParameter(Pap_Contexts_Tracking $context) {
          $id = $context->getBannerId();
          if($id == '') {
              $message = 'Banner id not found in parameter';
              $context->debug($message);
              throw new Pap_Tracking_Exception($message);
          }
          $context->debug("Getting banner from request parameter. Banner Id: ".$id);
          return $this->getBannerById($context, $id);
      }
  
      /**
       * @return Pap_Common_Banner
       * @throws Gpf_Exception
       */
      protected function getBannerById(Pap_Contexts_Tracking $context, $id) {
          if (isset($this->bannersCache[$id])) {
              return $this->bannersCache[$id];
          }
  
          $bannerFactory = new Pap_Common_Banner_Factory();
          $banner = $bannerFactory->getBanner($id);
          $this->checkBanner($context, $banner);        
          $this->bannersCache[$id] = $banner;
          return $this->bannersCache[$id];
      }
      
      private function checkBanner(Pap_Contexts_Tracking $context, Pap_Common_Banner $banner) {
          if ($this->isAccountRecognizedNotFromDefault($context) && $banner->getAccountId() != $context->getAccountId()) {
              $context->debug("Banner with Id: ".$banner->getId()." and name '".$banner->getName()."' cannot be used with accountId: '". $context->getAccountId() ."'!");
              throw new Gpf_Exception("Banner is from differen account");
          }
      }
      
      private function isAccountRecognizedNotFromDefault(Pap_Contexts_Tracking $context) {
          if ($context->getAccountId() != null && $context->getAccountRecognizeMethod() != Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_DEFAULT) {
              return true;
          } 
          return false;
      }
  }
  

} //end Pap_Tracking_Common_RecognizeBanner

if (!class_exists('Pap_Tracking_Common_RecognizeCampaign', false)) {
  abstract class Pap_Tracking_Common_RecognizeCampaign extends Gpf_Object implements Pap_Tracking_Common_Recognizer {
      private $campaignsCache = array();
      
      public function __construct() {
      }
      
      public final function recognize(Pap_Contexts_Tracking $context) {
          $context->debug('Recognizing campaign started');
  
          $campaign = $this->recognizeCampaigns($context);
  
          if($campaign != null) {
          	$this->onCampaignRecognized($campaign, $context);
              $context->setCampaignObject($campaign);
          } else {
              $context->debug('No campaign recognized!');
              $context->setDoTrackerSave(false);
              $context->setDoCommissionsSave(false);
          }
           
          $context->debug('Recognizing campaign ended');
      }
      
      protected function onCampaignRecognized(Pap_Common_Campaign $campaign, Pap_Contexts_Tracking $context) {
          if ($context->getAccountId() != null && $context->getAccountId() != '' && $context->getAccountRecognizeMethod() != Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_DEFAULT) {
              $context->debug('AccountId already recognized before recognizing campaign.');
              return;
          }
  
          $context->debug('AccountId recognized from Campaign, set: ' . $campaign->getAccountId());
          $context->setAccountId($campaign->getAccountId(), Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_FROM_CAMPAIGN);
      }
  
      /**
       * @return Pap_Common_Campaign
       */
      protected abstract function recognizeCampaigns(Pap_Contexts_Tracking $context);
  
      /**
       * gets campaign by campaign id
       * @param $campaignId
       * @return Pap_Common_Campaign
       * @throws Gpf_Exception
       */
      public function getCampaignById(Pap_Contexts_Tracking $context, $campaignId) {
          if($campaignId == '') {
              throw new Gpf_Exception('Can not get campaign. Empty campaign id');
          }
          
          if (isset($this->campaignsCache[$campaignId])) {
              return $this->campaignsCache[$campaignId];
          }
          
          $campaign = new Pap_Common_Campaign();
          $campaign->setId($campaignId);
          $campaign->load();
          $this->checkCampaign($context, $campaign);
          $this->campaignsCache[$campaignId] = $campaign;
          return $campaign;
      }
      
      private function checkCampaign(Pap_Contexts_Tracking $context, Pap_Common_Campaign $campaign) {
          if ($this->isAccountRecognizedNotFromDefault($context) && $campaign->getAccountId() != $context->getAccountId()) {
              $context->debug("Campaign with Id: ".$campaign->getId()." and name '".$campaign->getName()."' cannot be used with accountId: '". $context->getAccountId() ."'!");
              throw new Gpf_Exception("Campaign is from differen account");
          }
          $status = $campaign->getCampaignStatus();
          if($status == Pap_Db_Campaign::CAMPAIGN_STATUS_STOPPED
              || $status == Pap_Db_Campaign::CAMPAIGN_STATUS_STOPPED_INVISIBLE) {
              $context->debug("Campaign with Id: ".$campaign->getId()." and name '".$campaign->getName()."' is stopped, cannot be used!");
              throw new Gpf_Exception("Campaign stopped");
          }
          if($status == Pap_Db_Campaign::CAMPAIGN_STATUS_DELETED) {
              $context->debug("Campaign with Id: ".$campaign->getId()." and name '".$campaign->getName()."' is deleted, cannot be used!");
              throw new Gpf_Exception("Campaign deleted");
          }
      }
      
      private function isAccountRecognizedNotFromDefault(Pap_Contexts_Tracking $context) {
          if ($context->getAccountId() != null && $context->getAccountRecognizeMethod() != Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_DEFAULT) {
              return true;
          } 
          return false;
      }
      
      /**
       * @return Pap_Common_Campaign
       * @throws Gpf_Exception
       */
      public function getDefaultCampaign(Pap_Contexts_Tracking $context) {
          $context->debug('Loading default campaign for account: '.$context->getAccountId());
          $defaultcampaignid = Pap_Db_Table_Campaigns::getDefaultCampaignId($context->getAccountId());
          $context->debug('Loading default campaign by Id: '.$defaultcampaignid);
          return $this->getCampaignById($context, $defaultcampaignid);
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
  

} //end Pap_Tracking_Common_RecognizeCampaign

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

if (!class_exists('Pap_Tracking_Click_ClickProcessor', false)) {
  class Pap_Tracking_Click_ClickProcessor extends Gpf_Object implements Pap_Tracking_Common_VisitProcessor {
      /*
       * @var Pap_Tracking_Common_RecognizeAccountId
       */
      private $accountRecognizer;
  
      /*
       * array<Pap_Tracking_Common_Recognizer>
       */
      private $paramRecognizers = array();
  
      /**
       * @var Pap_Tracking_Click_RecognizeDirectLink
       */
      private $directLinkRecognizer;
  
      /*
       * array<Pap_Tracking_Common_Saver>
       */
      private $savers = array();
  
      /*
       * Pap_Tracking_Click_FraudProtection
       */
      private $fraudProtection;
  
      /**
       * @var Pap_Tracking_Click_RecognizeAffiliate
       */
      private $affiliateRecognizer;
  
      /**
       * @var Pap_Tracking_Visit_VisitorAffiliateCache
       */
      private $visitorAffiliateCache;
  
  
      public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache, Pap_Tracking_Click_RecognizeAffiliate $recognizeAffiliate = null) {
          if ($recognizeAffiliate === null) {
              $recognizeAffiliate = new Pap_Tracking_Click_RecognizeAffiliate();
          }
  
          $this->visitorAffiliateCache = $visitorAffiliateCache;
  
          $this->accountRecognizer = new Pap_Tracking_Common_RecognizeAccountId();
  
          $this->paramRecognizers[] = $this->affiliateRecognizer = $recognizeAffiliate;
          $this->paramRecognizers[] = new Pap_Tracking_Click_RecognizeBanner();
          $this->paramRecognizers[] = $recognizeCampaign = new Pap_Tracking_Click_RecognizeCampaign($visitorAffiliateCache);
          $this->paramRecognizers[] = new Pap_Tracking_Click_RecognizeChannel();
  
          $this->directLinkRecognizer = new Pap_Tracking_Click_RecognizeDirectLink($recognizeCampaign);
  
          $this->savers[] = new Pap_Tracking_Click_SaveClick();
          $this->savers[] = new Pap_Tracking_Click_SaveClickCommissions();
          $this->savers[] = new Pap_Tracking_Click_SaveVisitorAffiliate($visitorAffiliateCache);
  
          $this->fraudProtection = new Pap_Tracking_Click_FraudProtection();
      }
  
      public function process(Pap_Db_Visit $visit) {
          if ($this->processParamsClick($visit)) {
              return;
          }
  
          $this->processDirectLinkClick($visit);
      }
  
      public function saveChanges() {
          foreach ($this->savers as $saver) {
              $saver->saveChanges();
          }
      }
  
      private function processParamsClick(Pap_Db_Visit $visit) {
          $context = $this->getContextFromParams($visit);
          if ($context == null) {
              return false;
          }
          $this->fraudProtection->check($context);
          foreach ($this->paramRecognizers as $recognizer) {
              $recognizer->recognize($context);
          }
          $this->saveClick($context);
          return true;
      }
  
      private function processDirectLinkClick(Pap_Db_Visit $visit) {
          if (Gpf_Settings::get(Pap_Settings::SUPPORT_DIRECT_LINKING) != Gpf::YES) {
              return;
          }
          $context = $this->createContext($visit);
          $this->fraudProtection->check($context);
          $this->directLinkRecognizer->process($context, $visit->getReferrerUrl());
          $this->saveClick($context);
      }
  
      private function isClickRequest(Pap_Tracking_Request $request) {
          return ($request->getAffiliateId() != '' || $request-> getForcedAffiliateId() != '');
      }
  
      /**
       * @param Pap_Db_Visit
       * @return Pap_Contexts_Click
       */
      private function getContextFromParams(Pap_Db_Visit $visit) {
          $context = $this->createContext($visit);
  
          $getRequest = new Pap_Tracking_Request();
          $getRequest->parseUrl($visit->getGetParams());
          if($getRequest->getAffiliateId() == ''){
              $context->debug('Affiliate Id or Affiliate Id Parameter is missing');
          }
          if ($this->isClickRequest($getRequest)) {
              $context->setRequestObject($getRequest);
              $context->debug('It is click request.');
              return $context;
          }
          $anchorRequest = new Pap_Tracking_Request();
          $anchor = $visit->getAnchor();
          $anchorRequest->parseUrl($anchor);
          if ($this->isClickRequest($anchorRequest)) {
              $context->setRequestObject($anchorRequest);
              $context->debug('It is anchor request, anchor: ' . $anchor);
              return $context;
          }
  
          if ($anchor != '' && Gpf_Settings::get(Pap_Settings::SUPPORT_SHORT_ANCHOR_LINKING) == Gpf::YES) {
              $user = $this->affiliateRecognizer->getUserById($context, $anchor);
              if ($user == null) {
                  $context->debug('User is null, anchor was:' . $anchor);
                  return null;
              }
              $shortAnchorRequest = new Pap_Tracking_Request();
              $shortAnchorRequest->parseUrl('?'.Pap_Tracking_Request::getAffiliateClickParamName().'='.$anchor);
              $context->setRequestObject($shortAnchorRequest);
              $context->debug('Short anchor link');
              return $context;
          }
  
          $context->debug('Return null - this might be a problem...');
          return null;
      }
  
      private function createContext(Pap_Db_Visit $visit) {
          $context = new Pap_Contexts_Click();
          $context->setDoTrackerSave(true);
          $context->setVisit($visit);
          $context->setVisitorId($visit->getVisitorId());
          $context->setDateCreated($visit->getDateVisit());
          $this->accountRecognizer->recognize($context);
          $this->visitorAffiliateCache->setAccountId($context->getAccountId());
          return $context;
      }
  
      protected function saveClick(Pap_Contexts_Click $context) {
          Gpf_Plugins_Engine::extensionPoint('Tracker.click.recognizeParameters', $context);
  
          if(!$context->getDoTrackerSave()) {
              $context->debug('Click registration stopped by feature or plugin');
              return;
          }
  
          $context->debug("  Saving click started");
  
          Gpf_Plugins_Engine::extensionPoint('Tracker.click.beforeSaveClick', $context);
  
          foreach ($this->savers as $saver) {
              $saver->process($context);
          }
  
          Gpf_Plugins_Engine::extensionPoint('Tracker.click.afterSaveClick', $context);
  
          $context->debug("  Saving click ended");
          $context->debug("");
      }
  }
  

} //end Pap_Tracking_Click_ClickProcessor

if (!class_exists('Pap_Tracking_Click_RecognizeAffiliate', false)) {
  class Pap_Tracking_Click_RecognizeAffiliate extends Pap_Tracking_Common_RecognizeAffiliate {
  
  
      public function recognize(Pap_Contexts_Tracking $context) {
          if(($context->getUserObject()) != null) {
              $context->debug('  User already recognized, finishing user recognizer');
              return;
          }
  
          parent::recognize($context);
      }
  
  
      protected function getUser(Pap_Contexts_Tracking $context) {
          if (($user = $this->getUserFromForcedParameter($context)) != null) {
              return $user;
          }
           
          if(($user = $this->getUserFromParameter($context)) != null) {
              return $user;
          }
  
          return null;
      }
  
      /**
       * returns user object from forced parameter AffiliateID
       * parameter name is dependent on track.js, where it is used.
       *
       * @return string
       */
      protected function getUserFromForcedParameter(Pap_Contexts_Click $context) {
          $context->debug("  Trying to get affiliate from request parameter '".Pap_Tracking_Request::getForcedAffiliateParamName()."'");
  
          $userId = $context->getForcedAffiliateId();
          if($userId != '') {
              $context->debug("    Setting affiliate from request parameter. Affiliate Id: ".$userId);
              return $this->getUserById($context, $userId);
          }
  
          $context->debug('Affiliate not found in forced parameter');
          return null;
      }
  
      /**
       * returns user object from standard parameter from request
       *
       * @return string
       */
      protected function getUserFromParameter(Pap_Contexts_Click $context) {
          $parameterName = Pap_Tracking_Request::getAffiliateClickParamName();
          if($parameterName == '') {
              $context->debug("  Cannot get name of request parameter for affiliate ID");
              return null;
          }
           
          $context->debug("  Trying to get affiliate from request parameter '$parameterName'");
  
          $userId = $context->getAffiliateId();
          if($userId != '') {
              $context->debug("    Setting affiliate from request parameter. Affiliate Id: ".$userId);
              return $this->getUserById($context, $userId);
          }
  
          $context->debug("    Affiliate not found in parameter");
          return null;
      }
  }
  

} //end Pap_Tracking_Click_RecognizeAffiliate

if (!class_exists('Pap_Tracking_Click_RecognizeBanner', false)) {
  class Pap_Tracking_Click_RecognizeBanner extends Pap_Tracking_Common_RecognizeBanner {
  
      /**
       * @return Pap_Common_Banner
       */
      protected function recognizeBanners(Pap_Contexts_Tracking $context) {
          try {
              return $this->getBannerFromForcedParameter($context);
          } catch (Gpf_Exception $e) {
          }
          
          try {
              return $this->getBannerFromParameter($context);
          } catch (Gpf_Exception $e) {
          }
  
          return null;
      }
  
      /**
       * returns user object from forced parameter AffiliateID
       * parameter name is dependent on track.js, where it is used.
       *
       * @return Pap_Common_Banner
       * @throws Gpf_Exception
       */
      private function getBannerFromForcedParameter(Pap_Contexts_Click $context) {
          $id = $context->getForcedBannerId();
  
          if($id == '') {
              $message = 'Banner id not found in forced parameter';
              $context->debug($message);
              throw new Pap_Tracking_Exception($message);
          }
  
          $context->debug("Getting banner from forced request parameter. Banner Id: ".$id);
          return $this->getBannerById($context, $id);
      }
  }
  

} //end Pap_Tracking_Click_RecognizeBanner

if (!class_exists('Pap_Tracking_Click_RecognizeCampaign', false)) {
  class Pap_Tracking_Click_RecognizeCampaign extends Pap_Tracking_Common_RecognizeCampaign {
  
      /**
       * @var Pap_Tracking_Visit_VisitorAffiliateCache
       */
      private $visitorAffiliateCache;
  
      public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
          $this->visitorAffiliateCache = $visitorAffiliateCache;
      }
  
      protected function onCampaignRecognized(Pap_Common_Campaign $campaign, Pap_Contexts_Tracking $context) {
          parent::onCampaignRecognized($campaign, $context);
          $this->visitorAffiliateCache->setAccountId($context->getAccountId());
      }
  
      /**
       * @return Pap_Common_Banner
       */
      protected function recognizeCampaigns(Pap_Contexts_Tracking $context) {
          if ($context->getCampaignObject() != null) {
              $context->debug('Campaign already recognized, skipping. CampaignId: '.$context->getCampaignObject()->getId());
              return $context->getCampaignObject();
          }
  
          try {
              return $this->getCampaignFromForcedParameter($context);
          } catch (Gpf_Exception $e) {
          }
          	
          try {
              return $this->getCampaignFromParameter($context);
          } catch (Gpf_Exception $e) {
          }
  
          try {
              return $this->recognizeCampaignFromBanner($context);
          } catch (Gpf_Exception $e) {
          }
  
          try {
              return $this->getDefaultCampaign($context);
          } catch (Gpf_Exception $e) {
          }
      }
  
      /**
       * returns user object from forced parameter CampaignID
       * parameter name is dependent on track.js, where it is used.
       *
       * @return Pap_Common_Campaign
       * @throws Gpf_Exception
       */
      protected function getCampaignFromForcedParameter(Pap_Contexts_Tracking $context) {
          $campaignId = $context->getForcedCampaignId();
          if($campaignId != '') {
              $context->debug("Getting campaign from forced parameter. Campaign Id: ".$campaignId);
              return $this->getCampaignById($context, $campaignId);
          }
          $this->logAndThrow($context, 'Campaign not found in forced parameter');
      }
  
      /**
       * returns campaign object from standard parameter from request
       *
       * @return string
       */
      protected function getCampaignFromParameter(Pap_Contexts_Tracking $context) {
          $campaignId = $context->getCampaignId();
          if($campaignId != '') {
              $context->debug("Getting affiliate from request parameter. Campaign Id: ".$campaignId);
              return $this->getCampaignById($context, $campaignId);
          }
          $this->logAndThrow($context, "Campaign not found in parameter");
      }
  
      /**
       * if banner was recognized, get campaign from this banner
       *
       * @param Pap_Plugins_Tracking_Click_Context $context
       * @return unknown
       */
      protected function recognizeCampaignFromBanner(Pap_Contexts_Tracking $context) {
          $banner = $context->getBannerObject();
          if($banner == null) {
              $this->logAndThrow($context, 'Banner is null, cannot recognize campaign');
          }
  
          $context->debug('Banner recognized, Banner Id: '.$banner->getId().', getting campaign for this banner, campaignId: '. $banner->getCampaignId());
          return $this->getCampaignById($context, $banner->getCampaignId());
      }
  }
  

} //end Pap_Tracking_Click_RecognizeCampaign

if (!class_exists('Pap_Tracking_Click_RecognizeChannel', false)) {
  class Pap_Tracking_Click_RecognizeChannel extends Pap_Tracking_Common_RecognizeChannel implements Pap_Tracking_Common_Recognizer {
  
      /**
       * @return Pap_Db_Channel
       */
      protected function recognizeChannels(Pap_Contexts_Tracking $context) {
          try {
              return $this->getChannelFromForcedParameter($context);
          } catch (Gpf_Exception $e) {
          }
  
          try {
              return $this->getChannelFromParameter($context);
          } catch (Gpf_Exception $e) {
          }
      }
  
      /**
       * @return Pap_Db_Channel
       * @throws Gpf_Exception
       */
      private function getChannelFromForcedParameter(Pap_Contexts_Click $context) {
          $context->debug('Trying to get channel from forced parameter');
          return $this->getChannelById($context, $context->getForcedChannelId());
      }
  
      /**
       * @return Pap_Db_Channel
       * @throws Gpf_Exception
       */
      private function getChannelFromParameter(Pap_Contexts_Click $context) {
          $context->debug('Trying to get channel from parameter');
          return $this->getChannelById($context, $context->getChannelId());
      }
  }
  

} //end Pap_Tracking_Click_RecognizeChannel

if (!class_exists('Pap_Tracking_Click_RecognizeDirectLink', false)) {
  class Pap_Tracking_Click_RecognizeDirectLink extends Gpf_Object {
  
      private $campaignRecognizer;
  
      public function __construct(Pap_Tracking_Common_RecognizeCampaign $campaignRecognizer = null) {
          if ($campaignRecognizer == null) {
              $this->campaignRecognizer = new Pap_Tracking_Common_RecognizeCampaign();
          } else {
              $this->campaignRecognizer = $campaignRecognizer;
          }
      }
  
      /**
       * @anonym
       * @service direct_link read
       */
      public function getAffiliateId(Pap_Contexts_Click $context, Gpf_Rpc_Params $params) {
          $data = new Gpf_Rpc_Data($params);
  
          $context = new Pap_Contexts_Click();
          $context->getRequestObject()->setRequestParameter(
          Pap_Tracking_Request::PARAM_REFERRERURL_NAME,
          $params->get('url'));
          $match = $this->getMatch($context);
  
          if ($match != null) {
              foreach ($match as $key => $value) {
                  $data->setValue($key, $value);
              }
          }
          return $data;
      }
  
      public function process(Pap_Contexts_Click $context, $referrerUrl) {
          $context->debug('DirectLink recognition started');
  
          $match = $this->getMatch($context, $referrerUrl);
          if($match != null) {
              $context->debug('  Match found, continue processing');
              $this->fillParametersFromMatch($context, $match);
          } else {
              $context->debug('  Match not found, stopping');
              $context->setDoTrackerSave(false);
          }
          	
          $context->debug('DirectLink recognition ended');
      }
  
      /**
       * recognizes match from referrer by DirectLink feature
       *
       * @return array
       */
      protected function getMatch(Pap_Contexts_Click $context, $referrerUrl) {
          if(Gpf_Settings::get(Pap_Settings::SUPPORT_DIRECT_LINKING) != Gpf::YES) {
              $context->debug('  DirectLink tracking is not supported');
              return null;
          }
  
          if($referrerUrl == '') {
              $context->debug('Referrer URL empty');
              return null;
          }
          	
          $context->debug('  Trying to recognize affiliate from referrer URL (DirectLink): \'' . $referrerUrl . '\'');
          $directLinksBase = Pap_Tracking_DirectLinksBase::getInstance();
          try {
              $match = $directLinksBase->checkDirectLinkMatch($referrerUrl);
              return $match;
          } catch(Gpf_Exception $e) {
              $context->debug('Exception :'.$e->getMessage());
              return null;
          }
      }
  
      /**
       * processes match and sets userid, campaign, banner, channel
       */
      protected function fillParametersFromMatch(Pap_Contexts_Click $context, $match) {
          if($match == null || $match == false || !is_array($match) || count($match) != 5) {
              $context->debug("    Matching data are in incorrect format");
          }
  
          $userId = $match['userid'];
          $url = $match['url'];
          $channelid = $match['channelid'];
          $campaignid = $match['campaignid'];
          $bannerid = $match['bannerid'];
  
          $context->debug("    Referrer matched '$url' pattern");
  
          // user
          if ($userId == '') {
              $context->debug("    DirectLink affiliate Id is empty stopping");
              $context->setDoTrackerSave(false);
              return;
          }
  
          try {
              $user = Pap_Affiliates_User::loadFromId($userId);
          } catch (Gpf_Exception $e) {
              $context->debug(" DirectLink affiliate with id '$userId' doesn't exist");
              $context->setDoTrackerSave(false);
              return;
          }
  
          $context->debug("    Setting affiliate from referrer URL. Affiliate Id: ".$userId."");
          $context->setUserObject($user);
  
  
          // banner
          $banner = null;
          try {
              $bannerFactory = new Pap_Common_Banner_Factory();
              $banner = $bannerFactory->getBanner($bannerid);
              $context->debug("Setting banner from referrer URL. Banner Id: ".$bannerid."");
              $context->setBannerObject($banner);
          } catch (Gpf_Exception $e) {
              $context->debug("Banner parameter in DirectLink is empty");
          }
  
          // campaign
          $campaign = $this->getCampaignById($context, $campaignid);
          if($campaignid != '' && $campaign != null) {
              $context->debug("    Setting campaign from DirectLink. Campaign Id: ".$campaignid."");
              $context->setCampaignObject($campaign);
          } else {
              $context->debug("    Campaign parameter in DirectLink is empty");
          }
          	
          if($banner != null) {
              $context->debug("    Trying to get campaign from banner");
              $campaign = $this->getCampaignFromBanner($context, $banner);
          }
  
          if($campaign == null) {
              $campaign = $this->getDefaultCampaign($context);
          }
  
          if($campaign != null) {
              $context->setCampaignObject($campaign);
          } else {
              $context->setDoTrackerSave(false);
              $context->debug("        No default campaign defined");
          }
          
          // channel
          $channel = $this->getChannelById($context, $channelid);
          if($channelid != '' && $channel != null) {
              $context->debug("    Setting channel from referrer URL. Channel Id: ".$channelid."");
              $context->setChannelObject($channel);
          } else {
              $context->debug("    Channel parameter in DirectLink is empty");
          }
          
          // account
          if ($campaign != null) {
              $context->setAccountId($campaign->getAccountId(), Pap_Contexts_Tracking::ACCOUNT_RECOGNIZED_FROM_CAMPAIGN);
          }
      }
  
      private function getChannelById(Pap_Contexts_Click $context, $channelid) {
          $channelRecognizer = new Pap_Tracking_Click_RecognizeChannel();
          try {
              return $channelRecognizer->getChannelById($context, $channelid);
          } catch (Gpf_Exception $e) {
              return null;
          }
      }
  
      private function getCampaignFromBanner(Pap_Contexts_Click $context, $banner) {
          $campaignId = $banner->getCampaignId();
          if($campaignId != '') {
              $context->debug("    Setting campaign. Campaign Id: ".$campaignId);
              return $this->getCampaignById($context, $campaignId);
          }
  
          $context->debug("    Campaign not found");
          return null;
      }
  
      private function getCampaignById(Pap_Contexts_Click $context, $campaignId) {
          try {
              return $this->campaignRecognizer->getCampaignById($context, $campaignId);
          } catch (Gpf_Exception $e) {
              return null;
      }
      }
  
      private function getDefaultCampaign(Pap_Contexts_Click $context) {
          try {
              return $this->campaignRecognizer->getDefaultCampaign($context);
          } catch (Gpf_Exception $e) {
              return null;
          }
      }
  }
  

} //end Pap_Tracking_Click_RecognizeDirectLink

if (!class_exists('Pap_Tracking_Click_SaveClick', false)) {
  class Pap_Tracking_Click_SaveClick extends Gpf_Object implements Pap_Tracking_Common_Saver {
  
  	protected $clicks = array();
  
  	public function __construct() {
  	}
  	
  	public function process(Pap_Contexts_Tracking $context) {
  		$banner = $context->getBannerObject();
  		$rawClick = $this->createRawClick($context, $banner);
  		if($context->getDoTrackerSave() && $rawClick != null) {
  			$this->saveRawClick($rawClick);
  			$this->saveClick($context, new Pap_Db_Click(), $banner, $rawClick);
  		}
  		$context->debug('');
  	}
  	
  	public function saveChanges() {
  		foreach ($this->clicks as $click) {
  			$click->save();
  		}
  	}
  
  	private function getCampaignId(Pap_Contexts_Click $context) {
  		$campaignObj = $context->getCampaignObject();
  		if($campaignObj != null) {
  			return $campaignObj->getId();
  		}
  		return null;
  	}
  
  	private function getChannel(Pap_Contexts_Click $context) {
  		$channelObj = $context->getChannelObject();
  		if($channelObj != null) {
  			return $channelObj->getId();
  		}
  		return null;
  	}
  
  	protected function isClickUnique(Pap_Contexts_Click $context) {
  	    return $context->getVisit()->isNewVisitor();
  	}
  
  	/**
  	 * @param Pap_Contexts_Click $context
  	 * @param Pap_Common_Banner $banner
  	 *
  	 * @return Pap_Db_RawClick
  	 */
  	private function createRawClick(Pap_Contexts_Click $context, Pap_Common_Banner $banner=null){
  		$context->debug('    Creating raw click started');
  
  		if ($context->getUserObject() == null) {
  			$context->debug('    Raw clicked not created. User not set.');
  			return null;
  		}
  
  		$click = new Pap_Db_RawClick();
  		$click->setUserId($context->getUserObject()->getId());
  		if($banner!=null){
  			$click->setBannerId($banner->getId());
  			$click->setParentBannerId($banner->getParentBannerId());
  		}
  		$click->setCampaignId($this->getCampaignId($context));
  	    $click->setIp($context->getIp());
  		$click->setCountryCode($context->getCountryCode());
  		$click->setData1($context->getExtraDataFromRequest(1));
  		$click->setData2($context->getExtraDataFromRequest(2));
  		$click->setChannel($this->getChannel($context));
  		$click->setDateTime($context->getVisitDateTime());
  		$click->setRefererUrl($context->getReferrerUrl());
  		$click->setBrowser($context->getUserAgent());
  		$click->setProcessedStatus(true);
  		$click->setType($this->getType($context));
  		$context->setRawClickObject($click);
  			
  		$context->debug('    Creating raw click ended');
  
  		return $click;
  	}
  
  	private function getType(Pap_Contexts_Click $context) {
  		if($context->getClickStatus() == Pap_Db_ClickImpression::STATUS_DECLINED) {
  			return Pap_Db_ClickImpression::STATUS_DECLINED;
  		}
  		if($this->isClickUnique($context)) {
  			return Pap_Db_ClickImpression::STATUS_UNIQUE;
  		}
  		return Pap_Db_ClickImpression::STATUS_RAW;
  	}
  	
  	private function hashClick(Pap_Db_Click $clickPrototype, $clickParams) {
  		return md5(implode('_', array_values($clickParams)));
  	}
  	
  	private function saveClick(Pap_Contexts_Click $context, Pap_Db_Click $clickPrototype,
  	                            Pap_Common_Banner $banner=null, Pap_Db_RawClick $rawClick) {
  	    $context->debug('Saving click (as object, not rawclick)');
  		$clickParams = $this->getClickParamsArray($clickPrototype, $context, $banner);
  		$hash = $this->hashClick($clickPrototype, $clickParams);
          if (!array_key_exists($hash, $this->clicks)) {
              $this->clicks[$hash] = $this->initClick($clickPrototype, $clickParams);
          }
  
          $click = $this->clicks[$hash];
          $context->debug('click type=' . $rawClick->getType());
          switch ($rawClick->getType()) {
              case Pap_Db_ClickImpression::STATUS_DECLINED:
                  $click->addDeclined();
                  break;
              case Pap_Db_ClickImpression::STATUS_UNIQUE:
                  $click->addUnique();
              default:
                  $click->addRaw();
                  break;
          }
          $context->debug('Saving done');
  	}
  
  	protected function saveRawClick(Pap_Db_RawClick $rawClick) {
  		Gpf_Log::debug('Calling save on raw click');
  		$rawClick->save();
  		Gpf_Log::debug('Saving done');
  	}
  
  	protected function fillClickParams(Pap_Db_ClickImpression $click, $clickParams) {
  		foreach ($clickParams as $name => $value) {
  			$click->set($name, $value);
  		}
  	}
  
  	private function getClickParamsArray(Pap_Db_ClickImpression $click, Pap_Contexts_Click $context, Pap_Common_Banner $banner=null) {
  	    $columns = array();
  	    $columns[Pap_Db_Table_ClicksImpressions::ACCOUNTID] = $context->getAccountId();
          $columns[Pap_Db_Table_ClicksImpressions::USERID] = $context->getUserObject()->getId();
          $columns[Pap_Db_Table_ClicksImpressions::BANNERID] = $banner == null ? '' : $banner->getId();
          $columns[Pap_Db_Table_ClicksImpressions::PARENTBANNERID] = $banner == null ? '' : $banner->getParentBannerId();
          $columns[Pap_Db_Table_ClicksImpressions::CAMPAIGNID] = $context->getCampaignObject() == null ? '' : $context->getCampaignObject()->getId();
          $columns[Pap_Db_Table_ClicksImpressions::COUNTRYCODE] = $context->getCountryCode();
          $columns[Pap_Db_Table_ClicksImpressions::CDATA1] = $context->getExtraDataFromRequest(1);
          $columns[Pap_Db_Table_ClicksImpressions::CDATA2] = $context->getExtraDataFromRequest(2);
          $columns[Pap_Db_Table_ClicksImpressions::CHANNEL] = $this->getChannel($context);
          $timeNow = new Gpf_DateTime($context->getVisitDateTime());
          $columns[Pap_Db_Table_ClicksImpressions::DATEINSERTED] = $timeNow->format("Y-m-d H:00:00");
          return $columns;
  	}
  	
  	protected function initClick(Pap_Db_ClickImpression $click, $clickParams) {
  	    $this->fillClickParams($click, $clickParams);
  		try {
  			$click->loadFromData(array_keys($clickParams));
  		} catch (Gpf_DbEngine_NoRowException $e) {
  		} catch (Gpf_DbEngine_TooManyRowsException $e) {
  		    $fixedClick = $this->fixTooManyRows($click->loadCollection(array_keys($clickParams)));
  		    if (!is_null($fixedClick)) {
  		        $click = $fixedClick;
  		    }
  		}
  		return $click;
  	}
  
  	/**
  	 * @param $clicks array<Pap_Db_ClickImpression>
  	 * @return Pap_Db_ClickImpression
  	 */
  	private function fixTooManyRows(Gpf_DbEngine_Row_Collection $clicks) {
  		if ($clicks->getSize() <= 0) {
  			return null;
  		}
  		$first = true;
  		foreach ($clicks as $click) {
  			if ($first) {
  				$firstClick = $click;
  				$first = false;
  				continue;
  			}
  			$firstClick->mergeWith($click);
  			$click->delete();
  		}
  		$firstClick->save();
  		return $firstClick;
  	}
  }
  

} //end Pap_Tracking_Click_SaveClick

if (!class_exists('Pap_Tracking_Click_SaveClickCommissions', false)) {
  class Pap_Tracking_Click_SaveClickCommissions extends Gpf_Object implements Pap_Tracking_Common_Saver {
  
      /*
       * array<Pap_Tracking_Common_Recognizer>
       */
      private $paramRecognizers = array();
  
      /**
       * array<Pap_Tracking_Common_Saver>
       */
      private $commissionSavers = array();
  
      private $commissions = array();
  
      public function __construct() {
          $this->paramRecognizers[] = new Pap_Tracking_Common_RecognizeCommGroup();
          $this->paramRecognizers[] = new Pap_Tracking_Common_RecognizeCommSettings();
  
          $this->commissionSavers[] = new Pap_Tracking_Common_UpdateAllCommissions();
      }
  
      public function saveChanges() {
          foreach ($this->commissionSavers as $commissionSaver) {
              $commissionSaver->saveChanges();
          }
      }
  
      public function process(Pap_Contexts_Tracking $context) {
          $context->debug('  Preparing commissions for the click started');
  
          $context->setDoCommissionsSave($this->isValidCommission($context));
          if (!$context->getDoCommissionsSave()) {
              return;
          }
  
          $this->recognizeCommissions($context);
  
          if($context->getDoCommissionsSave() && $context->getDoTrackerSave()
          && $context->getClickStatus() != Pap_Db_ClickImpression::STATUS_DECLINED) {
              $this->saveCommissions($context);
          }
  
          $context->debug('  Preparing commissions for the click ended');
          $context->debug('');
      }
  
      protected function recognizeCommissions(Pap_Contexts_Click $context) {
          foreach ($this->paramRecognizers as $recognizer) {
              $recognizer->recognize($context);
          }
      }
  
      protected function saveCommissions(Pap_Contexts_Click $context) {
          Gpf_Plugins_Engine::extensionPoint('Tracker.click.beforeSaveCommissions', $context);
          if (!$context->getDoCommissionsSave()) {
              $context->debug('Click commissions save stopped by plugin.');
              return;
          }
          foreach ($this->commissionSavers as $commissionSaver) {
              $commissionSaver->process($context);
          }
          Gpf_Plugins_Engine::extensionPoint('Tracker.click.afterSaveCommissions', $context);
      }
  
      private function isValidCommission(Pap_Contexts_Click $context) {
          $context->debug('    Checking if we should save commissions for this click');
  
          if(!$context->getDoTrackerSave()) {
              $context->debug("  Saving cookies in Tracker is disabled (getDoTrackerSave() returned false), so we set also saving comissions (getDoCommissionsSave() to false");
              return false;
          }
  
          if($context->getCampaignObject() == null) {
              $context->debug('        STOPPING, campaign not recognized');
              return false;
          }
  
          $clickCommission = $this->getCampaignClickCommissions($context);
          if ($clickCommission == null) {
              $context->debug('        STOPPING, campaign does not have per click commission');
              return false;
          }
          $context->setCommissionTypeObject($clickCommission);
  
          if (!$this->setCurrency($context)) {
              $context->debug('        STOPPING, no default currency defined');
              return false;
          }
  
          $this->initTransactionObject($context);
  
          $context->debug('    Checking ended');
          $context->debug('');
  
          return true;
      }
  
      protected function getCampaignClickCommissions(Pap_Contexts_Click $context) {
          try {
              $context->debug('        Checking that click commission is in campaign');
  
              if($context->getCampaignObject() == null) {
                  $context->debug("    STOPPING, no campaign was recognized! ");
                  return null;
              }
              
              return $context->getCampaignObject()->getCommissionTypeObject(Pap_Common_Constants::TYPE_CLICK, '', $context->getVisit()->getCountryCode()); 
          } catch (Pap_Tracking_Exception $e) {
              $context->debug("    STOPPING, This commission type is not supported by current campaign or is NOT enabled! ");
              return null;
          }
      }
  
      protected function setCurrency(Pap_Contexts_Click $context) {
          try {
              $defaultCurrency = Gpf_Db_Currency::getDefaultCurrency();
          } catch(Gpf_Exception $e) {
              $context->debug('        ERROR, Cannot get default curency');
              return false;
          }
  
          $context->debug("    Currency set to ".$defaultCurrency->getName());
          $context->setDefaultCurrencyObject($defaultCurrency);
          return true;
      }
  
      private function initTransactionObject(Pap_Contexts_Click $context) {
          $transaction = new Pap_Common_Transaction();
  
          $transaction->setTotalCost('');
  
          $transaction->generateNewTransactionId();
          $transaction->setData1($context->getExtraDataFromRequest(1));
          $transaction->setData2($context->getExtraDataFromRequest(2));
          $transaction->set(Pap_Db_Table_Transactions::REFERER_URL, $context->getReferrerUrl());
          $transaction->set(Pap_Db_Table_Transactions::IP, $context->getIp());
          $transaction->set(Pap_Db_Table_Transactions::BROWSER, $context->getUserAgent());
          $transaction->setType(Pap_Common_Constants::TYPE_CLICK);
          $transaction->setDateInserted($context->getVisitDateTime());
          if ($context->getVisit()!= null && $context->getVisit()->getCountryCode() != '') {
              $transaction->setCountryCode($context->getVisit()->getCountryCode());
          }
          $context->setTransactionObject($transaction);
          $context->debug("Transaction object set");
      }
  }
  

} //end Pap_Tracking_Click_SaveClickCommissions

if (!class_exists('Pap_Tracking_Common_RecognizeCommGroup', false)) {
  class Pap_Tracking_Common_RecognizeCommGroup extends Gpf_Object implements Pap_Tracking_Common_Recognizer  {
  
  	private $commissionsGroup = array();
  
  	private $userCommissionGroup = array();
  	
  	public function recognize(Pap_Contexts_Tracking $context) {
  		return $this->getCommissionGroup($context);
  	}
  
  	/**
  	 * returns commission group for user (if not set already)
  	 * Commission group can be set previously in the checkCampaignType() function
  	 *
  	 */
  	protected function getCommissionGroup(Pap_Contexts_Tracking $context) {
  		$context->debug('Recognizing commission group started');
  
  		if (($user = $context->getUserObject()) == null) {
  		    $context->debug('STOPPING, user is not set - cannot find commission group');
  		    return;
  		}
  		
  		$commGroupId = $this->getUserCommissionGroupFromCache($context->getCampaignObject(), $user->getId());
  		if($commGroupId == false) {
  			$context->debug("STOPPING, Cannot find commission group for this affiliate and campaign! ".$context->getCampaignObject()->getId().' - '.$user->getId());
  			$context->setDoCommissionsSave(false);
  			$context->setDoTrackerSave(false);
  			return;
  		}
  			
  		Gpf_Plugins_Engine::extensionPoint('PostAffiliate.RecognizeCommGroup.getCommissionGroup', $context);
  
          $commissionGroup = $this->getCommissionGroupFromCache($commGroupId);
          if ($commissionGroup == null) {
          	$context->debug('    Commission group with ID '.$commGroupId . ' does not exist');
          	return;
          }
  
  		$context->setCommissionGroup($commissionGroup);
  	    $context->debug('Received commission group ID = '.$commGroupId);
  	}
  	
  	private function getUserCommissionGroupFromCache(Pap_Common_Campaign $campaign, $userId) {
  		if (isset($this->userCommissionGroup[$campaign->getId()][$userId])) {
  			return $this->userCommissionGroup[$campaign->getId()][$userId];
  		}
  		$userCommissionGroup = $campaign->getCommissionGroupForUser($userId);
  		$this->userCommissionGroup[$campaign->getId()][$userId] = $userCommissionGroup;
  		return $userCommissionGroup;
  	}
  
  	protected function getCommissionGroupFromCache($commGroupId) {
  		if (isset($this->commissionsGroup[$commGroupId])) {
  			return $this->commissionsGroup[$commGroupId];
  		}
  
  		$commissionGroup = new Pap_Db_CommissionGroup();
  		$commissionGroup->setPrimaryKeyValue($commGroupId);
  		try {
  			$commissionGroup->load();
  			$this->commissionsGroup[$commGroupId] = $commissionGroup;
  			return $commissionGroup;
  		} catch (Gpf_DbEngine_NoRowException $e) {
  		}
  		return null;
  	}
  }
  

} //end Pap_Tracking_Common_RecognizeCommGroup

if (!class_exists('Pap_Tracking_Common_RecognizeCommSettings', false)) {
  class Pap_Tracking_Common_RecognizeCommSettings extends Gpf_Object implements Pap_Tracking_Common_Recognizer {
  
  	protected $commissions = array();
  
  	/*
  	 * @var Pap_Common_UserTree
  	 */
  	protected $userTree;
  
  	public function __construct() {
  		$this->userTree = new Pap_Common_UserTree();
  	}
  
  	public function recognize(Pap_Contexts_Tracking $context, $customStatus = null) {
  		$context->debug('Recognizing commission settings started');
  
  		$campaign = $context->getCampaignObject();
  		if($campaign == null) {
  			$context->debug('    Error, campaign cannot be null!');
  			return;
  		}
  			
  		$commissionType = $context->getCommissionTypeObject();
  
  		if ($commissionType == null) {
  			$context->debug('    No commission type found for this action');
  			return;
  		}
  
  		try {
  			$commissionCollection = $this->getCommissionsCollection($context);
  			foreach($commissionCollection as $dbCommission) {
  				$commission = new Pap_Tracking_Common_Commission();
  				$commission->loadFrom($dbCommission);
  				$commission->setStatusFromType($commissionType);
  				if ($customStatus != null) {
  					$commission->setStatus($customStatus);
  				}
  				$context->addCommission($commission);
  			}
  			$context->debug('    Commission settings loaded, # of tiers: ' . $commissionCollection->getSize());
  		} catch(Exception $e) {
  			$context->debug('    EXCEPTION, STOPPING. Exception message: '.$e->getMessage());
  			return;
  		}
  			
  		$context->debug('Recognizing commission settings ended');
  		$context->debug('');
  	}
  
  	/*
       * @return Gpf_DbEngine_Row_Collection
       */
      protected function getCommissionsCollection(Pap_Contexts_Tracking $context) {
          $tier = 1;
          $currentUser = $context->getUserObject();
          $collection = new Gpf_DbEngine_Row_Collection();
  
          while($currentUser != null && $tier < 100) {
              $tierCommissions = $this->getTierCommissionCollection($context, $currentUser->getId(), $tier);
                  foreach ($tierCommissions as $dbCommission) {
                      $context->debug('Adding commission commissiontypeid: '.$dbCommission->get(Pap_Db_Table_Commissions::TYPE_ID).
                                  ', commissiongroupid: '.$dbCommission->get(Pap_Db_Table_Commissions::GROUP_ID).
                                  ', tier: '.$dbCommission->get(Pap_Db_Table_Commissions::TIER).
                                  ', subtype: '.$dbCommission->get(Pap_Db_Table_Commissions::SUBTYPE));
                      $collection->add($dbCommission);
                  }
  
              $tier++;
              $currentUser = $this->userTree->getParent($currentUser);
          }
          return $collection;
      }
  
      /**
       * @return Gpf_DbEngine_Row_Collection
       */
      private function getTierCommissionCollection(Pap_Contexts_Tracking $context, $userId, $tier) {
      	$context->debug('Loading tier commission collection for userid: ' . $userId . ' and tier: ' . $tier);
          $commissionTypeId = $context->getCommissionTypeObject()->getId();
          $groupId = $this->getCommissionGroupForUser($context->getCampaignObject(), $userId);
          $hash = $commissionTypeId.$groupId.$tier;
  
          if (isset($this->commissions[$hash])) {
          	$context->debug('Record found in cache.');
              return $this->commissions[$hash];
          }
  
          $context->debug('Trying to load commission for typeid:' . $commissionTypeId . ', groupId:' . $groupId . ',tier:' . $tier);
          $commission = new Pap_Db_Commission();
          $commission->setCommissionTypeId($commissionTypeId);
          $commission->setGroupId($groupId);
          $commission->setTier($tier);
          try {
              $commissions = $this->loadCommissionCollectionFromData($commission);
          } catch (Gpf_DbEngine_NoRowException $e) {
          	$context->debug('Error loading collection from data. returning empty collection.');
              return new Gpf_DbEngine_Row_Collection();
          }
          $context->debug('Commissions succ. loaded, saving to cache.');
          $this->commissions[$hash] = $commissions;
          return $this->commissions[$hash];
      }
  
      protected function getCommissionGroupForUser(Pap_Common_Campaign $campaign, $userId) {        
          $groupId = $campaign->getCommissionGroupForUser($userId);        
          return $groupId;
      }
      
      /**
       *
       * @return Gpf_DbEngine_Row_Collection
       */
      protected function loadCommissionCollectionFromData(Pap_Db_Commission $commission) {
          return $commission->loadCollection();
      }
  }
  

} //end Pap_Tracking_Common_RecognizeCommSettings

if (!class_exists('Pap_Common_UserTree', false)) {
  class Pap_Common_UserTree extends Gpf_Object  {
  
  	private $affiliatesInDownline = null;
  	private $parentUserCache = null;
  	
  	/**
  	 * @return array
  	 */
  	public function getChildren(Pap_Common_User $parent, $offset = '', $limit = '') {
  		$children = array();
  
  		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
  		$selectBuilder->select->addAll(Pap_Db_Table_Users::getInstance(), 'pu');
  		$selectBuilder->select->addAll(Gpf_Db_Table_AuthUsers::getInstance(), 'au');
  
  		$selectBuilder->from->add(Pap_Db_Table_Users::getName(), 'pu');
  		$selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu', 'pu.accountuserid = gu.accountuserid');
  		$selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au', 'gu.authid = au.authid');
  
  		$selectBuilder->where->add(Pap_Db_Table_Users::PARENTUSERID, '=', $parent->getId());
  		$selectBuilder->where->add(Gpf_Db_Table_Users::STATUS, 'IN', array(Pap_Common_Constants::STATUS_APPROVED, Pap_Common_Constants::STATUS_PENDING));
  		$selectBuilder->orderBy->add(Pap_Db_Table_Users::DATEINSERTED);
  
  		$selectBuilder->limit->set($offset, $limit);
  			
  		foreach ($selectBuilder->getAllRowsIterator() as $userRecord) {
  			$user = new Pap_Common_User();
  			$user->fillFromRecord($userRecord);
  			$children[] = $user;
  		}
  
  		return $children;
  	}
  
  	public function startCheckingLoops() {
  		$this->affiliatesInDownline = array();
  	}
  
  	public function stopCheckingLoops() {
  		$this->affiliatesInDownline = null;
  	}
  	/**
  	 * @return Pap_Common_User or null
  	 */
  	public function getParent(Pap_Common_User $child) {
          $parentUserId = $child->getParentUserId();
          if (!isset($this->parentUserCache[$parentUserId])) {
              if (is_array($this->affiliatesInDownline)) {
                  $this->affiliatesInDownline[] = $child->getId();
                  if (in_array($parentUserId, $this->affiliatesInDownline) || $child->getId() == $parentUserId) {
                      $child->setParentUserId('');
                      $child->save();
                      $this->parentUserCache[$parentUserId] = null;
                      return null;
                  }
              }
              $this->parentUserCache[$parentUserId] = $child->getParentUser();
          }
          return $this->parentUserCache[$parentUserId];
      }
  
  	/**
  	 * @return Pap_Common_User or null
  	 */
  	public function getChosenUser($chosenUserId) {
  		$user = new Pap_Common_User();
  		$user->setId($chosenUserId);
  		try {
  			$user->load();
  			return $user;
  		} catch (Gpf_Exception $e) {
  			return null;
  		}
  	}
  }
  

} //end Pap_Common_UserTree

if (!class_exists('Pap_Tracking_Common_UpdateAllCommissions', false)) {
  class Pap_Tracking_Common_UpdateAllCommissions extends Pap_Tracking_Common_SaveAllCommissions implements Pap_Tracking_Common_Saver {
  
      /*
       * @var array<Pap_Common_Transaction>
       */
      protected $transactions = array();
  
      public function process(Pap_Contexts_Tracking $context) {
          parent::save($context);
      }
  
      public function saveChanges() {
          foreach ($this->transactions as $transaction) {
              $transaction->save();
          }
      }
  
      protected function saveTransaction(Pap_Common_Transaction $transaction, $dateInserted) {
          try {
              $transactionClone = $this->getCachedTransaction($transaction);
  
              $transaction->setId($transactionClone->getId());
              $transaction->setPersistent($transactionClone->isPersistent());
               
              $transaction->setClickCount($transaction->getClickCount()
              + $transactionClone->getClickCount());
              $transaction->setCommission($transaction->getCommission()
              + $transactionClone->getCommission());
              $transaction->setDateInserted($dateInserted);
          } catch (Gpf_DbEngine_NoRowException $e) {
          }
          $this->transactions[$this->hashTransaction($transaction)] = $transaction;
      }
  
      /**
       * @throws Gpf_DbEngine_NoRowException
       * @return Pap_Common_Transcation
       */
      protected function getClonedTransactionFromDb(Pap_Common_Transaction $transaction) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->select->addAll(Pap_Db_Table_Transactions::getInstance());
          $select->from->add(Pap_Db_Table_Transactions::getName());
          $select->where->add(Pap_Db_Table_Transactions::USER_ID, '=', $transaction->getUserId());
          $select->where->add(Pap_Db_Table_Transactions::CAMPAIGN_ID, '=', $transaction->getCampaignId());
          $select->where->add(Pap_Db_Table_Transactions::BANNER_ID, '=', $transaction->getBannerId());
          $select->where->add(Pap_Db_Table_Transactions::CHANNEL, '=', $transaction->getChannel());
          $select->where->add(Pap_Db_Table_Transactions::R_STATUS, '=', $transaction->getStatus());
          $select->where->add(Pap_Db_Table_Transactions::TIER, '=', $transaction->getTier());
          $select->where->add(Pap_Db_Table_Transactions::R_TYPE, '=', $transaction->getType());
          $select->where->add(Pap_Db_Table_Transactions::COUNTRY_CODE, '=', $transaction->getCountryCode());
          $select->where->add(Pap_Db_Table_Transactions::PAYOUT_STATUS, '=', $transaction->getPayoutStatus());
          $select->where->add(Pap_Db_Table_Transactions::DATE_INSERTED, 'like', $this->dateTimeToDate($transaction->getDateInserted()).'%');
          $select->orderBy->add(Pap_Db_Table_Transactions::DATE_INSERTED, false);
          $select->limit->set(0, 1);
  
          $transaction = new Pap_Common_Transaction();
          $transaction->fillFromRecord($select->getOneRow());
          return $transaction;
      }
  
      /**
       * @return Pap_Common_Transcation
       */
      private function getCachedTransaction(Pap_Common_Transaction $transaction) {
          $hash = $this->hashTransaction($transaction);
  
          if (isset($this->transactions[$hash])) {
              return $this->transactions[$hash];
          }
  
          $transactionClone = $this->getClonedTransactionFromDb($transaction);
          $transactionClone->setPersistent(true);
  
          return $transactionClone;
      }
  
  
  
      private function hashTransaction(Pap_Common_Transaction $transaction) {
          return $transaction->getUserId(). $transaction->getCampaignId(). $transaction->getBannerId(). $transaction->getChannel() .
          $transaction->getStatus() . $transaction->getTier(). $transaction->getType(). $transaction->getPayoutStatus() . $this->dateTimeToDate($transaction->getDateInserted() . $transaction->getCountryCode());
      }
  
      private function dateTimeToDate($dateTime) {
          return substr($dateTime, 0, strpos($dateTime, ' '));
      }
  }
  
  

} //end Pap_Tracking_Common_UpdateAllCommissions

if (!class_exists('Pap_Tracking_Common_SaveAllCommissions', false)) {
  class Pap_Tracking_Common_SaveAllCommissions extends Gpf_Object {
  
      /*
       * @var Pap_Common_UserTree
       */
      protected $userTree;
  
      public function __construct() {
          $this->userTree = new Pap_Common_UserTree();
      }
  
      public function save(Pap_Contexts_Tracking $context) {
  		$context->debug('Saving commissions started');
  
  		$tier = 1;
  		$currentUser = $context->getUserObject();
  		$currentCommission = $context->getCommission($tier);
  
  		while($currentUser != null && $tier < 100) {
  			if ($currentCommission != null) {
  				if ($currentUser->getStatus() != null && $currentUser->getStatus() != 'D') {
  					$this->saveCommission($context, $currentUser, $currentCommission);
  				} else {
  					$context->debug('Commission is not saved, because user is declined');
                  }
  			} else {
  			    $context->debug('Commission is not saved, because it is not defined in campaign or user has no multi tier commission');
  			}
  		    Gpf_Plugins_Engine::extensionPoint('Tracker.saveAllCommissions', new Pap_Common_SaveCommissionCompoundContext($context, $tier, $currentUser, $this ));
  
  			$tier++;
  			$currentUser = $this->userTree->getParent($currentUser);
  			$currentCommission = $context->getCommission($tier);
  		}
  
  		Gpf_Plugins_Engine::extensionPoint('Tracker.saveCommissions', $context);
  
  		$context->debug('Saving commissions ended');
  		$context->debug("");
      }
  
     public function saveCommission(Pap_Contexts_Tracking $context, Pap_Common_User $user, Pap_Tracking_Common_Commission $commission) {
          $context->debug('Saving '.$context->getActionType().' commission started');
  
          $transaction = $context->getTransaction($commission->getTier());
          if ($transaction == null) {
              $transaction = clone $context->getTransaction(1);
              $transaction->setPersistent(false);
              $transaction->setId($transaction->generateNewTransactionId());
          }
          if (($parentTransaction = $context->getTransaction($commission->getTier() - 1)) != null) {
              $transaction->setParentTransactionId($parentTransaction->getId());
          }
          if (($channel = $context->getChannelObject()) != null) {
              $transaction->setChannel($channel->getId());
          }
  
          $transaction->setTier($commission->getTier());
          $transaction->setUserId($user->getId());
          $transaction->setCampaignId($context->getCampaignObject()->getId());
          $transaction->setAccountId($context->getAccountId());
  
          $banner = $context->getBannerObject();
          if (!is_null($banner)) {
              $transaction->setBannerId($banner->getId());
          }
  
          if ($user->getStatus() == 'P') {
          	$transaction->setStatus('P');
          	$context->debug('Commission is saved as pending because user state is in pending');
          } else {
              $transaction->setStatus($commission->getStatus());
          }
          $transaction->setPayoutStatus(Pap_Common_Transaction::PAYOUT_UNPAID);
          $transaction->setCommissionTypeId($context->getCommissionTypeObject()->getId());
          $transaction->setCountryCode(($context->getVisit()!=null)?$context->getVisit()->getCountryCode():'');
          $transaction->setType($context->getCommissionTypeObject()->getType());
          $transaction->setCommission($commission->getCommission($context->getRealTotalCost()-$context->getFixedCost()));
          $context->debug('  Computed commission is: '.$transaction->getCommission());
          $transaction->setClickCount(1);
          $transaction->setLogGroupId($context->getLoggerGroupId());
  
          if ($transaction->getTier() == 1) {
              $transaction->setSaleId($transaction->getId());
          } else {
              $transaction->setSaleId($context->getTransaction(1)->getSaleId());
          }
  
          //check if we can save zero commission
          if ($transaction->getCommission() == 0 &&
          $context->getCommissionTypeObject()->getSaveZeroCommissions() != Gpf::YES) {
              $context->debug('  Saving of commission transaction was STOPPED. Saving of zero commissions is disabled. Trans id: '.$transaction->getId());
              return Gpf_Plugins_Engine::PROCESS_CONTINUE;
          }
  
  
          $transactionCompoundContext = new Pap_Common_TransactionCompoundContext($transaction, $context);
          Gpf_Plugins_Engine::extensionPoint('Tracker.saveCommissions.beforeSaveTransaction', $transactionCompoundContext);
          if (!$transactionCompoundContext->getSaveTransaction()) {
              $context->debug('  Saving of commission transaction was STOPPED by plugin. Trans id: '.$transaction->getId());
              return Gpf_Plugins_Engine::PROCESS_CONTINUE;
          }
  
          $this->saveTransaction($transaction, $context->getVisitDateTime());
          $context->setTransactionObject($transaction, $commission->getTier());
  
          $context->debug('    Commission transaction was successfuly saved with ID: '.$transaction->getId());
          $context->debug('Saving '.$context->getActionType().' commission ended');
          $context->debug('');
  
          return Gpf_Plugins_Engine::PROCESS_CONTINUE;
      }
  
      protected function saveTransaction(Pap_Db_Transaction $transaction, $dateInserted) {
          $transaction->setDateInserted($dateInserted);
      	$transaction->save();
      }
  }
  
  
  

} //end Pap_Tracking_Common_SaveAllCommissions

if (!class_exists('Pap_Tracking_Click_SaveVisitorAffiliate', false)) {
  class Pap_Tracking_Click_SaveVisitorAffiliate extends Gpf_Object implements Pap_Tracking_Common_Saver {
  
      /**
       * @var Pap_Tracking_Visit_VisitorAffiliateCache
       */
      protected $visitorAffiliateCache;
  
      private $overwriteSettings = array();
  
      /**
       * Pap_Tracking_Cookie
       */
      protected $cookieObject;
  
      public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
          $this->cookieObject = new Pap_Tracking_Cookie();
          $this->visitorAffiliateCache = $visitorAffiliateCache;
      }
  
      public function saveChanges() {
      }
  
      public function process(Pap_Contexts_Tracking $context) {
          $context->debug('Preparing for save visitor affiliate');
  
          $cacheCompoundContex = new Pap_Common_VisitorAffiliateCacheCompoundContext($this->visitorAffiliateCache, $context);
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.click.beforeSaveVisitorAffiliate', $cacheCompoundContex);
  
          if ($cacheCompoundContex->getVisitorAffiliateAlreadySaved()) {
              $context->debug('VisitorAffiliate already set by plugins, not saving');
              return;
          }
          
          Pap_Tracking_Common_VisitorAffiliateCheckCompatibility::getHandlerInstance()->checkCompatibility($context->getVisitorId(), $this->visitorAffiliateCache);
  
          $rows = $this->visitorAffiliateCache->getVisitorAffiliateAllRows($context->getVisitorId());
          
          $context->debug('Found ' . $rows->getSize() . ' records in visitorAffiliates');
          switch ($rows->getSize()) {
              case 0:
                  $visitorAffiliate = $this->createAndPrepareVisitorAffiliate($context);
                  $visitorAffiliate->setActual(true);
                  $rows->add($visitorAffiliate);
                  $context->debug('Saving first visitorAffiliate '.$visitorAffiliate->toString());
                  break;
              case 1:
                  $lastVisit = $this->createAndPrepareVisitorAffiliate($context);
                  if ($this->isOverWriteEnabled($context) || !$rows->get(0)->isValid()) {
                      $rows->get(0)->setActual(false);
                      $lastVisit->setActual(true);
                  }
                  $context->debug('Adding second visitorAffiliate '.$lastVisit->toString());
                  $rows->add($lastVisit);
                  break;
              case 2:
                  if ($this->isOverWriteEnabled($context) || ($rows->get(0)->isActual() && !$rows->get(0)->isValid())) {
                      $rows->get(0)->setActual(false);
                      $this->prepareVisitorAffiliate($rows->get(1), $context);
                      $rows->get(1)->setActual(true);
                      $context->debug('Overwrting second visitor affilite '.$rows->get(1)->toString());
                  } else {
                      if ($rows->get(1)->isActual() && $rows->get(1)->isValid()) {
                          $rows->add($this->createAndPrepareVisitorAffiliate($context));
                          $context->debug('Adding third (last) visitor affiliate '.$rows->get(1)->toString());
                      } else {
                          $this->prepareVisitorAffiliate($rows->get(1), $context);
                          $context->debug('Overwriting second visitor affiliate '.$rows->get(1)->toString());
                      }
                  }
                  break;
              case 3:
                  if ($this->isOverWriteEnabled($context) || ($rows->get(1)->isActual() && !$rows->get(1)->isValid())) {
                      for ($i = 1; $i <=2; $i++) {
                          if ($rows->get($i)->isPersistent()) {
                              $rows->get($i)->delete();
                              $context->debug('Deleting '.$i.' visitoraffiliate ' . $rows->get($i)->toString());
                          }
                          $rows->remove($i);
                      }
                      $rows->correctIndexes();
                      $lastVisit = $this->createAndPrepareVisitorAffiliate($context);
                      $lastVisit->setActual(true);
                      $rows->add($lastVisit);
                      $context->debug('Adding third (last) visitor affiliate '.$lastVisit->toString());
                  } else {
                      $this->prepareVisitorAffiliate($rows->get(2), $context);
                      $context->debug('Overwriting third (last) visitor affiliate '.$rows->get(2)->toString());
                  }
                  break;
              default:
                  $context->error('Too many rows per visitor in visitor affiliates table');
                  break;
          }
  
          $this->checkActualSelected($rows);
  
          $context->debug('Finished saving visitor affiliate');
          $context->debug('');
      }
  
      private function checkActualSelected(Pap_Tracking_Common_VisitorAffiliateCollection $rows) {
          $actual = false;
          foreach ($rows as $row) {
              $actual = $actual || $row->isActual();
          }
          if (!$actual) {
              $rows->get($rows->getSize()-1)->setActual();
          }
      }
  
      protected function createAndPrepareVisitorAffiliate(Pap_Contexts_Tracking $context) {
          $visitorAffiliate = $this->visitorAffiliateCache->createVisitorAffiliate($context->getVisitorId());
          $this->prepareVisitorAffiliate($visitorAffiliate, $context);
          return $visitorAffiliate;
      }
  
      public static function prepareVisitorAffiliate(Pap_Db_VisitorAffiliate $visitorAffiliate, Pap_Contexts_Tracking $context) {
          $visitorAffiliate->setUserId($context->getUserObject()->getId());
  
          if ($context->getBannerObject() != null) {
              $visitorAffiliate->setBannerId($context->getBannerObject()->getId());
          } else {
              $visitorAffiliate->setBannerId(null);
          }
          
          if ($context->getChannelObject() != null) {
              $visitorAffiliate->setChannelId($context->getChannelObject()->getId());
          }
  
          $visitorAffiliate->setCampaignId($context->getCampaignObject()->getId());
          $visitorAffiliate->setIp($context->getIp());
          $visitorAffiliate->setDateVisit($context->getDateCreated());
          $visitorAffiliate->setReferrerUrl($context->getReferrerUrl());
          $visitorAffiliate->setData1($context->getExtraDataFromRequest(1));
          $visitorAffiliate->setData2($context->getExtraDataFromRequest(2));
          $visitorAffiliate->setValidTo(self::getVisitorAffiliateValidity($context, $visitorAffiliate));
      }
      
      public static function getVisitorAffiliateValidity(Pap_Contexts_Tracking $context, Pap_Db_VisitorAffiliate $visitorAffiliate) {
          return Gpf_Common_DateUtils::addDateUnit($visitorAffiliate->getDateVisit(),
              Pap_Tracking_Cookie::getCookieLifeTimeInDays($context),
              Gpf_Common_DateUtils::DAY);
      }
  
      private function isOverWriteEnabled(Pap_Contexts_Click $context) {
          $key = '';
          if ($context->getCampaignObject() != null) {
              $key .= $context->getCampaignObject()->getId();
          }
          $key .= '_';
          if ($context->getUserObject() != null) {
              $key .= $context->getUserObject()->getId();
          }
           
          if (!isset($this->overwriteSettings[$key])) {
              $this->overwriteSettings[$key] =
              $this->cookieObject->isOverwriteEnabled($context->getCampaignObject(), $context->getUserObject());
          }
          return $this->overwriteSettings[$key];
      }
  }
  

} //end Pap_Tracking_Click_SaveVisitorAffiliate

if (!class_exists('Pap_Tracking_Click_FraudProtection', false)) {
  class Pap_Tracking_Click_FraudProtection extends Gpf_Object {
  
      const ACTION_DECLINE = 'D';
      const ACTION_DONTSAVE = 'DS';
  
      /**
       * checks for click fraud rules...
       *
       * @param Pap_Contexts_Click $context
       */
      public function check(Pap_Contexts_Click $context) {
          $context->debug('FraudProtection started');
  
          $this->checkBannedIP($context);
          $this->checkMultipleClicksFromSameIP($context);
  
          Gpf_Plugins_Engine::extensionPoint('FraudProtection.Click.check', $context);
  
          $context->debug('FraudProtection ended');
      }
  
  
      /**
       * checks for banned IP
       *
       * @param Pap_Contexts_Click $context
       * @return string
       */
      private function checkBannedIP(Pap_Contexts_Click $context) {
          if(Gpf_Settings::get(Pap_Settings::BANNEDIPS_CLICKS) != Gpf::YES) {
              $context->debug('Check for banned IP address is not turned on');
              return true;
          }
  
          $context->debug('Checking banned IP started');
  
  
          $bannedIPAddresses = Gpf_Net_Ip::getBannedIPAddresses(Pap_Settings::BANNEDIPS_LIST_CLICKS);
  
          if($bannedIPAddresses === false) {
              $context->debug('List of banned IP addresses is invalid or empty, stop checking');
              return true;
          }
  
          $checkAction = Gpf_Settings::get(Pap_Settings::BANNEDIPS_CLICKS_ACTION);
          if($checkAction != self::ACTION_DECLINE && $checkAction != self::ACTION_DONTSAVE) {
              $context->debug("Action after check is not correct: '$checkAction'");
              return true;
          }
  
  
          $ip = $context->getVisit()->getIp();
  
          if(Gpf_Net_Ip::ipMatchRange($ip, $bannedIPAddresses)) {
              if($checkAction == self::ACTION_DONTSAVE) {
                  $context->debug("    STOPPING (setting setDoTrackerSave(false), IP: $ip is banned");
                  $context->setDoTrackerSave(false);
                  $context->debug('      Checking banned IP endeded');
                  return false;
  
              } else {
                  $context->debug("  DECLINING, IP: $ip is banned");
  
                  $this->declineClick($context);
  
                  $context->debug('      Checking banned IP endeded');
                  return true;
              }
          } else {
              $context->debug("    IP: $ip is not banned");
          }
  
          $context->debug('      Checking banned IP endeded');
          return true;
      }
  
  
      /**
       * checks for duplicate records from same IP
       *
       * @param Pap_Contexts_Click $context
       * @return string
       */
      private function checkMultipleClicksFromSameIP(Pap_Contexts_Click $context) {
          if(Gpf_Settings::get(Pap_Settings::REPEATING_CLICKS_SETTING_NAME) != Gpf::YES) {
              $context->debug('    Check for duplicate clicks with the same IP is not turned on');
              return true;
          }
  
          $context->debug('    Checking duplicate clicks from the same IP started');
  
          $checkPeriod = Gpf_Settings::get(Pap_Settings::REPEATING_CLICKS_SECONDS_SETTING_NAME);
          $checkAction = Gpf_Settings::get(Pap_Settings::REPEATING_CLICKS_ACTION_SETTING_NAME);
          if($checkPeriod == '' || $checkPeriod == '0' || !is_numeric($checkPeriod)) {
              $context->debug("Checking period is not correct: '$checkPeriod'");
              return true;
          }
          if($checkAction != self::ACTION_DECLINE && $checkAction != self::ACTION_DONTSAVE) {
              $context->debug("Action after check is not correct: '$checkAction'");
              return true;
          }
  
          $ip = $context->getVisit()->getIp();
          $clickObject = new Pap_Db_RawClick();
  
          //only clicks on same banner will be fraudulent
          $bannerId = false;
          if (Gpf_Settings::get(Pap_Settings::REPEATING_BANNER_CLICKS) == Gpf::YES) {
              $bannerId = $context->getBannerId();
              if (!strlen($bannerId)) {
                  $bannerId = false;
              }
          }
  
          $recordsCount = $clickObject->getNumberOfClicksFromSameIP($ip, $checkPeriod, $bannerId, $context->getVisitDateTime());
          if($recordsCount > 0) {
              if($checkAction == self::ACTION_DONTSAVE) {
                  $context->debug("    STOPPING (setting setDoTrackerSave(false), found another clicks from the same IP: $ip within $checkPeriod seconds");
                  $context->setDoTrackerSave(false);
                  $context->debug('      Checking duplicate clicks from the same IP endeded');
                  return false;
  
              } else {
                  $context->debug("  DECLINING, found another clicks from the same IP: $ip within $checkPeriod seconds");
  
                  $this->declineClick($context);
  
                  $context->debug('      Checking duplicate clicks from the same IP endeded');
                  return true;
              }
          } else {
              $context->debug("    No duplicate clicks from the same IP: $ip found");
          }
  
          $context->debug('      Checking duplicate clicks from the same IP endeded');
          return true;
      }
  
      /**
       * Sets status of transaction to declined and sets it's message
       *
       * @param Pap_Plugins_Tracking_Action_Context $context
       * @param string $checkMessage
       */
      private function declineClick(Pap_Contexts_Click $context) {
          $context->setClickStatus(Pap_Db_ClickImpression::STATUS_DECLINED);
      }
  }
  

} //end Pap_Tracking_Click_FraudProtection

if (!class_exists('Pap_Tracking_Action_ActionProcessor', false)) {
  class Pap_Tracking_Action_ActionProcessor extends Gpf_Object implements Pap_Tracking_Common_VisitProcessor {
      
      /*
       * @var Pap_Tracking_Common_RecognizeAccountId
       */
      private $accountRecognizer;
      
      /*
       * array<Pap_Tracking_Common_Recognizer>
       */
      private $paramRecognizers = array();
  
      /*
       * array<Pap_Tracking_Common_Recognizer>
       */
      private $settingLoaders = array();
  
      /*
       * array<Pap_Tracking_Common_Recognizer>
       */
      private $recognizers = array();
  
      /*
       * @var Pap_Tracking_Common_SaveAllCommissions
       */
      private $saveAllCommissionsSaver;
  
      /**
       * @var Gpf_Rpc_Json
       */
      private $json;
  
      /**
       * @var Pap_Tracking_Action_FraudProtection
       */
      private $fraudProtectionObj;
  
      /**
       * @var Pap_Tracking_Visit_VisitorAffiliateCache
       */
      private $visitorAffiliateCache;
  
      public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
          $this->saveAllCommissionsSaver = new Pap_Tracking_Common_SaveAllCommissions();
          $this->visitorAffiliateCache = $visitorAffiliateCache;
          $this->json = new Gpf_Rpc_Json();
  
          $this->accountRecognizer = new Pap_Tracking_Common_RecognizeAccountId();
  
          $this->paramRecognizers[] = new Pap_Tracking_Action_RecognizeVisitorAffiliateFromVisitorId($this->visitorAffiliateCache);
          $this->paramRecognizers[] = new Pap_Tracking_Action_RecognizeVisitorAffiliateFromIp($this->visitorAffiliateCache);
          $this->paramRecognizers[] = new Pap_Tracking_Action_RecognizeAffiliate($this->visitorAffiliateCache);
          $this->paramRecognizers[] = new Pap_Tracking_Action_RecognizeCampaign();
          $this->paramRecognizers[] = new Pap_Tracking_Action_RecognizeBanner();
          $this->paramRecognizers[] = new Pap_Tracking_Action_RecognizeChannel();
  
          $this->fraudProtectionObj = new Pap_Tracking_Action_FraudProtection();
  
          $this->recognizers[] = new Pap_Tracking_Action_RecognizeCommType();
  
          $this->settingLoaders[] = new Pap_Tracking_Common_RecognizeCommGroup();
          $this->settingLoaders[] = new Pap_Tracking_Common_RecognizeCommSettings();
          $this->settingLoaders[] = new Pap_Tracking_Action_ComputeCommissions();
          $this->settingLoaders[] = new Pap_Tracking_Action_ComputeStatus();
      }
  
      /**
       * @return Pap_Tracking_Visit_VisitorAffiliateCache
       */
      public function getVisitorAffiliatesCache() {
          return $this->visitorAffiliateCache;
      }
  
      public function process(Pap_Db_Visit $visit) {
      	Gpf_Log::debug('Action processor processing...');
          $accountContext = $this->processAccount($visit);        
          if (!$accountContext->getDoTrackerSave()) {
          	Gpf_Log::debug('Saving disabled because of account problems.');
              return;
          }
          $this->visitorAffiliateCache->setAccountId($accountContext->getAccountId());
  
          try {
              $actions = $this->loadActions($visit->getSaleParams());
          } catch (Gpf_Exception $e) {
              Gpf_Log::debug('Action processor: ' . $e->getMessage());
              return;
          }
  
          foreach ($actions as $action) {
              $context = new Pap_Contexts_Action($action, $visit);
              $context->debug('Saving sale/action for visit: '.$visit->toText());
              $context->setDoCommissionsSave(true);
              $context->setAccountId($accountContext->getAccountId(), $accountContext->getAccountRecognizeMethod());
              
              try {
                  $this->processAction($context);
              } catch (Gpf_Exception $e) {
                  $context->debug("Saving commission interrupted: ".$e->getMessage());
              }
          }
      }
  
      public function runSettingLoadersAndSaveCommissions(Pap_Contexts_Action $context) {
          $context->setDoCommissionsSave(true);
          $context->setDoTrackerSave(true);
          $this->runRecognizers($context, $this->settingLoaders,
              'Commission save disabled in load settings.');
          $this->prepareContextForSave($context);
  
          $this->saveAllCommissionsSaver->save($context);
      }
          
      /**
       *
       * @param Pap_Db_Visit $visit
       * @return Pap_Contexts_Action
       */
      private function processAccount(Pap_Db_Visit $visit) {
          $context = new Pap_Contexts_Action();
          $context->setVisit($visit);
          $context->setDoCommissionsSave(true);
          $context->setDoTrackerSave(true);
  
          $this->accountRecognizer->recognize($context);
          Gpf_Plugins_Engine::extensionPoint('Pap_Tracking_Action_ActionProcessor.processAccount', $context);
          return $context;
      }
  
      /**
       * @param Pap_Contexts_Action $context
       * @throws Gpf_Exception
       */
      private function processAction(Pap_Contexts_Action $context) {
          $visitorAffiliateCacheCompoundContext = new Pap_Common_VisitorAffiliateCacheCompoundContext($this->visitorAffiliateCache,
          $context);
  
          Gpf_Plugins_Engine::extensionPoint('Tracker.action.recognizeParametersStarted', $visitorAffiliateCacheCompoundContext);
  
          $this->runRecognizers($context, $this->paramRecognizers,
              'Commission save disabled in recognize parameters.');
  
          $this->fraudProtection($context);
  
          Gpf_Plugins_Engine::extensionPoint('Tracker.action.recognizeAfterFraudProtection', $visitorAffiliateCacheCompoundContext); 
          
          $this->runRecognizers($context, $this->recognizers,
              'Commission save disabled in recognize parameters - second part.');
  
          $this->runRecognizers($context, $this->settingLoaders,
              'Commission save disabled in load settings.');
          Gpf_Plugins_Engine::extensionPoint('Tracker.action.recognizeParametersEnded', $visitorAffiliateCacheCompoundContext);
  
          $this->saveCommissions($context);
  
          $this->deleteCookies($context);
      }
  
      private function fraudProtection(Pap_Contexts_Action $context) {
          $this->fraudProtectionObj->check($context);
          if (!$context->getDoCommissionsSave()) {
              throw new Gpf_Exception("Commission save disabled by fraud protection.");
          }
      }
  
      private function runRecognizers(Pap_Contexts_Action $context, array $recognizers, $stopMessage) {
          foreach ($recognizers as $recognizer) {
              $recognizer->recognize($context);
              if (!$context->getDoCommissionsSave()) {
                  throw new Gpf_Exception($stopMessage);
              }
          }
      }
  
      private function deleteCookies(Pap_Contexts_Action $context) {
          if (Gpf_Settings::get(Pap_Settings::DELETE_COOKIE) != Gpf::YES) {
              return;
          }
  
          $visitorAffiliates = $this->visitorAffiliateCache->getVisitorAffiliateAllRows($context->getVisitorId());
          foreach ($visitorAffiliates as $visitorAffiliate) {
              $this->visitorAffiliateCache->removeVisitorAffiliate($visitorAffiliate->getId());
          }
      }
  
      private function loadActions($actionsString) {
          if ($actionsString == '') {
              throw new Gpf_Exception($this->_('no actions in visit'));
          }
          $actionsArray = $this->json->decode($actionsString);
          if (!is_array($actionsArray)) {
              throw new Gpf_Exception($this->_('invalid action format (%s)', $actionsString));
          }
          $actions = array();
          foreach ($actionsArray as $actionObject) {
              $actions[] = new Pap_Tracking_Action_RequestActionObject($actionObject);
          }
          return $actions;
      }
  
      public function saveChanges() {
      }
  
      private function saveCommissions(Pap_Contexts_Action $context) {
          $context->debug('Saving commissions started');
          Gpf_Plugins_Engine::extensionPoint('Tracker.action.beforeSaveCommissions', $context);
          if (!$context->getDoCommissionsSave()) {
              $context->debug('Commissions save stopped by plugin.');
              return;
          }
          $this->saveCommission($context);
  
          Gpf_Plugins_Engine::extensionPoint('Tracker.action.afterSaveCommissions', $context);
  
          $context->debug("Saving commissions ended");
      }
  
      protected function prepareContextForSave(Pap_Contexts_Action $context) {
          $transaction = $context->getTransaction();
          $transaction->setOrderId($context->getOrderIdFromRequest());
          $transaction->setProductId($context->getProductIdFromRequest());
          $transaction->setTotalCost($context->getRealTotalCost());
          $transaction->setFixedCost($context->getFixedCost());
          $transaction->setCountryCode($context->getCountryCode());
  
          if($context->getChannelObject() !== null) {
              $transaction->setChannel($context->getChannelObject()->getId());
          }
          if($context->getBannerObject() !== null) {
              $transaction->setBannerId($context->getBannerObject()->getId());
          }
  
          $transaction->setData1($context->getExtraDataFromRequest(1));
          $transaction->setData2($context->getExtraDataFromRequest(2));
          $transaction->setData3($context->getExtraDataFromRequest(3));
          $transaction->setData4($context->getExtraDataFromRequest(4));
          $transaction->setData5($context->getExtraDataFromRequest(5));
  
          $transaction->setDateInserted($context->getVisitDateTime());
  
          $transaction->setVisitorId($context->getVisitorId());
          $transaction->setTrackMethod($context->getTrackingMethod());
          $transaction->setIp($context->getIp());
          try {
              $transaction->setRefererUrl($context->getVisitorAffiliate()->getReferrerUrl());
          } catch (Gpf_Exception $e) {
              $transaction->setRefererUrl($context->getReferrerUrl());
          }
  
          try {
              $visitorId = $context->getVisitorAffiliate()->getVisitorId();
          } catch (Exception $e) {
              $visitorId = $this->_('unknown');
          }
          
          try {
              $this->setFirstAndLastClick($transaction, $this->getVisitorAffiliatesCollection($context));
          } catch (Gpf_Exception $e) {
              $context->debug('First and Last click can not be recognized for visitorId: ' . $visitorId . '. ' . $e->getMessage());
          }
      }
  
      /**
       * @throws Gpf_Exception
       * @return Pap_Tracking_Common_VisitorAffiliateCollection
       */
      protected function getVisitorAffiliatesCollection(Pap_Contexts_Action $context) {
          return $this->visitorAffiliateCache->getVisitorAffiliateAllRows($context->getVisitorAffiliate()->getVisitorId());
      }
  
      private function saveCommission(Pap_Contexts_Action $context) {
          $this->prepareContextForSave($context);
  
          $actionProcessorCompoundContext = new Pap_Common_ActionProcessorCompoundContext($context, $this);
          Gpf_Plugins_Engine::extensionPoint('Tracker.action.saveCommissions', $actionProcessorCompoundContext);
          if ($actionProcessorCompoundContext->getCommissionsAlreadySaved()) {
              return;
          }
          $this->saveAllCommissionsSaver->save($context);
      }
  
      protected function setFirstAndLastClick(Pap_Common_Transaction $transaction, Pap_Tracking_Common_VisitorAffiliateCollection $collection) {
          if ($collection->getSize() == 0) {
              throw new Gpf_Exception('VisitorAffiliates for this visitor are empty');
          }
          
          $firstVisitorAffiliate = $collection->get(0);
          $transaction->setFirstClickTime($firstVisitorAffiliate->getDateVisit());
          $transaction->setFirstClickReferer($firstVisitorAffiliate->getReferrerUrl());
          $transaction->setFirstClickIp($firstVisitorAffiliate->getIp());
          $transaction->setFirstClickData1($firstVisitorAffiliate->getData1());
          $transaction->setFirstClickData2($firstVisitorAffiliate->getData2());
  
          $lastVisitorAffiliate = $collection->get($collection->getSize()-1);
          $transaction->setLastClickTime($lastVisitorAffiliate->getDateVisit());
          $transaction->setLastClickReferer($lastVisitorAffiliate->getReferrerUrl());
          $transaction->setLastClickIp($lastVisitorAffiliate->getIp());
          $transaction->setLastClickData1($lastVisitorAffiliate->getData1());
          $transaction->setLastClickData2($lastVisitorAffiliate->getData2());
      }
  }
  

} //end Pap_Tracking_Action_ActionProcessor

if (!class_exists('Gpf_Rpc_Json', false)) {
  class Gpf_Rpc_Json implements Gpf_Rpc_DataEncoder, Gpf_Rpc_DataDecoder {
      /**
       * Marker constant for Services_JSON::decode(), used to flag stack state
       */
      const SERVICES_JSON_SLICE = 1;
  
      /**
       * Marker constant for Services_JSON::decode(), used to flag stack state
       */
      const SERVICES_JSON_IN_STR = 2;
  
      /**
       * Marker constant for Services_JSON::decode(), used to flag stack state
       */
      const SERVICES_JSON_IN_ARR = 3;
  
      /**
       * Marker constant for Services_JSON::decode(), used to flag stack state
       */
      const SERVICES_JSON_IN_OBJ = 4;
  
      /**
       * Marker constant for Services_JSON::decode(), used to flag stack state
       */
      const SERVICES_JSON_IN_CMT = 5;
  
      /**
       * Behavior switch for Services_JSON::decode()
       */
      const SERVICES_JSON_LOOSE_TYPE = 16;
  
      /**
       * Behavior switch for Services_JSON::decode()
       */
      const SERVICES_JSON_SUPPRESS_ERRORS = 32;
  
      /**
       * constructs a new JSON instance
       *
       * @param    int     $use    object behavior flags; combine with boolean-OR
       *
       *                           possible values:
       *                           - SERVICES_JSON_LOOSE_TYPE:  loose typing.
       *                                   "{...}" syntax creates associative arrays
       *                                   instead of objects in decode().
       *                           - SERVICES_JSON_SUPPRESS_ERRORS:  error suppression.
       *                                   Values which can't be encoded (e.g. resources)
       *                                   appear as NULL instead of throwing errors.
       *                                   By default, a deeply-nested resource will
       *                                   bubble up with an error, so all return values
       *                                   from encode() should be checked with isError()
       */
      function __construct($use = 0)
      {
          $this->use = $use;
      }
  
      /**
       * convert a string from one UTF-16 char to one UTF-8 char
       *
       * Normally should be handled by mb_convert_encoding, but
       * provides a slower PHP-only method for installations
       * that lack the multibye string extension.
       *
       * @param    string  $utf16  UTF-16 character
       * @return   string  UTF-8 character
       * @access   private
       */
      function utf162utf8($utf16)
      {
          // oh please oh please oh please oh please oh please
          if(Gpf_Php::isFunctionEnabled('mb_convert_encoding')) {
              return mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
          }
  
          $bytes = (ord($utf16{0}) << 8) | ord($utf16{1});
  
          switch(true) {
              case ((0x7F & $bytes) == $bytes):
                  // this case should never be reached, because we are in ASCII range
                  // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                  return chr(0x7F & $bytes);
  
              case (0x07FF & $bytes) == $bytes:
                  // return a 2-byte UTF-8 character
                  // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                  return chr(0xC0 | (($bytes >> 6) & 0x1F))
                  . chr(0x80 | ($bytes & 0x3F));
  
              case (0xFFFF & $bytes) == $bytes:
                  // return a 3-byte UTF-8 character
                  // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                  return chr(0xE0 | (($bytes >> 12) & 0x0F))
                  . chr(0x80 | (($bytes >> 6) & 0x3F))
                  . chr(0x80 | ($bytes & 0x3F));
          }
  
          // ignoring UTF-32 for now, sorry
          return '';
      }
  
      /**
       * convert a string from one UTF-8 char to one UTF-16 char
       *
       * Normally should be handled by mb_convert_encoding, but
       * provides a slower PHP-only method for installations
       * that lack the multibye string extension.
       *
       * @param    string  $utf8   UTF-8 character
       * @return   string  UTF-16 character
       * @access   private
       */
      function utf82utf16($utf8)
      {
          // oh please oh please oh please oh please oh please
          if(Gpf_Php::isFunctionEnabled('mb_convert_encoding')) {
              return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
          }
  
          switch(strlen($utf8)) {
              case 1:
                  // this case should never be reached, because we are in ASCII range
                  // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                  return $utf8;
  
              case 2:
                  // return a UTF-16 character from a 2-byte UTF-8 char
                  // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                  return chr(0x07 & (ord($utf8{0}) >> 2))
                  . chr((0xC0 & (ord($utf8{0}) << 6))
                  | (0x3F & ord($utf8{1})));
  
              case 3:
                  // return a UTF-16 character from a 3-byte UTF-8 char
                  // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                  return chr((0xF0 & (ord($utf8{0}) << 4))
                  | (0x0F & (ord($utf8{1}) >> 2)))
                  . chr((0xC0 & (ord($utf8{1}) << 6))
                  | (0x7F & ord($utf8{2})));
          }
  
          // ignoring UTF-32 for now, sorry
          return '';
      }
  
      public function encodeResponse(Gpf_Rpc_Serializable $response) {
          return $this->encode($response->toObject());
      }
  
      /**
       * encodes an arbitrary variable into JSON format
       *
       * @param    mixed   $var    any number, boolean, string, array, or object to be encoded.
       *                           see argument 1 to Services_JSON() above for array-parsing behavior.
       *                           if var is a strng, note that encode() always expects it
       *                           to be in ASCII or UTF-8 format!
       *
       * @return   mixed   JSON string representation of input var or an error if a problem occurs
       * @access   public
       */
      public function encode($var) {
          if ($this->isJsonEncodeEnabled()) {
              return @json_encode($var);
          }
          switch (gettype($var)) {
              case 'boolean':
                  return $var ? 'true' : 'false';
  
              case 'NULL':
                  return 'null';
  
              case 'integer':
                  return (int) $var;
  
              case 'double':
              case 'float':
                  return (float) $var;
  
              case 'string':
                  // STRINGS ARE EXPECTED TO BE IN ASCII OR UTF-8 FORMAT
                  $ascii = '';
                  $strlen_var = strlen($var);
  
                  /*
                   * Iterate over every character in the string,
                   * escaping with a slash or encoding to UTF-8 where necessary
                   */
                  for ($c = 0; $c < $strlen_var; ++$c) {
  
                      $ord_var_c = ord($var{$c});
  
                      switch (true) {
                          case $ord_var_c == 0x08:
                              $ascii .= '\b';
                              break;
                          case $ord_var_c == 0x09:
                              $ascii .= '\t';
                              break;
                          case $ord_var_c == 0x0A:
                              $ascii .= '\n';
                              break;
                          case $ord_var_c == 0x0C:
                              $ascii .= '\f';
                              break;
                          case $ord_var_c == 0x0D:
                              $ascii .= '\r';
                              break;
  
                          case $ord_var_c == 0x22:
                          case $ord_var_c == 0x2F:
                          case $ord_var_c == 0x5C:
                              // double quote, slash, slosh
                              $ascii .= '\\'.$var{$c};
                              break;
  
                          case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
                              // characters U-00000000 - U-0000007F (same as ASCII)
                              $ascii .= $var{$c};
                              break;
  
                          case (($ord_var_c & 0xE0) == 0xC0):
                              // characters U-00000080 - U-000007FF, mask 1 1 0 X X X X X
                              // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                              $char = pack('C*', $ord_var_c, ord($var{$c + 1}));
                              $c += 1;
                              $utf16 = $this->utf82utf16($char);
                              $ascii .= sprintf('\u%04s', bin2hex($utf16));
                              break;
  
                          case (($ord_var_c & 0xF0) == 0xE0):
                              // characters U-00000800 - U-0000FFFF, mask 1 1 1 0 X X X X
                              // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                              $char = pack('C*', $ord_var_c,
                              ord($var{$c + 1}),
                              ord($var{$c + 2}));
                              $c += 2;
                              $utf16 = $this->utf82utf16($char);
                              $ascii .= sprintf('\u%04s', bin2hex($utf16));
                              break;
  
                          case (($ord_var_c & 0xF8) == 0xF0):
                              // characters U-00010000 - U-001FFFFF, mask 1 1 1 1 0 X X X
                              // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                              $char = pack('C*', $ord_var_c,
                              ord($var{$c + 1}),
                              ord($var{$c + 2}),
                              ord($var{$c + 3}));
                              $c += 3;
                              $utf16 = $this->utf82utf16($char);
                              $ascii .= sprintf('\u%04s', bin2hex($utf16));
                              break;
  
                          case (($ord_var_c & 0xFC) == 0xF8):
                              // characters U-00200000 - U-03FFFFFF, mask 111110XX
                              // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                              $char = pack('C*', $ord_var_c,
                              ord($var{$c + 1}),
                              ord($var{$c + 2}),
                              ord($var{$c + 3}),
                              ord($var{$c + 4}));
                              $c += 4;
                              $utf16 = $this->utf82utf16($char);
                              $ascii .= sprintf('\u%04s', bin2hex($utf16));
                              break;
  
                          case (($ord_var_c & 0xFE) == 0xFC):
                              // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                              // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                              $char = pack('C*', $ord_var_c,
                              ord($var{$c + 1}),
                              ord($var{$c + 2}),
                              ord($var{$c + 3}),
                              ord($var{$c + 4}),
                              ord($var{$c + 5}));
                              $c += 5;
                              $utf16 = $this->utf82utf16($char);
                              $ascii .= sprintf('\u%04s', bin2hex($utf16));
                              break;
                      }
                  }
  
                  return '"'.$ascii.'"';
  
                          case 'array':
                              /*
                               * As per JSON spec if any array key is not an integer
                               * we must treat the the whole array as an object. We
                               * also try to catch a sparsely populated associative
                               * array with numeric keys here because some JS engines
                               * will create an array with empty indexes up to
                               * max_index which can cause memory issues and because
                               * the keys, which may be relevant, will be remapped
                               * otherwise.
                               *
                               * As per the ECMA and JSON specification an object may
                               * have any string as a property. Unfortunately due to
                               * a hole in the ECMA specification if the key is a
                               * ECMA reserved word or starts with a digit the
                               * parameter is only accessible using ECMAScript's
                               * bracket notation.
                               */
  
                              // treat as a JSON object
                              if (is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) {
                                  $properties = array_map(array($this, 'name_value'), array_keys($var), array_values($var));
  
                                  foreach($properties as $property) {
                                      if(Gpf_Rpc_Json::isError($property)) {
                                          return $property;
                                      }
                                  }
  
                                  return '{' . join(',', $properties) . '}';
                              }
  
                              // treat it like a regular array
                              $elements = array_map(array($this, 'encode'), $var);
  
                              foreach($elements as $element) {
                                  if(Gpf_Rpc_Json::isError($element)) {
                                      return $element;
                                  }
                              }
  
                              return '[' . join(',', $elements) . ']';
  
                          case 'object':
                              $vars = get_object_vars($var);
  
                              $properties = array_map(array($this, 'name_value'),
                              array_keys($vars),
                              array_values($vars));
  
                              foreach($properties as $property) {
                                  if(Gpf_Rpc_Json::isError($property)) {
                                      return $property;
                                  }
                              }
  
                              return '{' . join(',', $properties) . '}';
  
                          default:
                              if ($this->use & self::SERVICES_JSON_SUPPRESS_ERRORS) {
                                  return 'null';
                              }
                              return new Gpf_Rpc_Json_Error(gettype($var)." can not be encoded as JSON string");
          }
      }
  
      /**
       * array-walking function for use in generating JSON-formatted name-value pairs
       *
       * @param    string  $name   name of key to use
       * @param    mixed   $value  reference to an array element to be encoded
       *
       * @return   string  JSON-formatted name-value pair, like '"name":value'
       * @access   private
       */
      function name_value($name, $value)
      {
          $encoded_value = $this->encode($value);
  
          if(Gpf_Rpc_Json::isError($encoded_value)) {
              return $encoded_value;
          }
  
          return $this->encode(strval($name)) . ':' . $encoded_value;
      }
  
      /**
       * reduce a string by removing leading and trailing comments and whitespace
       *
       * @param    $str    string      string value to strip of comments and whitespace
       *
       * @return   string  string value stripped of comments and whitespace
       * @access   private
       */
      function reduce_string($str)
      {
          $str = preg_replace(array(
  
          // eliminate single line comments in '// ...' form
                  '#^\s*//(.+)$#m',
  
          // eliminate multi-line comments in '/* ... */' form, at start of string
                  '#^\s*/\*(.+)\*/#Us',
  
          // eliminate multi-line comments in '/* ... */' form, at end of string
                  '#/\*(.+)\*/\s*$#Us'
  
                  ), '', $str);
  
                  // eliminate extraneous space
                  return trim($str);
      }
  
      /**
       * decodes a JSON string into appropriate variable
       *
       * @param    string  $str    JSON-formatted string
       *
       * @return   mixed   number, boolean, string, array, or object
       *                   corresponding to given JSON input string.
       *                   See argument 1 to Services_JSON() above for object-output behavior.
       *                   Note that decode() always returns strings
       *                   in ASCII or UTF-8 format!
       * @access   public
       */
      function decode($str)
      {
          if ($this->isJsonDecodeEnabled()) {
              return json_decode($str);
          }
  
          $str = $this->reduce_string($str);
  
          switch (strtolower($str)) {
              case 'true':
                  return true;
  
              case 'false':
                  return false;
  
              case 'null':
                  return null;
  
              default:
                  $m = array();
  
                  if (is_numeric($str)) {
                      // Lookie-loo, it's a number
  
                      // This would work on its own, but I'm trying to be
                      // good about returning integers where appropriate:
                      // return (float)$str;
  
                      // Return float or int, as appropriate
                      return ((float)$str == (integer)$str)
                      ? (integer)$str
                      : (float)$str;
  
                  } elseif (preg_match('/^("|\').*(\1)$/s', $str, $m) && $m[1] == $m[2]) {
                      // STRINGS RETURNED IN UTF-8 FORMAT
                      $delim = substr($str, 0, 1);
                      $chrs = substr($str, 1, -1);
                      $utf8 = '';
                      $strlen_chrs = strlen($chrs);
  
                      for ($c = 0; $c < $strlen_chrs; ++$c) {
  
                          $substr_chrs_c_2 = substr($chrs, $c, 2);
                          $ord_chrs_c = ord($chrs{$c});
  
                          switch (true) {
                              case $substr_chrs_c_2 == '\b':
                                  $utf8 .= chr(0x08);
                                  ++$c;
                                  break;
                              case $substr_chrs_c_2 == '\t':
                                  $utf8 .= chr(0x09);
                                  ++$c;
                                  break;
                              case $substr_chrs_c_2 == '\n':
                                  $utf8 .= chr(0x0A);
                                  ++$c;
                                  break;
                              case $substr_chrs_c_2 == '\f':
                                  $utf8 .= chr(0x0C);
                                  ++$c;
                                  break;
                              case $substr_chrs_c_2 == '\r':
                                  $utf8 .= chr(0x0D);
                                  ++$c;
                                  break;
  
                              case $substr_chrs_c_2 == '\\"':
                              case $substr_chrs_c_2 == '\\\'':
                              case $substr_chrs_c_2 == '\\\\':
                              case $substr_chrs_c_2 == '\\/':
                                  if (($delim == '"' && $substr_chrs_c_2 != '\\\'') ||
                                  ($delim == "'" && $substr_chrs_c_2 != '\\"')) {
                                      $utf8 .= $chrs{++$c};
                                  }
                                  break;
  
                              case preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $c, 6)):
                                  // single, escaped unicode character
                                  $utf16 = chr(hexdec(substr($chrs, ($c + 2), 2)))
                                  . chr(hexdec(substr($chrs, ($c + 4), 2)));
                                  $utf8 .= $this->utf162utf8($utf16);
                                  $c += 5;
                                  break;
  
                              case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F):
                                  $utf8 .= $chrs{$c};
                                  break;
  
                              case ($ord_chrs_c & 0xE0) == 0xC0:
                                  // characters U-00000080 - U-000007FF, mask 1 1 0 X X X X X
                                  //see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                                  $utf8 .= substr($chrs, $c, 2);
                                  ++$c;
                                  break;
  
                              case ($ord_chrs_c & 0xF0) == 0xE0:
                                  // characters U-00000800 - U-0000FFFF, mask 1 1 1 0 X X X X
                                  // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                                  $utf8 .= substr($chrs, $c, 3);
                                  $c += 2;
                                  break;
  
                              case ($ord_chrs_c & 0xF8) == 0xF0:
                                  // characters U-00010000 - U-001FFFFF, mask 1 1 1 1 0 X X X
                                  // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                                  $utf8 .= substr($chrs, $c, 4);
                                  $c += 3;
                                  break;
  
                              case ($ord_chrs_c & 0xFC) == 0xF8:
                                  // characters U-00200000 - U-03FFFFFF, mask 111110XX
                                  // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                                  $utf8 .= substr($chrs, $c, 5);
                                  $c += 4;
                                  break;
  
                              case ($ord_chrs_c & 0xFE) == 0xFC:
                                  // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                                  // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                                  $utf8 .= substr($chrs, $c, 6);
                                  $c += 5;
                                  break;
  
                          }
  
                      }
  
                      return $utf8;
  
                  } elseif (preg_match('/^\[.*\]$/s', $str) || preg_match('/^\{.*\}$/s', $str)) {
                      // array, or object notation
  
                      if ($str{0} == '[') {
                          $stk = array(self::SERVICES_JSON_IN_ARR);
                          $arr = array();
                      } else {
                          if ($this->use & self::SERVICES_JSON_LOOSE_TYPE) {
                              $stk = array(self::SERVICES_JSON_IN_OBJ);
                              $obj = array();
                          } else {
                              $stk = array(self::SERVICES_JSON_IN_OBJ);
                              $obj = new stdClass();
                          }
                      }
  
                      array_push($stk, array('what'  => self::SERVICES_JSON_SLICE,
                                             'where' => 0,
                                             'delim' => false));
  
                      $chrs = substr($str, 1, -1);
                      $chrs = $this->reduce_string($chrs);
  
                      if ($chrs == '') {
                          if (reset($stk) == self::SERVICES_JSON_IN_ARR) {
                              return $arr;
  
                          } else {
                              return $obj;
  
                          }
                      }
  
                      //print("\nparsing {$chrs}\n");
  
                      $strlen_chrs = strlen($chrs);
  
                      for ($c = 0; $c <= $strlen_chrs; ++$c) {
  
                          $top = end($stk);
                          $substr_chrs_c_2 = substr($chrs, $c, 2);
  
                          if (($c == $strlen_chrs) || (($chrs{$c} == ',') && ($top['what'] == self::SERVICES_JSON_SLICE))) {
                              // found a comma that is not inside a string, array, etc.,
                              // OR we've reached the end of the character list
                              $slice = substr($chrs, $top['where'], ($c - $top['where']));
                              array_push($stk, array('what' => self::SERVICES_JSON_SLICE, 'where' => ($c + 1), 'delim' => false));
                              //print("Found split at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");
  
                              if (reset($stk) == self::SERVICES_JSON_IN_ARR) {
                                  // we are in an array, so just push an element onto the stack
                                  array_push($arr, $this->decode($slice));
  
                              } elseif (reset($stk) == self::SERVICES_JSON_IN_OBJ) {
                                  // we are in an object, so figure
                                  // out the property name and set an
                                  // element in an associative array,
                                  // for now
                                  $parts = array();
  
                                  if (preg_match('/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
                                      // "name":value pair
                                      $key = $this->decode($parts[1]);
                                      $val = $this->decode($parts[2]);
  
                                      if ($this->use & self::SERVICES_JSON_LOOSE_TYPE) {
                                          $obj[$key] = $val;
                                      } else {
                                          $obj->$key = $val;
                                      }
                                  } elseif (preg_match('/^\s*(\w+)\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
                                      // name:value pair, where name is unquoted
                                      $key = $parts[1];
                                      $val = $this->decode($parts[2]);
  
                                      if ($this->use & self::SERVICES_JSON_LOOSE_TYPE) {
                                          $obj[$key] = $val;
                                      } else {
                                          $obj->$key = $val;
                                      }
                                  }
  
                              }
  
                          } elseif ((($chrs{$c} == '"') || ($chrs{$c} == "'")) && ($top['what'] != self::SERVICES_JSON_IN_STR)) {
                              // found a quote, and we are not inside a string
                              array_push($stk, array('what' => self::SERVICES_JSON_IN_STR, 'where' => $c, 'delim' => $chrs{$c}));
                              //print("Found start of string at {$c}\n");
  
                          } elseif (($chrs{$c} == $top['delim']) &&
                          ($top['what'] == self::SERVICES_JSON_IN_STR) &&
                          (($chrs{$c - 1} != '\\') ||
                          ($chrs{$c - 1} == '\\' && $chrs{$c - 2} == '\\'))) {
                              // found a quote, we're in a string, and it's not escaped
                              array_pop($stk);
                              //print("Found end of string at {$c}: ".substr($chrs, $top['where'], (1 + 1 + $c - $top['where']))."\n");
  
                          } elseif (($chrs{$c} == '[') &&
                          in_array($top['what'], array(self::SERVICES_JSON_SLICE, self::SERVICES_JSON_IN_ARR, self::SERVICES_JSON_IN_OBJ))) {
                              // found a left-bracket, and we are in an array, object, or slice
                              array_push($stk, array('what' => self::SERVICES_JSON_IN_ARR, 'where' => $c, 'delim' => false));
                              //print("Found start of array at {$c}\n");
  
                          } elseif (($chrs{$c} == ']') && ($top['what'] == self::SERVICES_JSON_IN_ARR)) {
                              // found a right-bracket, and we're in an array
                              array_pop($stk);
                              //print("Found end of array at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");
  
                          } elseif (($chrs{$c} == '{') &&
                          in_array($top['what'], array(self::SERVICES_JSON_SLICE, self::SERVICES_JSON_IN_ARR, self::SERVICES_JSON_IN_OBJ))) {
                              // found a left-brace, and we are in an array, object, or slice
                              array_push($stk, array('what' => self::SERVICES_JSON_IN_OBJ, 'where' => $c, 'delim' => false));
                              //print("Found start of object at {$c}\n");
  
                          } elseif (($chrs{$c} == '}') && ($top['what'] == self::SERVICES_JSON_IN_OBJ)) {
                              // found a right-brace, and we're in an object
                              array_pop($stk);
                              //print("Found end of object at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");
  
                          } elseif (($substr_chrs_c_2 == '/*') &&
                          in_array($top['what'], array(self::SERVICES_JSON_SLICE, self::SERVICES_JSON_IN_ARR, self::SERVICES_JSON_IN_OBJ))) {
                              // found a comment start, and we are in an array, object, or slice
                              array_push($stk, array('what' => self::SERVICES_JSON_IN_CMT, 'where' => $c, 'delim' => false));
                              $c++;
                              //print("Found start of comment at {$c}\n");
  
                          } elseif (($substr_chrs_c_2 == '*/') && ($top['what'] == self::SERVICES_JSON_IN_CMT)) {
                              // found a comment end, and we're in one now
                              array_pop($stk);
                              $c++;
  
                              for ($i = $top['where']; $i <= $c; ++$i)
                              $chrs = substr_replace($chrs, ' ', $i, 1);
  
                              //print("Found end of comment at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");
  
                          }
  
                      }
  
                      if (reset($stk) == self::SERVICES_JSON_IN_ARR) {
                          return $arr;
  
                      } elseif (reset($stk) == self::SERVICES_JSON_IN_OBJ) {
                          return $obj;
  
                      }
  
                  }
          }
      }
      
      protected function isJsonEncodeEnabled() {
          return Gpf_Php::isFunctionEnabled('json_encode');
      }
      
      protected function isJsonDecodeEnabled() {
          return Gpf_Php::isFunctionEnabled('json_decode');
      }
      
  
      /**
       * @todo Ultimately, this should just call PEAR::isError()
       */
      function isError($data, $code = null)
      {
          if (is_object($data) &&
              (get_class($data) == 'Gpf_Rpc_Json_Error' || is_subclass_of($data, 'Gpf_Rpc_Json_Error'))) {
                  return true;
          }
          return false;
      }
  }
  
  class Gpf_Rpc_Json_Error {
      private $message;
      
      public function __construct($message) {
          $this->message = $message;
      }
  }
  

} //end Gpf_Rpc_Json

if (!interface_exists('Gpf_Rpc_DataEncoder', false)) {
  interface Gpf_Rpc_DataEncoder {
      function encodeResponse(Gpf_Rpc_Serializable $response);
  }
  
  

} //end Gpf_Rpc_DataEncoder

if (!interface_exists('Gpf_Rpc_DataDecoder', false)) {
  interface Gpf_Rpc_DataDecoder {
      /**
       * @param string $str
       * @return StdClass
       */
      function decode($str);
  }
  
  

} //end Gpf_Rpc_DataDecoder

if (!class_exists('Pap_Tracking_Action_RecognizeVisitorAffiliateFromVisitorId', false)) {
  class Pap_Tracking_Action_RecognizeVisitorAffiliateFromVisitorId extends Gpf_Object implements Pap_Tracking_Common_Recognizer {
      /**
       * @var Pap_Tracking_Visit_VisitorAffiliateCache
       */
      private $visitorAffiliateCache;
  
      public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
          $this->visitorAffiliateCache = $visitorAffiliateCache;
      }
  
      public function recognize(Pap_Contexts_Tracking $context) {
          if ($context->isVisitorAffiliateRecognized()) {
              return;
          }
  
          Pap_Tracking_Common_VisitorAffiliateCheckCompatibility::getHandlerInstance()->checkCompatibility($context->getVisitorId(), $this->visitorAffiliateCache);
          
          $context->debug('Getting VisitorAffiliate for visitorId = ' . $context->getVisitorId());
          if (($visitorAffiliate = $this->visitorAffiliateCache->getActualVisitorAffiliate($context->getVisitorId())) == null) {
              $context->debug('Recognize VisitorAffiliate not recognized from actual');
              return;
          }
          
          $context->debug('Recognize VisitorAffiliate recognized from actual, id: '.$visitorAffiliate->getId(). ', accountId: '. $visitorAffiliate->getAccountId());
          $context->setVisitorAffiliate($visitorAffiliate);
      }
  }
  

} //end Pap_Tracking_Action_RecognizeVisitorAffiliateFromVisitorId

if (!class_exists('Pap_Tracking_Action_RecognizeVisitorAffiliateFromIp', false)) {
  class Pap_Tracking_Action_RecognizeVisitorAffiliateFromIp extends Gpf_Object {
      /**
       * @var Pap_Tracking_Visit_VisitorAffiliateCache
       */
      private $visitorAffiliateCache;
  
      public function __construct(Pap_Tracking_Visit_VisitorAffiliateCache $visitorAffiliateCache) {
          $this->visitorAffiliateCache = $visitorAffiliateCache;
      }
  
      public function recognize(Pap_Contexts_Action $context) {
          if ($context->isVisitorAffiliateRecognized()) {
              return;
          }
          
          if(Gpf_Settings::get(Pap_Settings::TRACK_BY_IP_SETTING_NAME) != Gpf::YES) {
              return;
          }
          
          $ip = $context->getIp();
          $context->debug('Trying to get visitor affiliate from IP address '. $ip);
  
          $visitorAffiliate = $this->visitorAffiliateCache->getLatestVisitorAffiliateFromIp($ip, $context->getAccountId());
          if ($visitorAffiliate == null) {
              $context->debug("No visitor affiliate from IP '$ip'");
              return;
          }
          
          try {
              $periodInSeconds = $this->getValidityInSeconds();
          } catch (Gpf_Exception $e) {
              $context->debug($e->getMessage());
              return;
          }
          
          
          $dateFrom = new Gpf_DateTime($context->getVisitDateTime());
          $dateFrom->addSecond(-1*$periodInSeconds);
          $dateVisit = new Gpf_DateTime($visitorAffiliate->getDateVisit());
  
          if ($dateFrom->compare($dateVisit) > 0) {
              $context->debug("    No click from IP '$ip' found within ip validity period");
              return null;
          }
  
          if (!$context->isTrackingMethodSet()) {
              $context->setTrackingMethod(Pap_Common_Transaction::TRACKING_METHOD_IP_ADDRESS);
          }
          $context->debug('Visitor affiliate recognized from IP, id: '.$visitorAffiliate->getId(). ', accountId: '. $visitorAffiliate->getAccountId());
          $context->setVisitorAffiliate($visitorAffiliate);
      }
  
      private function getValidityInSeconds() {
          $validity = Gpf_Settings::get(Pap_Settings::IP_VALIDITY_SETTING_NAME);
          if($validity == '' || $validity == '0' || !is_numeric($validity)) {
              throw new Gpf_Exception("    IP address validity period is not correct: '$validity'");
          }
          $validityPeriod = Gpf_Settings::get(Pap_Settings::IP_VALIDITY_FORMAT_SETTING_NAME);
          if(!in_array($validityPeriod, array(Pap_Merchants_Config_TrackingForm::VALIDITY_DAYS,
          Pap_Merchants_Config_TrackingForm::VALIDITY_HOURS,
          Pap_Merchants_Config_TrackingForm::VALIDITY_MINUTES))) {
              throw new Gpf_Exception("    IP address validity period is not correct: '$validityPeriod'");
          }
  
          switch($validityPeriod) {
              case Pap_Merchants_Config_TrackingForm::VALIDITY_DAYS:
                  return $validity * 86400;
                   
              case Pap_Merchants_Config_TrackingForm::VALIDITY_HOURS:
                  return $validity * 3600;
                   
              case Pap_Merchants_Config_TrackingForm::VALIDITY_MINUTES:
                  return $validity * 60;
                   
              default: return 0;
          }
      }
  }
  

} //end Pap_Tracking_Action_RecognizeVisitorAffiliateFromIp

if (!class_exists('Pap_Tracking_Action_RecognizeAffiliate', false)) {
  class Pap_Tracking_Action_RecognizeAffiliate extends Pap_Tracking_Common_RecognizeAffiliate implements Pap_Tracking_Common_Recognizer {
  
      protected function getUser(Pap_Contexts_Tracking $context) {
          if ($context->getUserObject() != null) {
              return $context->getUserObject();
          }
  
          if (($user = $this->getUserFromParameter($context)) != null) {
              return $user;
          }
  
          if (($user = $this->getUserFromVisitorAffiliate($context)) != null) {
              return $user;
          }
  
          if (($user = $this->getDefaultAffiliate($context)) != null) {
              return $user;
          }
  
          return null;
      }
  
      /**
       * @return Pap_Common_User
       */
      private function getUserFromVisitorAffiliate(Pap_Contexts_Action $context) {
          $context->debug('Getting user from visitor affiliate');
          try {
              return $this->getCorrectUser($context, $context->getVisitorAffiliate()->getUserId(), $context->getVisit()->getTrackMethod());
          } catch (Gpf_Exception $e) {
              $context->debug('User not recognized from visitor affiliate');
              return null;
          }
      }
  
      /**
       * returns user object from user ID stored in request parameter
       */
      private function getUserFromParameter(Pap_Contexts_Action $context) {
          $context->debug("    Trying to get affiliate from request parameter '".Pap_Tracking_ActionRequest::PARAM_ACTION_AFFILIATEID."'");
  
          $userId = $context->getAffiliateIdFromRequest();
          if($userId != '') {
              return $this->getCorrectUser($context, $userId, Pap_Common_Transaction::TRACKING_METHOD_FORCED_PARAMETER);
          }
  
          $context->debug("        Affiliate not found in parameter");
          return null;
      }
  
  
  
      /**
       * returns user object from user ID stored in default affiliate
       *
       * @return string
       */
      protected function getDefaultAffiliate(Pap_Contexts_Action $context) {
          $context->debug("Trying to get default affiliate");
          if (Gpf_Settings::get(Pap_Settings::SAVE_UNREFERED_SALE_LEAD_SETTING_NAME) != Gpf::YES) {
              $context->debug("Save unreferred sale is not enabled");
              return null;
          }
          $userId = Gpf_Settings::get(Pap_Settings::DEFAULT_AFFILIATE_SETTING_NAME);
          if($userId == '') {
              $context->debug("No default affiliate defined");
              return null;
          }
  
          return $this->getCorrectUser($context, $userId, Pap_Common_Transaction::TRACKING_METHOD_DEFAULT_AFFILIATE);
      }
  
      /**
       * checks that user with this ID exists and is correct
       *
       * @param Pap_Contexts_Action $context
       * @param string $userId
       * @param string $trackingMethod
       * @return Pap_Common_User
       */
      protected function getCorrectUser(Pap_Contexts_Action $context, $userId, $trackingMethod) {
          $context->debug('Checking affiliate with Id: '.$userId);
          $userObj = $this->getUserById($context, $userId);
          if($userObj == null) {
              return null;
          }
          if ($context->getTrackingMethod() == '') {
              $context->setTrackingMethod($trackingMethod);
          }
          return $userObj;
      }
  }

} //end Pap_Tracking_Action_RecognizeAffiliate

if (!class_exists('Pap_Tracking_Action_RecognizeCampaign', false)) {
  class Pap_Tracking_Action_RecognizeCampaign extends Pap_Tracking_Common_RecognizeCampaign {
      /**
       * @var Pap_Tracking_Action_RecognizeCampaignIdByProductId
       */
      protected $recognizeCampaignIdByProductId;
  
      public function __construct() {
          $this->recognizeCampaignIdByProductId = new Pap_Tracking_Action_RecognizeCampaignIdByProductId();
      }
  
      /**
       * @return Pap_Common_Banner
       */
      protected function recognizeCampaigns(Pap_Contexts_Tracking $context) {
          if ($context->getCampaignObject() != null) {
              return $context->getCampaignObject();
          }
  
          try {
              return $this->getCampaignFromForcedBanner($context);
          } catch (Gpf_Exception $e) {
          }
          
          try {
              return $this->getCampaignFromParameter($context);
          } catch (Gpf_Exception $e) {
          }
  
          try {
              return $this->getCampaignFromProductID($context);
          } catch (Gpf_Exception $e) {
              if (Gpf_Settings::get(Pap_Settings::FORCE_CHOOSING_PRODUCTID_SETTING_NAME) == Gpf::YES) {
                  $context->setDoCommissionsSave(false);
                  return; 
              }
          }
  
          try {
              $visitorAffiliate = $context->getVisitorAffiliate();
              if ($visitorAffiliate != null) {
                  $context->debug('Getting campaign from visitor affiliate, visitorId: '.$visitorAffiliate->getVisitorId());
                  $context->debug('Checking campaign with Id: '.$visitorAffiliate->getCampaignId());
                  return $this->getCampaignById($context, $visitorAffiliate->getCampaignId());
              }
          } catch (Gpf_Exception $e) {
          }
  
          try {
              return $this->getDefaultCampaign($context);
          } catch (Gpf_Exception $e) {
          }
      }
  
      private function getCampaignFromForcedBanner(Pap_Contexts_Action $context) {
          $banner = $this->getBanner($context->getBannerIdFromRequest());
          return $this->getCampaignById($context, $banner->getCampaignId());
      }
      
      /**
       * @return Pap_Db_Banner
       * @throws Gpf_Exception
       */
      protected function getBanner($bannerId) {
          $banner = new Pap_Db_Banner();
          $banner->setId($bannerId);
          $banner->load();
          return $banner;
      }
      
      /**
       * returns campaign object from campaign ID stored in request parameter
       */
      private function getCampaignFromParameter(Pap_Contexts_Action $context) {
          $context->debug('Trying to get campaign from request parameter '.Pap_Tracking_ActionRequest::PARAM_ACTION_CAMPAIGNID);
  
          $campaignId = $context->getCampaignIdFromRequest();
  
          if($campaignId == '') {
              $this->logAndThrow($context, 'Campaign ID request parameter is empty');
          }
  
          $context->debug('Checking campaign with Id: '.$campaignId);
          return $this->getCampaignById($context, $campaignId);
      }
  
      /**
       * returns campaign object from Product ID stored in request parameter
       */
      private function getCampaignFromProductID(Pap_Contexts_Action $context) {
          $context->debug('Trying to get campaign from Product ID: '.$context->getProductIdFromRequest());
          return $this->getCampaignById($context, $this->recognizeCampaignIdByProductId->recognizeCampaignId($context, $context->getProductIdFromRequest()));
      }
  }
  

} //end Pap_Tracking_Action_RecognizeCampaign

if (!class_exists('Pap_Tracking_Action_RecognizeCampaignIdByProductId', false)) {
  class Pap_Tracking_Action_RecognizeCampaignIdByProductId extends Gpf_Object {
  
      public function __construct() {
      }
      
      /**
       * @return Gpf_Data_RecordSet
       */
      protected function getMatchingCampaignsRecordSet($productId) {
          $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
          $selectBuilder->select->add(Pap_Db_Table_Campaigns::ID);
          $selectBuilder->select->add(Pap_Db_Table_Campaigns::PRODUCT_ID);
          $selectBuilder->from->add(Pap_Db_Table_Campaigns::getName());
          $selectBuilder->where->add(Pap_Db_Table_Campaigns::PRODUCT_ID, 'like', '%'.$productId.'%');
          
          return $selectBuilder->getAllRows();
      }
      
      /**
       * @return string campaignId
       * @throws Gpf_Exception
       */
      public function recognizeCampaignId(Pap_Contexts_Tracking $context, $productId) {
          if($productId == '') {
              $context->debug('Empty product ID');
              throw new Gpf_Exception('Empty product ID');
          }
          
          $matchingCampaigns = $this->getMatchingCampaignsRecordSet($productId);
          
          switch ($matchingCampaigns->getSize()) {
              case 0:
              	$context->debug('No campaign matching product ID: '.$productId);
                  throw new Gpf_Exception('No campaign matching product ID: '.$productId);
              case 1:
                  foreach ($matchingCampaigns as $campaign) {
                      $campaignId = $campaign->get(Pap_Db_Table_Campaigns::ID);
                      $context->debug("Campaign was found for this Product ID. Campaign Id: ".$campaignId);
                      return $campaignId;
                  }
              default:
                  $context->debug("More campaigns matched product ID '.$productId.'. Finding correct campaign");
                  return $this->findBestMatchingCampaignId($matchingCampaigns, $productId);
          }
      }
      
      private function findBestMatchingCampaignId(Gpf_Data_RecordSet $matchingCampaigns, $productId) {
          foreach ($matchingCampaigns as $campaign) {
              $campaignProductIds = explode(',', $campaign->get(Pap_Db_Table_Campaigns::PRODUCT_ID));
              if (in_array($productId, array_values($campaignProductIds))) {
                  return $campaign->get(Pap_Db_Table_Campaigns::ID);
              }
          }
          throw new Gpf_Exception('No campaign matching product ID: '.$productId);
      }
  }
  

} //end Pap_Tracking_Action_RecognizeCampaignIdByProductId

if (!class_exists('Pap_Tracking_Action_RecognizeBanner', false)) {
  class Pap_Tracking_Action_RecognizeBanner extends Gpf_Object implements Pap_Tracking_Common_Recognizer  {
  
      private $bannerCache = array();
  
      public function __construct() {
      }
  
      /**
       * @return Pap_Common_Banner
       */
      public function recognize(Pap_Contexts_Tracking $context) {
          if ($context->getBannerObject() != null) {
              return;
          }
          
          try {
              $context->setBannerObject($this->getBannerById($context->getBannerIdFromRequest()));
              return;
          } catch (Exception $e) {
          }
  
          try {
              $bannerId = $context->getVisitorAffiliate()->getBannerId();
              $context->setBannerObject($this->getBannerById($bannerId));
          } catch (Exception $e) {
              $context->debug('Banner not recognized');
              return;
          }
      }
  
      /**
       * @throws Gpf_Exception
       * @return Pap_Common_Banner
       */
      protected function getBannerById($bannerId) {
          if (!$bannerId) {
              throw new Gpf_Exception('Undefined bannerid in Pap_Tracking_Action_RecognizeBanner::getBannerById()');
          }
          if (isset($bannerCache[$bannerId])) {
              return $bannerCache[$bannerId];
          }
  
          $banner = new Pap_Common_Banner();
          $banner->setId($bannerId);
          $banner->loadFromData();
          $bannerCache[$bannerId] = $banner;
          return $banner;
      }
  }
  

} //end Pap_Tracking_Action_RecognizeBanner

if (!class_exists('Pap_Tracking_Action_RecognizeChannel', false)) {
  class Pap_Tracking_Action_RecognizeChannel extends Pap_Tracking_Common_RecognizeChannel {
  
      /**
       * @return Pap_Db_Channel
       */
      protected function recognizeChannels(Pap_Contexts_Tracking $context) {
  
          try {
              return $this->getChannelFromParameter($context);
          } catch (Gpf_Exception $e) {
          }
  
          try {
              $visitorAffiliate = $context->getVisitorAffiliate();
              if ($visitorAffiliate != null) {
                  $context->debug('Trying to get channel from visitor affiliate.');
                  return $this->getChannelById($context, $visitorAffiliate->getChannelId());
              }
          } catch (Gpf_Exception $e) {
          }
      }
  
      /**
       * returns campaign object from user ID stored in custom cookie parameter
       */
      private function getChannelFromParameter(Pap_Contexts_Action $context) {
          $context->debug('Trying to get channel from forced parameter '.Pap_Tracking_ActionRequest::PARAM_ACTION_CHANNELID);
  
          return $this->getChannelById($context, $context->getChannelIdFromRequest());
      }
  }
  

} //end Pap_Tracking_Action_RecognizeChannel

if (!class_exists('Pap_Tracking_Action_FraudProtection', false)) {
  class Pap_Tracking_Action_FraudProtection extends Gpf_Object {
  
      const ACTION_DECLINE = 'D';
      const ACTION_DONTSAVE = 'DS';
  
      /**
       * checks for click fraud rules...
       *
       * @param Pap_Contexts_Click $context
       */
      public function check(Pap_Contexts_Action $context) {
          $context->debug('    FraudProtection started');
  
          $this->checkSalesFromBannedIP($context);
          $this->checkMultipleSalesFromSameIP($context);
          $this->checkMultipleSalesWithSameOrderID($context);
  
          Gpf_Plugins_Engine::extensionPoint('FraudProtection.Action.check', $context);
  
          $context->debug('    FraudProtection ended');
          $context->debug("");
      }
  
      /**
       * checks for duplicate records from same IP
       *
       * @param Pap_Contexts_Action $context
       * @return string
       */
      private function checkMultipleSalesFromSameIP(Pap_Contexts_Action $context) {
          $checkIt = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_SETTING_NAME);
          if($checkIt != Gpf::YES) {
              $context->debug('    Check for duplicate sales / leads with the same IP is not turned on');
              return true;
          }
  
          $context->debug('    Checking duplicate sales / leads from the same IP started');
  
          $checkPeriod = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_SECONDS_SETTING_NAME);
          $checkAction = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_ACTION_SETTING_NAME);
          if($checkPeriod == '' || $checkPeriod == '0' || !is_numeric($checkPeriod)) {
              $context->debug("Checking period is not correct: '$checkPeriod'");
              return true;
          }
          if($checkAction != self::ACTION_DECLINE && $checkAction != self::ACTION_DONTSAVE) {
              $context->debug("Action after check is not correct: '$checkAction'");
              return true;
          }
  
          $campaignId = null; 
          if (Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_SAMECAMPAIGN_SETTING_NAME) == Gpf::YES) {
              $campaignId = $context->getCampaignObject()->getId();
          }
          $orderId = null; 
          if (Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_SAMEORDERID_SETTING_NAME) == Gpf::YES) {
              $orderId = $context->getOrderIdFromRequest();
              if (trim($orderId) == '') {
                  $orderId = null;
              }
          }
          $ip = $context->getIp();
          $context->debug("    Looking transactions with IP: $ip" . (!is_null($campaignId) ? ", campaignid: $campaignId" : '') . (!is_null($orderId) ? ", order ID: $orderId" : '') . '.');
          $transactionsObject = $context->getTransactionObject();
          $recordsCount = $transactionsObject->getNumberOfRecordsFromSameIP($ip,  $this->getTransactionType($context), $checkPeriod, $context->getParentTransactionId(), $context->getVisitDateTime(), $campaignId, $orderId);
          if($recordsCount > 0) {
              if($checkAction == self::ACTION_DONTSAVE) {
                  $context->debug("    STOPPING (setting setDoCommissionsSave(false), found another sales / leads from the same IP: $ip". (!is_null($campaignId) ? ", campaignid: $campaignId" : '') . (!is_null($orderId) ? ", order ID: $orderId" : '') ."within $checkPeriod seconds");
                  $context->setDoCommissionsSave(false);
                  $context->debug('      Checking duplicate sales / leads from the same IP endeded');
                  return false;
  
              } else {
                  $context->debug("  DECLINING, found another sales / leads from the same IP: $ip". (!is_null($campaignId) ? ", campaignid: $campaignId" : '') . (!is_null($orderId) ? ", order ID: $orderId" : '') ." within $checkPeriod seconds");
  
                  $message = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_IP_MESSAGE_SETTING_NAME);
  
                  $this->declineAction($context, $message);
  
                  $context->debug('      Checking duplicate sales / leads from the same IP endeded');
                  return true;
              }
          } else {
              $context->debug("    No duplicate sales / leads from the same IP: $ip".(!is_null($campaignId) ? ", campaignid: $campaignId" : '') . (!is_null($orderId) ? ", order ID: $orderId" : '')." found");
          }
  
          $context->debug('      Checking duplicate sales / leads from the same IP endeded');
          return true;
      }
  
      /**
       * checks for duplicate records from same IP
       *
       * @param Pap_Contexts_Action $context
       * @return string
       */
      private function checkSalesFromBannedIP(Pap_Contexts_Action $context) {
          $checkIt = Gpf_Settings::get(Pap_Settings::BANNEDIPS_SALES);
          if($checkIt != Gpf::YES) {
              $context->debug('    Check for sales / leads with banned IP is not turned on');
              return true;
          }
  
          $context->debug('    Checking banned IP address of sales / leads started');
  
  
          $bannedIPAddresses = Gpf_Net_Ip::getBannedIPAddresses(Pap_Settings::BANNEDIPS_LIST_SALES);
  
          if($bannedIPAddresses === false) {
              $context->debug("List of banned IP addresses is invalid or empty, stop checking");
              return true;
          }
  
          $checkAction = Gpf_Settings::get(Pap_Settings::BANNEDIPS_SALES_ACTION);
          if($checkAction != self::ACTION_DECLINE && $checkAction != self::ACTION_DONTSAVE) {
              $context->debug("Action after check is not correct: '$checkAction'");
              return true;
          }
  
          $ip = $context->getIp();
          if(Gpf_Net_Ip::ipMatchRange($ip, $bannedIPAddresses)) {
              if($checkAction == self::ACTION_DONTSAVE) {
                  $context->debug("    STOPPING (setting setDoCommissionsSave(false), IP: $ip is banned");
                  $context->setDoCommissionsSave(false);
                  $context->debug('      Checking banned IP of sales / leads endeded');
                  return false;
  
              } else {
                  $context->debug("  DECLINING, IP is banned: $ip");
  
                  $message = Gpf_Settings::get(Pap_Settings::BANNEDIPS_SALES_MESSAGE);
  
                  $this->declineAction($context, $message);
  
                  $context->debug('      Checking banned IP of sales / leads endeded');
                  return true;
              }
          } else {
              $context->debug("    IP $ip is not banned");
          }
  
          $context->debug('      Checking banned IP of sales / leads endeded');
          return true;
      }
  
  
  
  
  
  
      /**
       * checks for duplicate records with same OrderID
       *
       * @param Pap_Contexts_Action $context
       * @return string
       */
      private function checkMultipleSalesWithSameOrderID(Pap_Contexts_Action $context) {
          $checkIt = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_ID_SETTING_NAME);
          if($checkIt != Gpf::YES) {
              $context->debug('    Check for duplicate sales / leads with the same OrderID is not turned on');
              return true;
          }
  
          $context->debug('    Checking duplicate sales / leads with the same OrderID started');
  
          $checkPeriod = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDER_ID_HOURS_SETTING_NAME);
          $checkAction = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_ID_ACTION_SETTING_NAME);
          if($checkPeriod == '' || $checkPeriod == '0' || !is_numeric($checkPeriod)) {
              $context->debug("Checking period is not correct: '$checkPeriod'");
              return true;
          }
          if($checkAction != self::ACTION_DECLINE && $checkAction != self::ACTION_DONTSAVE) {
              $context->debug("Action after check is not correct: '$checkAction'");
              return true;
          }
  
          $orderId = $context->getOrderIdFromRequest();
          $transactionsObject = $context->getTransactionObject();
  
          if(trim($orderId) == '') {
              $applyToEmptyOrderIDs = Gpf_Settings::get(Pap_Settings::APPLY_TO_EMPTY_ID_SETTING_NAME);
              if($applyToEmptyOrderIDs != Gpf::YES) {
                  $context->debug('      Order ID is empty, we do not aply fraud protection to empty order IDs');
                  return false;
              }
          }
  
          $transactionType = $this->getTransactionType($context);
          $parentTransactionId = $context->getParentTransactionId();
          $recordsCount = $transactionsObject->getNumberOfRecordsWithSameOrderId($orderId, $transactionType, $checkPeriod, $parentTransactionId, $context->getVisitDateTime());
          $context->debug("Getting number of transactions orderId=$orderId, type=$transactionType, not older than $checkPeriod hours, and not with parent transaction with id=$parentTransactionId returned $recordsCount");
          if($recordsCount > 0) {
              if($checkAction == self::ACTION_DONTSAVE) {
                  $context->debug("    STOPPING (setting setDoCommissionsSave(false), found another sales / leads from the same OrderID '$orderId' within $checkPeriod hours");
                  $context->setDoCommissionsSave(false);
                  $context->debug('      Checking duplicate sales / leads with the same OrderID endeded');
                  return false;
  
              } else {
                  $context->debug("  DECLINING, found another sales / leads with the same OrderID '$orderId' within $checkPeriod hours");
  
                  $message = Gpf_Settings::get(Pap_Settings::DUPLICATE_ORDERS_ID_MESSAGE_SETTING_NAME);
  
                  $this->declineAction($context, $message);
  
                  $context->debug('      Checking duplicate sales / leads with the same OrderID endeded');
                  return true;
              }
          } else {
              $context->debug("    No duplicate sales / leads with the same OrderID '$orderId' found");
          }
  
          $context->debug('      Checking duplicate sales / leads with the same OrderID endeded');
          return true;
      }
  
      /**
       * Sets status of transaction to declined and sets it's message
       *
       * @param Pap_Contexts_Action $context
       * @param string $checkMessage
       */
      private function declineAction(Pap_Contexts_Action $context, $message) {
          $context->setFraudProtectionStatus(Pap_Db_ClickImpression::STATUS_DECLINED);
          $transactionsObject = $context->getTransactionObject();
  
          if($message != '') {
              $transactionsObject->setSystemNote($message);
          }
      }
  
      private function getTransactionType(Pap_Contexts_Action $context) {
          $actionCode = $context->getActionCodeFromRequest();
          if ($actionCode == null || $actionCode == '') {
              return Pap_Common_Constants::TYPE_SALE;
          } else {
              return Pap_Common_Constants::TYPE_ACTION;
          }
      }
  }
  

} //end Pap_Tracking_Action_FraudProtection

if (!class_exists('Pap_Tracking_Action_RecognizeCommType', false)) {
  class Pap_Tracking_Action_RecognizeCommType extends Gpf_Object implements Pap_Tracking_Common_Recognizer {
      
      public function recognize(Pap_Contexts_Tracking $context) {
          return $this->getCommissionType($context);
      }
      
       /**
       * recognizes commission type for campaign
       *
       * @param Pap_Plugins_Tracking_Action_Context $context
       */
      public function getCommissionType(Pap_Contexts_Action $context) {
          $campaign = $context->getCampaignObject();
          
          $context->debug('Recognizing commission type started');
          $actionCode = $context->getActionCodeFromRequest();
          if ($actionCode == null || $actionCode == '') {
              $type = Pap_Common_Constants::TYPE_SALE;
          } else {
              $type = Pap_Common_Constants::TYPE_ACTION;
          }
          
          try {
              $context->debug('    Checking commission type : '.$type.' is in campaign');
              $commissionType = $campaign->getCommissionTypeObject($type, $context->getActionCodeFromRequest(), $context->getVisit()->getCountryCode());
          } catch (Pap_Tracking_Exception $e) {          
              $context->debug("    STOPPING, This commission type is not supported by current campaign or is NOT enabled! ");
              throw $e;
          }
          $context->setCommissionTypeObject($commissionType);
          
          $context->getTransaction(1)->setType($type);
          $context->debug('    Commission type set to: '.$type.', ID: '.$commissionType->getId());
          $context->debug('Recognizing commission type ended');
          $context->debug("");
      }    
  }
  

} //end Pap_Tracking_Action_RecognizeCommType

if (!class_exists('Pap_Tracking_Action_ComputeCommissions', false)) {
  class Pap_Tracking_Action_ComputeCommissions extends Gpf_Object implements Pap_Tracking_Common_Recognizer {
  
      private $recognizeCurrency;
  
      public function __construct() {
          $this->recognizeCurrency = new Pap_Tracking_Action_RecognizeCurrency();
      }
  
      public function recognize(Pap_Contexts_Tracking $context) {
          $this->computeCustomCommissions($context);
          $this->computeRealTotalCost($context);
          $this->computeFixedCost($context);
          $this->checkZeroOrdersCommissions($context);
      }
  
      private function checkZeroOrdersCommissions(Pap_Contexts_Tracking $context) {
          if ($context->getRealTotalCost() == 0 &&
          $context->getCommissionTypeObject()->getZeroOrdersCommissions() != Gpf::YES) {
              $context->debug("    STOPPING (setting setDoCommissionsSave(false), because TotalCost is 0 and zero order commissions should not be saved.");
              $context->setDoCommissionsSave(false);
          }
      }
  
      private function getParameterType($value) {
          $type = '$';
          if(strpos($value, '%') !== false) {
              $type = '%';
          }
          return $type;
      }
  
      private function makeCorrections($value) {
          $value = str_replace('%', '', $value);
          $value = str_replace('$', '', $value);
          $value = str_replace(',', '.', $value);
          $value = str_replace(' ', '', $value);
          return $value;
      }
  
      /**
       *
       * @param Pap_Contexts_Action $context
       * @return string
       */
      protected function getDefaultFixedCost($context) {
          $commissionTypeObject = $context->getCommissionTypeObject();
          if ($commissionTypeObject == null) {
              return false;
          }
          return array('fixedcosttype' => $commissionTypeObject->getFixedcostType(),
  	                 'fixedcostvalue' => $commissionTypeObject->getFixedcostValue());
      }
  
      public function computeFixedCost(Pap_Contexts_Action $context) {
          $context->debug('Fixed cost comnputing started');
          $context->debug("    Trying to get fixed cost from request parameter '".Pap_Tracking_ActionRequest::PARAM_ACTION_FIXEDCOST."'");
  
          $fixedCost = $context->getFixedCostFromRequest();
          if($fixedCost == '') {
              $context->debug("    Fixedcost not found in request trying to get default for campaign.'");
              $fixedCost = $this->getDefaultFixedCost($context);
              if ($fixedCost != false) {
                  $fixedCost = $fixedCost['fixedcosttype'].$fixedCost['fixedcostvalue'];
              }else{
                  $fixedCost = 0;
              }
          }
          if($fixedCost != '') {
              $type = $this->getParameterType($fixedCost);
              $fixedCost = $this->makeCorrections($fixedCost);
              $value = '';
              if(is_numeric($fixedCost) && $fixedCost >= 0) {
                  $value = $fixedCost;
              }
              if($value != '') {
                  $context->debug("        Fixed cost is $type $value");
                  if ($type == '%') {
                      if ($value > 100) {
                          $context->debug("        Fixed cost is greater than 100%!");
                          return;
                      }
                      $context->setFixedCost($context->getRealTotalCost()/100*$value);
                  } elseif ($type=='$') {
                      $context->setFixedCost($value);
                      $this->recognizeCurrency->processFixedCost($context);
                  }
              } else {
                  $context->debug("        Fixed cost has bad format");
              }
          }else{
              $context->setFixedCost(0);
          }
  
          $context->debug('Fixed cost computing ended');
          $context->debug("");
      }
  
      private function computeRealCost(Pap_Contexts_Action $context, $valueIN, $paramName) {
          if($context->getActionType() != Pap_Common_Constants::TYPE_ACTION) {
          	$context->debug('RealCost is 0 as the transaction type is not sale/action but '.$context->getActionType());
              return 0;
          }
          $valueOriginal = $valueIN;
          $value = $this->normalizeValue($valueOriginal);
  
          if($valueOriginal != $value) {
              $context->debug("        $paramName value from parameter is '".$valueOriginal."', corrected to '".$value."'");
          } else {
              $context->debug("        $paramName value from parameter is '".$value."'");
          }
          if($value == ''){
              return 0;
          }
          return $value;
      }
  
      public function computeCustomCommissions(Pap_Contexts_Action $context) {
          $context->debug('Custom commission computing started');
  
          $commission = $context->getCustomCommissionFromRequest();
          if($commission != '') {
              $context->debug("        Found custom commission: ".$commission.", decoding");
  
              $type = $this->getParameterType($commission);
              $commission = $this->makeCorrections($commission);
  
              $value = '';
              if(is_numeric($commission) && $commission >= 0) {
                  $value = $commission;
              }
  
              if($value != '') {
                  $context->debug("        Custom commission is $type $value");
                  $i = 1;
                  while ($context->getCommission($i) != null) {
                      $context->removeCommission($i++);
                  }
                  $newCommission = new Pap_Tracking_Common_Commission(1, $type, $value);
                  $newCommission->setStatus($this->recognizeStatus($context->getCommissionTypeObject()));
                  $context->addCommission($newCommission);
  
                  if ($type!='%'){
                      $this->recognizeCurrency->computeCustomCommission($context);
                  }
              } else {
                  $context->debug("        Custom commission has bad format");
              }
          } else {
          	$context->debug('No custom commission defined');
          }
  
          $context->debug('Checking for forced commissions ended');
  
          $context->debug('Custom commission computing ended');
          $context->debug("");
      }
  
      private function recognizeStatus(Pap_Db_CommissionType $commissionType) {
          if($commissionType->getApproval() == Pap_Db_CommissionType::APPROVAL_AUTOMATIC) {
              return Pap_Common_Constants::STATUS_APPROVED;
          }
          return Pap_Common_Constants::STATUS_PENDING;
      }
  
      /**
       * recomputes total cost to default currency
       *
       * @return unknown
       */
      public function computeRealTotalCost(Pap_Contexts_Tracking $context) {
          if($context->getActionType() != Pap_Common_Constants::TYPE_ACTION) {
          	$context->debug('Setting commission to 0 as the transaction type is not sale/action but '.$context->getActionType());
              return 0;
          }
          $this->recognizeCurrency->processTotalCost($context);
          $newTotalCost = $this->computeRealCost($context,$context->getRealTotalCost() ,'realTotalCost');
          $context->debug('Setting realTotalCost to '.$newTotalCost);
          $context->setRealTotalCost($newTotalCost);
      }
  
      /**
       * normalizes total cost, removes all spaces and non-numbers
       *
       * @param string $totalCost
       * @return string
       */
      protected function normalizeValue($value) {
          $value = str_replace('%20', '', $value);
          $value = preg_replace('/[^0-9.,\-]/', '', $value);
          return $value;
      }
  }
  

} //end Pap_Tracking_Action_ComputeCommissions

if (!class_exists('Pap_Tracking_Action_RecognizeCurrency', false)) {
  class Pap_Tracking_Action_RecognizeCurrency extends Gpf_Object {
  	
      public function processTotalCost (Pap_Contexts_Action $context) {
      	return $this->computeTotalCost($context);
      }
      
  	public function processFixedCost (Pap_Contexts_Action $context) {
      	return $this->computeFixedCost($context);
      }
      
  	public function processCommission (Pap_Contexts_Action $context) {
      	return $this->computeCustomCommission($context);
      }
      
      public function computeCustomCommission(Pap_Contexts_Action $context) {
      	$context->debug('Recognizing commission currency started');
  
      	$defaultCurrency = $this->getDefaultCurrency();
      	$context->debug("    Default currency is ".$defaultCurrency->getName());
      	$context->set("defaultCurrencyObject", $defaultCurrency);
      	if ($context->getCurrencyFromRequest() != '') {
      		Gpf_Plugins_Engine::extensionPoint('Tracker.action.computeCommission', $context);
      	}
      	$context->debug('Recognizing commission currency ended');
  		$context->debug("");
      	return Gpf_Plugins_Engine::PROCESS_CONTINUE;
      }
  
      private function computeTotalCost(Pap_Contexts_Action $context) {
      	$context->debug('Recognizing totalCost currency started');
  
      	$defaultCurrency = $this->getDefaultCurrency();
      	$context->debug("    Default currency is ".$defaultCurrency->getName());
          $context->set("defaultCurrencyObject", $defaultCurrency);
  
          $context->setRealTotalCost($context->getTotalCostFromRequest());
          $context->debug('Setting realTotalCost to '.$context->getTotalCostFromRequest());
          if ($context->getCurrencyFromRequest() != '') {
              Gpf_Plugins_Engine::extensionPoint('Tracker.action.computeTotalCost', $context);
          }
      	
      	$context->debug('Recognizing totalCost currency ended. totalCost: '.$context->getRealTotalCost());
  		$context->debug("");
      	return Gpf_Plugins_Engine::PROCESS_CONTINUE;
      }
  
      private function computeFixedCost(Pap_Contexts_Action $context) {
      	$context->debug('Recognizing fixedCost currency started');
  
      	$defaultCurrency = $this->getDefaultCurrency();
      	$context->debug("    Default currency is ".$defaultCurrency->getName());
      	$context->set("defaultCurrencyObject", $defaultCurrency);
      	
      	if ($context->getCurrencyFromRequest() != '') {
      		Gpf_Plugins_Engine::extensionPoint('Tracker.action.computeFixedCost', $context);
      	}
      	
      	$context->debug('Recognizing fixedCost currency ended');
  		$context->debug("");
      	return Gpf_Plugins_Engine::PROCESS_CONTINUE;
      }
  
      /**
       * retrieves default currency
       *
       * @return Gpf_Db_Currency
       */
      private function getDefaultCurrency() {
          try {
              return Gpf_Db_Currency::getDefaultCurrency();
          } catch (Gpf_DbEngine_NoRowException $e) {
              throw new Pap_Tracking_Exception("    Critical error - No default currency is defined");
          }
      }
  }
  

} //end Pap_Tracking_Action_RecognizeCurrency

if (!class_exists('Pap_Tracking_Action_ComputeStatus', false)) {
  class Pap_Tracking_Action_ComputeStatus extends Gpf_Object implements Pap_Tracking_Common_Recognizer {
  
      public function recognize(Pap_Contexts_Tracking $context) {
          $fpStatus = $context->getFraudProtectionStatus();
          if($fpStatus != null && $fpStatus != '') {
              $context->debug("    Using status '".$fpStatus."' set by fraud protection");
              $context->setStatusForAllCommissions($fpStatus);
              return;
          }
          if($this->getCustomStatus($context)) {
              return;
          }
      }
  
      protected function getCustomStatus(Pap_Contexts_Action $context) {
          $context->debug("    Trying to get custom status from request parameter '".Pap_Tracking_ActionRequest::PARAM_ACTION_CUSTOM_STATUS."'");
  
          $status = $context->getCustomStatusFromRequest();
          if($status != '') {
              $context->debug("        Found custom status: ".$status.", checking");
               
              if(in_array($status, array(Pap_Common_Constants::STATUS_APPROVED, Pap_Common_Constants::STATUS_PENDING, Pap_Common_Constants::STATUS_DECLINED))) {
                  $context->debug("        Setting custom status to $status");
                  $context->setStatusForAllCommissions($status);
                  return true;
              } else {
                  $context->debug("        Custom status is incorrect, it must be one of: A, P, D");
              }
          }
  
          return false;
      }
  }
  

} //end Pap_Tracking_Action_ComputeStatus

if (!class_exists('Gpf_SqlBuilder_InsertBuilder', false)) {
  class Gpf_SqlBuilder_InsertBuilder extends Gpf_SqlBuilder_ModifyBuilder {
      private $columns = array();
      private $tableName;
      
      /**
       * @var Gpf_DbEngine_Table
       */
      private $table;
  
      private $fromSelect = null;
  
      function __construct() {
      }
  
      public function add($column, $value, $doQuote = true) {
          $i = count($this->columns);
          $this->columns[$i]['column'] = $column;
          $this->columns[$i]['value']  = $value;
          $this->columns[$i]['doQuote']  = $doQuote;
      }
  
      public function addDontQuote($column, $value) {
          $this->add($column, $value, false);
      }
  
      public function setTable(Gpf_DbEngine_Table $table) {
          $this->tableName = $table->name();
          $this->table = $table;
      }
  
      public function toString() {
          $out =  "INSERT INTO $this->tableName (";
          foreach ($this->columns as $column) {
              $out .= $column['column'] . ',';
          }
          $out = rtrim($out, ',') . ') ';
          if(strlen($this->fromSelect)) {
              return $out . $this->fromSelect;
          }
          $out .= ' VALUES (';
          foreach ($this->columns as $column) {
              $value = $this->createDatabase()->escapeString($column['value']);
              if ($column['doQuote']) {
                  $out .= "'" . $value . "'";
              } else {
                  if ($value === null) {
                      $out .= "NULL";
                  } else {
                      $out .= $value;
                  }
              }
              $out .= ',';
          }
          return rtrim($out, ',') . ')';
      }
  
      public function insertAutoincrement() {
          return $this->createDatabase()->execute($this->toString(), true);
      }
  
      public function insert() {
          try {
              return $this->execute();
          } catch (Gpf_DbEngine_SqlException $e) {
              if($e->isDuplicateEntryError()) {
                  throw new Gpf_DbEngine_DuplicateEntryException($e->getMessage());
              }
              throw $e;
          }
      }
  
      public function fromSelect(Gpf_SqlBuilder_SelectBuilder $selectBuilder) {
          $this->fromSelect = $selectBuilder->toString();
          foreach ($selectBuilder->select->getColumns() as $column) {
              if($this->table !== null && !$this->table->hasColumn($column->getAlias())) {
                  throw new Gpf_Exception('Column ' . $column->getAlias() 
                      . " doesn't exist in $this->tableName.");
              }
              $i = count($this->columns);
              $this->columns[$i]['column'] = $column->getAlias();
          }
      }
  }

} //end Gpf_SqlBuilder_InsertBuilder

if (!class_exists('Gpf_DbEngine_Exception', false)) {
  class Gpf_DbEngine_Exception extends Gpf_Exception  {
  
      function __construct($message) {
          parent::__construct($message);
      }
  
      protected function logException() {
          Gpf_Log::disableType(Gpf_Log_LoggerDatabase::TYPE);
          Gpf_Log::error($this->getMessage());
          Gpf_Log::enableAllTypes();
      }
  }

} //end Gpf_DbEngine_Exception

if (!class_exists('Gpf_DbEngine_SqlException', false)) {
  abstract class Gpf_DbEngine_SqlException extends Gpf_DbEngine_Exception  {
      protected $_code;
      private $isLoggerException = false;
  
      function __construct($sqlString, $message, $code) {
          $this->isLoggerException = Gpf_Log_Logger::isLoggerInsert($sqlString);
          $this->_code = $code;
          parent::__construct("ERROR: " . $message);
      }
  
      protected function logException() {
          if ($this->isLoggerException) {
              parent::logException();
              return;
          }
          Gpf_Log::error($this->getMessage());
      }
  
      abstract function isDuplicateEntryError();
  }

} //end Gpf_DbEngine_SqlException

if (!class_exists('Gpf_DbEngine_Driver_Mysql_SqlException', false)) {
  class Gpf_DbEngine_Driver_Mysql_SqlException extends Gpf_DbEngine_SqlException {
       
      function isDuplicateEntryError() {
          return $this->_code == 1062;
      }
  }

} //end Gpf_DbEngine_Driver_Mysql_SqlException

if (!class_exists('Gpf_Db_Table_Logs', false)) {
  class Gpf_Db_Table_Logs extends Gpf_DbEngine_Table {
      const ID = "logid";
      const GROUP_ID = "groupid";
      const TYPE = "rtype";
      const CREATED = "created";
      const FILENAME = "filename";
      const LEVEL = "level";
      const LINE = "line";
      const MESSAGE = "message";
      const ACCOUNT_USER_ID = "accountuserid";
      const IP = "ip";
  	
      private static $instance;
          
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
          
      protected function initName() {
          $this->setName('g_logs');
      }
      
      public static function getName() {
          return self::getInstance()->name();
      }
      
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, self::INT, 0, true);
          $this->createColumn(self::GROUP_ID, self::CHAR, 16);
          $this->createColumn(self::TYPE, self::CHAR, 1);
          $this->createColumn(self::CREATED, self::DATETIME);
          $this->createColumn(self::FILENAME, self::CHAR, 255);
          $this->createColumn(self::LEVEL, self::INT);
          $this->createColumn(self::LINE, self::INT);
          $this->createColumn(self::MESSAGE, self::CHAR);
          $this->createColumn(self::ACCOUNT_USER_ID, self::CHAR, 8);
          $this->createColumn(self::IP, self::CHAR, 39);
      }
  
      public function deleteAll($logId) {
          $deleteBulider = new Gpf_SqlBuilder_DeleteBuilder();
          $deleteBulider->from->add(self::getName());
          $deleteBulider->where->add(self::ID, '=', $logId);
          $this->createDatabase()->execute($deleteBulider->toString());
      }
  }

} //end Gpf_Db_Table_Logs

if (!class_exists('Gpf_DbEngine_DuplicateEntryException', false)) {
  class Gpf_DbEngine_DuplicateEntryException extends Gpf_Exception  {
  
      function __construct($message) {
          parent::__construct($message);
      }
  
      protected function logException() {
      }
  }

} //end Gpf_DbEngine_DuplicateEntryException

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

if (!class_exists('Pap_Contexts_Action', false)) {
  class Pap_Contexts_Action extends Pap_Contexts_Tracking {
  
      /**
       * @var Pap_Tracking_Action_RequestActionObject
       */
      private $requestActionObject;
  
      public function __construct(Pap_Tracking_Action_RequestActionObject $requestActionObject = null, Pap_Db_Visit $visit = null) {
          if ($requestActionObject == null) {
              $requestActionObject = new Pap_Tracking_Action_RequestActionObject();
          }
          $this->requestActionObject = $requestActionObject;
  
          $this->visit = $visit;
          if ($visit != null) {
              $this->setVisitorId($visit->getVisitorId());
          }
  
          $this->setTransactionObject(new Pap_Common_Transaction());
          parent::__construct();
      }
  
      function __clone() {
          $transaction = clone $this->transactions[1];
          $this->transactions = array();
          $this->transactions[1] = $transaction;
      }
  
      /**
       * @return Pap_Contexts_Action
       */
      public static function getContextInstance() {
          if (self::$instance == null) {
              self::$instance = new Pap_Contexts_Action();
          }
          return self::$instance;
      }
  
      /**
       * @return Pap_Tracking_Action_RequestActionObject
       */
      public function getRequestActionObject() {
          return $this->getRequestActionObject();
      }
  
      protected function getActionTypeConstant() {
          return Pap_Common_Constants::TYPE_ACTION;
      }
  
      /**
       * gets client tracking method used
       * @return string
       */
  
      public function getClientTrackingMethod() {
          return $this->get("clientTrackingMethod");
      }
  
      /**
       * sets client tracking method used
       */
      public function setClientTrackingMethod($value) {
          $this->set("clientTrackingMethod", $value);
      }
  
      /**
       * gets tracking method used
       * @return string
       */
      public function getTrackingMethod() {
          return $this->get("realTrackingMethod");
      }
  
      /**
       * sets tracking method used
       */
      public function setTrackingMethod($value) {
          $this->set("realTrackingMethod", $value);
      }
      
      public function isTrackingMethodSet() {
          return $this->getTrackingMethod() != '';
      }
  
      /**
       * sets fixed cost
       */
      public function setFixedCost($value) {
          $this->requestActionObject->setFixedCost($value);
      }
  
      /**
       * gets fixed cost
       */
      public function getFixedCost() {
          return $this->requestActionObject->getFixedCost();
      }
  
      /**
       * gets ID of parent transaction
       * @return string
       */
      public function getParentTransactionId() {
          return $this->get("parentTransactionId");
      }
  
      /**
       * sets ID of parent transaction
       */
      public function setParentTransactionId($value) {
          $this->set("parentTransactionId", $value);
      }
  
      /**
       * gets fraud protection status
       * @return string
       */
      public function getFraudProtectionStatus() {
          return $this->get("fraudProtectionStatus");
      }
  
      /**
       * sets fraud protection status
       */
      public function setFraudProtectionStatus($value) {
          $this->set("fraudProtectionStatus", $value);
      }
  
      public function getAffiliateIdFromRequest() {
          return $this->requestActionObject->getAffiliateId();
      }
  
      public function getCouponFromRequest() {
          return $this->requestActionObject->getCouponCode();
      }
  
      public function getCampaignIdFromRequest() {
          return $this->requestActionObject->getCampaignId();
      }
      
      public function getBannerIdFromRequest() {
          return $this->requestActionObject->getBannerId();
      }
  
      public function getChannelIdFromRequest() {
          return $this->requestActionObject->getChannelId();
      }
  
      public function getProductIdFromRequest() {
          return $this->requestActionObject->getProductId();
      }
  
      public function getOrderIdFromRequest() {
          return $this->requestActionObject->getOrderId();
      }
  
      /**
       * gets real total cost
       */
      public function getRealTotalCost() {
          return $this->get("realTotalCost");
      }
  
      /**
       * sets real total cost
       */
      public function setRealTotalCost($value) {
          $this->set("realTotalCost", $value);
      }
  
      public function getTotalCostFromRequest() {
          return $this->requestActionObject->getTotalCost();
      }
  
      public function setTotalCost($value) {
          $this->requestActionObject->setTotalCost($value);
      }
  
      public function getExtraDataFromRequest($i) {
          return $this->requestActionObject->getData($i);
      }
  
      public function getActionCodeFromRequest() {
          return $this->requestActionObject->getActionCode();
      }
  
      public function getCurrencyFromRequest() {
          return $this->requestActionObject->getCurrency();
      }
  
      public function getCustomCommissionFromRequest() {
          return $this->requestActionObject->getCustomCommission();
      }
  
      public function getFixedCostFromRequest() {
          return $this->requestActionObject->getFixedCost();
      }
  
      public function getCustomStatusFromRequest() {
          return $this->requestActionObject->getStatus();
      }
  
      private $channelIdFromIp;
  
      public function setChannelIdByIp($id) {
          $this->channelIdFromIp = $id;
      }
  
      public function getChannelIdByIp() {
          return $this->channelIdFromIp;
      }
  
      public function getCustomTimeStampFromRequest() {
          return $this->requestActionObject->getTimeStamp();
      }
  
      /**
       * @return string datetime in standard format
       */
      public function getVisitDateTime() {
          if (strlen($timeStamp = $this->getCustomTimeStampFromRequest())) {
              return Gpf_Common_DateUtils::getDateTime($timeStamp);
          }
          return parent::getVisitDateTime();
      }
  
      public function getIp() {
          if ($this->getVisit() == null) {
              return null;
          }
          return $this->getVisit()->getIp();
      }
  }

} //end Pap_Contexts_Action

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

if (!class_exists('Pap_Tracking_Action_RequestActionObject', false)) {
  class Pap_Tracking_Action_RequestActionObject extends Gpf_Rpc_JsonObject {
      public $ac = ''; // actionCode
      public $t  = ''; // totalCost
      public $f  = ''; // fixedCost
      public $o  = ''; // order ID
      public $p  = ''; // product ID
      public $d1 = ''; // data1
      public $d2 = ''; // data2
      public $d3 = ''; // data3
      public $d4 = ''; // data4
      public $d5 = ''; // data5
      public $a  = ''; // affiliate ID
      public $c  = ''; // campaign ID
      public $b  = ''; // banner ID
      public $ch = ''; // channel ID
      public $cc = ''; // custom commission
      public $s  = ''; // status
      public $cr = ''; // currency
      public $cp = ''; // coupon code
      public $ts = ''; // time stamp
      
      public function __construct($object = null) {
          parent::__construct($object);
      }
  
      public function getActionCode() {
          return $this->ac;
      }
  
      public function getTotalCost() {
          return $this->t;
      }
  
      public function getFixedCost() {
          return $this->f;
      }
  
      public function getOrderId() {
          return $this->o;
      }
  
      public function getProductId() {
          return $this->p;
      }
  
      public function getData1() {
          return $this->d1;
      }
  
      public function getData2() {
          return $this->d2;
      }
  
      public function getData3() {
          return $this->d3;
      }
  
      public function getData4() {
          return $this->d4;
      }
  
      public function getData5() {
          return $this->d5;
      }
  
      public function getData($i) {
          $dataVar = 'd'.$i;
          return $this->$dataVar;
      }
  
      public function setData($i, $value) {
          $dataVar = 'd'.$i;
          $this->$dataVar = $value;
      }
  
      public function getAffiliateId() {
          return $this->a;
      }
  
      public function getCampaignId() {
          return $this->c;
      }
      
      public function getBannerId() {
          return $this->b;
      }
  
      public function getChannelId() {
          return $this->ch;
      }
  
      public function getCustomCommission() {
          return $this->cc;
      }
  
      public function getStatus() {
          return $this->s;
      }
  
      public function getCurrency() {
          return $this->cr;
      }
  
      public function getCouponCode() {
          return $this->cp;
      }
  
      public function getTimeStamp() {
          return $this->ts;
      }
  
      public function setActionCode($value) {
          $this->ac = $value;
      }
  
      public function setTotalCost($value) {
          $this->t = $value;
      }
  
      public function setFixedCost($value) {
          $this->f = $value;
      }
  
      public function setOrderId($value) {
          $this->o = $value;
      }
  
      public function setProductId($value) {
          $this->p = $value;
      }
  
      public function setData1($value) {
          $this->d1 = $value;
      }
  
      public function setData2($value) {
          $this->d2 = $value;
      }
  
      public function setData3($value) {
          $this->d3 = $value;
      }
  
      public function setData4($value) {
          $this->d4 = $value;
      }
  
      public function setData5($value) {
          $this->d5 = $value;
      }
  
      public function setAffiliateId($value) {
          $this->a = $value;
      }
  
      public function setCampaignId($value) {
          $this->c = $value;
      }
      
      public function setBannerId($value) {
          $this->b = $value;
      }
  
      public function setChannelId($value) {
          $this->ch = $value;
      }
  
      public function setCustomCommission($value) {
          $this->cc = $value;
      }
  
      public function setStatus($value) {
          $this->s = $value;
      }
  
      public function setCurrency($value) {
          $this->cr = $value;
      }
  
      public function setCouponCode($value) {
          $this->cp = $value;
      }
  
      public function setTimeStamp($value) {
          $this->ts = $value;
      }
  
  }

} //end Pap_Tracking_Action_RequestActionObject

if (!class_exists('Gpf_Rpc_JsonObject', false)) {
  class Gpf_Rpc_JsonObject extends Gpf_Object {
      
      public function __construct($object = null) {
          if ($object != null) {
              $this->initFrom($object);
          }
      }
      
      public function decode($string) {
          if ($string == null || $string == "") {
              throw new Gpf_Exception("Invalid format (".get_class($this).")");
          }
          $string = stripslashes($string);
          $json = new Gpf_Rpc_Json();
          $object = $json->decode($string);
          if (!is_object($object)) {
              throw new Gpf_Exception("Invalid format (".get_class($this).")");
          }
          $this->initFrom($object);
      }
      
      private function initFrom($object) {
          $object_vars = get_object_vars($object);
          foreach ($object_vars as $name => $value) {
              if (property_exists($this, $name)) {
                  $this->$name = $value;
              }
          }
      }
      
      public function encode() {
          $json = new Gpf_Rpc_Json();
          return $json->encode($this);
      }
      
      public function __toString() {
          return $this->encode();
      }
  }

} //end Gpf_Rpc_JsonObject

if (!class_exists('Pap_Db_Table_Transactions', false)) {
  class Pap_Db_Table_Transactions extends Gpf_DbEngine_Table implements Pap_Stats_Table {
  	const TRANSACTION_ID = 'transid';
  	const ACCOUNT_ID = Pap_Stats_Table::ACCOUNTID;
  	const USER_ID = Pap_Stats_Table::USERID;
  	const BANNER_ID = Pap_Stats_Table::BANNERID;
  	const PARRENT_BANNER_ID = Pap_Stats_Table::PARENTBANNERID;
  	const CAMPAIGN_ID = Pap_Stats_Table::CAMPAIGNID;
  	const COUNTRY_CODE = Pap_Stats_Table::COUNTRYCODE;
  	const PARRENT_TRANSACTION_ID = 'parenttransid';
  	const R_STATUS = 'rstatus';
  	const R_TYPE = 'rtype';
  	const DATE_INSERTED = Pap_Stats_Table::DATEINSERTED;
  	const DATE_APPROVED = 'dateapproved';
  	const PAYOUT_STATUS = 'payoutstatus';
  	const REFERER_URL = 'refererurl';
  	const IP = 'ip';
  	const BROWSER = 'browser';
  	const COMMISSION = 'commission';
  	const RECURRING_COMM_ID = 'recurringcommid';
  	const PAYOUTHISTORY_ID = 'payouthistoryid';
  	const FIRST_CLICK_TIME = 'firstclicktime';
  	const FIRST_CLICK_REFERER = 'firstclickreferer';
  	const FIRST_CLICK_IP = 'firstclickip';
  	const FIRST_CLICK_DATA1 = 'firstclickdata1';
  	const FIRST_CLICK_DATA2 = 'firstclickdata2';
  	const CLICK_COUNT = 'clickcount';
  	const LAST_CLICK_TIME = 'lastclicktime';
  	const LAST_CLICK_REFERER = 'lastclickreferer';
  	const LAST_CLICK_IP = 'lastclickip';
  	const LAST_CLICK_DATA1 = 'lastclickdata1';
  	const LAST_CLICK_DATA2 = 'lastclickdata2';
  	const TRACK_METHOD = 'trackmethod';
  	const ORDER_ID = 'orderid';
  	const PRODUCT_ID = 'productid';
  	const TOTAL_COST = 'totalcost';
  	const FIXED_COST = 'fixedcost';
  	const DATA1 = 'data1';
      const DATA2 = 'data2';
  	const DATA3 = 'data3';
  	const DATA4 = 'data4';
  	const DATA5 = 'data5';
  	const ORIGINAL_CURRENCY_ID = 'originalcurrencyid';
  	const ORIGINAL_CURRENCY_VALUE = 'originalcurrencyvalue';
  	const ORIGINAL_CURRENCY_RATE = 'originalcurrencyrate';
  	const TIER = 'tier';
  	const COMMISSIONTYPEID = 'commtypeid';
  	const COMMISSIONGROUPID = 'commissiongroupid';
  	const MERCHANTNOTE = 'merchantnote';
  	const SYSTEMNOTE = 'systemnote';
  	const COUPON_ID = 'couponid';
  	const VISITOR_ID = 'visitorid';
  	const SALE_ID = 'saleid';
  	const SPLIT = 'split';
  	const LOGGROUPID = 'loggroupid';
      const ALLOW_FIRST_CLICK_DATA = 'allowfirstclickdata';
      const ALLOW_LAST_CLICK_DATA = 'allowlastclickdata';
  	private static $instance;
  
  	/**
  	 * @return Pap_Db_Table_Transactions
  	 */
  	public static function getInstance() {
  		if(self::$instance === null) {
  			self::$instance = new self;
  		}
  		return self::$instance;
  	}
  
  	protected function initName() {
  		$this->setName('pap_transactions');
  	}
  
  	public static function getName() {
  		return self::getInstance()->name();
  	}
  
  	protected function initColumns() {
  		$this->createPrimaryColumn(self::TRANSACTION_ID, 'char', 8, true);
  		$this->createColumn(Pap_Stats_Table::ACCOUNTID, 'char', 8);
  		$this->createColumn(self::USER_ID, 'char', 8);
  		$this->createColumn(self::BANNER_ID, 'char', 8);
  		$this->createColumn(self::PARRENT_BANNER_ID, 'char', 8);
  		$this->createColumn(self::CAMPAIGN_ID, 'char', 8);
  		$this->createColumn(self::COUNTRY_CODE, 'char', 2);
  		$this->createColumn(self::PARRENT_TRANSACTION_ID, 'char', 8);
  		$this->createColumn(self::R_STATUS, 'char', 1);
  		$this->createColumn(self::R_TYPE, 'char', 1);
  		$this->createColumn(self::DATE_INSERTED, 'datetime');
  		$this->createColumn(self::DATE_APPROVED, 'datetime');
  		$this->createColumn(self::PAYOUT_STATUS, 'char', 1);
  		$this->createColumn(self::PAYOUTHISTORY_ID, 'char', 8);
  		$this->createColumn(self::REFERER_URL, 'char');
  		$this->createColumn(self::IP, 'char', 39);
  		$this->createColumn(self::BROWSER, 'char', 6);
  		$this->createColumn(self::COMMISSION, 'float');
  		$this->createColumn(self::RECURRING_COMM_ID, 'char', 8);
  		$this->createColumn(self::FIRST_CLICK_TIME, 'datetime');
  		$this->createColumn(self::FIRST_CLICK_REFERER, 'char');
  		$this->createColumn(self::FIRST_CLICK_IP, 'char', 39);
  		$this->createColumn(self::FIRST_CLICK_DATA1, 'char', 255);
  		$this->createColumn(self::FIRST_CLICK_DATA2, 'char', 255);
  		$this->createColumn(self::CLICK_COUNT, 'int', 10);
  		$this->createColumn(self::LAST_CLICK_TIME, 'datetime');
  		$this->createColumn(self::LAST_CLICK_REFERER, 'char');
  		$this->createColumn(self::LAST_CLICK_IP, 'char', 39);
  		$this->createColumn(self::LAST_CLICK_DATA1, 'char', 255);
  		$this->createColumn(self::LAST_CLICK_DATA2, 'char', 255);
  		$this->createColumn(self::TRACK_METHOD, 'char', 1);
  		$this->createColumn(self::ORDER_ID, 'char', 200);
  		$this->createColumn(self::PRODUCT_ID, 'char', 200);
  		$this->createColumn(self::TOTAL_COST, 'float');
  		$this->createColumn(self::FIXED_COST, 'float');
  		$this->createColumn(self::DATA1, 'char', 255);
  		$this->createColumn(self::DATA2, 'char', 255);
  		$this->createColumn(self::DATA3, 'char', 255);
  		$this->createColumn(self::DATA4, 'char', 255);
  		$this->createColumn(self::DATA5, 'char', 255);
  		$this->createColumn(self::ORIGINAL_CURRENCY_ID, 'char', 8);
  		$this->createColumn(self::ORIGINAL_CURRENCY_VALUE, 'float');
  		$this->createColumn(self::ORIGINAL_CURRENCY_RATE, 'float');
  		$this->createColumn(self::TIER, 'int', 10);
  		$this->createColumn(self::COMMISSIONTYPEID, 'char', 8);
  		$this->createColumn(self::COMMISSIONGROUPID, 'char', 8);
  		$this->createColumn(self::MERCHANTNOTE, 'char', 250);
  		$this->createColumn(self::SYSTEMNOTE, 'char', 250);
  		$this->createColumn(self::CHANNEL, self::CHAR, 10);
  		$this->createColumn(self::COUPON_ID, self::CHAR, 8);
  		$this->createColumn(self::VISITOR_ID, self::CHAR, 36);
  		$this->createColumn(self::SALE_ID, self::CHAR, 8);
  		$this->createColumn(self::SPLIT, 'float');
  		$this->createColumn(self::LOGGROUPID, self::CHAR, 16);
  		$this->createColumn(self::ALLOW_FIRST_CLICK_DATA, self::CHAR);
  		$this->createColumn(self::ALLOW_LAST_CLICK_DATA, self::CHAR);
  	}
  
  	/**
  	 *
  	 * Pap alert application handle, do not modify this source!
  	 *
  	 * @param String $dateFrom
  	 * @param String $dateTo
  	 * @param String $userId
  	 * @return Gpf_Data_RecordSet
  	 */
  	public static function getTransactions(Pap_Stats_Params $statsParams) {
  		$select = new Gpf_SqlBuilder_SelectBuilder();
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::USER_ID, 'userid');
  		$select->select->add('au.'.Gpf_Db_Table_AuthUsers::FIRSTNAME, 'name');
  		$select->select->add('au.'.Gpf_Db_Table_AuthUsers::LASTNAME, 'surname');
  		$select->select->add('au.'.Gpf_Db_Table_AuthUsers::USERNAME, 'username');
  		$select->select->add('pu.data1', 'weburl');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::TRANSACTION_ID, 'transid');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::TOTAL_COST, 'totalcost');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::FIXED_COST, 'fixedcost');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::ORDER_ID, 'orderid');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::PRODUCT_ID, 'productid');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::DATE_INSERTED, 'dateinserted');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::R_STATUS, 'rstatus');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::R_TYPE, 'transtype');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, 'transkind');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::PAYOUT_STATUS, 'payoutstatus');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::DATE_APPROVED, 'dateapproved');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::COMMISSION, 'commission');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::REFERER_URL, 'refererurl');
  		$select->select->add('c.'.Pap_Db_Table_Campaigns::ID, 'campcategoryid');
          $select->select->add('c.'.Pap_Db_Table_Campaigns::NAME, 'campaign');
  		$select->select->add('tr.data1', 'data1');
  		$select->select->add('tr.data2', 'data2');
  		$select->select->add('tr.data3', 'data3');
  		$select->select->add('tr.'.Pap_Db_Table_Transactions::COUNTRY_CODE, 'countrycode');
  		$select->from->add(Pap_Db_Table_Transactions::getName(), 'tr');
  		$select->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'c',
              'tr.'.Pap_Db_Table_Transactions::CAMPAIGN_ID.'=c.'.Pap_Db_Table_Campaigns::ID);
  		$select->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'pu',
              'tr.'.Pap_Db_Table_Transactions::USER_ID.'=pu.'.Pap_Db_Table_Users::ID);
  		$select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu',
              'gu.'.Gpf_Db_Table_Users::ID.'=pu.'.Pap_Db_Table_Users::ACCOUNTUSERID);
  		$select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au',
              'gu.'.Gpf_Db_Table_Users::AUTHID.'=au.'.Gpf_Db_Table_AuthUsers::ID);
  
  		if ($statsParams->isDateFromDefined()) {
  			$select->where->add('tr.'.Pap_Db_Table_Transactions::DATE_INSERTED, '>=', $statsParams->getDateFrom()->toDateTime());
  		}
  		if ($statsParams->isDateToDefined()) {
  			$select->where->add('tr.'.Pap_Db_Table_Transactions::DATE_INSERTED, '<=', $statsParams->getDateTo()->toDateTime());
  		}
  		if ($statsParams->getAffiliateId() != '') {
  			$select->where->add('tr.'.Pap_Db_Table_Transactions::USER_ID, '=', $statsParams->getAffiliateId());
  		}
  
  		return $select->getAllRows();
  	}
  	
  	 /**
       * @return Gpf_SqlBuilder_SelectBuilder
       */
      public function getStatsSelect(Pap_Stats_Params $statParams, $groupColumn, $groupColumnAlias) {
          $stats = new Pap_Stats_Computer_TransactionsStatsBuilder($statParams, $groupColumn, $groupColumnAlias);
          return $stats->getStatsSelect();
      }
  }
  

} //end Pap_Db_Table_Transactions

if (!class_exists('Pap_Db_Transaction', false)) {
  class Pap_Db_Transaction extends Gpf_DbEngine_Row {
  
      const TYPE_SALE = 'S';
      const TYPE_SIGNUP_BONUS = 'B';
      const TYPE_REFUND = 'R';
      const TYPE_CLICK = 'C';
      const TYPE_CPM = 'I';
      const TYPE_EXTRA_BONUS = 'E';
      const TYPE_CHARGE_BACK = 'H';
      const TYPE_REFERRAL = 'F';
  
      function __construct(){
          parent::__construct();
          $this->setAccountId(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
          $this->setDateInserted(Gpf_Common_DateUtils::now());
          $this->setChannel('');
          $this->setSplit(1);
          $this->setTier(1);
      }
  
      function init() {
          $this->setTable(Pap_Db_Table_Transactions::getInstance());
          parent::init();
      }
  
      protected function beforeSaveAction() {
          if ($this->getSaleId() == null) {
              $this->setSaleId(Gpf_Common_String::generateId(8));
          }
      }
  
      public function setAccountId($id) {
          $this->set(Pap_Db_Table_Transactions::ACCOUNT_ID, $id);
      }
      
      public function getAccountId() {
          return $this->get(Pap_Db_Table_Transactions::ACCOUNT_ID);
      }
      
      public function setDateInserted($dateInserted) {
          $this->set(Pap_Db_Table_Transactions::DATE_INSERTED, $dateInserted);
      }
  
      public function setOriginalCurrencyId($id) {
          $this->set(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_ID, $id);
      }
  
      public function getCommissionGroupId() {
          return $this->get(Pap_Db_Table_Transactions::COMMISSIONGROUPID);
      }
  
      public function setCommissionGroupId($groupid) {
          $this->set(Pap_Db_Table_Transactions::COMMISSIONGROUPID, $groupid);
      }
  
      public function setOriginalCurrencyRate($rate) {
          $this->set(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_RATE, $rate);
      }
  
      public function setOriginalCurrencyValue($value) {
          $this->set(Pap_Db_Table_Transactions::ORIGINAL_CURRENCY_VALUE, $value);
      }
  
      public function setOrderId($value) {
          $this->set(Pap_Db_Table_Transactions::ORDER_ID, $value);
      }
  
      public function getOrderId() {
          return $this->get(Pap_Db_Table_Transactions::ORDER_ID);
      }
  
      public function setRefererUrl($value) {
          $this->set(Pap_Db_Table_Transactions::REFERER_URL, $value);
      }
  
      public function getTransactionId() {
          return $this->get(Pap_Db_Table_Transactions::TRANSACTION_ID);
      }
  
      public function setProductId($value) {
          $this->set(Pap_Db_Table_Transactions::PRODUCT_ID, $value);
      }
  
      public function getProductId() {
          return $this->get(Pap_Db_Table_Transactions::PRODUCT_ID);
      }
      
      public function getPayoutHistoryId() {
          return $this->get(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID);
      }
      
      public function setPayoutHistoryId($value) {
          return $this->set(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, $value);
      }
  
      public function setDateApproved($value) {
          $this->set(Pap_Db_Table_Transactions::DATE_APPROVED, $value);
      }
  
      public function getDateApproved() {
          return $this->get(Pap_Db_Table_Transactions::DATE_APPROVED);
      }
  
      public function getDateInserted() {
          return $this->get(Pap_Db_Table_Transactions::DATE_INSERTED);
      }
  
      public function setTotalCost($value) {
          if($value == null || $value == '') {
              $value = 0;
          }
          $this->set(Pap_Db_Table_Transactions::TOTAL_COST, $value);
      }
  
      public function getSplit() {
          return $this->get(Pap_Db_Table_Transactions::SPLIT);
      }
  
      public function setSplit($value) {
          $this->set(Pap_Db_Table_Transactions::SPLIT, $value);
      }
  
      public function getTotalCost() {
          return $this->get(Pap_Db_Table_Transactions::TOTAL_COST);
      }
  
      public function getTotalCostAsText() {
          return round($this->getTotalCost(),Pap_Common_Utils_CurrencyUtils::getDefaultCurrencyPrecision());
      }
  
      public function setFixedCost($value) {
          $this->set(Pap_Db_Table_Transactions::FIXED_COST, $value);
      }
  
      public function getFixedCost() {
          return $this->get(Pap_Db_Table_Transactions::FIXED_COST);
      }
      
      public function getFirstClickReferer() {
          return $this->get(Pap_Db_Table_Transactions::FIRST_CLICK_REFERER);
      }
      
      public function getLastClickReferer() {
          return $this->get(Pap_Db_Table_Transactions::LAST_CLICK_REFERER);
      }
  
      public function setType($value) {
          $this->set(Pap_Db_Table_Transactions::R_TYPE, $value);
      }
  
      public function getType() {
          return $this->get(Pap_Db_Table_Transactions::R_TYPE);
      }
  
      public function setUserId($value) {
          $this->set(Pap_Db_Table_Transactions::USER_ID, $value);
      }
  
      public function getUserId() {
          return $this->get(Pap_Db_Table_Transactions::USER_ID);
      }
  
      public function setCampaignId($value) {
          $this->set(Pap_Db_Table_Transactions::CAMPAIGN_ID, $value);
      }
  
      public function setCouponId($couponID) {
          $this->set(Pap_Db_Table_Transactions::COUPON_ID, $couponID);
      }
  
      public function getCampaignId() {
          return $this->get(Pap_Db_Table_Transactions::CAMPAIGN_ID);
      }
  
      public function setBannerId($value) {
          $this->set(Pap_Db_Table_Transactions::BANNER_ID, $value);
      }
  
      public function getBannerId() {
          return $this->get(Pap_Db_Table_Transactions::BANNER_ID);
      }
  
      public function setParentBannerId($value) {
          $this->set(Pap_Db_Table_Transactions::PARRENT_BANNER_ID, $value);
      }
  
      public function getParentBannerId() {
          return $this->get(Pap_Db_Table_Transactions::PARRENT_BANNER_ID);
      }
  
      public function setCountryCode($value) {
          $this->set(Pap_Db_Table_Transactions::COUNTRY_CODE, $value);
      }
  
      public function getCountryCode() {
          return $this->get(Pap_Db_Table_Transactions::COUNTRY_CODE);
      }
  
      public function setTier($value) {
          $this->set(Pap_Db_Table_Transactions::TIER, $value);
      }
  
      public function getTier() {
          return $this->get(Pap_Db_Table_Transactions::TIER);
      }
  
      public function setCommission($value) {
          $this->set(Pap_Db_Table_Transactions::COMMISSION, $value);
      }
  
      public function getCommission() {
          return $this->get(Pap_Db_Table_Transactions::COMMISSION);
      }
  
      public function getCommissionAsText() {
          return round($this->getCommission(), Pap_Common_Utils_CurrencyUtils::getDefaultCurrencyPrecision());
      }
  
      public function getCouponID() {
          return $this->get(Pap_Db_Table_Transactions::COUPON_ID);
      }
  
      public function setPayoutStatus($value) {
          $this->set(Pap_Db_Table_Transactions::PAYOUT_STATUS, $value);
      }
  
      public function getPayoutStatus() {
          return $this->get(Pap_Db_Table_Transactions::PAYOUT_STATUS);
      }
  
      public function setParentTransactionId($id) {
          $this->set(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, $id);
      }
  
      public function getParentTransactionId() {
          return $this->get(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID);
      }
  
      public function setStatus($value) {
          $this->set(Pap_Db_Table_Transactions::R_STATUS, $value);
      }
  
      public function getStatus() {
          return $this->get(Pap_Db_Table_Transactions::R_STATUS);
      }
  
      public function setMerchantNote($value) {
          $this->set(Pap_Db_Table_Transactions::MERCHANTNOTE, $value);
      }
  
      public function getMerchantNote() {
          return $this->get(Pap_Db_Table_Transactions::MERCHANTNOTE);
      }
  
      public function setChannel($value) {
          $this->set(Pap_Db_Table_Transactions::CHANNEL, $value);
      }
  
      public function getChannel() {
          return $this->get(Pap_Db_Table_Transactions::CHANNEL);
      }
  
      public function setSystemNote($value) {
          $this->set(Pap_Db_Table_Transactions::SYSTEMNOTE, $value);
      }
  
      public function setClickCount($value) {
          $this->set(Pap_Db_Table_Transactions::CLICK_COUNT, $value);
      }
  
      public function getClickCount() {
          return $this->get(Pap_Db_Table_Transactions::CLICK_COUNT);
      }
  
      public function setId($id) {
          $this->set(Pap_Db_Table_Transactions::TRANSACTION_ID, $id);
      }
  
      public function setCommissionTypeId($commTypeId) {
          $this->set(Pap_Db_Table_Transactions::COMMISSIONTYPEID, $commTypeId);
      }
  
      public function getCommissionTypeId() {
          return $this->get(Pap_Db_Table_Transactions::COMMISSIONTYPEID);
      }
  
      public function getId() {
          return $this->get(Pap_Db_Table_Transactions::TRANSACTION_ID);
      }
  
      public function setData1($data1) {
          $this->set(Pap_Db_Table_Transactions::DATA1, $data1);
      }
  
      public function setData2($data2) {
          $this->set(Pap_Db_Table_Transactions::DATA2, $data2);
      }
  
      public function setData3($data3) {
          $this->set(Pap_Db_Table_Transactions::DATA3, $data3);
      }
  
      public function setData4($data4) {
          $this->set(Pap_Db_Table_Transactions::DATA4, $data4);
      }
  
      public function setData5($data5) {
          $this->set(Pap_Db_Table_Transactions::DATA5, $data5);
      }
      
      public function getData1() {
          return $this->get(Pap_Db_Table_Transactions::DATA1);
      }
  
      public function getData2() {
          return $this->get(Pap_Db_Table_Transactions::DATA2);
      }
  
      public function getData3() {
          return $this->get(Pap_Db_Table_Transactions::DATA3);
      }
  
      public function getData4() {
          return $this->get(Pap_Db_Table_Transactions::DATA4);
      }
  
      public function getData5() {
          return $this->get(Pap_Db_Table_Transactions::DATA5);
      }
  
      public function setFirstClickTime($value) {
          $this->set(Pap_Db_Table_Transactions::FIRST_CLICK_TIME, $value);
      }
  
      public function setFirstClickReferer($value) {
          $this->set(Pap_Db_Table_Transactions::FIRST_CLICK_REFERER, $value);
      }
  
      public function setFirstClickIp($value) {
          $this->set(Pap_Db_Table_Transactions::FIRST_CLICK_IP, $value);
      }
  
      public function setFirstClickData1($value) {
          $this->set(Pap_Db_Table_Transactions::FIRST_CLICK_DATA1, $value);
      }
  
      public function setFirstClickData2($value) {
          $this->set(Pap_Db_Table_Transactions::FIRST_CLICK_DATA2, $value);
      }
  
      public function setLastClickTime($value) {
          $this->set(Pap_Db_Table_Transactions::LAST_CLICK_TIME, $value);
      }
  
      public function setLastClickReferer($value) {
          $this->set(Pap_Db_Table_Transactions::LAST_CLICK_REFERER, $value);
      }
  
      public function setLastClickIp($value) {
          $this->set(Pap_Db_Table_Transactions::LAST_CLICK_IP, $value);
      }
  
      public function setLastClickData1($value) {
          $this->set(Pap_Db_Table_Transactions::LAST_CLICK_DATA1, $value);
      }
  
      public function setLastClickData2($value) {
          $this->set(Pap_Db_Table_Transactions::LAST_CLICK_DATA2, $value);
      }
  
      public function setTrackMethod($value) {
          $this->set(Pap_Db_Table_Transactions::TRACK_METHOD, $value);
      }
  
      public function setIp($value) {
          $this->set(Pap_Db_Table_Transactions::IP, $value);
      }
  
      public function setVisitorId($value) {
          $this->set(Pap_Db_Table_Transactions::VISITOR_ID, $value);
      }
  
      public function setSaleId($value) {
          $this->set(Pap_Db_Table_Transactions::SALE_ID, $value);
      }
  
      public function getSaleId() {
          return $this->get(Pap_Db_Table_Transactions::SALE_ID);
      }
  
      public function setLogGroupId($value) {
          $this->set(Pap_Db_Table_Transactions::LOGGROUPID, $value);
      }
  
      public function getLogGroupId() {
          return $this->get(Pap_Db_Table_Transactions::LOGGROUPID);
      }
  
      public function getIp() {
          return $this->get(Pap_Db_Table_Transactions::IP);
      }
  
      public function getRefererUrl() {
          return $this->get(Pap_Db_Table_Transactions::REFERER_URL);
      }
      
      public function getLastClickTime() {
          return $this->get(Pap_Db_Table_Transactions::LAST_CLICK_TIME);
      }
      
      
      public function setAllowLastClickData($value) {
          $this->set(Pap_Db_Table_Transactions::ALLOW_LAST_CLICK_DATA, $value);
      }
      
      public function getAllowLastClickData() {
          return $this->get(Pap_Db_Table_Transactions::ALLOW_LAST_CLICK_DATA);
      }
      
      public function setAllowFirstClickData($value) {
          $this->set(Pap_Db_Table_Transactions::ALLOW_FIRST_CLICK_DATA, $value);
      }
      
      public function getAllowFirstClickData() {
          return $this->get(Pap_Db_Table_Transactions::ALLOW_FIRST_CLICK_DATA);
      }
     
      public function recompute(Pap_Db_Commission $commission) {
          $commissionType = $commission->getCommissionType();
          $commissionValue = $commission->getCommissionValue();
          $realTotalCost = $this->getTotalCost() - $this->getFixedCost();
  
          if ($commissionType == Pap_Db_Commission::COMMISSION_TYPE_PERCENTAGE) {
              $newValue = $realTotalCost * ($commissionValue/100);
          } else if($commissionType == Pap_Db_Commission::COMMISSION_TYPE_FIXED) {
              $newValue = $commissionValue;
          } else {
              return;
          }
          $newValue = $newValue * $this->getSplit();
  
          if ($this->getType() == Pap_Db_Transaction::TYPE_REFUND ||
                  $this->getType() == Pap_Db_Transaction::TYPE_CHARGE_BACK) {
              $newValue *= -1;
          }
  
          $this->setCommission($newValue);
      }
  
      /**
       * @return Pap_Db_Transaction
       */
      public function getRefundOrChargebackTransaction() {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->select->addAll(Pap_Db_Table_Transactions::getInstance());
          $select->from->add(Pap_Db_Table_Transactions::getName());
          $select->where->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, '=', $this->getId());
          $select->where->add(Pap_Db_Table_Transactions::R_TYPE, 'IN', array(Pap_Db_Transaction::TYPE_REFUND, Pap_Db_Transaction::TYPE_CHARGE_BACK));
          try {
              $record = $select->getOneRow();
              $transaction = new Pap_Db_Transaction();
              $transaction->fillFromRecord($record);
              return $transaction;
          } catch (Gpf_Exception $e) {
              return null;
          }
      }
  }
  

} //end Pap_Db_Transaction

if (!class_exists('Pap_Common_Transaction', false)) {
  class Pap_Common_Transaction extends Pap_Db_Transaction {
      const TRACKING_METHOD_UNKNOWN = 'U';
      const TRACKING_METHOD_3RDPARTY_COOKIE = '3';
      const TRACKING_METHOD_1STPARTY_COOKIE = '1';
      const TRACKING_METHOD_FLASH_COOKIE = 'F';
      const TRACKING_METHOD_FORCED_PARAMETER = 'R';
      const TRACKING_METHOD_IP_ADDRESS = 'I';
      const TRACKING_METHOD_DEFAULT_AFFILIATE = 'D';
      const TRACKING_METHOD_MANUAL_COMMISSION = 'M';
      const TRACKING_METHOD_LIFETIME_REFERRAL = 'L';
      const TRACKING_METHOD_RECURRING_COMMISSION = 'O';
      const TRACKING_METHOD_COUPON = 'C';
  
      const PAYOUT_PAID = "P";
      const PAYOUT_UNPAID = "U";
  
      const PAYMENT_PENDING_ID = "toPay";
  
      protected $originalCurrencyPrecision = 0;
  
      private $oldStatus;
      private $notification;
  
      function __construct(){
          parent::__construct();
          $this->setNotification(true);
      }
  
      public function generateNewTransactionId() {
          $this->generatePrimaryKey();
      }
  
      public function getNumberOfRecordsFromSameIP($ip, $transType, $periodInSeconds, $parentTransId, $visitDateTime, $campaignId = null, $orderId = null) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
  
          $select->select->add("count(transid)", "count");
          $select->from->add(Pap_Db_Table_Transactions::getName());
          $select->where->add(Pap_Db_Table_Transactions::IP, "=", $ip);
          $select->where->add(Pap_Db_Table_Transactions::R_TYPE, "=", $transType);
          $select->where->add(Pap_Db_Table_Transactions::TIER, "=", "1");
          if (!is_null($campaignId)) {
              $select->where->add(Pap_Db_Table_Transactions::CAMPAIGN_ID, "=", $campaignId);
          }
          if (!is_null($orderId)) {
              $select->where->add(Pap_Db_Table_Transactions::ORDER_ID, "=", $orderId);
          }
          $dateFrom = new Gpf_DateTime($visitDateTime);
          $dateFrom->addSecond(-1*$periodInSeconds);
          $select->where->add(Pap_Db_Table_Transactions::DATE_INSERTED, ">", $dateFrom->toDateTime());
          if($parentTransId != null && $parentTransId != '') {
              $select->where->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, "<>", $parentTransId);
          }
  
          $recordSet = new Gpf_Data_RecordSet();
          $recordSet->load($select);
  
          foreach($recordSet as $record) {
              return $record->get("count");
          }
          return 0;
      }
  
      public function getNumberOfRecordsWithSameOrderId($orderId, $transType, $periodInHours, $parentTransId, $visitDateTime) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
  
          $select->select->add("count(transid)", "count");
          $select->from->add(Pap_Db_Table_Transactions::getName());
          if($orderId == '') {
              $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
              $condition->add(Pap_Db_Table_Transactions::ORDER_ID, '=', '', 'OR');
              $condition->add(Pap_Db_Table_Transactions::ORDER_ID, '=', null, 'OR');
              $select->where->addCondition($condition);
          } else {
              $select->where->add(Pap_Db_Table_Transactions::ORDER_ID, "=", $orderId);
          }
          $select->where->add(Pap_Db_Table_Transactions::R_TYPE, "=", $transType);
          $select->where->add(Pap_Db_Table_Transactions::TIER, "=", "1");
          $select->where->add(Pap_Db_Table_Transactions::R_STATUS, "<>", Pap_Common_Constants::STATUS_DECLINED);
          if($periodInHours > 0) {
              $dateFrom = new Gpf_DateTime($visitDateTime);
              $dateFrom->addHour(-1*$periodInHours);
              $select->where->add(Pap_Db_Table_Transactions::DATE_INSERTED, ">", $dateFrom->toDateTime());
          }
          if($parentTransId != null && $parentTransId != '') {
              $select->where->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, "<>", $parentTransId);
          }
  
          $recordSet = new Gpf_Data_RecordSet();
          $recordSet->load($select);
  
          foreach($recordSet as $record) {
              return $record->get("count");
          }
          return 0;
      }
  
      public static function getTrackingMethodName($type) {
          $obj = new Gpf_Object();
          switch($type) {
              case Pap_Common_Transaction::TRACKING_METHOD_UNKNOWN: return $obj->_('Unknown');
              case Pap_Common_Transaction::TRACKING_METHOD_3RDPARTY_COOKIE: return $obj->_('3rd party cookie');
              case Pap_Common_Transaction::TRACKING_METHOD_1STPARTY_COOKIE: return $obj->_('1st party cookie');
              case Pap_Common_Transaction::TRACKING_METHOD_FLASH_COOKIE: return $obj->_('Flash cookie');
              case Pap_Common_Transaction::TRACKING_METHOD_FORCED_PARAMETER: return $obj->_('Forced parameter');
              case Pap_Common_Transaction::TRACKING_METHOD_IP_ADDRESS: return $obj->_('IP address');
              case Pap_Common_Transaction::TRACKING_METHOD_DEFAULT_AFFILIATE: return $obj->_('Default affiliate');
              case Pap_Common_Transaction::TRACKING_METHOD_RECURRING_COMMISSION: return $obj->_('Recurring commission');
              default: return $obj->_('Unknown');
          }
      }
  
      /**
       * @returns Pap_Common_Transaction
       *
       */
      public function getFirstRecordWith($columnName, $value, $status = Pap_Common_Constants::STATUS_DECLINED) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
           
          $select->select->addAll(Pap_Db_Table_Transactions::getInstance());
          $select->from->add(Pap_Db_Table_Transactions::getName());
          $select->where->add($columnName, "=", $value);
  
          $select->where->add(Pap_Db_Table_Transactions::R_TYPE, "IN", array(Pap_Common_Constants::TYPE_SALE, Pap_Common_Constants::TYPE_ACTION, Pap_Common_Constants::TYPE_LEAD));
          $select->where->add(Pap_Db_Table_Transactions::TIER, "=", "1");
          if (is_array($status)) {
              $select->where->add(Pap_Db_Table_Transactions::R_STATUS, "IN", $status);
          }else{
              $select->where->add(Pap_Db_Table_Transactions::R_STATUS, "=", $status);
          }
  
          $select->limit->set(0, 1);
  
          $t = new Pap_Common_Transaction();
          $t->fillFromRecord($select->getOneRow());
  
          return $t;
      }
  
      /**
       * @throws Gpf_DbEngine_Row_ConstraintException
       */
      public function insert() {
          $this->saveTransaction();
      }
  
      /**
       * @param array $updateColumns list of columns that should be updated. if not set, all modified columns are update
       * @throws Gpf_DbEngine_Row_ConstraintException
       */
      public function update($updateColumns = array()) {
          $this->saveTransaction($updateColumns);
      }
  
      protected function saveTransaction($updateColumns = null) {
          $this->updateDateApproved();
          $this->processBeforeSaveExtensionPoint();
  
          $isNewSale = $this->saveTransactionToDb($updateColumns);
  
          $this->processAfterSaveExtensionPoint();
  
          $this->sendNotificationEmails($isNewSale);
      }
  
      protected function processBeforeSaveExtensionPoint() {
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Transaction.beforeSave', $this);
      }
  
      protected function processAfterSaveExtensionPoint() {
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Transaction.afterSave', $this);
      }
  
      /**
       * @return bool true if it is new transaction
       */
      protected function saveTransactionToDb($updateColumns) {
          if($updateColumns === null) {
              parent::insert();
              return true;
          }
          parent::update($updateColumns);
          return false;
      }
  
      /**
       * @param boolean $notification
       */
      public function setNotification($notification) {
          $this->notification = $notification;
      }
  
      protected function updateDateApproved() {
          if ($this->getStatus() == Pap_Common_Constants::STATUS_APPROVED &&
          ($this->getDateApproved() == null)) {
              $this->setDateApproved(Gpf_Common_DateUtils::now());
          }
      }
  
      protected function sendNotificationEmails($isNewSale) {
          if (!$this->notification) {
              return;
          }
  
          if ($this->getType() != Pap_Common_Constants::TYPE_SALE &&
          $this->getType() != Pap_Common_Constants::TYPE_ACTION) {
              return;
          }
          Gpf_Log::debug('SendNotificationEmails started');
          $notification = $this->getTransactionNotificationEmails($this);
          if ($isNewSale) {
              if ($this->getTier() == 1 || $this->getTier() == null) {
                  $notification->sendOnNewSaleNotification();
              } else if($this->getTier() == 2) {
                  $notification->sendOnNewSaleNotificationToParentAffiliate();
              }
          } else {
              if ($this->oldStatus == $this->getStatus()) {
                  Gpf_Log::debug('Notification emails ended. Status not changed.');
                  return;
              }
              $notification->sendOnChangeStatusNotification();
          }
          Gpf_Log::debug('SendNotificationEmails ended');
      }
  
      protected function getTransactionNotificationEmails(Pap_Common_Transaction $transaction) {
          return new Pap_Tracking_Action_SendTransactionNotificationEmails($transaction);
      }
  
      protected function afterLoad() {
          parent::afterLoad();
          $this->oldStatus = $this->getStatus();
      }
  
      public function processRefundChargeback($id, $type, $note = '', $orderId = '', $fee = 0, $refundTiers = false) {
          Pap_Contexts_Action::getContextInstance()->debug('Process refund on transaction: '.$id);
          $childTansactions = $this->getTransactionsByParent($refundTiers, $id);    
  
          $transaction = $this->getTransaction($id);
          try {
              $transaction->refundChargeback($type, $note, $orderId, $fee);
          } catch (Gpf_Exception $e) {
              Pap_Contexts_Action::getContextInstance()->debug($e->getMessage());
          } 
          
          if (!$refundTiers) {
              Pap_Contexts_Action::getContextInstance()->debug('No MultiTier children transactions refunds set');            
              return;
          }      
          
          foreach ($childTansactions as $childTransaction) {
              $this->processRefundChargeback($childTransaction->getId(), $type, $note, $orderId, $fee, true);
          }        
      }
      
      /**
       * @return Gpf_DbEngine_Row_Collection
       */
      protected function getTransactionsByParent($refundTiers, $parentId) {
          if (!$refundTiers) {
              return new Gpf_DbEngine_Row_Collection();
          }
          $transaction = new Pap_Common_Transaction();
          $transaction->setParentTransactionId($parentId);
          return $transaction->loadCollection(array(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID));
      }
  
      /** 
       * @return Pap_Common_Transaction
       */
      protected function getTransaction($id) {
          $transaction = new Pap_Common_Transaction();
          $transaction->setId($id);
          $transaction->load();
          return $transaction;
      }
  
      public function refundChargeback($type, $note = '', $orderId = '', $fee = 0) {
          if (($this->getType() == Pap_Common_Constants::TYPE_REFUND)||($this->getType() == Pap_Common_Constants::TYPE_CHARGEBACK)) {
              throw new Gpf_Exception("This transaction is already marked as refund/chargeback!");
          }
          if ($this->getStatus() == Pap_Common_Constants::STATUS_DECLINED) {
              throw new Gpf_Exception("This transaction was declined!");
          }
          if ($this->checkIfHasRefundChargeback() == true ) {
              throw new Gpf_Exception($this->_('Refund or chargeback for this transaction already exists!'));
          }
          $this->addRefundChargeback(new Pap_Db_Transaction(), $type, $note, $orderId, $fee);
      }
  
      protected function checkIfHasRefundChargeback(){
          $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
          $selectBuilder->select->add(Pap_Db_Table_Transactions::TRANSACTION_ID, 'id');
          $selectBuilder->from->add(Pap_Db_Table_Transactions::getName());
          $selectBuilder->where->add(Pap_Db_Table_Transactions::PARRENT_TRANSACTION_ID, '=', $this->getId());
          $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
          $condition->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Common_Constants::TYPE_REFUND, 'OR');
          $condition->add(Pap_Db_Table_Transactions::R_TYPE, '=', Pap_Common_Constants::TYPE_CHARGEBACK, 'OR');
          $selectBuilder->where->addCondition($condition);
          $rows = $selectBuilder->getAllRows();
          if ($rows->getSize() > 0) {
              return true;
          }
          return false;
  
      }
  
      protected function addRefundChargeback(Pap_Db_Transaction $refundChargeback, $type, $note = '', $orderId = '', $fee = 0) {
          foreach ($this as $name => $value) {
              $refundChargeback->set($name, $value);
          }
          $refundChargeback->setId(Gpf_Common_String::generateId());
          $refundChargeback->setCommission(($this->getCommission() * -1) - $fee);
          $refundChargeback->setType($type);
          if ($orderId != '') {
              $refundChargeback->setOrderId($orderId);
          }
          $refundChargeback->setParentTransactionId($this->getId());
          $refundChargeback->setDateInserted(Gpf_Common_DateUtils::now());
          $refundChargeback->setPayoutStatus(Pap_Common_Constants::PSTATUS_UNPAID);
          $refundChargeback->setMerchantNote($note);
          $refundChargeback->insert();
      }
  }
  

} //end Pap_Common_Transaction

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

if (!class_exists('Pap_Contexts_Click', false)) {
  class Pap_Contexts_Click extends Pap_Contexts_Tracking {
  
      public function __construct() {
      	parent::__construct();
      }
  
      protected function getActionTypeConstant() {
      	return Pap_Common_Constants::TYPE_CLICK;
      }
  
  	/**
  	 * sets click status
       */
  	public function setClickStatus($value) {
  		$this->set("clickStatus", $value);
  	}
  
  	/**
  	 * gets click status
  	 * @return string
       */
  	public function getClickStatus() {
  		return $this->get("clickStatus");
  	}
  
  	/**
  	 * sets click tracking type
       */
  	public function setClickTrackingType($value) {
  		$this->set("clickTrackingType", $value);
  	}
  
  	/**
  	 * gets click tracking type
  	 * @return string
       */
  	public function getClickTrackingType() {
  		return $this->get("clickTrackingType");
      }
  
  	/**
  	 * gets raw click object (instance of Pap_Db_RawClick)
  	 * @return Pap_Db_RawClick
       */
      public function getRawClickObject() {
  		return $this->get("rawClickObject");
  	}
  
  	/**
  	 * sets raw click object (instance of Pap_Db_RawClick)
       */
  	public function setRawClickObject(Pap_Db_RawClick $value) {
  		$this->set("rawClickObject", $value);
      }
  
      public function getIp() {
          if ($this->visit != null) {
              return $this->visit->getIp();
          }
          return $this->getRequestObject()->getIP();
      }
  
      public function getForcedBannerId() {
          return $this->getRequestObject()->getForcedBannerId();
      }
  
      public function getForcedCampaignId() {
          return $this->getRequestObject()->getForcedCampaignId();
      }
  
      public function getCampaignId() {
          return $this->getRequestObject()->getCampaignId();
      }
  
      public function getForcedAffiliateId() {
          return $this->getRequestObject()->getRequestParameter(Pap_Tracking_Request::getForcedAffiliateParamName());
      }
  
      public function getAffiliateId() {
          return $this->getRequestObject()->getRequestParameter(Pap_Tracking_Request::getAffiliateClickParamName());
      }
  
      public function getBannerId() {
          return $this->getRequestObject()->getBannerId();
      }
  
      public function getRotatorBannerId() {
          return $this->getRequestObject()->getRequestParameter(Pap_Tracking_Request::getRotatorBannerParamName());
      }
  
      public function getForcedChannelId() {
          return $this->getRequestObject()->getForcedChannelId();
      }
  
      public function getChannelId() {
          return $this->getRequestObject()->getChannelId();
      }
  
      public function getExtraDataFromRequest($i) {
          if ($i == 1) {
              return $this->getRequestObject()->getClickData1();
          }
  
          if ($i == 2) {
              return $this->getRequestObject()->getClickData2();
          }
      }
  
      public function getUserAgent() {
          if ($this->getVisit() == null || $this->getVisit()->getUserAgent() == '') {
              return '';
          }
          return substr(md5($this->getVisit()->getUserAgent()), 0, 6);
      }
  }

} //end Pap_Contexts_Click

if (!class_exists('Pap_Tracking_Exception', false)) {
  class Pap_Tracking_Exception extends Gpf_Exception {
  
  	function __construct($message) {
  		parent::__construct($message);
  	}
  }
  

} //end Pap_Tracking_Exception

if (!class_exists('Pap_Common_Campaign', false)) {
  class Pap_Common_Campaign extends Pap_Db_Campaign  {
      const CAMPAIGN_COMMISSION_STATUS_NOTDEFINED = Gpf::NO;
      const CAMPAIGN_COMMISSION_STATUS_DEFINED = Gpf::YES;
  
      function __construct() {
          parent::__construct();
      }
  
      /**
       * returns commission group for user.
       * If it doesn't exists, it will create default commission group and assign user to it.
       *
       * @param string $userId
       * @return string or false
       */
      public function getCommissionGroupForUser($userId) {
          $commGroupId = $this->checkUserIsInCampaign($userId);
  
          if($commGroupId != false) {
              return $commGroupId;
          }
  
          if($this->getCampaignType() == Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC) {
  
              $defaultCommGroupId = $this->getDefaultCommissionGroup();
  
              return $defaultCommGroupId;
          }
          Gpf_Log::info($this->_('No commissiongroup recognized - this is just hint: This campaign has type: %s. If, problem occured during commissiongrup recognition, you shoud check if this type is correct.', $this->getCampaignType()));
  
          return false;
      }
  
      /**
       * returns ID of default commission group for this campaign
       *
       * @return string
       */
      public function getDefaultCommissionGroup() {
          return Pap_Db_Table_Campaigns::getInstance()->getDefaultCommissionGroup($this->getId())->getId();
      }
  
      private function getCommissionTypeSelect($commissionType, $code = '',$countryCode = '') {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->select->addAll(Pap_Db_Table_CommissionTypes::getInstance());
          $select->from->add(Pap_Db_Table_CommissionTypes::getName());
          $select->where->add(Pap_Db_Table_CommissionTypes::CAMPAIGNID, '=', $this->getId());
          $select->where->add(Pap_Db_Table_CommissionTypes::TYPE, '=', $commissionType);
          $select->where->add(Pap_Db_Table_CommissionTypes::STATUS, '=', Pap_Db_CommissionType::STATUS_ENABLED);
          if ($code != null && $code != '') {
              $select->where->add(Pap_Db_Table_CommissionTypes::CODE, '=', $code);
          }
          if (!strlen($countryCode)) {
              $compoundCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
              $compoundCondition->add(Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID, '=', null, 'OR');
              $compoundCondition->add(Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID, '=', '', 'OR');
              $select->where->addCondition($compoundCondition);
          } else {
              $select->where->add(Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID, '!=', null);
              $select->where->add(Pap_Db_Table_CommissionTypes::COUNTRYCODES, 'like', '%' . $countryCode . '%');
          }
          return $select;
      }
  
      /**
       * checks if commission type exists in this campaign
       *
       * @param string $commissionType
       * @return Pap_Db_CommissionType
       */
      public function getCommissionTypeObject($commissionType, $code = '',$countryCode = '') {
          $baseTypeSelect = $this->getCommissionTypeSelect($commissionType, $code, '');
          $commType = new Pap_Db_CommissionType();
  
          try {
              $baseTypesCollection = $commType->loadCollectionFromRecordset($baseTypeSelect->getAllRows());
          } catch (Gpf_DbEngine_NoRowException $e) {
              throw new Pap_Tracking_Exception("Commission type not found in campaign: " . $e->getMessage());
          }
          if ($baseTypesCollection->getSize()==0) {
              throw new Pap_Tracking_Exception("Commission type not found in campaign");
          }
  
          $countrySpecificTypeSelect = $this->getCommissionTypeSelect($commissionType, $code, $countryCode);
          try {
              $countryTypesCollection = $commType->loadCollectionFromRecordset($countrySpecificTypeSelect->getAllRows());
          } catch (Gpf_DbEngine_NoRowException $e) {
              return $baseTypesCollection->get(0);
          }
          if ($countryTypesCollection->getSize()==0) {
              return $baseTypesCollection->get(0);
          }
          return $countryTypesCollection->get(0);
      }
  
      /**
       * returns commission object
       *
       * @param int $tier
       * @param string $commissionGroupId
       * @param string $commissionTypeId
       * @return Pap_Db_Commission
       */
      public function getCommission($tier, $commissionGroupId, $commissionTypeId) {
          $commission = new Pap_Db_Commission();
          $commission->setGroupId($commissionGroupId);
          $commission->setTypeId($commissionTypeId);
          $commission->setTier($tier);
  
          try {
              $commission->loadFromData();
          } catch (Gpf_DbEngine_NoRowException $e) {
              throw new Gpf_Exception("Cannot load commission for tier=".$tier.", comgroupid=".$commissionGroupId.", commtypeid=".$commissionTypeId);
          }
  
          return $commission;
      }
  
      /**
       * returns recordset with commission objects
       *
       * @param string $commissionGroupId
       * @param string $commissionTypeId
       * @return Gpf_DbEngine_Row_Collection <Pap_Db_Commission>
       */
      public function getCommissionsCollection($commissionGroupId, $commissionTypeId) {
          $commission = new Pap_Db_Commission();
          $commission->setGroupId($commissionGroupId);
          $commission->setTypeId($commissionTypeId);
  
          try {
              return $commission->loadCollection();
          } catch (Gpf_DbEngine_NoRowException $e) {
              throw new Gpf_Exception("Cannot load commission settings for comgroupid=".$commissionGroupId.", commtypeid=".$commissionTypeId);
          }
      }
  
      public function getDefaultFixedCost($type, $actionCode) {
          $cmpType = $this->getCommissionTypeObject($type, $actionCode);
          return array('fixedcosttype' => $cmpType->getFixedcostType(), 'fixedcostvalue' => $cmpType->getFixedcostValue());
      }
  
      /**
       * function checks if for this campaigns some commission types are set
       * and if they have some commissions defined.
       * If not, it returns N, if yes it returns Y.
       *
       */
      public function getCommissionStatus() {
          $cTable = Pap_Db_Table_Commissions::getInstance();
  
          $commissionsExist = $cTable->checkCommissionsExistInCampaign($this->getId());
          if($commissionsExist) {
              return self::CAMPAIGN_COMMISSION_STATUS_DEFINED;
          }
  
          return self::CAMPAIGN_COMMISSION_STATUS_NOTDEFINED;
      }
  
      /**
       * @param String $campaignId
       * @return NULL|Pap_Common_Campaign
       */
      public static function getCampaignById($campaignId) {
          if($campaignId == '') {
              return null;
          }
  
          $campaign = new Pap_Common_Campaign();
          $campaign->setPrimaryKeyValue($campaignId);
          try {
              $campaign->load();
          } catch (Gpf_DbEngine_NoRowException $e) {
              return null;
          }
          return $campaign;
      }
  
      public function insertCommissionType($type) {
          $commissionType = new Pap_Db_CommissionType();
          $commissionType->setCampaignId($this->getId());
          $commissionType->setType($type);
          $commissionType->setStatus(Pap_Db_CommissionType::STATUS_ENABLED);
          $commissionType->setApproval(Pap_Db_CommissionType::APPROVAL_AUTOMATIC);
          $commissionType->setRecurrencePresetId(Pap_Db_CommissionType::RECURRENCE_NONE);
          $commissionType->setZeroOrdersCommission(Gpf::NO);
          $commissionType->setSaveZeroCommission(Gpf::NO);
          $commissionType->insert();
  
          return $commissionType->getId();
      }
  }

} //end Pap_Common_Campaign

if (!class_exists('Pap_Db_Campaign', false)) {
  class Pap_Db_Campaign extends Gpf_DbEngine_Row {
  
      /* Campaign types */
      const CAMPAIGN_TYPE_PUBLIC = 'P';
      const CAMPAIGN_TYPE_PUBLIC_MANUAL = 'M';
      const CAMPAIGN_TYPE_ON_INVITATION = 'I';
  
      /* Campaign statuses */
      const CAMPAIGN_STATUS_ACTIVE = 'A';
      const CAMPAIGN_STATUS_STOPPED_INVISIBLE = 'S';
      const CAMPAIGN_STATUS_STOPPED = 'W';
      const CAMPAIGN_STATUS_DELETED = 'D';
      const CAMPAIGN_STATUS_ACTIVE_DATERANGE = 'T';
      const CAMPAIGN_STATUS_ACTIVE_RESULTS = 'L';
  
      /* Campaign types */
      const USER_IN_CAMPAIGN_STATUS_APPROVED = 'A';
      const USER_IN_CAMPAIGN_STATUS_PENDING = 'P';
      const USER_IN_CAMPAIGN_STATUS_DECLINED = 'D';
  
      function init() {
          $this->setTable(Pap_Db_Table_Campaigns::getInstance());
          parent::init();
      }
  
      /**
       * @return int cookie lifetime in seconds
       */
      public function getCookieLifetime() {
          return Pap_Tracking_Cookie::computeLifeTimeDaysToSeconds($this->get(Pap_Db_Table_Campaigns::COOKIELIFETIME));
      }
  
      /**
       * @return boolean if cookie should be overwritten
       */
      public function getOverwriteCookie() {
          return $this->get(Pap_Db_Table_Campaigns::OVERWRITECOOKIE);
      }
      
      public function resetOverwriteCookieToDefault() {
          $this->set(Pap_Db_Table_Campaigns::OVERWRITECOOKIE, 'D');
      }
  
      public function getId() {
          return $this->get(Pap_Db_Table_Campaigns::ID);
      }
  
      public function setId($value) {
          return $this->set(Pap_Db_Table_Campaigns::ID, $value);
      }
  
      public function getName() {
          return $this->get(Pap_Db_Table_Campaigns::NAME);
      }
  
      public function setName($value) {
          return $this->set(Pap_Db_Table_Campaigns::NAME, $value);
      }
      
      public function setDateInserted($value) {
          $this->set(Pap_Db_Table_Campaigns::DATEINSERTED, $value);
      }
      
      public function setType($value) {
          return $this->set(Pap_Db_Table_Campaigns::TYPE, $value);
      }
      
      public function setStatus($value) {
          return $this->set(Pap_Db_Table_Campaigns::STATUS, $value);
      }
  
      public function getAccountId() {
          return $this->get(Pap_Db_Table_Campaigns::ACCOUNTID);
      }
      
      public function setAccountId($value) {
          $this->set(Pap_Db_Table_Campaigns::ACCOUNTID, $value);
      }
  
      public function getLongDescription() {
          return $this->get(Pap_Db_Table_Campaigns::LONG_DESCRIPTION);
      }
      
      /**
       * returns campaign type
       *
       */
      public function getCampaignType() {
          return $this->get(Pap_Db_Table_Campaigns::TYPE);
      }
  
      public function setCampaignType($value) {
          return $this->set(Pap_Db_Table_Campaigns::TYPE, $value);
      }
  
      /**
       * returns campaign status
       *
       */
      public function getCampaignStatus() {
          return $this->get(Pap_Db_Table_Campaigns::STATUS);
      }
  
      public function setCampaignStatus($value) {
          return $this->set(Pap_Db_Table_Campaigns::STATUS, $value);
      }
  
      //    public function getCookieLifetime() {
      //        return $this->get(Pap_Db_Table_Campaigns::COOKIELIFETIME);
      //    }
  
      public function setCookieLifetime($value) {
          return $this->set(Pap_Db_Table_Campaigns::COOKIELIFETIME, $value);
      }
  
  
      public function getDescription() {
          return $this->get(Pap_Db_Table_Campaigns::DESCRIPTION);
      }
      /**
       * checks if user is in campaign, if yes, it will return valid commissionGroupID,
       * otherwise it returns false
       *
       * @param string $userId
       * @return string or false
       */
      public function checkUserIsInCampaign($userId) {
          $result = new Gpf_Data_RecordSet();
          $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
          $selectBuilder->select->add('u.usercommgroupid', 'usercommgroupid');
          $selectBuilder->select->add('u.commissiongroupid', 'commissiongroupid');
          $selectBuilder->select->add('u.rstatus', 'rstatus');
          $selectBuilder->from->add('qu_pap_userincommissiongroup', 'u');
          $selectBuilder->from->addInnerJoin('qu_pap_commissiongroups', 'g',
              'u.commissiongroupid=g.commissiongroupid');
  
          $selectBuilder->where->add('g.campaignid', '=', $this->getId());
          $selectBuilder->where->add('u.userid', '=', $userId);
          $selectBuilder->limit->set(0, 1);
  
          $result->load($selectBuilder);
  
          if($result->getSize() == 0) {
              return false;
          }
  
          foreach($result as $record) {
              if($this->isUserCommissionGroupStatusAllowed($record->get('rstatus'))) {
                  return $record->get('commissiongroupid');
              }
              break;
          }
  
          return false;
      }
  
      private function isUserCommissionGroupStatusAllowed($status) {
          if($status != Pap_Features_PerformanceRewards_Condition::STATUS_DECLINED &&
          $status != Pap_Features_PerformanceRewards_Condition::STATUS_PENDING) {
              return true;
          }
          return false;
      }
  
      public function delete() {
          if (Gpf_Application::isDemo() && Gpf_Application::isDemoEntryId($this->getId())) {
              throw new Gpf_Exception("Demo campaign can not be deleted");
          }
          return parent::delete();
      }
  
      public function insert($createDefaultCommissionGroup = true) {
          parent::insert();
          if ($createDefaultCommissionGroup) {
              $this->createDefaultCommissionGroup();
          }
      }
  
      private function createDefaultCommissionGroup() {
          $commissionGroup = new Pap_Db_CommissionGroup();
          $commissionGroup->setCampaignId($this->getId());
          $commissionGroup->setDefault(GPF::YES);
          $commissionGroup->setName('Default commission group');
          $commissionGroup->insert();
      }
      
      public function getIsDefault() {
          if ($this->get(Pap_Db_Table_Campaigns::IS_DEFAULT) == Gpf::YES) {
              return true;
          }
          return false;
      }
      
      public function setIsDefault($value = true) {
          if ($value) {
              $this->set(Pap_Db_Table_Campaigns::IS_DEFAULT, Gpf::YES);
              return;
          }
          $this->set(Pap_Db_Table_Campaigns::IS_DEFAULT, Gpf::NO);
      }
  }
  

} //end Pap_Db_Campaign

if (!class_exists('Pap_Db_Table_Campaigns', false)) {
  class Pap_Db_Table_Campaigns extends Gpf_DbEngine_Table {
      const ID = 'campaignid';
      const TYPE = 'rtype';
      const STATUS = 'rstatus';
      const NAME = 'name';
      const DESCRIPTION = 'description';
      const LONG_DESCRIPTION = 'longdescription';
      const DATEINSERTED = 'dateinserted';
      const ORDER = 'rorder';
      const NETWORK_STATUS = 'networkstatus';
      const IS_DEFAULT = 'isdefault';
      const LOGO_URL = 'logourl';
      const PRODUCT_ID = 'productid';
      const DISCONTINUE_URL = 'discontinueurl';
      const VALID_FROM = 'validfrom';
      const VALID_TO = 'validto';
      const VALID_NUMBER = 'validnumber';
      const VALID_TYPE = 'validtype';
      const COUNTRIES = 'countries';
      const ACCOUNTID = 'accountid';
      const COOKIELIFETIME = 'cookielifetime';
      const OVERWRITECOOKIE = 'overwritecookie';
      const GEO_CAMPAIGN_DISPLAY = 'geocampaigndisplay';
      const GEO_BANNER_SHOW = 'geobannersshow';
      const GEO_TRANS_REGISTER = 'geotransregister';
      private static $instance;
  
      /**
       * @return Pap_Db_Table_Campaigns
       */
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
  
      protected function initName() {
          $this->setName('pap_campaigns');
      }
  
      public static function getName() {
          return self::getInstance()->name();
      }
  
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, 'char', 8, true);
          $this->createColumn(self::TYPE, 'char', 1);
          $this->createColumn(self::STATUS, 'char', 1);
          $this->createColumn(self::NAME, 'char', 100);
          $this->createColumn(self::DESCRIPTION, 'char');
          $this->createColumn(self::LONG_DESCRIPTION, 'char');
          $this->createColumn(self::DATEINSERTED, 'datetime', 0);
          $this->createColumn(self::ORDER, 'int', 0);
          $this->createColumn(self::NETWORK_STATUS, 'char', 1);
          $this->createColumn(self::IS_DEFAULT, 'char', 1);
          $this->createColumn(self::LOGO_URL, 'char', 255);
          $this->createColumn(self::PRODUCT_ID, 'char');
          $this->createColumn(self::DISCONTINUE_URL, 'char', 255);
          $this->createColumn(self::VALID_FROM, 'datetime', 0);
          $this->createColumn(self::VALID_TO, 'datetime', 0);
          $this->createColumn(self::VALID_NUMBER, 'int', 0);
          $this->createColumn(self::VALID_TYPE, 'char', 1);
          $this->createColumn(self::ACCOUNTID, 'char', 8);
          $this->createColumn(self::COOKIELIFETIME, 'int', 0);
          $this->createColumn(self::OVERWRITECOOKIE, 'char', 1);
          $this->createColumn(self::COUNTRIES, 'char', 1000);
          $this->createColumn(self::GEO_CAMPAIGN_DISPLAY, 'char', 1);
          $this->createColumn(self::GEO_BANNER_SHOW, 'char', 1);
          $this->createColumn(self::GEO_TRANS_REGISTER, 'char', 1);
      }
  
      protected function initConstraints() {
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Banners::CAMPAIGN_ID, new Pap_Db_Banner());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, new Pap_Db_CommissionGroup());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CommissionTypes::CAMPAIGNID, new Pap_Db_CommissionType());
  
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_RawClicks::CAMPAIGNID, new Pap_Db_RawClick());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Clicks::CAMPAIGNID, new Pap_Db_Click());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Impressions::CAMPAIGNID, new Pap_Db_Impression());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Transactions::CAMPAIGN_ID, new Pap_Db_Transaction());
           
          $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_DirectLinkUrls::CAMPAIGN_ID, new Pap_Db_DirectLinkUrl());
           
          $this->addConstraint(new Pap_Common_Campaign_ZeroOrOneDefaultCampaignConstraint(array(self::ACCOUNTID => false, self::IS_DEFAULT=>Gpf::YES), $this->_('There must be exactly one default campaign')));
      }
  
      /**
       *
       * @return Pap_Db_CommissionGroup
       */
      public function getDefaultCommissionGroup($campaignId) {
          $commissionGroup = new Pap_Db_CommissionGroup();
          $commissionGroup->setCampaignId($campaignId);
          $commissionGroup->setDefault(GPF::YES);
          $commissionGroup->loadFromData();
          return $commissionGroup;
      }
  
      private function getCampaignsSelect() {
          $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
          $selectBuilder->select->add(self::ID, 'id');
          $selectBuilder->select->add(self::NAME, 'name');
          $selectBuilder->from->add(self::getName());
          $selectBuilder->where->add(self::ACCOUNTID, '=', Gpf_Session::getAuthUser()->getAccountId());
          Gpf_Plugins_Engine::extensionPoint('Pap_Db_Table_Campaigns.getCampaignsSelect', $selectBuilder);
          return $selectBuilder;
      }
  
      public static function getDefaultCampaignId($accountId = null) {
          if ($accountId == null) {
              $accountId = Gpf_Db_Account::DEFAULT_ACCOUNT_ID;
          }
          $campaign = new Pap_Db_Campaign();
          $campaign->setAccountId($accountId);
          $campaign->setIsDefault();
          try {
              $campaign->loadFromData(array(self::ACCOUNTID, self::IS_DEFAULT));
              return $campaign->getId();
          } catch (Gpf_Exception $e) {
          }
          return null;
      }
  
      /**
       * @service campaign read
       *
       * @param Gpf_Rpc_Params $params
       */
      public function getPrivateAndManualCampaigns(Gpf_Rpc_Params $params) {
          $selectBuilder = $this->getCampaignsSelect();
          $selectBuilder->select->add(self::TYPE, 'type');
          $selectBuilder->where->add(self::TYPE, 'IN', array(Pap_Db_Campaign::CAMPAIGN_TYPE_ON_INVITATION, Pap_Db_Campaign::CAMPAIGN_TYPE_PUBLIC_MANUAL));
          $selectBuilder->orderBy->add(self::NAME);
          $campaigns = $selectBuilder->getAllRows();
           
          $cTable = Pap_Db_Table_Commissions::getInstance();
          $rsCommissions = $cTable->getAllCommissionsInCampaign();
          $campaigns->addColumn('commissions', '');
  
          foreach ($campaigns as $campaign) {
              $campaign->set('type', Pap_Common_Constants::getCampaignTypeAsText($campaign->get('type')));
              if ($cTable->findCampaignInCommExistsRecords($campaign->get('id'), $rsCommissions)) {
                  $campaign->set('commissions', $cTable->getCommissionsDescription($campaign->get('id'), $rsCommissions));
              }
          }
  
          return $campaigns;
      }
  
      /**
       * @return Pap_Common_Campaign
       */
      public static function createDefaultCampaign($accountId, $campaignName, $campaignId = null, $type = Pap_Common_Campaign::CAMPAIGN_TYPE_PUBLIC) {
          $campaign = new Pap_Common_Campaign();
          if ($campaignId != null) {
              $campaign->setId($campaignId);
          }
          $campaign->setName($campaignName);
          $campaign->setDateInserted(Gpf_Common_DateUtils::now());
          $campaign->setCampaignStatus(Pap_Common_Campaign::CAMPAIGN_STATUS_ACTIVE);
          $campaign->setCampaignType($type);
          $campaign->setCookieLifetime(0);
          $campaign->resetOverwriteCookieToDefault();
          $campaign->setAccountId($accountId);
          $campaign->setIsDefault();
          $campaign->save();
  
          self::createDefaultCommissionSettings($campaign);
  
          return $campaign;
      }
  
      private function createDefaultCommissionSettings(Pap_Common_Campaign $campaign) {
          $commissionGroupId = $campaign->getDefaultCommissionGroup();
  
          $clickCommTypeId = $campaign->insertCommissionType(Pap_Common_Constants::TYPE_CLICK);
          self::createCommission($commissionGroupId, $clickCommTypeId, 1, '$', 0.5);
  
          $saleCommTypeId = $campaign->insertCommissionType(Pap_Common_Constants::TYPE_SALE);
          self::createCommission($commissionGroupId, $saleCommTypeId, 1, '%', 30);
          self::createCommission($commissionGroupId, $saleCommTypeId, 2, '%', 10);
      }
  
      private function createCommission($commissionGroupId, $commissionTypeId, $tier, $type, $value) {
          $c = new Pap_Db_Commission();
  
          $c->set("tier", $tier);
          $c->set("subtype", 'N');
          $c->set("commissiontype", $type);
          $c->set("commissionvalue", $value);
          $c->set("commtypeid", $commissionTypeId);
          $c->set("commissiongroupid", $commissionGroupId);
  
          $c->insert();
          return $c->get("commissionid");
      }
  }
  

} //end Pap_Db_Table_Campaigns

if (!class_exists('Pap_Db_VisitorAffiliate', false)) {
  class Pap_Db_VisitorAffiliate extends Gpf_DbEngine_Row {
  
      const TYPE_ACTUAL = 'A';
  
      function __construct() {
          parent::__construct();
      }
      
      protected function init() {
          $this->setTable(Pap_Db_Table_VisitorAffiliates::getInstance());
          parent::init();
      }
  
      /**
       * @return Pap_Tracking_Common_VisitorAffiliateCollection
       */
      public function loadCollectionFromRecordset(Gpf_Data_RecordSet $rowsRecordSet) {
          return $this->fillCollectionFromRecordset(new Pap_Tracking_Common_VisitorAffiliateCollection(), $rowsRecordSet);
      }
      
      public function setId($value) {
          return $this->set(Pap_Db_Table_VisitorAffiliates::ID, $value);
      }
  
      public function getId() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::ID);
      }
  
      public function getVisitorId() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::VISITORID);
      }
  
      public function setVisitorId($value) {
          $this->set(Pap_Db_Table_VisitorAffiliates::VISITORID, $value);
      }
  
      public function getUserId() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::USERID);
      }
  
      public function setUserId($value) {
          $this->set(Pap_Db_Table_VisitorAffiliates::USERID, $value);
      }
  
      public function getBannerId() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::BANNERID);
      }
  
      public function setBannerId($value) {
          $this->set(Pap_Db_Table_VisitorAffiliates::BANNERID, $value);
      }
  
      public function getCampaignId() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::CAMPAIGNID);
      }
  
      public function setCampaignId($value) {
          $this->set(Pap_Db_Table_VisitorAffiliates::CAMPAIGNID, $value);
      }
  
      public function getDateVisit() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::DATEVISIT);
      }
  
      public function setDateVisit($value) {
          $this->set(Pap_Db_Table_VisitorAffiliates::DATEVISIT, $value);
      }
  
      public function getData1() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::DATA1);
      }
  
      public function setData1($value) {
          $this->set(Pap_Db_Table_VisitorAffiliates::DATA1, $value);
      }
  
      public function getData2() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::DATA2);
      }
  
      public function setData2($value) {
          $this->set(Pap_Db_Table_VisitorAffiliates::DATA2, $value);
      }
  
      public function getIp() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::IP);
      }
  
      public function setIp($value) {
          $this->set(Pap_Db_Table_VisitorAffiliates::IP, $value);
      }
  
      public function setReferrerUrl($value) {
          $this->set(Pap_Db_Table_VisitorAffiliates::REFERRERURL, $value);
      }
  
      public function getReferrerUrl() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::REFERRERURL);
      }
  
      public function setType($value) {
          $this->set(Pap_Db_Table_VisitorAffiliates::TYPE, $value);
      }
  
      public function getType() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::TYPE);
      }
  
      public function getChannelId() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::CHANNELID);
      }
  
      public function setChannelId($value) {
          $this->set(Pap_Db_Table_VisitorAffiliates::CHANNELID, $value);
      }
  
      public function setActual($actual = true) {
          if ($this->isActual() == $actual) {
              return;
          }
          $this->setType($actual ? self::TYPE_ACTUAL : '');
      }
  
      public function isActual() {
          return $this->getType() == self::TYPE_ACTUAL;
      }
      
      public function isValid() {
          return $this->getValidTo() >= Gpf_Common_DateUtils::now();
      }
  
      public function getValidTo() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::VALIDTO);
      }
  
      public function setValidTo($value) {
          $this->set(Pap_Db_Table_VisitorAffiliates::VALIDTO, $value);
      }
      
      public function setAccountId($accountId) {
      	$this->set(Pap_Db_Table_VisitorAffiliates::ACCOUNTID, $accountId);
      }
      
      public function getAccountId() {
          return $this->get(Pap_Db_Table_VisitorAffiliates::ACCOUNTID);
      }
      
      public function toString() {
          return 'visitorId: '.$this->getVisitorId().", ".
                 'userid: '.$this->getUserId().", ".
                 'bannerid: '.$this->getBannerId().", ".
                 'validto: '.$this->getValidTo().
                 ($this->isActual() ? ' ACTUAL' : '');
                 
      }
  }
  

} //end Pap_Db_VisitorAffiliate

if (!class_exists('Pap_Db_Table_VisitorAffiliates', false)) {
  class Pap_Db_Table_VisitorAffiliates extends Gpf_DbEngine_Table {
      const ID = 'visitoraffiliateid';
      const VISITORID = 'visitorid';
      const USERID = 'userid';
      const BANNERID = 'bannerid';
      const CAMPAIGNID = 'campaignid';
      const CHANNELID = 'channelid';
      const TYPE = 'rtype';
      const IP = 'ip';
      const DATEVISIT = 'datevisit';
      const VALIDTO = 'validto';
      const REFERRERURL = 'referrerurl';
      const DATA1 = 'data1';
      const DATA2 = 'data2';
      const ACCOUNTID = 'accountid';
  
      private static $instance;
  
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
  
      protected function initName() {
          $this->setName('pap_visitoraffiliates');
      }
  
      public static function getName() {
          return self::getInstance()->name();
      }
  
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, self::INT, 0, true);
          $this->createColumn(self::VISITORID, self::CHAR, 36);
          $this->createColumn(self::USERID, self::CHAR, 8);
          $this->createColumn(self::BANNERID, self::CHAR, 8);
          $this->createColumn(self::CAMPAIGNID, self::CHAR, 8);
          $this->createColumn(self::CHANNELID, self::CHAR, 8);
          $this->createColumn(self::TYPE, self::CHAR, 1);
          $this->createColumn(self::IP, self::CHAR, 39);
          $this->createColumn(self::DATEVISIT, self::DATETIME);
          $this->createColumn(self::VALIDTO, self::DATETIME);
          $this->createColumn(self::REFERRERURL, self::CHAR);
          $this->createColumn(self::DATA1, self::CHAR, 255);
          $this->createColumn(self::DATA2, self::CHAR, 255);
          $this->createColumn(self::ACCOUNTID, self::CHAR, 8);
      }
  
  }

} //end Pap_Db_Table_VisitorAffiliates

if (!class_exists('Gpf_DbEngine_Row_Collection', false)) {
  class Gpf_DbEngine_Row_Collection extends Gpf_Object implements IteratorAggregate {
  
      /**
       * @var array of Gpf_DbEngine_RowBase
       */
      protected $rows = array();
  
      public function add(Gpf_DbEngine_RowBase $row) {
          $this->rows[] = $row;
      }
  
      /**
       * @return ArrayIterator
       */
      public function getIterator() {
          return new ArrayIterator($this->rows);
      }
  
      public function getSize() {
          return count($this->rows);
      }
  
      /**
       * @return Gpf_DbEngine_RowBase
       */
      public function get($i) {
          return $this->rows[$i];
      }
  
      public function set($i, Gpf_DbEngine_RowBase $row) {
          $this->rows[$i] = $row;
      }
  
      public function remove($i) {
          unset($this->rows[$i]);
      }
  
      public function insert($i, Gpf_DbEngine_RowBase $row) {
          array_splice($this->rows, $i, 0, array($row));
      }
  }

} //end Gpf_DbEngine_Row_Collection

if (!class_exists('Gpf_Rpc_Form_Validator_Validator', false)) {
  abstract class Gpf_Rpc_Form_Validator_Validator extends Gpf_Object {
      
      /**
       * @return String
       */
      public abstract function getText();
  
      /**
       * @param $value
       * @return boolean
       */
      public abstract function validate($value);
      
      /**
       * @param $value
       * @return boolean
       */
      protected function isEmpty($value) {
          return is_null($value) || $value == '';
      }
  }

} //end Gpf_Rpc_Form_Validator_Validator

if (!class_exists('Gpf_Rpc_Form_Validator_MandatoryValidator', false)) {
  class Gpf_Rpc_Form_Validator_MandatoryValidator extends Gpf_Rpc_Form_Validator_Validator {
      
      /**
       * @return String
       */
      public function getText() {
          return $this->_('%s is mandatory', '%s');
      }
  
      /**
       * @param $value
       * @return boolean
       */
      public function validate($value) {
          return !$this->isEmpty($value);
      }
  }

} //end Gpf_Rpc_Form_Validator_MandatoryValidator

if (!class_exists('Gpf_Rpc_Form_Validator_NumberValidator', false)) {
  class Gpf_Rpc_Form_Validator_NumberValidator extends Gpf_Rpc_Form_Validator_MandatoryValidator {
  
      /**
       * @return String
       */
      public function getText() {
          return $this->_('%s has to be number', '%s');
      }
  
      /**
       * @param $value
       * @return boolean
       */
      public function validate($value) {
          if ($this->isEmpty($value)) {
              return true;
          }
          return is_numeric($value);
      }
  }

} //end Gpf_Rpc_Form_Validator_NumberValidator

if (!class_exists('Gpf_Rpc_Form_Validator_IntegerNumberValidator', false)) {
  class Gpf_Rpc_Form_Validator_IntegerNumberValidator extends Gpf_Rpc_Form_Validator_NumberValidator {
      
      /**
       * @return String
       */
      public function getText() {
          return $this->_('%s has to be integer', '%s');
      }
  
      /**
       * @param $value
       * @return boolean
       */
      public function validate($value) {
          return parent::validate($value) && $value - floor($value) == 0;
      }
  }

} //end Gpf_Rpc_Form_Validator_IntegerNumberValidator

if (!class_exists('Gpf_Rpc_Form_Validator_IntegerNumberPositiveValidator', false)) {
  class Gpf_Rpc_Form_Validator_IntegerNumberPositiveValidator extends Gpf_Rpc_Form_Validator_IntegerNumberValidator {
      
      /**
       * @return String
       */
      public function getText() {
          return $this->_('%s has to be positive integer', '%s');
      }
      
      /**
       * @param $value
       * @return boolean
       */
      public function validate($value) {
          return parent::validate($value) && $value >= 0;
      }
  }

} //end Gpf_Rpc_Form_Validator_IntegerNumberPositiveValidator

if (!class_exists('Pap_Merchants_Config_AutoDeleteRawClicksValidator', false)) {
  abstract class Pap_Merchants_Config_AutoDeleteRawClicksValidator extends Gpf_Rpc_Form_Validator_IntegerNumberPositiveValidator {
  
      /**
       * @var Gpf_Rpc_Form
       */
      protected $form;
  
      public function __construct(Gpf_Rpc_Form $form) {
          $this->form = $form;
      }
  
      /**
       * @param $value
       * @return boolean
       */
      public function validate($value) {
          if ($this->getAutoDeleteRawClicks() === '0') {
              return true;
          }
          $compareValue = $this->getCompareValue();
          if (($compareValue = $this->checkZero($compareValue)) === false) {
              return true;
          }
          $compareValue = $this->computeCompareValue($compareValue);
          return parent::validate($value) && $this->isBiggerOrEqual($value, $compareValue);
      }
  
      /**
       * @return String
       */
      protected abstract function getAutoDeleteRawClicks();
  
      /**
       * @return String
       */
      protected abstract function getCompareValue();
  
      /**
       * @return Number
       */
      protected function computeCompareValue($compareValue) {
          return $compareValue;
      }
  
      private function checkZero($value) {
          if (!($value > 0)) {
              return false;
          }
          return $value;
      }
  
      private function isBiggerOrEqual($big, $small) {
          if ($big >= $small) {
              return true;
          }
          return false;
      }
  }

} //end Pap_Merchants_Config_AutoDeleteRawClicksValidator

if (!class_exists('Pap_Merchants_Config_TaskSettingsFormBase', false)) {
  abstract class Pap_Merchants_Config_TaskSettingsFormBase extends Gpf_Object {
  
  	protected function insertTask($className) {    
  		$task = $this->createTask($className);
  		try {
  			$task->loadFromData($this->getTaskLoadColumns());
  		} catch (Gpf_DbEngine_NoRowException $e) {
  			$task->insert();
  		} catch (Gpf_DbEngine_TooManyRowsException $e) {
  		}
  	}
  
  	protected function removeTask($className) {
  		$task = $this->createTask($className);
  		try {
  			$task->loadFromData($this->getTaskLoadColumns());
  			$task->delete();
  		} catch (Gpf_DbEngine_NoRowException $e) {
  		}
  	}
  	
      protected function getFieldValue(Gpf_Rpc_Form $form, $fieldName) {
      	if($form->existsField($fieldName)) {
      		return $form->getFieldValue($fieldName);
      	}
      	return '';
      }
      
      /**
       * @return array
       */
      protected function getTaskLoadColumns() {
      	return array(
  				Gpf_Db_Table_PlannedTasks::CLASSNAME, 
  				Gpf_Db_Table_PlannedTasks::RECURRENCEPRESETID
  				);
      }
      
      protected function initAccountId(Gpf_Db_PlannedTask $task) {   	
      }
  
  	/**
  	 * @param String $className
  	 * @return Gpf_Db_PlannedTask
  	 */
  	private function createTask($className) {
  		$task = new Gpf_Db_PlannedTask();
  		$task->setClassName($className);
  		$task->setRecurrencePresetId('A');
  		$task->setParams($this->getLastDateParams());
  		$this->initAccountId($task);
  		return $task;
  	}
  	
  	private function getLastDateParams() {
  	    $params = array('lastdate' => Gpf_Common_DateUtils::now());
  	    return serialize($params);
  	}
  }
  

} //end Pap_Merchants_Config_TaskSettingsFormBase

if (!class_exists('Pap_Merchants_Config_TrackingForm', false)) {
  class Pap_Merchants_Config_TrackingForm extends Pap_Merchants_Config_TaskSettingsFormBase {
  
      const VALIDITY_DAYS = "D";
      const VALIDITY_HOURS = "H";
      const VALIDITY_MINUTES = "M";
  
      /**
       * @service tracking_setting read
       * @param $fields
       */
      public function load(Gpf_Rpc_Params $params) {
          $form = new Gpf_Rpc_Form($params);
  
          $form->setField(Pap_Settings::DEFAULT_AFFILIATE_SETTING_NAME,
          Gpf_Settings::get(Pap_Settings::DEFAULT_AFFILIATE_SETTING_NAME));
          $form->setField(Pap_Settings::SAVE_UNREFERED_SALE_LEAD_SETTING_NAME,
          Gpf_Settings::get(Pap_Settings::SAVE_UNREFERED_SALE_LEAD_SETTING_NAME));
          $form->setField(Pap_Settings::FORCE_CHOOSING_PRODUCTID_SETTING_NAME,
          Gpf_Settings::get(Pap_Settings::FORCE_CHOOSING_PRODUCTID_SETTING_NAME));
          $form->setField(Pap_Settings::TRACK_BY_IP_SETTING_NAME,
          Gpf_Settings::get(Pap_Settings::TRACK_BY_IP_SETTING_NAME));
          $form->setField(Pap_Settings::IP_VALIDITY_SETTING_NAME,
          Gpf_Settings::get(Pap_Settings::IP_VALIDITY_SETTING_NAME));
          $form->setField(Pap_Settings::IP_VALIDITY_FORMAT_SETTING_NAME,
          Gpf_Settings::get(Pap_Settings::IP_VALIDITY_FORMAT_SETTING_NAME));
          $form->setField(Pap_Settings::AUTO_DELETE_RAWCLICKS,
          Gpf_Settings::get(Pap_Settings::AUTO_DELETE_RAWCLICKS));
          $form->setField(Pap_Settings::ALLOW_COMPUTE_NEGATIVE_COMMISSION,
          Gpf_Settings::get(Pap_Settings::ALLOW_COMPUTE_NEGATIVE_COMMISSION));
  
          $form->setField(Pap_Settings::MAIN_SITE_URL,
          Gpf_Settings::get(Pap_Settings::MAIN_SITE_URL));
          $form->setField(Pap_Settings::AUTO_DELETE_EXPIRED_VISITORS,
          Gpf_Settings::get(Pap_Settings::AUTO_DELETE_EXPIRED_VISITORS));
          $form->setField(Pap_Settings::SETTING_LINKING_METHOD,
          Gpf_Settings::get(Pap_Settings::SETTING_LINKING_METHOD));
           
          $form->setField(Pap_Settings::SUPPORT_DIRECT_LINKING, Gpf_Settings::get(Pap_Settings::SUPPORT_DIRECT_LINKING));
          $form->setField(Pap_Settings::SUPPORT_SHORT_ANCHOR_LINKING, Gpf_Settings::get(Pap_Settings::SUPPORT_SHORT_ANCHOR_LINKING));
  
          return $form;
      }
  
      /**
       * @service tracking_setting write
       * @param $fields
       */
      public function save(Gpf_Rpc_Params $params) {
          $form = new Gpf_Rpc_Form($params);
          $this->initValidators($form);
          if (!$form->validate()) {
              return $form;
          }
  
          Gpf_Settings::set(Pap_Settings::DEFAULT_AFFILIATE_SETTING_NAME,
          $form->getFieldValue(Pap_Settings::DEFAULT_AFFILIATE_SETTING_NAME));
          Gpf_Settings::set(Pap_Settings::SAVE_UNREFERED_SALE_LEAD_SETTING_NAME,
          $form->getFieldValue(Pap_Settings::SAVE_UNREFERED_SALE_LEAD_SETTING_NAME));
          Gpf_Settings::set(Pap_Settings::FORCE_CHOOSING_PRODUCTID_SETTING_NAME,
          $form->getFieldValue(Pap_Settings::FORCE_CHOOSING_PRODUCTID_SETTING_NAME));
          Gpf_Settings::set(Pap_Settings::TRACK_BY_IP_SETTING_NAME,
          $form->getFieldValue(Pap_Settings::TRACK_BY_IP_SETTING_NAME));
          Gpf_Settings::set(Pap_Settings::IP_VALIDITY_SETTING_NAME,
          $form->getFieldValue(Pap_Settings::IP_VALIDITY_SETTING_NAME));
          Gpf_Settings::set(Pap_Settings::IP_VALIDITY_FORMAT_SETTING_NAME,
          $form->getFieldValue(Pap_Settings::IP_VALIDITY_FORMAT_SETTING_NAME));
          Gpf_Settings::set(Pap_Settings::MAIN_SITE_URL, $form->getFieldValue(Pap_Settings::MAIN_SITE_URL));
                  Gpf_Settings::set(Pap_Settings::ALLOW_COMPUTE_NEGATIVE_COMMISSION,
          $form->getFieldValue(Pap_Settings::ALLOW_COMPUTE_NEGATIVE_COMMISSION));
          Gpf_Settings::set(Pap_Settings::AUTO_DELETE_EXPIRED_VISITORS,$form->getFieldValue(Pap_Settings::AUTO_DELETE_EXPIRED_VISITORS));
          Gpf_Settings::set(Pap_Settings::SETTING_LINKING_METHOD,
          $form->getFieldValue(Pap_Settings::SETTING_LINKING_METHOD));
          Gpf_Settings::set(Pap_Settings::SUPPORT_DIRECT_LINKING, $form->getFieldValue(Pap_Settings::SUPPORT_DIRECT_LINKING));
          Gpf_Settings::set(Pap_Settings::SUPPORT_SHORT_ANCHOR_LINKING, $form->getFieldValue(Pap_Settings::SUPPORT_SHORT_ANCHOR_LINKING));
          $this->saveDeleteClicks($form);
          $this->insertAutoDeleteExpiredUsersTask();
          
          $form->setInfoMessage($this->_("Tracking saved"));
          return $form;
      }
  
      private function initValidators(Gpf_Rpc_Form $form) {
          $form->addValidator(new Pap_Merchants_Config_TrackingAutoDeleteWithIpValidityValidator($form), Pap_Settings::AUTO_DELETE_RAWCLICKS);
          $form->addValidator(new Pap_Merchants_Config_TrackingAutoDeleteWithRepeatingClicksValidator($form), Pap_Settings::AUTO_DELETE_RAWCLICKS);
      }
  
      private function saveDeleteClicks(Gpf_Rpc_Form $form) {
          Gpf_Settings::set(Pap_Settings::AUTO_DELETE_RAWCLICKS, $form->getFieldValue(Pap_Settings::AUTO_DELETE_RAWCLICKS));
          if (Gpf_Settings::get(Pap_Settings::AUTO_DELETE_RAWCLICKS) > 0) {
              $this->insertTask('Pap_Merchants_Config_DeleteClicksTask');
              return;
          }
          $this->removeTask('Pap_Merchants_Config_DeleteClicksTask');
      }
      
      private function insertAutoDeleteExpiredUsersTask(){
          if(Gpf_Settings::get(Pap_Settings::AUTO_DELETE_EXPIRED_VISITORS) == Gpf::YES){
              $this->insertTask('Pap_Tracking_Visit_DeleteVisitorAffiliatesTask');
          } else{
              $this->removeTask('Pap_Tracking_Visit_DeleteVisitorAffiliatesTask');
          }
      }
  }

} //end Pap_Merchants_Config_TrackingForm

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

if (!class_exists('Pap_Tracking_ActionRequest', false)) {
  class Pap_Tracking_ActionRequest extends Pap_Tracking_Request {
      const PARAM_ACTION_ACCOUNTID = 'AccountId';
      const PARAM_ACTION_ACTIONCODE = "ActionCode";
      const PARAM_ACTION_TOTALCOST = 'TotalCost';
      const PARAM_ACTION_FIXEDCOST = 'FixedCost';
      const PARAM_ACTION_ORDERID = 'OrderID';
      const PARAM_ACTION_PRODUCTID = 'ProductID';
      const PARAM_ACTION_ACTIONTYPE = 'ActionType';
      const PARAM_ACTION_AFFILIATEID = 'AffiliateID';
      const PARAM_ACTION_COOKIEVALUE = 'CookieValue';
      const PARAM_ACTION_CHANNELID = 'ChannelID';
      const PARAM_ACTION_CAMPAIGNID = 'CampaignID';
      const PARAM_ACTION_CURRENCY = 'Currency';
      const PARAM_ACTION_CUSTOM_COMMISSION = 'Commission';
      const PARAM_ACTION_CUSTOM_STATUS = 'PStatus';
      const PARAM_ACTION_TRACKING_METHOD = 'ptm';
      const PARAM_ACTION_CLIENT_SALE_COOKIE = 'fsc';
      const PARAM_ACTION_CLIENT_FIRST_CLICK = 'ffcc';
      const PARAM_ACTION_CLIENT_LAST_CLICK = 'flcc'; 
      const PARAM_ACTION_CUSTOM_TIMESTAMP = 'TimeStamp';
      const PARAM_ACTION_COUPON = 'Coupon';
      const PARAM_ACTION_VISITORID = 'visitorId';
      
      /**
       * @var Pap_Tracking_Cookie
       */
      private $cookie;
      
      function __construct() {
          parent::__construct();
          $this->cookie = new Pap_Tracking_Cookie();
      }
      
      public function getCookieValue() {
          return $this->getRequestParameter(self::PARAM_ACTION_COOKIEVALUE);
      }
      
      /**
       * gets action type (sale/lead) from parameter
       * @return string
       */
      public function getActionType() {
          if(isset($_REQUEST[self::PARAM_ACTION_ACTIONTYPE])) {
              if(in_array($_REQUEST[self::PARAM_ACTION_ACTIONTYPE], array('lead', 'sale')) ) {
                  return $_REQUEST[self::PARAM_ACTION_ACTIONTYPE];
              }
          } 
          return '';
      }
      
      private function percentageTranslate($value) {
      	return str_replace('%25','%',$value);
      }
  
      public function getRawOrderId() {
          return $this->getRequestParameter(self::PARAM_ACTION_ORDERID);
      }
      
      public function setRawOrderId($value) {
          $_REQUEST[self::PARAM_ACTION_ORDERID] = $value;
      }
  
      public function setRawActionCode($value) {
          $_REQUEST[self::PARAM_ACTION_ACTIONCODE] = $value;
      }
      
      public function getRawActionCode() {
          return $this->getRequestParameter(self::PARAM_ACTION_ACTIONCODE);
      }
      
      public function getRawTotalCost() {
          return $this->getRequestParameter(self::PARAM_ACTION_TOTALCOST);
      }
      
      public function setRawTotalCost($value) {
          $_REQUEST[self::PARAM_ACTION_TOTALCOST] = $value;
      }
      
  	public function getRawFixedCost() {
          return $this->percentageTranslate($this->getRequestParameter(self::PARAM_ACTION_FIXEDCOST));
      }
      
  	public function getRawCurrency() {
          return $this->getRequestParameter(self::PARAM_ACTION_CURRENCY);
      }
      
      public function setRawFixedCost($value) {
          $_REQUEST[self::PARAM_ACTION_FIXEDCOST] = $value;
      }
      
      public function getRawAffiliateId() {
          return $this->getRequestParameter(self::PARAM_ACTION_AFFILIATEID);
      }
      
      public function getRawCoupon() {
          return $this->getRequestParameter(self::PARAM_ACTION_COUPON);
      }
      
      public function setRawAffiliateId($value) {
          $_REQUEST[self::PARAM_ACTION_AFFILIATEID] = $value;
      }
      
      
      public function getRawCampaignId() {
          return $this->getRequestParameter(self::PARAM_ACTION_CAMPAIGNID);
      }
      
      public function getRawChannelId() {
          return $this->getRequestParameter(self::PARAM_ACTION_CHANNELID);
      }
      
      public function setRawCampaignId($value) {
          $_REQUEST[self::PARAM_ACTION_CAMPAIGNID] = $value;
      }
      
      public function setRawCoupon($couponCode) {
          $_REQUEST[self::PARAM_ACTION_COUPON] = $couponCode;
      }
      
      
      public function getRawProductId() {
          return $this->getRequestParameter(self::PARAM_ACTION_PRODUCTID);
      }
  
      public function setRawProductId($value) {
          $_REQUEST[self::PARAM_ACTION_PRODUCTID] = $value;
      }
      
      
  	/**
       * gets currency from parameter
       * @return string
       */
      public function getCurrency() {
          return $this->getRequestParameter(self::PARAM_ACTION_CURRENCY);
      }
      
      public function setCurrency($value) {
          $_REQUEST[self::PARAM_ACTION_CURRENCY] = $value;
      }
      
      public function getRawCustomCommission() {
           return $this->percentageTranslate($this->getRequestParameter(self::PARAM_ACTION_CUSTOM_COMMISSION));
      }
      
      public function setRawCustomCommission($value) {
          $_REQUEST[self::PARAM_ACTION_CUSTOM_COMMISSION] = $value;
      }
      
      public function getRawCustomStatus() {
          return $this->getRequestParameter(self::PARAM_ACTION_CUSTOM_STATUS);
      }
      
      public function setRawCustomStatus($value) {
          $_REQUEST[self::PARAM_ACTION_CUSTOM_STATUS] = $value;
      }
      
      public function getRawCustomTimeStamp() {
          return $this->getRequestParameter(self::PARAM_ACTION_CUSTOM_TIMESTAMP);
      }
      
      public function getRecognizedParameters() {
          $params = 'TotalCost='.$this->getRawTotalCost();
          $params .= ' ,AccountId='.$this->getAccountId();
          $params .= ' ,FixedCost='.$this->getRawFixedCost();
          $params .= ' ,OrderID='.$this->getRawOrderID();
          $params .= ' ,ProductID='.$this->getRawProductID();
          //$params .= ',ActionType='.$this->getActionType();
          $params .= ' ,Debug='.$this->getDebug();
          $params .= ' ,data1='.$this->getRawExtraData(1);
          $params .= ' ,data2='.$this->getRawExtraData(2);
          $params .= ' ,data3='.$this->getRawExtraData(3);
          $params .= ' ,data4='.$this->getRawExtraData(4);
          $params .= ' ,data5='.$this->getRawExtraData(5);
          $params .= ' ,AffiliateID='.$this->getRawAffiliateId();
          $params .= ' ,CampaignID='.$this->getRawCampaignId();
          $params .= ' ,Currency='.$this->getCurrency();
          $params .= ' ,Commission='.$this->getRawCustomCommission();
          $params .= ' ,Status='.$this->getRawCustomStatus();
          $params .= ' ,Coupon='.$this->getRawCoupon();
          
          return $params;
      }
  
      public function getTrackingMethod() {
          return $this->getRequestParameter(self::PARAM_ACTION_TRACKING_METHOD);
      }
      
      /**
       * @return Pap_Tracking_Cookie_Sale
       */
      public function getClientSaleCookie() {
          $cookie = new Pap_Tracking_Cookie_Sale();
          $cookie->decode($this->getRequestParameter(self::PARAM_ACTION_CLIENT_SALE_COOKIE));
          return $cookie;
      }    
  
      /**
       * @return Pap_Tracking_Cookie_ClickData
       */
      public function getFirstClickCookie() {
          $cookie = new Pap_Tracking_Cookie_ClickData();
          $cookie->decode($this->getRequestParameter(self::PARAM_ACTION_CLIENT_FIRST_CLICK));
          return $cookie;
      }     
  
      /**
       * @return Pap_Tracking_Cookie_ClickData
       */
      public function getLastClickCookie() {
          $cookie = new Pap_Tracking_Cookie_ClickData();
          $cookie->decode($this->getRequestParameter(self::PARAM_ACTION_CLIENT_LAST_CLICK));
          return $cookie;
      }  
      
      public function getAccountId() {
          return $this->getRequestParameter(self::PARAM_ACTION_ACCOUNTID);
      }
  
      public function getVisitorId() {
          return $this->getRequestParameter(self::PARAM_ACTION_VISITORID);
      }
  }

} //end Pap_Tracking_ActionRequest

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

if (!class_exists('Pap_Db_Table_CommissionTypes', false)) {
  class Pap_Db_Table_CommissionTypes extends Gpf_DbEngine_Table {
      const ID = 'commtypeid';
      const TYPE = 'rtype';
      const STATUS = 'rstatus';
      const NAME = 'name';
      const APPROVAL = 'approval';
      const CODE = 'code';
      const RECURRENCEPRESETID = 'recurrencepresetid';
      const ZEROORDERSCOMMISSION = 'zeroorderscommission';
      const SAVEZEROCOMMISSION = 'savezerocommission';
      const CAMPAIGNID = 'campaignid';
      const FIXEDCOSTVALUE = 'fixedcostvalue';
      const FIXEDCOSTTYPE = 'fixedcosttype';
      const COUNTRYCODES = 'countrycodes';
      const PARENT_COMMISSIONTYPE_ID = 'parentcommtypeid';
  
      private static $instance;
  
      /**
       * @return Pap_Db_Table_CommissionTypes
       */
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
  
      protected function initName() {
          $this->setName('pap_commissiontypes');
      }
  
      public static function getName() {
          return self::getInstance()->name();
      }
  
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
          $this->createColumn(self::TYPE, self::CHAR, 1);
          $this->createColumn(self::STATUS, self::CHAR, 1);
          $this->createColumn(self::NAME, self::CHAR, 40);
          $this->createColumn(self::APPROVAL, self::CHAR, 1);
          $this->createColumn(self::CODE, self::CHAR, 20);
          $this->createColumn(self::RECURRENCEPRESETID, self::CHAR, 8);
          $this->createColumn(self::ZEROORDERSCOMMISSION, self::CHAR, 1);
          $this->createColumn(self::SAVEZEROCOMMISSION, self::CHAR, 1);
          $this->createColumn(self::CAMPAIGNID, self::CHAR, 8);
          $this->createColumn(self::FIXEDCOSTTYPE, self::CHAR, 1);
          $this->createColumn(self::FIXEDCOSTVALUE, self::FLOAT);
          $this->createColumn(self::COUNTRYCODES, 'text');
          $this->createColumn(self::PARENT_COMMISSIONTYPE_ID, self::CHAR, 8);
      }
  
      protected function initConstraints() {
          $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(
                                      array(self::CODE, self::CAMPAIGNID, self::TYPE, self::COUNTRYCODES),
                                      $this->_("Action code must be unique in campaign")));
          $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(
                                      array(self::NAME, self::CAMPAIGNID, self::TYPE, self::COUNTRYCODES),
                                      $this->_("Action name must be unique in campaign")));
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Commissions::TYPE_ID, new Pap_Db_Commission());
      }
  
      /**
       *
       * @param $campaignId
       * @param $commissionType
       * @param $affiliateId
       * @return Gpf_Data_RecordSet
       */
      public function getAllUserCommissionTypes($campaignId = null, $commissionType = null, $affiliateId = null) {
          $selectBuilder = $this->getAllCommissionTypesSelect($campaignId, $commissionType);
          $selectBuilder->from->addInnerJoin(Pap_Db_Table_Campaigns::getName(), 'c', 'ct.'.self::CAMPAIGNID.'=c.'.Pap_Db_Table_Campaigns::ID);
          if (Gpf_Session::getAuthUser()->getAccountId() != Gpf_Db_Account::DEFAULT_ACCOUNT_ID) {
              $selectBuilder->where->add(Pap_Db_Table_Campaigns::ACCOUNTID, '=', Gpf_Session::getAuthUser()->getAccountId());
          }
          if ($affiliateId !== null && $affiliateId !== '') {
              $selectBuilder->from->addLeftJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
                  'ct.'.self::CAMPAIGNID.'=cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID);
              $selectBuilder->from->addLeftJoin(Pap_Db_Table_UserInCommissionGroup::getName(), 'uicg',
                  'cg.'.Pap_Db_Table_CommissionGroups::ID.'=uicg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID);
              $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
                  $subCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
                  $subCondition->add('uicg.'.Pap_Db_Table_UserInCommissionGroup::USER_ID, '=', $affiliateId);
                  $subCondition->add('uicg.'.Pap_Db_Table_UserInCommissionGroup::STATUS, '=', 'A');
              $condition->addCondition($subCondition,  'OR');
              $condition->add('c.'.Pap_Db_Table_Campaigns::TYPE, '=', 'P', 'OR');
              $selectBuilder->where->addCondition($condition);
              $selectBuilder->groupBy->add('ct.'.self::ID);
          }
          return $selectBuilder->getAllRows();
      }
  
      /**
       *
       * @param $campaignId
       * @param $commissionType
       * @return Gpf_SqlBuilder_SelectBuilder
       */
      private function getAllCommissionTypesSelect($campaignId = null, $commissionType = null) {
          $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
          $selectBuilder->select->add('ct.'.self::ID, 'commtypeid');
          $selectBuilder->select->add('ct.'.self::TYPE, 'rtype');
          $selectBuilder->select->add('ct.'.self::STATUS, 'rstatus');
          $selectBuilder->select->add('ct.'.self::NAME, 'name');
          $selectBuilder->select->add('ct.'.self::APPROVAL, 'approval');
          $selectBuilder->select->add('ct.'.self::CODE, 'code');
          $selectBuilder->select->add('ct.'.self::RECURRENCEPRESETID, 'recurrencepresetid');
          $selectBuilder->select->add('ct.'.self::COUNTRYCODES, self::COUNTRYCODES);
          $selectBuilder->select->add('ct.'.self::CAMPAIGNID, self::CAMPAIGNID);
          $selectBuilder->select->add('ct.'.self::PARENT_COMMISSIONTYPE_ID, self::PARENT_COMMISSIONTYPE_ID);
          $selectBuilder->select->add('ct.'.self::ZEROORDERSCOMMISSION, 'zeroorderscommission');
          $selectBuilder->select->add('ct.'.self::SAVEZEROCOMMISSION, 'savezerocommission');
          $selectBuilder->select->add('ct.'.self::FIXEDCOSTTYPE, 'fixedcosttype');
          $selectBuilder->select->add('ct.'.self::FIXEDCOSTVALUE, 'fixedcostvalue');
          $selectBuilder->from->add(self::getName(), 'ct');
          
          if ($commissionType !== null && $commissionType !== '') {
              $selectBuilder->where->add('ct.'.self::TYPE, '=', $commissionType);
          }
          if ($campaignId !== null && $campaignId !== '') {
              $selectBuilder->where->add('ct.'.self::CAMPAIGNID, '=', $campaignId);
          }
          $selectBuilder->orderBy->add('ct.'.self::STATUS, false);
          $selectBuilder->orderBy->add('ct.'.self::TYPE);
          $selectBuilder->orderBy->add('ct.'.self::COUNTRYCODES);
          $selectBuilder->orderBy->add('ct.'.self::NAME);
          return $selectBuilder;
      }
  
      /**
       * @param String $campaignId
       * @param String $commissionType
       * @return Gpf_Data_RecordSet
       */
      public function getAllCommissionTypes($campaignId = null, $commissionType = null) {
          $result = new Gpf_Data_RecordSet('id');
  
          $selectBuilder = $this->getAllCommissionTypesSelect($campaignId, $commissionType);
  
          $result->load($selectBuilder);
          return $result;
      }
  
      /**
       * @return Pap_Db_CommissionType
       */
      public static function getReferralCommissionType() {
      	$commissionType = new Pap_Db_CommissionType();
      	$commissionType->setType(Pap_Db_Transaction::TYPE_REFERRAL);
      	$commissionType->loadFromData(array(self::TYPE));
  
      	return $commissionType;
      }
  
      /**
       * Load commissionType from campaignId and type
       *
       * @throws Gpf_DbEngine_NoRowException
       * @throws Gpf_DbEngine_TooManyRowsException
       *
       * @return Pap_Db_CommissionType
       */
      public function getCommissionType($campaignId, $type) {
          $commissionType = new Pap_Db_CommissionType();
          $commissionType->setCampaignId($campaignId);
          $commissionType->setType($type);
          $commissionType->loadFromData(array(Pap_Db_Table_CommissionTypes::CAMPAIGNID, Pap_Db_Table_CommissionTypes::TYPE));
  
          return $commissionType;
      }
  
  	/**
  	 * @param $commType
  	 * @return boolean
  	 */
  	public static function isSpecialType($rtype) {
  		return in_array($rtype, self::getSpecialTypesArray());
  	}
  
  	/**
  	 * @return array
  	 */
  	public static function getSpecialTypesArray() {
  		return array(Pap_Common_Constants::TYPE_EXTRABONUS,
  		Pap_Common_Constants::TYPE_SIGNUP,
  		Pap_Common_Constants::TYPE_REFUND,
  		Pap_Common_Constants::TYPE_CHARGEBACK,
  		Pap_Common_Constants::TYPE_REFERRAL);
  	}
  }

} //end Pap_Db_Table_CommissionTypes

if (!class_exists('Pap_Db_CommissionType', false)) {
  class Pap_Db_CommissionType extends Gpf_DbEngine_Row {
      const STATUS_ENABLED = 'E';
      const STATUS_DISABLED = 'D';
  
      const APPROVAL_AUTOMATIC = 'A';
      const APPROVAL_MANUAL = 'M';
  
      const COMMISSION_PERCENTAGE = '%';
      const COMMISSION_FIXED = '$';
  
      /* Recurrence types */
      const RECURRENCE_NONE = '';
      const RECURRENCE_DAILY = 'A';
      const RECURRENCE_WEEKLY = 'B';
      const RECURRENCE_MONTHLY = 'C';
      const RECURRENCE_QUARTERLY = 'Q'; // new constant!!!
      const RECURRENCE_SEMIANNUALLY = 'D';
      const RECURRENCE_YEARLY = 'E';
  
      public function __construct(){
          parent::__construct();
      }
  
      public function init() {
          $this->setTable(Pap_Db_Table_CommissionTypes::getInstance());
          parent::init();
      }
  
      public function getId() {
      	return $this->get(Pap_Db_Table_CommissionTypes::ID);
      }
  
      public function setId($commissionTypeId) {
      	$this->set(Pap_Db_Table_CommissionTypes::ID, $commissionTypeId);
      }
  
      public function getApproval() {
      	return $this->get(Pap_Db_Table_CommissionTypes::APPROVAL);
      }
  
      public function getZeroOrdersCommissions() {
      	return $this->get(Pap_Db_Table_CommissionTypes::ZEROORDERSCOMMISSION);
      }
  
      public function getSaveZeroCommissions() {
          return $this->get(Pap_Db_Table_CommissionTypes::SAVEZEROCOMMISSION);
      }
  
      public function getRecurrencePresetId() {
      	return $this->get(Pap_Db_Table_CommissionTypes::RECURRENCEPRESETID);
      }
  
      public function getStatus() {
          return $this->get(Pap_Db_Table_CommissionTypes::STATUS);
      }
  
      public function getType() {
          return $this->get(Pap_Db_Table_CommissionTypes::TYPE);
      }
  
      public function getFixedcostType() {
          return $this->get(Pap_Db_Table_CommissionTypes::FIXEDCOSTTYPE);
      }
  
      public function getFixedcostValue() {
          return $this->get(Pap_Db_Table_CommissionTypes::FIXEDCOSTVALUE);
      }
  
      public function setFixedcostType($value) {
          $this->set(Pap_Db_Table_CommissionTypes::FIXEDCOSTTYPE, $value);
      }
  
      public function setFixedcostValue($value) {
          $this->set(Pap_Db_Table_CommissionTypes::FIXEDCOSTVALUE, $value);
      }
  
      public function setName($value) {
          $this->set(Pap_Db_Table_CommissionTypes::NAME, $value);
      }
  
      public function getName() {
          return $this->get(Pap_Db_Table_CommissionTypes::NAME);
      }
  
      public function getCode() {
          return $this->get(Pap_Db_Table_CommissionTypes::CODE);
      }
  
      public function setCode($code) {
          return $this->set(Pap_Db_Table_CommissionTypes::CODE, $code);
      }
  
      public function setStatus($status) {
          $this->set(Pap_Db_Table_CommissionTypes::STATUS, $status);
      }
  
      public function setCampaignId($campaignId) {
          $this->set(Pap_Db_Table_CommissionTypes::CAMPAIGNID, $campaignId);
      }
  
      public function getCampaignId() {
          return $this->get(Pap_Db_Table_CommissionTypes::CAMPAIGNID);
      }
  
      public function setType($type) {
          $this->set(Pap_Db_Table_CommissionTypes::TYPE, $type);
      }
  
      public function setApproval($approval) {
          $this->set(Pap_Db_Table_CommissionTypes::APPROVAL, $approval);
      }
  
      public function setRecurrencePresetId($recurrenceType) {
          $this->set(Pap_Db_Table_CommissionTypes::RECURRENCEPRESETID, $recurrenceType);
      }
  
      public function getParentCommissionTypeId() {
          return $this->get(Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID);
      }
  
      public function setParentCommissionTypeId($parentId) {
          $this->set(Pap_Db_Table_CommissionTypes::PARENT_COMMISSIONTYPE_ID, $parentId);
      }
  
      public function setCountryCodes($countryCodes) {
          $this->set(Pap_Db_Table_CommissionTypes::COUNTRYCODES, $countryCodes);
      }
  
      public function getCountryCodes() {
          return $this->get(Pap_Db_Table_CommissionTypes::COUNTRYCODES);
      }
  
      /**
       * Sets whether transaction should be saved even if the total cost value is set to zero
       *
       * @param String $zeroOrdersCommission "Y" or "N"
       */
      public function setZeroOrdersCommission($zeroOrdersCommission) {
          $this->set(Pap_Db_Table_CommissionTypes::ZEROORDERSCOMMISSION, $zeroOrdersCommission);
      }
  
      /**
       * Sets whether transaction should be saved even if the commision value is set to zero
       *
       * @param String $saveZeroCommission "Y" or "N"
       */
      public function setSaveZeroCommission($saveZeroCommission) {
          $this->set(Pap_Db_Table_CommissionTypes::SAVEZEROCOMMISSION, $saveZeroCommission);
      }
      
      protected function beforeSaveCheck() {
          parent::beforeSaveCheck();
          
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.CommissionType.beforeSaveCheck', $this);
      }
  }
  

} //end Pap_Db_CommissionType

if (!class_exists('Gpf_SqlBuilder_JoinTable', false)) {
  class Gpf_SqlBuilder_JoinTable extends Gpf_Object implements Gpf_SqlBuilder_FromClauseTable {
      private $type;
      private $name;
      private $alias;
      private $onCondition;
  
      function __construct($type, $name, $alias, $onCondition) {
          $this->type = $type;
          $this->name = $name;
          $this->alias = $alias;
          $this->onCondition = $onCondition;
      }
  
      public function getAlias() {
          return $this->alias;
      }
  
      public function getName() {
          return $this->name;
      }
  
      public function toString() {
          $out = " ".$this->type." JOIN ".$this->name;
          if(!empty($this->alias)) {
              $out .= ' ' . $this->alias;
          }
          $out .= " ON ".$this->onCondition;
          return $out;
      }
  
      public function isJoin() {
          return true;
      }
      
      public function getRequiredPreffixes() {
          $requiredPreffixes = array();
          $matches = array();
          if (preg_match_all("/([a-zA-Z]+)\./", $this->onCondition, $matches) > 0) {
              foreach ($matches[1] as $preffix) {
                  $requiredPreffixes[$preffix] = $preffix;
              }
          }
          return $requiredPreffixes;
      }
  }
  

} //end Gpf_SqlBuilder_JoinTable

if (!class_exists('Pap_Db_Table_CommissionGroups', false)) {
  class Pap_Db_Table_CommissionGroups extends Gpf_DbEngine_Table {
      const ID = 'commissiongroupid';
      const IS_DEFAULT = 'isdefault';
      const NAME = 'name';
      const CAMPAIGN_ID = 'campaignid';
      const COOKIE_LIFE_TIME = 'cookielifetime';
      const PRIORITY = 'priority';
  
      private static $instance;
  
      /**
       * @return Pap_Db_Table_CommissionGroups
       */
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
  
      protected function initName() {
          $this->setName('pap_commissiongroups');
      }
  
      public static function getName() {
          return self::getInstance()->name();
      }
  
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, 'char', 8, true);
          $this->createColumn(self::IS_DEFAULT, 'char', 1);
          $this->createColumn(self::NAME, 'char', 60);
          $this->createColumn(self::CAMPAIGN_ID, 'char', 8);
          $this->createColumn(self::COOKIE_LIFE_TIME, 'int', 0);
          $this->createColumn(self::PRIORITY, 'int');
      }
  
      protected function initConstraints() {
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Commissions::GROUP_ID, new Pap_Db_Commission());
          $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID, new Pap_Db_UserInCommissionGroup());
      }
  
      /**
       * @param String $campaignId
       *
       * @return Gpf_Data_RecordSet
       */
      public function getAllCommissionGroups($campaignId) {
          $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
          $selectBuilder->select->add(self::ID, 'commissiongroupid');
          $selectBuilder->select->add(self::NAME, 'name');
          $selectBuilder->select->add(self::IS_DEFAULT, 'isdefault');
          $selectBuilder->from->add(self::getName());
          $selectBuilder->where->add(self::CAMPAIGN_ID, '=', $campaignId);
  
          return $selectBuilder->getAllRows();
      }
  
      public function getUserCommissionGroup($campaignId, $userId) {
          $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
          $selectBuilder->select->add('cg.'.Pap_Db_Table_CommissionGroups::ID, 'commissiongroupid');
          $selectBuilder->from->add(Pap_Db_Table_Campaigns::getName(), 'ca');
          $selectBuilder->from->addInnerJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
              'ca.'.Pap_Db_Table_Campaigns::ID.'=cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID);
          $selectBuilder->from->addInnerJoin(Pap_Db_Table_UserInCommissionGroup::getName(), 'ucg',
              'cg.'.Pap_Db_Table_CommissionGroups::ID.'=ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID);
          $selectBuilder->where->add('ca.'.Pap_Db_Table_Campaigns::ID, '=', $campaignId);
          $selectBuilder->where->add('ucg.'.Pap_Db_Table_UserInCommissionGroup::USER_ID, '=', $userId);
  
          try {
              $commissionGroupId = $selectBuilder->getOneRow();
          } catch (Gpf_DbEngine_NoRowException $e) {
              return null;
          }
  
          return $commissionGroupId->get('commissiongroupid');
      }
  
      public function getUserInCommissionGroup($campaignId, $userId) {
          $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
          $selectBuilder->select->add('ucg.'.Pap_Db_Table_UserInCommissionGroup::ID, 'usercommgroupid');
          
          $selectBuilder->from->add(Pap_Db_Table_UserInCommissionGroup::getName(), 'ucg');
          $selectBuilder->from->addInnerJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
              'ucg.'.Pap_Db_Table_UserInCommissionGroup::COMMISSION_GROUP_ID.'=cg.'.Pap_Db_Table_CommissionGroups::ID);
          
          $selectBuilder->where->add('cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, '=', $campaignId);
          $selectBuilder->where->add('ucg.'.Pap_Db_Table_UserInCommissionGroup::USER_ID, '=', $userId);
  
          try {
              $userInCommisionGroupId = $selectBuilder->getOneRow();
          } catch (Gpf_DbEngine_NoRowException $e) {
              return null;
          }
  
          return $userInCommisionGroupId->get('usercommgroupid');
      }
  
      /**
       * @service commission_group read
       *
       * @param Gpf_Rpc_Params $params
       * @return Gpf_Rpc_Form
       */
      public function getAllCommissionGroupsForAllCampaigns(Gpf_Rpc_Params $params) {
          $form = new Gpf_Rpc_Form($params);
  
          $select = $this->getCommissionGroupsSelect();
          try {
              $commissionGroupsData = $select->getAllRows();
          } catch (Gpf_DbEngine_NoRowException $e) {
              return $form;
          }
  
          $commissionGroupsData->addColumn('commissiongroupvalue', '');
          $cTable = Pap_Db_Table_Commissions::getInstance();
          $rsCommissions = $cTable->getAllCommissionsInCampaign();
  
          foreach ($commissionGroupsData as $commissionGroupData) {
              $commissionGroupData->set('commissiongroupvalue', $cTable->getCommissionsDescription($commissionGroupData->get(Pap_Db_Table_Campaigns::ID),
              $rsCommissions, $commissionGroupData->get(Pap_Db_Table_CommissionGroups::ID)));
          }
  
          $form->setField('commissionGroups', '', $commissionGroupsData->toObject());
  
          return $form;
      }
  
      /**
       * @service commission_group read
       *
       * @param Gpf_Rpc_Params $params
       * @return Gpf_Rpc_Form
       */
      public function getAllCommissionGroupsForCampaign(Gpf_Rpc_Params $params) {
          $form = new Gpf_Rpc_Form($params);
  
          $select = $this->getCommissionGroupsSelect($params->get('campaignid'));
          try {
              $commissionGroupsData = $select->getAllRows();
          } catch (Gpf_DbEngine_NoRowException $e) {
              return $form;
          }
  
          $cTable = Pap_Db_Table_Commissions::getInstance();
          $rsCommissions = $cTable->getAllCommissionsInCampaign();
  
          $commissionGroups = new Gpf_Data_RecordSet();
          $commissionGroups->setHeader(array('id', 'name', 'commissiongroupvalue'));
  
          foreach ($commissionGroupsData as $commissionGroupData) {
              $commissionGroups->add(array($commissionGroupData->get(Pap_Db_Table_CommissionGroups::ID),
              Pap_Db_Table_CommissionGroups::NAME,
              $commissionGroupData->set('commissiongroupvalue', $cTable->getCommissionsDescription($commissionGroupData->get(Pap_Db_Table_Campaigns::ID),
              $rsCommissions, $commissionGroupData->get(Pap_Db_Table_CommissionGroups::ID)))));
          }
  
          $form->setField('commissionGroups', '', $commissionGroupsData->toObject());
  
          return $form;
      }
  
      private function getCommissionGroupsSelect($campaignId = null) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->select->add('ca.'.Pap_Db_Table_Campaigns::ID);
          $select->select->add('cg.'.Pap_Db_Table_CommissionGroups::ID);
          $select->select->add('cg.'.Pap_Db_Table_CommissionGroups::NAME);
          $select->from->add(Pap_Db_Table_Campaigns::getName(), 'ca');
          $select->from->addInnerJoin(Pap_Db_Table_CommissionGroups::getName(), 'cg',
             'ca.'.Pap_Db_Table_Campaigns::ID.'=cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID);
          if ($campaignId != null) {
              $select->where->add('cg.'.Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, '=', $campaignId);
          }
  
          return $select;
      }
  }

} //end Pap_Db_Table_CommissionGroups

if (!class_exists('Pap_Db_CommissionGroup', false)) {
  class Pap_Db_CommissionGroup extends Gpf_DbEngine_Row {
  	
  	const COOKIE_LIFETIME_VALUE_SAME_AS_CAMPAIGN = -1;
  
      function __construct(){
          parent::__construct();
      }
  
      function init() {
          $this->setTable(Pap_Db_Table_CommissionGroups::getInstance());
          parent::init();
      }
      
      public function getId() {
          return $this->get(Pap_Db_Table_CommissionGroups::ID);
      }
      public function setId($value) {
          $this->set(Pap_Db_Table_CommissionGroups::ID, $value);
      }
          
      public function getIsDefault() {
      	return $this->get(Pap_Db_Table_CommissionGroups::IS_DEFAULT);
      }
      
      /**
       * @return int cookie lifetime in seconds OR -1 if is not defined
       */
      public function getCookieLifetime() {
      	if ($this->get(Pap_Db_Table_CommissionGroups::COOKIE_LIFE_TIME) > self::COOKIE_LIFETIME_VALUE_SAME_AS_CAMPAIGN) {
      		return Pap_Tracking_Cookie::computeLifeTimeDaysToSeconds($this->get(Pap_Db_Table_CommissionGroups::COOKIE_LIFE_TIME));
      	}
      	return self::COOKIE_LIFETIME_VALUE_SAME_AS_CAMPAIGN;
      }
      
      public function setCookieLifetime($value) {
         $this->set(Pap_Db_Table_CommissionGroups::COOKIE_LIFE_TIME, $value);
      }
      
      public function getCampaignId() {
          return $this->get(Pap_Db_Table_CommissionGroups::CAMPAIGN_ID);
      }
          
      public function setDefault($value) {
          $this->set(Pap_Db_Table_CommissionGroups::IS_DEFAULT, $value);
      }
      
      public function getDefault() {
          return $this->get(Pap_Db_Table_CommissionGroups::IS_DEFAULT);
      }
      
      public function setCampaignId($campaignId) {
          $this->set(Pap_Db_Table_CommissionGroups::CAMPAIGN_ID, $campaignId);
      }
      
      public function setName($name) {
          $this->set(Pap_Db_Table_CommissionGroups::NAME, $name);
      }
      
      public function getName() {
          return $this->get(Pap_Db_Table_CommissionGroups::NAME);
      }
      
       public function getPriority() {
          return $this->get(Pap_Db_Table_CommissionGroups::PRIORITY);
      }
      
      public function setPriority($priority) {
          $this->set(Pap_Db_Table_CommissionGroups::PRIORITY, $priority);
      }
      
      
      /**
       * @throws Gpf_DbEngine_NoRowException
       * @param $commiossionGroupId
       * @return Pap_Db_CommissionGroup
       */
      public static function getCommissionGroupById($commiossionGroupId) {
          $commissionGroup = new Pap_Db_CommissionGroup();
          $commissionGroup->setPrimaryKeyValue($commiossionGroupId);
          $commissionGroup->load();
          return $commissionGroup;
      }
  }
  

} //end Pap_Db_CommissionGroup

if (!class_exists('Pap_Tracking_Common_Commission', false)) {
  class Pap_Tracking_Common_Commission extends Gpf_Object {
  
      private $tier;
      private $type;
      private $value;
      private $subtype;
      private $status;
      private $groupid;
      private $typeid;
  
      public function __construct($tier = 1, $type = Pap_Db_CommissionType::COMMISSION_FIXED,
      $value = 0, $subtype = Pap_Db_Table_Commissions::SUBTYPE_NORMAL){
          $this->tier = $tier;
          $this->type = $type;
          $this->value = $value;
          $this->subtype = $subtype;
      }
  
      public function loadFrom(Pap_Db_Commission $commission){
          $this->tier = $commission->getTier();
          $this->type = $commission->getCommissionType();
          $this->value = $commission->getCommissionValue();
          $this->subtype = $commission->getSubtype();
          $this->groupid = $commission->getGroupId();
          $this->typeid = $commission->getCommissionTypeId();
      }
  
      public function getGroupId() {
          return $this->groupid;
      }
  
      public function getCommissionTypeId() {
          return $this->typeid;
      }
  
      public function getTier() {
          return $this->tier;
      }
  
      public function getSubType() {
          return $this->subtype;
      }
  
      public function getStatus() {
          return $this->status;
      }
  
      public function setStatus($status) {
          $this->status = $status;
      }
  
      public function getValue() {
          return $this->value;
      }
  
      public function getType() {
          return $this->type;
      }
  
      public function getCommission($totalCost) {
          if($this->type == Pap_Db_CommissionType::COMMISSION_PERCENTAGE) {
              if(!is_numeric($totalCost)) {
                  throw new Gpf_Exception("    STOPPING, For percentage campaign there has to be TotalCost parameter");
              }
              $returnValue = ($this->value / 100) * $totalCost;
          } else {
              $returnValue = $this->value;
          }
          if (Gpf_Settings::get(Pap_Settings::ALLOW_COMPUTE_NEGATIVE_COMMISSION) == Gpf::NO) {
              $returnValue = ($returnValue < 0 ? 0 : $returnValue);
          }
          return $returnValue;
      }
  
      public function setStatusFromType(Pap_Db_CommissionType $commissionType) {
          if($commissionType->getApproval() == Pap_Db_CommissionType::APPROVAL_AUTOMATIC) {
              $this->setStatus(Pap_Common_Constants::STATUS_APPROVED);
              return;
          }
          $this->setStatus(Pap_Common_Constants::STATUS_PENDING);
      }
  }

} //end Pap_Tracking_Common_Commission

if (!class_exists('Pap_Db_Table_Commissions', false)) {
  class Pap_Db_Table_Commissions extends Gpf_DbEngine_Table {
  	const ID = 'commissionid';
  	const TIER = 'tier';
  	const SUBTYPE = 'subtype';
  	const TYPE = 'commissiontype';
  	const RTYPE = 'rtype';
  	const VALUE = 'commissionvalue';
  	const TYPE_ID = 'commtypeid';
  	const GROUP_ID = 'commissiongroupid';
  
  	const SUBTYPE_NORMAL = 'N';
  	const SUBTYPE_RECURRING = 'R';
  
  	const FIRST_TIER = '1';
  	private static $instance;
  
  	/**
  	 * @return Pap_Db_Table_Commissions
  	 */
  	public static function getInstance() {
  		if(self::$instance === null) {
  			self::$instance = new self;
  		}
  		return self::$instance;
  	}
  
  	protected function initName() {
  		$this->setName('pap_commissions');
  	}
  
  	public static function getName() {
  		return self::getInstance()->name();
  	}
  
  	protected function initColumns() {
  		$this->createPrimaryColumn(self::ID, 'char', 8, true);
  		$this->createColumn(self::TIER, 'int', 0);
  		$this->createColumn(self::SUBTYPE, 'char', 1);
  		$this->createColumn(self::TYPE, 'char', 1);
  		$this->createColumn(self::VALUE, 'float', 0);
  		$this->createColumn(self::TYPE_ID, 'char', 8);
  		$this->createColumn(self::GROUP_ID, 'char', 8);
  	}
  
  	protected function initConstraints() {
  		$this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::TIER,
  		self::SUBTYPE,
  		self::TYPE_ID,
  		self::GROUP_ID)));
  	}
  
  	/**
  	 * returns all commissions for given commission type and group
  	 * If $commissionTypeId is empty, it returns all commissions for this campaign and group
  	 * @param String $commissionTypeId
  	 * @param String $commissionGroupId
  	 * @param String $multiTier
  	 *
  	 * @return Gpf_Data_RecordSet
  	 */
  	public function getAllCommissions($commissionTypeId, $commissionGroupId, $multiTier = 'N', $rtype = null) {
  		$result = new Gpf_Data_RecordSet();
  
  		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
  		$selectBuilder->select->add(self::ID, self::ID);
  		$selectBuilder->select->add(self::TIER, 'tier');
  		$selectBuilder->select->add(self::SUBTYPE, 'subtype');
  		$selectBuilder->select->add(self::TYPE, 'commissiontype');
  		$selectBuilder->select->add(self::VALUE, 'commissionvalue');
  		$selectBuilder->select->add(self::TYPE_ID, 'commtypeid');
  		$selectBuilder->select->add(self::GROUP_ID, 'commissiongroupid');
  		$selectBuilder->from->add(self::getName());
  
  		if ($commissionTypeId != '') {
  			$selectBuilder->where->add(self::TYPE_ID, '=', $commissionTypeId);
  		}
  		if ($commissionGroupId != null) {
  		    $selectBuilder->where->add(self::GROUP_ID, '=', $commissionGroupId);
  		}
  		if ($multiTier == Gpf::YES) {
  			$selectBuilder->where->add(self::TIER, '>', '1');
  		}
  	    if ($rtype != null) {
              $selectBuilder->where->add(self::RTYPE, '=', $rtype);
          }
  		$selectBuilder->orderBy->add(Pap_Db_Table_Commissions::TIER);
  
  		$result->load($selectBuilder);
  
  		return $result;
  	}
  
  	/**
  	 * checks if there are any commission types and commissions defined for this campaign
  	 * returns true if yes, of false
  	 *
  	 * @param unknown_type $campaignId
  	 */
  	public function checkCommissionsExistInCampaign($campaignId) {
  		$result = $this->getAllCommissionsInCampaign($campaignId);
  		if($result->getSize() == 0) {
  			return false;
  		}
  		return true;
  	}
  
  	/**
  	 *
  	 * @param $campaignId
  	 * @param $tier
  	 * @return Gpf_SqlBuilder_SelectBuilder
  	 */
  	public function getAllCommissionsInCampaignSelectBuilder($campaignId, $tier) {
  		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
          $selectBuilder->select->add(self::ID, self::ID);
          $selectBuilder->select->add(self::TYPE, self::TYPE);
          $selectBuilder->select->add(self::VALUE, self::VALUE);
          $selectBuilder->select->add('c.commtypeid', 'commtypeid');
          $selectBuilder->select->add("campaignid", "campaignid");
          $selectBuilder->select->add("ct.countrycodes", "countrycodes");
          $selectBuilder->select->add("commissiongroupid", "commissiongroupid");
          $selectBuilder->select->add("tier", "tier");
          $selectBuilder->select->add('ct.rtype', 'rtype');
          $selectBuilder->select->add('ct.'.Pap_Db_Table_CommissionTypes::NAME, 'commissionTypeName');
          $selectBuilder->from->add(self::getName(), 'c');
          $selectBuilder->from->addInnerJoin(Pap_Db_Table_CommissionTypes::getName(), 'ct', 'c.commtypeid = ct.commtypeid');
          if($campaignId != '') {
              $selectBuilder->where->add('campaignid', '=', $campaignId);
          }
          $selectBuilder->where->add('ct.rstatus', '=', 'E');
          if($tier != '') {
              $selectBuilder->where->add('c.tier', '=', $tier);
          }
          $selectBuilder->where->add('c.subtype', '=', self::SUBTYPE_NORMAL);
          return $selectBuilder;
  	}
  
  	/**
  	 *
  	 * @param $campaignId
  	 * @param $tier
  	 * @return Gpf_Data_RecordSet
  	 */
  	public function getAllCommissionsInCampaign($campaignId = '', $tier = '1') {
  		$result = new Gpf_Data_RecordSet();
  
  		$selectBuilder = $this->getAllCommissionsInCampaignSelectBuilder($campaignId, $tier);
  		$selectBuilder->orderBy->add(self::VALUE);
  
  		$result->load($selectBuilder);
  		return $result;
  	}
  
  	/**
  	 * checks if for this campaign there is at least one active commission defined
  	 *
  	 * @param $campaignId
  	 * @param Gpf_Data_RecordSet $rsCommissionsExist
  	 * @return boolean
  	 */
  	public function findCampaignInCommExistsRecords($campaignId, Gpf_Data_RecordSet $rsCommissions) {
  		if($rsCommissions->getSize() == 0) {
  			return false;
  		}
  
  		foreach($rsCommissions as $record) {
  			if($campaignId == $record->get("campaignid")) {
  				return true;
  			}
  		}
  
  		return false;
  	}
  
  	/**
  	 * returns text description about campaign commissions
  	 *
  	 * @param string $campaignId
  	 * @param Gpf_Data_RecordSet $rsCommissions
  	 * @return string
  	 */
  	public function getCommissionsDescription($campaignId, Gpf_Data_RecordSet $rsCommissions, $commissionGroupId = null, $extendedFormatting = false) {
  		if ($rsCommissions->getSize() == 0) {
  			return $this->_('none active !');
  		}
  
  		if ($commissionGroupId == null) {
  			try {
  				$commissionGroupId = $this->getDefaultCommissionGroup($campaignId);
  			} catch (Gpf_Exception $e) {
  				return $this->_('none active');
  			}
  		}
  
  		$commissions = array();
  		foreach ($rsCommissions as $record) {
  			if ($campaignId != $record->get("campaignid") ||
  			($commissionGroupId != '' && $commissionGroupId != $record->get("commissiongroupid"))) {
  				continue;
  			}
  
  			$rType = $record->get('rtype');
  			$commissions[$rType]['commtype'] = $record->get(Pap_Db_Table_Commissions::TYPE);
  			$commissions[$rType]['value'] = $record->get(Pap_Db_Table_Commissions::VALUE);
  			switch ($rType) {
  				case Pap_Common_Constants::TYPE_CPM:
  					$commissionTypeName = $this->_('CPM');
  					break;
  				case Pap_Common_Constants::TYPE_CLICK:
  					$commissionTypeName = $this->_('per click');
  					break;
  				case Pap_Common_Constants::TYPE_SALE:
  					$commissionTypeName = $this->_('per sale / lead');
  					break;
  				default:
  					$commissionTypeName = $record->get('commissionTypeName');
  					break;
  			}
  			$commissions[$rType]['name'] = $commissionTypeName;
  		}
  
  		$description = '';
  		if ($extendedFormatting) {
  			foreach ($commissions as $rtype => $commission) {
  				$description .= ($description != '' ? '<br>' : '');
  				$description .= $commission['name'].': <strong>'.Pap_Common_Utils_CurrencyUtils::toStandardCurrencyFormat($commission['value'], $commission['commtype']).'</strong>';
  			}
  		} else {
  			foreach ($commissions as $rtype => $commission) {
  				$description .= ($description != '' ? ', ' : '');
  				$description .= $commission['name'].': '.Pap_Common_Utils_CurrencyUtils::toStandardCurrencyFormat($commission['value'], $commission['commtype']);
  			}
  		}
  		if($description == '') {
  			$description = $this->_('none active');
  		}
  
  		return $description;
  	}
  
  	public function getDefaultCommissionGroup($campaignId) {
          return Pap_Db_Table_Campaigns::getInstance()->getDefaultCommissionGroup($campaignId)->getId();
  	}
  
  	public function deleteAllSubtypeCommissions($subType) {
  		$delete = new Gpf_SqlBuilder_DeleteBuilder();
  		$delete->from->add(Pap_Db_Table_Commissions::getName());
  		$delete->where->add(Pap_Db_Table_Commissions::SUBTYPE, "=", $subType);
  		$delete->execute();
  	}
  
     /**
       * @return Gpf_Data_RecordSet
       */
      public function getReferralCommissions() {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->select->add('c.'.Pap_Db_Table_Commissions::ID, Pap_Db_Table_Commissions::ID);
          $select->select->add('c.'.Pap_Db_Table_Commissions::TIER, Pap_Db_Table_Commissions::TIER);
          $select->select->add('c.'.Pap_Db_Table_Commissions::SUBTYPE, Pap_Db_Table_Commissions::SUBTYPE);
          $select->select->add('c.'.Pap_Db_Table_Commissions::TYPE, 'commissiontype');
          $select->select->add('c.'.Pap_Db_Table_Commissions::VALUE, 'commissionvalue');
          $select->select->add('c.'.Pap_Db_Table_Commissions::TYPE_ID, 'commtypeid');
          $select->from->add(Pap_Db_Table_Commissions::getName(), 'c');
          $select->from->addInnerJoin(Pap_Db_Table_CommissionTypes::getName(), 'ct',
              'c.'.Pap_Db_Table_Commissions::TYPE_ID.'=ct.'.Pap_Db_Table_CommissionTypes::ID);
          $select->where->add(Pap_Db_Table_CommissionTypes::TYPE, '=', Pap_Db_Transaction::TYPE_REFERRAL);
          $select->orderBy->add(Pap_Db_Table_Commissions::TIER);
  
          return $select->getAllRows();
      }
  }

} //end Pap_Db_Table_Commissions

if (!class_exists('Pap_Db_Commission', false)) {
  class Pap_Db_Commission extends Gpf_DbEngine_Row {
      
      const COMMISSION_TYPE_PERCENTAGE = '%';
      const COMMISSION_TYPE_FIXED = '$';
  
      public function __construct(){
          parent::__construct();
      }
  
      public function init() {
          $this->setTable(Pap_Db_Table_Commissions::getInstance());
          parent::init();
      }
  
      public function getCommissionType() {
          return $this->get(Pap_Db_Table_Commissions::TYPE);
      }
  
      public function getCommissionValue() {
          return $this->get(Pap_Db_Table_Commissions::VALUE);
      }
      
      public function getGroupId() {
          return $this->get(Pap_Db_Table_Commissions::GROUP_ID);
      }
      
      public function setGroupId($groupId) {
          $this->set(Pap_Db_Table_Commissions::GROUP_ID, $groupId);
      }
      
      public function setType($type) {
          $this->set(Pap_Db_Table_Commissions::TYPE, $type);
      }
      
      public function setTypeId($typeId) {
          $this->set(Pap_Db_Table_Commissions::TYPE_ID, $typeId);
      }
      
      public function setTier($tier) {
          $this->set(Pap_Db_Table_Commissions::TIER, $tier);
      }
      
      public function getTier() {
          return $this->get(Pap_Db_Table_Commissions::TIER);
      }
  
      public function setSubtype($subtype) {
          $this->set(Pap_Db_Table_Commissions::SUBTYPE, $subtype);
      }
      
      public function getSubtype() {
          return $this->get(Pap_Db_Table_Commissions::SUBTYPE);
      }
      
      public function setCommType($type) {
          $this->set(Pap_Db_Table_Commissions::TYPE, $type);
      }
      
      public function setCommissionTypeId($commissionTypeId) {
          $this->set(Pap_Db_Table_Commissions::TYPE_ID, $commissionTypeId);
      }
      
      public function getCommissionTypeId() {
          return $this->get(Pap_Db_Table_Commissions::TYPE_ID);
      }
      
      public function setCommission($value) {
          $this->set(Pap_Db_Table_Commissions::VALUE, $value);
      }
      
      /**
       * deletes tier commission
       * if deleteType == exact, it will delete only given tier
       * if deleteType == above, it will delete given tier and all above
       *
       * @param unknown_type $fromTier
       * @param unknown_type $subType
       * @param unknown_type $commGroupId
       * @param unknown_type $commTypeId
       * @param unknown_type $deleteType
       */
      public function deleteUnusedCommissions($fromTier, $subType, $commGroupId, $commTypeId, $deleteType = 'extact') {
      	$deleteBuilder = new Gpf_SqlBuilder_DeleteBuilder();
      	$deleteBuilder->from->add(Pap_Db_Table_Commissions::getName());
      	$deleteBuilder->where->add('subtype', '=', $subType);
      	if($deleteType == 'above') {
      		$deleteBuilder->where->add('tier', '>', $fromTier);
      	} else {
      		$deleteBuilder->where->add('tier', '=', $fromTier);
      	}
  	    $deleteBuilder->where->add('commtypeid', '=', $commTypeId);
      	$deleteBuilder->where->add('commissiongroupid', '=', $commGroupId);
      	
      	$deleteBuilder->delete();
  	}
  }

} //end Pap_Db_Commission

if (!class_exists('Gpf_Db_Table_Currencies', false)) {
  class Gpf_Db_Table_Currencies extends Gpf_DbEngine_Table {
      const ID = 'currencyid';
      const NAME = 'name';
      const SYMBOL = 'symbol';
      const PRECISION = 'cprecision';
      const IS_DEFAULT = "isdefault";
      const WHEREDISPLAY = 'wheredisplay';
      const EXCHANGERATE = 'exchrate';
      const ACCOUNTID = 'accountid';
  
      private static $instance;
          
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
          
      protected function initName() {
          $this->setName('g_currencies');
      }
      
      public static function getName() {
          return self::getInstance()->name();
      }
      
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, 'char', 8, true);
          $this->createColumn(self::NAME, 'char', 40);
          $this->createColumn(self::SYMBOL, 'char', 20);
          $this->createColumn(self::PRECISION, 'tinyint');
          $this->createColumn(self::IS_DEFAULT, 'tinyint');
          $this->createColumn(self::WHEREDISPLAY, 'tinyint');
          $this->createColumn(self::EXCHANGERATE, 'float', 1);
          $this->createColumn(self::ACCOUNTID, 'char', 8);
      }
      
      protected function initConstraints() {
          $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::NAME, self::ACCOUNTID)));
      }
  }

} //end Gpf_Db_Table_Currencies

if (!class_exists('Gpf_Db_Currency', false)) {
  class Gpf_Db_Currency extends Gpf_DbEngine_Row {
      const DEFAULT_CURRENCY_VALUE = "1";
      const ACCOUNT_ID = "accountid";
      
      const DISPLAY_LEFT = 1;
      const DISPLAY_RIGHT = 2;
      
      /* is default constants */
      const ISDEFAULT_NO = '0';    
      
      function __construct(){
          parent::__construct();
      }
  
      public function init() {
          $this->setTable(Gpf_Db_Table_Currencies::getInstance());
          parent::init();
      }  
  
      /**
       * returns currency found by name or exception
       *
       * @service currency read
       * @param $ids
       * @return Gpf_Db_Currency
       */    
      public function findCurrencyByCode($currencyCode) {
  		$result = new Gpf_Data_RecordSet();
  
  		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::ID, 'currencyid');
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::NAME, 'name');
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::SYMBOL, 'symbol');
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::PRECISION, 'cprecision');
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::IS_DEFAULT, 'isdefault');
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::WHEREDISPLAY, 'wheredisplay');
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::EXCHANGERATE, 'exchrate');
  		$selectBuilder->from->add(Gpf_Db_Table_Currencies::getName());
  		$selectBuilder->where->add('name', '=', $currencyCode);
  		$selectBuilder->limit->set(0, 1);
  		$result->load($selectBuilder);
  
  		if($result->getSize() == 0) {
  			throw new Gpf_DbEngine_NoRowException($selectBuilder);
  		}
  
  		foreach($result as $record) {
  			$currency = new Gpf_Db_Currency();
  			$currency->fillFromRecord($record);
  			return $currency;
  		}    	
  		
  		throw new Gpf_DbEngine_NoRowException($selectBuilder);
  	}    
  	
  	/**
  	 * returns default currency or an exception
  	 *
  	 * @return Gpf_Db_Currency
  	 */
  	public static function getDefaultCurrency() {
  		$result = new Gpf_Data_RecordSet();
  
  		$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::ID, 'currencyid');
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::NAME, 'name');
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::SYMBOL, 'symbol');
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::PRECISION, 'cprecision');
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::IS_DEFAULT, 'isdefault');
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::WHEREDISPLAY, 'wheredisplay');
  		$selectBuilder->select->add(Gpf_Db_Table_Currencies::EXCHANGERATE, 'exchrate');
  		$selectBuilder->from->add(Gpf_Db_Table_Currencies::getName());
  		$selectBuilder->where->add('isdefault', '=', 1);
  		$selectBuilder->limit->set(0, 1);
  		$result->load($selectBuilder);
  
  		if($result->getSize() == 0) {
  			throw new Gpf_DbEngine_NoRowException($selectBuilder);
  		}
  
  		foreach($result as $record) {
  			$currency = new Gpf_Db_Currency();
  			$currency->fillFromRecord($record);
  			return $currency;
  		}    	
  		
  		throw new Gpf_DbEngine_NoRowException($selectBuilder);  		
  	}
  	
  	public function getIsDefault() {
      	return $this->get(Gpf_Db_Table_Currencies::IS_DEFAULT);
      }    
      
  	public function getName() {
      	return $this->get(Gpf_Db_Table_Currencies::NAME);
      }    
  
  	public function getSymbol() {
      	return $this->get(Gpf_Db_Table_Currencies::SYMBOL);
      }    
  
  	public function getWhereDisplay() {
      	return $this->get(Gpf_Db_Table_Currencies::WHEREDISPLAY);
      }    
      
      public function getId() {
      	return $this->get(Gpf_Db_Table_Currencies::ID);
      }    
      
      public function getExchangeRate() {
      	return $this->get(Gpf_Db_Table_Currencies::EXCHANGERATE);
      }    
  
      public function getPrecision() {
      	return $this->get(Gpf_Db_Table_Currencies::PRECISION);
      }
  
      public function setIsDefault($isDefault) {
          return $this->set(Gpf_Db_Table_Currencies::IS_DEFAULT, $isDefault);
      }
      
      public function setAccountId($accountId) {
          $this->set(Gpf_Db_Table_Currencies::ACCOUNTID, $accountId);
      }
  
      public function setId($id) {
          $this->set(Gpf_Db_Table_Currencies::ID, $id);
      }
      
      public function setName($name) {
          $this->set(Gpf_Db_Table_Currencies::NAME, $name);
      }    
  
      public function setSymbol($symbol) {
          $this->set(Gpf_Db_Table_Currencies::SYMBOL, $symbol);
      }    
  
      public function setWhereDisplay($whereDisplay) {
          $this->set(Gpf_Db_Table_Currencies::WHEREDISPLAY, $whereDisplay);
      }    
      
      public function setPrecision($precision) {
          $this->set(Gpf_Db_Table_Currencies::PRECISION, $precision);
      }
      
      public function setExchangeRate($exchangeRate) {
          $this->set(Gpf_Db_Table_Currencies::EXCHANGERATE, $exchangeRate);
      }  
  
      /**
       * Gets currency names for CurrencySearchListBox
       *
       * @service currency read
       * @param $search
       */
      public function getCurrencies(Gpf_Rpc_Params $params) {
          $searchString = $params->get('search');
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->select->add(Gpf_Db_Table_Currencies::ID, "id");
          $select->select->add(Gpf_Db_Table_Currencies::NAME, "name");
          $select->from->add(Gpf_Db_Table_Currencies::getName());
          $select->where->add(Gpf_Db_Table_Currencies::NAME, "LIKE", "%".$searchString."%");
  
          $result = new Gpf_Data_RecordSet();
          $result->load($select);
          
          return $result;
      }
  }
  

} //end Gpf_Db_Currency

if (!class_exists('Pap_Common_SaveCommissionCompoundContext', false)) {
  class Pap_Common_SaveCommissionCompoundContext {
      /**
       * @var Pap_Contexts_Tracking
       */
      private $context;
      private $tier;
      private $user;
      /**
       * @var Pap_Tracking_Common_SaveAllCommissions
       */
      private $saveAllCommissions;
      
      public function __construct(Pap_Contexts_Tracking $context, $tier, Pap_Common_User $user, Pap_Tracking_Common_SaveAllCommissions $saveAllCommissions){
          $this->context = $context;
          $this->tier = $tier;
          $this->user = $user;
          $this->saveAllCommissions = $saveAllCommissions;
      }
      
      /**
       * @return Pap_Contexts_Tracking
       */
      public function getContext(){
          return $this->context;
      }
      
      public function getTier() {
          return $this->tier;
      }
      
      /**
       * @return Pap_Common_User
       */
      public function getUser() {
          return $this->user;
      }
      
      /**
       * @return Pap_Tracking_Common_SaveAllCommissions
       */
      public function getSaveObject() {
          return $this->saveAllCommissions;
      }
  }
  

} //end Pap_Common_SaveCommissionCompoundContext

if (!class_exists('Pap_Tracking_Action_SendTransactionNotificationEmails', false)) {
  class Pap_Tracking_Action_SendTransactionNotificationEmails extends Gpf_Object {
  
      /**
       * @var Pap_Common_Transaction
       */
      private $transaction;
      /**
       * @var Gpf_Settings_AccountSettings
       */
      protected $accountSettings;
  
      public function __construct(Pap_Common_Transaction $transaction) {
          $this->transaction = $transaction;
          $this->accountSettings = $this->createAccountSettings();
      }
  
      public function sendOnNewSaleNotification() {
          $this->sendOnNewSaleNotificationToMerchant();
          $this->sendOnNewSaleNotificationToDirectAffiliate();
      }
  
      public function sendOnNewSaleNotificationToMerchant() {
          try {
              $user = $this->getUser($this->transaction);
          } catch (Gpf_DbEngine_NoRowException $e) {
              Gpf_Log::debug('Sending notification to merchant ended');
              Gpf_Log::error('User with userid=' . $this->transaction->getUserId() . 'can not be loaded and mail can not be send.');
              return;
          }
          $isNotify = $this->accountSettings->get(Pap_Settings::NOTIFICATION_ON_SALE);
          if($isNotify <> Gpf::YES) {
              Gpf_Log::debug('Merchant does not have email notifications on sale');
              return;
          }
  
          if (strstr($this->accountSettings->get(Pap_Settings::NOTIFICATION_ON_SALE_STATUS), $this->transaction->getStatus()) === false) {
              Gpf_Log::debug('Merchant does not have notification for transaction with status '.$this->transaction->getStatus());
              return;
          }
  
          Gpf_Log::debug('Sending normal sale notification');
          $this->sendEmail(new Pap_Mail_MerchantOnSale(), $user,
          $this->transaction, $this->getMerchantEmail());
  
          Gpf_Log::debug('Sending notification to merchant ended');
      }
  
      public function sendOnNewSaleNotificationToDirectAffiliate() {
          try {
              $user = $this->getUser($this->transaction);
          } catch (Gpf_DbEngine_NoRowException $e) {
              Gpf_Log::error('User with userid=' . $this->transaction->getUserId() . 'can not be loaded and mail can not be send.');
              Gpf_Log::debug('Sending notification to affiliate ended');
              return;
          }
          $isNotify = $this->isNotify($user,
          Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_DEFAULT_SETTING_NAME,
          Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_ENABLED_SETTING_NAME,
      	   'aff_notification_on_new_sale',
          $this->transaction->getStatus(),
          Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_STATUS);
  
          if($isNotify <> Gpf::YES) {
              Gpf_Log::debug('Sending new sale notification to affiliate ended. Affiliate '.$user->getId().': '.$user->getName().' does not have new sale notification after sales turned on.');
              return;
          }
  
          $this->sendEmail(new Pap_Mail_AffiliateOnNewSale(), $user, $this->transaction, $user->getEmail());
          Gpf_Log::debug('Sending notification to affiliate ended');
      }
  
      public function sendOnNewSaleNotificationToParentAffiliate() {
          try {
              $user = $this->getUser($this->transaction);
          } catch (Gpf_DbEngine_NoRowException $e) {
              Gpf_Log::error('User with userid=' . $this->transaction->getUserId() . 'can not be loaded and mail can not be send.');
              Gpf_Log::debug('Sending new sale notification to parent affiliate (sub-tier sale) ended.');
              return;
          }
          $isNotify = $this->isNotify($user,
          Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_DEFAULT_SETTING_NAME,
          Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_ENABLED_SETTING_NAME,
      	   "aff_notification_on_subaff_sale");
           
          if ($isNotify <> Gpf::YES) {
              Gpf_Log::debug('Sending new sale notification to parent affiliate (sub-tier sale) ended. Affiliate '.$user->getId().': '.$user->getName().' does not have notification after sub-affiliate sale turned on');
              return;
          }
           
          $this->sendEmail(new Pap_Mail_OnSubAffiliateSale(), $user, $this->transaction, $user->getEmail());
          Gpf_Log::debug('Sending new sale notification to parent affiliate (sub-tier sale) ended.');
      }
  
      public function sendOnChangeStatusNotification() {
          try {
              $user = $this->getUser($this->transaction);
          } catch (Gpf_DbEngine_NoRowException $e) {
              Gpf_Log::error('User with userid=' . $this->transaction->getUserId() . 'can not be loaded and mail can not be send.');
              Gpf_Log::debug('Sending notification to affiliate ended');
              return;
          }
          $isNotify = $this->isNotify($user,
          Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_DEFAULT_SETTING_NAME,
          Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_ENABLED_SETTING_NAME,
              'aff_notification_on_change_comm_status',
          $this->transaction->getStatus(),
          Pap_Settings::AFF_NOTIFICATION_ON_CHANGE_COMMISSION_STATUS_OPTION_STATUS);
  
          if ($isNotify <> Gpf::YES) {
              Gpf_Log::debug('Sending change status notification to affiliate ended. Affiliate '.$user->getId().': '.$user->getName().' does not have change status notification turned on');
              return;
          }
  
          $this->sendEmail(new Pap_Mail_AffiliateChangeCommissionStatus(), $user, $this->transaction, $user->getEmail());
          
          if(Gpf_Settings::get(Pap_Settings::NOTIFICATION_ON_COMMISSION_APPROVED) == Gpf::YES && $this->transaction->getStatus() == Pap_Common_Constants::STATUS_APPROVED){
              $this->sendEmail(new Pap_Mail_MerchantOnCommissionApproved(), $user, $this->transaction, $this->getMerchantEmail());
          }
          
          Gpf_Log::debug('Sending notification to affiliate ended');
      }
  
      private function isNotify(Pap_Common_User $user, $defaultSetting, $enabledSetting, $settingName, $transactionStatus = null, $transactionStatusSettingName = null) {
  
          $isNotify = $this->accountSettings->get($defaultSetting);
          try {
              if ($this->accountSettings->get($enabledSetting) == Gpf::YES) {
                  $isNotify = Gpf_Db_Table_UserAttributes::getSetting($settingName, $user->getAccountUserId());
              }
          } catch(Gpf_Exception $e) {
          }
  
          if ($transactionStatus == null) {
              return $isNotify;
          }
  
          if (strstr($this->accountSettings->get($transactionStatusSettingName), $transactionStatus) === false) {
              return Gpf::NO;
          }
  
          return $isNotify;
      }
  
      protected function sendEmail(Pap_Mail_SaleMail $mailTemplate, $user, Pap_Common_Transaction $transaction, $recipient) {
          $mailTemplate->setUser($user);
          $mailTemplate->setTransaction($transaction);
          $mailTemplate->addRecipient($recipient);
          $mailTemplate->send();
      }
  
      protected function getUser(Pap_Common_Transaction $transaction) {
          return Pap_Common_User::getUserById($transaction->getUserId());
      }
      
      /**
       * @return Gpf_Settings_AccountSettings
       */
      protected function createAccountSettings() {
      	$campaign = new Pap_Common_Campaign();
      	$campaign->setId($this->transaction->getCampaignId());
      	try {
      		$campaign->load();
      		return Gpf_Settings::getAccountSettings($campaign->getAccountId());
      	} catch (Gpf_Exception $e) {
      	}
      	return Gpf_Settings::getAccountSettings(Gpf_Db_Account::DEFAULT_ACCOUNT_ID);
      }
  
      protected function getMerchantEmail() {
      	return $this->accountSettings->get(Pap_Settings::MERCHANT_NOTIFICATION_EMAIL);
      }
  }
  

} //end Pap_Tracking_Action_SendTransactionNotificationEmails

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

if (!class_exists('Pap_Db_Channel', false)) {
  class Pap_Db_Channel extends Gpf_DbEngine_Row {
  
      public function __construct(){
          parent::__construct();
      }
  
      public function init() {
          $this->setTable(Pap_Db_Table_Channels::getInstance());
          parent::init();
      }
      
      public function getId() {
      	return $this->get(Pap_Db_Table_Channels::ID);
      }
      
      public function setId($value) {
      	$this->set(Pap_Db_Table_Channels::ID, $value);
      }
          
      public function getValue() {
      	$value = $this->get(Pap_Db_Table_Channels::VALUE);
      	if($value != null && $value != '') {
      		return $value;
      	}
      	
      	return $this->get(Pap_Db_Table_Channels::ID);
      }
      
      public function setValue($value) {
      	return $this->set(Pap_Db_Table_Channels::VALUE, $value);
      }
          
      public function getName() {
      	return $this->get(Pap_Db_Table_Channels::NAME);
      }
  
      public function setName($value) {
      	$this->set(Pap_Db_Table_Channels::NAME, $value);
      }
      
      public function setPapUserId($value) {
      	$this->set(Pap_Db_Table_Channels::USER_ID, $value);
      }
  
      /**
       * @return Pap_Db_Channel
       * @throws Gpf_Exception
       */
      public static function loadFromId($channelId, $userId) {
          $channel = new Pap_Db_Channel();
          $channel->setPrimaryKeyValue($channelId);
          $channel->setPapUserId($userId);
          try {
              $channel->loadFromData(array(Pap_Db_Table_Channels::ID, Pap_Db_Table_Channels::USER_ID));
              return $channel;
          } catch (Gpf_DbEngine_NoRowException $e) {
              $channel->setValue($channelId);
              $channel->loadFromData(array(Pap_Db_Table_Channels::VALUE, Pap_Db_Table_Channels::USER_ID));
              return $channel;
          }
      }
  }

} //end Pap_Db_Channel

if (!class_exists('Pap_Db_Table_Channels', false)) {
  class Pap_Db_Table_Channels extends Gpf_DbEngine_Table {
  	
      const ID = 'channelid';
      const USER_ID = 'userid';
      const NAME = 'name';
      const VALUE = 'channel';
      
      private static $instance;
          
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
          
      protected function initName() {
          $this->setName('pap_channels');
      }
      
      public static function getName() {
          return self::getInstance()->name();
      }
      
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, 'char', 8, true);
          $this->createColumn(self::USER_ID, 'char', 8);
          $this->createColumn(self::NAME, 'char', 255);
          $this->createColumn(self::VALUE, 'char', 10);
      }
      
      protected function initConstraints() {
         $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_RawClicks::CHANNEL, new Pap_Db_RawClick());
         $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_Clicks::CHANNEL, new Pap_Db_Click());
         
         $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_Impressions::CHANNEL, new Pap_Db_Impression());
         
         $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_DirectLinkUrls::CHANNEL_ID, new Pap_Db_DirectLinkUrl());
         $this->addSetNullDeleteConstraint(self::ID, Pap_Db_Table_Transactions::CHANNEL, new Pap_Db_Transaction());
      }
      
      public static function getUserChannels($userId) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->select->add(self::ID);
          $select->select->add(self::NAME);
          $select->select->add(self::VALUE);
          $select->from->add(self::getName());
          $select->where->add(self::USER_ID, "=", $userId);
      
          return $select->getAllRows();
      }
  }

} //end Pap_Db_Table_Channels

if (!class_exists('Pap_Db_RawClick', false)) {
  class Pap_Db_RawClick extends Gpf_DbEngine_Row {
  
  	const RAW = "R";
  	const UNIQUE = "U";
  	const DECLINED = "D";
  
  	const PROCESSED = 'P';
  
  	function __construct(){
  		parent::__construct();
  	}
  
  	function init() {
  		$this->setTable(Pap_Db_Table_RawClicks::getInstance());
  		parent::init();
  	}
  
  	public function getId() {
  	    return $this->get(Pap_Db_Table_RawClicks::ID);
      }
  
      public function setId($value) {
          $this->set(Pap_Db_Table_RawClicks::ID, $value);
  	}
  	
      public function getCountryCode() {
          return $this->get(Pap_Db_Table_RawClicks::COUNTRYCODE);
      }
  
      public function setCountryCode($countryCode) {
          $this->set(Pap_Db_Table_RawClicks::COUNTRYCODE, $countryCode);
      }
  
  	public function getBannerId() {
  	    return $this->get(Pap_Db_Table_RawClicks::BANNERID);
  	}
  
      public function getUserId() {
          return $this->get(Pap_Db_Table_RawClicks::USERID);
      }
  
  	public function setUserId($id) {
  		$this->set(Pap_Db_Table_RawClicks::USERID, $id);
  	}
  
      public function getCampaignId() {
          return $this->get(Pap_Db_Table_RawClicks::CAMPAIGNID);
      }
  
  	public function setCampaignId($id) {
  		$this->set(Pap_Db_Table_RawClicks::CAMPAIGNID, $id);
  	}
  
  	public function setBannerId($id) {
  		$this->set(Pap_Db_Table_RawClicks::BANNERID, $id);
  	}
  
  	public function setParentBannerId($id) {
  		$this->set(Pap_Db_Table_RawClicks::PARENTBANNERID, $id);
  	}
  
      public function getParentBannerId() {
          return $this->get(Pap_Db_Table_RawClicks::PARENTBANNERID);
      }
  
  	public function setData1($value) {
  		$this->set(Pap_Db_Table_RawClicks::DATA1, $value);
  	}
  
      public function getData1() {
          return $this->get(Pap_Db_Table_RawClicks::DATA1);
      }
  
  	public function setData2($value) {
  		$this->set(Pap_Db_Table_RawClicks::DATA2, $value);
  	}
  
      public function getData2() {
          return $this->get(Pap_Db_Table_RawClicks::DATA2);
      }
  
      public function setChannel($value) {
  		$this->set(Pap_Db_Table_RawClicks::CHANNEL, $value);
  	}
  
      public function getChannel() {
  		return $this->get(Pap_Db_Table_RawClicks::CHANNEL);
      }
  
  	public function setType($value) {
  		$this->set(Pap_Db_Table_RawClicks::RTYPE, $value);
  	}
  
      public function getType() {
          return $this->get(Pap_Db_Table_RawClicks::RTYPE);
      }
  
  	public function setDateTime($value) {
  		$this->set(Pap_Db_Table_RawClicks::DATETIME, $value);
  	}
  
      public function getDateTime() {
          return $this->get(Pap_Db_Table_RawClicks::DATETIME);
      }
  
      public function getDateTimestamp() {
          return strtotime($this->get(Pap_Db_Table_RawClicks::DATETIME));
      }
  
  	public function setRefererUrl($value) {
  		$this->set(Pap_Db_Table_RawClicks::REFERERURL, substr($value, 0, 250));
  	}
  
      public function getRefererUrl() {
          return $this->get(Pap_Db_Table_RawClicks::REFERERURL);
      }
  
  	public function setIp($value) {
  		$this->set(Pap_Db_Table_RawClicks::IP, $value);
  	}
  
      public function getIp() {
          return $this->get(Pap_Db_Table_RawClicks::IP);
      }
  
  	public function setBrowser($value) {
  		$this->set(Pap_Db_Table_RawClicks::BROWSER, $value);
  	}
  
      public function getBrowser() {
          return $this->get(Pap_Db_Table_RawClicks::BROWSER);
      }
  
  	/**
  	 * @param boolean $unique
  	 */
  	public function setUnique($unique) {
  		if ($unique) {
  			$this->setType(self::UNIQUE);
  		} else {
  			$this->setType(self::RAW);
  		}
  	}
  
  	/**
  	 * @param boolean $unique
  	 */
  	public function setProcessedStatus($status) {
  		if ($status) {
  			$this->set(Pap_Db_Table_RawClicks::RSTATUS, self::PROCESSED);
  		} else {
  			$this->set(Pap_Db_Table_RawClicks::RSTATUS, null);
  		}
  	}
  
  	/**
  	 * Get summary of clicks
  	 *
  	 * @param Gpf_SqlBuilder_WhereClause $whereRawClicks
  	 * @return array with count of raw, unique and declined clicks
  	 */
  	public static function getClickCounts(Gpf_SqlBuilder_WhereClause $whereRawClicks) {
  		$select = new Gpf_SqlBuilder_SelectBuilder();
          $select->from->add(Pap_Db_Table_RawClicks::getName(), "rc");
          $select->select->add("SUM(IF(".Pap_Db_Table_RawClicks::RTYPE."='R',1,0))", "raw");
          $select->select->add("SUM(IF(".Pap_Db_Table_RawClicks::RTYPE."='U',1,0))", "uniq");
          $select->select->add("SUM(IF(".Pap_Db_Table_RawClicks::RTYPE."='D',1,0))", "declined");
          $select->where = $whereRawClicks;
          $select->where->add(Pap_Db_Table_RawClicks::RSTATUS, "=", null);
          $clicks = $select->getOneRow();
          $clicks = array("raw" => $clicks->get("raw"), "unique" => $clicks->get("uniq"), "declined" => $clicks->get("declined"));
  
          return $clicks;
  	}
  
      public function getNumberOfClicksFromSameIP($ip, $periodInSeconds, $bannerId = false, $dateCreated) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
  
          $select->select->add("count(clickid)", "count");
          $select->from->add(Pap_Db_Table_RawClicks::getName());
          $select->where->add(Pap_Db_Table_RawClicks::IP, "=", $ip);
          if ($bannerId !== false) {
              $select->where->add(Pap_Db_Table_RawClicks::BANNERID, "=", $bannerId);
          }
          $select->where->add(Pap_Db_Table_RawClicks::RTYPE, "<>", Pap_Db_ClickImpression::STATUS_DECLINED);
          $dateFrom = new Gpf_DateTime($dateCreated);
          $dateFrom->addSecond(-1*$periodInSeconds);
          $select->where->add(Pap_Db_Table_RawClicks::DATETIME, ">", $dateFrom->toDateTime());
  
          $recordSet = new Gpf_Data_RecordSet();
          $recordSet->load($select);
  
          foreach($recordSet as $record) {
          	return $record->get("count");
          }
          return 0;
      }
  
      /**
       * returns latest undeclined click from the given IP address
       *
       * @param string $ip
       * @param int $periodInSeconds
       * @return unknown
       */
      public function getLatestClickFromIP($ip, $periodInSeconds) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
  
          $select->select->add(Pap_Db_Table_RawClicks::USERID, "userid");
          $select->select->add(Pap_Db_Table_RawClicks::CHANNEL, "channel");
          $select->from->add(Pap_Db_Table_RawClicks::getName());
          $select->where->add(Pap_Db_Table_RawClicks::IP, "=", $ip);
          $select->where->add(Pap_Db_Table_RawClicks::RTYPE, "<>", Pap_Db_ClickImpression::STATUS_DECLINED);
          $dateFrom = new Gpf_DateTime();
          $dateFrom->addSecond(-1*$periodInSeconds);
          $select->where->add(Pap_Db_Table_RawClicks::DATETIME, ">", $dateFrom->toDateTime());
          $select->orderBy->add(Pap_Db_Table_RawClicks::DATETIME, false);
          $select->limit->set(0, 1);
  
          $recordSet = new Gpf_Data_RecordSet();
          $recordSet->load($select);
  
          foreach($recordSet as $record) {
          	return array('userid' => $record->get("userid"), 'channel' => $record->get("channel"));
          }
          return null;
      }
  }
  

} //end Pap_Db_RawClick

if (!class_exists('Pap_Db_Table_RawClicks', false)) {
  class Pap_Db_Table_RawClicks extends Gpf_DbEngine_Table {
  
  	const ID = "clickid";
  	const USERID = "userid";
  	const BANNERID = "bannerid";
  	const CAMPAIGNID = "campaignid";
  	const PARENTBANNERID = "parentbannerid";
  	const COUNTRYCODE = "countrycode";
  	const RTYPE = "rtype";
  	const DATETIME = 'datetime';
  	const REFERERURL = "refererurl";
  	const IP = "ip";
  	const BROWSER = "browser";
  	const DATA1 = "cdata1";
  	const DATA2 = "cdata2";
      const CHANNEL = "channel";
  	
  	const RSTATUS = "rstatus";
  
  	private static $instance;
  	    
  	public static function getInstance() {
  	    if(self::$instance === null) {
  	        self::$instance = new self;
  	    }
  	    return self::$instance;
  	}
  	    
  	protected function initName() {
  	    $this->setName('pap_rawclicks');
  	}
      
  	public static function getName() {
          return self::getInstance()->name();
      }
  	
  	function initColumns() {
  		$this->createPrimaryColumn(self::ID, 'int');
  		$this->createColumn(self::USERID, 'char', 8);
  		$this->createColumn(self::BANNERID, 'char', 8);
  		$this->createColumn(self::CAMPAIGNID, 'char', 8);
  		$this->createColumn(self::PARENTBANNERID, 'char', 8);
  		$this->createColumn(self::COUNTRYCODE, 'char', 2);
  		$this->createColumn(self::RTYPE, 'char', 1);
  		$this->createColumn(self::DATETIME, 'datetime');
  		$this->createColumn(self::REFERERURL, 'char', 250);
  		$this->createColumn(self::IP, 'char', 39);
  		$this->createColumn(self::BROWSER, 'char', 6);
  		$this->createColumn(self::DATA1, 'char', 255);
  		$this->createColumn(self::DATA2, 'char', 255);
          $this->createColumn(self::CHANNEL, 'char', 10);
  		$this->createColumn(self::RSTATUS, 'char', 1);
  	}
  }
  

} //end Pap_Db_Table_RawClicks

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

if (!class_exists('Gpf_Db_UserAttribute', false)) {
  class Gpf_Db_UserAttribute extends Gpf_DbEngine_Row {
  
      function init() {
          $this->setTable(Gpf_Db_Table_UserAttributes::getInstance());
          parent::init();
      }
  
      public function setName($name) {
          $this->set(Gpf_Db_Table_UserAttributes::NAME, $name);
      }
  
      public function setAccountUserId($accountUserId) {
          $this->set(Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID, $accountUserId);
      }
  
      public function getValue() {
          return $this->get(Gpf_Db_Table_UserAttributes::VALUE);
      }
  
      public function setValue($value) {
          $this->set(Gpf_Db_Table_UserAttributes::VALUE, $value);
      }
  
      public function getName() {
          return $this->get(Gpf_Db_Table_UserAttributes::NAME);
      }
  
      public function getAccountUserId() {
          return $this->get(Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID);
      }
  
      public function getSetting($name, $accounUsertId = null) {
          if ($accounUsertId == null) {
              $accounUsertId = Gpf_Session::getAuthUser()->getAccountUserId();
          }
          $this->setName($name);
          $this->setAccountUserId($accounUsertId);
          $this->loadFromData(array(Gpf_Db_Table_UserAttributes::NAME, Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID));
          return $this->getValue();
      }
  
      public function save() {
          try {
              $attribute = new Gpf_Db_UserAttribute();
              $attribute->getSetting($this->getName(), $this->getAccountUserId());
              $this->setPrimaryKeyValue($attribute->getPrimaryKeyValue());
              $this->update();
          } catch (Gpf_DbEngine_NoRowException $e) {
              $this->insert();
          }
      }
  
      /**
       * returns recordset of given attribute values for given array of users
       *
       * @param unknown_type $settingsNames
       * @param unknown_type $accountUserIds
       */
      public static function getSettingsForGroupOfUsers($settingsNames, $accountUserIds) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->select->add(Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID, "accountuserid");
          $select->select->add(Gpf_Db_Table_UserAttributes::NAME, "name");
          $select->select->add(Gpf_Db_Table_UserAttributes::VALUE, "value");
  
          $select->from->add(Gpf_Db_Table_UserAttributes::getName());
  
          $select->where->add(Gpf_Db_Table_UserAttributes::NAME, "IN", $settingsNames);
          $select->where->add(Gpf_Db_Table_UserAttributes::ACCOUNT_USER_ID, "IN", $accountUserIds);
  
          $recordSet = $select->getAllRows();
  
          $results = array();
          foreach($recordSet as $record) {
              $results[$record->get('accountuserid')][$record->get('name')] = $record->get('value');
          }
  
          return $results;
      }
  
      public static function saveAttribute($name, $value, $accountUserId = null) {
          $attribute = new Gpf_Db_UserAttribute();
          $attribute->setName($name);
          $attribute->setValue($value);
          if ($accountUserId == null) {
              $attribute->setAccountUserId(Gpf_Session::getInstance()->getAuthUser()->getAccountUserId());
          } else {
              $attribute->setAccountUserId($accountUserId);
          }
          return $attribute->save();
      }
  }
  

} //end Gpf_Db_UserAttribute
/*
VERSION
81c0371ba4760cee63ce8716755d029d
*/
?>
