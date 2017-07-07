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


class AW_Productupdates_Block_Subscribe extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('productupdates/subscribe.phtml');
    }

    public function getFormUrl()
    {
        $id = $this->getRequest()->getParam('id');
        return $this->getUrl('productupdates/index/subscriptionsend', array('id' => $id, '_secure' => true));
    }    
    
    protected function _product()
    {        
        if (!$this->getData('product')) {
            $this->setData(
                'product',
                Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id', false))
            );
        }
        
        return $this->getData('product');
    }
    
    protected function _prepareLayout()
    {
       if ($this->_product() && $this->_product()->getTypeId() == 'configurable') {
            $this->getLayout()->getBlock('head')->addJs('productupdates/configurable.js');
            $block = $this->getLayout()
                ->createBlock('productupdates/options_configurable', 'productupdates_configurable_options')
                ->setProduct($this->_product())
            ;
            $this->setChild('configurable_options', $block);        
       }
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getSubscriberName()
    {        
        $name = Mage::getModel('core/cookie')->get(AW_Productupdates_IndexController::PUN_COOKIE_NAME);
        if ($name) {
            return $this->escapeHtml($name);
        }
        
        $customer = $this->helper('productupdates')->getCustomer();
        if ($customer) {
            return $customer->getName();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getSubscriberEmail()
    {
        $email = Mage::getModel('core/cookie')->get(AW_Productupdates_IndexController::PUN_COOKIE);
        if ($email) {
            return $this->escapeHtml($email);
        }

        $customer = $this->helper('productupdates')->getCustomer();
        if ($customer) {
            return $customer->getEmail();
        }
        return '';
    }
    
    public function getSubscriptionType()
    {
        return Mage::helper('productupdates')->getSubscriptionType($this);
    } 
    
    public function getProductName()
    {
        return $this->_product()->getName();
    }
}
