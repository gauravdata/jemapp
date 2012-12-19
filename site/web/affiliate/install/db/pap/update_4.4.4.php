<?php
class pap_update_4_4_4 {

    public function execute() {
        $this->updateNullDateVisits();
        $this->updateNullValidTo();
        $this->updateValidToDate(3652); //forever
    }

    private function updateNullDateVisits() {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Pap_Db_Table_VisitorAffiliates::getName());
        $update->set->add(Pap_Db_Table_VisitorAffiliates::DATEVISIT, Gpf_Common_DateUtils::now());
        $update->where->add(Pap_Db_Table_VisitorAffiliates::DATEVISIT, 'is', 'NULL', 'AND', false);
        $update->update();
    }

    private function updateNullValidTo() {
        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->add('campaignid');
        $select->select->add('cookielifetime');
        $select->from->add(Pap_Db_Table_Campaigns::getName());
        $recordSet = $select->getAllRows();

        foreach ($recordSet as $record) {
            $campaign = new Pap_Common_Campaign();
            $campaign->setId($record->get('campaignid'));
            $campaign->setCookieLifetime($record->get('cookielifetime'));
            $this->updateValidityFor($campaign);
        }
    }

    private function updateValidityFor(Pap_Common_Campaign $campaign) {
        $context = new Pap_Contexts_BackwardCompatibility();
        $context->setCampaignObject($campaign);
        $lifetime = ceil(Pap_Tracking_Cookie::getCookieLifeTimeInDays($context));

        $this->updateValidToDate($lifetime, $campaign->getId());
    }

    private function updateValidToDate($lifetime, $campaignId = null) {
        $update = new Gpf_SqlBuilder_UpdateBuilder();
        $update->from->add(Pap_Db_Table_VisitorAffiliates::getName());
        $update->set->add(Pap_Db_Table_VisitorAffiliates::VALIDTO,
            'DATE_ADD('.Pap_Db_Table_VisitorAffiliates::DATEVISIT.', INTERVAL '.$lifetime.' DAY)', false);
        $update->where->add(Pap_Db_Table_VisitorAffiliates::VALIDTO, 'is', 'NULL', 'AND', false);
        if ($campaignId !== null) {
            $update->where->add(Pap_Db_Table_VisitorAffiliates::CAMPAIGNID, '=', $campaignId);
        }
        $update->update();
    }

}
?>
