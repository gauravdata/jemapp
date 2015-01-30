<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Customsmtp
 * @version    1.0.8
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Customsmtp_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    private $_saveRange = array();

    public function sendMail(AW_Customsmtp_Model_Mail $mailDataModel, $storeId)
    {
        $config = array(
            'port'     => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_PORT, $storeId),
            'auth'     => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_AUTH, $storeId),
            'username' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_LOGIN, $storeId),
            'password' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_PASSWORD, $storeId),
        );

        $needSSL = Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_SSL, $storeId);
        if (!empty($needSSL)) {
            $config['ssl'] = Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_SSL, $storeId);
        }

        $transport = new Zend_Mail_Transport_Smtp(
            Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_HOST, $storeId), $config
        );
        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host', $storeId));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port', $storeId));

        $mail = $this->getMail();

        $mail->setSubject('=?utf-8?B?' . base64_encode($mailDataModel->getSubject()) . '?=');

        /* Starts from 1.10.1.1 version "TO" holds array values */
        if (!empty($this->_saveRange)) {
            foreach ($this->_saveRange as $range) {
                $mail->addTo($range['email'], '=?utf-8?B?' . base64_encode($range['name']) . '?=');
            }
        } else {
            $mail->addTo(
                $mailDataModel->getToEmail(), '=?utf-8?B?' . base64_encode($mailDataModel->getToName()) . '?='
            );
        }

        if (!array_key_exists('Reply-To', $mail->getHeaders())) {
            $mail->setReplyTo($mailDataModel->getFromEmail(), $mailDataModel->getFromName());
        }

        $mail->setFrom($mailDataModel->getFromEmail(), $mailDataModel->getFromName());

        if ($mailDataModel->getIsPlain()) {
            $mail->setBodyText($mailDataModel->getBody());
        } else {
            $mail->setBodyHTML($mailDataModel->getBody());
        }

        $this->setUseAbsoluteLinks(true);

        try {
            $mail->send($transport); //add $transport object as parameter
            $this->_mail = null;
        } catch (Exception $e) {
            throw($e);
            return false;
        }
        return true;
    }

    public function send($email, $name = null, array $variables = array())
    {
        $storeId = null;
        if (array_key_exists('store', $variables) && $variables['store']) {
            $storeId = $variables['store']->getId();
        } else {
            $storeId = $this->getDesignConfig()->getStore();
        }

        if (
            !Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_ENABLED, $storeId)
            || Mage::getStoreConfig('customsmtp/general/mode', $storeId) == AW_Customsmtp_Model_Source_Mode::CORE
        ) {
            return parent::send($email, $name, $variables);
        }

        /**
         *  If existing order is edited from admin panel, send function is called twice
         *  and for the first time $email VAR has no value. Log exception is created every time
         *  send function is called with $email VAR === NULL
         * 'Zend_Mail_Transport_Exception' with message 'Unable to send mail'
         */

        if (!$this->isValidForSend() || !$email) {
            return false;
        }

        $mail = Mage::getModel('customsmtp/mail');

        if (is_null($name)) {
            $name = substr($email, 0, strpos($email, '@'));
        }

        $variables['email'] = $email;
        $variables['name'] = $name;

        $mail->setBody($this->getProcessedTemplate($variables, true));
        $mail->setIsPlain($this->isPlain());
        $mail->setSubject($this->getProcessedTemplateSubject($variables));

        $mail
            ->setFromName($this->getSenderName())
            ->setFromEmail($this->getSenderEmail())
            ->setReplyTo($this->getReplyTo())
            ->setToName($name)
            ->setToEmail($email)
            ->setTemplateId($this->getTemplateId())
            ->setStoreId(Mage::app()->getStore()->getId())
        ;

        $this->_saveRange = $this->_getToData($email, $name);

        try {
            if (Mage::getStoreConfig('customsmtp/general/mode', $storeId) == AW_Customsmtp_Model_Source_Mode::ON) {
                $this->sendMail($mail, $storeId);
            }
        } catch (Exception $e) {
            if (Mage::getStoreConfig('customsmtp/general/log', $storeId)) {
                if (is_array($this->_saveRange)) {
                    for ($i = 0; $i < count($this->_saveRange); $i++) {
                        Mage::getModel('customsmtp/mail')
                            ->setBody($e->getMessage())
                            ->setToName($this->_saveRange[$i]['name'])
                            ->setToEmail($this->_saveRange[$i]['email'])
                            ->setStatus('failed')
                            ->save()
                        ;
                    }
                } else {
                    $mail->save();
                }
            } else {
                throw $e;
            }
            return false;
        }

        if (Mage::getStoreConfig('customsmtp/general/log', $storeId)) {
            if (is_array($this->_saveRange)) {
                $mailData = $mail->getData();
                for ($i = 0; $i < count($this->_saveRange); $i++) {
                    Mage::getModel('customsmtp/mail')
                        ->setData($mailData)
                        ->setToName($this->_saveRange[$i]['name'])
                        ->setToEmail($this->_saveRange[$i]['email'])
                        ->save()
                    ;
                }
            } else {
                $mail->save();
            }
        }

        return true;
    }

    private function _getToData($email, $name)
    {
        $range = array();
        $mail = $this->getMail();
        $email = array_merge((array)$email, $mail->getRecipients());
        if (!is_array($name)) {
            $name = (array)$name;
        }

        for ($i = (count($email) - 1); $i >= 0; $i--) {
            if (!isset($name[$i])) {
                $name[$i] = substr($email[$i], 0, strpos($email[$i], '@'));
            }
            if (isset($name[$i]) && !is_array($name[$i]) && empty($name[$i])) {
                $name[$i] = substr($email[$i], 0, strpos($email[$i], '@'));
            }
            $range[$i]['email'] = $email[$i];
            $range[$i]['name'] = $name[$i];
        }
        return $range;
    }
}