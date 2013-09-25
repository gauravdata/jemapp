<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
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
class DatabaseStatus_Definition extends Gpf_Plugins_Definition {

    public function __construct() {
        $this->codeName =  'DatabaseStatus';
        $this->name = $this->_('Database Status');
        $this->description = $this->_('Plugin will add to menu Tools new item "Database status", under which you can view database tables of %s. Information about database tables includes e.g. table names, table size, number of rows in each table. You can execute on tables database operations like Analyze table, Optimize table or Repair table. After activation of this plugin you need to reload application in browser to see changes in menu!', Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO));
        $this->version = '1.0.1';
        $this->help = $this->_('Plugin is possible to activate only in case database user used for connecting to database of %s has rights to read from system table INFORMATION_SCHEMA.TABLES.', Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO));

        $this->addRequirement('PapCore', '4.1.15.0');

        $this->addImplementation('PostAffiliate.merchant.menu', 'DatabaseStatus_Main', 'addToMenu');
    }

    public function onActivate() {
        try {
            $sql = new Gpf_SqlBuilder_SelectBuilder();
            $sql->select->add('count(*)', 'num_tables');
            $sql->from->add('INFORMATION_SCHEMA.TABLES');
            $sql->where->add("TABLE_SCHEMA", "=", Gpf_Settings::get(Gpf_Settings_Gpf::DB_DATABASE));
            $num_tables = $sql->getOneRow()->get('num_tables');
            if ($num_tables < 10) {
                throw new Gpf_Exception('Tables is less than 10 - something is not correct');
            }
        } catch (Gpf_Exception $e) {
            throw new Gpf_Exception($this->_('Failed to activate plugin, database user %s has no rights to read information_schema.tables system database.', Gpf_Settings::get(Gpf_Settings_Gpf::DB_USERNAME)));
        }
    }
}
?>
