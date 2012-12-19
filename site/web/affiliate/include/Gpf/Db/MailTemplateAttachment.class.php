<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailTemplateAttachment.class.php 20081 2008-08-22 10:21:35Z vzeman $
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
class Gpf_Db_MailTemplateAttachment extends Gpf_DbEngine_Row {

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_MailTemplateAttachments::getInstance());
        parent::init();
    }
    
    function setFileId($fileId) {
        $this->set(Gpf_Db_Table_Files::ID, $fileId);
    }
    
    function setTemplateId($templateId) {
        $this->set(Gpf_Db_Table_MailTemplates::ID, $templateId);
    }
    
    function setIsIncludedImage($isIncluded) {
        if ($isIncluded === true || $isIncluded == Gpf::YES) {
            $this->set(Gpf_Db_Table_MailTemplateAttachments::IS_INCLUDED_IMAGE, Gpf::YES);
        } else {
            $this->set(Gpf_Db_Table_MailTemplateAttachments::IS_INCLUDED_IMAGE, Gpf::NO);
        }
    }
}
