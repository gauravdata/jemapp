<?php
/** *   @copyright Copyright (c) 2008-2009 Quality Unit s.r.o. *   @author Quality Unit *   @package Core classes *   @since Version 1.0.0 *    *   Licensed under the Quality Unit, s.r.o. Dual License Agreement, *   Version 1.0 (the "License"); you may not use this file except in compliance *   with the License. You may obtain a copy of the License at *   http://www.qualityunit.com/licenses/gpf *    */

if (!class_exists('Gpf_Plugins_Handler', false)) {
  abstract class Gpf_Plugins_Handler extends Gpf_Object {
  
      /**
       * returns instance of handler class.
       * Instance can be either singleton or can create new object for every call
       *
       * @return instance of Gpf_Plugins_Handler child class
       */
      //TODO: This generated warning - not supported in PHP 5.2.x, maybe in next releases of php it will be supported
      //abstract public static function getHandlerInstance();
  }
  

} //end Gpf_Plugins_Handler

if (!class_exists('Gpf_Plugins_Definition', false)) {
  class Gpf_Plugins_Definition extends Gpf_Object {
      const CODE = 'id';
      const NAME = 'name';
      const URL = 'url';
      const DESCRIPTION = 'description';
      const VERSION = 'version';
      const AUTHOR = 'author';
      const AUTHOR_URL = 'author_url';
      const ACTIVE = 'active';
      const HELP = 'help';
      const CONFIG_CLASS_NAME = 'conf_service';
  
      const PLUGIN_TYPE_SYSTEM = "S";
      const PLUGIN_TYPE_NORMAL = "N";
      const PLUGIN_TYPE_FEATURE = "F";
  
      protected $codeName;
      protected $name;
      protected $url;
      protected $description;
      protected $version;
      protected $author = 'Quality Unit, s.r.o.';
      protected $authorUrl;
  
      /**
       * Text of help
       *
       * @var unknown_type
       */
      protected $help;
  
      protected $configurationClassName;
  
      /**
       * System plugin will be not displayed in list of plugins and is always activated
       *
       * @var boolean
       */
      protected $pluginType = self::PLUGIN_TYPE_NORMAL;
  
      private $arrDefines = array();
      private $arrImplements = array();
      private $arrRequirements = array();
      private $arrRejected = array();
  
      /**
       * @param Gpf_Data_RecordSet $recordset
       * @return Gpf_Data_Record
       */
      public function toRecord(Gpf_Data_RecordSet $recordset) {
          $record = $recordset->createRecord();
  
          $record->set(self::CODE, $this->getCodeName());
          $record->set(self::NAME, $this->getName());
          $record->set(self::URL, $this->getUrl());
          $record->set(self::DESCRIPTION, $this->getDescription());
          $record->set(self::VERSION, $this->getVersion());
          $record->set(self::AUTHOR, $this->getAuthor());
          $record->set(self::AUTHOR_URL, $this->getAuthorUrl());
          $record->set(self::HELP, $this->getHelp());
          $record->set(self::ACTIVE, 'N');
          $record->set(self::CONFIG_CLASS_NAME, $this->getConfigurationClassName());
  
          return $record;
      }
  
      public function getCodeName() {
          return $this->codeName;
      }
  
      public function getName() {
          return $this->name;
      }
  
      public function getUrl() {
          return Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_TEXT_BASE_LINK);
      }
  
      public function getDescription() {
          return $this->description;
      }
  
      public function getVersion() {
          return $this->version;
      }
  
      public function getAuthor() {
          return $this->author;
      }
  
      public function getAuthorUrl() {
          return Gpf_Settings::get(Gpf_Settings_Gpf::BRANDING_TEXT_BASE_LINK);
      }
  
      public function getHelp() {
          return $this->help;
      }
  
      /**
       * Add extension point definition
       *
       * @param string $extensionPoint Name of extension point
       * @param string $className Class name of input context
       */
      protected function addDefine($extensionPoint, $className) {
           $this->arrDefines[] = new Gpf_Plugins_Definition_ExtensionPoint($extensionPoint, $className);
      }
  
      /**
       * Extension point with lower priority is executed later
       */
      protected function addImplementation($extensionPoint, $className, $methodName, $priority = 10) {
           $this->arrImplements[] = new Gpf_Plugins_Definition_ExtensionPoint($extensionPoint, $className, $methodName, $priority);;
      }
  
      protected function addRequirement($pluginCode, $minVersion) {
          $this->arrRequirements[] = new Gpf_Plugins_Definition_VersionRequirement($pluginCode, $minVersion);
      }
      
      protected function addRefuse($pluginCode) {    
      	$this->arrRejected[] = $pluginCode;	
      }
  
      /**
       * Return array of extension point definitions (Gpf_Plugins_Definition_ExtensionPoint)
       */
      public function getDefines() {
          return $this->arrDefines;
      }
  
      /**
       * Return array of extension point implementations (Gpf_Plugins_Definition_ExtensionPoint)
       */
      public function getImplements(){
          return $this->arrImplements;
      }
  
      /**
       * Get default priority value.
       * Overwrite this method if is required different priority level.
       *
       * @return int
       */
      public function getPriority() {
          return 10;
      }
  
      public function check() {
      	$this->checkRequirements();
          $this->checkRejectedPlugins();
      }
      
      public function checkRequirements() {
          foreach ($this->arrRequirements as $requirement) {
              $requirement->check();
          }
      }
      
      public function checkRejectedPlugins() {
          foreach ($this->arrRejected as $pluginCode) {
  			if (Gpf_Plugins_Engine::getInstance()->getConfiguration()->isPluginActive($pluginCode)) {
              	throw new Gpf_Exception($this->_("%s refused active %s plugin", $this->codeName, $pluginCode));
  			}
          }
      }
  
      /**
       * Method will be called, when plugin is activated. e.g. create some tables required by plugin.
       *
       * @throws Gpf_Exception when plugin can not be activated
       */
      public function onActivate() {
      }
  
      /**
       * Method will be called, when plugin is deactivated. e.g. drop some tables needed by plugin.
       *
       */
      public function onDeactivate() {
      }
  
      /**
       * Is plugin normal plugin ? If yes, it willnot  be displayed in list of plugins and will be always activated.
       *
       * @return boolean
       */
  
      public function isSystemPlugin() {
          return $this->pluginType == self::PLUGIN_TYPE_SYSTEM;
      }
  
      public function getPluginType() {
          return $this->pluginType;
      }
  
      public function getConfigurationClassName() {
          return $this->configurationClassName;
      }
  }
  
  class Gpf_Plugins_Definition_VersionRequirement extends Gpf_Object {
      private $pluginCode;
      private $minVersion;
  
      public function __construct($pluginCode, $minVersion) {
          $this->pluginCode = $pluginCode;
          $this->minVersion = $minVersion;
      }
  
      public function check() {
          if (!Gpf_Plugins_Engine::getInstance()->getConfiguration()->isPluginActive($this->pluginCode)) {
              throw new Gpf_Exception($this->_("Required plugin %s is not active", $this->pluginCode));
          }
          $plugin = Gpf_Plugins_Engine::getInstance()->findPlugin($this->pluginCode);
          if (version_compare($plugin->getVersion(), $this->minVersion) < 0) {
              throw new Gpf_Exception($this->_("Required plugin %s has to be in version %s or higher", $this->pluginCode, $this->minVersion));
          }
      }
  }

} //end Gpf_Plugins_Definition

if (!class_exists('Pap_Branding', false)) {
  class Pap_Branding {
      const DEFAULT_BRANDING_KNOWLEDGEBASE_LINK = 'http://support.qualityunit.com/knowledgebase/post-affiliate-pro/';
      const DEFAULT_BRANDING_POST_AFFILIATE_PRO_HELP_LINK = 'http://support.qualityunit.com/knowledgebase/post-affiliate-pro.html';
      const DEFAULT_BRANDING_QUALITYUNIT_POSTAFFILIATEPRO_LINK = 'http://www.qualityunit.com/postaffiliatepro/';
      const DEFAULT_BRANDING_QUALITYUNIT_CHANGELOG_LINK = 'http://bugs.qualityunit.com/mantis/changelog_page.php';
      const DEFAULT_BRANDING_QUALITYUNIT_PAP = 'PAP';
      const DEFAULT_BRANDING_TEXT_POST_AFFILIATE_PRO = 'Post Affiliate Pro';
      const DEFAULT_BRANDING_TUTORIAL_VIDEOS_BASE_LINK = 'http://paphelp.qualityunit.com/pap4/';
      const DEFAULT_BRANDING_TEXT = '<a class="papCopyright" href="http://www.qualityunit.com/postaffiliatepro/" target="_blank">Affiliate Software by Post Affiliate Pro</a>';
      
      const DEMO_MERCHANT_USERNAME = 'merchant@example.com';
      const DEMO_AFFILIATE_USERNAME = 'affiliate@example.com';
      const DEMO_PASSWORD = 'demo';
      
      const DEFAULT_MERCHANT_PANEL_THEME = 'blue_aero';
      const DEFAULT_AFFILIATE_PANEL_THEME = 'classic_wide';
      const DEFAULT_SIGNUP_THEME = 'classic';
      
      const DEFAULT_LANGUAGE_CODE = 'en-US';
      
      public static function initDefaultCurrency(Gpf_Db_Currency $currency) {
          $currency->setId('usd00000');
          $currency->setName('USD');
          $currency->setSymbol('$');
          $currency->setPrecision(2);
          $currency->setWhereDisplay(Gpf_Db_Currency::DISPLAY_LEFT);
      }
  }

} //end Pap_Branding

if (!class_exists('Pap_SignupBase', false)) {
  abstract class Pap_SignupBase extends Gpf_ModuleBase {
  
  	/**
  	 * @var Gpf_Rpc_Form
  	 */
  	protected $signupResponse;
  
  	/**
  	 * @return String
  	 */
  	protected abstract function getFormId();
  
  	/**
  	 * @return String
  	 */
  	protected abstract function getSignupTemplateName();
  
  	/**
  	 * @return String
  	 */
  	protected abstract function getSignupSettingsClassName();
  
  	/**
  	 * @return String
  	 */
  	protected abstract function getSignupFormService();
  
  	protected function onStart() {
  		parent::onStart();
  		$this->checkSignupFields();
  		if ($this->isPostRequest()) {
  			$this->signupResponse = $this->processPostRequest();
  		}
  	}
  
  	protected function checkSignupFields() {
  		$dynamicFormPanel = $this->createDynamicFormPanel();
  		$dynamicFormPanel->checkTemplate();
  	}
  
  	/**
  	 * @return Gpf_Ui_DynamicFormPanel
  	 */
  	protected function createDynamicFormPanel() {
  		return new Gpf_Ui_DynamicFormPanel($this->getSignupTemplateName(). '.tpl', $this->getFormId(), "signup");
  	}
  
  	protected function setSessionInfo(Gpf_Rpc_Data $sessionInfo) {
  		Pap_Module::setSessionInfo($sessionInfo);
  	}
  
  	protected function getTitle() {
  		return $this->_localize(Gpf_Settings::get(Pap_Settings::PROGRAM_NAME));
  	}
  
  	protected function initCachedData() {
  		parent::initCachedData();
  		$this->renderSignupFieldsRequest();
  		$this->renderSignupRequest();
  		$this->renderSignupSettingsRequest();
  	}
  
  	protected function initStyleSheets() {
  		parent::initStyleSheets();
  		$this->addStyleSheets(Pap_Module::getStyleSheets());
  	}
  
  	protected function getCachedTemplateNames() {
  		return array('notification_window', 'context_menu', 'icon_button',
                       'window', 'window_left', 'window_header', 'window_header_refresh',
                       'window_bottom_left', 'window_empty_content','task','page_header','button',
                       'loading_screen', 'window_move_panel', 'single_content_panel',
                       'item', 'signup_form', 'icon_button', 'tooltip_popup', 'link_button', 'form_field_checkbox',
                       'form_field', 'button', 'listbox', 'listbox_popup', 'grid_pager', $this->getSignupTemplateName());
  	}
  
  	protected function renderSignupRequest() {
  		if ($this->signupResponse !== null) {
  			$this->renderSignupFormSaveRequest();
  		}
  	}
  
  	protected function renderSignupFormSaveRequest() {
  		Gpf_Rpc_CachedResponse::add($this->signupResponse, $this->getSignupFormService(), "add", "signupFormSaveRequest");
  	}
  
  	protected function renderSignupSettingsRequest() {
  		$signupSettingsClassName = $this->getSignupSettingsClassName();
  		$settings = new $signupSettingsClassName;
  		Gpf_Rpc_CachedResponse::add($settings->getSettingsNoRpc(), $signupSettingsClassName, 'getSettings');
  	}
  
  
  	protected function renderSignupFieldsRequest() {
  		$formFields = Gpf_Db_Table_FormFields::getInstance()->getFieldsNoRpc($this->getFormId(), array(Gpf_Db_FormField::STATUS_MANDATORY, Gpf_Db_FormField::STATUS_OPTIONAL));
  		Gpf_Rpc_CachedResponse::add($formFields, 'Gpf_Db_Table_FormFields', 'getFields');
  		foreach ($formFields as $field) {
  			if ($field->get('type') == 'C') {
  				$this->addCountriesRequest();
  			}
  		}
  	}
  
  	private function addCountriesRequest() {
  		$countryData = new Gpf_Country_CountryData();
  		Gpf_Rpc_CachedResponse::add($countryData->getRows(new Gpf_Rpc_Params()), 'Gpf_Country_CountryData', 'getRows');
  	}
  
  	public function assignModuleAttributes(Gpf_Templates_Template $template) {
  		parent::assignModuleAttributes($template);
  		$template->assign(Pap_Settings::PROGRAM_NAME, Gpf_Settings::get(Pap_Settings::PROGRAM_NAME));
  		$template->assign(Pap_Settings::PROGRAM_LOGO, Gpf_Settings::get(Pap_Settings::PROGRAM_LOGO));
  	}
  
  	protected function assignTemplateVariables($template) {
  		parent::assignTemplateVariables($template);
  		Pap_Module::assignTemplateVariables($template);
  	}
  }

} //end Pap_SignupBase

if (!class_exists('Pap_Signup', false)) {
  class Pap_Signup extends Pap_SignupBase {
  
      public function __construct() {
          parent::__construct('com.qualityunit.pap.SignupApplication', 'signup', 'A');
      }
  
      protected function getFormId() {
          return 'affiliateForm';
      }
  
      protected function getSignupFormService() {
          return 'Pap_Signup_AffiliateForm';
      }
  
      protected function getSignupSettingsClassName() {
          return 'Pap_Common_SignupApplicationSettings';
      }
  
      protected function getSignupTemplateName() {
          return 'signup_fields';
      }
  
      protected function getMainDocumentTemplate() {
          return 'main_signup_html_doc.stpl';
      }
  
      protected function getCachedTemplateNames() {
          return array_merge(parent::getCachedTemplateNames(), array('post_signup_page'));
      }
  
      protected function executePostRequest(Gpf_Rpc_Params $params) {
          $signupFormHandler = new Pap_Signup_AffiliateForm();
          return $signupFormHandler->add($params);
      }
  
      public function getDefaultTheme() {
          $this->initDefaultTheme(Gpf_Settings::get(Pap_Settings::DEFAULT_AFFILIATE_SIGNUP_THEME));
          return parent::getDefaultTheme();
      }
  
      /**
       * Checks if signup form is ok
       *
       * @service affiliate_signup_setting read
       * @param Gpf_Rpc_Params $params
       * @return Gpf_Rpc_Action
       */
      public function checkSignupFieldsRpc(Gpf_Rpc_Params $params) {
          $action = new Gpf_Rpc_Action($params);
          try {
              $this->checkSignupFields();
              $action->addOk();
          } catch (Exception $e) {
              $action->setErrorMessage($e->getMessage());
              $action->addError();
          }
          return $action;
      }
  }

} //end Pap_Signup

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

if (!class_exists('Gpf_Data_RecordSetNoRowException', false)) {
  class Gpf_Data_RecordSetNoRowException extends Gpf_Exception {
      public function __construct($keyValue) {
          parent::__construct("'Row $keyValue does not exist");
      }
      
      protected function logException() {
      }
  }

} //end Gpf_Data_RecordSetNoRowException

if (!class_exists('Gpf_Db_Table_Versions', false)) {
  class Gpf_Db_Table_Versions extends Gpf_DbEngine_Table {
      const ID = 'versionid';
      const NAME = 'name';
      const APPLICATION = 'application';
      const DONE_DATE = 'done';
      
      /**
       *
       * @var Gpf_Db_Table_Versions
       */
      private static $instance;
          
      /**
       *
       * @return Gpf_Db_Table_Versions
       */
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
          
      protected function initName() {
          $this->setName('g_versions');
      }
      
      public static function getName() {
          return self::getInstance()->name();
      }
      
      public function getLatestVersion($application) {
          if(!is_array($application)) {
              $application = array($application);
          }
          
          $sql = new Gpf_SqlBuilder_SelectBuilder();
          $sql->select->add('name');
          $sql->from->add(self::getName(), 'v');
          $sql->where->add('application', 'IN', $application);
          $sql->where->add('done', '!=', null);
          $sql->orderBy->add('done', false);
          $sql->orderBy->add('versionid', false);
          $sql->limit->set(0, 1);;
          $rows = $sql->getAllRows();
          if($rows->getSize() == 0) {
              return false;
          }
          return $rows->getRecord(0)->get('name');
      }
      
      public function isExists() {
          try {
              $this->getLatestVersion('');
              return true;
          } catch (Exception $e) {
          }
          return false;
      }
      
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, 'int', 0, true);
          $this->createColumn(self::NAME, 'char', 40);
          $this->createColumn(self::APPLICATION, 'char', 40);
          $this->createColumn(self::DONE_DATE, 'datetime');
      }
  }
  

} //end Gpf_Db_Table_Versions

if (!class_exists('Gpf_Ui_Widget', false)) {
  abstract class Gpf_Ui_Widget extends Gpf_Object {
      /**
       *
       * @var Gpf_Ui_Page
       */
      protected $page;
  
      protected $code = '';
  
      public function __construct(Gpf_Ui_Page $page = null) {
          $this->page = $page;
          $this->code = get_class($this);
      }
      
      abstract public function render();
  
      public function getCode() {
          return $this->code;
      }
  
  }
  

} //end Gpf_Ui_Widget

if (!class_exists('Gpf_Ui_TemplatePanel', false)) {
  class Gpf_Ui_TemplatePanel extends Gpf_Ui_Widget {
      private $templateHtml;
      protected $templateName;
  
      public function __construct($templateName, $panel='') {
          parent::__construct(null);
          $this->templateName = $templateName;
          $template = new Gpf_Templates_Template($templateName, $panel);
          $this->templateHtml = $template->getHTML();
      }
  
      public function render() {
          return $this->templateHtml;
      }
  
      public function add($widget, $id) {
          $startDivIndex = strpos($this->templateHtml, '<div id="'. $id .'"');
          if (!$startDivIndex) {
              return;
          }
          $startHtml = substr($this->templateHtml, 0, $startDivIndex-1);
  
          $endHtml = substr($this->templateHtml, $startDivIndex);
          $endHtml = substr($endHtml, strpos($endHtml, '</div>')+6);
  
          $this->templateHtml = $startHtml.$widget.$endHtml;
      }
  
      public function addWidget($widget) {
          $this->templateHtml .= $widget;
      }
  
      public function containsId($id) {
          return strpos($this->templateHtml, '<div id="'. $id .'"') !== false;
      }
  }
  

} //end Gpf_Ui_TemplatePanel

if (!class_exists('Gpf_Templates_Template', false)) {
  class Gpf_Templates_Template extends Gpf_Object {
  
      /**
       * @var Gpf_Paths
       */
      private $paths;
      /**
       * @var Gpf_Templates_Smarty
       */
      protected $smarty;
      protected $name;
      protected $theme;
      protected $panel;
      protected $HTML;
      protected $basePath;
  
      const FETCH_FILE = "F";
      const FETCH_TEXT = "T";
  
      /**
       *
       * @param string $templateSource if $fetchType is FETCH_FILE,
       *                                  then $templateSource is template file name
       *                               if $fetchType is FETCH_TEXT,
       *                                  then $templateSource is template source as a string
       * @param string $panelName optional
       * @param string $fetchType FETCH_FILE (default) or FETCH_TEXT
       */
      public function __construct($templateSource, $panelName='', $fetchType=self::FETCH_FILE, $theme='') {
          if ($theme == '') {
              $this->theme = Gpf_Session::getAuthUser()->getTheme();
              $this->paths = Gpf_Paths::getInstance();
          } else {
              $this->theme = $theme;
              $this->paths = Gpf_Paths::getInstance()->clonePaths($theme);
          }
          if ($panelName == '') {
              $this->panel = Gpf_Session::getModule()->getPanelName();
          } else {
              $this->panel = $panelName;
          }
          $this->basePath = $this->paths->getTopPath();
  
          if ($fetchType == self::FETCH_FILE) {
              $this->initFetchFromFile($templateSource);
          } else {
              $this->initFetchFromText($templateSource);
          }
  
          $this->addPluginsDirectories();
  
          $this->setAndCheckCompileDir();
          $this->smarty->register_prefilter(array(&$this,'preProcess'));
  
          $this->assign('basePath', $this->paths->getBaseServerUrl());
          $this->assign('imgPath', $this->getImgUrl());
          $this->assign('logoutUrl', $this->getLogoutUrl());
  
          Gpf_Session::getModule()->assignModuleAttributes($this);
      }
  
      protected function addPluginsDirectories() {
          $paths = $this->paths->getSmartyPluginsPaths();
          foreach ($paths as $path) {
              $this->smarty->plugins_dir[] = $path;
          }
      }
  
      /**
       * @return boolean
       */
      public function isValid() {
          try {
              $this->getHTML();
              return true;
          } catch (Gpf_Templates_SmartySyntaxException $e) {
          }
          return false;
      }
  
      public function assignByRef($tpl_var, &$value) {
          $this->smarty->assign_by_ref($tpl_var, $value);
      }
  
      private function initFetchFromFile($templateName) {
          $this->name = $templateName;
          $this->smarty = new Gpf_Templates_Smarty($templateName, $this->panel);
          $this->smarty->template_dir = $this->getTemplateDir($templateName);
      }
  
      private function initFetchFromText($templateText) {
          $this->name = md5($templateText);
          $this->smarty = new Gpf_Templates_Smarty("text://" . $this->name, $this->panel);
          $this->smarty->setTemplateSource($templateText);
      }
  
      public function getName() {
          return $this->name;
      }
  
      public function setDelimiter($left, $right) {
          $this->smarty->setDelimiter($left, $right);
      }
  
      /**
       * @return Gpf_Io_File
       * @throws Gpf_Exception
       */
      public function getTemplateFile() {
          $file = new Gpf_Io_File($this->paths->getTemplatePath($this->name, $this->panel));
          if (!$file->isExists()) {
              throw new Gpf_Exception('Template '.$this->name.' does not exist');
          }
          return $file;
      }
  
      /**
       * @throws Gpf_Exception
       */
      public function getTemplateSource() {
          return $this->getTemplateFile()->getContents();
      }
  
      public function getTimestamp() {
          return $this->getTemplateFile()->getInodeChangeTime();
      }
  
      public function saveTemplateToFile($templateContent) {
          $file = new Gpf_Io_File($this->paths->getTemplatePath($this->name, $this->panel));
          $file->setFileMode("w");
          $file->write($templateContent);
          $this->deleteCacheFile();
      }
  
      public function deleteCacheFile(){
          $file = new Gpf_Io_File(Gpf_Paths::getInstance()->getCacheAccountDirectory()
          . Gpf_Templates_Smarty::COMPILED_TEMPLATES_DIR
          . $this->panel . '/' . $this->theme . '/' . basename($this->name));
  
          if ($this->theme != rtrim(Gpf_Paths::DEFAULT_THEME, '/')) {
              $this->deleteCacheFileFromDirectory($file->getParent(), $file->getName());
              return;
          }
  
          foreach (new Gpf_Io_DirectoryIterator($file->getParent()->getParent(), '', false, true) as $fullName => $name) {
              $this->deleteCacheFileFromDirectory($fullName, $file->getName());
          }
      }
  
      private function deleteCacheFileFromDirectory($directory, $fileName) {
          foreach (new Gpf_Io_DirectoryIterator($directory, '', false, false) as $fullName => $name) {
              if(strripos($name, $this->encodeFileName($fileName))){
                  $fileToDelete = new Gpf_Io_File($fullName);
                  $fileToDelete->delete();
                  break;
              }
          }
      }
      
      protected function encodeFileName($fileName) {
          return str_replace('=', '%', $this->encodeQP($fileName));
      }
  
      private function encodeQP($str) {
          $search  = array('=',   '+',   '?',   ' ', '!', '@', '#', '$', '%', '^', '&');
          $replace = array('=3D', '=2B', '=3F', '_', '=21', '=40', '=23', '=24', '=25', '=5E', '=26');
          $str = str_replace($search, $replace, $str);
          // Replace all extended characters (\x80-xFF) with their ASCII values.
          return preg_replace_callback(
              '/([\x80-\xFF])/', array('Gpf_Templates_Template', '_qpReplaceCallback'), $str
          );
      }
  
      private function _qpReplaceCallback($matches) {
          return sprintf('=%02X', ord($matches[1]));
      }
  
      /**
       *
       * @return Gpf_Rpc_Data
       */
      public function getDataResponse() {
          $template = new Gpf_Rpc_Data(new Gpf_Rpc_Params());
          $template->setValue("html", $this->getHTML());
          return $template;
  
      }
  
      public function assignAttributes(Gpf_Templates_HasAttributes $data) {
          foreach ($data->getAttributes() as $id => $value) {
              $this->smarty->assign($id, $value);
          }
      }
  
      public function assignNameAttributes($name, Gpf_Templates_HasAttributes $data) {
          $array = array();
          foreach ($data->getAttributes() as $id => $value) {
              $array[$id] = $value;
          }
          $this->smarty->assign($name, $array);
      }
  
      public function assign($varName, $value = null) {
          if($value instanceof Gpf_Data_RecordSet) {
              $this->smarty->assign($varName, $value->toArray());
              return;
          }
          $this->smarty->assign($varName, $value);
      }
  
      public function register_object($name, $object, $methods){
          $this->smarty->register_object($name, $object, $methods);
      }
  
      public function register_function($name, $impl){
          $this->smarty->register_function($name, $impl);
      }
  
      public function preProcess($source, &$smarty) {
          preg_match_all('/##(.+?)##/ms', $source, $attributes, PREG_OFFSET_CAPTURE);
          foreach ($attributes[1] as $index => $attribute) {
              $source = str_replace($attributes[0][$index][0], '{localize str=\'' . addcslashes($attribute[0], "'"). '\'}', $source);
          }
          return $source;
      }
  
      /**
       * @throws Gpf_Templates_SmartySyntaxException
       */
      public function getHTML() {
          return $this->smarty->getText();
      }
  
      public function check() {
          return $this->smarty->checkSyntax();
      }
  
      private function setAndCheckCompileDir() {
          $baseCompileDir = Gpf_Templates_Smarty::getCompileDir();
          $dir = new Gpf_Io_File($baseCompileDir . $this->panel . '/' . $this->theme . '/');
          if(!$dir->isExists()) {
              $dir->mkdir(true);
          }
  
          $this->smarty->compile_dir = $dir->getFileName();
      }
  
      private function getImgUrl() {
          return $this->paths->getTopTemplateUrl() . $this->panel . '/' .  $this->theme . '/img/';
      }
  
      private function getTemplateDir($templateName) {
          return dirname($this->paths->getTemplatePath($templateName, $this->panel));
      }
  
      private function getLogoutUrl() {
      	return Gpf_Paths::getInstance()->getFullScriptsUrl(). 'server.php?C=Gpf_Auth_Service&M=logoutByURL&S=' . Gpf_Session::getInstance()->getId() . '&FormRequest=Y&FormResponse=Y';    	
      }
  }

} //end Gpf_Templates_Template

if (!class_exists('Gpf_Db_Table_FormFields', false)) {
  class Gpf_Db_Table_FormFields extends Gpf_DbEngine_Table {
      const ID = 'formfieldid';
      const ACCOUNTID = 'accountid';
      const FORMID = 'formid';
      const CODE = 'code';
      const NAME = 'name';
      const TYPE = 'rtype';
      const STATUS = 'rstatus';
      const AVAILABLEVALUES = 'availablevalues';
      const ORDER = 'rorder';
      const SECTION = 'sectionid';
  
      private static $instance;
  
      /**
       * @return Gpf_Db_Table_FormFields
       */
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
  
      protected function initName() {
          $this->setName('g_formfields');
      }
  
      public static function getName() {
          return self::getInstance()->name();
      }
  
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, 'int', 0, true);
          $this->createColumn(self::ACCOUNTID, 'char', 8);
          $this->createColumn(self::FORMID, 'char', 40);
          $this->createColumn(self::CODE, 'char', 40);
          $this->createColumn(self::NAME, 'char', 100);
          $this->createColumn(self::TYPE, 'char', 1);
          $this->createColumn(self::STATUS, 'char', 1);
          $this->createColumn(self::AVAILABLEVALUES, 'char');
          $this->createColumn(self::ORDER, 'int');
          $this->createColumn(self::SECTION, 'char', 8);
      }
  
      protected function initConstraints() {
          $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::FORMID, self::CODE)));
      }
  
      /**
       * @param string $formid
       * @param string/array $status
       * @return Gpf_Data_RecordSet
       */
      public function getFieldsNoRpc($formid, $status = null, $mainFields = null) {
          $select = new Gpf_SqlBuilder_SelectBuilder();
          $select->select->add("f." . self::ID, "id");
          $select->select->add("f." . self::CODE, "code");
          $select->select->add("f." . self::NAME, "name");
          $select->select->add("f." . self::TYPE, "type");
          $select->select->add("f." . self::STATUS, "status");
          $select->select->add("f." . self::AVAILABLEVALUES, "availablevalues");
  
          $select->from->add($this->getName(), "f");
  
          $select->where->add(Gpf_Db_Table_Accounts::ID, '=', Gpf_Application::getInstance()->getAccountId());
          $select->where->add(self::FORMID, '=', $formid);
  
          if ($status != null) {
              if (is_array($status)) {
                  $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
                  foreach ($status as $statusCode) {
                      $condition->add(self::STATUS, '=', $statusCode, 'OR');
                  }
                  $select->where->addCondition($condition);
              } else {
                  $select->where->add(self::STATUS, '=', $status);
              }
          }
  
          if ($mainFields != null && $mainFields == Gpf::YES) {
              $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
  
              $condition->add(Gpf_Db_Table_Accounts::ID, '=', Gpf_Application::getInstance()->getAccountId());
              $condition->add(self::FORMID, '=', $formid);
  
              $conditionInner = new Gpf_SqlBuilder_CompoundWhereCondition();
              $conditionInner->add(self::CODE, '=', 'parentuserid', 'OR');
              $conditionInner->add(self::CODE, '=', 'refid', 'OR');
              $conditionInner->add(self::CODE, '=', 'notificationemail', 'OR');
  
              $condition->addCondition($conditionInner);
  
              $select->where->addCondition($condition, 'OR');
          }
  
          //$select->orderBy->add("section");
          $select->orderBy->add(self::ORDER);
  
          $result = $select->getAllRows();
          $result->addColumn("help", "");
          return $result;
      }
  
      /**
       * Loads list of fields for dynamic form panel
       *
       * @anonym
       * @service
       * @param $formId
       * @param $status (comma separated list of statuses)
       */
      public function getFields(Gpf_Rpc_Params $params) {
          $formId = $params->get('formId');
          $status = $params->get('status');
          $mainFields = $params->get('mainFields');
          if ($status == '') {
              $status = null;
          } else {
              $status = explode(",", $status);
          }
          return $this->getFieldsNoRpc($formId, $status, $mainFields);
      }
  }

} //end Gpf_Db_Table_FormFields

if (!class_exists('Gpf_Db_FormField', false)) {
  class Gpf_Db_FormField extends Gpf_DbEngine_Row {
  
      const STATUS_MANDATORY = 'M';
      const STATUS_OPTIONAL = 'O';
      const STATUS_HIDDEN = 'H';
      const STATUS_DISABLED = 'D';
  
      const TYPE_TEXT = 'T';
      const TYPE_TEXT_WITH_DEFAULT = 'D';
      const TYPE_PASSWORD = 'P';
      const TYPE_NUMBER = 'N';
      const TYPE_CHECKBOX = 'B';
      const TYPE_LISTBOX = 'L';
      const TYPE_COUNTRY_LISTBOX = 'C';
      const TYPE_COUNTRY_LISTBOX_GWT = 'S';
  
      const DEFAULT_SECTION = "e2ce2502";
  
      /**
       * @var Gpf_Data_RecordSet
       */
      private $availableValues;
  
      function __construct(){
          parent::__construct();
      }
      
      function init() {
          $this->setTable(Gpf_Db_Table_FormFields::getInstance());
          parent::init();
      }
  
      public function setAccountId($accountId) {
          $this->set(Gpf_Db_Table_FormFields::ACCOUNTID, $accountId);
      }
      
      public function setFormId($formId) {
          $this->set(Gpf_Db_Table_FormFields::FORMID, $formId);
      }
  
      public function setType($type) {
          $this->set(Gpf_Db_Table_FormFields::TYPE, $type);
      }
  
      public function setStatus($status) {
          $this->set(Gpf_Db_Table_FormFields::STATUS, $status);
      }
  
      public function setName($name) {
          $this->set(Gpf_Db_Table_FormFields::NAME, $name);
      }
  
      public function setCode($code) {
          $this->set(Gpf_Db_Table_FormFields::CODE, $code);
      }
  
      public function getType() {
          return $this->get(Gpf_Db_Table_FormFields::TYPE);
      }
  
      public function getCode() {
          return $this->get(Gpf_Db_Table_FormFields::CODE);
      }
  
      public function getId() {
          return $this->get(Gpf_Db_Table_FormFields::ID);
      }
  
      public function setAvailableValues($availableValues) {
          $this->set(Gpf_Db_Table_FormFields::AVAILABLEVALUES, $availableValues);
      }
  
      public function clearAvailableValues() {
          $this->availableValues = new Gpf_Data_RecordSet();
          $this->availableValues->addColumn('id');
          $this->availableValues->addColumn('name');
          $this->setAvailableValues("");
      }
  
      public function addAvailableValue($value, $label) {
          $record = $this->availableValues->createRecord();
          $record->set('id', $value);
          $record->set('name', $label);
          $this->availableValues->addRecord($record);
          $json = new Gpf_Rpc_Json();
          $this->setAvailableValues($json->encodeResponse($this->availableValues));
      }
  }
  

} //end Gpf_Db_FormField

if (!class_exists('Gpf_SqlBuilder_CompoundWhereCondition', false)) {
  class Gpf_SqlBuilder_CompoundWhereCondition extends Gpf_SqlBuilder_WhereClause {
  
      public function toString() {
          $out = '';
          foreach ($this->clause as $key => $columnObj) {
              $out .= $out ? $columnObj['operator'] . ' ' : '';
              $out .= $columnObj['obj']->toString() . ' ';
          }
          if(empty($out)) {
              return '';
          }
          return "($out) ";
      }
  }
  

} //end Gpf_SqlBuilder_CompoundWhereCondition

if (!class_exists('Gpf_Rpc_CachedResponse', false)) {
  class Gpf_Rpc_CachedResponse extends Gpf_Object {
      private $responses = array();
      private $encodedResponses = array();
      /**
       * @var Gpf_Rpc_CachedResponse
       */
      private static $instance = null;
      
      private function __construct() {
          
      }
      
      /**
       * @return Gpf_Rpc_CachedResponse
       */
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new Gpf_Rpc_CachedResponse();
          }
          return self::$instance;
      }
      
      public static function reset() {
          $instance = self::getInstance();
          $instance->clearResponse();
      }
      
      public static function add(Gpf_Rpc_Serializable $response, $className, $methodName, $id = '') {
          $instance = self::getInstance();
          $instance->addResponse($response, $className, $methodName, $id);
      }
      
      public static function addById(Gpf_Rpc_Serializable $response, $id) {
          $instance = self::getInstance();
          $instance->addResponse($response, '', '', $id);
      }
      
      public static function addEncodedById($encodedValue, $id) {
          $instance = self::getInstance();
          $instance->addEncodedResponse($encodedValue, '', '', $id);
      }
      
      private function addEncodedResponse($encodedValue, $className, $methodName, $id = '') {
          $this->encodedResponses[$this->getKey($className, $methodName, $id)] = trim($encodedValue);
      }
      
      private function addResponse(Gpf_Rpc_Serializable $response, $className, $methodName, $id = '') {
          $this->responses[$this->getKey($className, $methodName, $id)] = $response;
      }
      
      private function getKey($className, $methodName, $id) {
          return md5(strtolower($className) . '|' . $methodName . '|' . $id);
      }
      
      private function clearResponse() {
          $this->responses = array();
      }
      
      public static function render() {
          $encoder = new Gpf_Rpc_Json();
          $out = '';
          $response = self::getInstance();
          
          foreach ($response->encodedResponses as $id => $value) {
              $out .= 'window["' . $id . '"]="' . $value . '";';                
          }
          
          foreach ($response->responses as $id => $value) {
              $out .= 'window["' . $id . '"]="' . addcslashes($encoder->encodeResponse($value), '"\\') . '";';                
          }
          return '<script type="text/javascript">' . $out . '</script>';
      }
  }

} //end Gpf_Rpc_CachedResponse

if (!class_exists('Gpf_Templates_Templates', false)) {
  class Gpf_Templates_Templates extends Gpf_Object {
      private $templates = array();
      
      /**
       * Gets template names for template name suggestion oracle
       *
       * @service template read
       * @param $search
       */
      public function getTemplateNames(Gpf_Rpc_Params $params) {
          $searchString = $params->get('search');
          $this->loadTemplates();
  
          $result = new Gpf_Data_RecordSet();
          $result->setHeader(array('id', 'name'));
  
          foreach ($this->templates as $templateName) {
              if ($searchString == "" || strstr($templateName, $searchString) !== false) {
                  $result->add(array($templateName, $templateName . '.tpl'));
              }
          }
          return $result;
      }
  
      public function getAllTemplateNames() {
          $this->loadTemplates();
          return $this->templates;
      }
      
      private function loadTemplates() {
          if(count($this->templates)) {
              return;
          }
          foreach (Gpf_Paths::getInstance()->getTemplateSearchPaths() as $templateDir) {
              $this->loadTemplatesFromDirectory($templateDir);
          }
      }
  
      private function loadTemplatesFromDirectory($dirname) {
          foreach (new Gpf_Io_DirectoryIterator($dirname, '.tpl') as $fullFileName => $fileName) {
              $name = substr($fileName, 0, strrpos($fileName, '.'));
              $this->templates[$name] = $name;
          }
      }
  
      public function addToCache($allowedTemplates) {
          $service = new Gpf_Templates_TemplateService();
          foreach ($allowedTemplates as $templateName) {        	
              if ($this->existTemplate($templateName)) {
                  Gpf_Rpc_CachedResponse::add($service->getTemplateNoRpc($templateName),
                  'Gpf_Templates_TemplateService', 'getTemplate', $templateName);
              }
          }
      }
      
      private function existTemplate($templateName) {
          foreach (Gpf_Paths::getInstance()->getTemplateSearchPaths() as $templateDir) {
          	if (Gpf_Io_File::isFileExists($templateDir . $templateName . ".tpl")) {
          		return true;
          	}
          }
          return false;
      }
  }

} //end Gpf_Templates_Templates

if (!class_exists('Gpf_Templates_TemplateService', false)) {
  class Gpf_Templates_TemplateService extends Gpf_Object {
  
      /**
       * Returns template
       *
       * @service
       * @anonym
       * @param $templateName
       * @return Gpf_Rpc_Serializable
       */
      public function getTemplate(Gpf_Rpc_Params $params) {
          $templateName = $params->get('templateName');
          return $this->getTemplateNoRpc($templateName);
      }
  
      /**
       * Returns missing templates
       *
       * @service
       * @anonym
       * @param $loadedTemplates String of templates names loaded already in client (separated by comma)
       * @return Gpf_Rpc_Serializable
       */
      public function getAllMissingTemplates(Gpf_Rpc_Params $params) {
          $loadedTemplates = explode(',', trim($params->get('loadedTemplates'), ','));
          $recordSet = new Gpf_Data_RecordSet();
          $recordSet->setHeader(array('templateName', 'templateHtml'));
  
          $service = new Gpf_Templates_Templates();
          $allNames = $service->getAllTemplateNames();
          $count = $params->get('templatesCount');
          foreach ($allNames as $templateName) {
              if (!in_array($templateName, $loadedTemplates)) {
                  $template = new Gpf_Templates_Template($templateName.'.tpl');
                  $recordSet->add(array($templateName, $template->getHTML()));
                  $count --;
                  if ($count == 0) break;
              }
          }
          return $recordSet;
      }
  
  
      /**
       * Returns raw template
       *
       * @service
       * @anonym
       * @param $templateName
       * @return Gpf_Data_RecordSet
       */
      public function getRawTemplate(Gpf_Rpc_Params $params) {
          $templateName = $params->get('templateName');
          return $this->createTemplateRecordSet($templateName, $this->getRawTemplateNoRpc($templateName));
      }
  
      /**
       * Returns default raw template
       * (template is not loaded from database)
       *
       * @service
       * @anonym
       * @param $templateName
       * @return Gpf_Data_RecordSet
       */
      public function getDefaultRawTemplate(Gpf_Rpc_Params $params) {
          $templateName = $params->get('templateName');
          return $this->createTemplateRecordSet($templateName, $this->getRawTemplateNoRpc($templateName, false));
      }
  
      public function getRawTemplateNoRpc($templateName, $loadCustomTemplates = true) {
          $template = new Gpf_Templates_Template($templateName.'.tpl', true, $loadCustomTemplates);
          return $template->getTemplateSource();
      }
  
  
      public function getTemplateNoRpc($templateName) {
          $template = new Gpf_Templates_Template($templateName.'.tpl');
          return $this->createTemplateRecordSet($templateName, $template->getHTML());
      }
  
      private function createTemplateRecordSet($templateName, $templateContent) {
          $recordSet = new Gpf_Data_RecordSet();
          $recordSet->setHeader(array('templateName', 'templateHtml'));
          $recordSet->add(array($templateName, $templateContent));
          return $recordSet;
      }
  }
  

} //end Gpf_Templates_TemplateService

if (!interface_exists('Gpf_Rpc_TableData', false)) {
  interface Gpf_Rpc_TableData {
  	
      const SEARCH = 'search';
      
      /**
       * @service
       * @return Gpf_Data_RecordSet
       */
      public function getRow(Gpf_Rpc_Params $params);
      
      /**
       * @service
       * @return Gpf_Data_Table
       */
      public function getRows(Gpf_Rpc_Params $params);
      
  }

} //end Gpf_Rpc_TableData

if (!class_exists('Gpf_Country_CountryData', false)) {
  class Gpf_Country_CountryData extends Gpf_Object implements Gpf_Rpc_TableData {
  
  	/**
  	 * @service
  	 * @anonym
  	 * @return Gpf_Data_RecordSet
  	 */
  	public function getRow(Gpf_Rpc_Params $params) {
  		$select = $this->createCountriesSelect();
  		$select->where->add(Gpf_Db_Table_Countries::COUNTRY_CODE, '=', $params->get(self::SEARCH));
  		$select->limit->set(0, 1);
  		$recordset = $select->getAllRows();
  		foreach ($recordset as $record) {
  			$record->set('name', $this->_localize($record->get('name')));
  		}
  		return $recordset;
  	}
  
  	/**
  	 * @service
  	 * @anonym
  	 * @return Gpf_Data_Table
  	 */
  	public function getRows(Gpf_Rpc_Params $params) {
  		$data = new Gpf_Data_Table($params);
  		$select = $this->createCountriesSelect();
  		$recordset = $select->getAllRows();
  		foreach ($recordset as $record) {
  			$record->set('name', $this->_localize($record->get('name')));
  		}
  		$data->fill($recordset);
  		return $data;
  	}
  
  	/**
  	 * @return Gpf_SqlBuilder_SelectBuilder
  	 */
  	protected function createCountriesSelect() {
  		$select = new Gpf_SqlBuilder_SelectBuilder();
  		$select->select->add(Gpf_Db_Table_Countries::COUNTRY_CODE, 'code');
  		$select->select->add(Gpf_Db_Table_Countries::COUNTRY, 'name');
  		$select->from->add(Gpf_Db_Table_Countries::getName());
  		$select->where->add(Gpf_Db_Table_Countries::STATUS, '=', Gpf_Db_Country::STATUS_ENABLED);
  		$select->orderBy->add(Gpf_Db_Table_Countries::ORDER);
  		$select->orderBy->add(Gpf_Db_Table_Countries::COUNTRY);
  		return $select;
  	}
  		
  }

} //end Gpf_Country_CountryData

if (!class_exists('Gpf_Rpc_Params', false)) {
  class Gpf_Rpc_Params extends Gpf_Object implements Gpf_Rpc_Serializable {
      private $params;
      const CLASS_NAME = 'C';
      const METHOD_NAME = 'M';
      const SESSION_ID = 'S';
      const ACCOUNT_ID = 'aid';
  
      function __construct($params = null) {
          if($params === null) {
              $this->params = new stdClass();
              return;
          }
          $this->params = $params;
      }
  
      public static function createGetRequest($className, $methodName = 'execute', $formRequest = false, $formResponse = false) {
          $requestData = array();
          $requestData[self::CLASS_NAME] = $className;
          $requestData[self::METHOD_NAME] = $methodName;
          $requestData[Gpf_Rpc_Server::FORM_REQUEST] = $formRequest ? Gpf::YES : '';
          $requestData[Gpf_Rpc_Server::FORM_RESPONSE] = $formResponse ? Gpf::YES : '';
          return $requestData;
      }
  
      /**
       *
       * @param unknown_type $className
       * @param unknown_type $methodName
       * @param unknown_type $formRequest
       * @param unknown_type $formResponse
       * @return Gpf_Rpc_Params
       */
      public static function create($className, $methodName = 'execute', $formRequest = false, $formResponse = false) {
          $params = new Gpf_Rpc_Params();
          $obj = new stdClass();
          foreach (self::createGetRequest($className, $methodName, $formRequest, $formResponse) as $name => $value) {
              $params->add($name,$value);
          }
          return $params;
      }
  
      public function setArrayParams(array $params) {
          foreach ($params as $name => $value) {
              $this->add($name, $value);
          }
      }
  
      public function exists($name) {
          if(!is_object($this->params) || !array_key_exists($name, $this->params)) {
              return false;
          }
          return true;
      }
  
      /**
       *
       * @param unknown_type $name
       * @return mixed Return null if $name does not exist.
       */
      public function get($name) {
          if(!$this->exists($name)) {
              return null;
          }
          return $this->params->{$name};
      }
  
      public function set($name, $value) {
          if(!$this->exists($name)) {
              return;
          }
          $this->params->{$name} = $value;
      }
  
      public function add($name, $value) {
          $this->params->{$name} = $value;
      }
  
      public function getClass() {
          return $this->get(self::CLASS_NAME);
      }
  
      public function getMethod() {
          return $this->get(self::METHOD_NAME);
      }
  
      public function getSessionId() {
          $sessionId = $this->get(self::SESSION_ID);
          if ($sessionId === null || strlen(trim($sessionId)) == 0) {
              Gpf_Session::create(new Gpf_ApiModule());
          }
          return $sessionId;
      }
      
      public function clearSessionId() {
          $this->set(self::SESSION_ID, null);
      }
  
      public function getAccountId() {
          return $this->get(self::ACCOUNT_ID);
      }
  
      public function toObject() {
          return $this->params;
      }
  
      public function toText() {
          throw new Gpf_Exception("Unimplemented");
      }
  }
  

} //end Gpf_Rpc_Params

if (!class_exists('Gpf_Data_Table', false)) {
  class Gpf_Data_Table extends Gpf_Object implements Gpf_Rpc_Serializable {
      /**
       * @var Gpf_Data_RecordSet
       */
      private $rows;
      private $count;
      private $from;
      private $to;
  
      public function __construct(Gpf_Rpc_Params $params) {
          $this->from = $params->get("from");
          $this->to = $params->get("to");
      }
      
      public function getFrom() {
          return $this->from;
      }
      
      public function getTo() {
          return $this->to;
      }
      
      public function fill(Gpf_Data_RecordSet $data) {
          $this->rows = $data;
          $this->from = 0;
          $this->to = $this->count = $data->getSize();
      }
  
      public function loadFromObject(stdClass  $object) {
          $this->rows = new Gpf_Data_RecordSet();
          $this->rows->loadFromObject($object->rows);
          $this->count = $object->count;
          $this->from = $object->from;
          $this->to = $object->to;
      }
  
      public function setCount($count) {
          $this->count = $count;
      }
  
      public function setRange($from, $to) {
          $this->from = $from;
          $this->to = $to;
      }
      
      public function setData(Gpf_Data_RecordSet $data) {
          $this->rows = $data;
      }
      
      public function extendRange($extendSize) {
          if (($this->from -= $extendSize) < 0) {
              $this->from = 0;
          }
          if (($this->to += $extendSize) > $this->count) {
              $this->to = $this->count;
          }
     
      }
  
      public function toObject() {
          $object = new stdClass();
          $object->rows = $this->rows->toObject();
          $object->from = $this->from;
          $object->to = $this->to;
          $object->count = $this->count;
          return $object;
      }
  
      public function toText() {
          return "$this->from - $this->to ($this->count)";
      }
  }
  

} //end Gpf_Data_Table

if (!class_exists('Gpf_Db_Table_Countries', false)) {
  class Gpf_Db_Table_Countries extends Gpf_DbEngine_Table {
      const ID = 'countryid';
      const COUNTRY_CODE = 'countrycode';
      const COUNTRY = 'country';
      const STATUS = 'status';
      const ORDER = "rorder";
      const ACCOUNTID = 'accountid';
  
      private static $instance;
          
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
          
      protected function initName() {
          $this->setName('g_countries');
      }
      
      public static function getName() {
          return self::getInstance()->name();
      }
      
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, 'char', 8, true);
          $this->createColumn(self::COUNTRY_CODE, 'char', 8);
          $this->createColumn(self::COUNTRY, 'char', 80);
          $this->createColumn(self::STATUS, 'char', 1);
          $this->createColumn(self::ORDER, 'int');
          $this->createColumn(self::ACCOUNTID, 'char', 8);
      }
      
      protected function initConstraints() {
          $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(array(self::COUNTRY_CODE, self::ACCOUNTID), $this->_('Country code must by unique peer account')));
      }
  }

} //end Gpf_Db_Table_Countries

if (!class_exists('Gpf_Db_Country', false)) {
  class Gpf_Db_Country extends Gpf_DbEngine_Row {
  
      const STATUS_ENABLED = 'E';
      const STATUS_DISABLED = 'D';
  
      function __construct(){
          parent::__construct();
      }
  
      function init() {
          $this->setTable(Gpf_Db_Table_Countries::getInstance());
          parent::init();
      }
  
      public function setAccountId($id) {
          $this->set(Gpf_Db_Table_Countries::ACCOUNTID, $id);
      }
      
      public function setStatusId($id) {
          $this->set(Gpf_Db_Table_Countries::ID, $id);
      }
  
      public function setCountryCode($countryCode) {
          $this->set(Gpf_Db_Table_Countries::COUNTRY_CODE, $countryCode);
      }
  
      public function setCountry($country) {
          $this->set(Gpf_Db_Table_Countries::COUNTRY, $country);
      }
  
      public function setStatus($status) {
          $this->set(Gpf_Db_Table_Countries::STATUS, $status);
      }
  
      public function getCountry() {
          return $this->get(Gpf_Db_Table_Countries::COUNTRY);
      }
  
      public function getStatus() {
          return $this->get(Gpf_Db_Table_Countries::STATUS);
      }
  }
} //end Gpf_Db_Country

if (!class_exists('Gpf_ApplicationSettings', false)) {
  abstract class Gpf_ApplicationSettings extends Gpf_Object {
      
      /**
       * @var Gpf_Data_RecordSet
       */
      private $recordSet;
      
      const CODE = "code";
      const VALUE = "value";
      
      protected function loadSetting() {
          $this->addValue("theme", Gpf_Session::getAuthUser()->getTheme());
          $this->addValue("date_time_format", 'MM/d/yyyy HH:mm:ss');
          $this->addValue("programVersion", Gpf_Application::getInstance()->getVersion());
          $this->addValue(Gpf_Settings_Gpf::NOT_FORCE_EMAIL_USERNAMES, Gpf_Settings::get(Gpf_Settings_Gpf::NOT_FORCE_EMAIL_USERNAMES));
          
          $quickLaunchSettings = new Gpf_Desktop_QuickLaunch();
      	$this->addValue(Gpf_Desktop_QuickLaunch::SHOW_QUICK_LAUNCH, $quickLaunchSettings->getShowQuickLaunch());
          
          $this->addValue(Gpf_Settings_Gpf::REGIONAL_SETTINGS_THOUSANDS_SEPARATOR, Gpf_Settings_Regional::getInstance()->getThousandsSeparator());
          $this->addValue(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DECIMAL_SEPARATOR, Gpf_Settings_Regional::getInstance()->getDecimalSeparator());
          $this->addValue(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DATE_FORMAT, Gpf_Settings_Regional::getInstance()->getDateFormat());
          $this->addValue(Gpf_Settings_Gpf::REGIONAL_SETTINGS_TIME_FORMAT, Gpf_Settings_Regional::getInstance()->getTimeFormat());
          
          Gpf_Plugins_Engine::extensionPoint('Core.loadSetting', $this);
      }
      
      /**
       * @anonym
       * @service
       */
      public function getSettings(Gpf_Rpc_Params $params) {
          return $this->getSettingsNoRpc();
      }
      
      /**
       *
       * @return Gpf_Data_RecordSet
       */
      public function getSettingsNoRpc() {
          $this->recordSet = new Gpf_Data_RecordSet();
          $this->recordSet->setHeader(new Gpf_Data_RecordHeader(array(self::CODE, self::VALUE)));
          $this->loadSetting();
          return $this->recordSet;
      }
      
      public function addValue($code, $value) {
          $record = $this->recordSet->createRecord();
          $record->set(self::CODE, $code);
          $record->set(self::VALUE, $value);
          $this->recordSet->addRecord($record);
      }
      
      protected function addSetting($code) {
          $this->addValue($code, Gpf_Settings::get($code));
      }
  }

} //end Gpf_ApplicationSettings

if (!class_exists('Pap_ApplicationSettings', false)) {
  class Pap_ApplicationSettings extends Gpf_ApplicationSettings {
  
      //TODO - IIF Export Format - easy solution for adding other export button than CSV, if needed other formats need to refactor (find out if plugin is active)
      protected function isPluginActive($codename) {
          if(Gpf_Plugins_Engine::getInstance()->getConfiguration()->isPluginActive($codename)) {
              return "true";
          }
          return "false";
      }
  
      protected function loadSetting() {
          parent::loadSetting();
  
          $this->addSetting(Pap_Settings::AFF_NOTIFICATION_ON_NEW_SALE_ENABLED_SETTING_NAME);
  
          $this->addSetting(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SIGNUP_ENABLED_SETTING_NAME);
          $this->addSetting(Pap_Settings::AFF_NOTIFICATION_ON_SUBAFF_SALE_ENABLED_SETTING_NAME);
  
      	$this->addSetting(Pap_Settings::SUPPORT_DIRECT_LINKING);
          $this->addSetting(Pap_Settings::MAIN_SITE_URL);
  
          $this->addSetting(Pap_Settings::TEXT_BANNER_FORMAT_SETTING_NAME);
          $this->addSetting(Pap_Settings::IMAGE_BANNER_FORMAT_SETTING_NAME);
          $this->addSetting(Pap_Settings::FLASH_BANNER_FORMAT_SETTING_NAME);
  
          $this->addSetting(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO);
          $this->addSetting(Gpf_Settings_Gpf::BRANDING_QUALITY_UNIT);
          $this->addSetting(Pap_Settings::BRANDING_KNOWLEDGEBASE_LINK);
          $this->addSetting(Pap_Settings::BRANDING_POST_AFFILIATE_PRO_HELP_LINK);
          $this->addSetting(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_ENABLED);
          
          $this->addValue(Pap_Settings::PARAM_NAME_USER_ID, Pap_Tracking_Request::getAffiliateClickParamName());
          $this->addValue(Pap_Settings::PARAM_NAME_BANNER_ID, Pap_Tracking_Request::getBannerClickParamName());
          
          $currentTheme = new Gpf_Desktop_Theme();
          $this->addValue("desktopMode", $currentTheme->getDesktopMode());
  
          //TODO - IIF Export Format - easy solution for adding other export button than CSV, if needed other formats need to refactor (find out if plugin is active)
          $this->addValue("quickBooksPluginActive", $this->isPluginActive('QuickBooks'));
  
  
         	try {
         	    $defaultCurrency = Gpf_Db_Currency::getDefaultCurrency();
         	    $this->addValue("currency_symbol", $defaultCurrency->getSymbol());
         	    $this->addValue("currency_precision", $defaultCurrency->getPrecision());
         	    $this->addValue("currency_wheredisplay", $defaultCurrency->getWhereDisplay());
         	} catch(Gpf_Exception $e) {
         	    $this->addValue("currency_symbol", "Unknown");
         	    $this->addValue("currency_precision", 2);
         	    $this->addValue("currency_wheredisplay", 1);
         	}
      }    
  }

} //end Pap_ApplicationSettings

if (!class_exists('Pap_Common_SignupApplicationSettings', false)) {
  class Pap_Common_SignupApplicationSettings extends Pap_ApplicationSettings {
  
      protected function loadSetting() {
          parent::loadSetting();
          $this->addSetting(Pap_Settings::POST_SIGNUP_TYPE_SETTING_NAME);
          $this->addSetting(Pap_Settings::POST_SIGNUP_URL_SETTING_NAME);
          $this->addValue(Pap_Settings::SIGNUP_TERMS_SETTING_NAME, Gpf_Lang::_localizeRuntime(Gpf_Settings::get(Pap_Settings::SIGNUP_TERMS_SETTING_NAME)));
          $this->addSetting(Pap_Settings::INCLUDE_PAYOUT_OPTIONS);
          $this->addSetting(Pap_Settings::PAYOUT_OPTIONS);
          $this->addSetting(Pap_Settings::FORCE_PAYOUT_OPTION);
          $this->addSetting(Pap_Settings::FORCE_TERMS_ACCEPTANCE_SETTING_NAME);
          $this->addSetting(Pap_Settings::DEFAULT_PAYOUT_METHOD);
          $this->loadParentFromRequest();
          $this->addDefaultCountry();
      }
  
      private function addDefaultCountry() {
          $form = new Gpf_Country_CountryForm();
          $result = $form->loadDefaultCountry(new Gpf_Rpc_Params());
          $this->addValue('defaultCountry', $result->getValue('default'));
      }
  
      private function loadParentFromRequest() {
          $affiliateParamName = Pap_Tracking_Request::getAffiliateClickParamName();
          if (array_key_exists($affiliateParamName, $_REQUEST)) {
              $this->addValue("parentAffiliateIdFromRequest", $_REQUEST[$affiliateParamName]);
          } else {
              $this->addValue("parentAffiliateIdFromRequest", "");
          }
      }
  }

} //end Pap_Common_SignupApplicationSettings

if (!class_exists('Gpf_Desktop_QuickLaunch', false)) {
  class Gpf_Desktop_QuickLaunch extends Gpf_Object {
  
  	const SHOW_QUICK_LAUNCH = 'showQuickLaunch';
  	const SHOW_QUICK_LAUNCH_DEFAULT_VALUE = 'Y';
  
  	/**
  	 * @service quicklaunch read
  	 * @param Gpf_Rpc_Params $params
  	 */
  	public function load(Gpf_Rpc_Params $params) {
  		$form = new Gpf_Rpc_Form();
  		$form->addField(self::SHOW_QUICK_LAUNCH, $this->getShowQuickLaunch());
  		return $form;
  	}
  
  	/**
  	 * @service quicklaunch write
  	 * @param Gpf_Rpc_Params $params
  	 */
  	public function save(Gpf_Rpc_Params $params) {
  		$form = new Gpf_Rpc_Form($params);
  
  		try {
  			Gpf_Db_UserAttribute::saveAttribute(self::SHOW_QUICK_LAUNCH, $form->getFieldValue(self::SHOW_QUICK_LAUNCH));
  		} catch (Gpf_Exception $e) {
  			$form->setErrorMessage($this->_('Failed to save quick launch settings with error %s', $e->getMessage()));
  			return $form;
  		}
  
  		$form->setInfoMessage($this->_('Quick launch saved.'));
  
  		return $form;
  	}
  
  	/**
  	 * @return String
  	 */
  	public function getShowQuickLaunch() {
  		try {
  			$attributes = $this->getAttributes();
  			if (isset($attributes[self::SHOW_QUICK_LAUNCH])) {
  				return $attributes[self::SHOW_QUICK_LAUNCH];
  			}
  		} catch (Gpf_Exception $e) {
  		}
  		return self::SHOW_QUICK_LAUNCH_DEFAULT_VALUE;
  	}
  
  	/**
  	 * @return array
  	 */
  	private function getAttributes() {
  		$attributes = Gpf_Db_UserAttribute::getSettingsForGroupOfUsers(array(self::SHOW_QUICK_LAUNCH),
  		array($this->getAccountUserId()));
  
  		if (isset($attributes[$this->getAccountUserId()])) {
  			return $attributes[$this->getAccountUserId()];
  		}
  		throw new Gpf_Exception($this->_('Settings not exists, load default settings.'));
  	}
  
  	/**
  	 * @return String
  	 */
  	private function getAccountUserId() {
  		return Gpf_Session::getInstance()->getAuthUser()->getAccountUserId();
  	}
  }

} //end Gpf_Desktop_QuickLaunch

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

if (!class_exists('Gpf_Settings_Regional', false)) {
  class Gpf_Settings_Regional {
  
      /**
       * @var Gpf_Settings_Regional
       */
      private static $instance;
  
      private $thousandsSeparator, $decimalSeparator, $dateFormat, $timeFormat;
  
      /**
       * @return Gpf_Settings_Regional
       */
      public function getInstance() {
          if (self::$instance == null) {
              self::$instance = new Gpf_Settings_Regional();
          }
          return self::$instance;
      }
  
      private function __construct() {
          $this->loadSettings();
      }
  
      public function getThousandsSeparator() {
          return $this->thousandsSeparator;
      }
  
      public function getDecimalSeparator() {
          return $this->decimalSeparator;
      }
  
      public function getDateFormat() {
          return $this->dateFormat;
      }
      
      public function setDateFormat($format) {
          $this->dateFormat = $format;
      }
  
      public function getTimeFormat() {
          return $this->timeFormat;
      }
  
      private function loadSettings() {
          if (Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_IS_DEFAULT) == Gpf::YES) {
              try {
                  $this->loadSettingsFromLanguage();
                  return;
              } catch (Gpf_Exception $e) {
              }
          }
          $this->thousandsSeparator = Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_THOUSANDS_SEPARATOR);
          $this->decimalSeparator = Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DECIMAL_SEPARATOR);
          $this->dateFormat = Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_DATE_FORMAT);
          $this->timeFormat = Gpf_Settings::get(Gpf_Settings_Gpf::REGIONAL_SETTINGS_TIME_FORMAT);
      }
  
      /**
       * @throws Gpf_Exception
       */
      private function loadSettingsFromLanguage() {
          $lang = Gpf_Lang_Dictionary::getInstance()->getLanguage();
          if ($lang == null) {
              throw new Gpf_Exception('No language loaded');
          }
          $this->thousandsSeparator = $lang->getThousandsSeparator();
          $this->decimalSeparator = $lang->getDecimalSeparator();
          $this->dateFormat = $lang->getDateFormat();
          $this->timeFormat = $lang->getTimeFormat();
      }
  
  }

} //end Gpf_Settings_Regional

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

if (!interface_exists('Gpf_FormHandler', false)) {
  interface Gpf_FormHandler {
      public function load(Gpf_Rpc_Params $params);
      
      public function add(Gpf_Rpc_Params $params);
      
      public function save(Gpf_Rpc_Params $params);
  }

} //end Gpf_FormHandler

if (!class_exists('Gpf_View_FormService', false)) {
  abstract class Gpf_View_FormService extends Gpf_Object implements Gpf_FormHandler {
      const ADD = "add";
      const EDIT = "edit";
      const ID = "Id";
  
      /**
       * @return Gpf_DbEngine_RowBase
       */
      protected abstract function createDbRowObject();
  
      /**
       * @return string
       */
      protected function getDbRowObjectName() {
          return $this->_('Row');
      }
       
      /**
       * @param Gpf_DbEngine_RowBase $dbRow
       */
      protected function setDefaultDbRowObjectValues(Gpf_DbEngine_RowBase $dbRow) {
      }
  
      protected function getId(Gpf_Rpc_Form $form) {
          return $form->getFieldValue(self::ID);
      }
  
  
      protected function loadRow(Gpf_DbEngine_RowBase $row) {
          try {
              $row->load();
          } catch (Gpf_DbEngine_NoRow $e) {
              throw new Gpf_Exception($this->_("%s does not exist", $this->getDbRowObjectName()));
          }
      }
  
      protected function loadForm(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $dbRow) {
          $form->load($dbRow);
      }
  
  
      /**
       *
       * @service
       * @param $fields
       * @return Gpf_Rpc_Form
       */
      public function load(Gpf_Rpc_Params $params) {
          $form = new Gpf_Rpc_Form($params);
  
          $dbRow = $this->createDbRowObject();
          $dbRow->setPrimaryKeyValue($this->getId($form));
          $this->loadRow($dbRow);
          $this->loadForm($form, $dbRow);
          return $form;
      }
  
      protected function updateRow(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $row) {
          //TODO: here should be update() instead of save()
          $row->save();
      }
  
      protected function fillSave(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $dbRow) {
          $form->fill($dbRow);
      }
  
      protected function fillAdd(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $dbRow) {
          $form->fill($dbRow);
      }
  
      /**
       *
       * @service
       * @param $fields
       * @return Gpf_Rpc_Form
       */
      public function save(Gpf_Rpc_Params $params) {
          $form = new Gpf_Rpc_Form($params);
  
          $dbRow = $this->createDbRowObject();
          $dbRow->setPrimaryKeyValue($this->getId($form));
  
          try {
              $this->loadRow($dbRow);
          } catch (Exception  $e) {
              $form->setErrorMessage($e->getMessage());
              return $form;
          }
  
          $this->fillSave($form, $dbRow);
          $dbRow->setPrimaryKeyValue($this->getId($form));
  
          if(!$this->checkBeforeSave($dbRow, $form, self::EDIT)) {
              return $form;
          }
          try {
              $this->updateRow($form, $dbRow);
              $this->afterSave($dbRow, self::EDIT);
          } catch (Gpf_DbEngine_Row_CheckException $checkException) {
              foreach ($checkException as $contstraintException) {
                  if ($form->existsField($contstraintException->getFieldCode())) {
                      $form->setFieldError($contstraintException->getFieldCode(), $contstraintException->getMessage());
                  }
              }
              $form->setErrorMessage($checkException->getMessage());
              return $form;
          } catch (Exception $e) {
              $form->setErrorMessage($e->getMessage());
              return $form;
          }
  
          $this->loadForm($form, $dbRow);
          $form->setInfoMessage($this->_("%s saved", $this->getDbRowObjectName()));
          return $form;
      }
  
      /**
       * Checks fields before saving
       *
       * @override
       * @return true/false
       */
      protected function checkBeforeSave(Gpf_DbEngine_RowBase $row, Gpf_Rpc_Form $form, $operationType = self::EDIT) {
          return true;
      }
  
  
      protected function addRow(Gpf_Rpc_Form $form, Gpf_DbEngine_RowBase $row) {
          $row->insert();
      }
  
      /**
       *
       * @service
       * @param $fields
       * @return Gpf_Rpc_Form
       */
      public function add(Gpf_Rpc_Params $params) {
          $form = new Gpf_Rpc_Form($params);
  
          $dbRow = $this->createDbRowObject();
          $this->setDefaultDbRowObjectValues($dbRow);
  
          $this->fillAdd($form, $dbRow);
  
          //TODO: remove following lines and all dependencies
          if(!$this->checkBeforeSave($dbRow, $form, self::ADD)) {
              return $form;
          }
  
          try {
              $this->addRow($form, $dbRow);
              //TODO: remove following line and all dependencies
              $this->afterSave($dbRow, self::ADD);
          } catch (Exception $e) {
              $form->setErrorMessage($e->getMessage());
              return $form;
          }
  
          $form->load($dbRow);
          $form->setField("Id", $dbRow->getPrimaryKeyValue());
          $form->setInfoMessage($this->_("%s was successfuly added", $this->getDbRowObjectName()));
          $form->setSuccessful();
          return $form;
      }
  
      /**
       * called after the object is saved
       *
       * @param unknown_type $dbRow
       */
      protected function afterSave($dbRow, $saveType) {
      }
  
      /**
       * @service
       *
       * @param $fields
       * @return Gpf_Rpc_Action
       */
      public function saveFields(Gpf_Rpc_Params $params) {
          $action = new Gpf_Rpc_Action($params);
          $action->setErrorMessage($this->_('Failed to save %s field(s) in %s(s)', '%s', $this->getDbRowObjectName()));
          $action->setInfoMessage($this->_('%s field(s) in %s(s) successfully saved', '%s', $this->getDbRowObjectName()));
  
          $fields = new Gpf_Data_RecordSet();
          $fields->loadFromArray($action->getParam("fields"));
  
  
          foreach ($fields as $field) {
              $dbRow = $this->createDbRowObject();
              $dbRow->setPrimaryKeyValue($field->get('id'));
              $dbRow->load();
              $dbRow->set($field->get("name"), $field->get("value"));
              $dbRow->save();
              $action->addOk();
          }
  
          return $action;
      }
  
      /**
       * @service
       * @param $ids
       * @return Gpf_Rpc_Action
       */
      public function deleteRows(Gpf_Rpc_Params $params) {
          $action = new Gpf_Rpc_Action($params);
          $errorMessages = "";
          foreach ($action->getIds() as $id) {
              try {
                  $row = $this->createDbRowObject();
                  $row->setPrimaryKeyValue($id);
                  $this->deleteRow($row);
                  $action->addOk();
              } catch (Exception $e) {
                  $action->addError();
                  $errorMessages .= '<br/>' . $e->getMessage();
              }
          }
           
          $action->setErrorMessage($this->_('Failed to delete %s %s(s)', '%s', $this->getDbRowObjectName()) .
                                      '<br/>' .
          $this->_('Error details: %s', $errorMessages));
          $action->setInfoMessage($this->_('%s %s(s) successfully deleted', '%s', $this->getDbRowObjectName()));
           
          return $action;
      }
      
      protected function deleteRow(Gpf_DbEngine_RowBase $row) {
          $row->delete();
      }
  }
  

} //end Gpf_View_FormService

if (!class_exists('Gpf_Country_CountryForm', false)) {
  class Gpf_Country_CountryForm extends Gpf_View_FormService {
  
      /**
       * @return Gpf_Db_Country
       */
      protected function createDbRowObject() {
          return new Gpf_Db_Country();
      }
  
      /**
       * @service country write
       *
       * @param $id
       * @return Gpf_Rpc_Action
       */
      public function setDefaultCountry(Gpf_Rpc_Params $params) {
          $action = new Gpf_Rpc_Action($params);
          $action->setErrorMessage($this->_("Error changing default country"));
          $action->setInfoMessage($this->_("Default country changed"));
  
          try {
              $countryCode = $action->getParam('id');
              Gpf_Settings::set(Gpf_Settings_Gpf::DEFAULT_COUNTRY, $countryCode);
              $country = new Gpf_Db_Country();
              $country->setCountryCode($countryCode);
              $country->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
              $country->loadFromData(array(Gpf_Db_Table_Countries::COUNTRY_CODE, Gpf_Db_Table_Countries::ACCOUNTID));
              if ($country->getStatus() != Gpf_Db_Country::STATUS_ENABLED) {
                  $country->setStatus(Gpf_Db_Country::STATUS_ENABLED);
                  $country->save();
              }
              $action->addOk();
          } catch (Exception $e) {
              $action->addError();
          }
  
          return $action;
      }
  
      /**
       * @service country read
       * @anonym
       * @return Gpf_Rpc_Data
       */
      public function loadDefaultCountry(Gpf_Rpc_Params $params) {
          $data = new Gpf_Rpc_Data($params);
  
          $context = new Gpf_Plugins_ValueContext(Gpf_Settings::get(Gpf_Settings_Gpf::DEFAULT_COUNTRY));
          Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Countries.getDefaultCountry', $context);
  
          $data->setValue('default', $context->get());
          return $data;
      }
  
      /**
       * @service country write
       *
       * @param $fields
       * @return Gpf_Rpc_Action
       */
      public function saveFields(Gpf_Rpc_Params $params) {
      	return parent::saveFields($params);
      }
  
      /**
       * @service country read
       * @anonym
       * @return Gpf_Data_RecordSet
       */
      public function loadCountries() {
      	$select = new Gpf_SqlBuilder_SelectBuilder();
      	$select->select->add(Gpf_Db_Table_Countries::COUNTRY_CODE, 'id');
          $select->select->add(Gpf_Db_Table_Countries::COUNTRY, 'name');
      	$select->from->add(Gpf_Db_Table_Countries::getName());
      	$select->where->add(Gpf_Db_Table_Countries::ACCOUNTID, '=', Gpf_Application::getInstance()->getAccountId());
          $select->where->add(Gpf_Db_Table_Countries::STATUS, '=', Gpf_Db_Country::STATUS_ENABLED);
          $select->orderBy->add(Gpf_Db_Table_Countries::COUNTRY);
  
          return $select->getAllRows();
      }
  }

} //end Gpf_Country_CountryForm

if (!class_exists('Gpf_Rpc_Data', false)) {
  class Gpf_Rpc_Data extends Gpf_Object implements Gpf_Rpc_Serializable {
  	const NAME  = "name";
      const VALUE = "value";
      const DATA = "data";
      const ID = "id";
      
  	/**
  	 * @var Gpf_Data_IndexedRecordSet
  	 */
      private $params;
      
      /**
       * @var string
       */
      private $id;
      
      
      /**
       * @var Gpf_Rpc_FilterCollection
       */
      private $filters;
      
      /**
       * @var Gpf_Data_IndexedRecordSet
       */
      private $response;
      
      /**
       *
       * @return Gpf_Data_IndexedRecordSet
       */
      public function getParams() {
          return $this->params;
      }
      
      /**
       * Create instance to handle DataRequest
       *
       * @param Gpf_Rpc_Params $params
       */
      public function __construct(Gpf_Rpc_Params $params = null) {
      	if($params === null) {
      	    $params = new Gpf_Rpc_Params();
      	}
          
      	$this->filters = new Gpf_Rpc_FilterCollection($params);
          
      	$this->params = new Gpf_Data_IndexedRecordSet(self::NAME);
      	$this->params->setHeader(array(self::NAME, self::VALUE));
          
          if ($params->exists(self::DATA) !== null) {
              $this->loadParamsFromArray($params->get(self::DATA));
          }
          
          $this->id = $params->get(self::ID);
          
          $this->response = new Gpf_Data_IndexedRecordSet(self::NAME);
          $this->response->setHeader(array(self::NAME, self::VALUE));
      }
      
     /**
       * Return id
       *
       * @return string
       */
      public function getId() {
          return $this->id;
      }
      
      /**
       * Return parameter value
       *
       * @param String $name
       * @return unknown
       */
      public function getParam($name) {
          try {
             return $this->params->getRecord($name)->get(self::VALUE);
          } catch (Gpf_Data_RecordSetNoRowException $e) {
             return null;
          }
      }
      
      public function setParam($name, $value) {
          self::setValueToRecordset($this->params, $name, $value);
      }
      
      public function loadFromObject(array $object) {
          $this->response->loadFromObject($object);
          $this->params->loadFromObject($object);
      }
          
      /**
       * @return Gpf_Rpc_FilterCollection
       */
      public function getFilters() {
      	return $this->filters;
      }
  
      private static function setValueToRecordset(Gpf_Data_IndexedRecordSet $recordset, $name, $value) {
          try {
             $record = $recordset->getRecord($name);
          } catch (Gpf_Data_RecordSetNoRowException $e) {
             $record = $recordset->createRecord();
             $record->set(self::NAME, $name);
             $recordset->addRecord($record);
          }
          $record->set(self::VALUE, $value);
      }
      
      public function setValue($name, $value) {
          self::setValueToRecordset($this->response, $name, $value);
      }
      
      public function getSize() {
          return $this->response->getSize();
      }
      
      public function getValue($name) {
          try {
              return $this->response->getRecord($name)->get(self::VALUE);
          } catch (Gpf_Data_RecordSetNoRowException $e) {
          }
          return null;
      }
      
      public function toObject() {
      	return $this->response->toObject();
      }
  
      public function toText() {
      	return $this->response->toText();
      }
  
      private function loadParamsFromArray($data) {
          for ($i = 1; $i < count($data); $i++) {
              $this->params->add($data[$i]);
          }
      }
  }

} //end Gpf_Rpc_Data

if (!class_exists('Gpf_Rpc_FilterCollection', false)) {
  class Gpf_Rpc_FilterCollection extends Gpf_Object implements IteratorAggregate {
  
      /**
       * @var array of Gpf_SqlBuilder_Filter
       */
      private $filters;
  
      public function __construct(Gpf_Rpc_Params $params = null) {
          $this->filters = array();
          if ($params != null) {
              $this->init($params);
          }
      }
      
      public function add(array $filterArray) {
      	$this->filters[] = new Gpf_SqlBuilder_Filter($filterArray);
      }
  
      private function init(Gpf_Rpc_Params $params) {
          $filtersArray = $params->get("filters");
          if (!is_array($filtersArray)) {
              return;
          }
          foreach ($filtersArray as $filterArray) {
              $this->add($filterArray);
          }
      }
  
      /**
       *
       * @return ArrayIterator
       */
      public function getIterator() {
          return new ArrayIterator($this->filters);
      }
  
      public function addTo(Gpf_SqlBuilder_WhereClause $whereClause) {
          foreach ($this->filters as $filter) {
              $filter->addTo($whereClause);
          }
      }
  
      /**
       * Returns first filter with specified code.
       * If filter with specified code does not exists null is returned.
       *
       * @param string $code
       * @return array<Gpf_SqlBuilder_Filter>
       */
      public function getFilter($code) {
      	$filters = array();
          foreach ($this->filters as $filter) {
              if ($filter->getCode() == $code) {
                  $filters[] = $filter;
              }
          }
          return $filters;
      }
      
      public function isFilter($code) {
          foreach ($this->filters as $filter) {
              if ($filter->getCode() == $code) {
                  return true;
              }
          }
          return false;
      }
      
      public function getFilterValue($code) {
          $filters = $this->getFilter($code);
          if (count($filters) == 1) {
              return $filters[0]->getValue();
          }
          return "";
      }
  
      public function matches(Gpf_Data_Record $row) {
          foreach ($this->filters as $filter) {
              if (!$filter->matches($row)) {
                  return false;
              }
          }
          return true;
      }
  
      public function getSize() {
          return count($this->filters);
      }
  }

} //end Gpf_Rpc_FilterCollection

if (!class_exists('Gpf_Plugins_ValueContext', false)) {
  class Gpf_Plugins_ValueContext  {
  
      private $value;
      /**
       * @var array
       */
      private $array;
  
      public function __construct($value) {
          $this->value = $value;
      }
  
      public function get() {
          return $this->value;
      }
  
      public function set($value) {
          $this->value = $value;
      }
  
      public function getArray() {
          return $this->array;
      }
  
      public function setArray(array $array) {
          $this->array = $array;
      }
  }

} //end Gpf_Plugins_ValueContext

if (!class_exists('Pap_Module', false)) {
  class Pap_Module extends Gpf_Object {
      
      public static function assignTemplateVariables(Gpf_Templates_Template $template) {
          if (Gpf_Session::getAuthUser()->isLogged()) {
              $template->assign('isLogged', '1');
          } else {
              $template->assign('isLogged', '0');
          }
          $template->assign('papCopyrightText', Gpf_Settings::get(Pap_Settings::BRANDING_TEXT));
      	$template->assign('papVersionText', 'version ' . Gpf_Application::getInstance()->getVersion());
      	$template->assign('postAffiliatePro', Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO));
      	$template->assign('qualityUnitPostaffiliateproLink', Gpf_Settings::get(Pap_Settings::BRANDING_QUALITYUNIT_POSTAFFILIATEPRO_LINK));
      	$template->assign('postAffiliateProHelp', Gpf_Settings::get(Pap_Settings::BRANDING_POST_AFFILIATE_PRO_HELP_LINK));
      	$template->assign('qualityUnitChangeLog', Gpf_Settings::get(Pap_Settings::BRANDING_QUALITYUNIT_CHANGELOG_LINK));
      	$template->assign('knowledgebaseUrl', Gpf_Settings::get(Pap_Settings::BRANDING_KNOWLEDGEBASE_LINK));
      	$template->assign('PAP', Gpf_Settings::get(Pap_Settings::BRANDING_QUALITYUNIT_PAP));
      	$template->assign('tutorialVideosBaseLink', Gpf_Settings::get(Pap_Settings::BRANDING_TUTORIAL_VIDEOS_BASE_LINK));
      }
      
      public static function setSessionInfo(Gpf_Rpc_Data $sessionInfo) {
          $sessionInfo->setValue('extra1', Pap_Db_Table_Users::getAffiliateCount());
      }
      
      /**
       *
       * @return array
       */
      public static function getStyleSheets() {
          return array('pap4.css', 'custom.css');
      }
  }

} //end Pap_Module

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

if (!class_exists('Gpf_ResourceNotFoundException', false)) {
  class Gpf_ResourceNotFoundException extends Gpf_Exception {
      public function __construct($resouceName, $panelName) { 
          parent::__construct("Resource '$resouceName' not found in panel '$panelName'");
      }
  }

} //end Gpf_ResourceNotFoundException

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

if (!class_exists('Gpf_Contexts_Module', false)) {
  class Gpf_Contexts_Module extends Gpf_Plugins_Context {
  
      /**
       * @var array of resource urls
       */
      private $jsResources = array();
  
      /**
       * @var array of js scripts
       */
      private $jsScripts = array();
  
      /**
       * @var Gpf_Contexts_Module
       */
      private static $instance = null;
  
      protected function __construct() {
      }
  
      /**
       * @return Gpf_Contexts_Module
       */
      public function getContextInstance() {
          if (self::$instance == null) {
              self::$instance = new Gpf_Contexts_Module();
          }
          return self::$instance;
      }
  
      /**
       * Add javascript resource url
       *
       * @param string $resource
       */
      public function addJsResource($resource, $id = null) {
          $this->jsResources[$resource] = array('resource' => $resource, 'id' => $id);
      }
  
      /**
       * Get javascript resources array
       *
       * @return array
       */
      public function getJsResources() {
          return $this->jsResources;
      }
  
      /**
       * Get javascripts array
       *
       * @return array
       */
      public function getJsScripts() {
          return $this->jsScripts;
      }
  
      /**
       * Add javascript code
       *
       * @param string $sourceCode javascript source code, which should be added to main page header
       */
      public function addJsScript($sourceCode) {
          $this->jsScripts[] = $sourceCode;
      }
  
  }

} //end Gpf_Contexts_Module

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
/*
VERSION
1478697f6ec3b3bb317b019d9ea668ba
*/
?>
