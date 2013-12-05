<?php
/**
 * API extension
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
 * Extending the API in order to easily support updating version
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
class KiTT_API extends Klarna
{
    /**
     * Read kred url from host override file if available.
     *
     * @param KiTT_Config $config configuration
     *
     * @return string
     */
    protected function getHost(KiTT_Config $config)
    {
        $hostfile = '';
        try {
            $hostfile = $config->get('paths/data') . '/host';
        } catch (KiTT_MissingConfigurationException $e) {
            return null;
        }

        if (file_exists($hostfile)) {
            return file_get_contents($hostfile);
        }

        return null;
    }

    /**
     * Constructor
     *
     * @param KiTT_Config $config configuration
     * @param KiTT_Locale $locale locale
     */
    public function __construct(KiTT_Config $config, KiTT_Locale $locale)
    {
        parent::__construct();

        $country = $locale->getCountryCode();

        $partialConfig = array(
            'eid' => $config->get("sales_countries/{$country}/eid"),
            'secret' => $config->get("sales_countries/{$country}/secret"),
            'country' => $country,
            'currency' => $locale->getCurrencyCode(),
            'language' => $locale->getLanguageCode(),
        );

        // Optional override of the xmlrpc target server.
        $host = $this->getHost($config);
        if ($host) {
            $partialConfig['url'] = $host;
        }

        $module = $config->get("module");
        $version = $config->get("version");
        $this->VERSION = "PHP:{$module}:{$version}";
        $klarnaConfig = array_merge($config->get('api'), $partialConfig);
        $this->setConfig($klarnaConfig);
    }

}
