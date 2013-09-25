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

class Iksanika_Countdown_Model_System_Config_Visibleoptions
{
    
    public function toOptionArray()
    {
        $formats = array(
            array(
                'value' => 'showall',
                'label' => 'Show in catalog/products pages'
            ),
            array(
                'value' => 'listpage',
                'label' => 'Show in catalog page'
            ),
            array(
                'value' => 'viewpage',
                'label' => 'Show in product page'
            ),
            array(
                'value' => 'hideall',
                'label' => 'Hide in all pages'
            ),
        );
        return $formats;
    }
}