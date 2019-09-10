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


class AW_Productupdates_Model_Subscribers extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('productupdates/subscribers');
    }
 
    public function validateEmail($email)
    {
        $item = $this->getCollection()->addFieldToFilter('email', array('eq' => $email))->getFirstItem();
        if ($item->getSubscriberId()) {
            return $item->getSubscriberId();
        }
        return false;
    }

    public function subscribeUser(Varien_Object $info)
    {
        if ($info->isEmpty()) {
            throw new Exception($this->_helper()->__('No user info'));
        }
        $this->setData('user_info_data', $info);
        // process logged in customers
        $customer = $info->getCustomer();
        if ($customer) {
            $data = array();
            if ($customer->getId()) {
                $data['reg_id'] = $customer->getId();
            }
            if ($customer->getEmail()) {
                $data['email'] = $customer->getEmail();
            }
            $subscriber = $this->getActiveSubscriber($data, false);
            if ($subscriber->getId()) {
                if ($subscriber->getRegId() != $customer->getId()) {
                    $subscriber->setRegId($customer->getId())->save();
                }
                return $this->_subscriptionCreate($subscriber->getId());
            }
        } elseif ($info->getCookieSubscriber()) {// process cookie subscribers
            return $this->_subscriptionCreate($info->getCookieSubscriber());
        } elseif ($info->getEmail()) {// check if post email is in the database
            $userId = $this->validateEmail($info->getEmail());
            if ($userId) {
                return $this->_subscriptionCreate($userId);
            }
        }
        // first process items
        return $this->_subscriberCreate();
    }

    public function getActiveSubscriber($data = array(), $check = true)
    {
        if (empty($data) || !is_array($data)) {
            throw new Exception($this->_helper()->__('No user info'));
        }
        return $this->getCollection()->getActiveSubscriber($data, $check);
    }

    /**
     * Create subscriber
     */
    protected function _subscriberCreate()
    {
        $instance = new self();
        $instance->setData($this->getUserInfoData()->getData())->save();
        return $this->_subscriptionCreate($instance->getId());
    }

    protected function _subscriptionCreate($subscriberId)
    {
        return $this->_model()->subscriptionCreate($this->getUserInfoData(), $subscriberId);
    }

    protected function _helper($type = 'productupdates')
    {
        return Mage::helper($type);
    }

    protected function _model($type = 'productupdates/productupdates')
    {
        return Mage::getModel($type);
    }

}