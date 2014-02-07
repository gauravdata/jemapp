<?php
/**
 * Abstract class for installments
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
 * KiTT_Installment_Controller_Abstract
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
abstract class KiTT_Installment_Controller_Abstract
    implements KiTT_Installment_ControllerInterface
{

    protected $config;
    protected $locale;
    protected $sum;
    protected $formatter;
    protected $translator;
    protected $templateLoader;
    protected $api;
    protected $logic;

    /**
     * Constructor
     *
     * @param KiTT_Config         $config         site configuration
     * @param KiTT_Locale         $locale         locale
     * @param numeric             $sum            total price
     * @param KiTT_Formatter      $formatter      formatter
     * @param KiTT_Translator     $translator     translator
     * @param KiTT_TemplateLoader $templateLoader template loader
     * @param Klarna              $api            klarna api instance
     * @param KiTT_CountryLogic   $logic          Country Logic
     */
    public function __construct(
        KiTT_Config $config,
        KiTT_Locale $locale,
        $sum,
        KiTT_Formatter $formatter,
        KiTT_Translator $translator,
        KiTT_TemplateLoader $templateLoader,
        Klarna $api,
        KiTT_CountryLogic $logic
    ) {
        $this->config = $config;
        $this->locale = $locale;
        $this->sum = $sum;
        $this->formatter = $formatter;
        $this->translator = $translator;
        $this->templateLoader = $templateLoader;
        $this->api = $api;
        $this->logic = $logic;
    }

    /**
     * Create a pclass collection
     *
     * @return KiTT_PClassCollection
     */
    protected abstract function createPClassCollection();

    /**
     * Create a new installment widget
     *
     * @return KiTT_Installment_Widget
     */
    public function createWidget()
    {
        return new KiTT_Installment_Widget(
            $this->config,
            $this->locale,
            $this->createPClassCollection(),
            $this->templateLoader,
            $this->translator,
            $this->logic
        );
    }

    /**
     * Check if the installments should be available or not
     *
     * @return boolean
     */
    public function isAvailable()
    {
        if (count($this->createPClassCollection()->pclasses) === 0) {
            return false;
        }

        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)
            && (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE 7.0")
            || strstr($_SERVER['HTTP_USER_AGENT'], "MSIE 6.0"))
        ) {
            return false;
        }

        if (!$this->logic->isBelowLimit($this->sum, KiTT::PART)) {
            return false;
        }

        return true;
    }

}
