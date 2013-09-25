<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Template.class.php 34162 2011-08-08 08:56:00Z mkendera $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */


/**
 * @package GwtPhpFramework
 * Handling of templates just on Server
 */
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
            if(strripos($name, '%%'.$this->encodeFileName($fileName))){
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
?>
