<?php
/**
 * Exception type for locale errors.
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
 * Exception extension for invalid locale.
 *
 * @category  Payment
 * @package   KlarnaAPI
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
class KiTT_InvalidLocaleException extends KiTT_Exception
{
    /**
     * KiTT_InvalidLocaleException constructor
     *
     * @param mixed $locale The object in question
     */
    public function __construct($locale)
    {
        parent::__construct("({$locale}) is not a valid locale");
    }
}
