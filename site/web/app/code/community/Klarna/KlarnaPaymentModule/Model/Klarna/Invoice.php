<?php
/**
 * Klarna_KlarnaPaymentModule_Model_Klarna_Invoice
 *
 * PHP Version 5.3
 *
 * @category  Payment
 * @package   Klarna_Module_Magento
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */

/**
 * Invoice specific overrides of the shared payment model
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Model_Klarna_Invoice
    extends Klarna_KlarnaPaymentModule_Model_Klarna_Shared
{
    // @codingStandardsIgnoreStart variable name required by Magento
    protected $_code = 'klarna_invoice';
    // @codingStandardsIgnoreEnd
}
