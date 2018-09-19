<?php
class AW_Colorswatches_Helper_Config extends Mage_Core_Helper_Abstract
{
    const GLOBAL_IS_ENABLED = 'awcolorswatches/global/enabled';

    const PRODUCT_IMAGE_WIDTH = 'awcolorswatches/product_view/width';
    const PRODUCT_IMAGE_HEIGHT = 'awcolorswatches/product_view/height';

    const CATEGORY_IS_ENABLED = 'awcolorswatches/category_view/is_enabled';
    const CATEGORY_IMAGE_WIDTH = 'awcolorswatches/category_view/width';
    const CATEGORY_IMAGE_HEIGHT = 'awcolorswatches/category_view/height';

    const LAYER_USE_AS = 'awcolorswatches/layer/use_as';

    /**
     * @param Mage_Core_Model_Store|null $store
     *
     * @return bool
     */
    public static function isEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig(self::GLOBAL_IS_ENABLED, $store);
    }

    /**
     * @param Mage_Core_Model_Store|null $store
     *
     * @return int|null
     */
    public static function getProductViewImageWidth($store = null)
    {
        $value = Mage::getStoreConfig(self::PRODUCT_IMAGE_WIDTH, $store);
        if (is_numeric($value)) {
            return intval($value);
        }
        return null;
    }

    /**
     * @param Mage_Core_Model_Store|null $store
     *
     * @return int|null
     */
    public static function getProductViewImageHeight($store = null)
    {
        $value = Mage::getStoreConfig(self::PRODUCT_IMAGE_HEIGHT, $store);
        if (is_numeric($value)) {
            return intval($value);
        }
        return null;
    }

    /**
     * @param Mage_Core_Model_Store|null $store
     *
     * @return bool
     */
    public static function isEnabledOnCategory($store = null)
    {
        return (bool)Mage::getStoreConfig(self::CATEGORY_IS_ENABLED, $store);
    }

    /**
     * @param Mage_Core_Model_Store|null $store
     *
     * @return int|null
     */
    public static function getCategoryViewImageWidth($store = null)
    {
        $value = Mage::getStoreConfig(self::CATEGORY_IMAGE_WIDTH, $store);
        if (is_numeric($value)) {
            return intval($value);
        }
        return null;
    }

    /**
     * @param Mage_Core_Model_Store|null $store
     *
     * @return int|null
     */
    public static function getCategoryViewImageHeight($store = null)
    {
        $value = Mage::getStoreConfig(self::CATEGORY_IMAGE_HEIGHT, $store);
        if (is_numeric($value)) {
            return intval($value);
        }
        return null;
    }

    /**
     * @param Mage_Core_Model_Store|null $store
     *
     * @return bool
     */
    public static function isCanShowInLayer($store = null)
    {
        $value = intval(Mage::getStoreConfig(self::LAYER_USE_AS, $store));
        return $value !== AW_Colorswatches_Model_Source_Config_UseInLayeredNavigation::NO_VALUE;
    }

    /**
     * @param Mage_Core_Model_Store|null $store
     *
     * @return bool
     */
    public static function isCanShowTextInLayer($store = null)
    {
        $value = intval(Mage::getStoreConfig(self::LAYER_USE_AS, $store));
        return $value === AW_Colorswatches_Model_Source_Config_UseInLayeredNavigation::YES_ALL_VALUE;
    }
}