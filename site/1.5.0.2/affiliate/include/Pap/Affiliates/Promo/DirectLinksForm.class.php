<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: AffiliateForm.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Affiliates_Promo_DirectLinksForm extends Pap_Common_User_DirectLinksFormBase {

    /**
     *
     * @service direct_link read_own
     * @param Gpf_Rpc_Params $params (pattern, url)
     * @return Gpf_Rpc_Action
     */
    public function checkUrlMatch(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setErrorMessage($this->_("Pattern didn't matched the URL, check your pattern"));
        $action->setInfoMessage($this->_("Pattern matched the URL"));

        $pattern = $action->getParam("pattern");
        $url = $action->getParam("url");

        $url = str_replace('https://', '', $url);
        $url = str_replace('http://', '', $url);

        $directLinksBase = Pap_Tracking_DirectLinksBase::getInstance();
        $pattern = $directLinksBase->transformToPreg($pattern);

        if ($directLinksBase->isMatch($url, '', $pattern)) {
            $action->addOk();
        } else {
            $action->addError();
        }

        return $action;
    }

    /**
     * @param Gpf_DbEngine_Row $dbRow
     */
    protected function setDefaultDbRowObjectValues(Gpf_DbEngine_Row $dbRow) {
        $dbRow->setPapUserId(Gpf_Session::getAuthUser()->getPapUserId());
        $dbRow->setStatus(Pap_Common_Constants::STATUS_PENDING);
    }

    /**
     * @service direct_link add_own
     * @param $fields
     */
    public function add(Gpf_Rpc_Params $params) {
        return parent::add($params);
    }

    /**
     * @service direct_link delete
     * @param $fields
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }

    private function sendNotificationToMerchant(Pap_Db_DirectLinkUrl $directLink) {
        $mail = new Pap_Mail_MerchantNewDirectLinkNotification($directLink);
        $mail->loadUser(Gpf_Session::getAuthUser()->getPapUserId());
        $mail->addRecipient(Pap_Common_User::getMerchantEmail());
        try {
            $mail->send();
        } catch (Exception $e) {
        	Gpf_Log::error($this->_('Error sending new direct link notification email to merchant: %s', $e->getMessage()));
        }
    }

    protected function afterSave($dbRow, $saveType) {
        if ($saveType != self::ADD) {
            return;
        }

        if (Gpf_Settings::get(Pap_Settings::NOTIFICATION_NEW_DIRECT_LINK) == Gpf::YES) {
            $this->sendNotificationToMerchant($dbRow);
        }
    }

}

?>
