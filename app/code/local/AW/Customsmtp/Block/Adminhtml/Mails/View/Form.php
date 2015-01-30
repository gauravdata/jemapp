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


class AW_Customsmtp_Block_Adminhtml_Mails_View_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData();
        } elseif ($this->getEntry()) {
            $data = $this->getEntry()->getData();
        } else {
            $data = array();
        }

        return parent::_prepareForm();
    }

    public function getMail()
    {
        return Mage::registry('awcsmtp_current_mail');
    }

    public function _toHtml()
    {
        $headerHtml = Mage::app()
            ->getLayout()
            ->createBlock('customsmtp/adminhtml_mails_view_body')
            ->setTemplate('aw_customsmtp/mail/view/header.phtml')
            ->toHtml()
        ;
        $bodyHtml = Mage::app()
            ->getLayout()
            ->createBlock('customsmtp/adminhtml_mails_view_body')
            ->setTemplate('aw_customsmtp/mail/view/body.phtml')
            ->toHtml()
        ;

        return $headerHtml . $bodyHtml;
    }
}
