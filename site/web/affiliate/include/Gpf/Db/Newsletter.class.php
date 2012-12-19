<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.7
 *   $Id: Gadget.class.php 19129 2008-07-15 09:35:17Z mjancovic $
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
class Gpf_Db_Newsletter extends Gpf_DbEngine_Row {

    public function __construct(){
        parent::__construct();
    }

    public function init() {
        $this->setTable(Gpf_Db_Table_Newsletters::getInstance());
        parent::init();
    }

    public function getId() {
        return $this->get(Gpf_Db_Table_Newsletters::ID);
    }

    /**
     * Create optin mail template
     *
     * @return Gpf_Db_MailTemplate
     */
    private function createOptInMailTemplate() {
        $template = new Gpf_Db_MailTemplate();
        return $template;
    }

    public function insert() {
        $tpl = $this->createOptInMailTemplate();
        $this->setOptInTemplateId($tpl->getId());
        parent::insert();
    }
    
    public function setAccountId($accountId) {
        $this->set(Gpf_Db_Table_Accounts::ID, $accountId);
    }

    public function setOptInTemplateId($templateId) {
        $this->set(Gpf_Db_Table_Newsletters::OPTIN_MAILTEMPLATEID, $templateId);
    }
}
?>
