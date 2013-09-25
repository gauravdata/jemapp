<?php
/**
 * Update step will delete plugin configuration file in every account
 *
 */
class pap_update_4_1_3 {
    public function execute() {

        $template = new Gpf_Db_MailTemplate();
        $template->setClassName('Pap_Mail_MerchantOnContactUs');
        try {
            $template->loadFromData(array(Gpf_Db_Table_MailTemplates::CLASS_NAME));

            //correct typo error
            $template->setBodyHtml(str_replace('afiliate', 'affiliate', $template->getBodyHtml()), false);
            $template->setBodyText(str_replace('afiliate', 'affiliate', $template->getBodyText()));

            //correct name of field subject
            $template->setBodyHtml(str_replace('{$subject}', '{$emailsubject}{*Email subject*}<br/>', $template->getBodyHtml()), false);
            $template->setBodyText(str_replace('{$subject}', '{$emailsubject}{*Email subject*}<br/>', $template->getBodyText()));

            //correct message text variable
            $template->setBodyHtml(str_replace('{$text}', '{$emailtext}{*Email text*}', $template->getBodyHtml()), false);
            $template->setBodyText(str_replace('{$text}', '{$emailtext}{*Email text*}', $template->getBodyText()));

            $template->save();
        } catch (Gpf_Exception $e) {
        }
    }
}
?>
