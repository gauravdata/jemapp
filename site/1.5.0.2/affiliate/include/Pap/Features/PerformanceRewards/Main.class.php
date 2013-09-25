<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
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
class Pap_Features_PerformanceRewards_Main extends Gpf_Plugins_Handler {

    /**
     * @var Pap_Common_Transaction
     */
    private $transaction;

    /**
     * @var Gpf_Log_Logger
     */
    private $logger;

    /**
     * @return Pap_Features_PerformanceRewards_Main
     */
    public static function getHandlerInstance() {
        return new Pap_Features_PerformanceRewards_Main();
    }

    public function checkRules(Pap_Common_Transaction $transaction) {
        Gpf_Log::debug('Performance reward started');
        foreach($this->getRules($transaction->getCampaignId()) as $ruleRecord) {
            $rule = new Pap_Features_PerformanceRewards_Rule_Transaction($transaction);
            $rule->fillFromRecord($ruleRecord);
            try {
                Gpf_Log::debug('Processing rule ' . $rule->getId() . ': ' . $rule->getAction());
                $rule->executeAction();
                Gpf_Log::debug('Rule completed');
            } catch (Exception $e) {
                Gpf_Log::error(sprintf('Rule %s failed. Reason: %s', $rule->getId(), $e->getMessage()));
            }
        }
        Gpf_Log::debug('Performance reward ended');
    }
    
    /**
     * @param String $campaignId
     * @return Gpf_SqlBuilder_SelectIterator
     */
    private function getRules($campaignId) {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Pap_Db_Table_Rules::getInstance());
        $select->from->add(Pap_Db_Table_Rules::getName());
        $select->where->add(Pap_Db_Table_Rules::CAMPAIGN_ID, '=', $campaignId);
        return $select->getAllRowsIterator();
    }
}
?>
