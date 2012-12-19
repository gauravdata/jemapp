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
class Pap_Common_Reports_PayoutsGridBase extends Gpf_View_GridService {

    protected function initViewColumns() {
        $this->addViewColumn(Pap_Db_Table_Payouts::USER_ID, $this->_("User Id"), true);
        $this->addViewColumn("dateinserted", $this->_("Payment date"), false, Gpf_View_ViewColumn::TYPE_DATETIME);
        $this->addViewColumn("username", $this->_("Username"), false);
        $this->addViewColumn("name", $this->_("Name"), false);
        $this->addViewColumn(Pap_Db_Table_Payouts::AMOUNT, $this->_("Amount"), true);
        $this->addViewColumn(Pap_Db_Table_Payouts::AFFILIATE_NOTE, $this->_("Note to affiliate"), true);
        $this->addViewColumn(Pap_Db_Table_Payouts::INVOICENUMBER, $this->_("Invoice number"), true);
        $this->addViewColumn(self::ACTIONS, $this->_("Actions"), false);
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn(Pap_Db_Table_Payouts::ID);
        $this->addDataColumn(Pap_Db_Table_Payouts::USER_ID, "p.userid");
        $this->addDataColumn("username");
        $this->addDataColumn("firstname");
        $this->addDataColumn("lastname");
        $this->addDataColumn("symbol");
        $this->addDataColumn("wheredisplay");
        $this->addDataColumn("dateinserted", "ph.dateinserted");
        $this->addDataColumn("payouthistoryid", "p.payouthistoryid");
        $this->addDataColumn("affiliatenote", "ph.affiliatenote");
        $this->addDataColumn(Pap_Db_Table_Payouts::AMOUNT);
        $this->addDataColumn(Pap_Db_Table_Payouts::CURRENCY_ID, "p.currencyid");
        $this->addDataColumn(Pap_Db_Table_Payouts::INVOICE);
        $this->addDataColumn(Pap_Db_Table_Payouts::INVOICENUMBER, "p.invoicenumber");
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn("name", '', 'A');
        $this->addDefaultViewColumn("dateinserted", '', 'A');
        $this->addDefaultViewColumn("amount", '', 'A');
        $this->addDefaultViewColumn("affiliatenote", '', 'A');
        $this->addActionView();
    }

    protected function addActionView() {        
        if(Gpf_Settings::get(Pap_Settings::GENERATE_INVOICES) == Gpf::YES) {
            $this->addDefaultViewColumn(self::ACTIONS, '', 'N');
        }
    }

    function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_Payouts::getName(), "p");
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_PayoutsHistory::getName(), "ph", "p.payouthistoryid = ph.payouthistoryid");
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), "pu", "p.userid = pu.userid");
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), "gu", "pu.accountuserid = gu.accountuserid");
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), "au", "gu.authid = au.authid");
        $this->_selectBuilder->from->addLeftJoin(Gpf_Db_Table_Currencies::getName(), "c", "p.currencyid = c.currencyid");
    }
}
?>
