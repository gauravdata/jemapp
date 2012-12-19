<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailTemplateAttachments.class.php 26360 2009-11-30 17:20:06Z mgalik $
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
class Gpf_Db_Table_MailTemplateAttachments extends Gpf_DbEngine_Table {
    const IS_INCLUDED_IMAGE = 'is_included_image';
    const FILEID = 'fileid';
    const MAILTEMPLATEID = 'templateid';

    private static $instance;
    
    /**
     * @return Gpf_Db_Table_MailTemplateAttachments
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_mail_template_attachments');
    }

    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::FILEID, 'char', 32, false);
        $this->createPrimaryColumn(self::MAILTEMPLATEID, 'char', 8, false);
        $this->createColumn(self::IS_INCLUDED_IMAGE, 'char', 1);
    }

    public function deleteAll($templateId = false, $fileId = false) {
        $deleteBulider = new Gpf_SqlBuilder_DeleteBuilder();
        $deleteBulider->from->add(self::getName());
        if ($templateId) {
            $deleteBulider->where->add('templateid', '=', $templateId);
        }
        if ($fileId) {
            $deleteBulider->where->add('fileid', '=', $fileId);
        }
        $this->createDatabase()->execute($deleteBulider->toString());
    }
}
?>
