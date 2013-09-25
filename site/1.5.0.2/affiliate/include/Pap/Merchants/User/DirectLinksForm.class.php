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
class Pap_Merchants_User_DirectLinksForm extends Pap_Common_User_DirectLinksFormBase {

    protected function getLinks($rowsIterator, $deleted) {
        $usersMailTemplates = array();

        foreach ($rowsIterator as $row) {
            $link = new Pap_Db_DirectLinkUrl();
            $link->fillFromRecord($row);

            $userId = $link->getPapUserId();
            if (!array_key_exists($userId, $usersMailTemplates)) {
                $usersMailTemplates[$userId] = new Pap_Mail_AffiliateDirectLinkNotification();
            }
            if ($deleted) {
                $usersMailTemplates[$userId]->addDeletedDirectLink($link);
            } else {
                switch ($link->get(Pap_Db_Table_DirectLinkUrls::STATUS)) {
                    case Pap_Common_Constants::STATUS_APPROVED:
                        $usersMailTemplates[$userId]->addApprovedDirectLink($link);
                        break;
                    case Pap_Common_Constants::STATUS_DECLINED:
                        $usersMailTemplates[$userId]->addDeclinedDirectLink($link);
                        break;
                    case Pap_Common_Constants::STATUS_PENDING:
                        $usersMailTemplates[$userId]->addPendingDirectLink($link);
                        break;
                }
            }
        }
        return $usersMailTemplates;
    }
    /**
     *
     * @return Pap_Common_User
     */
    protected function loadUser($userId) {
        return Pap_Common_User::getUserById($userId);
    }

    protected function loadAttributes($accountUserId) {
        $attribute = Gpf_Db_Table_UserAttributes::getInstance();
        $attribute->loadAttributes($accountUserId);
        return $attribute;
    }

    /**
     *
     * @param $params
     */
    protected function sendNotificationEmail(array $linkIds, $deleted = false) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->addAll(Pap_Db_Table_DirectLinkUrls::getInstance());
        $selectBuilder->from->add(Pap_Db_Table_DirectLinkUrls::getName());
        $selectBuilder->where->add(Pap_Db_Table_DirectLinkUrls::ID, 'IN', $linkIds);

        $usersMailTemplates = $this->getLinks($selectBuilder->getAllRowsIterator(), $deleted);

        foreach ($usersMailTemplates as $userId => $mail) {
            try {
                $user = $this->loadUser($userId);
            } catch (Gpf_DbEngine_NoRowException $e) {
                Gpf_Log::warning($this->_('Error load affiliate with userid=' . $userId . ', mail will not be send.'));
                continue;
            }

            $attribute = $this->loadAttributes($user->getAccountUserId());

            if (Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_ENABLED) == Gpf::YES) {
                $isNotify = $attribute->getAttributeWithDefaultValue(Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_ENABLED,
                Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_DEFAULT));
            } else {
                $isNotify = Gpf_Settings::get(Pap_Settings::AFF_NOTIFICATION_ON_DIRECT_LINK_DEFAULT);
            }

            if ($isNotify == Gpf::YES) {

                $mail->setUser($user);
                $mail->addRecipient($user->getEmail());
                try {
                    $mail->send();
                } catch (Exception $e) {
                    Gpf_Log::error($this->_('Error sending direct link changed status notification email to affiliate: %s', $e->getMessage()));
                }
            }
        }
    }

    /**
     *
     * @param $params
     * @return Gpf_Rpc_Action
     */
    private function quickTaskSaveApprovement(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);

        $dbRow = $this->createDbRowObject();
        $dbRow->setPrimaryKeyValue($action->getParam('directLinkId'));
        try {
            $dbRow->load();
        } catch (Gpf_Exception $e) {
            $action->setErrorMessage($this->_('Failed to load direct link. (direct link propably deleted)'));
            $action->addError();
            return $action;
        }
        $dbRow->set('rstatus', $action->getParam('rstatus'));
        $dbRow->save();
        $action->addOk();

        if ($action->getParam('rstatus') == Pap_Common_Constants::STATUS_APPROVED) {
            $action->setErrorMessage($this->_('Failed to approve direct link %s.', $dbRow->getUrl()));
            $action->setInfoMessage($this->_('Direct link %s was successfully approved.', $dbRow->getUrl()));
        } else if ($action->getParam('rstatus') == Pap_Common_Constants::STATUS_DECLINED) {
            $action->setErrorMessage($this->_('Failed to decline direct link %s.', $dbRow->getUrl()));
            $action->setInfoMessage($this->_('Direct link %s was successfully declined.', $dbRow->getUrl()));
        }

        return $action;
    }

    /**
     * @service
     * @param $params
     * @return unknown_type
     */
    public function quickTaskApprove(Gpf_Rpc_Params $params) {
        $retval = $this->quickTaskSaveApprovement($params);

        Pap_Tracking_DirectLinksBase::getInstance()->regenerateDirectLinksFile();

        $changedUrl = array();
        $changedUrl[] = $params->get('directLinkId');

        $this->sendNotificationEmail($changedUrl);

        return $retval;
    }


    /**
     * @service direct_link write
     *
     * @param $fields
     */
    public function saveFields(Gpf_Rpc_Params $params) {
        $retval = parent::saveFields($params);

        Pap_Tracking_DirectLinksBase::getInstance()->regenerateDirectLinksFile();

        $fields = new Gpf_Data_RecordSet();
        $fields->loadFromArray($params->get("fields"));
        $changedUrls = array();
        foreach ($fields as $field) {
            $changedUrls[] = $field->get('id');
        }

        $this->sendNotificationEmail($changedUrls);

        return $retval;
    }

    /**
     * @service direct_link delete
     * @param $ids
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        $deletedUrls = array();
        $ids = $params->get("ids");
        foreach ($ids as $url) {
            $deletedUrls[] = $url;
        }
        $this->sendNotificationEmail($deletedUrls, true);

        $ret = parent::deleteRows($params);

        Pap_Tracking_DirectLinksBase::getInstance()->regenerateDirectLinksFile();
        return $ret;
    }


    /**
     *
     * @param $params
     * @return Gpf_Rpc_Action
     */
    private function saveChangedStatusToDB(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        $action->setInfoMessage($this->_("Status successfully set to selected url(s)"));
        $action->setErrorMessage($this->_("Failed to set status selected url(s)"));

        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Pap_Db_Table_DirectLinkUrls::getName());
        $update->set->add(Pap_Db_Table_DirectLinkUrls::STATUS, $action->getParam("status"));

        foreach ($action->getIds() as $id){
            $update->where->add(Pap_Db_Table_DirectLinkUrls::ID, "=", $id, "OR");
        }

        try {
            $update->execute();
            $action->addOk();

            Pap_Tracking_DirectLinksBase::getInstance()->regenerateDirectLinksFile();
        } catch(Gpf_DbEngine_NoRowException $e) {
            $action->addError();
        }
        return $action;
    }

    /**
     *
     * @service direct_link write
     * @param ids, status
     */
    public function changeStatus(Gpf_Rpc_Params $params) {
        $retval = $this->saveChangedStatusToDB($params);
        $changedUrls = array();
        $ids = $params->get("ids");
        foreach ($ids as $url) {
            $changedUrls[] = $url;
        }
        $this->sendNotificationEmail($changedUrls);
        return $retval;
    }

    /**
     * @service direct_link read
     * @param $fields
     */
    public function load(Gpf_Rpc_Params $params) {
        return parent::load($params);
    }

    /**
     * @service direct_link write
     * @param $fields
     */
    public function save(Gpf_Rpc_Params $params) {
        $ret = parent::save($params);
        Pap_Tracking_DirectLinksBase::getInstance()->regenerateDirectLinksFile();
        return $ret;
    }

    /**
     * @service direct_link add
     * @param $fields
     */
    public function add(Gpf_Rpc_Params $params) {
        $ret = parent::add($params);
        Pap_Tracking_DirectLinksBase::getInstance()->regenerateDirectLinksFile();
        return $ret;
    }
}

?>
