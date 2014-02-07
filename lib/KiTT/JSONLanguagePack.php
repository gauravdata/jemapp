<?php
/**
 * Klarna JSON Language Pack
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
 * The Klarna Language Pack class. This class fetches translations from a
 * language pack.
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
class KiTT_JSONLanguagePack implements KiTT_LanguagePack
{
    /**
     * Translation map
     * @var array
     */
    private $_data;

    /**
     * @var KiTT_Config
     */
    private $_config;

    /**
     * Create language pack
     *
     * @param KiTT_Config $config configuration
     * @param KiTT_VFS    $vfs    vfs object
     */
    public function __construct(KiTT_Config $config, KiTT_VFS $vfs)
    {
        $this->_config = $config;
        $path = $this->_config->get('paths/lang');
        $this->_data = json_decode($vfs->file_get_contents($path), true);
    }

    /**
     * Get a translated text from the language pack
     *
     * @param string     $text     the string to be translated
     * @param string|int $language target language, iso code or KlarnaLanguage
     *
     * @return string  the translated text
     */
    public function fetch ($text, $language)
    {
        if (is_numeric($language)) {
            $language = KlarnaLanguage::getCode($language);
        } else {
            $language = strtolower($language);
        }

        if (array_key_exists($text, $this->_data)
            && array_key_exists($language, $this->_data[$text])
        ) {
            return $this->_data[$text][$language];
        }

        // Fallback to the english text
        if ($language != 'en') {
            return $this->fetch($text, 'en');
        }

        // Or failing that, the placeholder
        return $text;
    }

}
