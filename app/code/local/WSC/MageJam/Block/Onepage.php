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
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout status
 *
 * @category   Mage
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class WSC_MageJam_Block_Onepage extends Mage_Checkout_Block_Onepage_Abstract
{
/**
     * Get 'one step checkout' step data
     *
     * @return array
     */
    public function getSteps()
    {
        $steps = array();
        $stepCodes = $this->_getStepCodes();

        if ($this->isCustomerLoggedIn()) {
            $stepCodes = array_diff($stepCodes, array('login'));
        }

        foreach ($stepCodes as $step) {
        	if ($step==='shipping_method') {
               $this->getCheckout()->setStepData($step, 'allow', true);
            }
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }
        return $steps;
    }

    /**
     * Get active step
     *
     * @return string
     */
    public function getActiveStep()
    {
    	return 'shipping_method';
       // return $this->isCustomerLoggedIn() ? 'shipping_method' : 'login';
    }
    
	/**
     * Get checkout steps codes
     *
     * @return array
     */
    protected function _getStepCodes()
    {
    	// return array('login', 'billing', 'shipping', 'shipping_method', 'payment', 'review');
    	return array('shipping_method', 'payment', 'review');
    }
}
