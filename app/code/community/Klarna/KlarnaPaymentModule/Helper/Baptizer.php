<?php
/**
 * File used in order to name input fields
 *
 * PHP Version 5.3
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */

/**
 * Helper class to implement the KiTT_Baptizer interface
 *
 * @category Payment
 * @package  Klarna_Module_Magento
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class Klarna_KlarnaPaymentModule_Helper_Baptizer
    extends Mage_Core_Helper_Abstract
    implements KiTT_Baptizer
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * Construct a Baptizer
     *
     * @param string $prefix value to prepend names with e.g a payment code
     *
     * @return KiTT_Baptizer
     */
    public function init($prefix)
    {
        $this->prefix = "{$prefix}_";
        return $this;
    }

    /**
     * Baptize a field name
     *
     * @param string $baseName original name of field
     *
     * @return string input baptized with prefix
     */
    public function nameField($baseName)
    {
        return "payment[{$this->prefix}{$baseName}]";
    }

    /**
     * Baptize an ID
     *
     * @param string $baseName original id
     *
     * @return string input baptized with prefix
     */
    public function nameId($baseName)
    {
        return "{$this->prefix}{$baseName}";
    }

}
