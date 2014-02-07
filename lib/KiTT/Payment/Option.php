<?php
/**
 * Payment Option
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
 * Kitt_Payment_Option
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
class KiTT_Payment_Option implements KiTT_Payment_OptionInterface
{

    protected $config;

    protected $paymentCode;

    protected $locale;

    protected $formatter;

    protected $pclasses;

    protected $translator;

    protected $templateLoader;

    protected $lookup;

    protected $input;

    protected $settings = array();

    /**
     * @var KiTT_CountryLogic
     */
    protected $logic;

    /**
     * @var KiTT_Baptizer
     */
    private $_baptizer;

    /**
     * @var KiTT_TemplateFieldsInterface
     */
    private $_templateFields;

    /**
     * @var KiTT_Title
     */
    private $_title;

    /**
     * Construct Kitt_Payment_Option
     *
     * @param KiTT_Config           $config         site configuration
     * @param string                $paymentCode    payment widget type
     * @param KiTT_Locale           $locale         locale
     * @param KiTT_Formatter        $formatter      formatter
     * @param KiTT_PClassCollection $pclasses       pclass collection
     * @param KiTT_Translator       $translator     translator
     * @param KiTT_TemplateLoader   $templateLoader template loader
     * @param KiTT_Lookup           $lookup         lookup table
     * @param KiTT_InputValues      $input          input values
     * @param KiTT_VFS              $vfs            vfs
     * @param KiTT_CountryLogic     $logic          Country Logic instance
     * @param float                 $sum            total sum
     */
    public function __construct(
        KiTT_Config $config, $paymentCode, KiTT_Locale $locale,
        KiTT_Formatter $formatter, KiTT_PClassCollection $pclasses,
        KiTT_Translator $translator, KiTT_TemplateLoader $templateLoader,
        KiTT_Lookup $lookup, KiTT_InputValues $input, KiTT_VFS $vfs,
        KiTT_CountryLogic $logic, $sum = 0.0
    ) {

        $this->config = $config;
        $this->paymentCode = $paymentCode;
        $this->locale = $locale;
        $this->formatter = $formatter;
        $this->pclasses = $pclasses;
        $this->translator = $translator;
        $this->templateLoader = $templateLoader;
        $this->lookup = $lookup;
        $this->input = $input;
        $this->vfs = $vfs;
        $this->logic = $logic;
        $this->settings['sum'] = $this->sum = $sum;
    }

    /**
     * Set a baptizer.
     *
     * @param KiTT_Baptizer $baptizer KiTT_Baptizer implementation
     *
     * @return void
     */
    public function setBaptizer(KiTT_Baptizer $baptizer)
    {
        $this->_baptizer = $baptizer;
    }

    /**
     * Get the baptizer.
     *
     * @return KiTT_Baptizer
     */
    public function getBaptizer()
    {
        if ($this->_baptizer === null) {
            $this->_baptizer = $this->createBaptizer();
        }

        return $this->_baptizer;
    }

    /**
     * Create a baptizer
     *
     * @return KiTT_Baptizer
     */
    protected function createBaptizer()
    {
        return new KiTT_DefaultBaptizer("klarna_{$this->paymentCode}");
    }

    /**
     * Set template fields.
     *
     * @param KiTT_TemplateFieldsInterface $fields value to set.
     *
     * @return void
     */
    public function setTemplateFields(KiTT_TemplateFieldsInterface $fields)
    {
        $this->_templateFields = $fields;
    }

    /**
     * Get the template fields.
     *
     * @return KiTT_TemplateFieldsInterface
     */
    public function getTemplateFields()
    {
        if ($this->_templateFields === null) {
            $this->_templateFields = $this->createTemplateFields();
        }

        return $this->_templateFields;
    }

    /**
     * Create the template fields
     *
     * @return KiTT_TemplateFieldsInterface
     */
    protected function createTemplateFields()
    {
        return new KiTT_TemplateFields(
            $this->locale,
            $this->paymentCode,
            $this->input,
            $this->translator,
            $this->vfs,
            $this->getBaptizer(),
            $this->logic
        );
    }

    /**
     * Format the title
     *
     * @param array $options formatting options for KiTT_Payment_Title
     *
     * @return array
     */
    public function formatTitle(array $options = array())
    {
        $settings = array_merge($this->settings, $options);
        $title = new KiTT_Payment_Title(
            $this->paymentCode,
            $this->locale,
            $this->formatter,
            $this->translator,
            $this->pclasses,
            $settings
        );
        $this->_title = $title->getTitle();

        return $this->_title;
    }

    /**
     * Create the payment widget
     *
     * @return KiTT_Payment_Widget
     */
    protected function createWidget()
    {
        return new KiTT_Payment_Widget(
            $this->config,
            $this->locale,
            $this->paymentCode,
            $this->templateLoader,
            $this->translator,
            $this->getBaptizer(),
            $this->lookup,
            $this->getTemplateFields(),
            $this->pclasses,
            $this->settings
        );
    }

    /**
     * Get the total sum to use. This will pick up the invoice fee if one is
     * configured
     *
     * @return numeric
     */
    protected function getTotalSum()
    {
        $sum = $this->sum;
        if (array_key_exists('invoiceFee', $this->settings)) {
            $sum += $this->settings['invoiceFee'];
        }

        return $sum;
    }

    /**
     * Prefill checkout values by merging an associative array.
     *
     * @param array $data Values to prefill the checkout with their matching keys.
     *
     * @return void
     */
    public function prefill($data)
    {
        $this->input->merge($data);
    }

    /**
     * Set the address to prefill checkout with.
     *
     * @param KlarnaAddr $addr Klarna Address object
     *
     * @return void
     */
    public function setAddress(KlarnaAddr $addr)
    {
        $this->input->setAddress($addr);
    }

    /**
     * Set Date of Birth to prefill the checkout with.
     *
     * @param string $dob date of birth in ISO 8601 date format (YYYY-MM-DD)
     *
     * @return void
     */
    public function setBirthDay($dob)
    {
        $this->input->setBirthDay($dob);
    }

    /**
     * Set the payment fee for this option
     * Only used if the type is invoice
     *
     * @param numeric $fee the payment fee to display
     *
     * @return void
     */
    public function setPaymentFee($fee)
    {
        if (!is_numeric($fee)) {
            throw new InvalidArgumentException(
                '$fee must be numeric but was: ' . print_r($fee, true)
            );
        }
        $this->settings['invoiceFee'] = $fee;
    }

    /**
     * Set the error message to display
     *
     * @param mixed $error KiTT_Error or string with error message
     *
     * @return void
     */
    public function setError($error)
    {
        if (!$error instanceof KiTT_ErrorInterface) {
            $this->settings['errors'] = array(
                'message' => (string) $error,
                'json' => "{}"
            );
        } else {
            $this->settings['errors'] = array(
                'message' => $this->translator->translate($error->message()),
                'json' => $error->json()
            );
        }
    }

    /**
     * Set the payment id used by collapse
     *
     * @param string $id identifier
     *
     * @return void
     */
    public function setPaymentId($id)
    {
        $this->settings['paymentCode'] = $id;
    }

    /**
     * Preselect a PClass
     *
     * @param int $pclassId id of the pclass to select
     *
     * @return void
     */
    public function selectPClass($pclassId)
    {
        $this->pclasses->setDefault($pclassId);
    }

    /**
     * Get the payment widget title
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->_title === null) {
            $this->formatTitle();
        }

        return $this->_title['title'];
    }

    /**
     * Get the payment widget extra message
     *
     * @return string
     */
    public function getExtra()
    {
        if ($this->_title === null) {
            $this->formatTitle();
        }

        return $this->_title["extra"];
    }

    /**
     * Get the current payment code
     *
     * @return string
     */
    public function getPaymentCode()
    {
        return $this->paymentCode;
    }

    /**
     * Assert that the payment option is available
     *
     * @return bool
     */
    public function isAvailable()
    {
        $country = $this->locale->getCountryCode();
        $countryCurrency = $this->lookup->getCurrency($country);
        if ($this->locale->getCurrencyCode() !== $countryCurrency) {
            return false;
        }

        if (!$this->config->has("sales_countries/{$country}")) {
            return false;
        }

        if (!$this->logic->isBelowLimit($this->getTotalSum(), $this->paymentCode)) {
            return false;
        }

        if ($this->paymentCode === KiTT::PART
            || $this->paymentCode === KiTT::SPEC
        ) {
            return (count($this->pclasses->pclasses) > 0);
        }

        return true;
    }

    /**
     * Render the payment widget
     *
     * @return string
     */
    public function show()
    {
        $widget = $this->createWidget();
        return $widget->show();
    }

}
