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


class AW_Customsmtp_Block_Adminhtml_Mails_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_period;

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'customsmtp';
        $this->_mode = 'view';
        $this->_controller = 'adminhtml_mails';
        $this->_removeButton('reset');
        $this->_removeButton('save');
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    public function getEntry()
    {
        return Mage::registry('awcsmtp_current_mail');
    }

    public function filterJS($html)
    {
        return Mage::helper('customsmtp')->filterJS($html);
    }

    public function getHeaderText()
    {
        $this->getChild('form')->setEntry($this->getEntry());
        if ($this->getEntry()->getId()) {
            return $this->__("View Email \"%s\"", $this->filterJS($this->getEntry()->getSubject()));
        } else {
            return $this->__("");
        }
    }

    public function getBackUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('*/*');
    }

    public function getDeleteUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl(
            '*/*/delete', array('id' => $this->getRequest()->getParam('id'))
        );
    }
}