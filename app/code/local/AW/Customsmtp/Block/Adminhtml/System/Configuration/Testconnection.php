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
 * @package    AW_Customsmtp
 * @version    1.0.8
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Customsmtp_Block_Adminhtml_System_Configuration_Testconnection
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html
            = <<<HTML
<script type="text/javascript">
var AW_CSMTP_CONFIG = {
    testConnectionUrl: '{$this->getUrl('customsmtp_admin/adminhtml_mails/testconnection')}',
    msgSuccess: '{$this->__('Successfully connected')}',
    msgFailure: '{$this->__('Connection error')}'
};
</script>
HTML;
        $html .= $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel($this->__('Test Connection'))
            ->setStyle("width:280px")
            ->setId('awcsmtp_testconnection')
            ->toHtml()
        ;

        $html .= '<div class="awcsmtp-message"></div>';
        return $html;
    }
}