<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: Users.class.php 28977 2010-08-04 06:57:49Z iivanco $
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
class Pap_Db_Table_Users extends Gpf_DbEngine_Table {
    const ID = 'userid';
    const REFID = 'refid';
    const NUMBERUSERID = 'numberuserid';
    const TYPE = 'rtype';
    const DATEINSERTED = 'dateinserted';
    const DATEAPPROVED = 'dateapproved';
    const DELETED = 'deleted';
    const ACCOUNTUSERID = 'accountuserid';
    const PARENTUSERID = 'parentuserid';
    const PAYOUTOPTION_ID = "payoutoptionid";
    const MINIMUM_PAYOUT = "minimumpayout";
    const NOTE = "note";
    const PHOTO = "photo";
    const ORIGINAL_PARENT_USERID = 'originalparentuserid';

    private static $instance;

    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function initName() {
        $this->setName('pap_users');
    }

    public static function getName() {
        return self::getInstance()->name();
    }

    public static function getDataColumnName($i) {
        return 'data'.$i;
    }

    protected function initColumns() {
        $this->createPrimaryColumn(self::ID, 'char', 8, true);
        $this->createColumn(self::REFID, 'char', 128, true);
        $this->createColumn(self::NUMBERUSERID, self::INT);
        $this->createColumn(self::TYPE, 'char', 1);
        $this->createColumn(self::DATEINSERTED, 'datetime', 0);
        $this->createColumn(self::DATEAPPROVED, 'datetime', 0);
        $this->createColumn(self::DELETED, 'char', 1);
        $this->createColumn(self::ACCOUNTUSERID, 'char', 20);
        $this->createColumn(self::PARENTUSERID, 'char', 20);
        $this->createColumn(self::MINIMUM_PAYOUT, 'char', 20);
        $this->createColumn(self::PAYOUTOPTION_ID, 'char', 8);
        $this->createColumn(self::NOTE, 'char');
        $this->createColumn(self::PHOTO, 'char', 255);
        $this->createColumn(self::ORIGINAL_PARENT_USERID, self::CHAR, 20);
        for ($i = 1; $i <= 25; $i++) {
            $this->createColumn(self::getDataColumnName($i), self::CHAR, 255);
        }
    }

    public static function getAffiliateCount() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('COUNT(*)', 'count');
        $select->from->add(self::getName(), 'pu');
        $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'u', 'u.accountuserid=pu.accountuserid');
        $select->where->add('pu.' . self::TYPE, '=', Pap_Application::ROLETYPE_AFFILIATE);
        $select->where->add('u.'.Gpf_Db_Table_Users::STATUS, '=', Gpf_Db_User::APPROVED);
        $select->where->add('pu.'.Pap_Db_Table_Users::DELETED, '=', Gpf::NO);
        return $select->getOneRow()->get('count');
    }

    /**
     * Pap alert application handle, do not modifi this source!
     *
     * @return Gpf_Data_Record
     */
    public static function getAffiliatesCount($date) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add("SUM(IF(gu.".Gpf_Db_Table_Users::STATUS."= 'A', 1, 0))", 'affiliates_approved');
        $select->select->add("SUM(IF(gu.".Gpf_Db_Table_Users::STATUS."= 'P', 1, 0))", 'affiliates_pending');
        $select->select->add("SUM(IF(gu.".Gpf_Db_Table_Users::STATUS."= 'D', 1, 0))", 'affiliates_declined');
        $select->from->add(Gpf_Db_Table_Users::getName(), 'gu');
        $select->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'pu',
            'gu.'.Gpf_Db_Table_Users::ID.'=pu.'.Pap_Db_Table_Users::ACCOUNTUSERID);
        $select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au',
            'gu.'.Gpf_Db_Table_Users::AUTHID.'=au.'.Gpf_Db_Table_AuthUsers::ID);
        $select->where->add('gu.'.Gpf_Db_Table_Users::ROLEID, "=", Pap_Application::DEFAULT_ROLE_AFFILIATE);
        $select->where->add('pu.'.Pap_Db_Table_Users::DATEINSERTED, ">=", $date);
        $row = $select->getOneRow();

        return $row;
    }

    protected function initConstraints() {
        $this->addConstraint(new Gpf_DbEngine_Row_RegExpConstraint(self::REFID,
                                    "/^[a-zA-Z0-9_\-]*$/",
        $this->_('Referral ID can contain only [a-zA-Z0-9_-] characters. %s given')));

        $this->addConstraint(new Gpf_DbEngine_Row_ColumnsNotEqualConstraint(self::REFID, array(self::ID, self::REFID),
        $this->_("Referral ID is already used")));

        $this->addConstraint(new Gpf_DbEngine_Row_RelationConstraint(
        array(self::PARENTUSERID => self::ID),
        new Pap_Db_User_SpecialInit($this),
        false,
        $this->_('Selected parent affiliate does not exist')));

        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CachedBanners::USERID, new Pap_Db_CachedBanner());
        $this->addCascadeDeleteConstraint(self::REFID, Pap_Db_Table_CachedBanners::USERID, new Pap_Db_CachedBanner());

        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_RawClicks::USERID, new Pap_Db_RawClick());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Clicks::USERID, new Pap_Db_Click());

        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Impressions::USERID, new Pap_Db_Impression());

        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_CpmCommissions::USERID, new Pap_Db_CpmCommission());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Channels::USER_ID, new Pap_Db_Channel());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_DirectLinkUrls::USER_ID, new Pap_Db_DirectLinkUrl());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_LifetimeCommissions::USER_ID, new Pap_Db_LifetimeCommission());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Payouts::USER_ID, new Pap_Db_Payout());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_Transactions::USER_ID, new Pap_Db_Transaction());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_UserInCommissionGroup::USER_ID, new Pap_Db_UserInCommissionGroup());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_UserPayoutOptions::USERID, new Pap_Db_UserPayoutOption());
        $this->addCascadeDeleteConstraint(self::ID, Pap_Db_Table_VisitorAffiliates::USERID, new Pap_Db_VisitorAffiliate());

        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.UsersTable.constraints', $this);
    }
}

class Pap_Db_User_SpecialInit extends Pap_Db_User {

    private $table;

    function __construct(Gpf_DbEngine_Table $table){
        $this->table = $table;
        parent::__construct();
    }

    function init() {
        $this->setTable($this->table);
        Gpf_DbEngine_Row::init();
    }
}

?>
