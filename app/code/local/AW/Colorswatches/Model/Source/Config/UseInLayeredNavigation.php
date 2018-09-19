<?php

class AW_Colorswatches_Model_Source_Config_UseInLayeredNavigation
{
    const NO_VALUE = 0;
    const YES_SWATCHES_ONLY_VALUE = 1;
    const YES_ALL_VALUE = 2;

    const NO_LABEL = 'No';
    const YES_SWATCHES_ONLY_LABEL = 'Yes, swatch only';
    const YES_ALL_LABEL = 'Yes, swatch and text';

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public static function toArray()
    {
        $helper = Mage::helper('awcolorswatches');
        return array(
            self::NO_VALUE                => $helper->__(self::NO_LABEL),
            self::YES_SWATCHES_ONLY_VALUE => $helper->__(self::YES_SWATCHES_ONLY_LABEL),
            self::YES_ALL_VALUE           => $helper->__(self::YES_ALL_LABEL),
        );
    }

    /**
     * Options getter
     *
     * @return array
     */
    public static function toOptionArray()
    {
        $return = array();
        foreach (self::toArray() as $value => $label) {
            $return[] = array(
                'value' => $value, 'label' => $label
            );
        }
        return $return;
    }
}