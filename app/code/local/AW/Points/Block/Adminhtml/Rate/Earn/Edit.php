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
 * @package    AW_Points
 * @version    1.9.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Points_Block_Adminhtml_Rate_Earn_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'points';
        $this->_controller = 'adminhtml_rate_earn';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('points')->__('Save Rate'));
        $this->_updateButton('delete', 'label', Mage::helper('points')->__('Delete Rate'));
    }

    public function getHeaderText()
    {
        $rate = Mage::registry('points_rate_data');
        if ($rate->getId()) {
            return Mage::helper('points')->__("Edit Rate #%s", $this->escapeHtml($rate->getId()));
        } else {
            return Mage::helper('points')->__('Add Rate');
        }
    }
}