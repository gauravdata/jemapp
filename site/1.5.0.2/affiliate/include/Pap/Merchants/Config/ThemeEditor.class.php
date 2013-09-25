<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Merchants_Config_ThemeEditor extends Gpf_Object {

    /**
     * @service theme read
     */
    public function loadTree(Gpf_Rpc_Params $params) {
        $themeBase = $this->getThemeFile($params->get('panelName'));
        $themeDirName = $params->get('themeId');
        $itemPath = $params->get('itemId');
        $filter = $params->get('filter');

        $files = array();
        $dirs = array();
        $this->loadFiles($files, $dirs, $themeBase.'/'.Gpf_Paths::DEFAULT_THEME.$itemPath, $filter);
        $this->loadFiles($files, $dirs, $themeBase.'/'.$themeDirName.'/'.$itemPath, $filter);
        return $this->createLoadTreeResult($files, $dirs, $itemPath);
    }

    /**
     * @service theme write
     */
    public function revertFile(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        try{
            $themeId = $action->getParam('themeId');
            $itemId = $action->getParam("Id");
            $themeBase = $this->getThemeFile($action->getParam('panelName'));

            $file_original = new Gpf_Io_File($themeBase.'/'.Gpf_Paths::DEFAULT_THEME.$itemId);
            $file_new = new Gpf_Io_File($themeBase.'/'.$themeId.$itemId);

            if($file_original->isExists() && $file_new->isExists()){
                if(!$file_new->delete()){
                    throw new Gpf_Exception(_("Revert of file has failed"));
                }else{
                    $action->setInfoMessage(_("Revert of file success"));
                }
            }else{
                $action->setInfoMessage(_("This file cannot be reverted"));
            }
            $action->addOk();
        }catch (Gpf_Exception $e){
            $action->addError();
            $action->setErrorMessage($e->getMessage());
        }
        return $action;
    }
    

    /**
     * @service theme read
     */
    public function loadFile(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $themeId = $form->getFieldValue('themeId');
        $itemId = $form->getFieldValue("id");
        $themeBase = $this->getThemeFile($form->getFieldValue('panelName'));

        $loadFile = null;
        $file_theme = new Gpf_Io_File($themeBase.'/'.$themeId.$itemId);
        if($file_theme->isExists()){
            $loadFile = $file_theme;
        }else{
            $loadFile = new Gpf_Io_File($themeBase.'/'.Gpf_Paths::DEFAULT_THEME.$itemId);
        }
        $form->setField('text', $loadFile->getContents());
        return $form;
    }

    protected function getFileExtension($fileName) {
        $offset = strrpos($fileName, '.');
        return substr($fileName, $offset + 1);
    }

    private function createLoadTreeResult(array $files, array $dirs, $itemPath){
        $result = new Gpf_Data_RecordSet();
        $result->setHeader(array('itemId', 'subItemsCount', 'name', 'info', 'type', 'path'));
        foreach ($files as $name => $path){
            $date = date("y-m-d H:i:s.", filectime($path));
            $result->add(array($itemPath.'/'.$name, 0, $name, $date,  Gpf_File_FilesTree::TYPE_FILE, $path));
        }
        foreach ($dirs as $name => $path){
            $result->add(array($itemPath.'/'.$name, 1, $name, 99999999,  Gpf_File_FilesTree::TYPE_DIRECTORY, $path));
        }
        $result->sort('info', Gpf_Data_RecordSet::SORT_DESC);
        return $result;
    }


    private function loadFiles(array &$files, array &$dirs, $folder, $filter){
        $file = new Gpf_Io_File($folder);
        if (!$file->isExists()) {
            return;
        }
        foreach (new Gpf_Io_DirectoryIterator($file, '', false, true) as $fullName => $name) {
            $dirs[$name] = $fullName;
        }
        foreach (new Gpf_Io_DirectoryIterator($file) as $fullName => $name) {
            if($this->acceptsFilter($fullName, $name, $filter)){
                $files[$name] = $fullName;
            }
        }
    }


    private function acceptsFilter($fullName, $name ,$filter){
        if($filter == null || $filter == ''){
            return true;
        }
        if(stripos($name, $filter) !== false){
            return true;
        }
        if($this->isSearchFile($name)){
            $contents = file_get_contents($fullName);
            return stripos($contents, $filter) !== false;
        }else{
            return false;
        }
    }


    private function isSearchFile($name){
        return strripos($name, '.tpl') || strripos($name, '.stpl') || strripos($name, '.css');
    }

    /**
     * @service theme write
     * @return Gpf_Rpc_Form
     */
    public function saveFile(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $theme = $form->getFieldValue('themeId');
        $fileName = ltrim($form->getFieldValue("id"), '/');
        $panel = basename($this->getThemeFile($form->getFieldValue('panelName')));
        $text = $form->getFieldValue('text');
        
        $template = new Gpf_Templates_Template($text, $panel, Gpf_Templates_Template::FETCH_TEXT, $theme);
        if ($this->getFileExtension($fileName) == 'tpl' || $this->getFileExtension($fileName) == 'stpl') {
            try {
                $template->check();
            } catch (Gpf_Templates_SmartySyntaxException $e) {
                $form->setErrorMessage($e->getMessage());
                return $form;
            }
        }
        
		$file = $this->getTemplateFile($panel, $theme, $fileName);
        try {
            if (!$file->getParent()->isExists()) {
                $file->getParent()->mkdir(true);
            }
        } catch (Exception $ex) {
            $form->setErrorMessage($this->_('Error creating directory %s '.$ex->getMessage(), $file->getParent()->getFileName()));
            return $form;
        }
        try {
        	if (!$file->isExists()) {
				$file->open('w');
				$file->close();
			}
        } catch (Exception $ex) {
            $form->setErrorMessage($this->_('Error creating file %s '.$ex->getMessage(), $file->getFileName()));
            return $form;
        }
        try {
        	$template = $this->getTemplateObject($panel, $theme, $fileName);
            $template->saveTemplateToFile($text);
        } catch(Exception $ex){
            $form->setErrorMessage($this->_('Error writing to file %s '.$ex->getMessage(), $file->getFileName()));
            return $form;
        }
        $form->setInfoMessage($this->_('File %s was saved.', $file->getFileName()));
        return $form;
    }

    /**
     * @return Gpf_Io_File
     */
    private function getThemeFile($path){
        return new Gpf_Io_File(Gpf_Paths::getInstance()->getTopTemplatePath().$path);
    }
    
    /**
     * @return Gpf_Templates_Template
     */
    protected function getTemplateObject($panel, $theme, $fileName) {
        return new Gpf_Templates_Template($fileName, $panel, Gpf_Templates_Template::FETCH_FILE, $theme);
    }
    
	/**
     * @return Gpf_Io_File
     */
    protected function getTemplateFile($panel, $theme, $fileName) {
    	return new Gpf_Io_File(Gpf_Paths::getInstance()->buildTemplatePath('../', $panel, $theme).$fileName);
    }
    
}
?>
