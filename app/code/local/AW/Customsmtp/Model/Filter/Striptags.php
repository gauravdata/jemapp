<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Customsmtp
 * @version    1.0.8
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Customsmtp_Model_Filter_Striptags extends Zend_Filter_StripTags
{
    protected $_restrictedTags = array();

    protected $_restrictedAttrs = array();

    protected $_restrictedAttrsPatterns = array();

    public function __construct($options = null)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (array_key_exists('restrictedTags', $options)) {
            $this->setRestrictedTags($options['restrictedTags']);
        }

        if (array_key_exists('restrictedAttrs', $options)) {
            $this->setRestrictedAttrs($options['restrictedAttrs']);
        }

        if (array_key_exists('restrictedAttrsPatterns', $options)) {
            $this->_setRestricted('_restrictedAttrsPatterns', $options['restrictedAttrsPatterns']);
        }
    }

    public function getRestrictedTags()
    {
        return $this->_restrictedTags;
    }

    protected function _setRestricted($var, $values)
    {
        foreach ($values as $v) {
            array_push($this->$var, strtolower($v));
        }
        return $this;
    }

    public function setRestrictedTags($tags)
    {
        if (!is_array($tags)) {
            $tags = array($tags);
        }

        foreach ($tags as $index => $element) {
            // If the tag was provided without attributes
            if (is_int($index) && is_string($element)) {
                // Canonicalize the tag name
                $tagName = strtolower($element);
                // Store the tag as allowed with no attributes
                $this->_restrictedTags[$tagName] = array();
            }
        }

        return $this;
    }

    public function getRestrictedAttrs()
    {
        return $this->_restrictedAttrs;
    }

    public function setRestrictedAttrs($attributesAllowed)
    {
        if (!is_array($attributesAllowed)) {
            $attributesAllowed = array($attributesAllowed);
        }

        // Store each attribute as allowed
        foreach ($attributesAllowed as $attribute) {
            if (is_string($attribute)) {
                // Canonicalize the attribute name
                $attributeName = strtolower($attribute);
                $this->_restrictedAttrs[$attributeName] = null;
            }
        }

        return $this;
    }

    /**
     * Filters a single tag against the current option settings
     *
     * @param  string $tag
     *
     * @return string
     */
    protected function _filterTag($tag)
    {
        // Parse the tag into:
        // 1. a starting delimiter (mandatory)
        // 2. a tag name (if available)
        // 3. a string of attributes (if available)
        // 4. an ending delimiter (if available)
        $isMatch = preg_match('~(</?)(\w*)((/(?!>)|[^/>])*)(/?>)~', $tag, $matches);

        // If the tag does not match, then strip the tag entirely
        if (!$isMatch) {
            return '';
        }

        // Save the matches to more meaningfully named variables
        $tagStart = $matches[1];
        $tagName = strtolower($matches[2]);
        $tagAttributes = $matches[3];
        $tagEnd = $matches[5];

        // If tag is in restricted then remove it
        if (isset($this->_restrictedTags[$tagName])) {
            return '';
        }

        // Trim the attribute string of whitespace at the ends
        $tagAttributes = trim($tagAttributes);

        // If there are non-whitespace characters in the attribute string
        if (strlen($tagAttributes)) {
            // Parse iteratively for well-formed attributes
            preg_match_all('/(\w+)\s*=\s*(?:(")(.*?)"|(\')(.*?)\')/s', $tagAttributes, $matches);

            // Initialize valid attribute accumulator
            $tagAttributes = '';

            // Iterate over each matched attribute
            foreach ($matches[1] as $index => $attributeName) {
                $attributeName = strtolower($attributeName);
                $attributeDelimiter = empty($matches[2][$index]) ? $matches[4][$index] : $matches[2][$index];
                $attributeValue = $matches[3][$index] === '' ? $matches[5][$index] : $matches[3][$index];


                // If the attribute is in restricted, then remove it entirely
                if (array_key_exists($attributeName, $this->_restrictedAttrs)
                    || $this->_matchRestrictedAttrPatterns($attributeName)
                ) {
                    continue;
                }

                // Add the attribute to the accumulator
                $tagAttributes .= " $attributeName=" . $attributeDelimiter
                    . $attributeValue . $attributeDelimiter;
            }
        }

        // Reconstruct tags ending with "/>" as backwards-compatible XHTML tag
        if (strpos($tagEnd, '/') !== false) {
            $tagEnd = " $tagEnd";
        }

        // Return the filtered tag
        return $tagStart . $tagName . $tagAttributes . $tagEnd;
    }

    protected function _matchRestrictedAttrPatterns($attributeName)
    {
        foreach ($this->_restrictedAttrsPatterns as $pattern) {
            if (preg_match('|' . $pattern . '|i', $attributeName)) {
                return true;
            }
        }
        return false;
    }
}
