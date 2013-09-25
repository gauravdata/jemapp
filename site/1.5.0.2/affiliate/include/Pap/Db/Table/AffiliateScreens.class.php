<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ActiveViews.class.php 18002 2008-05-13 18:31:39Z aharsani $
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
class Pap_Db_Table_AffiliateScreens extends Gpf_DbEngine_Table {

    const ID = "affiliatescreenid";
    const ACCOUNTID = "accountid";
    const CODE = "code";
    const PARAMS = "params";
    const TITLE = "title";
    const ICON = "icon";
    const DESCRIPTION = "description";
    const SHOWHEADER = "showheader";

    private static $instance;
        
    /**
     * @return Pap_Db_Table_AffiliateScreens
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
        
    protected function initName() {
        $this->setName('pap_affiliatescreens');
    }
    
    public static function getName() {
        return self::getInstance()->name();
    }
    
    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::ACCOUNTID, 'char', 8);
        $this->createColumn(self::CODE, 'char', 255);
        $this->createColumn(self::PARAMS, 'char', 255);
        $this->createColumn(self::TITLE, 'char', 255);
        $this->createColumn(self::ICON, 'char', 255);
        $this->createColumn(self::DESCRIPTION, 'char');
        $this->createColumn(self::SHOWHEADER, 'char', 1);
    }

    protected function initConstraints() {
         $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(
            array(self::ACCOUNTID, self::TITLE),
            $this->_("Title must be unique")));
            
         $this->addDeleteConstraint(new Pap_Db_Table_AffiliateScreens_InMenuDeleteConstraint($this));
    }

    public function getAllNoRpc() {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add(self::ID, 'id');
        $selectBuilder->select->add(self::CODE, 'code');
        $selectBuilder->select->add(self::PARAMS, 'params');
        $selectBuilder->select->add(self::TITLE, 'title');
        $selectBuilder->select->add(self::ICON, 'icon');
        $selectBuilder->select->add(self::DESCRIPTION, 'description');
        $selectBuilder->select->add(self::SHOWHEADER, 'showheader');
        $selectBuilder->from->add(self::getName());
        
        return $this->replaceUserVariablesInParams($selectBuilder->getAllRows());
    }    
    
    /**
     * @return Gpf_Data_RecordSet
     */
    protected function replaceUserVariablesInParams(Gpf_Data_RecordSet $rows) {
        foreach ($rows as $row) {
            $row->set('params', str_replace('{$refid}', $this->getCurrentUserRefId(), $row->get('params')));
        }
        
        return $rows;
    }
    
    protected function getCurrentUserRefId() {
        try {
            $user = Pap_Common_User::getUserById(Gpf_Session::getAuthUser()->getPapUserId());
        } catch (Gpf_DbEngine_NoRowException $e) {
            return '';
        }
        
        return $user->getRefId();
    }
    
    /**
     * @service affiiliate_screen read
     */
    public function getAll(Gpf_Rpc_Params $params) {
        return $this->getAllNoRpc();
    }
}

class Pap_Db_Table_AffiliateScreens_InMenuDeleteConstraint extends Gpf_DbEngine_DeleteConstraint {
    function __construct() {
    }
    
    public function execute(Gpf_DbEngine_Row $dbRow) {
        $menu = Gpf_Settings::get(Pap_Settings::AFFILIATE_MENU);
        if (strpos($menu, $dbRow->getPrimaryKeyValue()) !== false) {
            throw new Gpf_Exception($this->_("Screen %s is in menu and thus can not be deleted",
                                             $dbRow->get(Pap_Db_Table_AffiliateScreens::TITLE)));
        }
    }
}

?>
