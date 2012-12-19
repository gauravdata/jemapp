<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: MailAttachment.class.php 19002 2008-07-07 11:35:22Z vzeman $
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
class Gpf_Db_MailAttachment extends Gpf_DbEngine_Row {

    function __construct(){
        parent::__construct();
    }

    function init() {
        $this->setTable(Gpf_Db_Table_MailAttachments::getInstance());
        parent::init();
    }
    
    public function setFileId($fileId) {
        $this->set('fileid', $fileId);
    }
    
    public function setMailId($mailId) {
        $this->set('mailid', $mailId);
    }
    
    /**
     * Set if attachment is included image in body or simple attachment
     *
     * @param string $isInlcuded Possible values Y or N
     */
    public function setIsIncluded($isInlcuded) {
        $this->set('is_included_image', $isInlcuded);
    }
}

?>
