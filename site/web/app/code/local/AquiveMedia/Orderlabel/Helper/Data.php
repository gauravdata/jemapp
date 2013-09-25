<?php
/**
 * Author: Jeroen Smit - Smit Webdevelopment - www.smit-web.nl
 * Contact: jeroen@smit-web.nl
 * Copyright: Aquive Media
 * Created: 12/2/11
 */
class AquiveMedia_OrderLabel_Helper_Data extends Mage_Core_Helper_Abstract
{
    const FORMAT_POSTCODE_CITY = 1;
    const FORMAT_CITY_POSTCODE = 2;

    /**
     * Filters an instance of Mage_Sales_Model_Order_Address down to the point where it contains only the necessary
     * information. These fields depend on this shop configuration. The default fields are Name, Street, Zipcode and City.
     *
     * Returns an associative array.
     * @param Mage_Sales_Model_Order $address
     * @return array
     */
    public function filterAddress($address)
    {
        if($address == null){
            return null;
        }
        $details['name'] = $address->getName();
        $details['street'] = $address->getStreetFull();

        $postcode = $address->getPostcode();
        $city = $address->getCity();

        switch (Mage::getStoreConfig('orderlabel/general/postcodeformat')) {
            case AquiveMedia_OrderLabel_Helper_Data::FORMAT_CITY_POSTCODE:
                $details['cpc'] = implode(' ', array($city, $postcode));
                break;
            case AquiveMedia_OrderLabel_Helper_Data::FORMAT_POSTCODE_CITY:
            default:
                $details['cpc'] = implode(' ', array($postcode, $city));
                break;
        }

        if (Mage::getStoreConfig('orderlabel/recipient/print_country') == true) {
            $details['country'] = Mage::getModel('directory/country')->loadByCode($address->getCountry())->getName();
        }

        if (Mage::getStoreConfig('orderlabel/recipient/print_state') == true) {
            $details['region'] = $address->getRegion();
            if($details['region'] == '-'){
                $details['region'] = null;
            }
        }

        if (Mage::getStoreConfig('orderlabel/recipient/print_company') == true) {
            $details['company'] = $address->getCompany();
            if(($prefix = Mage::getStoreConfig('orderlabel/recipient/print_company_prefix')) != ''){
                $details['prefix'] = $prefix;
            }
        }

        if (Mage::getStoreConfig('orderlabel/recipient/print_phone') == true) {
            $details['phone'] = $address->getTelephone();
            if($details['phone'] == '-'){
                $details['phone'] = null;
            }
        }
        return $details;
    }

    /**
     * Returns an array containing all available labels.
     * @see _scanPresentLabels
     * @return array
     */
    public function getRecipientOptionsArray()
    {
        return $this->_scanPresentLabels();
    }

    public function getButtonlocationOptionsArray()
    {
        return array(
            array('value' => 'header', 'label' => 'Header'),
            array('value' => 'footer', 'label' => 'Footer')
        );
    }

    public function getPostcodeFormatOptionsArray()
    {
        return array(
            array('value' => AquiveMedia_OrderLabel_Helper_Data::FORMAT_POSTCODE_CITY, 'label' => $this->__('Postcode + City')),
            array('value' => AquiveMedia_OrderLabel_Helper_Data::FORMAT_CITY_POSTCODE, 'label' => $this->__('City + Postcode')),
        );
    }

    /**
     * Scans through the label directory, for the .label extension.
     * It returns an array containing all label files present in the directory.
     * @return array
     */
    private final function _scanPresentLabels()
    {
        $pathToLabels = getcwd() . '/js/orderlabel/assets/labels';
        $labels = array();
        $files = scandir($pathToLabels);
        foreach ($files as $file) {
            $fileInfo = pathinfo($file);
            if ($fileInfo['extension'] == 'label') {
                $displayName = ucwords(str_replace('_', ' ', $fileInfo['filename']));
                $labels[] = array('value' => $fileInfo['basename'], 'label' => "Dymo Label {$displayName}");
            }
        }
        return $labels;
    }

    public function getFormatedLayout()
    {
        $activeLayout = $this->getActiveLabelLayout();
        $activeLayout = str_replace('.label', '', $activeLayout);
        $activeLayout = str_replace('_', ' ', $activeLayout);
        $activeLayout = ucwords($activeLayout);
        return $activeLayout;
    }

    /**
     * Retrieves the value for the active layout used for printing labels
     * @return string
     */
    public function getActiveLabelLayout()
    {
        return Mage::getStoreConfig('orderlabel/general/layout');
    }

    public function isActiveLayoutValid()
    {
        return strpos($this->getActiveLabelLayout(),'.label') !== null;
    }

    /**
     * Checks the configuration to see whether or not the printing of sender labels is enabled.
     * @return bool
     */
    public function isSenderEnabled()
    {
        return Mage::getStoreConfig('orderlabel/sender/enabled');
    }

    /**
     * Returns the Sender Name as specified in the module configuration.
     * @return String
     */
    public function getSenderName()
    {
        if ($this->isSenderEnabled()) {
            return Mage::getStoreConfig('orderlabel/sender/name');
        } else {
            return '';
        }
    }

    /**
     * Returns the Sender Address as specified in the module configuration.
     * @return String
     */
    public function getSenderAddress()
    {
        if ($this->isSenderEnabled()) {
            return Mage::getStoreConfig('orderlabel/sender/address');
        } else {
            return '';
        }
    }

    /**
     * Returns the Sender City as specified in the module configuration.
     * @return String
     */
    public function getSenderCity()
    {
        if ($this->isSenderEnabled()) {
            return Mage::getStoreConfig('orderlabel/sender/city');
        } else {
            return '';
        }
    }

    /**
     * Returns the Sender Postcode as specified in the module configuration.
     * @return String
     */
    public function getSenderPostcode()
    {
        if ($this->isSenderEnabled()) {
            return Mage::getStoreConfig('orderlabel/sender/postcode');
        } else {
            return '';
        }
    }


    /**
     * Returns the Sender Country as specified in the module configuration.
     * @param bool $code If true, the country code is returned. If false, the country name is returned.
     * @return String
     */
    public function getSenderCountry($code = false)
    {
        $countryCode = Mage::getStoreConfig('orderlabel/sender/country');
        if ($this->isSenderEnabled() && $countryCode != null) {
            if ($code === true) {
                return $countryCode;
            } else {
                return Mage::getModel('directory/country')->loadByCode($countryCode)->getName();
            }
        } else {
            return '';
        }
    }

    /**
     * Retrieves the url which points to the currently selected layout used for printing labels.
     * @see AquiveMedia_OrderLabel_Helper_Data::getActiveLabelLayout
     * @return string
     */
    public function getActiveLabelLayoutUrl()
    {
        $activeLabel = $this->getActiveLabelLayout();
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS) . "orderlabel/assets/labels/{$activeLabel}";
        return $url;
    }

    public function getScriptHtml()
    {
        $useLocal = Mage::getStoreConfig('orderlabel/general/localscript');
        if ($useLocal == 1) {
            $html = '<script type="text/javascript" src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS) . 'orderlabel/DYMO.Label.Framework.latest.js"></script>';
        } else {
            $html = '<script type="text/javascript" language="javascript" src="http://labelwriter.com/software/dls/sdk/js/DYMO.Label.Framework.latest.js"></script>';
        }

        return $html;
    }

    public function getSenderDetails($json = false)
    {
        if ($this->isSenderEnabled()) {
            $details = array(
                'name' => $this->getSenderName(),
                'street' => $this->getSenderAddress(),
                'city' => $this->getSenderCity(),
                'postcode' => $this->getSenderPostcode(),
                'country' => $this->getSenderCountry(),
            );
            if ($json === true) {
                return Mage::helper('core')->jsonEncode($details);
            } else {
                return $details;
            }
        }
        return null;
    }
}
