<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
*   @since Version 1.0.0
*   $Id: TransactionReportsForm.class.php 16622 2008-03-21 09:39:50Z aharsani $
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
class Pap_Merchants_Reports_TransactionReportsForm extends Gpf_View_FormService {
    const DATE_ONE_WEEK = 'OW';
    const DATE_TWO_WEEKS = 'TW';
    const DATE_ONE_MONTH = 'OM';
    
   /**
     * @return Gpf_DbEngine_Row
     */
    protected function createDbRowObject() {
        return new Pap_Db_RawClick();
    }
    
    /**
     * @return string
     */
    protected function getDbRowObjectName() {
        return $this->_("Click");
    }
    
    /**
     *
     * @service click delete
     * @param ids, status
     * @return Gpf_Rpc_Action
     */
    public function deleteClicks(Gpf_Rpc_Params $params) {
        $action = new Gpf_Rpc_Action($params);
        
        $date = array();
    	switch ($action->getParam("status")) {
    		case self::DATE_ONE_WEEK:
    			$filter = new Gpf_SqlBuilder_Filter(array("", "DP", "L7D"));
    	        $date = $filter->addDateValueToArray($date);
    	        $olderThan = "one week";
    			break;
    		case self::DATE_TWO_WEEKS:
    			$dateFrom = Gpf_DbEngine_Database::getDateString(
    			     Gpf_Common_DateUtils::getServerTime(
    			         mktime(0,0,0,date("m"), date("d") - 14, date("Y"))));
                $date = array("dateFrom" => $dateFrom);
                $olderThan = "two weeks";
    			break;
    		case self::DATE_ONE_MONTH:
    			$filter = new Gpf_SqlBuilder_Filter(array("", "DP", "L30D"));
                $date = $filter->addDateValueToArray($date);
                $olderThan = "one month";
    			break;
    	}
    	
    	$action->setInfoMessage($this->_("Raw clicks older than %s are deleted", $olderThan));
        $action->setErrorMessage($this->_("Failed to delete raw clicks"));
        
        $delete = new Gpf_SqlBuilder_DeleteBuilder();
        $delete->from->add(Pap_Db_Table_RawClicks::getName());
        $delete->where->add(Pap_Db_Table_RawClicks::DATETIME, "<", $date["dateFrom"]);
        

        try {
            $delete->delete();
            $action->addOk();
        } catch(Gpf_DbEngine_NoRowException $e) {
            $action->addError();
        }

        return $action;
    }
    
    /**
     * @service click delete
     * @param $ids
     * @return Gpf_Rpc_Action
     */
    public function deleteRows(Gpf_Rpc_Params $params) {
        return parent::deleteRows($params);
    }
}

?>
