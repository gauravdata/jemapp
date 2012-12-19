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
class Pap_Merchants_Reports_AffiliateTree extends Pap_Common_Reports_AffiliateTreeBase {
    
    protected function addWhereCondition(Gpf_SqlBuilder_SelectBuilder $selectBuilder, $parentUserId) {
    	$selectBuilder->where->add('u.deleted', '=', Gpf::NO);
        if($parentUserId == '') {
    		$condition = new Gpf_SqlBuilder_CompoundWhereCondition();
    		$condition->add('u.parentuserid', '=', null, 'OR');
            $condition->add('u.parentuserid', '=', '', 'OR');
            $selectBuilder->where->addCondition($condition);
            return;
    	} 
        $selectBuilder->where->add('u.parentuserid', '=', $parentUserId);
    }
}
?>
