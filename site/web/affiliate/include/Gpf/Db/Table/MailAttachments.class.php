<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailAttachments.class.php 20866 2008-09-12 11:39:19Z mbebjak $
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
class Gpf_Db_Table_MailAttachments extends Gpf_DbEngine_Table {
    const FILE_ID = 'fileid';
    const MAIL_ID = 'mailid';
    const IS_INCLUDED_IMAGE = 'is_included_image';
    
    private static $instance;
        
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('g_mail_attachments');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::FILE_ID, 'char', 32, false);
        $this->createPrimaryColumn(self::MAIL_ID, 'int', 0, false);
        $this->createColumn(self::IS_INCLUDED_IMAGE, 'char', 1);
    }
    
    /**
     * Return all mail attachments for given mail id
     *
     * @param int $mailId
     * @return Gpf_Data_RecordSet
     */
    public static function getMailAttachments($mailId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        
        $select->select->add('f.*');
        $select->select->add('ma.is_included_image', 'is_included_image');
        
        $select->from->add(Gpf_Db_Table_MailAttachments::getName(), 'ma');
        $select->from->addInnerJoin(Gpf_Db_Table_Files::getName(), 'f', 'f.fileid=ma.fileid');
        
        $select->where->add('mailid', '=', $mailId);
          
        return $select->getAllRows();
    }
}
?>
