<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Smarty.class.php 27406 2010-02-26 12:14:26Z jsimon $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

require_once 'Smarty.class.php';

/**
 * @package GwtPhpFramework
 * Handling of templates just on Server
 */
class Gpf_Templates_Smarty extends Smarty {
    const COMPILED_TEMPLATES_DIR = 'templates/';
    const INSTALL_STREAM = 'sinstall';
    private $name;
    private $panelName;
    private $templateSource;
    private static $cacheCompileDir;

    public function __construct($name, $panelName = '') {
        Smarty::Smarty();
        $this->name = $name;
        $this->panelName = $panelName;
        $this->default_resource_type = 'template';
        $this->security = true;
        Gpf_Plugins_Engine::extensionPoint('Core.initSmarty', $this);
    }

    public function setDelimiter($left, $right) {
        $this->left_delimiter = $left;
        $this->right_delimiter = $right;
    }

    public function setTemplateSource($templateSource) {
        if (strpos($this->name, "text://") === false) {
            throw new Gpf_Exception("setTemplateSource can be used only if text fetching is enabled");
        }
        $this->templateSource = $templateSource;
    }

    public function getTemplateSource() {
        if (strpos($this->name, "text://") === false) {
            throw new Gpf_Exception("getTemplateSource can be used only if text fetching is enabled");
        }
        return $this->templateSource;
    }

    public function getTemplateFromFile($templateName) {
        $template = new Gpf_Templates_Template($templateName, $this->panelName);
        return $template->getTemplateSource();
    }

    public function getTemplateTimestamp($templateName) {
        $template = new Gpf_Templates_Template($templateName, $this->panelName);
        return $template->getTimestamp();
    }

    public function getText() {
        return $this->fetch($this->name);
    }
    
    public function checkSyntax () {
    	$this->fetch($this->name, null, null, false, true);    	
    }

    public static function getCompileDir() {
        if(self::$cacheCompileDir === null) {
            self::$cacheCompileDir = rtrim(self::computeCompileDir(), '/\\') . '/';
        }
        return self::$cacheCompileDir;
    }

    private static function computeCompileDir() {
        if(Gpf_Paths::getInstance()->isInstallModeActive()) {
            return self::resolveCompileDirInInstallMode();
        }

        return Gpf_Paths::getInstance()->getCacheAccountDirectory().self::COMPILED_TEMPLATES_DIR;
    }

   public static function checkCompileDirRequirementsInInstallMode() {
        $installDir = self::resolveCompileDirInInstallMode();
        if ($installDir == self::INSTALL_STREAM. '://:') {
            ob_start();
            phpInfo();
            $info = ob_get_contents();
            ob_end_clean();
            if (strstr($info, 'Suhosin') !== false) {
                die('When Suhosin extension is loaded, system temporary directory needs to be available');
            }
        }
    }


    private static function resolveCompileDirInInstallMode() {
        $tempDir = new Gpf_Io_File(str_replace('\\', '/', self::getSysTempDir()));
        if($tempDir->isDirectory() && $tempDir->isWritable()) {
            return $tempDir->getFileName();
        }
        return self::INSTALL_STREAM . '://:';
    }

    private static function getSysTempDir() {
        if (Gpf_Php::isFunctionEnabled('sys_get_temp_dir') ) {
            return rtrim(rtrim(sys_get_temp_dir(), '/'), '\\') . '/';
        }
        if ( !empty($_ENV['TMP']) ) {
            return realpath( $_ENV['TMP'] );
        } else if ( !empty($_ENV['TMPDIR']) ) {
            return realpath( $_ENV['TMPDIR'] );
        } else if ( !empty($_ENV['TEMP']) ) {
            return realpath( $_ENV['TEMP'] );
        } else {
            $temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
            if ( $temp_file ) {
                $temp_dir = realpath( dirname($temp_file) );
                unlink( $temp_file );
                return $temp_dir;
            } else {
                return FALSE;
            }
        }
    }

    public function localize($message) {
        return Gpf_Lang_Dictionary::getInstance(Gpf_Session::getInstance()->getAuthUser()->getLanguage())->get($message);
    }

    public function renderServerWidget($className) {
        $widgetContent = '';
        try {
            $widget = $this->createWidget($className);
            return $widget->render();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function createWidget($className) {
        if(Gpf::existsClass($className)) {
            $class = new ReflectionClass($className);
            $widget = $class->newInstance(Gpf_Ui_Controller_Main::getController());
            return $widget;
        }
        throw new Gpf_Templates_NoWidget();
    }
}

class Gpf_Templates_Smarty_File {
    private $path;
    private $data = '';
    private $position = 0;

    public function __construct($path) {
        $this->path = $path;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function addData($data) {
        $this->data .= $data;
    }

    public function setPosition($position) {
        $this->position = $position;
    }

    public function getPosition() {
        return $this->position;
    }

    public function isEof() {
        return $this->position >= strlen($this->data);
    }

    public function read($count) {
        $data = substr($this->data, $this->position, $count);
        $this->position += strlen($data);
        return $data;
    }
}

class Gpf_Templates_Smarty_InstallStream {
    private $file;
    private static $files = array();

    public function stream_open($path, $mode, $options, &$opened_path) {
        $this->file = null;
        if(array_key_exists($path, self::$files)) {
            $file = self::$files[$path];
            $file->setPosition(0);
        } else if ($mode[0] == 'r') {
            return false;
        } else {
            $file = new Gpf_Templates_Smarty_File($path);
            self::$files[$path] = $file;
        }

        if($mode[0] == 'w') {
            $file->setData('');
        }

        $this->file = $file;
        return true;
    }

    public function stream_close() {

    }

    public function stream_read($count) {
        return $this->file->read($count);
    }

    public function stream_tell() {
        return $this->file->getPosition();
    }

    public function stream_eof() {
        return $this->file->isEof();
    }

    public function stream_seek($offset, $whence) {
        $this->file->setPosition($offset);
        return true;
    }

    public function stream_flush() {
        return true;
    }

    public function stream_write($data) {
        $this->file->addData($data);
        return strlen($data);
    }
    
    public function stream_stat() {
      return array();
    }

    public function url_stat($path, $flag) {
        if(array_key_exists($path, self::$files)) {
            return array();
        }
        return false;
    }

    public function mkdir($path, $mode, $options) {
        return true;
    }
}
stream_wrapper_register(Gpf_Templates_Smarty::INSTALL_STREAM, 'Gpf_Templates_Smarty_InstallStream');

?>
