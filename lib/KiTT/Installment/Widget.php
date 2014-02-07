<?php
/**
 * Part payment widget view
 *
 * PHP Version 5.3
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */

/**
 * KiTT_ProductPrice
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
class KiTT_Installment_Widget extends KiTT_Widget
{
    /**
     * @var KiTT_PClassCollection
     */
    public $pclasses;

    /**
     * Asterisk character or empty stirng
     * @var string
     */
    public $asterisk;

    /**
     * @var int
     */
    protected static $ID = 0;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var KiTT_CountryLogic
     */
    protected $logic;

    /**
     * Template to render
     *
     * @var string
     */
    protected $template = 'ppbox.mustache';

    /**
     * Create product price widget
     *
     * @param KiTT_Config           $config         KiTT Config object
     * @param KiTT_Locale           $locale         KiTT Locale object
     * @param KiTT_PClassCollection $pclasses       Collection of pclasses
     * @param KiTT_TemplateLoader   $templateLoader KiTT TemplateLoader object
     * @param KiTT_Translator       $translator     KiTT Translator object
     */
    public function __construct($config, $locale, $pclasses, $templateLoader,
        $translator
    ) {
        parent::__construct($config, $locale, $templateLoader, $translator);
        $this->pclasses = $pclasses;
        self::$ID += 1;
        $this->id = self::$ID;
    }

    /**
     * Get the eid for the locale country
     *
     * @return int
     */
    public function eid()
    {
        $country = strtoupper($this->locale->getCountryCode());
        try {
            return $this->config->get("sales_countries/{$country}/eid");
        } catch (KiTT_MissingConfigurationException $e) {
            return 0;
        }
    }

    /**
     * Get the country code used
     *
     * @return string
     */
    public function country()
    {
        return strtolower($this->locale->getCountryCode());
    }

    /**
     * Get the identification number to use
     *
     * @return string
     */
    public function id()
    {
        return $this->id . "_klarna_PPBox";
    }
}

