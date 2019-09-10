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


class AW_Productupdates_Model_Mysql4_Subscribers_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('productupdates/subscribers');
    }

    public function updateSubscriber($data)
    {
        $this->getSelect()->where('reg_id = ?', $data['regId']);

        foreach ($this as $subscriber) {
            $subscriber->setFullname("{$data['fullname']}");

            if ($subscriber->getEmail() != $data['email']) {
                $this->getSelect()->reset();
                $this->getSelect()->from(array('main_table' => $this->getTable('productupdates/subscribers')))
                        ->where('email = ?', $data['email']);
                $this->load();
                if ($this->getSize() === 0) {
                    $subscriber->setEmail($data['email']);
                }
            }
            $subscriber->save();
        }
    }

    public function getActiveSubscriber($data, $check = true)
    {
        foreach ($data as $key => $info) {
            $this->getSelect()->orWhere("$key = ?", $info);
        }

        if ($check) {
            return $this->getConnection()->fetchOne($this->getSelect());
        }

        return $this->getFirstItem();
    }

    public function getSelectCountSql()
    {      
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);

        $countSelect->from('', 'COUNT(*)');
        return $countSelect;
    }

}