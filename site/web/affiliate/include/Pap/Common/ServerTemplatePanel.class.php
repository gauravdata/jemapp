<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: InvoiceFormatForm.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
abstract class Pap_Common_ServerTemplatePanel extends Gpf_Object {
    
    
    protected abstract function getTemplate();
    
    protected function getHtmlBody(Gpf_Rpc_Params $params) {
        $tmpl = new Gpf_Templates_Template($this->getTemplate().'.stpl');
        $tmpl = $this->fillDataToTemplate($tmpl, $params);
        return $tmpl->getHTML();
    }
    
    /**
     *
     * @param Gpf_Templates_Template $tmpl
     * @param Gpf_Rpc_Data $data
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Templates_Template
     */
    protected function fillDataToTemplate(Gpf_Templates_Template $tmpl, Gpf_Rpc_Params $params) {
        return $tmpl;
    }
    
    protected function fillData(Gpf_Rpc_Data $data, Gpf_Rpc_Params $params) {
        $data->setValue("htmlContent", $this->getHtmlBody($params));
    }
}
