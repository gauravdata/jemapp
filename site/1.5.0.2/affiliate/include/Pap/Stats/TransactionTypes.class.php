<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Matej Kendera
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: TemplateService.class.php 22443 2008-11-21 14:10:51Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Pap_Stats_TransactionTypes extends Gpf_Object {

    /**
     * @service campaign read
     * @return Gpf_Data_RecordSet
     */
    public function getActionTypes(Gpf_Rpc_Params $params) {
        $actionTypesHeader = array(Pap_Db_Table_CommissionTypes::TYPE, Pap_Db_Table_CommissionTypes::NAME, Pap_Db_Table_CommissionTypes::ID);

        $data = new Gpf_Rpc_Data($params);
        $filters = $data->getFilters();
        
        $statsParams = new Pap_Stats_Params();
        $statsParams->setCampaignId($filters->getFilterValue('campaignid'));
        $statsParams->setBannerId($filters->getFilterValue('bannerid'));
        
        $transactionTypeStats = new Pap_Stats_TransactionTypeStats($statsParams);
        $transactionTypes = $transactionTypeStats->getTypes();
        
        $actionTypesRecordSet = new Gpf_Data_RecordSet();
        $actionTypesRecordSet->setHeader($actionTypesHeader);
        
        foreach ($transactionTypes as $transactionType) {
            if ($transactionType->getType() != Pap_Common_Constants::TYPE_SALE && $transactionType->getType() !=Pap_Common_Constants::TYPE_ACTION && $transactionType->getType() != Pap_Common_Constants::TYPE_RECURRING) {
                continue;
            }
            $actionTypesRecordSet->add(new Gpf_Data_Record($actionTypesHeader, array($transactionType->getType(), $transactionType->getName(), $transactionType->getCommissionTypeId())));
        }
        return $actionTypesRecordSet;
    }
}

?>
