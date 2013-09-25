<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Dictionary.class.php 19083 2008-07-10 16:32:14Z aharsani $
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
class Gpf_Lang_Languages extends Gpf_Object {

    protected function __construct() {
    }

    /**
     * @return Gpf_Lang_Languages
     */
    public static function getInstance($ignoreInstall = false) {
        if (Gpf_Paths::getInstance()->isInstallModeActive() && !$ignoreInstall) {
            return new Gpf_Lang_InstallLanguages();
        }
        return new Gpf_Lang_Languages();
    }

    /**
     * Get recordset of active languages in this account
     *
     * @return Gpf_Data_IndexedRecordSet
     */
    public function getActiveLanguagesNoRpc() {
        $sql = new Gpf_SqlBuilder_SelectBuilder();
        $sql->select->add(Gpf_Db_Table_Languages::CODE);
        $sql->select->add(Gpf_Db_Table_Languages::ENGLISH_NAME);
        $sql->select->add(Gpf_Db_Table_Languages::NAME);
        $sql->select->add(Gpf_Db_Table_Languages::IS_DEFAULT);
        $sql->from->add(Gpf_Db_Table_Languages::getName());
        $sql->where->add(Gpf_Db_Table_Accounts::ID, '=', Gpf_Application::getInstance()->getAccountId());
        $sql->where->add(Gpf_Db_Table_Languages::ACTIVE, '=', Gpf::YES);
        $sql->orderBy->add(Gpf_Db_Table_Languages::NAME);
        return $sql->getAllRowsIndexedBy(Gpf_Db_Table_Languages::CODE);
    }
}
?>
