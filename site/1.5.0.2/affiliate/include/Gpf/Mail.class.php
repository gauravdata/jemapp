<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Mail.class.php 32876 2011-05-26 08:35:11Z mkendera $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 *
 */
require_once 'PEAR.php';
require_once 'Mail.php';
require_once 'Mail/mime.php';
require_once 'Mail/RFC822.php';

/**
 * @package GwtPhpFramework
 */
class Gpf_Mail extends Gpf_Object {
    private $_fromName;
    private $_fromEmail;
    private $_fromAddress;
    private $_replyTo;
    private $_recipients;
    private $_ccRecipients;
    private $_bccRecipients;
    private $_userAgent = '';
    private $_subject = '';
    private $_txtbody = '';
    private $_htmlbody = '';
    private $_headers = array();
    private $_transferMethod;
    private $_transferParams = array();
    private $_attachments = array();
    private $_images = array();

    /**
     * Set from name
     *
     * @param string $name
     * @param string $email
     */
    public function setFrom($name, $email) {
        $this->_fromName = $name;
        $this->_fromEmail = $email;
    }

    public function setFullFromAddress($from) {
        $this->_fromAddress = $from;
    }

    /**
     * Set reply to address
     *
     * @param string $replyto
     */
    public function setReplyTo($replyto) {
        $this->_replyTo = $replyto;
    }

    /**
     * Set email recipients
     *
     * @param string $recipients comma separated list of mail recipients
     */
    public function setRecipients($recipients) {
        $this->_recipients = $recipients;
    }

    /**
     * Set Carbon copy recipients
     *
     * @param string $recipients comma separated list of carbon copy recipients
     */
    public function setCcRecipients($recipients) {
        $this->_ccRecipients = $recipients;
    }

    /**
     * Set Bcc recipients
     *
     * @param string $recipients comma separated list of carbon copy recipients
     */
    public function setBccRecipients($recipients) {
        $this->_bccRecipients = $recipients;
    }

    /**
     * Set User Agent header value
     *
     * @param string $agent
     */
    public function setUserAgent($agent) {
        $this->_userAgent = $agent;
    }

    /**
     * Set subject
     *
     * @param string $subject
     */
    public function setSubject($subject) {
        $this->_subject = $subject;
    }

    /**
     * Set Text body of mail
     *
     * @param string $body
     */
    public function setTxtBody($body) {
        $this->_txtbody = $body;
    }

    /**
     * Set Html body of mail
     *
     * @param string $body
     */
    public function setHtmlBody($body) {
        $this->_htmlbody = $body;
    }

    /**
     * Set mail transfer method
     *
     * @param string $method Can contain value 'smtp' or 'mail'
     */
    public function setTransferMethod($method) {
        $this->_transferMethod = $method;
    }

    /**
     * Set transfer params
     *
     * @param array $params
     */
    public function setTransferParams($params) {
        $this->_transferParams = $params;
    }

    /**
     * Add attachment to mail
     *
     * @param string $filename file name of file
     * @param string $filetype mime type of file
     * @param string $content content of file
     */
    function addAttachment($filename, $filetype, $content) {
        $this->_attachments[] = array('filename'=>$filename, 'filetype' => $filetype, 'content'=>$content);
    }

    /**
     * Add image to mail
     *
     * @param string $filename
     * @param string $content
     */
    function addImage($filename, $content, $filetype) {
        $this->_images[] = array('filename'=>$filename, 'filetype' => $filetype, 'content'=>$content);
    }


    /**
     * Send mail
     *
     * @return boolean
     */
    public function send() {

        if (!strlen($this->_transferMethod)) {
            throw new Gpf_Exception('Mail Transfer Method not set!');
        }

        if(!strlen($this->_recipients)) {
            throw new Gpf_Exception($this->_("Recipients empty"));
        }

        if(!($method = $this->createMethod($this->_transferMethod, $this->_transferParams))) {
            throw new Gpf_Exception("Cannot instantiate mail method: ".$this->_transferMethod);
        }


        $mail = new Mail_mime("\n");
        $mail->_build_params['html_charset'] = 'UTF-8';
        $mail->_build_params['text_charset'] = 'UTF-8';
        $mail->_build_params['head_charset'] = 'UTF-8';
        if (defined('MAIL_HEADER_ENCODING_BASE64')) {
            $mail->_build_params['head_encoding'] = 'base64';
        }

        if (strlen($this->_htmlbody)) {
            $mail->setHTMLBody($this->_htmlbody);
            if (strlen($this->_txtbody)) {
                $mail->setTXTBody($this->_txtbody);
            } else {
                $mail->setTXTBody(Gpf_Mail_Html2Text::convert($this->_htmlbody));
            }
        } else if (strlen($this->_txtbody)) {
            $mail->setTXTBody($this->_txtbody);
        } else {
            throw new Gpf_Exception($this->_("Body of mail not specified"));
        }

        foreach($this->_attachments as $attachment) {
            if (is_array($attachment)) {
                if (strlen($attachment['content'])) {
                    $ret = $mail->addAttachment($attachment['content'],
                    $attachment['filetype'],
                    $attachment['filename'],
                    false);
                } else {
                    $ret = $mail->addAttachment($attachment['filename'],
                    $attachment['filetype'],
                    $attachment['filename'],
                    true);
                }
            } else {
                throw new Gpf_Exception('Unknown attachment type');
            }
            if(PEAR::isError($ret)) {
                throw new Gpf_Exception("Adding attachment error: ".$ret->getMessage());
            }
        }

        foreach($this->_images as $image) {
            if (strlen($image['content'])) {
                $ret = $mail->addHTMLImage($image['content'], $this->getImageContentType($image['filename']), $image['filename'], false);
            } else {
                $ret = $mail->addHTMLImage($image['filename'], $this->getImageContentType($image['filename']), $image['filename'], true);
            }
            if(PEAR::isError($ret)) {
                throw new Gpf_Exception("Adding image error: ".$ret->getMessage());
            }
        }

        if(!$this->initHeaders()) {
            throw new Gpf_Exception($this->_('Failed to init mail headers'));
        }

        if(!($method instanceof Mail)) {
            throw new Gpf_Exception($this->_('Email transfer method error'));
        }

        $body = $mail->get();
        $ret = $method->send($this->_recipients, $mail->headers($this->_headers), $body);
        if(PEAR::isError($ret)) {
            throw new Gpf_Exception($ret->getMessage());
        }
        return true;
    }

    private function initHeaders() {
        if(!strlen($this->_fromEmail) && !strlen($this->_fromAddress)) {
            throw new Gpf_Exception($this->_("From address is empty"));
        }

        $this->addHeader('Date', date('j M Y H:i:s O'));

        if (!strlen($this->_fromAddress)) {
            $from = "";
            if(strlen($this->_fromName)) {
                $from = '"' . $this->_fromName . '" ';
            }
            $this->_fromAddress = $from . '<' . $this->_fromEmail . '>';
        }
        $this->addHeader('From', $this->_fromAddress);
        $this->addHeader('Reply-To',  $this->_replyTo);

        if(!$this->addHeader('To',  $this->_recipients)) {
            throw new Gpf_Exception($this->_("Recipients empty"));
        }

        $this->addHeader('Cc',  $this->_ccRecipients);
        $this->addHeader('Bcc',  $this->_bccRecipients);
        $this->addHeader('User-Agent', $this->_userAgent);
        $this->addHeader('Subject', $this->_subject);

        return true;
    }

    private function addHeader($name, $value) {
        if(!strlen(trim($value))) {
            return false;
        }
        $this->_headers[$name] = trim($value);
        return true;
    }

    /**
     * Return image content type depending on extension of file
     *
     * @param string $filename
     * @return string mime type of image
     */
    function getImageContentType($filename) {
        $path = pathinfo($filename);
        switch(strtolower($path['extension'])) {
            case 'gif':
                $type = IMAGETYPE_GIF;
                break;
            case 'png':
                $type = IMAGETYPE_PNG;
                break;
            default:
                $type = IMAGETYPE_JPEG;
                break;
        }
        return image_type_to_mime_type($type);
    }


    /**
     * Create Transfer method
     *
     * @param string $method
     * @param array $params
     * @return Mail
     */
    public function createMethod($method, $params = '') {
        if($method != 'sendmail' && $method != 'smtp' && $method != 'mail') {
            return false;
        }
        require_once 'Mail/' . $method . '.php';
        $class= 'Mail_' . $method;
        $method = new $class($params);
        return $method;
    }

    /**
     * Explode mail address
     *
     * @param string $inValue
     * @return array
     */
    function prepareEmail($inValue) {
        $obj = new Mail_RFC822();
        $obj->validate = false;
        return $obj->parseAddressList($inValue);
    }

    /**
     * Return clean mail address
     *
     * @param array $email
     * @param int $index
     * @return string
     */
    function getEmailAddress($email, $index = 0) {
        if (is_array($email) &&
        count($email) > 0 &&
        isset($email[$index]->mailbox) &&
        isset($email[$index]->host) &&
        strlen($email[$index]->mailbox) &&
        strlen($email[$index]->host)) {
            return trim($email[$index]->mailbox) . '@' . trim($email[$index]->host);
        } else {
            return '';
        }
    }

    /**
     * Return person name from mail address format
     *
     * @param array $email
     * @param int $index
     * @return string
     */
    function getPersonalName($email, $index = 0) {
        if (is_array($email) &&
        count($email) > 0 &&
        isset($email[$index]->personal) &&
        strlen($email[$index]->personal)) {
            return str_replace('"', '', $email[$index]->personal);
        } else {
            return '';
        }
    }
}

?>
