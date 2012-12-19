<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TemplateForm.class.php 32186 2011-04-19 11:57:07Z iivanco $
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
class Gpf_Templates_TemplateForm extends Gpf_Object {

    /**
     *
     * @service template read
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $templateName = $params->get("templatename");
        $panelName = '';
        if ($params->exists("panelname")) {
            $panelName = $params->get("panelname");
        }
        $theme = trim(Gpf_Paths::DEFAULT_THEME, '/');
        if ($params->exists("theme") && $params->get("theme") != null && $params->get("theme") != "") {
            $theme = $params->get("theme");
        }
        $template = new Gpf_Templates_Template($this->fixTemplateName($templateName), $panelName, Gpf_Templates_Template::FETCH_FILE, $theme);

        $form->setField('templatename', $templateName);
        $form->setField('panelname', $panelName);
        $form->setField('theme', $theme);
        $form->setField('templatecontent', $template->getTemplateSource());

        return $form;
    }

    /**
     *
     * @service template write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $templateName = $form->getFieldValue("templatename");
        $templateContent = $form->getFieldValue('templatecontent');
        try {
            $panelName = $form->getFieldValue('panelname');
        } catch (Gpf_Data_RecordSetNoRowException $e) {
            $panelName = '';
        }
        try {
            $theme = $form->getFieldValue('theme');
        } catch (Gpf_Data_RecordSetNoRowException $e) {
            $theme = trim(Gpf_Paths::DEFAULT_THEME, '/');
        }

        $template = new Gpf_Templates_Template($this->fixTemplateName($templateName), $panelName, Gpf_Templates_Template::FETCH_FILE, $theme);
        try {
            $template->saveTemplateToFile($templateContent);
        } catch (Gpf_Exception $e) {
            $form->setErrorMessage($this->_("Unable to save template '%s'", $templateName ." EXCEPTION: ". $e));
        }

        $form->setInfoMessage($this->_("Template '%s' saved", $templateName));
        return $form;
    }

    private function fixTemplateName($templateName) {
        $pathInfo = pathinfo($templateName);
        if (array_key_exists('extension', $pathInfo)) {
            return $templateName;
        }
        return $templateName.'.tpl';
    }

    /**
     * @service template read
     * @param $fields
     * @return Gpf_Data_RecordSet
     */
    public function getThemesForFile(Gpf_Rpc_Params $params) {
        $templateName = $this->fixTemplateName($params->get("templatename"));
        $panelName = '';
        if ($params->exists("panelname")) {
            $panelName = $params->get("panelname");
        }
        $themeManager = new Gpf_Desktop_ThemeManager();
        $themes = $themeManager->getThemesNoRpc($panelName);
        $themes->addColumn("filename");
        foreach ($themes as $theme) {
            $paths = Gpf_Paths::getInstance()->clonePaths($theme->get(Gpf_Desktop_Theme::ID));
            $templatePath = $paths->getTemplatePath($templateName, $panelName);
            if (strpos($templatePath, Gpf_Paths::DEFAULT_THEME) === false) {
                $theme->set("filename", $templatePath);
            }
        }
        $commonTemplates = $themes->createRecord();
        $commonTemplates->set(Gpf_Desktop_Theme::ID, trim(Gpf_Paths::DEFAULT_THEME, '/'));
        $commonTemplates->set(Gpf_Desktop_Theme::NAME, $this->_("Common templates"));
        $paths = Gpf_Paths::getInstance()->clonePaths(trim(Gpf_Paths::DEFAULT_THEME, '/'));
        $commonTemplates->set("filename", $paths->getTemplatePath($templateName, $panelName));
        $themes->addRecord($commonTemplates);

        return $themes;
    }

    /**
     * @service template write
     * @param $fields
     * @return Gpf_Rpc_Action
     */
    public function deleteFile(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $templateName = $this->fixTemplateName($action->getParam("templatename"));
        $panelName = '';
        if ($action->existsParam("panelname")) {
            $panelName = $action->getParam("panelname");
        }
        if ($action->existsParam("theme") && $action->getParam("theme") != null
        && $action->getParam("theme") != "") {
            $theme = $action->getParam("theme");
            if ($theme == trim(Gpf_Paths::DEFAULT_THEME, '/')) {
                $action->setErrorMessage($this->_("Common template can not be deleted"));
                $action->addError();
                return $action;
            }
        } else {
            throw new Gpf_Exception("Theme not set");
        }

        $paths = Gpf_Paths::getInstance()->clonePaths($theme);
        $templateFile = new Gpf_Io_File($paths->getTemplatePath($templateName, $panelName));
        if ($templateFile->delete()) {
            $action->setInfoMessage($this->_("File deleted"));
            $action->addOk();
            return $action;
        }

        $action->setErrorMessage($this->_("File can not be deleted"));
        $action->addError();
         
        return $action;
    }

    /**
     * @service template write
     * @param $fields
     * @return Gpf_Rpc_Action
     */
    public function createFile(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $templateName = $this->fixTemplateName($action->getParam("templatename"));
        $panelName = '';
        if ($params->exists("panelname")) {
            $panelName = $action->getParam("panelname");
        }
        if ($params->exists("theme") && $action->getParam("theme") != null && $action->getParam("theme") != "") {
            $theme = $action->getParam("theme");
        } else {
            throw new Gpf_Exception("Theme not set");
        }

        $commonTemplate = new Gpf_Templates_Template($templateName, $panelName, Gpf_Templates_Template::FETCH_FILE, trim(Gpf_Paths::DEFAULT_THEME, '/'));
        $commonTemplate->getTemplateSource();

        $paths = Gpf_Paths::getInstance()->clonePaths($theme);
        $templatePaths = $paths->getTemplateSearchPaths($panelName);
        $filePath = $templatePaths[0].$templateName;
        $templateFile = new Gpf_Io_File($filePath);

        try {
            $templateFile->open('w');
        } catch (Gpf_Exception $e) {
            if(!$this->createDirectory($filePath)) {
                $action->setErrorMessage('Unable to create directory: '.$directory);
                $action->addError();
                return $action;
            }
            $templateFile->open('w');
        }
        $templateFile->write($commonTemplate->getTemplateSource());
        $action->setInfoMessage($this->_("File added"));
        $action->addOk();
         
        return $action;
    }

    private function createDirectory($filePath) {
        $p = explode('/', $filePath);
        array_pop($p);
        $directory = implode('/', $p);
        return mkdir($directory);
    }
}

?>
