<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 22-10-14
 * Time: 16:14
 */ 
class Twm_ThreeIsOne_Helper_Rules_Data extends Amasty_Rules_Helper_Data {
    const TYPE_PRICE_ATTR   = 'price_attribute';

    public function getDiscountTypes($asOptions=false)
    {
        $types = parent::getDiscountTypes($asOptions);
        $types[self::TYPE_PRICE_ATTR] = $this->__('Use product price attribute');
        return $types;
    }

}