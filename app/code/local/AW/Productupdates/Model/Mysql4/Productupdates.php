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


class AW_Productupdates_Model_Mysql4_Productupdates extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('productupdates/productupdates', 'productupdates_id');
    }
    
    /**
     * 
     * @param array $websites
     * @param array $stores
     * @return array
     */    
    public function getStoresByWebsite($websites = null, $stores = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(
                $this->getTable('core/store'),
                array('website_id', 'stores' => new Zend_Db_Expr('GROUP_CONCAT(store_id)'))
            )
            ->group('website_id')
        ;
        if (!empty($websites)) {
            $select->where('website_id IN (?)', $websites);
        }
        if (!empty($stores)) {
            $select->where('store_id IN (?)', $stores);
        }

        $data = $this->_getReadAdapter()->fetchAll($select);
        $storesByWebsite = array();
        foreach ($data as $scope) {
            $storesByWebsite[$scope['website_id']] = $scope['stores'];            
        }
        return $storesByWebsite;      
    }
    
    public function clearByParams($where)
    {
        return $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
    }
}