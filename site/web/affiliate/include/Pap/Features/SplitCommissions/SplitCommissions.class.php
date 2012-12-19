<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Galik
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
class Pap_Features_SplitCommissions_SplitCommissions extends Gpf_Plugins_Handler {
    private static $instance = false;

    /**
     * @return Pap_Features_SplitCommissions_SplitCommissions
     */
    public static function getHandlerInstance() {
        if(!self::$instance)  {
            return self::$instance = new Pap_Features_SplitCommissions_SplitCommissions();
        }
        return self::$instance;
    }

    private $splitCommissions = array();
    private $saleId = null;
    private $minCommission = 0;

    /**
     * @var Pap_Tracking_Common_VisitorAffiliateCollection
     */
    private $visitorAffiliateCollection = null;

    public function clearData() {
        $this->splitCommissions = array();
        $this->saleId = null;
        $this->minCommission = 0;
        $this->visitorAffiliateCollection = null;
    }

    public function applySplitCommission(Pap_Common_TransactionCompoundContext $transactionCompoundContext) {
        if ($transactionCompoundContext->getContext()->isManualAddMode()) {
            return;
        }

        if ($transactionCompoundContext->getContext()->getActionType() != Pap_Common_Constants::TYPE_ACTION &&
        $transactionCompoundContext->getContext()->getActionType() != Pap_Common_Constants::TYPE_SALE) {
            return;
        }

        if ($transactionCompoundContext->getContext()->getTrackingMethod() == Pap_Common_Transaction::TRACKING_METHOD_DEFAULT_AFFILIATE) {
            $transactionCompoundContext->getContext()->debug('SplitCommissions - default affiliate - not splitting commission. STOPPED');
            return;
        }

        if ($transactionCompoundContext->getContext()->getTrackingMethod() == Pap_Common_Transaction::TRACKING_METHOD_COUPON) {
            $transactionCompoundContext->getContext()->debug('SplitCommissions - coupon - not splitting commission. STOPPED');
            return;
        }

        if ($transactionCompoundContext->getContext()->getTrackingMethod() == Pap_Common_Transaction::TRACKING_METHOD_FORCED_PARAMETER) {
            $transactionCompoundContext->getContext()->debug('SplitCommissions - forced parameter - not splitting commission. STOPPED');
            return;
        }

        $userId = $transactionCompoundContext->getContext()->getUserObject()->getId();
        if (!isset($this->splitCommissions[$userId])) {
            $transactionCompoundContext->setSaveTransaction(false);
            return;
        }

        if ($this->splitCommissions[$userId] == 0) {
            unset($this->splitCommissions[$userId]);
            $transactionCompoundContext->setSaveTransaction(false);
            return;
        }

        $commission = $this->splitCommissions[$userId] * $transactionCompoundContext->getTransaction()->getCommission() / 100;
        if ($this->minCommission != 0 && $commission < $this->minCommission) {
            unset($this->splitCommissions[$userId]);
            $transactionCompoundContext->setSaveTransaction(false);
            return;
        }

        $transactionCompoundContext->getTransaction()->setCommission($commission);

        $transactionCompoundContext->getTransaction()->setSaleId($this->saleId);

        $transactionCompoundContext->getTransaction()->setSplit($this->splitCommissions[$userId] / 100);

        $this->setFirstAndLastClick($transactionCompoundContext->getTransaction(), $transactionCompoundContext->getContext()->getVisitorAffiliate()->getUserId());
    }


    public function saveCommissions(Pap_Common_ActionProcessorCompoundContext $actionProcessorCompoundContext) {
        $context = $actionProcessorCompoundContext->getContext();
        $cache = $actionProcessorCompoundContext->getActionProcessor()->getVisitorAffiliatesCache();

        $context->debug('SplitCommissions save started');

        $commType = $this->getCommissionType($context);

        if ($context->getTrackingMethod() == Pap_Common_Transaction::TRACKING_METHOD_DEFAULT_AFFILIATE) {
            $context->debug('SplitCommissions - default affiliate. STOPPED');
            return;
        }

        if ($context->getTrackingMethod() == Pap_Common_Transaction::TRACKING_METHOD_FORCED_PARAMETER) {
            $context->debug('SplitCommissions - forced parameter. STOPPED');
            return;
        }

        if ($context->getTrackingMethod() == Pap_Common_Transaction::TRACKING_METHOD_COUPON) {
            $context->debug('SplitCommissions - coupon. STOPPED');
            return;
        }

        $this->saleId = Gpf_Common_String::generateId();

        $firstAffBonus = $this->loadCommissionTypeAttributeValue($commType->getId(), Pap_Features_SplitCommissions_SplitCommissionsForm::FIRST_AFF_BONUS);
        $lastAffBonus = $this->loadCommissionTypeAttributeValue($commType->getId(), Pap_Features_SplitCommissions_SplitCommissionsForm::LAST_AFF_BONUS);
        $this->minCommission = $this->loadCommissionTypeAttributeValue($commType->getId(), Pap_Features_SplitCommissions_SplitCommissionsForm::MIN_COMMISSION);

        Pap_Features_SplitCommissions_VisitorAffiliateCheckCompatibility::getHandlerInstance()->checkCompatibility($context->getVisitorId(), $cache);

        $visitorAffiliates = $cache->getVisitorAffiliateAllRows($context->getVisitorId());

        Pap_Tracking_Visit_VisitorAffiliateCache::sortVisitorAffiliatesByDateVisit($visitorAffiliates);

        $this->setSplitCommissions($this->getSplitCommissions($firstAffBonus, $lastAffBonus, $visitorAffiliates));

        $this->visitorAffiliateCollection = $visitorAffiliates;

        $this->processVisitorAffiliates($visitorAffiliates, $actionProcessorCompoundContext);

        $context->debug('SplitCommissions save finished');
        $actionProcessorCompoundContext->setCommissionsAlreadySaved(true);
    }

    protected function setSplitCommissions($splitCommissions){
        $this->splitCommissions = $splitCommissions;
    }

    private function processVisitorAffiliates(
    Pap_Tracking_Common_VisitorAffiliateCollection $visitorAffiliates,
    Pap_Common_ActionProcessorCompoundContext $actionProcessorCompoundContext) {
        $processedAffiliates = array();

        $mailContext = null;

        foreach ($visitorAffiliates as $visitorAffiliate) {
            if (!$visitorAffiliate->isValid()) {
                continue;
            }
            if (isset($processedAffiliates[$visitorAffiliate->getUserId()])) {
                continue;
            }
            $processedAffiliates[$visitorAffiliate->getUserId()] = true;

            $contextProcessor = new Pap_Features_SplitCommissions_ContextProcessor($actionProcessorCompoundContext->getContext());
            $contextProcessor->recognizeAffiliate($visitorAffiliate);
            if(!$contextProcessor->isValid()) {
                continue;
            }
            $context = $contextProcessor->getContext();

            $actionProcessorCompoundContext->getActionProcessor()->runSettingLoadersAndSaveCommissions($context);

            if ($visitorAffiliates->getValid($visitorAffiliates->getValidSize()-1)->getId() == $visitorAffiliate->getId()) {
                $actionProcessorCompoundContext->getContext()->setTransactionObject($context->getTransactionObject());
            }

            if ($mailContext == null &&
            isset($this->splitCommissions[$visitorAffiliate->getUserId()]) &&
            $this->isSendMailContextStatus($context)) {
                $mailContext = $context;
            }
        }

        if ($mailContext != null) {
            $this->sendOnNewSaleNotificationEmail($mailContext);
        }
    }

    private function isSendMailContextStatus(Pap_Contexts_Action $context) {
        return strstr(Gpf_Settings::get(Pap_Features_SplitCommissions_Definition::NOTIFICATION_ON_SALE_SUMMARY_STATUS),
        $context->getTransaction()->getStatus()) !== false;
    }

    protected function sendOnNewSaleNotificationEmail(Pap_Contexts_Action $context) {
        if (!$this->isSendingMailNotificationEnabled($context)) {
            $context->debug('Sending Sale summary mail not enabled. STOPPED');
            return;
        }

        $mail = new Pap_Mail_SplitCommissionsMerchantOnSale($context->getTransaction()->getSaleId());
        $mail->addRecipient(Pap_Common_User::getMerchantEmail());
        try {
            $mail->send();
        } catch (Gpf_Exception $e) {
            $context->debug('Sending Sale summary mail failed, possibly there was no transaction recorded during sale processing. STOPPED');
            return;
        }

        $context->debug('Sending Sale summary mail sended. Ended');
    }

    private function isSendingMailNotificationEnabled(Pap_Contexts_Action $context) {
        if (Gpf_Settings::get(Pap_Features_SplitCommissions_Definition::NOTIFICATION_ON_SALE_SUMMARY) == Gpf::NO) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    protected function getSplitCommissions($firstAffBonus, $lastAffBonus, Pap_Tracking_Common_VisitorAffiliateCollection $visitorAffiliates) {
        $splitCommission = (100 - $firstAffBonus - $lastAffBonus) / ($visitorAffiliates->getValidSize()-1); // first-click affiliate is two times in visitorAffiliates

        $commissions = array();
        for ($i=0;$i<$visitorAffiliates->getValidSize();$i++) {
            $userId = $visitorAffiliates->getValid($i)->getUserId();

            if (!isset($commissions[$userId])) {
                $commissions[$userId] = $splitCommission;
            }

            if ($i == 0) {
                $commissions[$userId] += $firstAffBonus;
            }

            if ($i == $visitorAffiliates->getValidSize()-1) {
                $commissions[$userId] += $lastAffBonus;
            }
        }

        return $commissions;
    }

    /**
     * @param Pap_Contexts_Action $context
     * @return Pap_Db_CommissionType
     */
    protected function getCommissionType(Pap_Contexts_Action $context) {
        $campaign = $context->getCampaignObject();
        
        $actionCode = $context->getActionCodeFromRequest();
        if ($actionCode != '') {
            return $campaign->getCommissionTypeObject(Pap_Common_Constants::TYPE_ACTION, $actionCode, $context->getVisit()->getCountryCode());
        } else {
            return $campaign->getCommissionTypeObject(Pap_Common_Constants::TYPE_SALE, '', $context->getVisit()->getCountryCode());
        }
    }

    /**
     * @return Pap_Db_CommissionTypeAttribute
     */
    protected function loadCommissionTypeAttributeValue($commTypeId, $attributeName) {
        try {
            return Pap_Db_Table_CommissionTypeAttributes::getInstance()->getCommissionTypeAttribute($commTypeId, $attributeName)->getValue();
        } catch (Gpf_Exception $e) {
            return 0;
        }
    }

    private function setFirstAndLastClick(Pap_Common_Transaction $transaction, $contextUserId) {
        $firstAffiliate = $this->visitorAffiliateCollection->getValid(0);
        $lastAffiliate = $this->visitorAffiliateCollection->getValid($this->visitorAffiliateCollection->getValidSize()-1);

        try{
            $visitorAffiliate = $this->visitorAffiliateCollection->getVisitorAffiliateByUserId($contextUserId);
            $transaction->setRefererUrl($visitorAffiliate->getReferrerUrl());
        } catch(Gpf_Exception $e){
            $context->debug($e->getMessage());
            $transaction->setRefererUrl($this->_('Unknown'));
        }

        $this->setFirstClickData($transaction,$firstAffiliate);

        if ($firstAffiliate->getUserId() == $contextUserId){
            $transaction->setAllowFirstClickData(Gpf::YES);
        } else{
            $transaction->setAllowFirstClickData(Gpf::NO);
        }

        $this->setLastClickData($transaction,$lastAffiliate);
        if ($lastAffiliate->getUserId() == $contextUserId){
            $transaction->setAllowLastClickData(Gpf::YES);
        } else{
            $transaction->setAllowLastClickData(Gpf::NO);
        }
    }

    public function setVisitorAffiliateCollection($collection){
        $this->visitorAffiliateCollection = $collection;
    }

    private function setFirstClickData($transaction,$firstAffiliate){
        $transaction->setFirstClickTime($firstAffiliate->getDateVisit());
        $transaction->setFirstClickReferer($firstAffiliate->getReferrerUrl());
        $transaction->setFirstClickIp($firstAffiliate->getIp());
        $transaction->setFirstClickData1($firstAffiliate->getData1());
        $transaction->setFirstClickData2($firstAffiliate->getData2());
    }

    private function setLastClickData($transaction,$lastAffiliate){
        $transaction->setLastClickTime($lastAffiliate->getDateVisit());
        $transaction->setLastClickReferer($lastAffiliate->getReferrerUrl());
        $transaction->setLastClickIp($lastAffiliate->getIp());
        $transaction->setLastClickData1($lastAffiliate->getData1());
        $transaction->setLastClickData2($lastAffiliate->getData2());
    }

    public function initSelectClause(Gpf_SqlBuilder_SelectClause $select) {
        $select->replaceColumn('cnt', 'count(distinct(t.'.Pap_Db_Table_Transactions::SALE_ID.'))', 'cnt');
        $select->replaceColumn('totalcost', 'MAX(t.'.Pap_Db_Table_Transactions::TOTAL_COST.')', 'totalcost');
    }

    public function initGroupBy(Gpf_SqlBuilder_GroupByClause $groupBy) {
        $groupBy->add('t.'.Pap_Db_Table_Transactions::SALE_ID);
    }

    public function processResult(Gpf_SqlBuilder_SelectBuilder $selectBuilder) {
        $innerSelect = new Gpf_SqlBuilder_SelectBuilder();
        $innerSelect->cloneObj($selectBuilder);

        $selectBuilder->select = new Gpf_SqlBuilder_SelectClause();
        $selectBuilder->from = new Gpf_SqlBuilder_FromClause();
        $selectBuilder->where = new Gpf_SqlBuilder_WhereClause();
        $selectBuilder->groupBy = new Gpf_SqlBuilder_GroupByClause();
        $selectBuilder->orderBy = new Gpf_SqlBuilder_OrderByClause();
        $selectBuilder->limit = new Gpf_SqlBuilder_LimitClause();
        $selectBuilder->having = new Gpf_SqlBuilder_HavingClause();

        $selectBuilder->select->add("s.status");
        $selectBuilder->select->add("s.payoutstatus");
        $selectBuilder->select->add("sum(s.cnt)", "cnt");
        $selectBuilder->select->add("sum(s.commission)", "commission");
        $selectBuilder->select->add("sum(s.totalcost)", "totalcost");
        $selectBuilder->from->addSubselect($innerSelect, 's');
        $selectBuilder->groupBy->add("s.status");
        $selectBuilder->groupBy->add("s.payoutstatus");
    }

    public function transactionsStatsBuilderbuildGroupBy(Pap_Stats_Computer_TransactionsStatsBuilderContext $transactionsStatsBuilderContext) {
        $transactionsStatsBuilder = $transactionsStatsBuilderContext->getTransactionsStatsBuilder();
        $transactionsStatsBuilder->getTransactionsSelect()->groupBy->removeByName(Pap_Db_Table_Transactions::TRANSACTION_ID);
        $transactionsStatsBuilder->getTransactionsSelect()->groupBy->add(Pap_Db_Table_Transactions::SALE_ID);
        $transactionsStatsBuilder->getTransactionsSelect()->groupBy->add($transactionsStatsBuilderContext->getGroupColumn());
    }
}

?>
