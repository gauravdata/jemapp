<?php
/**
 * Country Logic
 *
 * PHP Version 5.2
 *
 * @category Payment
 * @package  KiTT
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */

/**
 * Class for country logic
 *
 * @category Payment
 * @package  KiTT
 * @author   MS Dev <ms.modules@klarna.com>
 * @license  http://opensource.org/licenses/BSD-2-Clause BSD2
 * @link     http://integration.klarna.com
 */
class KiTT_CountryLogic
{

    protected $country;

    /**
     * Create an instance of the country logic class
     *
     * @param KiTT_Locale $locale KiTT_Locale instance to use for logic.
     */
    public function __construct(KiTT_Locale $locale)
    {
        $this->country = strtoupper($locale->getCountryCode());
    }

    /**
     * Check if shipping and billing should be the same
     *
     * @return boolean
     */
    public function shippingSameAsBilling()
    {
        switch ($this->country) {
        case 'NL':
        case 'DE':
            return true;
        default:
            return false;
        }
    }

    /**
     * Check if consent is needed
     *
     * @return boolean
     */
    public function needConsent()
    {
        switch ($this->country) {
        case 'DE':
        case 'AT':
            return true;
        default:
            return false;
        }
    }

    /**
     * Check if an asterisk is needed
     *
     * @return boolean
     */
    public function needAsterisk()
    {
        return false;
    }

    /**
     * Check if gender is needed
     *
     * @return boolean
     */
    public function needGender()
    {
        switch ($this->country) {
        case 'NL':
        case 'DE':
        case 'AT':
            return true;
        default:
            return false;
        }
    }

    /**
     * Check if date of birth is needed
     *
     * @return boolean
     */
    public function needDateOfBirth()
    {
        switch ($this->country) {
        case 'NL':
        case 'DE':
        case 'AT':
            return true;
        default:
            return false;
        }
    }

    /**
     * Return the fields a street should be split into.
     *
     * @return array
     */
    public function getSplit()
    {
        switch ($this->country) {
        case 'DE':
            return array('street', 'house_number');
        case 'NL':
            return array('street', 'house_number', 'house_extension');
        default:
            return array('street');
        }
    }

    /**
     * Is the sum below the limit allowed for the given country?
     *
     * @param float  $sum    Sum to check
     * @param string $method KiTT payment method constant
     *
     * @return boolean
     */
    public function isBelowLimit($sum, $method)
    {
        if ($this->country !== 'NL') {
            return true;
        }

        if ($method === KiTT::INVOICE) {
            return true;
        }

        if (((double)$sum) <= 250.0) {
            return true;
        }

        return false;
    }

    /**
     * Is an AGB link needed?
     *
     * @return boolean
     */
    public function needAGB()
    {
        switch ($this->country) {
        case 'DE':
        case 'AT':
            return true;
        default:
            return false;
        }
    }

    /**
     * Do we need to call getAddresses
     *
     * @return boolean
     */
    public function useGetAddresses()
    {
        switch ($this->country) {
        case 'SE':
            return true;
        default:
            return false;
        }
    }

    /**
     * Are Company Purchases supported?
     *
     * @return boolean
     */
    public function isCompanyAllowed()
    {
        switch ($this->country) {
        case 'NL':
        case 'DE':
        case 'AT':
            return false;
        default:
            return true;
        }
    }
}
