<?php
/**
 * Class to build apropriate titles for each payment method
 *
 * PHP Version 5.3
 *
 * @category  Payment
 * @package   KITT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */

/**
 * Title module for the Klarna Integration ToolkiT
 *
 * @category  Payment
 * @package   KITT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
class KiTT_Payment_Title
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $paymentCode;

    /**
     * @var KiTT_Formatter
     */
    protected $formatter;

    /**
     * @var KiTT_PClassCollection
     */
    protected $pclassCollection;

    /**
     * @var KiTT_Translator
     */
    protected $translator;

    /**
     * @var KiTT_Locale
     */
    protected $locale;

    /**
     * Construct the Title class.
     *
     * Option is an assoc array. Supported fields with default value first.
     * "invoiceFee" 0 or invoice fee
     * "feepos"     title or extra
     * "feeformat"  short or long
     * "nofeepos"   extra or title
     *
     * @param String                $paymentCode invoice, part or spec
     * @param KiTT_Locale           $locale      locale.
     * @param KiTT_Formatter        $formatter   formatter.
     * @param KiTT_Translator       $translator  translator.
     * @param KiTT_PClassCollection $pclasses    pclasses collection.
     * @param array                 $options     array with options.
     *
     * @throws InvalidArgumentException if a required argument is
     * @return void
     */
    public function __construct(
        $paymentCode,
        KiTT_Locale $locale,
        KiTT_Formatter $formatter,
        KiTT_Translator $translator,
        KiTT_PClassCollection $pclasses = null,
        $options = array()
    ) {
        $default = array(
            "invoiceFee" => 0,
            "feepos" => "title",
            "feeformat" => "short",
            "nofeepos" => "extra"
        );
        $this->options = array_merge($default, $options);

        if ($paymentCode == null) {
            throw new InvalidArgumentException(
                '$paymentCode is not set'
            );
        }
        if ($paymentCode != 'part'
            && $paymentCode != 'invoice'
            && $paymentCode != 'spec'
        ) {
            throw new InvalidArgumentException(
                '$paymentCode must be "invoice", "part" or "spec"'
            );
        }

        $this->locale = $locale;
        $this->formatter = $formatter;
        $this->paymentCode = $paymentCode;
        $this->translator = $translator;
        $this->pclassCollection = $pclasses;
    }

    /**
     * Retrieve the title built from the set options as an associative array
     * with title and extra.
     *
     * @return array
     */
    public function getTitle()
    {
        switch ($this->paymentCode) {
        case'invoice':
            return $this->_invoiceTitle();
        case'part':
            return $this->_partPaymentTitle();
        case'spec':
            return $this->_specCampaignTitle();
        default:
            throw new KiTT_Exception("Unsupported payment option");
        }
    }

    /**
     * Invoice titles
     *
     * @return array
     */
    private function _invoiceTitle()
    {
        $title = $this->translator->translate('INVOICE_TITLE');

        if ($this->options['invoiceFee'] == 0) {
            return $this->_noFeeTitle($title);
        }

        if ($this->options['feeformat'] == 'short') {
            return $this->_shortFeeTitle($title);
        }

        return $this->_longFeeTitle($title);
    }

    /**
     * Title with no fee
     *
     * @param string $title title from language pack
     *
     * @return array
     */
    private function _noFeeTitle($title)
    {
        $noFee = $this->translator->translate('NO_INVOICE_FEE');
        if ($this->options['nofeepos'] == 'extra') {
            return array(
                'title' => str_replace('(+XX)', "", $title),
                'extra' => $noFee
            );
        }
        return array(
            'title' => str_replace('+XX', $noFee, $title),
            'extra' => ""
        );
    }

    /**
     * Title with short invoice fee formatting
     *
     * @param string $title title from language pack
     *
     * @return array
     */
    private function _shortFeeTitle($title)
    {
        $shortFee = $this->formatter->formatPrice(
            $this->options['invoiceFee'], $this->locale
        );
        if ($this->options['feepos'] == 'title') {
            return array(
                "title" => str_replace('+XX', $shortFee, $title),
                "extra" => ""
            );
        }
        return array(
            "title" => str_replace('(+XX)', "", $title),
            "extra" => $shortFee
        );
    }

    /**
     * Title with long invoice fee formatting
     *
     * @param string $title title from language pack
     *
     * @return array
     */
    private function _longFeeTitle($title)
    {
        $longFee = str_replace(
            "(xx)",
            $this->formatter->formatPrice(
                $this->options['invoiceFee'], $this->locale
            ),
            $this->translator->translate('format_invoicefee_not_included')
        );
        if ($this->options['feepos'] == 'title') {
            return array(
                "title" => str_replace('+XX', $longFee, $title),
                "extra" => ""
            );
        }
        return array(
            "title" => str_replace('(+XX)', "", $title),
            "extra" => $longFee
        );
    }

    /**
     * Part payment title
     *
     * @return array
     */
    private function _partPaymentTitle()
    {
        if (count($this->pclassCollection->pclasses) > 0) {
            $price = $this->pclassCollection->minimumPClass();
            return array(
                "title" => str_replace(
                    'xx',
                    $price,
                    $this->translator->translate('PARTPAY_TITLE')
                ),
                "extra" => ""
            );
        }
        return array(
            "title" => $this->translator->translate('PARTPAY_TITLE_NOSUM'),
            "extra" => ""
        );
    }

    /**
     * Special Campaign title
     *
     * @return array
     */
    private function _specCampaignTitle()
    {
        if (count($this->pclassCollection->pclasses) > 0) {
            $default = $this->pclassCollection->defaultPClass();
            return array(
                "title" => html_entity_decode(
                    $default['description'], ENT_COMPAT, "UTF-8"
                ),
                "extra" => ""
            );
        }
        return array(
            "title" => $this->translator->translate('SPEC_TITLE'),
            "extra" => ""
        );
    }
}
