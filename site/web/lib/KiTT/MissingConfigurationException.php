<?php
/**
 * Exception type for missing configuration keys.
 *
 * PHP Version 5.3
 *
 * @category  Payment
 * @package   KiTT
 * @author    David Keijser <david.keijser@klarna.com>
 * @copyright 2013 Klarna AB (http://klarna.com)
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache license v2.0
 * @link      http://integration.klarna.com/
 */

/**
 * Raised when trying to access a configuration option that is not set
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
class KiTT_MissingConfigurationException extends KiTT_Exception
{
    /**
     * The missing configuration key
     *
     * @var string
     */
    public $key;

    /**
     * Construct KiTT_MissingConfigurationException
     *
     * @param string $key the missing configuration key
     */
    public function __construct($key)
    {
        parent::__construct("Missing configuration key: {$key}");
        $this->key = $key;
    }
}
