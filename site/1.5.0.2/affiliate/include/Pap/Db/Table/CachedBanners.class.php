<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
 *   $Id: Banners.class.php 25633 2009-10-08 14:33:51Z mbebjak $
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
class Pap_Db_Table_CachedBanners extends Gpf_DbEngine_Table {
    const ID = 'cachedbannerid';
    const USERID = 'userid';
    const BANNERID = 'bannerid';
    const PARENTBANNERID = 'parentbannerid';
    const CHANNEL = 'channel';
    const WRAPPER = 'wrapper';
    const HEADERS = 'headers';
    const CODE = 'code';
    const RANK = 'rank';
    const VALID_FROM = 'valid_from';
    const VALID_UNTIL = 'valid_until';
    const DYNAMIC_LINK = 'dynamiclink';
    
    private static $instance;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('pap_cachedbanners');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, self::INT);
        $this->createColumn(self::USERID, self::CHAR, 128);
        $this->createColumn(self::BANNERID, self::CHAR, 8);
        $this->createColumn(self::PARENTBANNERID, self::CHAR, 8);
        $this->createColumn(self::CHANNEL, self::CHAR, 10);
        $this->createColumn(self::WRAPPER, self::CHAR, 8);
        $this->createColumn(self::HEADERS, self::CHAR);
        $this->createColumn(self::CODE, self::CHAR);
        $this->createColumn(self::RANK, self::FLOAT);
        $this->createColumn(self::VALID_FROM, self::DATETIME);
        $this->createColumn(self::VALID_UNTIL, self::DATETIME);
        $this->createColumn(self::DYNAMIC_LINK, self::CHAR);
    }
    
    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_UniqueConstraint(
            array(self::USERID, self::BANNERID, self::PARENTBANNERID, self::CHANNEL,
                  self::WRAPPER, self::HEADERS, self::CODE, self::RANK)));
    }
    
    public static function deleteCachedBannersForUser($userid, $refid = '') {
        if ($userid == '' || $userid == null) {
            return;
        }
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(self::getName());
        $delete->where->add(self::USERID, '=', $userid, 'OR');
        if ($refid != '' && $refid != null) {
            $delete->where->add(self::USERID, '=', $refid, 'OR');
        }
        $delete->execute();
    }
    
    public static function clearCachedBanners(){
        $select = new Gpf_SqlBuilder_DeleteBuilder();
        $select->from->add(Pap_Db_Table_CachedBanners::getName());
        $select->delete();
    }
    
    public static function deleteCachedBannersForBanner($bannerid) {
        if ($bannerid == '' || $bannerid == null) {
            return;
        }
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(self::getName());
        $delete->where->add(self::BANNERID, '=', $bannerid, 'OR');
        $delete->where->add(self::PARENTBANNERID, '=', $bannerid, 'OR');
        $delete->execute();
    }
}
?>
