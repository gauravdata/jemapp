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

class Iksanika_Countdown_Model_System_Config_Design
{
    
    const TEXT = 'text';
    const TEXT_BACKGROUND = 'TEXT_BACKGROUND';
    const DIGITS_OLD = 'DIGITS_OLD';
    const DIGITS_OLD2 = 'DIGITS_OLD2';
    
    public function toOptionArray()
    {
        $formats = array(
            array(
                'value' => Iksanika_Countdown_Model_System_Config_Design::TEXT,
                'label' => __('Text Based')
            ),
            array(
                'value' => Iksanika_Countdown_Model_System_Config_Design::TEXT_BACKGROUND,
                'label' => __('Text Based with Background')
            ),
            array(
                'value' => Iksanika_Countdown_Model_System_Config_Design::DIGITS_OLD,
                'label' => __('Old school Digits')
            ),
            array(
                'value' => Iksanika_Countdown_Model_System_Config_Design::DIGITS_OLD2,
                'label' => __('Old school Digits v2')
            ),
        );
        return $formats;
    }
}