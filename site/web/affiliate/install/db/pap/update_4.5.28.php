<?php
class pap_update_4_5_28 extends Gpf_Object {
    public function execute() {
        $this->createDatabase()->execute('ALTER TABLE  `qu_pap_campaigns` ADD  `longdescription` TEXT NULL');

        $select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Gpf_Db_Table_Settings::getInstance());
        $select->from->add(Gpf_Db_Table_Settings::getName());
        $select->where->add(Gpf_Db_Table_Settings::NAME, '=', 'default_campaign_id');
        try {
            $result = $select->getOneRow();
            $previousDefaultCampaign = $result->get(Gpf_Db_Table_Settings::VALUE);
            $campaignForm = new Pap_Merchants_Campaign_CampaignForm();
            $campaignForm->setCampaignDefault($previousDefaultCampaign);
        } catch (Gpf_DbEngine_NoRowException $e) {
        }

        $this->createDatabase()->execute('ALTER TABLE `qu_pap_campaigns` DROP `longdescription`');
    }
}

?>
