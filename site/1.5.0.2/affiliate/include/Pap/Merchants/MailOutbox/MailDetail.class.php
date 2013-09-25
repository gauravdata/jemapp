<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: InvoiceFormatForm.class.php 16653 2008-03-25 10:42:12Z mjancovic $
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

class Pap_Merchants_MailOutbox_MailDetail extends Pap_Common_Overview_OverviewBase {

    public function load(Gpf_Rpc_Params $params) {
    	$data = new Gpf_Rpc_Data($params);

    	return $data;
    }

    private function formatAttachments(Gpf_DbEngine_Row_Collection $attachements) {
    	if ($attachements->getSize() > 0) {
    		$output = '';
    		foreach ($attachements as $attachement) {
    			$output .= "<a href=\"".$attachement->getUrl()."\" target=\"_blank\">".$attachement->getFileName().'</a>, ';
    		}
    		return substr($output,0,strlen($output)-2);
    	} else {
    		return "No attachments";
    	}
    }

    /**
     *
     * @param $outboxid
     * @return Gpf_Db_Mail
     */
    private function loadMailFromOutbox($outboxid) {
    	$select = new Gpf_SqlBuilder_SelectBuilder();
        $select->select->addAll(Gpf_Db_Table_Mails::getInstance(),'m');
        $select->from->add(Gpf_Db_Table_MailOutbox::getName(), 'mo');
        $select->from->addInnerJoin(Gpf_Db_Table_Mails::getName(), 'm',
            'm.'.Gpf_Db_Table_Mails::ID.' = mo.'.Gpf_Db_Table_MailOutbox::MAIL_ID);
        $select->where->add("mo.".Gpf_Db_Table_MailOutbox::ID, "=", $outboxid);
        $mail = new Gpf_Db_Mail();
        $mail->fillFromSelect($select);
        return $mail;
    }

    /**
     * @service mail_outbox read
     * @param $fields
     */
    public function mailDetails(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);
        $search = $data->getFilters()->getFilter("outboxid");
        if (sizeof($search) == 1) {
            $id = $search[0]->getValue();
        }

        $mail = $this->loadMailFromOutbox($id);
        $mailTemplateObj = new Gpf_Db_MailTemplate();
        $data->setValue("subject", $mail->getSubject());
        $data->setValue("body_html", $mailTemplateObj->previewIncludedImages($mail->getHtmlBody()));
        $data->setValue("body_text", $mail->getTextBody());
        $data->setValue("attachments", $this->formatAttachments($mail->getAttachements()));

        return $data;
    }
}
?>
