<?php
class pap_update_4_4_13 {

    public function execute() {
        $this->repairSplitCommissionMailTemplate();
        $this->repairMerchantOnSaleMailTemplate();
    }

    private function repairMerchantOnSaleMailTemplate() {
        $dbTemplate = new Gpf_Db_MailTemplate();
        $dbTemplate->setClassName('Pap_Mail_MerchantOnSale');
        try {
            $dbTemplate->loadFromData();

            $bodyHtml = $dbTemplate->getBodyHtml();
            $bodyHtml = str_replace('{if $status ne \'Approved\'}', '{if $statuscode ne \'A\'}', $bodyHtml);
            $bodyHtml = str_replace('{if $status ne \'Declined\'}', '{if $statuscode ne \'D\'}', $bodyHtml);
            $dbTemplate->setBodyHtml($bodyHtml, false);

            $bodyText = $dbTemplate->getBodyText();
            $bodyText = str_replace('{if $status ne \'Approved\'}', '{if $statuscode ne \'A\'}', $bodyText);
            $bodyText = str_replace('{if $status ne \'Declined\'}', '{if $statuscode ne \'D\'}', $bodyText);
            $dbTemplate->setBodyText($bodyText);

            $dbTemplate->save();
        } catch (Gpf_Exception $e) {
        }
    }

    private function repairSplitCommissionMailTemplate() {
        $isDeleted = $this->deleteSplitCommissionMailTemplateFromDb();
        if ($isDeleted) {
            $this->insertSplitCommissionMailTemplateToDb();
        }
    }

    private function deleteSplitCommissionMailTemplateFromDb() {
        $dbTemplate = new Gpf_Db_MailTemplate();
        $dbTemplate->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
        $dbTemplate->setClassName('Pap_Mail_SplitCommissionsMerchantOnSale');
        try {
            $dbTemplate->loadFromData();
            $dbTemplate->delete();
            return true;
        } catch (Gpf_Exception $e) {
        }
        return false;
    }

    private function insertSplitCommissionMailTemplateToDb() {
        $template = new Pap_Mail_SplitCommissionsMerchantOnSale();
        $template->setup(Gpf_Session::getAuthUser()->getAccountId());
    }

}
?>
