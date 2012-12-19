<?php
/** 
* Magento Module developed by NoStress Commerce 
* 
* NOTICE OF LICENSE 
* 
* This source file is subject to the Open Software License (OSL 3.0) 
* that is bundled with this package in the file LICENSE.txt. 
* It is also available through the world-wide-web at this URL: 
* http://opensource.org/licenses/osl-3.0.php 
* If you did of the license and are unable to 
* obtain it through the world-wide-web, please send an email 
* to info@nostresscommerce.cz so we can send you a copy immediately. 
* 
* @copyright Copyright (c) 2009 NoStress Commerce (http://www.nostresscommerce.cz) 
* 
*/ 

/** 
* @category Nostress 
* @package Nostress_Nscexport
*/

class Nostress_Nscexport_Block_Adminhtml_Nscexport_New_Settings extends Mage_Adminhtml_Block_Widget_Form_Container
{    
    protected $_categoryIds;
    protected $_selectedNodes = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('nscexport/settings.phtml');
    }
    
	protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setCanLoadCalendarJs(true);
        }
        return $this;
    }
    
	/**
     * Retrieve system store model
     *
     * @return Mage_Adminhtml_Model_System_Store
     */
    protected function _getStoreModel() {
        if (is_null($this->_storeModel)) {
            $this->_storeModel = Mage::getSingleton('adminhtml/system_store');
        }
        return $this->_storeModel;
    }

    public function getWebsiteCollection()
    {
        return $this->_getStoreModel()->getWebsiteCollection();
    }

    public function getGroupCollection()
    {
        return $this->_getStoreModel()->getGroupCollection();
    }

    public function getStoreCollection()
    {
        return $this->_getStoreModel()->getStoreCollection();
    }	
}

