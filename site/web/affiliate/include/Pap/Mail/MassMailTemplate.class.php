<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package ShopMachine
 *   @since Version 1.0.0
 *   $Id: ActionParser.class.php 16620 2008-03-21 09:21:07Z aharsani $
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
class Pap_Mail_MassMailTemplate extends Pap_Mail_UserMail {

    private $templateId;

    public function setTemplateId($templateId) {
        $this->templateId = $templateId;
    }

    protected function loadTemplate() {
        $this->mailTemplate = new Gpf_Db_MailTemplate();
        $this->mailTemplate->setId($this->templateId);
        try {
            $this->mailTemplate->load();
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception($this->_('Mail template not defined in database for templateId %s', $this->templateId));
        }
    }
}
