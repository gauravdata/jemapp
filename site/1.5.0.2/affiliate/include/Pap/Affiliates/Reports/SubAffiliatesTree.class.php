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
class Pap_Affiliates_Reports_SubAffiliatesTree extends Pap_Common_Reports_AffiliateTreeBase {

    protected function addWhereCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $parentUserId) {
        if($parentUserId == '') {
            $parentUserId = Gpf_Session::getAuthUser()->getPapUserId();
        }
        $selectBuilder->where->add('u.parentuserid', '=', $parentUserId);
    }


    protected function loadItems($userId)  {
        $maxTiers = Gpf_Settings::get(Pap_Settings::TIERS_VISIBLE_TO_AFFILIATE);
        if ($maxTiers == -1) {
            return parent::loadItems($userId);
        }

        $currentUserId = Gpf_Session::getAuthUser()->getPapUserId();
        $numberOfTiers = -1;
        if($userId != '') {
            try {
                $numberOfTiers = $this->getNumberOfTiersToAncestor($userId, $currentUserId);
            } catch (Gpf_Exception $e) {
            }
        }

        if ($userId == '' && $maxTiers == 0) {
            return new Gpf_Data_RecordSet();
        }

        if ($numberOfTiers >= $maxTiers-2) {
            $lastTier = true;
        } else {
            $lastTier = false;
        }
        return parent::loadItems($userId, $lastTier);

    }
    protected function getFiltersRecordset() {
    	 $filters = Gpf_Db_Table_Filters::getInstance();
    	 return $filters->getFilters('subaffiliatetree');
    }

    protected function addFilters(Gpf_Rpc_Params $params) {       
        $recordset = $this->getFiltersRecordset();
        $fields = null;
    	foreach ($recordset as $record) {
            $fields[] = array($record->get(Gpf_Db_Table_FilterConditions::CODE), $record->get(Gpf_Db_Table_FilterConditions::OPERATOR), $record->get(Gpf_Db_Table_FilterConditions::VALUE));
        }
        $params->add('filters', $fields);

        return $params;
    }

}
?>
