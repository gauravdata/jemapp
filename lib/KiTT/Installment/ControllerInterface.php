<?php

/**
 * Installment Controller Interface
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
interface KiTT_Installment_ControllerInterface
{
    /**
     * Check if widget should be available
     *
     * @return bool
     */
    public function isAvailable();

    /**
     * Create an Installment Widget
     *
     * @return KiTT_Installment_Widget
     */
    public function createWidget();
}
