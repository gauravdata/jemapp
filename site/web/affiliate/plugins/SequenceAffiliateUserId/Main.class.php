<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro plugins
 */
class SequenceAffiliateUserId_Main extends Gpf_Plugins_Handler {
    const SETTING_USERID_SEQUENCE = 'USERID_SEQUENCE';

    /**
     * @return SequenceAffiliateUserId_Main
     */
    public static function getHandlerInstance() {
        return new SequenceAffiliateUserId_Main();
    }

    public function initSettings($context) {
        $context->addDbSetting(self::SETTING_USERID_SEQUENCE, '8000');
    }

    public function initPrivileges(Gpf_Privileges $privileges) {
        $privileges->addPrivilege('useridsequence', Gpf_Privileges::P_ALL);
    }

    public function generatePrimaryKey(Pap_Db_User $user) {
        $user2 = clone $user;

        //increment current userid
        $id = Gpf_Settings::get(SequenceAffiliateUserId_Main::SETTING_USERID_SEQUENCE) + 1;
        //save new userid
        Gpf_Settings::set(SequenceAffiliateUserId_Main::SETTING_USERID_SEQUENCE, $id);

        try {
            $user2->setId($id);
            $user2->load();
            return;
        } catch (Gpf_DbEngine_NoRowException $e) {
        }
        $user->setId($id);
    }
}
?>
