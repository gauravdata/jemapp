<?php

/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Countdown
 * @copyright  Copyright (c) 2013 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */

class Iksanika_Countdown_Model_System_Config_Formating
{
    const SEC = 'sec';
    const MIN_SEC = 'min_sec';
    const HOURS_MIN_SEC = 'hours_min_sec';
    const DAYS_HOURS_MIN_SEC = 'days_hours_min_sec';
    
    public function toOptionArray()
    {
        $formats = array(
            array(
                'value' => Iksanika_Countdown_Model_System_Config_Formating::SEC,
                'label' => __('Sec')
            ),
            array(
                'value' => Iksanika_Countdown_Model_System_Config_Formating::MIN_SEC,
                'label' => __('Mins:Sec')
            ),
            array(
                'value' => Iksanika_Countdown_Model_System_Config_Formating::HOURS_MIN_SEC,
                'label' => __('Hours:Mins:Sec')
            ),
            array(
                'value' => Iksanika_Countdown_Model_System_Config_Formating::DAYS_HOURS_MIN_SEC,
                'label' => __('Days.Hours:Mins:Sec')
            ),
        );
        return $formats;
    }
}