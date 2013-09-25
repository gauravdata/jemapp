<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: User.class.php 17743 2008-05-06 08:25:49Z mfric $
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
class Pap_Merchants_Filters extends Gpf_Object {
	
    public function __construct() {
    }
    
    private function setupAffiliateFilters() {
        $filter = $this->addFilter(Gpf_Lang::_runtime('Pending affiliates'),'aff_list','al_pend');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','P');
        $this->addFilterCondition($filter, 'dateinserted','dateRange','dateinserted','DP','A');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Declined affiliates'),'aff_list','al_decl');
        $this->addFilterCondition($filter, 'dateinserted','dateRange','dateinserted','DP','A');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','D');

        $filter = $this->addFilter(Gpf_Lang::_runtime('Approved affiliates'),'aff_list','al_appr');
        $this->addFilterCondition($filter, 'dateinserted','dateRange','dateinserted','DP','A');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','A');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Joined this month'),'aff_list','al_tm');
        $this->addFilterCondition($filter, 'dateinserted','dateRange','dateinserted','DP','TM');
    }
    
    private function setupTransactionFilters() {
       $filter = $this->addFilter(Gpf_Lang::_runtime('Pending commissions'),'transaction_filter','tl_pend');
        $this->addFilterCondition($filter, 'dateinserted','dateRange','dateinserted','DP','A');
        $this->addFilterCondition($filter, 'datepayout','dateRange','datepayout','DP','A');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','P');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Declined commissions'),'transaction_filter','tl_decl');
        $this->addFilterCondition($filter, 'dateinserted','dateRange','dateinserted','DP','A');
        $this->addFilterCondition($filter, 'datepayout','dateRange','datepayout','DP','A');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','D');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Approved commissions'),'transaction_filter','tl_appr');
        $this->addFilterCondition($filter, 'dateinserted','dateRange','dateinserted','DP','A');
        $this->addFilterCondition($filter, 'datepayout','dateRange','datepayout','DP','A');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','A');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Today commissions'),'transaction_filter','tl_t');
        $this->addFilterCondition($filter, 'dateinserted','dateRange','dateinserted','DP','T');
        $this->addFilterCondition($filter, 'datepayout','dateRange','datepayout','DP','A');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','P,A,D');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Yesterday commissions'),'transaction_filter','tl_y');
        $this->addFilterCondition($filter, 'dateinserted','dateRange','dateinserted','DP','Y');
        $this->addFilterCondition($filter, 'datepayout','dateRange','datepayout','DP','A');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','P,A,D');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('This month commissions'),'transaction_filter','tl_tm');
        $this->addFilterCondition($filter, 'dateinserted','dateRange','dateinserted','DP','TM');
        $this->addFilterCondition($filter, 'datepayout','dateRange','datepayout','DP','A');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','P,A,D');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Sales'),'transaction_filter','tl_sales');
        $this->addFilterCondition($filter, 'rtype','default','rtype','E','S');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','P,A,D');
    }
    
    private function setupClickFilters() {
        $filter = $this->addFilter(Gpf_Lang::_runtime('Raw clicks'),'clicks_filter','cl_raw');
        $this->addFilterCondition($filter, 'datetime','dateRange','datetime','DP','A');
        $this->addFilterCondition($filter, 'rtype','default','rtype','IN','R');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Unique clicks'),'clicks_filter','cl_uniq');
        $this->addFilterCondition($filter, 'datetime','dateRange','datetime','DP','A');
        $this->addFilterCondition($filter, 'rtype','default','rtype','IN','U');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Declined clicks'),'clicks_filter','cl_decl');
        $this->addFilterCondition($filter, 'datetime','dateRange','datetime','DP','A');
        $this->addFilterCondition($filter, 'rtype','default','rtype','IN','D');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Today clicks'),'clicks_filter','cl_t');
        $this->addFilterCondition($filter, 'datetime','dateRange','datetime','DP','T');
    }
    
    private function setupQuickReportFilters() {
        $filter = $this->addFilter(Gpf_Lang::_runtime('Today'),'quick_reports','qr_t');
        $this->addFilterCondition($filter, 'datetime','dateRange','datetime','DP','T');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Yesterday'),'quick_reports','qr_y');
        $this->addFilterCondition($filter, 'datetime','dateRange','datetime','DP','Y');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Last 7 days'),'quick_reports','qr_l7d');
        $this->addFilterCondition($filter, 'datetime','dateRange','datetime','DP','L7D');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('This month'),'quick_reports','qr_tm');
        $this->addFilterCondition($filter, 'datetime','dateRange','datetime','DP','TM');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('This year'),'quick_reports','qr_ty');
        $this->addFilterCondition($filter, 'datetime','dateRange','datetime','DP','TY');
    }
    
    private function setupPayoutHistoryFilters() {
        $filter = $this->addFilter(Gpf_Lang::_runtime('This month'),'payouts_history_filter','ph_tm');
        $this->addFilterCondition($filter, 'dateinserted','dateRange','dateinserted','DP','TM');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Last month'),'payouts_history_filter','ph_lm');
        $this->addFilterCondition($filter, 'dateinserted','dateRange','dateinserted','DP','LM');
    }
    
    private function setupPayAffiliateFilters() {
        $filter = $this->addFilter(Gpf_Lang::_runtime('Only above minimum'),'pay_affiliates_filter','pa_minim');
        $this->addFilterCondition($filter, 'reachedMinPayout','default','reachedMinPayout','=','Y');
        $this->addFilterCondition($filter, 'dateinserted','dateRange','dateinserted','DP','A'); 
    }
    
    private function setupDirectLinkFilters() {
        $filter = $this->addFilter(Gpf_Lang::_runtime('Pending Urls'),'dirlinks_list','dl_pend');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','P');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Declined Urls'),'dirlinks_list','dl_decl');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','D');
        
        $filter = $this->addFilter(Gpf_Lang::_runtime('Approved Urls'),'dirlinks_list','dl_appr');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','A');
    }
    
    private function setupMailOutboxFilters() {
        $filter = $this->addFilter(Gpf_Lang::_runtime('Pending Mails'),'mail_outbox','mo_pend');
        $this->addFilterCondition($filter, 'status','default','status','IN','p');
        $this->addFilterCondition($filter, 'scheduled_at','dateRange','scheduled_at','DP','A');
    }
    
    private function setupSubAffTreeFilter() {
        $filter = $this->addFilter('default','subaffiliatetree','afftreed');
        $this->addFilterCondition($filter, 'rstatus','default','rstatus','IN','A');
    }

    public function addDefaultFilters() {
        $this->setupAffiliateFilters();
        $this->setupTransactionFilters();
        $this->setupClickFilters();
        $this->setupQuickReportFilters();
        $this->setupPayoutHistoryFilters();
        $this->setupPayAffiliateFilters();
        $this->setupDirectLinkFilters();
        $this->setupMailOutboxFilters();
        $this->setupSubAffTreeFilter();    
    }
    
    /**
     *
     * @return Gpf_Db_Filter
     */
    private function addFilter($name, $type, $id = null) {
        $filter = new Gpf_Db_Filter();
        if ($id != null) {
            $filter->setFilterId($id);
        }
        $filter->setName($name);
        $filter->setFilterType($type);
        $filter->setNull(Gpf_Db_Table_Filters::USER_ID);
        $filter->setPreset('Y');
        $filter->save();
        return $filter;      
    }
    
    private function addFilterCondition(Gpf_Db_Filter $filter, $fieldId, $sectionCode, $code, $operator, $value) {
        $condition = new Gpf_Db_FilterCondition();
        $condition->setFieldId($fieldId);
        $condition->setFilterId($filter->getId());
        $condition->setSectionCode($sectionCode);
        $condition->setCode($code);
        $condition->setOperator($operator);
        $condition->setValue($value);
        $condition->save();       
    }   
}

?>
