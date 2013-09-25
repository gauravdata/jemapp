<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: PayAffiliatesForm.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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
class Pap_Merchants_Payout_PayAffiliatesForm extends Gpf_Object {
    
    /**
     * @service pay_affiliate read
     */
    public function exportMassPayFile(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);

        $payoutOption = new Pap_Db_PayoutOption();
        $payoutOption->setID($form->getFieldValue('payoutOptionId'));
        $payoutOption->load();
        
        $this->generateExportFile($payoutOption);
        
        $download = new Gpf_File_Download_String($payoutOption->getExportFileName(),
                                                 $this->generateExportFile($payoutOption, $form->getFieldValue('affiliateNote')));
        $download->setAttachment(true);
        return $download;
    }
    
    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    private function getAffiliatesToPaySelect($payoutOptionId = null) {
        $selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
        $selectBuilder->select->addAll(Pap_Db_Table_Users::getInstance(), 'pu');
        $selectBuilder->select->addAll(Gpf_Db_Table_AuthUsers::getInstance(), 'au');
        $selectBuilder->select->add('SUM('.Pap_Db_Table_Transactions::COMMISSION.')', 'amountRaw');
        
        $selectBuilder->from->add(Pap_Db_Table_Transactions::getName(), 't');    
        $selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(), 'pu', 't.userid = pu.userid');
        $selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(), 'gu', 'pu.accountuserid = gu.accountuserid');
        $selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(), 'au', 'gu.authid = au.authid');
        
        if ($payoutOptionId != null) {
            $selectBuilder->where->add('pu.'.Pap_Db_Table_Users::PAYOUTOPTION_ID, '=', $payoutOptionId);
        }
        $selectBuilder->where->add('t.'.Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, '=', Pap_Common_Transaction::PAYMENT_PENDING_ID);
        $selectBuilder->groupBy->add(Pap_Db_Table_Transactions::USER_ID);
        
        return $selectBuilder;
    }
    
    private function generateExportFile(Pap_Db_PayoutOption $payoutOption, $affiliateNote = '') {
        $header = new Gpf_Templates_Template($payoutOption->getExportHeaderTemplate(), '', Gpf_Templates_Template::FETCH_TEXT);
        $content = $header->getHTML(); 
        $currency = Pap_Common_Utils_CurrencyUtils::getDefaultCurrency();
        $affiliates = 0;
        $commissions = 0;
        
        $selectBuilder = $this->getAffiliatesToPaySelect($payoutOption->getID());
        foreach ($selectBuilder->getAllRowsIterator() as $row) {
            $user = new Pap_Common_User();
            $user->fillFromRecord($row);
            $payout = new Pap_Common_Payout($user, $currency, $row->get('amountRaw'),$this->generateInvoiceNumber());
            $payout->setAffiliateNote($affiliateNote);
            $affiliates++;
            $commissions += $row->get('amountRaw');
            
            $content .= $payout->getExportRow();
        }

        $footer = new Gpf_Templates_Template($payoutOption->getExportFooterTemplate(), '', Gpf_Templates_Template::FETCH_TEXT);
        $footer->assign('affiliates', $affiliates);
        $footer->assign('commissions', round($commissions, Pap_Common_Utils_CurrencyUtils::getDefaultCurrency()->getPrecision()));
        $content .= $footer->getHTML();
        
        $content = str_replace('\t', "\t", $content);
        $content = str_replace('\n', "\n", $content);
                
        return $content;        
    }
    
    /**
     * @service pay_affiliate read
     * @param $params
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
    	$form = new Gpf_Rpc_Form($params);
    	
    	$form->addField(Pap_Settings::GENERATE_INVOICES, Gpf_Settings::get(Pap_Settings::GENERATE_INVOICES));
    	$form->addField(Pap_Settings::SEND_PAYMENT_TO_AFFILIATE, Gpf_Settings::get(Pap_Settings::SEND_PAYMENT_TO_AFFILIATE));
    	$form->addField(Pap_Settings::SEND_GENERATED_INVOICES_TO_MERCHANT, Gpf_Settings::get(Pap_Settings::SEND_GENERATED_INVOICES_TO_MERCHANT));
    	$form->addField(Pap_Settings::SEND_GENERATED_INVOICES_TO_AFFILIATE, Gpf_Settings::get(Pap_Settings::SEND_GENERATED_INVOICES_TO_AFFILIATE));
    	
    	return $form;
    }
    
    /**
     * @service pay_affiliate write
     * @return Gpf_Rpc_Form
     */
    public function payAffiliates(Gpf_Rpc_Params $params) {
    	$form = new Gpf_Rpc_Form($params);    	    	        

    	$this->saveNotificationSettings($form);       	       
        $currency = Pap_Common_Utils_CurrencyUtils::getDefaultCurrency();
        $payoutHistory = $this->createPayoutHistoryItem($form);
        
        foreach ($this->getAffiliatesToPaySelect()->getAllRowsIterator() as $row) {
            $user = new Pap_Common_User();
            $user->fillFromRecord($row);
            $payout = new Pap_Common_Payout($user, $currency, $row->get('amountRaw'), $this->generateInvoiceNumber());
            $payout->setPayoutHistory($payoutHistory);
            $payout->generateInvoice();
            try {
                $payout->save();
            } catch (Gpf_DbEngine_Row_ConstraintException $e) {
            	$form->setErrorMessage($this->_('Failed to pay affiliates %s', $e->getMessage()));            	
            	continue;
            }
            $this->markTransactionsAsPaid($payout);
            $this->sendEmails($payout);

            Gpf_Plugins_Engine::extensionPoint('PostAffiliate.Pap_Merchants_Payout_PayAffiliatesForm.payAffiliates', $user);
        }           
             
        $form->setInfoMessage($this->_('Affiliates successfully paid'));        
        
        return $form;
    }            
    
    private function sendEmails(Pap_Common_Payout $payout) {
    	if (Gpf_Settings::get(Pap_Settings::SEND_PAYMENT_TO_AFFILIATE) == Gpf::YES) {
        	$payout->sendEmail();
        }
        if (Gpf_Settings::get(Pap_Settings::SEND_GENERATED_INVOICES_TO_MERCHANT) == Gpf::YES) {
        	$payout->sendInvoice(true);
        }
        if (Gpf_Settings::get(Pap_Settings::SEND_GENERATED_INVOICES_TO_AFFILIATE) == Gpf::YES) {
        	$payout->sendInvoice();	
        }
    }
    
    private function saveNotificationSettings(Gpf_Rpc_Form $form) {
    	Gpf_Settings::set(Pap_Settings::SEND_PAYMENT_TO_AFFILIATE, $form->getFieldValue(Pap_Settings::SEND_PAYMENT_TO_AFFILIATE));
    	if ($form->existsField(Pap_Settings::SEND_GENERATED_INVOICES_TO_MERCHANT) && $form->existsField(Pap_Settings::SEND_GENERATED_INVOICES_TO_AFFILIATE)) {
    		Gpf_Settings::set(Pap_Settings::SEND_GENERATED_INVOICES_TO_MERCHANT, $form->getFieldValue(Pap_Settings::SEND_GENERATED_INVOICES_TO_MERCHANT));
    		Gpf_Settings::set(Pap_Settings::SEND_GENERATED_INVOICES_TO_AFFILIATE, $form->getFieldValue(Pap_Settings::SEND_GENERATED_INVOICES_TO_AFFILIATE));
    	}    	
    }
    
    /**
     * @return Pap_Db_PayoutHistory
     */
    private function createPayoutHistoryItem(Gpf_Rpc_Form $form) {
        $payoutHistory = new Pap_Db_PayoutHistory();
        $payoutHistory->setAffiliateNote($form->getFieldValue("affiliateNote"));
        $payoutHistory->setMerchantNote($form->getFieldValue("paymentNote"));
        $payoutHistory->setDateInserted(Gpf_Common_DateUtils::now());
        $payoutHistory->setAccountId(Gpf_Application::getInstance()->getAccountId());
        $payoutHistory->save();
        
        return $payoutHistory;
    }
    
    protected function getMaxInvoiceNumber(){
    	$selectBuilder = new Gpf_SqlBuilder_SelectBuilder();
    	$selectBuilder->select->add('MAX('.Pap_Db_Table_Payouts::INVOICENUMBER.')', 'invoiceNumberMax');
    	$selectBuilder->from->add(Pap_Db_Table_Payouts::getName(), 't');
    	$row = $selectBuilder->getOneRow();
    	if ($selectBuilder->getAllRows()->getSize() == 0) {
    		return 0;
    	}
    	return $row->get('invoiceNumberMax');
    }
    
    protected function generateInvoiceNumber(){
    	return $this->getMaxInvoiceNumber() + 1;
    }        
    
    private function markTransactionsAsPaid(Pap_Common_Payout $payout) {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->set->add(Pap_Db_Table_Transactions::PAYOUT_STATUS, Pap_Common_Transaction::PAYOUT_PAID);
        $update->set->add(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, $payout->getPayoutHistoryId());
        $update->from->add(Pap_Db_Table_Transactions::getName());
        $update->where->add(Pap_Db_Table_Transactions::USER_ID, "=", $payout->getUserId());
        $update->where->add(Pap_Db_Table_Transactions::PAYOUTHISTORY_ID, '=', Pap_Common_Transaction::PAYMENT_PENDING_ID);
        $update->execute();
    }
}

?>
