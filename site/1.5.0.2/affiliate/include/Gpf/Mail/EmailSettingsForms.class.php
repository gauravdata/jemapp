<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: EmailSettingsForms.class.php 25470 2009-09-25 10:05:33Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Mail_EmailSettingsForms extends Gpf_Object {

    /**
     * @service email_setting read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $dbRow = new Gpf_Db_MailAccount();
        $dbRow->setAsDefault(true);

        try {
            $dbRow->loadFromData(array(Gpf_Db_Table_MailAccounts::IS_DEFAULT));
            $form->load($dbRow);
        } catch (Gpf_DbEngine_NoRow $e) {
            $form->setErrorMessage($this->getDbRowObjectName().$this->_(" does not exist"));
            return $form;
        }
        return $form;
    }

    /**
     * @service email_setting write
     * @param $fields
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $dbRow = new Gpf_Db_MailAccount();

        try {
            $dbRow->setAsDefault(true);
            $dbRow->loadFromData(array(Gpf_Db_Table_MailAccounts::IS_DEFAULT));
        } catch (Gpf_DbEngine_NoRowException $e) {
            $dbRow->setAccountName($this->_("Default Mail Account"));
            $dbRow->setAsDefault(true);
        }
        $form->fill($dbRow);
        $dbRow->save();

        $form->setInfoMessage($this->_("Email settings saved"));
        return $form;
    }

    /**
     * Save mail account and send test message
     *
     * @service email_setting write
     * @param $fields
     */
    public function sendTestMail(Gpf_Rpc_Params $params){
        $form = $this->save($params);

        if (!strlen($form->getFieldValue("send_test_mail_to"))) {
            $form->setErrorMessage($this->_('Failed to send test mail. Please fill in recipient of test mail first.'));
            return $form;
        }

        $mailAccount = new Gpf_Db_MailAccount();
        $mailAccount->setAsDefault(true);
        $mailAccount->loadFromData(array(Gpf_Db_Table_MailAccounts::IS_DEFAULT));;

        $testMail = new Gpf_Mail_EmailAccountTestMail();
        $testMail->setMailAccount($mailAccount);

        try {
            $testMail->addRecipient($form->getFieldValue("send_test_mail_to"));
            $testMail->sendNow();
            $form->setInfoMessage($this->_("Test mail scheduled, check outbox if it was sent."));
        } catch (Exception $e) {
            $form->setErrorMessage($e->getMessage());
        }

        return $form;
    }
}
?>
