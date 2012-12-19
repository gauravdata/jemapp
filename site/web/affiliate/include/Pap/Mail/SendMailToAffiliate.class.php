<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
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
class Pap_Mail_SendMailToAffiliate extends Pap_Mail_SendMassMail {

    /**
     * @service mass_email write
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $this->sendMail($this->createMassMailTemplate($form), $form);

        $form->setInfoMessage($this->_('Mail will be delivered in background process.'));
        return $form;
    }

    private function sendMail(Gpf_Db_MailTemplate $dbTemplate, Gpf_Rpc_Form $form) {
        $user = new Pap_Common_User();
        $user->setId($form->getFieldValue('userid'));
        $user->load();

        $template = new Pap_Mail_MassMailTemplate();
        $template->setTemplateId($dbTemplate->getId());
        $template->setUser($user);
        $template->addRecipient($user->getEmail());
        $template->send();
    }

}
