<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Templates.class.php 30236 2010-12-01 12:53:41Z mjancovic $
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
?>
