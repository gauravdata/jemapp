<?php

/**
 * KiTT Mustache Extension
 *
 * @category  Payment
 * @package   KiTT
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
class KiTT_Mustache_Wrapper extends KiTT_Mustache
{
    /**
     * @var KiTT_TemplateLoader
     */
    private $_loader;

    /**
     * Create an instance of KiTT_Mustache
     *
     * @param KiTT_TemplateLoader $loader Template Loader
     *
     * @return void
     */
    public function __construct(KiTT_TemplateLoader $loader)
    {
        $this->_loader = $loader;
    }

    /**
     * Override _getPartial to autoload partials.
     *
     * @param string $tag_name partial name
     *
     * @return string
     */
    protected function _getPartial($tag_name)
    {
        return $this->_loader->load($tag_name . '.mustache')->getTemplate();
    }
}

