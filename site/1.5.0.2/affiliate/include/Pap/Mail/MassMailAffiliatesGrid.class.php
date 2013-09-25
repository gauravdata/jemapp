<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Viktor Zeman
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
class Pap_Mail_MassMailAffiliatesGrid extends Pap_Merchants_User_AffiliatesGrid {

    private $filterId;
    private $recipients = array();
    private $fromRowNr = 0;

    protected function buildSelect() {
        $template = new Pap_Mail_UserMail();
        $this->buildSelectFromTemplateVariables($template->getTemplateVariables());
    }

    protected function buildSelectFromTemplateVariables(array $vars) {
        foreach ($vars as $varId => $varName) {
            if (array_key_exists($varId, $this->dataColumns) && $varId != 'date'
            && $varId != 'time' && $this->dataColumns[$varId] != null) {
                $this->addSelect($this->dataColumns[$varId]->getName(), $varId);
            }
        }
    }

    protected function initDataColumns() {
        parent::initDataColumns();
        $this->addDataColumn('password', 'au.rpassword');
        $this->addDataColumn('notificationemail', 'au.notificationemail');
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('refid');
        $this->addDefaultViewColumn('firstname');
        $this->addDefaultViewColumn('lastname');
        $this->addDefaultViewColumn('ip');
        $this->addDefaultViewColumn('username');
        $this->addDefaultViewColumn('rstatus');
        $this->addDefaultViewColumn('password');
        $this->addDefaultViewColumn('dateinserted');
        $this->addDefaultViewColumn('dateapproved');
        $this->addDefaultViewColumn('parentuserid');
        $this->addDefaultViewColumn('parentusername');
        $this->addDefaultViewColumn('parentfirstname');
        $this->addDefaultViewColumn('parentlastname');
        for ($i = 1; $i <= 25; $i++) {
            $this->addDefaultViewColumn('data'.$i);
        }
    }

    /**
     * Set allowed recipients
     *
     * @param array $recipients
     */
    public function setRecipients($recipients) {
        $this->recipients = $recipients;
    }

    public function setFilterId($filterId) {
        $this->filterId = $filterId;
    }

    protected function addFilter(Gpf_SqlBuilder_Filter $filter) {
        Gpf_Plugins_Engine::extensionPoint('PostAffiliate.MassMailAffiliatesGrid.addFilter',
        new Gpf_Common_SelectBuilderCompoundRecord($this->_selectBuilder, new Gpf_Data_Record(array('filter'), array($filter))));
        parent::addFilter($filter);
    }

    protected function buildFilter() {
        if ($this->filterId && $this->filterId != 'custom') {
            $sql = new Gpf_SqlBuilder_SelectBuilder();
            $sql->select->addAll(Gpf_Db_Table_FilterConditions::getInstance());
            $sql->from->add(Gpf_Db_Table_FilterConditions::getName());
            $sql->where->add('filterid', '=', $this->filterId);
            $conditions = $sql->getAllRows();

            foreach ($conditions as $condition) {
                $filterArray = array(
                Gpf_SqlBuilder_Filter::FILTER_CODE => $condition->get('code'),
                Gpf_SqlBuilder_Filter::FILTER_OPERATOR => $condition->get('operator'),
                Gpf_SqlBuilder_Filter::FILTER_VALUE => $condition->get('value'));

                $filter = new Gpf_SqlBuilder_Filter($filterArray);
                if (array_key_exists($filter->getCode(), $this->dataColumns)) {
                    $dataColumn = $this->dataColumns[$filter->getCode()];
                    $filter->setCode($dataColumn->getName());
                    $filter->addTo($this->_selectBuilder->where);
                } else {
                    $this->addFilter($filter);
                }
            }
        }
        if (!empty($this->recipients)) {
            $condition = new Gpf_SqlBuilder_CompoundWhereCondition();
            $condition->add('au.'.Gpf_Db_Table_AuthUsers::NOTIFICATION_EMAIL, 'IN', $this->recipients, 'OR');
            $condition->add('au.username', 'IN', $this->recipients, 'OR');
            $this->_selectBuilder->where->addCondition($condition);
        }
    }

    public function setFromRowNr($fromRowNr) {
        $this->fromRowNr = $fromRowNr;
    }

    public function getMaxRowsPerSql() {
        return 50;
    }

    protected function buildLimit() {
        $this->_selectBuilder->limit->set($this->fromRowNr, $this->getMaxRowsPerSql());
    }


}
?>
