<?php
/**
 * Handle Error Messages
 *
 * PHP Version 5
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */

/**
 * KiTT_Error
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
class KiTT_Error implements KiTT_ErrorInterface
{
    /**
     * Constructor.
     *
     * @param string $message Message, preferably a translation key.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the error message to display.
     *
     * @return string Error message to display.
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * Error json to pass to the javascript.
     *
     * @return string JSON object to pass to the javascript.
     */
    public function json()
    {
        return "{}";
    }
}
