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


class AW_Productupdates_Model_Productupdates extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('productupdates/productupdates');
    }

    public function clearByParams(array $params)
    {        
        $dataByProduct = array(
            'product_id=?' => $params['prod'],
            'subscr_store_id=?' => $params['store'],
            'subscriber_id=?' => $params['key']
        );
        $dataByParent = array(
            'parent=?' => $params['prod'],
            'subscr_store_id=?' => $params['store'],
            'subscriber_id=?' => $params['key']
        );
       
        if ($params['type'] != AW_Productupdates_Model_Source_SubscriptionTypes::GENERAL_SUBSCRIPTION_TYPE) {
            $dataByProduct['subscription_type=?'] = $params['type'];
            $dataByParent['subscription_type=?'] = $params['type'];
        }
        return $this->_getResource()->clearByParams($dataByProduct) ||
               $this->_getResource()->clearByParams($dataByParent);
    }

    /**
     * @param Varien_Object $data
     * @param int $subscriberId
     * @return AW_Productupdates_Model_Productupdates
     */
    public function subscriptionCreate(Varien_Object $data, $subscriberId)
    {
        // prepare data for save
        $this->setData($data->getData())->setData('subscriber_id', $subscriberId);
        // now check active subbscription       
        $subscription = $this->getCollection()->getActiveSubscription($this);
        if ($subscription->getId()) {
            $subscription->addData($this->getData())->save();
        } else {
            $this->save();
        }
        return $this;
    }

}