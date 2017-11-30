<?php
class AW_Storecredit_Model_Source_Storecredit_Subscribe_State
{
    const NOT_SUBSCRIBED_VALUE  = 0;
    const SUBSCRIBED_VALUE      = 1;
    const UNSUBSCRIBED_VALUE    = 2;

    const NOT_SUBSCRIBED_LABEL  = 'Not Subscribed';
    const SUBSCRIBED_LABEL      = 'Subscribed';
    const UNSUBSCRIBED_LABEL    = 'Unsubscribed';

    public function toOptionArray()
    {
        return array(
            self::NOT_SUBSCRIBED_VALUE  => Mage::helper('aw_storecredit')->__(self::NOT_SUBSCRIBED_LABEL),
            self::SUBSCRIBED_VALUE      => Mage::helper('aw_storecredit')->__(self::SUBSCRIBED_LABEL),
            self::UNSUBSCRIBED_VALUE    => Mage::helper('aw_storecredit')->__(self::UNSUBSCRIBED_LABEL)
        );
    }

    public function getOptionByValue($value)
    {
        $options = $this->toOptionArray();
        if (array_key_exists($value, $options)) {
            return $options[$value];
        }
        return null;
    }

    public function getKeyByValue($value)
    {
        $options = $this->toOptionArray();
        $key = array_search($value, $options);
        if ($key !== false) {
            return $key;
        }
        return null;
    }
}