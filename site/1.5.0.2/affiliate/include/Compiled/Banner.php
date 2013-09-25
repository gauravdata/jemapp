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

if (!class_exists('Pap_Tracking_BannerViewer', false)) {
  class Pap_Tracking_BannerViewer extends Gpf_Object {
  
      const EXT_POINT_NAME = 'PostAffiliate.BannerViewer.show';
  
      public function show(Pap_Db_CachedBanner $cachedBanner) {
          try {
              $request = new Pap_Tracking_Request();
              $banner = $this->getBanner($cachedBanner->getBannerId(), $cachedBanner->getUserId(), $cachedBanner->getChannel());
              $req = new Pap_Tracking_BannerViewerRequest($banner->getBannerType());
              Gpf_Plugins_Engine::extensionPoint(self::EXT_POINT_NAME, $req);
              if($req->getViewer() != null) {
                  $req->getViewer()->showBanner($request , $banner);
                  return;
              }
              $this->prepareCachedBanner($banner, $cachedBanner);
              try {
                  $cachedBanner->save();
              } catch (Gpf_DbEngine_Row_ConstraintException $e) {
                  // cached banner was saved already by other script
              }
              if ($cachedBanner->getHeaders() != '') {
                  header($cachedBanner->getHeaders(), true);
              }
              echo $cachedBanner->getCode();
          } catch (Exception $e) {
              $this->logMessage($e);
              echo $e;
          }
      }
  
      private function prepareCachedBanner(Pap_Common_Banner $banner, Pap_Db_CachedBanner $cachedBanner) {
          if ($banner->getWrapperId() !== null && $cachedBanner->getWrapper() !== '') {
              Pap_Merchants_Config_BannerWrapperService::fillCachedBanner($banner, $cachedBanner);
              return;
          }
          if ($banner->getBannerType() == Pap_Features_HoverBanner_Hover::TYPE_HOVER) {
              $cachedBanner->setCode($banner->getCode(Pap_Affiliates_User::loadFromId($cachedBanner->getUserId()), Pap_Common_Banner::FLAG_RAW_CODE));
          } else {
              $banner->fillCachedBanner($cachedBanner, Pap_Affiliates_User::loadFromId($cachedBanner->getUserId()));
          }
          self::addJavascriptCode($cachedBanner);
      }
      
      public static function addJavascriptCode(Pap_Db_CachedBanner $cachedBanner) {
          $code = $cachedBanner->getCode();
          $code = str_replace("\n", " ", $code);
          $code = str_replace("\r", " ", $code);
          $code = str_replace("'", "\\'", $code);
          $cachedBanner->setCode("document.write('$code')");
          $cachedBanner->setHeaders('Content-Type: application/x-javascript');
      }
  
      /**
       * @service banner read
       * @param $fields
       */
      public function getBannerLink(Gpf_Rpc_Params $params){
          $form = new Gpf_Rpc_Form($params);
          $form->addField("link", self::getBannerScriptUrl($params->get('affiliateId'),$params->get('bannerId')));
          return $form;
      }
  
      public static function getBannerScriptUrl($userId, $bannerId, $channelId = null, $parentBannerId = null){
          $url =  Gpf_Paths::getInstance()->getFullScriptsUrl().'banner.php'.
                      '?';
          $url .= Gpf_Settings::get(Pap_Settings::PARAM_NAME_USER_ID).'='.$userId;
          $url .= '&'.Gpf_Settings::get(Pap_Settings::PARAM_NAME_BANNER_ID).'='.$bannerId;
          if($channelId != null){
              $url .= '&'.Pap_Tracking_Request::getChannelParamName().'='.$channelId;
          }
          if($parentBannerId != null) {
              $url .= '&'.Pap_Tracking_Request::getRotatorBannerParamName().'='.$parentBannerId;
          }
          return $url;
      }
  
      private function logMessage(Exception $e) {
          Gpf_Log::warning('Trying to show non-existing banner: '. $e->getMessage());
      }
  
      /**
       * @return Pap_Affiliates_User
       * @throws Gpf_Exception
       */
      private function getUser($userid) {
          return Pap_Affiliates_User::loadFromId($userid);
      }
  
      /**
       * @return Pap_Common_Banner
       */
      private function getBanner($bannerId, $userId = null, $channelId = '') {
          $bannerFactory = new Pap_Common_Banner_Factory();
          $banner = $bannerFactory->getBanner($bannerId);
          if(isset($_REQUEST[Pap_Db_Table_CachedBanners::DYNAMIC_LINK])) {
              $banner->setDynamicLink($_REQUEST[Pap_Db_Table_CachedBanners::DYNAMIC_LINK]);
          }
          if ($channelId == '' || $userId == null || $userId == '') {
              return $banner;
          }
          try {
              $banner->setChannel($this->getChannel($userId, $channelId));
          } catch (Gpf_Exception $e) {
              Gpf_Log::info('Invalid channel '.$channelId.' used in banner '.$bannerId.' for user '.$userId.': '. $e->getMessage());
          }
          return $banner;
      }
      
      /**
       * @param $userId (can be userid or refid)
       * @param $channelId
       * @throws Gpf_DbEngine_NoRowException
       * @throws Gpf_DbEngine_TooManyRowsException
       * @return Pap_Db_Channel
       */
      protected function getChannel($userId, $channelId) {
      	$channel = $this->createChannel();
          $channel->setPapUserId($this->getAffiliate($userId)->getId());
          $channel->setValue($channelId);
          $channel->loadFromData(array(Pap_Db_Table_Channels::USER_ID, Pap_Db_Table_Channels::VALUE));
          return $channel;
      }
      
      /**
       * @throws Gpf_Exception
       * @return Pap_Affiliates_User
       */
      protected function getAffiliate($userId) {
      	return Pap_Affiliates_User::loadFromId($userId);
      }
      
      /**
       * @return Pap_Db_Channel
       */
      protected function createChannel() {
      	return new Pap_Db_Channel();
      }
      
      
      /**
       * @deprecated should be moved to hover banner
       */
      public function showHover() {
          $request = new Pap_Tracking_Request();
          try {
              $banner = $this->getBanner($request->getBannerId(), $request->getAffiliateId(), $request->getChannelId());
              if ($banner->getBannerType() == Pap_Features_HoverBanner_Hover::TYPE_HOVER) {
                  return $banner->getDisplayCode($request->getUser());
              }
          } catch (Exception $e) {
              $this->logMessage($e);
          }
      }
  
      /**
       * @deprecated should be moved to hover banner
       */
      public function previewHover() {
          $request = new Pap_Tracking_Request();
          try {
              $banner = $this->getBanner($request->getBannerId(), $request->getAffiliateId(), $request->getChannelId());
              if ($banner->getBannerType() == Pap_Features_HoverBanner_Hover::TYPE_HOVER) {
                  return $banner->getPreviewCode($request->getUser());
              }
          } catch (Exception $e) {
              $this->logMessage($e);
          }
      }
  }
  

} //end Pap_Tracking_BannerViewer

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

if (!class_exists('Pap_Common_Banner_BannerRequest', false)) {
  class Pap_Common_Banner_BannerRequest {
      /**
       * @var string
       */
      var $bannerType;
      /**
       * @var string
       */
      var $banner;
       
      /**
       * @param  string
       */
      function __construct($bannerType){
          $this->bannerType = $bannerType;
      }
  
      /**
       * @return Pap_Common_Banner
       */
      function getBanner(){
          return $this->banner;
      }
  
  
      function setBanner(Pap_Common_Banner $banner){
          return $this->banner = $banner;
      }
  
      /**
       * @return string
       */
      function getType(){
          return $this->bannerType;
      }
  }
  

} //end Pap_Common_Banner_BannerRequest

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

if (!class_exists('Pap_Features_BannerRotator_Config', false)) {
  class Pap_Features_BannerRotator_Config extends Gpf_Plugins_Handler {
  
      const BannerTypeRotator = 'R';
  
      public static function getHandlerInstance() {
          return new Pap_Features_BannerRotator_Config();
      }
  
      public function getBanner(Pap_Common_Banner_BannerRequest $bannerRequest) {
          if($bannerRequest->getType()==self::BannerTypeRotator){
              $bannerRequest->setBanner(new Pap_Features_BannerRotator_Rotator());
          }
      }
  }
  

} //end Pap_Features_BannerRotator_Config

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

if (!class_exists('Pap_Features_BannerRotator_Rotator', false)) {
  class Pap_Features_BannerRotator_Rotator extends Pap_Common_Banner {
      const DATETIME_FORMAT = 'Y-m-d H:i:s';
      /**
       * @var Pap_Common_Banner_Factory
       */
      var $bannerFactory;
  
      var $maxRank;
      var $rotatedBanners = null;
  
      function __construct() {
          parent::__construct();
          $this->bannerFactory = new Pap_Common_Banner_Factory();
      }
  
      public function getPreview(Pap_Common_User $user) {
          $this->parseRotatorBannerDescription();
          if($this->rotatedBanners!=null){
              if(Gpf_Session::getAuthUser()->isAffiliate()) {
                  return $this->getAffiliatePreview($user);
              }
              return $this->getMerchantPreview($user);
          }
          return '';
      }
  
      function getMerchantPreview(Pap_Common_User $user){
          $template = new Gpf_Templates_Template("rotator_preview_merchant.stpl");
          $this->addBannersForPreview($template,$user);
          return $template->getHTML();
      }
  
      function addBannersForPreview(Gpf_Templates_Template $template ,Pap_Common_User $user){
          $bannersPreview = '';
          $counter = 0;
          foreach ($this->rotatedBanners as $row) {
              $bannerId = $row->get(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID);
              $rank = $row->get(Pap_Db_Table_BannersInRotators::RANK);
              try {
                  $bannersPreview .= '<div>'. $this->bannerFactory->getBanner($bannerId)->getPreview($user).'</div><hr>';
                  $counter ++;
              } catch (Gpf_Exception $e) {
              }
          }
          if ($counter >= 5) {
              $bannersPreview = '<div class="BannerRotatorPreview">' . $bannersPreview . '</div>';
          }
          $template->assign("banners",$bannersPreview);
      }
  
      function getAffiliatePreview(Pap_Common_User $user){
          $template = new Gpf_Templates_Template("rotator_preview_affiliate.stpl");
          $this->addBannersForPreview($template,$user);
          $template->assign("id",$this->getId());
          return $template->getHTML();
      }
  
      public function initValidators(Gpf_Rpc_Form $form) {
          $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::NAME, $this->_('name'));
          $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::STATUS, $this->_('status'));
          $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::SIZE, $this->_('size'));
      }
  
      protected function getBannerCode(Pap_Common_User $user, $flags) {
          return '<script type="text/javascript" src="'.$this->getBannerScriptUrl($user).'"></script>';
      }
  
      public function getDisplayCode(Pap_Common_User $user) {
          $this->parseRotatorBannerDescription();
          $banner = $this->bannerFactory->getBanner($this->getBannerIdToShow());
          $banner->setParentBannerId($this->getId());
          return $banner->getCode($user);
      }
  
      public function fillCachedBanner(Pap_Db_CachedBanner $cachedBanner, Pap_Common_User $user) {
          $this->parseRotatorBannerDescription();
          $bannerIdToShow = $this->getBannerIdToShow();
          foreach ($this->rotatedBanners as $row) {
              $bannerId = $row->get(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID);
              $rank = $row->get(Pap_Db_Table_BannersInRotators::RANK);
              $banner = $this->bannerFactory->getBanner($bannerId);
              $banner->setParentBannerId($this->getId());
              $this->setBannerChannel($banner, $cachedBanner->getChannel());
              if ($bannerIdToShow == $bannerId) {
                  $cachedBanner->setCode($banner->getCompleteCode($user, ''));
                  $cachedBanner->setRank($rank);
                  $cachedBanner->setValidFrom($row->get(Pap_Db_Table_BannersInRotators::VALID_FROM));
                  $cachedBanner->setValidUntil($row->get(Pap_Db_Table_BannersInRotators::VALID_UNTIL));
                  continue;
              }
              $rotCachedBanner = clone $cachedBanner;
              $rotCachedBanner->setValidFrom($row->get(Pap_Db_Table_BannersInRotators::VALID_FROM));
              $rotCachedBanner->setValidUntil($row->get(Pap_Db_Table_BannersInRotators::VALID_UNTIL));
              $rotCachedBanner->setCode($banner->getCompleteCode($user, ''));
              Pap_Tracking_BannerViewer::addJavascriptCode($rotCachedBanner);
              $rotCachedBanner->setRank($rank);
              try {
                  $rotCachedBanner->save();
              } catch (Gpf_DbEngine_Row_ConstraintException $e) {
                  // cached banner was saved already by other script
              }
          }
      }
  
      private function setBannerChannel(Pap_Common_Banner $banner, $channel) {
          $dbChannel = new Pap_Db_Channel();
          $dbChannel->set(Pap_Db_Table_Channels::VALUE, $channel);
          try {
              $dbChannel->loadFromData(array(Pap_Db_Table_Channels::VALUE));
              $banner->setChannel($dbChannel);
          } catch (Exception $e) {
          }
      }
  
      /**
       * Returns banner which should be displayed
       *
       * @return Pap_Common_Banner
       */
      private function getBannerIdToShow() {
          $sum = 0;
          $id = 0;
          list($usec, $sec) = explode(' ', microtime());
          srand((float) $sec + ((float) $usec * 100000));
          $rnd = rand(0, $this->maxRank);
          foreach ($this->rotatedBanners as $row) {
              $valid_from = $row->get(Pap_Db_Table_BannersInRotators::VALID_FROM);
              if($valid_from < date("Y-m-d H:i:s") || $valid_from == null){
                  $bannerId = $row->get(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID);
                  $rank = $row->get(Pap_Db_Table_BannersInRotators::RANK);
                  $sum += $rank;
                  if ($rnd <= $sum) {
                      return $bannerId;
                  }
                  $id = $bannerId;
              }
          }
          return $id;
      }
  
      function parseRotatorBannerDescription() {
          $row = new Pap_Db_BannerInRotator();
          $row->setParentBannerId($this->getId());
          $select = new Gpf_SqlBuilder_SelectBuilder();
  
          $select->select->addAll(Pap_Db_Table_BannersInRotators::getInstance());
          $select->from->add(Pap_Db_Table_BannersInRotators::getName());
          $select->where->add(Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID,'=',$this->getId());
  
          $dateCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
          $dateCondition->add(Pap_Db_Table_BannersInRotators::VALID_UNTIL,'=',null,'OR',false);
          $dateCondition->add(Pap_Db_Table_BannersInRotators::VALID_UNTIL,'>',date(self::DATETIME_FORMAT),'OR');
  
          $select->where->addCondition($dateCondition,'AND');
          foreach ($select->getAllRows() as $row){
              $this->rotatedBanners[] = $row;
              $this->maxRank+=$row->get(Pap_Db_Table_BannersInRotators::RANK);
          }
      }
  
      function createUpdateIncrementBuild(Pap_Common_Banner $childBanner, $column){
          $updateBuild = new Gpf_SqlBuilder_UpdateBuilder();
          $updateBuild->from->add(Pap_Db_Table_BannersInRotators::getName());
          $updateBuild->set->add($column, "$column+1", false);
          $updateBuild->where->add(Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID,'=',$this->getId());
          $updateBuild->where->add(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID,'=',$childBanner->getId());
  
          $c1 = new Gpf_SqlBuilder_CompoundWhereCondition();
          $c1->add(Pap_Db_Table_BannersInRotators::VALID_FROM,'=',null);
          $c1->add(Pap_Db_Table_BannersInRotators::VALID_UNTIL,'=',null);
  
          $c2 = new Gpf_SqlBuilder_CompoundWhereCondition();
          $c2->add(Pap_Db_Table_BannersInRotators::VALID_FROM,'<',date("Y-m-d H:i:s"));
          $c2->add(Pap_Db_Table_BannersInRotators::VALID_UNTIL,'>',date("Y-m-d H:i:s"));
  
          $c3 = new Gpf_SqlBuilder_CompoundWhereCondition();
          $c3->add(Pap_Db_Table_BannersInRotators::VALID_FROM,'=',null);
          $c3->add(Pap_Db_Table_BannersInRotators::VALID_UNTIL,'>',date("Y-m-d H:i:s"));
  
          $c4 = new Gpf_SqlBuilder_CompoundWhereCondition();
          $c4->add(Pap_Db_Table_BannersInRotators::VALID_FROM,'<',date("Y-m-d H:i:s"));
          $c4->add(Pap_Db_Table_BannersInRotators::VALID_UNTIL,'=',null);
  
          $c = new Gpf_SqlBuilder_CompoundWhereCondition();
          $c->addCondition($c1,'OR');
          $c->addCondition($c2,'OR');
          $c->addCondition($c3,'OR');
          $c->addCondition($c4,'OR');
  
          $updateBuild->where->addCondition($c,'AND');
  
          return $updateBuild;
      }
  
      public function saveChildImpression(Pap_Common_Banner $banner, $isUnique){
          $updateBuild = $this->createUpdateIncrementBuild($banner, Pap_Db_Table_BannersInRotators::ALL_IMPS);
          if($isUnique){
              $column = Pap_Db_Table_BannersInRotators::UNIQ_IMPS;
              $updateBuild->set->add($column, "$column+1", false);
          }
          $updateBuild->execute();
      }
  
      public function saveChildClick(Pap_Common_Banner $banner){
          $updateBuild = $this->createUpdateIncrementBuild($banner,  Pap_Db_Table_BannersInRotators::CLICKS);
          $updateBuild->execute();
      }
  }
  

} //end Pap_Features_BannerRotator_Rotator

if (!class_exists('Pap_Tracking_BannerViewerRequest', false)) {
  class Pap_Tracking_BannerViewerRequest extends Gpf_Object {
  	/**
  	 * @var Pap_Common_Banner_Viewer
  	 */
  	private $viewer;
  
  	private $bannerType;
  	
  	function __construct($bannerType){
  		$this->bannerType = $bannerType;
  	}
  
  	function getType(){
  		return $this->bannerType;
  	}
  	
  	function setViewer(Pap_Common_Banner_Viewer $viewer){
  		$this->viewer = $viewer;
  	}
  
  	/**
  	 * @return Pap_Common_Banner_Viewer
  	 */
  	function getViewer(){
  		return $this->viewer;
  	}
  }
  

} //end Pap_Tracking_BannerViewerRequest

if (!class_exists('Pap_Merchants_Config_BannerWrapperService', false)) {
  class Pap_Merchants_Config_BannerWrapperService extends Gpf_Object {
  
      const CONST_HTML = 'html';
      const CONST_WIDTH = 'width';
      const CONST_HEIGHT = 'height';
      const CONST_NAME = 'bannername';
      const CONST_BANNERID = 'bannerid';
      const CONST_HTMLCOMPL = 'htmlcompleteurl';
      const CONST_HTMLCOMPL_ENCODED = 'htmlcompleteurlEncoded';
      const CONST_HTMLCLEAN = 'htmlcleanurl';
      const CONST_HTMLJSURL = 'htmljsurl';
      const CONST_CLICKURL = 'clickurl';
      const CONST_TARGETURL = 'targeturl';
      const CONST_SEOSTRING = 'seostring';
      const URL_PARAM_WRAPPER = 'w';
      const URL_VALUE_INNERPAGE = 1;
      const URL_VALUE_CLEAN = 2;
      const INNERPAGE_BEGIN = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><body style="margin:0; padding:0;">';
      const INNERPAGE_END = '</body></html>';
  
      /**
       * Load wrapper for edit
       * @service banner_format_setting read
       */
      public function load(Gpf_Rpc_Params $params) {
          $form = new Gpf_Rpc_Form($params);
          $row = new Pap_Db_BannerWrapper();
          $row->setId($form->getFieldValue("Id"));
          $row->load();
          $form->setField("editor", $row->getCode());
          return $form;
      }
  
      /**
       * @service banner_format_setting read
       * @return Gpf_Data_RecordSet
       */
      public function loadWrapperNames(Gpf_Rpc_Params $params) {
          $row = new Pap_Db_BannerWrapper();
          $collection = $row->loadCollection();
          $result = new Gpf_Data_RecordSet();
          $result->setHeader(array('id', 'name'));
          foreach ($collection as $row){
              $result->add(array($row->getId(), $row->getName()));
          }
          return $result;
      }
  
      /**
       * @service banner_format_setting read
       * @return Gpf_Rpc_Map
       */
      public function loadEditorConstants(Gpf_Rpc_Params $params) {
          return new Gpf_Rpc_Map(array(
          self::CONST_TARGETURL => $this->_('Target URL'),
          self::CONST_CLICKURL => $this->_('Click URL'),
          self::CONST_NAME => $this->_('Banner Name'),
          self::CONST_BANNERID => $this->_('Banner ID'),
          self::CONST_WIDTH => $this->_('Width'),
          self::CONST_HEIGHT => $this->_('Height'),
          self::CONST_HTML => $this->_('Banner Html'),
          self::CONST_HTMLCOMPL => $this->_('Url to complete page with banner code.'),
          self::CONST_HTMLCOMPL_ENCODED => $this->_('Url to complete page with banner code (URLEncoded).'),
          self::CONST_HTMLCLEAN => $this->_('Url to clean banner code.'),
          self::CONST_HTMLJSURL => $this->_('Url to javascript banner code'),
          self::CONST_SEOSTRING => $this->_('Seo string')
          ));
      }
  
      /**
       * save wrapper code
       * @service banner_format_setting write
       */
      public function save(Gpf_Rpc_Params $params) {
          $form = new Gpf_Rpc_Form($params);
          $wrapperId = $form->getFieldValue("Id");
          $wrapperCode = $form->getFieldValue("editor");
          $row = new Pap_Db_BannerWrapper();
          $row->setId($wrapperId);
          $row->load();
          $row->setCode($wrapperCode);
          $row->save();
          $form->setInfoMessage($this->_("Banner wrapper successfully saved"));
          Pap_Db_Table_CachedBanners::clearCachedBanners();
          return $form;
      }
  
      /**
       *  @service banner_format_setting write
       */
      public function deleteWrapper(Gpf_Rpc_Params $params) {
          $action = new Gpf_Rpc_Action($params);
          try {
              foreach ($action->getIds() as $id) {
                  $row = new Pap_Db_BannerWrapper();
                  $row->setId($id);
                  $row->delete();
              }
          } catch (Exception $e) {
              $action->setErrorMessage($e->getMessage());
              $action->addError();
          }
          return $action;
      }
  
      /**
       *  @service banner_format_setting write
       */
      public function addWrapper(Gpf_Rpc_Params $params) {
          $action = new Gpf_Rpc_Action($params);
          try {
              foreach ($action->getIds() as $name) {
                  $row = new Pap_Db_BannerWrapper();
                  $row->setName($name);
                  $row->insert();
              }
          } catch (Exception $e) {
              $action->setErrorMessage($e->getMessage());
              $action->addError();
          }
          return $action;
      }
  
      public function getBannerInWrapper($bannercode, Pap_Common_Banner $banner, Pap_Common_User $user){
          $wrapper = new Pap_Db_BannerWrapper();
          $wrapper->setId($banner->getWrapperId());
          $wrapper->load();
  
  
          $code = $wrapper->getCode();
          $code = $this->replaceConstant(self::CONST_WIDTH, $banner->getWidth(), $code);
          $code = $this->replaceConstant(self::CONST_HEIGHT, $banner->getHeight(), $code);
          $code = $this->replaceConstant(self::CONST_HTML, $bannercode, $code);
          $code = $this->replaceConstant(self::CONST_NAME, $banner->getName(), $code);
          $code = $this->replaceConstant(self::CONST_BANNERID, $banner->getId(), $code);
          $completeUrl = $banner->getBannerScriptUrl($user)
          . '&' . self::URL_PARAM_WRAPPER . '=' . self::URL_VALUE_INNERPAGE;
          if($banner->getDynamicLink() != '') {
              $completeUrl .= '&'. Pap_Db_Table_CachedBanners::DYNAMIC_LINK . '=' . urlencode($banner->getDynamicLink());
          }
          $code = $this->replaceConstant(self::CONST_HTMLCOMPL, $completeUrl, $code);
          $code = $this->replaceConstant(self::CONST_HTMLCOMPL_ENCODED, urlencode($completeUrl), $code);
          $code = $this->replaceConstant(self::CONST_HTMLCLEAN, $banner->getBannerScriptUrl($user)
          . '&' . self::URL_PARAM_WRAPPER . '=' . self::URL_VALUE_CLEAN, $code);
          $code = $this->replaceConstant(self::CONST_CLICKURL, $banner->getClickUrl($user), $code);
          $code = $this->replaceConstant(self::CONST_TARGETURL, $banner->getDestinationUrl($user), $code);
          $code = $this->replaceConstant(self::CONST_HTMLJSURL, $banner->getBannerScriptUrl($user), $code);
          $code = $this->replaceConstant(self::CONST_SEOSTRING, $banner->getSeoString(), $code);
          return Pap_Common_Banner::cleanIncompleteCode($code);
      }
  
      private function replaceConstant($code, $value, $text) {
          return str_replace('{$'.$code.'}', $value, $text);
      }
  
      public static function fillCachedBanner(Pap_Common_Banner $banner, Pap_Db_CachedBanner $cachedBanner){
          if ($cachedBanner->getParentBannerId() != '') {
              $banner->setParentBannerId($cachedBanner->getParentBannerId());
          }
          
          $banner->fillCachedBanner($cachedBanner, Pap_Affiliates_User::loadFromId($cachedBanner->getUserId()));
          if($cachedBanner->getWrapper() == self::URL_VALUE_INNERPAGE){
              $cachedBanner->setCode(self::INNERPAGE_BEGIN . $cachedBanner->getCode() . self::INNERPAGE_END);
          }
      }
  
      public static function isWrapperRequest(Pap_Common_Banner $banner, Pap_Tracking_Request $request){
          return $banner->getWrapperId() !== null &&
          $request->getRequestParameter(self::URL_PARAM_WRAPPER) !== '';
      }
  }
  

} //end Pap_Merchants_Config_BannerWrapperService

if (!class_exists('Pap_Features_HoverBanner_Hover', false)) {
  class Pap_Features_HoverBanner_Hover extends Pap_Common_Banner {
  
      const FORMAT = '<html><head></head><body style="margin: 0px;"><a href="{$targeturl}" target="_parent"><img src="{$image_src}" style="border: none;"/></a>{$impression_track}</body></html>';
      const TYPE_HOVER = 'U';
  
      protected function getBannerCode(Pap_Common_User $user, $flags) {
          $code = $this->getBaseCode($user, 'hover.php', $flags);
          $code .= "<script>setTimeout(\"showOnLoad('" . $this->getId() . "','".$this->get('data3')."')\", 500);</script>";
  
          return $code;
      }
  
      public function getDisplayCode(Pap_Common_User $user) {
          return $this->getDisplayCodeFrom('data1', $user);
      }
  
      private function getDisplayCodeFrom($data, Pap_Common_User $user, $flags = '') {
          $imageUrl = $this->get($data);
          $description = $this->getDescription($user);
          $format = self::FORMAT;
  
          $format = Pap_Common_UserFields::replaceCustomConstantInText('image_src', $imageUrl, $format);
  
          $format = $this->replaceUserConstants($format, $user);
          $format = $this->replaceUrlConstants($format, $user, $flags, '');
  
          return $format;
      }
  
      public function getPreview(Pap_Common_User $user) {
          $flag = self::FLAG_MERCHANT_PREVIEW;
          if (Gpf_Session::getAuthUser()->isAffiliate()) {
              return $this->getAfiliatePreview($user);
          }
          return $this->getDisplayCodeFrom('data2', $user, $flag);
      }
  
      public function getPreviewCode(Pap_Common_User $user) {
          return $this->getDisplayCodeFrom('data1', $user, self::FLAG_AFFILIATE_PREVIEW);
      }
  
      private function getAfiliatePreview(Pap_Common_User $user) {
          $code = '<div>'.$this->getBaseCode($user, 'preview.php');
          $code .= '<a onclick="show(\'' . $this->getId() . '\','.$this->get('data3').');">'.
          $this->_('Click here to see hover banner').'</a></div>';
          $code .= '<div>'.$this->getDisplayCodeFrom('data2', $user, self::FLAG_AFFILIATE_PREVIEW).'</div>';
  
          return $code;
      }
  
      private function getBaseCode(Pap_Common_User $user, $fileName = 'hover.php', $flags = '') {
          $url = Gpf_Paths::getInstance()->getFullBaseServerUrl() . "include/Pap/Features/HoverBanner/LyteBox/";
          $code = '<script type="text/javascript" src="'.
          $url . 'lytebox.js"></script>
                  <link rel="stylesheet" href="'.
          $url . 'lytebox.css" type="text/css" media="screen" />
                  <div id="' . $this->getId() . '" rel="lyteframe" rev="width: {$width}px; height: {$height}px; scrolling: no;" href="'.
          Gpf_Paths::getInstance()->getFullScriptsUrl() . $fileName .
                  '?'.Gpf_Settings::get(Pap_Settings::PARAM_NAME_USER_ID).'='.$user->getId().
                  '&amp;'.Gpf_Settings::get(Pap_Settings::PARAM_NAME_BANNER_ID).'='.$this->getId() . '"></div>';
          return $this->replaceWidthHeightConstants($code, $flags);
      }
       
      protected function setUndefinedSize() {
          if (($imageSize = @getimagesize($this->getData1())) !== false) {
              list($this->width, $this->height) = @getimagesize($this->getData1());
              return;
          }
          $this->width = 400;
          $this->height = 300;
      }
  }
  

} //end Pap_Features_HoverBanner_Hover

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

if (!class_exists('Pap_Db_BannerInRotator', false)) {
  class Pap_Db_BannerInRotator extends Gpf_DbEngine_Row {
       
      function __construct(){
          parent::__construct();
      }
  
      function init() {
          $this->setTable(Pap_Db_Table_BannersInRotators::getInstance());
          parent::init();
      }
  
      public function setParentBannerId($id) {
          $this->set(Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID, $id);
      }
      
      public function setId($id) {
          $this->set(Pap_Db_Table_BannersInRotators::ID, $id);
      }
      
      public function getId() {
          return $this->get(Pap_Db_Table_BannersInRotators::ID);
      }
  
      public function getClicks() {
          return $this->get(Pap_Db_Table_BannersInRotators::CLICKS);
      }
  
      public function getBannerId() {
          return $this->get(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID);
      }
  
      public function getUniqeImps() {
          return $this->get(Pap_Db_Table_BannersInRotators::UNIQ_IMPS);
      }
  
      public function getAllImps() {
          return $this->get(Pap_Db_Table_BannersInRotators::ALL_IMPS);
      }
  
      public function getRank() {
          return $this->get(Pap_Db_Table_BannersInRotators::RANK);
      }
      
      public function getRotatedBannerId() {
          return $this->get(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID);
      }
      
      public function getParentBannerId() {
          return $this->get(Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID);
      }
      
      public function setRotatedBannerId($bannerid) {
          $this->set(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID, $bannerid);
      }
      
      public function setValidFrom($value) {
          $this->set(Pap_Db_Table_CachedBanners::VALID_FROM, $value);
      }
      
      public function setRank($value) {
          $this->set(Pap_Db_Table_CachedBanners::RANK, $value);
      }
      
      public function setValidUntil($value) {
          $this->set(Pap_Db_Table_CachedBanners::VALID_UNTIL, $value);
      }
      
      public function getValidFrom() {
          return $this->get(Pap_Db_Table_CachedBanners::VALID_FROM);
      }
      
      public function getValidUntil() {
          return $this->get(Pap_Db_Table_CachedBanners::VALID_UNTIL);
      }
  }
  

} //end Pap_Db_BannerInRotator

if (!class_exists('Pap_Db_Table_BannersInRotators', false)) {
  class Pap_Db_Table_BannersInRotators extends Gpf_DbEngine_Table {
      const ID = 'bannerinrotatorid';
      const PARENT_BANNER_ID = 'parentbannerid';
      const ROTATED_BANNER_ID = 'rotatedbannerid';
      const ALL_IMPS = 'all_imps';
      const UNIQ_IMPS = 'uniq_imps';
      const CLICKS = 'clicks';
      const RANK = 'rank';
      const VALID_FROM = 'valid_from';
      const VALID_UNTIL = 'valid_until';
      const ARCHIVE = 'archive';
      
      private static $instance;
      
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
      
      protected function initName() {
          $this->setName('pap_bannersinrotators');
      }
      
      public static function getName() {
          return self::getInstance()->name();
      }
      
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
          $this->createColumn(self::PARENT_BANNER_ID, self::CHAR, 8);
          $this->createColumn(self::ROTATED_BANNER_ID, self::CHAR, 8);
          $this->createColumn(self::ALL_IMPS, self::INT, 11);
          $this->createColumn(self::UNIQ_IMPS, self::INT, 11);
          $this->createColumn(self::CLICKS, self::INT, 11);
          $this->createColumn(self::RANK, self::INT);
          $this->createColumn(self::VALID_FROM, self::DATETIME);
          $this->createColumn(self::VALID_UNTIL, self::DATETIME);
          $this->createColumn(self::ARCHIVE, self::CHAR, 1);
      }
      
      protected function initConstraints() {
          $this->addConstraint(new Gpf_DbEngine_Row_RelationConstraint(
                                      array(self::PARENT_BANNER_ID => Pap_Db_Table_Banners::ID), 
                                      new Pap_Db_Banner()));
          $this->addConstraint(new Gpf_DbEngine_Row_RelationConstraint(
                                      array(self::ROTATED_BANNER_ID => Pap_Db_Table_Banners::ID), 
                                      new Pap_Db_Banner()));
      }
  }

} //end Pap_Db_Table_BannersInRotators

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

if (!class_exists('Pap_Common_Banner_Html', false)) {
  class Pap_Common_Banner_Html extends Pap_Common_Banner {
  
      protected function getBannerCode(Pap_Common_User $user, $flags) {
          $description = $this->getDescription($user);
  
          $description = $this->replaceUserConstants($description, $user);
          $description = $this->replaceUrlConstants($description, $user, $flags, '');
          $description = $this->replaceBannerConstants($description, $user);
          
          if($this->getData3() == 'N') {
              $description = preg_replace("/\<script.*?\<\/script\>/", '', $description);
          }
          return $description;
      }
  
      public function getPreview(Pap_Common_User $user) {
          if($this->getData3() == 'I') {
              return "<img src=" . $this->getData5() . " alt=\"\" />";
          }
          return parent::getPreview($user);
      }
  
      public function fillForm(Gpf_Rpc_Form $form) {
          $form->load($this);
          $this->fillIframeSize($form);
  
      }
  
      public function encodeSize(Gpf_Rpc_Form $form, $sizeFieldName) {
          parent::encodeSize($form, $sizeFieldName);
          if ($form->existsField(Pap_Db_Table_Banners::DATA4)) {
              parent::encodeSize($form, Pap_Db_Table_Banners::DATA4);
          }
      }
  
      private function fillIframeSize(Gpf_Rpc_Form $form) {
          $form->setField(Pap_Db_Table_Banners::DATA4, $this->getSizeType(Pap_Db_Table_Banners::DATA4));
          $sizeArray = explode('x', substr($this->getData4(), 1));
          if (count($sizeArray) < 2) {
              return;
          }
  
          if ($form->getFieldValue(Pap_Db_Table_Banners::DATA4) == Pap_Db_Banner::SIZE_PREDEFINED) {
              $form->setField('size_predefined', $sizeArray[0].'x'.$sizeArray[1]);
              return;
          }
          	
          if ($form->getFieldValue(Pap_Db_Table_Banners::DATA4) == Pap_Db_Banner::SIZE_OWN) {
              $form->setField('size_width', $sizeArray[0]);
              $form->setField('size_height', $sizeArray[1]);
          }
      }
  }
  

} //end Pap_Common_Banner_Html

if (!class_exists('Pap_Common_UserFields', false)) {
  class Pap_Common_UserFields extends Gpf_Object  {
  
      /**
       * @var Pap_Common_User
       */
      private $user;
      /**
       * @var instance
       */
      static private $instance = null;
      static private $userFields = null;
      private $countryCodeFields = null;
  
      /*
       * TODO: this cache needs to be refatored
       */
      private $cache = array();
  
      private function __construct() {
          $this->user = null;
      }
  
      /**
       * returns instance of UserFields class
       *
       * @return Pap_Common_UserFields
       */
      public static function getInstance() {
          if (self::$instance == null) {
              self::$instance = new Pap_Common_UserFields();
          }
          return self::$instance;
      }
  
      /**
       * returns array of user fields
       *
       * @return unknown
       */
      public function getUserFields($type = array('M', 'O', 'R'), $mainFields = null) {
          if (is_array(self::$userFields)) {
              return self::$userFields;
          }
          $ffTable = Gpf_Db_Table_FormFields::getInstance();
          $formFields = $ffTable->getFieldsNoRpc("affiliateForm", $type, $mainFields);
  
          self::$userFields = array();
          self::$userFields['firstname'] = $this->_('First Name');
          self::$userFields['lastname'] = $this->_('Last Name');
          self::$userFields['username'] = $this->_('Username');
          self::$userFields['password'] = $this->_('Password');
          self::$userFields['ip'] = $this->_('IP');
          self::$userFields['photo'] = $this->_('Photo');
  
          foreach($formFields as $record) {
              $code = $record->get('code');
              $name = $record->get('name');
              self::$userFields[$code] = $name;
          }
  
          return self::$userFields;
      }
  
      /**
       * @service affiliate read
       */
      public function getVariablesRpc(Gpf_Rpc_Params $params) {
          return new Gpf_Rpc_Map(self::getUserFields());
      }
  
      public function getUserFieldsValues($mainFields = null) {
          if($this->user == null) {
              throw new Gpf_Exception("You have to set User before getting user fields value!");
          }
  
          if (array_key_exists($this->user->getId(), $this->cache)) {
              return $this->cache[$this->user->getId()];
          }
  
          $fields = $this->getUserFields(array('M', 'O', 'R', 'D'), $mainFields);
          $result = array();
          foreach($fields as $code => $name) {
              $result[$code] = $this->getUserFieldValue($code);
          }
          $this->cache[$this->user->getId()] = $result;
          return $result;
      }
  
      public function getUserFieldValue($code) {
          if($this->user == null) {
              throw new Gpf_Exception("You have to set User before getting user fields value!");
          }
  
          if($code == 'refid') {
              return $this->user->getRefId();
          } else if($code == 'firstname') {
              return $this->user->getFirstName();
          } else if($code == 'lastname') {
              return $this->user->getLastName();
          } else if($code == 'password') {
              return $this->user->getPassword();
          } else if($code == 'username') {
              return $this->user->getUserName();
          } else if($this->isCountryCode($code)){
              return $this->getCountryName($this->user->get($code));
          }
          try {
              return $this->user->get($code);
          } catch (Gpf_Exception $e) {
          }
          return '';
      }
  
      public function setUser(Pap_Common_User $user) {
          $this->user = $user;
      }
  
      public function loadUserById($userId) {
      }
  
      /**
       * replaces user fields values in standard format (${#data1#)
       * with their values
       *
       * @param string $text
       */
      public function replaceUserConstantsInText($text, $mainFields = null) {
          $values = $this->getUserFieldsValues($mainFields);
  
          // simple replace
          foreach($values as $code => $value) {
              $text = self::replaceCustomConstantInText($code, $value, $text);
          }
  
          return $text;
      }
  
      /**
       * removes user fields values in standard format (${#data1#)
       *
       * @param string $text
       */
      public function removeUserConstantsInText($text) {
          $fields = $this->getUserFields();
          foreach($fields as $code => $value) {
              $text = self::replaceCustomConstantInText($code, '', $text);
          }
  
          return $text;
      }
  
      /**
       * remove constants in standard format {*some comment*}
       * @throws Gpf_Exception
       *
       * @param string $text
       */
      public static function removeCommentsInText($text) {
          while ($indexOfStartTag = strpos($text, '{*')) {
              $start = substr($text, 0, $indexOfStartTag);
              $indexOfEndTag = strpos($text, '*}');
              if (!$indexOfEndTag) {
                  throw new Gpf_Exception("Comment not closed !");
              }
              $end = substr($text, $indexOfEndTag+2);
              $text = $start.$end;
          }
          return $text;
      }
  
      public static function replaceCustomConstantInText($code, $value, $text) {
          return str_replace('{$'.$code.'}', $value, $text);
      }
  
      protected function getCountryName($country_code){
          if($country_code == null) {
              return '';
          }
          $country = new Gpf_Db_Country();
          $country->setCountryCode($country_code);
          try {
              $country->loadFromData();
              return Gpf_Lang::_localizeRuntime($country->getCountry());
          } catch (Gpf_Exception $e) {
              return '';
          }
      }
  
      private function isCountryCode($code){
          if ($this->countryCodeFields == null) {
              $this->loadCountryCodeFields();
          }
          return in_array($code, $this->countryCodeFields);
      }
  
      private function loadCountryCodeFields() {
          $this->countryCodeFields = array();
          foreach ($this->getFormFields() as $row){
              if( ($row->getType() == Gpf_Db_FormField::TYPE_COUNTRY_LISTBOX ||
              $row->getType() == Gpf_Db_FormField::TYPE_COUNTRY_LISTBOX_GWT)) {
                  $this->countryCodeFields[] = $row->getCode();
              }
          }
      }
  
      /**
       * @return Gpf_DbEngine_Row_Collection
       */
      protected function getFormFields() {
          $field  = new Gpf_Db_FormField();
          $field->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
          $field->setFormId('affiliateForm');
          return $field->loadCollection();
      }
  }
  

} //end Pap_Common_UserFields

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

if (!class_exists('Pap_Tracking_TrackerBase', false)) {
  class Pap_Tracking_TrackerBase extends Gpf_Object {
  	const LOGGER_TRACKING = 'tracking';
  	   
  	/**
       * @var Gpf_Log_Logger
       */
  	protected $logger;
  	/**
       * @var Pap_Tracking_Request
       */
  	protected $request;
  	/**
  	 * @var Pap_Tracking_Response
  	 */
  	protected $response;
  	/**
  	 * @var Pap_Tracking_Cookie
  	 */
  	protected $cookie;
  	/**
  	 * @var Pap_Common_User
  	 */
  	protected $user = null;
  	/**
  	 * @var Pap_Common_Campaign
  	 */
  	protected $campaign = null;
  	/**
  	 * @var string
  	 */
  	protected $data1;
  	/**
  	 * @var string
  	 */
  	protected $data2;
  	/**
  	 * @var string
  	 */
  	protected $data3;
  	/**
  	 * @var string
  	 */
  	protected $data4;
  		/**
  	 * @var string
  	 */
  	protected $data5;
  	/**
  	 * @var string
  	 */
  	protected $countryCode = '';
  	/**
  	 * @var string
  	 */
  	protected $ip;
  	/**
  	 * @var string
  	 */
  	protected $browser;
  	/**
  	 * @var string
  	 */
  	protected $referrer;
      
      public function track(){
  		throw new Pap_Tracking_Exception("You cannot call track() from the base class");
      }
      
      protected function debug($msg) {
      	if($this->logger != null) {
      		$this->logger->debug($msg);
      	}
      }
  
      public function info($msg) {
      	if($this->logger != null) {
      		$this->logger->info(msg);
      	}
      }
      
      public function error($msg) {
      	if($this->logger != null) {
      		$this->logger->error(msg);
      	}
      }
      
      public function getScriptUrl($scriptName) {
          return Gpf_Paths::getInstance()->getFullScriptsUrl() . $scriptName;
      }
      
      private function checkActionTypeInDebugTypes($actionType) {
          $debugTypes = Gpf_Settings::get(Pap_Settings::DEBUG_TYPES);
      	if($debugTypes == '') {
      		return false;
      	}
      	
      	$arr = explode(",", $debugTypes);
      	if(in_array($actionType, $arr)) {
      		return true;
      	}
      	return false;
      }    
  }
  

} //end Pap_Tracking_TrackerBase

if (!class_exists('Pap_Tracking_ClickTracker', false)) {
  class Pap_Tracking_ClickTracker extends Pap_Tracking_TrackerBase {
      const LINKMETHOD_REDIRECT = "R";
      const LINKMETHOD_URLPARAMETERS = "P";
      const LINKMETHOD_MODREWRITE = "S";
      const LINKMETHOD_DIRECTLINK = "D";
      const LINKMETHOD_ANCHOR = "A";
      
      /**
       * @var Pap_Tracking_ClickTracker
       */
      private static $instance = NULL;
  
      /**
       * @var Pap_Tracking_Cookie
       */
      private $cookies;
  
      /**
       * @return Pap_Tracking_ClickTracker
       */
      public static function getInstance() {
          if (self::$instance == NULL) {
              self::$instance = new Pap_Tracking_ClickTracker();
          }
          return self::$instance;
      }
  
      public function __construct(){
          $this->cookies = new Pap_Tracking_Cookie();
      }
  
      public static function sendJavaScriptHeaders() {
          Gpf_Http::setHeader('Content-Type', 'application/x-javascript');
      }
  
      public function getLinkingMethod() {
          return Gpf_Settings::get(Pap_Settings::SETTING_LINKING_METHOD);
      }
  
      /**
       * Gets clickUrl for banner
       *
       * @param Pap_Common_Banner $banner
       * @param Pap_Common_User $user
       * @param string $specialDestinationUrl
       * @return string
       */
      public function getClickUrl(Pap_Common_Banner $banner = null,
                                  Pap_Common_User $user,
                                  $specialDestinationUrl = '',
                                  $flags = '',
                                  Pap_Db_Channel $channel = null) {
  
          if ($banner != null && $banner->getDynamicLink() != null && $flags == self::LINKMETHOD_REDIRECT) {
              return $this->getRedirectClickUrl($banner, $user, $banner->getDynamicLink(), $channel);
          }
          if($flags & Pap_Common_Banner::FLAG_DIRECTLINK) {
              return $this->geDirectLinkClickUrl($banner, $user, $specialDestinationUrl);
          }
          if ($this->getLinkingMethod() == self::LINKMETHOD_REDIRECT) {
              return $this->getRedirectClickUrl($banner, $user, $specialDestinationUrl, $channel);
  
          } else if ($this->getLinkingMethod() == self::LINKMETHOD_URLPARAMETERS) {
              return $this->getUrlParametersClickUrl($banner, $user, $specialDestinationUrl, $channel);
  
          } else if ($this->getLinkingMethod() == self::LINKMETHOD_MODREWRITE) {
              return $this->getModRewriteClickUrl($banner, $user, $specialDestinationUrl, $channel);
  
          } else if ($this->getLinkingMethod() == self::LINKMETHOD_DIRECTLINK) {
              return $this->geDirectLinkClickUrl($banner, $user, $specialDestinationUrl, $channel);
  
          } else if ($this->getLinkingMethod() == self::LINKMETHOD_ANCHOR) {
              return $this->getAnchorClickUrl($banner, $user, $specialDestinationUrl, $channel);
          }
      }
       
      /**
       * @return String redirect click url (redirect through click.php script)
       */
      private function getRedirectClickUrl(Pap_Common_Banner $banner = null, Pap_Common_User $user, $specialDesturl = '', Pap_Db_Channel $channel = null) {
          $clickUrl = Pap_Tracking_TrackerBase::getScriptUrl("click.php");
          $clickUrl .= "?".Pap_Tracking_Request::getAffiliateClickParamName()."=".$user->getRefId();
          $clickUrl .= $this->getBannerParams($banner);
          if ($specialDesturl != '') {
              $clickUrl .= "&amp;".Pap_Tracking_Request::getSpecialDestinationUrlParamName()."=".urlencode($specialDesturl);
          }
          if($channel != null && is_object($channel)) {
              $clickUrl .= "&amp;".Pap_Tracking_Request::getChannelParamName()."=".$channel->getValue();
          }
          return $clickUrl;
      }
  
      /**
       * @return String url parameters style click url (requires integration code on landing page)
       */
      private function getUrlParametersClickUrl(Pap_Common_Banner $banner = null,
                                                Pap_Common_User $user,
                                                $specialDesturl = '',
                                                Pap_Db_Channel $channel = null, $urlSeparator = '?') {
  
          $clickUrl = $this->getDestinationUrl($banner, $specialDesturl, $user);
  
          $firstParamSeparator = ($urlSeparator === '#' ? $urlSeparator : '&amp;');
          
          $clickUrl .= (strpos($clickUrl, '?') === false) ? $urlSeparator : $firstParamSeparator;
          $clickUrl .= Pap_Tracking_Request::getAffiliateClickParamName()."=".$user->getRefId();
          $clickUrl .= $this->getBannerParams($banner);
          if($channel != null && is_object($channel)) {
              $clickUrl .= "&amp;".Pap_Tracking_Request::getChannelParamName()."=".$channel->getValue();
          }
          return $clickUrl;
      }
      
      private function getAnchorClickUrl(Pap_Common_Banner $banner = null,
                                                Pap_Common_User $user,
                                                $specialDesturl = '',
                                                Pap_Db_Channel $channel = null) {
  
          return $this->getUrlParametersClickUrl($banner, $user, $specialDesturl, $channel, '#');
      }
  
      function getBannerParams(Pap_Common_Banner $banner = null){
        $clickUrl = '';
        if ($banner != null) {
              $clickUrl .= "&amp;".Pap_Tracking_Request::getBannerClickParamName()."=".$banner->getId();
              if ($banner->getParentBannerId()!= null) {
                  $clickUrl .= "&amp;".Pap_Tracking_Request::getRotatorBannerParamName()."=".$banner->getParentBannerId();
              }
          }
          return $clickUrl;
      }
      
      
      /**
       * @return String seo style click url
       */
      public function getModRewriteClickUrl(Pap_Common_Banner $banner = null,
                                             Pap_Common_User $user,
                                             $specialDesturl = '',
                                             Pap_Db_Channel $channel = null,
                                             $siteUrl = null) {
  
         	$prefix = Gpf_Settings::get(Pap_Settings::MOD_REWRITE_PREFIX_SETTING_NAME);
          $separator = Gpf_Settings::get(Pap_Settings::MOD_REWRITE_SEPARATOR_SETTING_NAME);
          $suffix = Gpf_Settings::get(Pap_Settings::MOD_REWRITE_SUFIX_SETTING_NAME);
          if ($siteUrl === null) {
              $siteUrl = Gpf_Settings::get(Pap_Settings::MAIN_SITE_URL);
          }
  
          if($siteUrl[strlen($siteUrl)-1] != '/') {
              $siteUrl .= '/';
          }
          $clickUrl = $siteUrl;
          $clickUrl .= $prefix.$user->getRefId();
          if ($banner != null) {
              $clickUrl .= $separator.$banner->getId();
          }
          if($channel != null && is_object($channel)) {
              $clickUrl .= $separator.$channel->getValue();
          }
  
          $clickUrl .= $suffix;
  
          return $clickUrl;
      }
  
      /**
       * @return String old style click url (redirect through click.php script)
       */
      private function geDirectLinkClickUrl(Pap_Common_Banner $banner = null,
                                            Pap_Common_User $user,
                                            $specialDesturl = '',
                                            Pap_Db_Channel $channel = null) {
                                                
          return $this->getDestinationUrl($banner, $specialDesturl, $user);
      }
      
      private function getDestinationUrl(Pap_Common_Banner $banner = null, $specialDestinationUrl = '', Pap_Common_User $user = null) {
          if ($specialDestinationUrl != '') {
              return $specialDestinationUrl;
          }
          if ($banner != null) {
              return $banner->getDestinationUrl($user);
          }
          return Gpf_Settings::get(Pap_Settings::MAIN_SITE_URL);
      }
  }
  

} //end Pap_Tracking_ClickTracker

if (!class_exists('Pap_Tracking_ImpressionTracker', false)) {
  class Pap_Tracking_ImpressionTracker extends Pap_Tracking_TrackerBase {
      /**
       * @var Pap_Tracking_ImpressionTracker
       */
      private static $instance = NULL;
  
      /**
       * @return Pap_Tracking_ImpressionTracker
       */
      public static function getInstance() {
          if (self::$instance == NULL) {
              self::$instance = new Pap_Tracking_ImpressionTracker();
          }
          return self::$instance;
      }
  
      private function __construct() {
      }
  
      /**
       * @param Pap_Common_Banner $banner
       * @param Pap_Common_User $user
       * @param Pap_Db_Channel $channel
       * @return string
       */
      public function getImpressionTrackingCode(Pap_Common_Banner $banner, Pap_Common_User $user, Pap_Db_Channel $channel = null) {
          $code  = "<img style=\"border:0\" src=\"";
          $code .= $this->getSrcCode($banner,$user,$channel);
          $code .= "\" width=\"1\" height=\"1\" alt=\"\" />";
          return $code;
      }
  
      public function getSrcCode(Pap_Common_Banner $banner, Pap_Common_User $user, Pap_Db_Channel $channel = null){
          $code = $this->getScriptUrl("imp.php");
          $code .= "?".Pap_Tracking_Request::getAffiliateClickParamName()."=".$user->getRefId();
          $code .= "&amp;".Pap_Tracking_Request::getBannerClickParamName()."=".$banner->getId();
          if ($banner->getParentBannerId() != null) {
              $code .= "&amp;".Pap_Tracking_Request::getRotatorBannerParamName()."=".$banner->getParentBannerId();
          }
          if($channel != null && is_object($channel)) {
              $code .= "&amp;".Pap_Tracking_Request::getChannelParamName()."=".$channel->getValue();
          }
          return $code;
      }
       
  }
  

} //end Pap_Tracking_ImpressionTracker

if (!class_exists('Pap_Common_BannerReplaceVariablesContext', false)) {
  class Pap_Common_BannerReplaceVariablesContext  {
  
      private $text;
      /**
       * @var Pap_Common_Banner
       */
      private $banner;
      /**
       * @var Pap_Common_User
       */
      private $user;
      
      public function __construct($text, Pap_Common_Banner $banner, Pap_Common_User $user = null) {
          $this->text = $text;
          $this->banner = $banner;
          $this->user = $user;
      }
      
      public function getText() {
          return $this->text;
      }
      
      public function setText($text) {
          $this->text = $text;
      } 
      
      /**
       * @return Pap_Common_Banner
       */
      public function getBanner() {
          return $this->banner;
      }
      
      /**
       * @return Pap_Common_User
       */
      public function getUser() {
          return $this->user;
      }
  }

} //end Pap_Common_BannerReplaceVariablesContext

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

if (!class_exists('Pap_Db_BannerWrapper', false)) {
  class Pap_Db_BannerWrapper extends Gpf_DbEngine_Row { 
  
      protected function init() {
          $this->setTable(Pap_Db_Table_BannerWrappers::getInstance());
          parent::init();
      }
  
      public function getId() {
          return $this->get(Pap_Db_Table_BannerWrappers::ID);
      }
  
      public function setId($id) {
          $this->set(Pap_Db_Table_BannerWrappers::ID, $id);
      }
  
      public function getName() {
          return $this->get(Pap_Db_Table_BannerWrappers::NAME);
      }
  
      public function setName($name) {
          $this->set(Pap_Db_Table_BannerWrappers::NAME, $name);
      }
  
      public function getCode() {
          return $this->get(Pap_Db_Table_BannerWrappers::CODE);
      }
  
      public function setCode($name) {
          $this->set(Pap_Db_Table_BannerWrappers::CODE, $name);
      }
  }
  

} //end Pap_Db_BannerWrapper

if (!class_exists('Pap_Db_Table_BannerWrappers', false)) {
  class Pap_Db_Table_BannerWrappers extends Gpf_DbEngine_Table {
      const ID = 'wrapperid';
      const NAME = 'name';
      const CODE = 'code';
  
      private static $instance;
  
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
  
      protected function initName() {
          $this->setName('pap_bannerwrappers');
      }
  
      public static function getName() {
          return self::getInstance()->name();
      }
  
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, self::CHAR, 8, true);
          $this->createColumn(self::NAME, self::CHAR, 80);
          $this->createColumn(self::CODE, self::CHAR);
      }
  
      protected function initConstraints() {
          $this->addRestrictDeleteConstraint(self::ID, Pap_Db_Table_Banners::WRAPPER_ID,new Pap_Db_Banner());
      }
  }

} //end Pap_Db_Table_BannerWrappers
/*
VERSION
f3fafbce4a663f5065d4be07883d9539
*/
?>
