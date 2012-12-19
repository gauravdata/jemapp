<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id:
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * Data model for page rendered on server
 *
 * @package GwtPhpFramework
 */
class Gpf_Ui_Model_Page extends Gpf_Object implements Gpf_Templates_HasAttributes {
    /**
     * Title of web page
     *
     * @var string
     */
    private $title;

    /**
     * Metadescription of web site
     *
     * @var string
     */
    private $metaDescription;

    /**
     * Page keywords
     *
     * @var string
     */
    private $metaKeywords;

    public function __construct() {
    }

    /**
     * Set page title
     *
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set page keywords
     *
     * @param string $keywords
     */
    public function setMetaKeywords($keywords) {
        $this->metaKeywords = $keywords;
    }

    /**
     * Get page keywords
     *
     * @return string
     */
    public function getMetaKeywords() {
        return $this->metaKeywords;
    }


    /**
     * Set page description (displayed in meta tags)
     *
     * @param string $description
     */
    public function setMetaDescription($description) {
        $this->metaDescription = $description;
    }


    /**
     * Get meta description of page
     *
     * @return string
     */
    public function getMetaDescription() {
        return $this->metaDescription;
    }

    public function getAttributes() {
        return array('title' => $this->getTitle(),
                    'metaDescription' => $this->getMetaDescription(),
                    'metaKeywords' => $this->getMetaKeywords()
        );
    }
}
?>
