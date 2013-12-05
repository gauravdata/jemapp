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
 * This package designed for Magento community edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento community edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Rma
 * @version    1.4.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Rma_Block_Adminhtml_Customer_Edit_View_Tabs_Requests_Grid extends AW_Rma_Block_Adminhtml_Rma_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId(self::CUSTOMERS_GRID_ID)
            ->setUseAjax(true);
    }

    public function getGridUrl()
    {
        return $this->getUrl('awrma_admin/adminhtml_rma/customergrid',array('customer_id' => intval($this->getRequest()->getParam('id'))));
    }
}