<?php
/**
 * Template fields interface
 *
 * PHP Version 5.3
 *
 * @category  Payment
 * @package   KiTT
 * @author    David Keijser <david.keijser@klarna.com>
 * @author    Rickard Dybeck <rickard.dybeck@klarna.com>
 * @copyright 2013 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */

/**
 * KiTT_TemplateFieldsInterface
 *
 * @category  Payment
 * @package   KiTT
 * @author    David Keijser <david.keijser@klarna.com>
 * @author    Rickard Dybeck <rickard.dybeck@klarna.com>
 * @copyright 2013 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
interface KiTT_TemplateFieldsInterface
{
    /**
     * Yields an array usable for our rendering fields in the templates.
     *
     * @return array representation of the fields.
     */
    public function get();
}
