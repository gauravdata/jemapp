<?php

class MT_Email_Model_Observer
{
    public function beforeEmailSend($observer)
    {
        $template = $observer->getTemplate();
        if(!$template->isPlain()) {
            $variables = $observer->getVariables();
            $plainText = Mage::getModel('mtemail/template')->getProcessedPlainText($template, $variables);
            $mail = $observer->getMail();
            $mail->setBodyText($plainText);
        }
    }
}
