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


class AW_Productupdates_Block_Subscribelink extends AW_Productupdates_Block_Subscribe
{
    protected $_idCounter = null;

    public function getNotificationLabel()
    {              
        if (!$this->_product()->getIsSalable()) {
            return $this->__('Notify me when product is in stock');
        }

        return $this->__('Notify about product updates');
    }  
     
    protected function _product()
    {
        if ($this->getData('product')) {
            return $this->getData('product');
        }

        return Mage::registry('current_product');
    }

    protected function _toHtml()
    {
        if (!$this->_product()) {
            return '';
        }
        $this->_idCounter = uniqid();
        $this->setTemplate('catalog/product/productupdates_link.phtml');

        return parent::_toHtml();
    }

    public function getContent()
    {
        return $this->_toHtml();
    }

    public function getProductId()
    {
        if ($this->_product()) {
            return $this->_product()->getId();
        }
        return $this->getRequest()->getParam('id');
    }

    public function getCounter()
    {
        return $this->_idCounter;
    }

    public function getLinkUrl()
    {
        return $this->getUrl(
            "productupdates/index/subscribe",
            array(
                'id' => $this->getProductId(),
                'type' => $this->getSubscriptionType(),
                '_secure' => Mage::app()->getStore(true)->isCurrentlySecure()
            )
        );
    }

}
