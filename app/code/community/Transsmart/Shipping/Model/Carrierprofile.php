<?php

/**
 * Transsmart Carrier Profile Model
 *
 * @method Transsmart_Shipping_Model_Resource_Carrierprofile _getResource()
 * @method Transsmart_Shipping_Model_Resource_Carrierprofile getResource()
 * @method int getCarrierprofileId()
 * @method Transsmart_Shipping_Model_Carrierprofile setCarrierprofileId(int $value)
 * @method int getCarrierId()
 * @method Transsmart_Shipping_Model_Carrierprofile setCarrierId(int $value)
 * @method int getServicelevelTimeId()
 * @method Transsmart_Shipping_Model_Carrierprofile setServicelevelTimeId(int $value)
 * @method int getServicelevelOtherId()
 * @method Transsmart_Shipping_Model_Carrierprofile setServicelevelOtherId(int $value)
 * @method int getEnableLocationSelect()
 * @method Transsmart_Shipping_Model_Carrierprofile setEnableLocationSelect(int $value)
 *
 * @method string getCarrierCode()
 * @method string getCarrierName()
 * @method bool getCarrierLocationSelect()
 * @method string getServicelevelTimeCode()
 * @method string getServicelevelTimeName()
 * @method string getServicelevelOtherCode()
 * @method string getServicelevelOtherName()
 *
 * @category    Transsmart
 * @package     Transsmart_Shipping
 * @copyright   Copyright (c) 2016 Techtwo Webdevelopment B.V. (http://www.techtwo.nl)
 */
class Transsmart_Shipping_Model_Carrierprofile extends Transsmart_Shipping_Model_Abstract
{
    /**
     * A list of API keys to be mapped to database columns.
     * @var array
     */
    protected $apiKeysMapping = array(
        'Id'                   => 'carrierprofile_id',
        'CarrierId'            => 'carrier_id',
        'ServiceLevelTimeId'   => 'servicelevel_time_id',
        'ServiceLevelOtherId'  => 'servicelevel_other_id',
        'EnableLocationSelect' => 'enable_location_select'
    );

    /**
     * Object initialization
     */
    protected function _construct()
    {
        $this->_init('transsmart_shipping/carrierprofile');
    }

    /**
     * Load the carrierprofile by the shipping method code.
     *
     * @param string $shippingMethodCode
     * @return $this
     */
    public function loadByShippingMethodCode($shippingMethodCode)
    {
        if (preg_match('/^transsmart(delivery|pickup)_carrierprofile_([0-9]+)$/', $shippingMethodCode, $match)) {
            $id = (int)$match[2];
            if ($id) {
                $this->load($id);
            }
        }
        return $this;
    }

    /**
     * Retrieve information from carrier profile configuration
     *
     * @param string $field
     * @param mixed $store
     * @return mixed
     */
    public function getConfigData($field, $store = null)
    {
        $path = 'transsmart_carrier_profiles/carrierprofile_' . $this->getId() . '/' . $field;
        return Mage::getStoreConfig($path, $store);
    }

    /**
     * Check if the location selector is enabled for this carrier profile and the carrier.
     *
     * @param mixed $store deprecated
     * @return bool
     */
    public function isLocationSelectEnabled($store = null)
    {
        return ($this->getCarrierLocationSelect() && $this->getEnableLocationSelect());
    }

    /**
     * Return a name identifying this carrier profile. Because there is no 'name' attribute, this name is constructed
     * using config settings, or the carrier/servicelevel name if no name is configured.
     *
     * @param mixed $store
     * @return string
     */
    public function getName($store = null)
    {
        $id = $this->getData('carrierprofile_id');

        $title = $this->getConfigData('title', $store);
        if (empty($title)) {
            $title = sprintf(
                '(%s / %s / %s)',
                $this->getData('carrier_name'),
                $this->getData('servicelevel_time_name'),
                $this->getData('servicelevel_other_name')
            );
        }

        return $id . ' - ' . $title;
    }
}
