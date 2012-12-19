<?php
/** *   @copyright Copyright (c) 2008-2009 Quality Unit s.r.o. *   @author Quality Unit *   @package Core classes *   @since Version 1.0.0 *    *   Licensed under the Quality Unit, s.r.o. Dual License Agreement, *   Version 1.0 (the "License"); you may not use this file except in compliance *   with the License. You may obtain a copy of the License at *   http://www.qualityunit.com/licenses/gpf *    */

if (!class_exists('Gpf_Rpc_Server', false)) {
  class Gpf_Rpc_Server extends Gpf_Object {
      const REQUESTS = 'requests';
      const REQUESTS_SHORT = 'R';
      const RUN_METHOD = 'run';
      const FORM_REQUEST = 'FormRequest';
      const FORM_RESPONSE = 'FormResponse';
      const BODY_DATA_NAME = 'D';
  
  
      const HANDLER_FORM = 'Y';
      const HANDLER_JASON = 'N';
      const HANDLER_WINDOW_NAME = 'W';
  
      /**
       * @var Gpf_Rpc_DataEncoder
       */
      private $dataEncoder;
      /**
       * @var Gpf_Rpc_DataDecoder
       */
      private $dataDecoder;
  
      public function __construct() {
      }
  
      private function initDatabaseLogger() {
          $logger = Gpf_Log_Logger::getInstance();
  
          if(!$logger->checkLoggerTypeExists(Gpf_Log_LoggerDatabase::TYPE)) {
              $logger->setGroup(Gpf_Common_String::generateId(10));
              $logLevel = Gpf_Settings::get(Gpf_Settings_Gpf::LOG_LEVEL_SETTING_NAME);
              $logger->add(Gpf_Log_LoggerDatabase::TYPE, $logLevel);
          }
      }
  
      /**
       * Return response to standard output
       */
      public function executeAndEcho($request = '') {
          $response = $this->encodeResponse($this->execute($request));
          Gpf_ModuleBase::startGzip();
          echo $response;
          Gpf_ModuleBase::flushGzip();
      }
  
      /**
       * @return Gpf_Rpc_Serializable
       */
      public function execute($request = '') {
          try {
              if(isset($_REQUEST[self::BODY_DATA_NAME])) {
                  $request = $this->parseRequestDataFromPost($_REQUEST[self::BODY_DATA_NAME]);
              }
              if($this->isStandardRequestUsed($_REQUEST)) {
                  $request = $this->setStandardRequest();
              }
  
              $this->setDecoder($request);
              $params = new Gpf_Rpc_Params($this->decodeRequest($request));
              $this->setEncoder($params);
              $response = $this->executeRequest($params);
          } catch (Exception $e) {
              return new Gpf_Rpc_ExceptionResponse($e);
          }
          return $response;
      }
  
      private function parseRequestDataFromPost($data) {
          if(get_magic_quotes_gpc()) {
              return stripslashes($data);
          }
          return $data;
      }
  
      /**
       *
       * @param unknown_type $requestObj
       * @return Gpf_Rpc_Serializable
       */
      private function executeRequest(Gpf_Rpc_Params $params) {
          try {
              Gpf_Db_LoginHistory::logRequest();
              return $this->callServiceMethod($params);
          } catch (Gpf_Rpc_SessionExpiredException $e) {
              return $e;
          } catch (Exception $e) {
              return new Gpf_Rpc_ExceptionResponse($e);
          }
      }
  
      protected function callServiceMethod(Gpf_Rpc_Params $params) {
          $method = new Gpf_Rpc_ServiceMethod($params);
          return $method->invoke($params);
      }
  
      /**
       * Compute correct handler type for server response
       *
       * @param array $requestData
       * @param string $type
       * @return string
       */
      private function getEncoderHandlerType($requestData) {
          if ($this->isFormHandler($requestData, self::FORM_RESPONSE, self::HANDLER_FORM)) {
              return self::HANDLER_FORM;
          }
          if ($this->isFormHandler($requestData, self::FORM_RESPONSE, self::HANDLER_WINDOW_NAME)) {
              return self::HANDLER_WINDOW_NAME;
          }
          return self::HANDLER_JASON;
      }
  
  
      private function isFormHandler($requestData, $type, $handler) {
          return (isset($_REQUEST[$type]) && $_REQUEST[$type] == $handler) ||
          (isset($requestData) && isset($requestData[$type]) && $requestData[$type] == $handler);
      }
  
      private function decodeRequest($requestData) {
          return $this->dataDecoder->decode($requestData);
      }
  
      private function isStandardRequestUsed($requestArray) {
          return is_array($requestArray) && array_key_exists(Gpf_Rpc_Params::CLASS_NAME, $requestArray);
      }
  
      private function setStandardRequest() {
          return array_merge($_POST, $_GET);
      }
  
      private function isFormRequest($request) {
          return $this->isFormHandler($request, self::FORM_REQUEST, self::HANDLER_FORM);
      }
  
      private function encodeResponse(Gpf_Rpc_Serializable $response) {
          return $this->dataEncoder->encodeResponse($response);
      }
  
  
      private function setDecoder($request) {
          if ($this->isFormRequest($request)) {
              $this->dataDecoder = new Gpf_Rpc_FormHandler();
          } else {
              $this->dataDecoder = new Gpf_Rpc_Json();
          }
      }
  
      private function setEncoder(Gpf_Rpc_Params $params) {
          switch ($params->get(self::FORM_RESPONSE)) {
              case self::HANDLER_FORM:
                  $this->dataEncoder = new Gpf_Rpc_FormHandler();
                  break;
              case self::HANDLER_WINDOW_NAME:
                  $this->dataEncoder = new Gpf_Rpc_WindowNameHandler();
                  break;
              default:
                  $this->dataEncoder = new Gpf_Rpc_Json();
                  break;
          }
      }
  
      /**
       * Executes multi request
       *
       * @service
       * @anonym
       * @return Gpf_Rpc_Serializable
       */
      public function run(Gpf_Rpc_Params $params) {
          $requestArray = $params->get(self::REQUESTS);
  
          if ($requestArray === null) {
              $requestArray = $params->get(self::REQUESTS_SHORT);
          }
  
          $response = new Gpf_Rpc_Array();
          foreach ($requestArray as $request) {
              $response->add($this->executeRequest(new Gpf_Rpc_Params($request)));
          }
          return $response;
      }
  
      /**
       * Set time offset between client and server and store it to session
       * Offset is computed as client time - server time
       *
       * @anonym
       * @service
       * @param Gpf_Rpc_Params $params
       * @return Gpf_Rpc_Action
       */
      public function syncTime(Gpf_Rpc_Params $params) {
          $action = new Gpf_Rpc_Action($params);
          Gpf_Session::getInstance()->setTimeOffset($action->getParam('offset')/1000);
          $action->addOk();
          return $action;
      }
  }

} //end Gpf_Rpc_Server

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

if (!class_exists('Gpf_Db_LoginHistory', false)) {
  class Gpf_Db_LoginHistory extends Gpf_DbEngine_Row {
  
      const WRITE_DELAY = 30;
  
      function __construct(){
          parent::__construct();
      }
  
      public function init() {
          $this->setTable(Gpf_Db_Table_LoginsHistory::getInstance());
          parent::init();
      }
  
      public function setId($id) {
          $this->set(Gpf_Db_Table_LoginsHistory::ID, $id);
      }
  
      public function setLastRequestTime($time) {
          $this->set(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, $time);
      }
  
      public function setLoginTime($time) {
          $this->set(Gpf_Db_Table_LoginsHistory::LOGIN, $time);
      }
  
      public function setLogoutTime($time) {
          $this->set(Gpf_Db_Table_LoginsHistory::LOGOUT, $time);
      }
  
      public function setIp($ip) {
          $this->set(Gpf_Db_Table_LoginsHistory::IP, $ip);
      }
  
      public function setAccountUserId($accountUserId) {
          $this->set(Gpf_Db_Table_Users::ID, $accountUserId);
      }
  
      public function getId() {
          return $this->get(Gpf_Db_Table_LoginsHistory::ID);
      }
  
      public static function logRequest() {
          try {
              if (!Gpf_Session::getInstance()->getAuthUser()->isLogged()) {
                  //user is not logged in, don't monitor his session
                  return;
              }
          } catch (Exception $e) {
              return;
          }
  
          $log = new Gpf_Db_LoginHistory();
          if ($loginId = Gpf_Session::getInstance()->getVar(Gpf_Db_Table_LoginsHistory::ID)) {
              if ((time() - Gpf_Session::getInstance()->getVar(Gpf_Db_Table_LoginsHistory::LAST_REQUEST)) > self::WRITE_DELAY) {
                  //login id already defined, update last request time
                  $log->setId($loginId);
                  $log->setLastRequestTime($log->createDatabase()->getDateString());
                  $log->update();
                  Gpf_Session::getInstance()->setVar(Gpf_Db_Table_LoginsHistory::LAST_REQUEST, time());
              }
          }
      }
  
      public static function logLogout() {
          try {
              if (!Gpf_Session::getInstance()->getAuthUser()->isLogged()) {
                  //user is not logged in, don't monitor his session
                  return;
              }
          } catch (Exception $e) {
              return;
          }
  
          if ($loginId = Gpf_Session::getInstance()->getVar(Gpf_Db_Table_LoginsHistory::ID)) {
              $log = new Gpf_Db_LoginHistory();
              $log->setId($loginId);
              $log->setLogoutTime($log->createDatabase()->getDateString());
              $log->update();
              Gpf_Session::getInstance()->setVar(Gpf_Db_Table_LoginsHistory::ID, false);
          }
      }
  }
  

} //end Gpf_Db_LoginHistory

if (!class_exists('Gpf_Rpc_ServiceMethod', false)) {
  class Gpf_Rpc_ServiceMethod extends Gpf_Object {
      protected $methodName;
      protected $className;
      /**
       * @var Gpf_Rpc_Annotation
       */
      protected $annotations;
      protected $serviceObj;
  
      function __construct(Gpf_Rpc_Params $params) {
          $this->methodName = $params->getMethod();
          $this->className = $params->getClass();
  
          $reflectionClass = new ReflectionClass($this->className);
          $reflectionMethod = $reflectionClass->getMethod($this->methodName);
          
          if (!$reflectionMethod->isPublic()) {
              throw new Gpf_Exception($this->className.'->'.$this->methodName.'() is not a service method (not public)');
          }
          $this->annotations = new Gpf_Rpc_Annotation($reflectionMethod);
          if (!$this->annotations->hasServiceAnnotation()) {
              throw new Gpf_Exception($this->className.'->'.$this->methodName.'() is not a service method (annotation)');
          }
  
          $this->initSession($params->getSessionId());
          $this->createInstance();
      }
      
      protected function createConstructorInstance() {
          $reflectionClass = new ReflectionClass($this->className);
          $constructor = $reflectionClass->getConstructor();
          if(is_object($constructor) && !$constructor->isPublic()) {
              throw new Gpf_Exception('Constructor of class '.$this->className.' is not public');
          }
          $this->serviceObj = Gpf::newObj($this->className);
      }
      
      protected function createInstance() {
          try {
              $this->createConstructorInstance();
              return;    
          } catch (Exception $e) {
              $reflectionMethod = new ReflectionMethod($this->className, "getInstance");
              $this->serviceObj = $reflectionMethod->invoke(null);
          }
      }
      
      public function invoke(Gpf_Rpc_Params $params) {
          Gpf_Log::debug($this->_sys("Invoking method %s->%s()", $this->className, $this->methodName));
          $this->checkPermissions($params);
          $this->checkParams();
          return call_user_func(array(&$this->serviceObj, $this->methodName), $params);
      }
      
      protected function initSession($sessionId) {
          Gpf_Session::load($sessionId);
      }
  
      protected function checkPermissions(Gpf_Rpc_Params $params) {
          if ($this->annotations->hasAnonymAnnotation()) {
              return;
          }
          if (Gpf_Session::getAuthUser()->isLogged()) {
              if (!$this->annotations->hasServicePermissionsAnnotation()) {
                  throw new Gpf_Exception("Method ".$this->className."->".$this->methodName."() does not have permission annotation");
              }
              if (Gpf_Session::getAuthUser()->hasPrivilege(
                  $this->annotations->getServicePermissionObject(),
                  $this->annotations->getServicePermissionPrivilege())) {
                      return;
              }
          }
          throw new Gpf_Rpc_PermissionDeniedException($this->className, $this->methodName);
      }
  
      private function checkParams() {
      }
  }

} //end Gpf_Rpc_ServiceMethod

if (!class_exists('Gpf_Rpc_Annotation', false)) {
  class Gpf_Rpc_Annotation extends Gpf_Object {
      const SERVICE = 'service';
      const ANONYM = 'anonym';
      
      private $permissionObject = "";
      private $permissionPrivilege = "";
      private $annotations;
  
      function __construct(ReflectionMethod $method) {
          $this->parseComment($this->getDocComment($method));
          $this->parsePermissionAnnotation($this->getAnnotation(self::SERVICE));
      }
      
      public function hasAnnotation($name) {
          return isset($this->annotations[$name]);
      }
  
      private function getAnnotation($name) {
          if ($this->hasAnnotation($name)) {
              return $this->annotations[$name];
          }
          return '';
      }
      
      public function hasServiceAnnotation() {
          return $this->hasAnnotation(self::SERVICE);
      }
      
      public function hasAnonymAnnotation() {
          return $this->hasAnnotation(self::ANONYM);
      }
      
      public function hasServicePermissionsAnnotation() {
          return $this->permissionObject != "" || $this->permissionPrivilege != "";
      }
      
      public function getServicePermissionObject() {
          return $this->permissionObject;
      }
      
      public function getServicePermissionPrivilege() {
          return $this->permissionPrivilege;
      }
      
      private function parsePermissionAnnotation($annotation) {
          $parsedArray = explode(" ", $annotation);
          if (is_array($parsedArray) && count($parsedArray) >= 2) {
              $this->permissionObject = $parsedArray[0];
              $this->permissionPrivilege = $parsedArray[1];
          }
      }
      
      /**
       *
       * @param ReflectionMethod $method
       * @return string
       */
      private function getDocComment(ReflectionMethod $method) {
          $comment = '';
          //$comment = $method->getDocComment();
          if(strlen($comment) > 0) {
              return $comment;
          }
          $fileName = $method->getDeclaringClass()->getFileName();
          
          $commentParser = new Gpf_Rpc_Annotation_CommentParser(new Gpf_Io_File($fileName));
          return $commentParser->getMethodComment($method->getName());
      }
      
      private function parseComment($comment) {
          $lines = explode("\n", $comment);
          if (count($lines)) {
              foreach ($lines as $line) {
                  $this->parseLine($line);
              }
          }
      }
  
      private function parseLine($line) {
          if (($posAt = strpos($line, "@")) === false) {
              return;
          }
          if (strlen($line = substr($line, $posAt+1)) < 1) {
              return;
          }
          if (($posSpace = strpos($line, " ")) === false) {
              $posSpace = strlen($line);
          }
          $name = trim(substr($line, 0, $posSpace));
          $value = trim(substr($line, $posSpace+1));
          $this->annotations[$name] = $value;
      }
  }

} //end Gpf_Rpc_Annotation

if (!class_exists('Gpf_Rpc_Annotation_CommentParser', false)) {
  class Gpf_Rpc_Annotation_CommentParser extends Gpf_Object {
      /**
       * @var Gpf_Io_File
       */
      private $file;
      
      public function __construct(Gpf_Io_File $file) {
          $this->file = $file;
      }
      
      public function getMethodComment($methodName) {
          try {
              $this->file->open();
              $source = $this->file->getContents();
          } catch (Exception $e) {
              return '';
          }
  
          $pattern = '|\s+function\s*' . $methodName . '\s*\(|ims';
          if(preg_match_all($pattern, $source, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE) > 0) {
              foreach ($matches as $match) {
                  $comment = $this->getComment(substr($source, 0, $match[0][1]));
                  if($comment !== false) {
                      return $comment;
                  }
              }
          }
          return '';
      }
  
      private function getComment($source) {
          $endCommentPosition = strrpos($source, '*/');
          if(false === $endCommentPosition) {
              return false;
          }
          if(strpos(substr($source, $endCommentPosition), '(') !== false) {
              return false;
          }
          
          $startCommentPosition = strrpos($source, '/**');
          if(false === $startCommentPosition) {
              return false;
          }
          return substr($source, $startCommentPosition, $endCommentPosition - $startCommentPosition + 2);
      }
  }

} //end Gpf_Rpc_Annotation_CommentParser

if (!class_exists('Gpf_Module', false)) {
  abstract class Gpf_Module extends Gpf_ModuleBase {
      
      protected function checkIfUserIsLogged() {
          if ($this->isAuthUserLogged()) {
              return;
          }
          $this->login();
      }
      
      protected function onStart() {
          parent::onStart();
          $this->checkIfUserIsLogged();
      }
      
      protected function login() {
          $this->authenticate();
          
          if (!$this->isAuthUserLogged()) {
              $this->redirectToLogin();
          }
      }
      
      protected function redirectToLogin() {
      	Gpf_Http::setHeader(Gpf_Net_Server_Http_Response::LOCATION, 'login.php');
  		exit;
      }
  }

} //end Gpf_Module

if (!class_exists('Pap_Merchant', false)) {
  class Pap_Merchant extends Gpf_Module {
      /**
       * @var Gpf_Desktop_WindowManager
       */
      private $windowManager;
      const MERCHANT_PANEL_NAME = 'merchants';
      
      public function __construct() {
          parent::__construct('com.qualityunit.pap.MerchantApplication', self::MERCHANT_PANEL_NAME, Pap_Application::ROLETYPE_MERCHANT);
          $this->windowManager = new Gpf_Desktop_WindowManager();
      }
  
      protected function getTitle() {
          return Gpf_Application::getInstance()->getName() . ' - ' . $this->_('Merchant');
      }
  
      protected function onStart() {
          parent::onStart();
          Gpf_Paths::getInstance()->saveServerUrlSettings();
          Gpf_Plugins_Engine::getInstance()->getAvailablePlugins();
      }
  
      protected function initCachedData() {
      	parent::initCachedData();
          $this->renderIconSetRequest();
          $this->renderMerchantSettingsRequest();
          $this->renderPermissionsRequest();
  
          $this->renderMenuRequest();
          $this->renderWindowManagerRequest();
          $this->renderQuickLaunchRequest();
          $this->renderGadgetRequest();
          $this->renderSideBarRequest();
          $this->renderWallpaperRequest();
      }
  
      protected function initStyleSheets() {
          parent::initStyleSheets();
          $this->addStyleSheets(Pap_Module::getStyleSheets());
      }
  
      protected function renderMenuRequest() {
          $menuRequest = new Pap_Merchants_Menu();
          Gpf_Rpc_CachedResponse::add($menuRequest->getNoRpc(), 'Pap_Merchants_Menu', 'get');
      }
  
      protected function setSessionInfo(Gpf_Rpc_Data $sessionInfo) {
          Pap_Module::setSessionInfo($sessionInfo);
      }
  
      protected function renderWallpaperRequest() {
          $wallpaperRequest = new Gpf_Desktop_WallPaper();
          Gpf_Rpc_CachedResponse::add($wallpaperRequest->loadSelectedWallpaperNoRpc(), 'Gpf_Desktop_WallPaper', 'loadSelectedWallpaper');
      }
  
      protected function renderIconSetRequest() {
          $iconSet = new Pap_Common_IconSet();
          Gpf_Rpc_CachedResponse::add($iconSet->getAllIconsNoRpc(), 'Pap_Common_IconSet', 'getAllIcons');
      }
  
      protected function renderMerchantSettingsRequest() {
          $settings = new Pap_Merchants_ApplicationSettings();
          Gpf_Rpc_CachedResponse::add($settings->getSettingsNoRpc(),
              'Pap_Merchants_ApplicationSettings', 'getSettings');
      }
  
      protected function renderWindowManagerRequest() {
          Gpf_Rpc_CachedResponse::add($this->windowManager->getWindowsNoRpc(),
              'Gpf_Desktop_WindowManager', 'getWindows');
      }
  
      protected function renderQuickLaunchRequest() {
          Gpf_Rpc_CachedResponse::add($this->windowManager->getQuickLaunchNoRpc(),
              'Gpf_Desktop_WindowManager', 'getQuickLaunch');
      }
  
      protected function renderGadgetRequest() {
          $gadgetManager = new Gpf_GadgetManager();
          Gpf_Rpc_CachedResponse::add($gadgetManager->getGadgetsNoRpc(),
              'Gpf_GadgetManager', 'getGadgets');
      }
  
      protected function renderSideBarRequest() {
          Gpf_Rpc_CachedResponse::add($this->windowManager->loadSideBarNoRpc(),
              'Gpf_Desktop_WindowManager', 'loadSideBar');
      }
  
      public function getDefaultTheme() {
          $this->initDefaultTheme(Pap_Branding::DEFAULT_MERCHANT_PANEL_THEME);
          return parent::getDefaultTheme();
      }
  
      protected function getCachedTemplateNames() {
          return array_merge(parent::getCachedTemplateNames(),
                             array('main', 'breadcrumbs', 'merchant_tutorial_video'));
      }
  
      public function assignModuleAttributes(Gpf_Templates_Template $template) {
          parent::assignModuleAttributes($template);
          Pap_Module::assignTemplateVariables($template);
          $template->assign(Pap_Settings::PROGRAM_NAME, $this->_localize(Gpf_Settings::get(Pap_Settings::PROGRAM_NAME)));
          $template->assign(Pap_Settings::PROGRAM_LOGO, Gpf_Settings::get(Pap_Settings::PROGRAM_LOGO));
          if (Gpf_Session::getAuthUser()->isLogged()) {
              $template->assignAttributes(Gpf_Session::getAuthUser()->getUserData());
          }
      }
  
  }

} //end Pap_Merchant

if (!class_exists('Gpf_Desktop_WindowManager', false)) {
  class Gpf_Desktop_WindowManager extends Gpf_Object {
      
      const SCREEN_CODE = "screenCode";
      
      const SIDEBAR_WIDTH_SETTING_NAME = "sideBarWidth";
      const SIDEBAR_HIDDEN_SETTING_NAME = "sideBarHidden";
      const SIDEBAR_ONTOP_SETTING_NAME = "sideBarOnTop";
      
      /**
       * @service window write
       * @return Gpf_Rpc_Action
       */
      public function saveWindows(Gpf_Rpc_Params $params) {
          $windows = new Gpf_Data_RecordSet();
          $windows->loadFromArray($params->get('windows'));
          
          //Gpf_Db_Table_Windows::setAllWindowClosed(Gpf_Session::getAuthUser()->getAccountUserId());
          
          foreach ($windows as $windowRecord) {
              $window = new Gpf_Db_Window();
              $window->set('accountuserid', Gpf_Session::getAuthUser()->getAccountUserId());
              $window->fillFromRecord($windowRecord);    
              try {
              	$window->insert();
              } catch (Gpf_DbEngine_DuplicateEntryException $e) {
              	$window->update();
              }
          }
          $action = new Gpf_Rpc_Action(new Gpf_Rpc_Params());
          $action->setInfoMessage($this->_('Windows saved'));
          $action->addOk(); 
          return $action;
      }
      
      /**
       * @service window write
       *
       * @param Gpf_Rpc_Params $params
       * @return Gpf_Rpc_Action
       */
      public function saveAutoRefresh(Gpf_Rpc_Params $params) {
          $window = new Gpf_Db_Window();
          $window->set('autorefreshtime', $params->get('autorefreshtime'));
          $window->set('content', $params->get('content'));
          $window->set('accountuserid', Gpf_Session::getAuthUser()->getAccountUserId());
  
          try {
              $window->insert();
          } catch (Gpf_DbEngine_DuplicateEntryException $e) {
              $window->update();
          }
  
          $action = new Gpf_Rpc_Action(new Gpf_Rpc_Params());
          $action->setInfoMessage($this->_('AutoRefresh saved'));
          $action->addOk(); 
          return $action;
      }
  
      /**
       * @service window read
       */
      public function getWindows(Gpf_Rpc_Params $params) {
          return $this->getWindowsNoRpc();
      }
      
      /**
       * @return Gpf_Data_RecordSet
       */
      public function getWindowsNoRpc() {
          $windowsTable = Gpf_Db_Table_Windows::getInstance();
          return $windowsTable->getWindows(Gpf_Session::getAuthUser()->getAccountUserId());
      }
      
      /**
       * @service quicklaunch write
       * @return Gpf_Rpc_Action
       */
      public function saveQuickLaunch(Gpf_Rpc_Params $params) {
          $items = new Gpf_Data_RecordSet();
          $items->loadFromArray($params->get('items'));
          
          $quickLaunchSetting = "";
          foreach ($items as $item) {
              $quickLaunchSetting .= $item->get(self::SCREEN_CODE) . ",";
          }
          $quickLaunchSetting = rtrim($quickLaunchSetting, ",");
          
          Gpf_Db_Table_UserAttributes::setSetting(Gpf_Settings_Gpf::QUICK_LAUNCH_SETTING_NAME, $quickLaunchSetting);
          
          $action = new Gpf_Rpc_Action(new Gpf_Rpc_Params());
          $action->setInfoMessage($this->_('Quick Launch saved'));
          $action->addOk(); 
          return $action;
      }
      
      /**
       * @service quicklaunch read
       */
      public function getQuickLaunch(Gpf_Rpc_Params $params) {
          return $this->getQuickLaunchNoRpc();
      }
      
      /**
       * @return Gpf_Data_RecordSet
       */
      public function getQuickLaunchNoRpc() {
          try {
              $quickLaunchSetting = Gpf_Db_Table_UserAttributes::getSetting(Gpf_Settings_Gpf::QUICK_LAUNCH_SETTING_NAME);
          } catch (Gpf_DbEngine_NoRowException $e) {
              $quickLaunchSetting = Gpf_Settings::get(Gpf_Settings_Gpf::QUICK_LAUNCH_SETTING_NAME); 
          }
          $items = explode(",", $quickLaunchSetting);
          $result = new Gpf_Data_RecordSet();
          $result->addColumn(self::SCREEN_CODE);
          if (is_array($items)) {
              foreach ($items as $item) {
                  $result->add(array(trim($item)));
              }
          }
          return $result; 
      }
      
     /**
       * @service sidebar write
       * 
       * @return Gpf_Rpc_Action
       */
      public function saveSideBar(Gpf_Rpc_Params $params) {
          $action = new Gpf_Rpc_Action($params);
          $action->setInfoMessage($this->_("Side bar saved"));
          
          Gpf_Db_Table_UserAttributes::setSetting(
              self::SIDEBAR_WIDTH_SETTING_NAME,
              $action->getParam("width"));
              
          Gpf_Db_Table_UserAttributes::setSetting(
              self::SIDEBAR_HIDDEN_SETTING_NAME,
              $action->getParam("hidden"));
              
          Gpf_Db_Table_UserAttributes::setSetting(
              self::SIDEBAR_ONTOP_SETTING_NAME,
              $action->getParam("onTop"));    
              
          $action->addOk();
          return $action; 
      }
      
      /**
       * @service sidebar read
       * 
       * @return Gpf_Rpc_Form
       */
      public function loadSideBar(Gpf_Rpc_Params $params) {
          return $this->loadSideBarNoRpc();
      }   
         
      /**
       * @return Gpf_Rpc_Form
       */
      public function loadSideBarNoRpc() {
          $response = new Gpf_Data_RecordSet();
          $response->addColumn("name");
          $response->addColumn("value");
          
          $record = $response->createRecord();
          $record->set("name", "width");
          $sideBarWidthValue = $this->getUserAttributeWithDefaultValue(
              self::SIDEBAR_WIDTH_SETTING_NAME, "200");
          if ($sideBarWidthValue < 0) {
              $sideBarWidthValue = 200;
          }
          $record->set("value", $sideBarWidthValue);
          $response->add($record);
          
          $record = $response->createRecord();
          $record->set("name", "hidden");
          $record->set("value", $this->getUserAttributeWithDefaultValue(
              self::SIDEBAR_HIDDEN_SETTING_NAME, "N"));
              
          $response->add($record);
          $record = $response->createRecord();
          $record->set("name", "onTop");
          $record->set("value", $this->getUserAttributeWithDefaultValue(
              self::SIDEBAR_ONTOP_SETTING_NAME, "N"));
          $response->add($record);
          
          return $response; 
      }
      
      public function getUserAttributeWithDefaultValue($attributeName, $defaultValue) {
          try {
              $value = Gpf_Db_Table_UserAttributes::getSetting($attributeName);
          } catch (Gpf_DbEngine_NoRowException $e) {
              $value = $defaultValue;
              Gpf_Db_Table_UserAttributes::setSetting($attributeName, $defaultValue);
          }
          return $value;
      }
  }
} //end Gpf_Desktop_WindowManager

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

if (!class_exists('Gpf_Db_Language', false)) {
  class Gpf_Db_Language extends Gpf_DbEngine_Row {
  
      public function __construct(){
          parent::__construct();
      }
  
      protected function init() {
          $this->setTable(Gpf_Db_Table_Languages::getInstance());
          parent::init();
      }
  
      public function insert() {
          $this->set('imported', Gpf_DbEngine_Database::getDateString());
          $this->setId($this->generateId());
          $this->checkIsDefaultStatus();
          return parent::insert();
      }
  
      public function delete() {
          $this->load();
          if ($this->isDefault()) {
              throw new Gpf_Exception($this->_("Default language can't be deleted"));
          }
  
          $returnValue = parent::delete();
  
          $this->deleteLanguageFilesFromAccount();
  
          return $returnValue;
      }
  
      /**
       * Delete csv file from account directory
       */
      private function deleteLanguageFilesFromAccount() {
          //delete csv file from account
          $fileName = Gpf_Lang_CsvLanguage::getAccountCsvFileName($this->getCode());
          $file = new Gpf_Io_File($fileName);
          if ($file->isExists()) {
              $file->delete();
          }
  
          //TODO delete also cache language files from account
      }
  
      private function checkIsDefaultStatus() {
          if (!$this->isActive() && $this->isDefault()) {
              throw new Gpf_Exception($this->_('Default language has to be active !'));
          }
  
          try {
              $defLang = Gpf_Db_Table_Languages::getInstance()->getDefaultLanguage();
              if (($this->getCode() == $defLang->getCode() || !strlen($defLang->getCode())) && $this->isDefault() === false) {
                  $this->setIsDefault(true);
              }
          } catch (Gpf_DbEngine_NoRowException $e) {
              $this->setIsDefault(true);
          }
  
          if ($this->isDefault()) {
              Gpf_Db_Table_Languages::getInstance()->unsetDefaultLanguage($this->getId());
          }
      }
  
      public function update($updateColumns = array()) {
          $this->checkIsDefaultStatus();
          parent::update($updateColumns);
      }
  
      public function generateId() {
          return $this->getAccountId() . '_' . $this->getCode();
      }
  
      public function getId() {
          return $this->get(Gpf_Db_Table_Languages::ID);
      }
  
      public function setId($id) {
          $this->set(Gpf_Db_Table_Languages::ID, $id);
      }
  
      public function getCode() {
          return $this->get(Gpf_Db_Table_Languages::CODE);
      }
  
      public function setCode($code) {
          $this->set(Gpf_Db_Table_Languages::CODE, $code);
      }
  
      public function getName() {
          return $this->get(Gpf_Db_Table_Languages::NAME);
      }
  
      public function setName($name) {
          $this->set(Gpf_Db_Table_Languages::NAME, $name);
      }
  
      public function getEnglishName() {
          return $this->get(Gpf_Db_Table_Languages::ENGLISH_NAME);
      }
  
      public function setEnglishName($name) {
          $this->set(Gpf_Db_Table_Languages::ENGLISH_NAME, $name);
      }
  
      public function isActive() {
          return $this->get(Gpf_Db_Table_Languages::ACTIVE) == Gpf::YES;
      }
  
      public function setActive($isActive) {
          if ($isActive == Gpf::YES || $isActive === true) {
              $this->set(Gpf_Db_Table_Languages::ACTIVE, Gpf::YES);
          } else {
              $this->set(Gpf_Db_Table_Languages::ACTIVE, Gpf::NO);
          }
      }
  
      public function getAuthor() {
          return $this->get(Gpf_Db_Table_Languages::AUTHOR);
      }
  
      public function setAuthor($author) {
          $this->set(Gpf_Db_Table_Languages::AUTHOR, $author);
      }
  
      public function getVersion() {
          return $this->get(Gpf_Db_Table_Languages::VERSION);
      }
  
      public function setVersion($version) {
          $this->set(Gpf_Db_Table_Languages::VERSION, $version);
      }
  
      public function getImported() {
          return $this->get(Gpf_Db_Table_Languages::IMPORTED);
      }
  
      public function setImported($imported) {
          $this->set(Gpf_Db_Table_Languages::IMPORTED, $imported);
      }
  
      public function getAccountId() {
          return $this->get(Gpf_Db_Table_Accounts::ID);
      }
  
      public function setAccountId($accountId) {
          $this->set(Gpf_Db_Table_Accounts::ID, $accountId);
      }
  
      public function getDateFormat() {
          return $this->get(Gpf_Db_Table_Languages::DATE_FORMAT);
      }
  
      public function setDateFormat($format) {
          $this->set(Gpf_Db_Table_Languages::DATE_FORMAT, $format);
      }
  
      public function getTimeFormat() {
          return $this->get(Gpf_Db_Table_Languages::TIME_FORMAT);
      }
  
      public function setTimeFormat($format) {
          $this->set(Gpf_Db_Table_Languages::TIME_FORMAT, $format);
      }
      
  	public function getThousandsSeparator() {
          return $this->get(Gpf_Db_Table_Languages::THOUSANDS_SEPARATOR);
      }
      
  	public function setThousandsSeparator($separator) {
          $this->set(Gpf_Db_Table_Languages::THOUSANDS_SEPARATOR, $separator);
      }
      
  	public function getDecimalSeparator() {
          return $this->get(Gpf_Db_Table_Languages::DECIMAL_SEPARATOR);
      }
  
  	public function setDecimalSeparator($separator) {
          $this->set(Gpf_Db_Table_Languages::DECIMAL_SEPARATOR, $separator);
      }
      
      public function getTranslatedPercentage() {
          return $this->get(Gpf_Db_Table_Languages::TRANSLATED_PERCENTAGE);
      }
  
      public function setTranslatedPercentage($percent) {
          $this->set(Gpf_Db_Table_Languages::TRANSLATED_PERCENTAGE, $percent);
      }
  
      public function isDefault() {
          return $this->get(Gpf_Db_Table_Languages::IS_DEFAULT) == Gpf::YES;
      }
  
      public function setIsDefault($isDefault) {
          if ($isDefault == Gpf::YES || $isDefault === true) {
              $this->set(Gpf_Db_Table_Languages::IS_DEFAULT, Gpf::YES);
          } else {
              $this->set(Gpf_Db_Table_Languages::IS_DEFAULT, Gpf::NO);
          }
      }
  }
} //end Gpf_Db_Language

if (!class_exists('Gpf_Rpc_Array', false)) {
  class Gpf_Rpc_Array extends Gpf_Object implements Gpf_Rpc_Serializable, IteratorAggregate {
  
  	private $array;
  
  	function __construct(array $array = null){
  		if($array === null){
  			$this->array = array();
  		}else{
  			$this->array = $array;
  		}
  	}
  
  	public function add($response) {
  		if(is_scalar($response) || $response instanceof Gpf_Rpc_Serializable) {
  			$this->array[] = $response;
  			return;
  		}
  		throw new Gpf_Exception("Value of type " . gettype($response) . " is not scalar or Gpf_Rpc_Serializable");
  	}
  
  	public function toObject() {
  		$array = array();
  		foreach ($this->array as $response) {
  			if($response instanceof Gpf_Rpc_Serializable) {
  				$array[] = $response->toObject();
  			} else {
  				$array[] = $response;
  			}
  		}
  		return $array;
  	}
  
  	public function toText() {
  		return var_dump($this->array);
  	}
  
  	public function getCount() {
  		return count($this->array);
  	}
  
  	public function get($index) {
  		return $this->array[$index];
  	}
  
  	/**
  	 *
  	 * @return ArrayIterator
  	 */
  	public function getIterator() {
  		return new ArrayIterator($this->array);
  	}
  }

} //end Gpf_Rpc_Array

if (!class_exists('Gpf_Db_Table_LoginsHistory', false)) {
  class Gpf_Db_Table_LoginsHistory extends Gpf_DbEngine_Table {
      const ID = 'loginid';
      const LOGIN = 'login';
      const LOGOUT = 'logout';
      const LAST_REQUEST = 'lastrequest';
      const IP = 'ip';
      const ACCOUNTUSERID = 'accountuserid';
      
      private static $instance;
      
      public static function getInstance() {
          if(self::$instance === null) {
              self::$instance = new self;
          }
          return self::$instance;
      }
      
      protected function initName() {
          $this->setName('g_logins');
      }
      
      public static function getName() {
          return self::getInstance()->name();
      }
      
      protected function initColumns() {
          $this->createPrimaryColumn(self::ID, 'int', 0, true);
          $this->createColumn(self::ACCOUNTUSERID, 'char', 8);
          $this->createColumn(self::LOGIN, 'datetime');
          $this->createColumn(self::LOGOUT, 'datetime');
          $this->createColumn(self::LAST_REQUEST, 'datetime');
          $this->createColumn(self::IP, 'char', 39);
      }
  }
  

} //end Gpf_Db_Table_LoginsHistory

if (!class_exists('Gpf_ApiModule', false)) {
  class Gpf_ApiModule extends Gpf_ModuleBase {
      public function __construct() {
          parent::__construct('', '');
      }
      
      public function getDefaultTheme() {
          return '';
      }
  }
} //end Gpf_ApiModule

if (!class_exists('Gpf_Privileges', false)) {
  abstract class Gpf_Privileges extends Gpf_Object {
      const P_ALL = "*";
      
      private $defaultPrivilieges;
      /**
       * @var array
       */
  	protected $objectRelation;
      
      public function __construct() {
          $this->defaultPrivilieges = array();
          $this->initDefaultPrivileges();
          Gpf_Plugins_Engine::extensionPoint('Core.initPrivileges', $this);
      }
  
      protected abstract function initDefaultPrivileges();
  
      public function addPrivilege($object, $privilege) {
          if(!array_key_exists($object, $this->defaultPrivilieges)) {
              $privileges = array();
              $privileges[$privilege] = $privilege;
              $this->defaultPrivilieges[$object] = $privileges;
              return;
          }
          $privileges = $this->defaultPrivilieges[$object];
          if(!array_key_exists($privilege, $privileges)) {
              $this->defaultPrivilieges[$object][$privilege] = $privilege;
          }
      }
  
      public function getDefaultPrivileges() {
          return $this->defaultPrivilieges;
      }    
  
      private static function loadPrivilegesFromDefault($privilegeList) {
          $privilegesResult = array();
          foreach ($privilegeList as $object => $privileges) {
              foreach ($privileges as $privilege) {
                  $privilegesResult[$object][$privilege] = true;
              }
          }
          return $privilegesResult;
      }    
      
      /**
       * Get array of role privileges
       *
       * @param string $roleId
       * @return array
       */
      public static function loadPrivileges($roleId) {
          try {
              return self::loadPrivilegesFromDefault(
              Gpf_Application::getInstance()->getRoleDefaultPrivileges($roleId));
          } catch (Gpf_Exception $e) {
              $privilegesValues = array();
              $privilegesTable = Gpf_Db_Table_RolePrivileges::getInstance();
              $privileges = $privilegesTable->getAllPrivileges($roleId);
              foreach ($privileges as $privilege) {
                  $privilegesValues[$privilege->get('object')][$privilege->get('privilege')] = true;
              }
              return $privilegesValues;
          }
          
      }
      
      /**
       * @return array
       */
      public function getObjectToTypeRelation() {
      	if (is_null($this->objectRelation)) {
  			$this->objectRelation = $this->initObjectRelation();
  		}
  		return $this->objectRelation;
      }
      
      //All after start tag is autogenerated !
      //Don't delete following line !!!!!
      //<PRIVILEGES_START>
  
  	// Privilege types
  	const P_READ = "read";
  	const P_WRITE = "write";
  	const P_LOGOUT = "logout";
  	const P_DELETE = "delete";
  	const P_EXPORT = "export";
  	const P_IMPORT = "import";
  	const P_ADD = "add";
  	const P_EXECUTE = "execute";
  
  	// Privilege objects
  	const AUTHENTICATION = "authentication"; // P_LOGOUT
  	const COUNTRY = "country"; // P_READ, P_WRITE
  	const CURRENCY = "currency"; // P_READ
  	const DB_FILE = "db_file"; // P_READ, P_WRITE
  	const EMAIL_SETTING = "email_setting"; // P_READ, P_WRITE
  	const EXPORT = "export"; // P_READ
  	const EXPORT_FILE = "export_file"; // P_DELETE, P_EXPORT, P_READ
  	const FEATURE = "feature"; // P_READ
  	const FILTER = "filter"; // P_DELETE, P_READ, P_WRITE
  	const FORM_FIELD = "form_field"; // P_ADD, P_DELETE, P_EXPORT, P_READ, P_WRITE
  	const GADGET = "gadget"; // P_ADD, P_DELETE, P_EXPORT, P_READ, P_WRITE
  	const GEOIP = "geoip"; // P_READ
  	const GOOGLEMAPS = "googlemaps"; // P_READ, P_WRITE
  	const GRID_VIEW = "grid_view"; // P_ADD, P_DELETE, P_WRITE
  	const IMPORT = "import"; // P_READ
  	const IMPORT_EXPORT = "import_export"; // P_EXPORT, P_IMPORT, P_READ
  	const LANGUAGE = "language"; // P_ADD, P_DELETE, P_EXPORT, P_IMPORT, P_READ, P_WRITE
  	const LOG = "log"; // P_ADD, P_DELETE, P_EXPORT, P_READ, P_WRITE
  	const MAIL_OUTBOX = "mail_outbox"; // P_ADD, P_DELETE, P_EXPORT, P_READ, P_WRITE
  	const MAIL_TEMPLATE = "mail_template"; // P_EXPORT, P_READ, P_WRITE
  	const MASS_EMAIL = "mass_email"; // P_WRITE
  	const MENU = "menu"; // P_READ
  	const MYPROFILE = "myprofile"; // P_READ, P_WRITE
  	const NEWSLETTER = "newsletter"; // P_ADD, P_DELETE, P_EXPORT, P_READ, P_WRITE
  	const ONLINE_USER = "online_user"; // P_DELETE, P_EXPORT, P_READ
  	const PASSWORD_CONSTRAINTS = "password_constraints"; // P_READ, P_WRITE
  	const PLUGIN = "plugin"; // P_READ, P_WRITE
  	const PROXY_SETTING = "proxy_setting"; // P_ADD, P_READ, P_WRITE
  	const QUICKLAUNCH = "quicklaunch"; // P_READ, P_WRITE
  	const RECURRENCE = "recurrence"; // P_READ
  	const REGIONAL_SETTINGS = "regional_settings"; // P_READ, P_WRITE
  	const ROLE = "role"; // P_ADD, P_DELETE, P_EXPORT, P_READ, P_WRITE
  	const SIDEBAR = "sidebar"; // P_READ, P_WRITE
  	const TASKS = "tasks"; // P_DELETE, P_EXECUTE, P_EXPORT, P_READ
  	const TEMPLATE = "template"; // P_READ, P_WRITE
  	const THEME = "theme"; // P_READ, P_WRITE
  	const UPLOADED_FILE = "uploaded_file"; // P_DELETE, P_READ
  	const USER = "user"; // P_READ
  	const WALLPAPER = "wallpaper"; // P_ADD, P_DELETE, P_READ, P_WRITE
  	const WINDOW = "window"; // P_READ, P_WRITE
  	
  
  	protected function initObjectRelation() {
  		return array(
  		self::AUTHENTICATION=>array(self::P_LOGOUT),
  		self::COUNTRY=>array(self::P_READ, self::P_WRITE),
  		self::CURRENCY=>array(self::P_READ),
  		self::DB_FILE=>array(self::P_READ, self::P_WRITE),
  		self::EMAIL_SETTING=>array(self::P_READ, self::P_WRITE),
  		self::EXPORT=>array(self::P_READ),
  		self::EXPORT_FILE=>array(self::P_DELETE, self::P_EXPORT, self::P_READ),
  		self::FEATURE=>array(self::P_READ),
  		self::FILTER=>array(self::P_DELETE, self::P_READ, self::P_WRITE),
  		self::FORM_FIELD=>array(self::P_ADD, self::P_DELETE, self::P_EXPORT, self::P_READ, self::P_WRITE),
  		self::GADGET=>array(self::P_ADD, self::P_DELETE, self::P_EXPORT, self::P_READ, self::P_WRITE),
  		self::GEOIP=>array(self::P_READ),
  		self::GOOGLEMAPS=>array(self::P_READ, self::P_WRITE),
  		self::GRID_VIEW=>array(self::P_ADD, self::P_DELETE, self::P_WRITE),
  		self::IMPORT=>array(self::P_READ),
  		self::IMPORT_EXPORT=>array(self::P_EXPORT, self::P_IMPORT, self::P_READ),
  		self::LANGUAGE=>array(self::P_ADD, self::P_DELETE, self::P_EXPORT, self::P_IMPORT, self::P_READ, self::P_WRITE),
  		self::LOG=>array(self::P_ADD, self::P_DELETE, self::P_EXPORT, self::P_READ, self::P_WRITE),
  		self::MAIL_OUTBOX=>array(self::P_ADD, self::P_DELETE, self::P_EXPORT, self::P_READ, self::P_WRITE),
  		self::MAIL_TEMPLATE=>array(self::P_EXPORT, self::P_READ, self::P_WRITE),
  		self::MASS_EMAIL=>array(self::P_WRITE),
  		self::MENU=>array(self::P_READ),
  		self::MYPROFILE=>array(self::P_READ, self::P_WRITE),
  		self::NEWSLETTER=>array(self::P_ADD, self::P_DELETE, self::P_EXPORT, self::P_READ, self::P_WRITE),
  		self::ONLINE_USER=>array(self::P_DELETE, self::P_EXPORT, self::P_READ),
  		self::PASSWORD_CONSTRAINTS=>array(self::P_READ, self::P_WRITE),
  		self::PLUGIN=>array(self::P_READ, self::P_WRITE),
  		self::PROXY_SETTING=>array(self::P_ADD, self::P_READ, self::P_WRITE),
  		self::QUICKLAUNCH=>array(self::P_READ, self::P_WRITE),
  		self::RECURRENCE=>array(self::P_READ),
  		self::REGIONAL_SETTINGS=>array(self::P_READ, self::P_WRITE),
  		self::ROLE=>array(self::P_ADD, self::P_DELETE, self::P_EXPORT, self::P_READ, self::P_WRITE),
  		self::SIDEBAR=>array(self::P_READ, self::P_WRITE),
  		self::TASKS=>array(self::P_DELETE, self::P_EXECUTE, self::P_EXPORT, self::P_READ),
  		self::TEMPLATE=>array(self::P_READ, self::P_WRITE),
  		self::THEME=>array(self::P_READ, self::P_WRITE),
  		self::UPLOADED_FILE=>array(self::P_DELETE, self::P_READ),
  		self::USER=>array(self::P_READ),
  		self::WALLPAPER=>array(self::P_ADD, self::P_DELETE, self::P_READ, self::P_WRITE),
  		self::WINDOW=>array(self::P_READ, self::P_WRITE)
  		);
	}
  
  }

} //end Gpf_Privileges

if (!class_exists('Gpf_Rpc_Form', false)) {
  class Gpf_Rpc_Form extends Gpf_Object implements Gpf_Rpc_Serializable, IteratorAggregate {
      const FIELD_NAME  = "name";
      const FIELD_VALUE = "value";
      const FIELD_ERROR = "error";
      const FIELD_VALUES = "values";
  
      private $isError = false;
      private $errorMessage = "";
      private $infoMessage = "";
      private $status;
      /**
       * @var Gpf_Data_IndexedRecordSet
       */
      private $fields;
      /**
       * @var Gpf_Rpc_Form_Validator_FormValidatorCollection
       */
      private $validators;
  
      public function __construct(Gpf_Rpc_Params $params = null) {
          $this->fields = new Gpf_Data_IndexedRecordSet(self::FIELD_NAME);
  
          $header = new Gpf_Data_RecordHeader();
          $header->add(self::FIELD_NAME);
          $header->add(self::FIELD_VALUE);
          $header->add(self::FIELD_VALUES);
          $header->add(self::FIELD_ERROR);
          $this->fields->setHeader($header);
          
          $this->validator = new Gpf_Rpc_Form_Validator_FormValidatorCollection($this);
          
          if($params) {
              $this->loadFieldsFromArray($params->get("fields"));
          }
      }
  
      /**
       * @param $validator
       * @param $fieldName
       * @param $fieldLabel
       */
      public function addValidator(Gpf_Rpc_Form_Validator_Validator $validator, $fieldName, $fieldLabel = null) {
          $this->validator->addValidator($validator, $fieldName, $fieldLabel);
      }
      
      /**
       * @return boolean
       */
      public function validate() {
          return $this->validator->validate();
      }
      
      public function loadFieldsFromArray($fields) {
          for ($i = 1; $i < count($fields); $i++) {
              $field = $fields[$i];
              $this->fields->add($field);
          }
      }
      
      /**
       *
       * @return ArrayIterator
       */
      public function getIterator() {
          return $this->fields->getIterator();
      }
      
      public function addField($name, $value) {
          $record = $this->fields->createRecord($name);
          $record->set(self::FIELD_VALUE, $value);
      }
      
      public function setField($name, $value, $values = null, $error = "") {
          $record = $this->fields->createRecord($name);
          $record->set(self::FIELD_VALUE, $value);
          $record->set(self::FIELD_VALUES, $values);
          $record->set(self::FIELD_ERROR, $error);
      }
      
      public function setFieldError($name, $error) {
          $this->isError = true;
          $record = $this->fields->getRecord($name);
          $record->set(self::FIELD_ERROR, $error);
      }
      
      public function getFieldValue($name) {
          $record = $this->fields->getRecord($name);
          return $record->get(self::FIELD_VALUE);
      }
      
      public function getFieldError($name) {
          $record = $this->fields->getRecord($name);
          return $record->get(self::FIELD_ERROR);
      }
      
      public function existsField($name) {
          return $this->fields->existsRecord($name);
      }
       
      public function load(Gpf_Data_Row $row) {
          foreach($row as $columnName => $columnValue) {
              $this->setField($columnName, $row->get($columnName));
          }
      }
  
      /**
       * @return Gpf_Data_IndexedRecordSet
       */
      public function getFields() {
          return $this->fields;
      }
      
      public function fill(Gpf_Data_Row $row) {
          foreach ($this->fields as $field) {
              try {
                  $row->set($field->get(self::FIELD_NAME), $field->get(self::FIELD_VALUE));
              } catch (Exception $e) {
              }
          }
      }
      
      public function toObject() {
          $response = new stdClass();
          $response->fields = $this->fields->toObject();
          if ($this->isSuccessful()) {
              $response->success = Gpf::YES;
              $response->message = $this->infoMessage;
          } else {
              $response->success = "N";
              $response->message = $this->errorMessage;
          }
          return $response;
      }
      
      public function loadFromObject(stdClass $object) {
          if ($object->success == Gpf::YES) {
          	$this->setInfoMessage($object->message);
          } else {
          	$this->setErrorMessage($object->message);
          }
          
          $this->fields = new Gpf_Data_IndexedRecordSet(self::FIELD_NAME);
          $this->fields->loadFromObject($object->fields);
      }
      
      public function toText() {
          return var_dump($this->toObject());
      }
  
      public function setErrorMessage($message) {
          $this->isError = true;
          $this->errorMessage = $message;
      }
      
      public function getErrorMessage() {
          if ($this->isError) {
              return $this->errorMessage;
          }
          return "";
      }
      
      public function setInfoMessage($message) {
          $this->infoMessage = $message;
      }
      
      public function setSuccessful() {
          $this->isError = false;
      }
      
      public function getInfoMessage() {
          if ($this->isError) {
              return "";
          }
          return $this->infoMessage;
      }
      
      
      /**
       * @return boolean
       */
      public function isSuccessful() {
          return !$this->isError;
      }
      
      /**
       * @return boolean
       */
      public function isError() {
          return $this->isError;
      }
  }
  

} //end Gpf_Rpc_Form
/*
VERSION
39d361a6a65ae3b78f3091926ceb9773
*/
?>
