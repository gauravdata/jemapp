<?php
/**
 *   @copyright Copyright (c) 2009 Quality Unit s.r.o.
 *   @author Michal Bebjak
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
 * @package PostAffiliatePro
 */
class ChannelsListInTemplates_Main extends Gpf_Plugins_Handler {

    private static $instance = null;
    private $channels = null;
    
    /**
     * @return ChannelsListInTemplates_Main
     */
    public static function getHandlerInstance() {
        if (self::$instance === null) {
            self::$instance = new ChannelsListInTemplates_Main();
        }
        return self::$instance;
    }

    public function assignTemplateVariables(Gpf_Templates_Template $template) {
        if ($this->channels === null) {
            $this->loadChannels();
        }
        $template->assign('channels', $this->channels);
    }
    
    public function loadChannels() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add(Pap_Db_Table_Channels::VALUE);
        $select->select->add(Pap_Db_Table_Channels::NAME);
        $select->from->add(Pap_Db_Table_Channels::getName());
        $select->where->add(Pap_Db_Table_Channels::USER_ID, '=', Gpf_Session::getAuthUser()->getPapUserId());
        
        $this->channels = array();
        foreach ($select->getAllRowsIterator() as $row) {
            $this->channels[$row->get(Pap_Db_Table_Channels::VALUE)] = $row->get(Pap_Db_Table_Channels::NAME);
        }
    }
}
?>
