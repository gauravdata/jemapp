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


class AW_Customsmtp_Block_Adminhtml_Mails_View_Body extends Mage_Adminhtml_Block_Template
{
    /**
     * @return AW_Customsmtp_Model_Mail
     */
    public function getMail()
    {
        return Mage::registry('awcsmtp_current_mail');
    }

    public function getMailBody()
    {
        return $this->filterJS($this->getMail()->getBody());
    }

    public function filterJS($html)
    {
        return Mage::helper('customsmtp')->filterJS($html);
    }
}