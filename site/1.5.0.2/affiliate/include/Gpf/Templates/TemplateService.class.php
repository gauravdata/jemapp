<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: TemplateService.class.php 22443 2008-11-21 14:10:51Z vzeman $
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

?>
