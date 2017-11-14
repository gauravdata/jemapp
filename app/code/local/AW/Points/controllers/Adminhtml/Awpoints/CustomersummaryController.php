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


class AW_Points_Adminhtml_Awpoints_CustomersummaryController extends Mage_Adminhtml_Controller_Action
{
    protected function displayTitle()
    {
        if (!Mage::helper('points')->magentoLess14()) {
            $this->_title($this->__('Rewards'))->_title($this->__('Customers'));
        }
        return $this;
    }

    public function indexAction()
    {
        $this
            ->displayTitle()
            ->loadLayout()
            ->_setActiveMenu('promo')
            ->_addContent($this->getLayout()->createBlock('points/adminhtml_customer'))
            ->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/points/customers');
    }

    /**
     * Export customer summary grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'aw_points_customers_summary.csv';
        $block = $this->getLayout()->createBlock('points/adminhtml_customersummary_grid');
        $content = $block ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customer summary grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'aw_points_customers_summary.xml';
        $content    = $this->getLayout()->createBlock('points/adminhtml_customersummary_grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }
}
