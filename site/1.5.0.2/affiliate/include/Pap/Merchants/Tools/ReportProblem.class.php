<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Google.class.php 18112 2008-05-20 07:17:10Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Merchants_Tools_ReportProblem extends Gpf_Object  {

    /**
     * @service report_problem write
     * 
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function report(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        
        $subject = 'PAP4: '.$form->getFieldValue('subject');
        
        $message = 'License: '.$form->getFieldValue('licenseId').'<br>'.
                   'Product: <b>'.Gpf_Settings::get(Pap_Settings::BRANDING_TEXT_POST_AFFILIATE_PRO).' '.Gpf_Settings::get(Gpf_Settings_Gpf::VARIATION).'</b><br>'.
                   'Version: <b>'.Gpf_Application::getInstance()->getVersion().'</b><br>'.
                   'Url: <a href="'.Gpf_Paths::getInstance()->getFullBaseServerUrl().'">'.Gpf_Paths::getInstance()->getFullBaseServerUrl().'</a><br>'.
                   '-------------------------------------------------------------------------------<br>'.
                   $form->getFieldValue('message');
        
        $mail = new Gpf_Db_Mail();
        $mail->setSubject($subject);
        $mail->setHtmlBody($message);
        $mail->setFromMail($form->getFieldValue('email'));
        $mail->setRecipients("support@qualityunit.com");
        
        try {
            $mail->insert();
            $mail->scheduleNow(true);
            $form->setInfoMessage($this->_("Email sent"));
        } catch (Exception $e) {
            $form->setErrorMessage($this->_("Error while sending mail"));
        }
        return $form;
    }
}
?>
