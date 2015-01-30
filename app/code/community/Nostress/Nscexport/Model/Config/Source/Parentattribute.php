<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Used in creating options Export parent product attribute
 *
 */
class Nostress_Nscexport_Model_Config_Source_Parentattribute
{
    const YES = 1;
    const NO = 0;
    const IF_EMPTY = 2;
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::YES, 'label'=>Mage::helper('adminhtml')->__('Yes')),
            array('value' => self::NO, 'label'=>Mage::helper('adminhtml')->__('No')),
            array('value' => self::IF_EMPTY, 'label'=>Mage::helper('nscexport')->__('If empty'))
        );
    }

}
