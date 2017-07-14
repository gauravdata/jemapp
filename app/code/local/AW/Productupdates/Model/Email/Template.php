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
 * @package    AW_Productupdates
 * @version    2.1.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


if (Mage::helper('productupdates')->extensionEnabled('Ebizmarts_Mandrill')) {
    class AW_Productupdates_Model_Email_Template_Abstract extends Ebizmarts_Mandrill_Model_Email_Template
    {
    }
} else {
    class AW_Productupdates_Model_Email_Template_Abstract extends Mage_Core_Model_Email_Template
    {
    }
}

class AW_Productupdates_Model_Email_Template extends AW_Productupdates_Model_Email_Template_Abstract
{
    const GENERAL_TEMPLATE_ID = 'productupdates_notifications_general_send';

    public function loadDefault($templateId, $locale = null)
    {
        parent::loadDefault($templateId, $locale);
        if ($templateId == self::GENERAL_TEMPLATE_ID) {
            $productObjectRequest = $this->getQueue()->getCatalogProduct()->getProductObjectRequest();
            $this->setTemplateSubject($productObjectRequest->getNotificationTitle());
            $this->setTemplateText($this->combineBody($productObjectRequest->getNotificationText()));
            $this->setId($templateId);
        }
        return $this;
    }

    protected function _helper()
    {
        return Mage::helper('productupdates');
    }

    protected function _store()
    {
        return $this->getQueue()->getStoreId();
    }

    public function combineBody($body)
    {
        $body = "<p>{$body}</p>";
        if ($this->_helper()->config('configuration/unsubscribe', $this->_store())) {
            $body .= "<p>{$this->_helper()->config('configuration/unsubscribetext', $this->_store())}</p>";
        }
        if ($this->_helper()->config('configuration/signature', $this->_store())) {
            $body .= "<p>{$this->_helper()->config('configuration/signaturetext', $this->_store())}</p>";
        }
        return $body;
    }
}