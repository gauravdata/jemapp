<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
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
class Pap_Common_Reports_AffiliateTreeBase extends Gpf_Ui_LoadableTree {

    /**
     * @var Gpf_Rpc_FilterCollection
     */
    private $filterCollection;

    /**
     *  Returns tree of subaffiliates
     *
     * @service affiliate_tree read
     * @param $itemId
     * @return Gpf_Data_RecordSet
     */
    public function loadTree(Gpf_Rpc_Params $params) {
        $params = $this->addFilters($params);
        $this->filterCollection = new Gpf_Rpc_FilterCollection($params);
        return parent::loadTree($params);
    }

    protected function addFilters(Gpf_Rpc_Params $params) {
        return $params;
    }

    /**
     *  Returns parent affiliates plus current affiliate
     *
     * @service affiliate_tree read
     * @param $itemId
     * @return Gpf_Data_RecordSet
     */
    public function loadParents(Gpf_Rpc_Params $params) {
        $result = new Gpf_Data_RecordSet();
        $user = new Pap_Affiliates_User();
        $result->setHeader(array_keys($user->toArray()));
        $result->addColumn("subaffiliates", 1);
        $this->addUserToRecordSet($result, $params->get('itemId'));
        return $result;
    }

    private function addUserToRecordSet(Gpf_Data_RecordSet $result, $userId) {
        $user = new Pap_Affiliates_User();
        $user->setId($userId);
        try {
            $user->load();
            $record = $result->createRecord();
            $record->loadFromObject(array_values($user->toArray()));
            $record->set("subaffiliates", 1);
            $result->add($record);
            if (strlen($user->getParentUserId())) {
                $this->addUserToRecordSet($result, $user->getParentUserId());
            }
        } catch (Exception $e) {
        }
    }


    protected function loadItems($itemId, $lastTier = false)  {
        $result = new Gpf_Data_RecordSet();

        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->addAll(Pap_Db_Table_Users::getInstance(), 'u');
        $selectBuilder->select->add('au.username', 'username');
        $selectBuilder->select->add('au.firstname', 'firstname');
        $selectBuilder->select->add('au.lastname', 'lastname');
        $selectBuilder->select->add('au.'.Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL, Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL);

        $selectBuilder->from->add(Pap_Db_Table_Users::getName(), "u");
        $selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "u.accountuserid = gu.accountuserid");
        $selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), "au", "gu.authid = au.authid");

        $selectBuilder->where->add('u.rtype', '=', Pap_Application::ROLETYPE_AFFILIATE);

        $this->filterCollection->addTo($selectBuilder->where);

        $this->addWhereCondition($selectBuilder, $itemId);

        $result->load($selectBuilder);


        $result->addColumn("subaffiliates", 0);
        if (!$lastTier) {
            $record = $this->addSubaffiliatesCount($result);
        }

        return $result;
    }

    protected function addWhereCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $parentUserId) {
    }

    private function addSubaffiliatesCount(Gpf_Data_RecordSet $input) {
        $maxUsersPerSelect = 1;

        $subAffiliatesCounts = array();
        $count = 0;
        $ids = array();
        foreach($input as $record) {
            $ids[] = $record->get("userid");

            $count++;
            if($count == $maxUsersPerSelect) {
                $countArray = $this->getSubaffiliatesCounts($ids);
                $subAffiliatesCounts = $this->merge($subAffiliatesCounts, $countArray);
                $ids = array();
                $count = 0;
            }
        }

        if($count > 0) {
            $countArray = $this->getSubaffiliatesCounts($ids);
            $subAffiliatesCounts = array_merge($subAffiliatesCounts, $countArray);
        }

        foreach($input as $record) {
            if(isset($subAffiliatesCounts[$record->get("userid")])) {
                $record->set("subaffiliates", $subAffiliatesCounts[$record->get("userid")]);
            } else {
                $record->set("subaffiliates", 0);
            }
        }

        return $input;
    }

    private function getSubaffiliatesCounts($ids) {
        $results = array();
        $condition = '';

        $rs = new Gpf_Data_RecordSet();
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->add('count(u.'.Pap_Db_Table_Users::ID.')', 'count');
        $selectBuilder->select->add('u.'.Pap_Db_Table_Users::PARENTUSERID, Pap_Db_Table_Users::PARENTUSERID);

        $selectBuilder->from->add(Pap_Db_Table_Users::getName(), 'u');
        $selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu', 'u.'.Pap_Db_Table_Users::ACCOUNTUSERID.' = gu.'.Gpf_Db_Table_Users::ID);
        $selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au', 'gu.'.Gpf_Db_Table_Users::AUTHID .' = au.'.Gpf_Db_Table_AuthUsers::ID);

        $selectBuilder->where->add('u.'.Pap_Db_Table_Users::PARENTUSERID, 'in', $ids);
        $selectBuilder->where->add('u.'.Pap_Db_Table_Users::DELETED, '=', Gpf::NO);
		$selectBuilder->where->add('gu.'.Gpf_Db_Table_Users::STATUS, 'in', array(Gpf_Db_User::APPROVED, Gpf_Db_User::PENDING));
        $this->filterCollection->addTo($selectBuilder->where);

        $selectBuilder->groupBy->add('u.'.Pap_Db_Table_Users::PARENTUSERID);
        
        $rs->load($selectBuilder);

        foreach($rs as $record) {
            $results[$record->get(Pap_Db_Table_Users::PARENTUSERID)] = $record->get('count');
        }

        return $results;
    }
    
    /**
     *
     * @return Pap_Common_User
     * @throws Gpf_DbEngine_NoRowException
     */
    
    protected function loadUser($userId) {
        return Pap_Common_User::getUserById($userId);
    }

    /**
     *
     * @param $userId
     * @param $ancestorId
     * @throws Gpf_DbEngine_NoRowException
     * @return integer, -1 where $userId is not child of $ancestorId
     */
    protected function getNumberOfTiersToAncestor($userId, $ancestorId) {
        if ($userId == $ancestorId) {
            return 0;
        }

        $count = 0;
        try {
            $user = $this->loadUser($userId);
        } catch (Gpf_DbEngine_NoRowException $e) {
            return -1;
        }
        while ($user != null && $user->getParentUserId() != $ancestorId) {
            $count++;
            $user = $user->getParentUser();
        }


        if ($user == null) {
            return -1;
        }
        return $count;
    }
    
    private function merge($arr1, $arr2) {
        foreach($arr2 as $k => $v) {
            $arr1[$k] = $v;
        }

        return $arr1;
    }
}
?>
