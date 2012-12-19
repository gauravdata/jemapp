<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: InvoiceFormatForm.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
class Pap_Merchants_Payout_PayAffiliateData extends Gpf_Object {

    /**
     * @service pay_affiliate read
     * @param $data
     */
    public function load(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);

        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add("au.username","userName");
        $select->select->add("au.firstname","firstName");
        $select->select->add("au.lastname","lastname");
        $select->select->add("SUM(t.".Pap_Db_Table_Transactions::COMMISSION.")", "payout");
        $select->select->add("pu.".Pap_Db_Table_Users::MINIMUM_PAYOUT, "minimumPayout");
        $select->select->add("IFNULL(po.".Gpf_Db_Table_FieldGroups::NAME.", 'undefined')", "payoutMethod");
        $select->from->add(Pap_Db_Table_Transactions::getName(), "t");
        $select->from->addInnerJoin(Pap_Db_Table_Users::getName(), "pu", "t.userid = pu.userid");
        $select->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "pu.accountuserid = gu.accountuserid");
        $select->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), "au", "gu.authid = au.authid");
        $select->groupBy->add("t.".Pap_Db_Table_Transactions::USER_ID);
        $select->from->addLeftJoin(Gpf_Db_Table_FieldGroups::getName(), "po", "pu.payoutoptionid = po.fieldgroupid");
        $select->where->add("t.".Pap_Db_Table_Transactions::USER_ID, "=", $params->get("id"));
        $row = $select->getOneRow();

        $data->setValue("userName", $row->get("userName"));
        $data->setValue("firstName", $row->get("firstName"));
        $data->setValue("lastname", $row->get("lastname"));
        $data->setValue("payout", $row->get("payout"));
        $data->setValue("minimumPayout", $row->get("minimumPayout"));
        $data->setValue("payoutMethod", $row->get("payoutMethod"));

        return $data;
    }
}

?>
